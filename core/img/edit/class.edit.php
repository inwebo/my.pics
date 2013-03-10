<?php

/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC\Img;

use LibreMVC\Img as Img;

class Edit {

    static public function resize($gdObject, $new_width = NULL, $new_height = NULL) {

        // Resize fixed width and height
        if (isset($new_width) && isset($new_height)) {
            $width = $new_width;
            $height = $new_height;
        }
        // Resize by new width
        elseif (isset($new_width) && is_null($new_height)) {
            // Ratio by width
            if ($new_width > $gdObject->width) {
                $ratio = $new_width / $gdObject->width;
                $width = $new_width;
                $height = round($gdObject->height * $ratio);
            } else {
                $ratio = $gdObject->width / $new_width;
                $width = $new_width;
                $height = round($gdObject->height / $ratio);
            }
        }
        // Resize by new height
        elseif (isset($new_height) && is_null($new_width)) {
            // Ratio by height
            if ($new_height > $gdObject->height) {
                $ratio = $new_height / $gdObject->height;
                $width = round($gdObject->width * $ratio);
                $height = $new_height;
            } else {
                $ratio = $gdObject->height / $new_height;
                $width = round($gdObject->width / $ratio);
                $height = $new_height;
            }
        }

        $image_mini = imagecreatetruecolor($width, $height);

        ImageAlphaBlending($image_mini, false);
        ImageSaveAlpha($image_mini, true);
        imagecopyresampled($image_mini, $gdObject->resource, 0, 0, 0, 0, $width, $height, $gdObject->width, $gdObject->height);

        $gdObject->width = $width;
        $gdObject->height = $height;
        $gdObject->resource = $image_mini;
        return $gdObject;
    }

    /**
     * Applique un masque d'opacité $mask sur l'image courante
     * Attention $mask DOIT être une image PNG avec canal ALPHA (png 24 bits)
     *
     * @arguments 	RESSOURCE $pictures chemin d'accès d'une image
     * 						  $mask chemin d'accès d'une image png
     * 				CONST $flag RESIZE_TO_PICTURES_SIZE Les dimensions de $mask seront adaptées aux dimensions de $pictures defaut
     * 							RESIZE_TO_MASK_SIZE     Les dimensions de $pictures seront adaptées aux dimensions de $mask
     * 				
     * @return    	BOOL 1 En cas de succès
     *
     * @throw EXCEPTION si $flag n'est pas reconnu
     */
    static public function mask($gdObject, $mask) {
        $gdMask = Img::load($mask);
        for ($i = 0; $i < $gdObject->width; ++$i) {
            for ($j = 0; $j < $gdObject->height; ++$j) {
                $pxl_alpha = imagecolorsforindex($gdMask->resource, imagecolorat($gdMask->resource, $i, $j));
                $pxl_color = imagecolorsforindex($gdObject->resource, imagecolorat($gdObject->resource, $i, $j));
                $color = imagecolorallocatealpha(
                        $gdObject->resource, $pxl_color['red'], $pxl_color['green'], $pxl_color['blue'], $pxl_alpha['alpha']);
                imagesetpixel($gdObject->resource, $i, $j, $color);
            }
        }
        imagesavealpha($gdObject->resource, true);
        return $gdObject;
    }

    static public function pattern($gdObject, $pattern) {

        $gdPattern = Img::load($pattern);

        static $x = 0;
        static $y = 0;

        $nbrColumn = floor($gdObject->width / $gdPattern->width);
        $nbrRow = floor($gdObject->height / $gdPattern->height);

        for ($i = 0; $i <= $nbrRow; $i++) {

            for ($j = 0; $j <= $nbrColumn; $j++) {
                imagecopy($gdObject->resource, $gdPattern->resource, $x, $y, 0, 0, $gdPattern->width, $gdPattern->height);
                $x += $gdPattern->width;
            }

            $x = 0;
            $y += $gdPattern->height;
        }
        //$gdObject->stack[] = array(__FUNCTION__, func_get_args());
        return $gdObject;
    }

    static public function crop($gdObject, $crop = array(null, null), $flag = "CENTER", $origin = array("x" => null, "y" => null)) {
        if ($crop[0] >= $gdObject->width || $crop[1] >= $gdObject->height) {
            trigger_error("Outbounds cropping");
        }

        $image_mini = imagecreatetruecolor($crop[0], $crop[1]);
        if ($origin['x'] != '' && $origin['y'] != '' && $flag = CUSTOM) {
            ImageAlphaBlending($image_mini, false);
            ImageSaveAlpha($image_mini, true);
            imagecopyresampled($image_mini, $gdObject->resource, 0, 0, $origin['x'], $origin['y'], $crop[0], $crop[1], $crop[0], $crop[1]);
            $gdObject->resource = $image_mini;
        } else {
            static $x;
            static $y;

            switch ($flag) {

                case "TOP_LEFT":
                    $x = 0;
                    $y = 0;
                    break;

                case "TOP":
                    $x = ($gdObject->width - $crop[0]) / 2;
                    $y = 0;
                    break;

                case "TOP_RIGHT":
                    $x = ($gdObject->width - $crop[0]);
                    $y = 0;
                    break;

                case "RIGHT":
                    $x = ($gdObject->width - $crop[0]);
                    $y = ($gdObject->height - $crop[1]) / 2;
                    break;

                case "BOTTOM_RIGHT":
                    $x = ($gdObject->width - $crop[0]);
                    $y = ($gdObject->height - $crop[1]);
                    break;

                case "BOTTOM":
                    $x = ($gdObject->width - $crop[0]) / 2;
                    $y = ($gdObject->height - $crop[1]);
                    break;

                case "BOTTOM_LEFT" :
                    $x = 0;
                    $y = ($gdObject->height - $crop[1]);
                    break;

                case "LEFT" :
                    $x = 0;
                    $y = ($gdObject->height - $crop[1]) / 2;
                    break;

                case "CENTER" :
                    $x = ($gdObject->width - $crop[0]) / 2;
                    $y = ($gdObject->height - $crop[1]) / 2;
                    break;

                default :
                    throw new Exception('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CUSTOM');
                    break;
            }

            ImageAlphaBlending($image_mini, false);
            ImageSaveAlpha($image_mini, true);
            imagecopyresampled($image_mini, $gdObject->resource, 0, 0, $x, $y, $crop[0], $crop[1], $crop[0], $crop[1]);
            $gdObject->resource = $image_mini;
        }

        //$this->stack[] = array(__FUNCTION__, func_get_args());
        return $gdObject;
    }

    static function merge($gdObject, $fileToMerge, $target = "CENTER", $opacity = 99, $origin = array(), $margin = NULL) {
        $gdMerge = Img::load($fileToMerge);

        static $x;
        static $y;

        switch (strtoupper($target)) {

            case "TOP_LEFT":
                $x = 0 + $margin;
                $y = 0 + $margin;
                break;

            case "TOP":
                $x = ( $gdObject->width - $gdMerge->width) / 2;
                $y = 0 + $margin;
                break;

            case "TOP_RIGHT":
                $x = ($gdMerge->width - $gdObject->width) - $margin;
                $y = 0 - $margin;
                break;

            case "RIGHT":
                $x = ($gdMerge->width - $gdObject->width) - $margin;
                $y = ($gdMerge->height - $gdObject->height) / 2;
                break;

            case "BOTTOM_RIGHT":
                $x = ($gdMerge->width - $gdObject->width) - $margin;
                $y = ($gdMerge->height - $gdObject->height) - $margin;
                break;

            case "BOTTOM":
                $x = ($gdMerge->width - $gdObject->width) / 2;
                $y = ($gdMerge->height - $gdObject->height) - $margin;
                break;

            case "BOTTOM_LEFT" :
                $x = 0 + $margin;
                $y = ($gdMerge->height - $gdObject->height) - $margin;
                break;

            case "LEFT" :
                $x = 0 + $margin;
                $y = ($gdMerge->height - $gdObject->height) / 2;
                break;

            case "CENTER" :
                $x = floor(( $gdObject->width - $gdMerge->width ) / 2);
                $y = floor(( $gdObject->height - $gdMerge->height ) / 2);
                break;

            case "CUSTOM" :
                $x = $origin[0];
                $y = $origin[1];
                break;

            default :
                throw new Exception('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CENTER, CUSTOM');
                break;
        }

        imagecopymerge($gdObject->resource, $gdMerge->resource, $x, $y, 0, 0, $gdMerge->width, $gdMerge->height, $opacity);

        //$this->stack[] = array(__FUNCTION__, func_get_args());

        return $gdObject;
    }

}

