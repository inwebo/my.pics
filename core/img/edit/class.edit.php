<?php

namespace LibreMVC\Img;

use LibreMVC\Img;

class Edit {

    static public function resize( $img, $new_width = null, $new_height = null ) {
        $src =  $img->getDriver()->getResource();
        // Resize fixed width and height
        if( isset( $new_width ) && isset( $new_height ) ) {
            $width  = $new_width;
            $height = $new_height;
        }
        // Resize by new width
        elseif ( is_null( $new_height ) && isset( $new_width ) ) {
            // Ratio by width
            if ( $new_width > $img->getWidth() ) {
                $ratio  = $new_width / $img->getWidth();
                $width  = $new_width;
                $height = round($img->getHeight() * $ratio);
            } else {
                $ratio  = $img->getWidth() / $new_width;
                $width  = $new_width;
                $height = round($img->getHeight() / $ratio);
            }
        }
        // Resize by new height
        elseif ( isset( $new_height ) && is_null( $new_width ) ) {
            // Ratio by height
            if ($new_height > $img->getHeight()) {
                $ratio = $new_height / $img->getHeight();
                $width = round($img->getWidth() * $ratio);
                $height = $new_height;
            } else {
                $ratio = $img->getHeight() / $new_height;
                $width = round($img->getWidth() / $ratio);
                $height = $new_height;
            }
        }

        $image_mini = imagecreatetruecolor($width, $height);

        imagealphablending ( $image_mini, false );

        imagesavealpha($image_mini, true);
        imagecopyresized( $image_mini, $src, 0, 0, 0, 0, $width , $height, $img->getWidth(), $img->getHeight() );

        imagealphablending ( $image_mini, true );
        return $image_mini;
    }

    static public function mask( Img $img, $path ){

        try {
            $img->convertTo('png');
            $layer = Img::load($path);
            $imgResource = $img->getDriver()->getResource();

            imagealphablending($imgResource, false);


            $layerResource = $layer->getDriver()->getResource();

            if( $layer->getWidth() !== $img->getWidth() || $layer->getHeight() !== $img->getHeight()) {
                $layer->resize($img->getWidth(),$img->getHeight());
            }

            for ($i = 0; $i < $img->getWidth(); ++$i) {
                for ($j = 0; $j < $img->getHeight(); ++$j) {

                    $pxl_alpha = imagecolorsforindex(
                        $layerResource,
                        imagecolorat(
                            $layerResource,
                            $i, $j
                        )
                    );

                    $pxl_color = imagecolorsforindex(
                        $imgResource,
                        imagecolorat(
                            $imgResource,
                            $i, $j
                        )
                    );

                    $color = imagecolorallocatealpha(
                        $imgResource,
                        $pxl_color['red'], $pxl_color['green'], $pxl_color['blue'], $pxl_alpha['alpha']
                    );

                    //var_dump($pxl_alpha['alpha'] );
                    //echo 'Origin alpha : ' . $pxl_color['alpha'] . ' <br>';
                    //echo 'New alpha : ' . $pxl_alpha['alpha'] . ' <br>';
                    imagesetpixel($imgResource, $i, $j, $color);
                    //echo 'Current alpha : ' .imagecolorsforindex($imgResource, imagecolorat($imgResource, $i, $j))['alpha'] ;
                    //echo '<br>--------<br>';
                    //var_dump(imagecolorsforindex($imgResource, imagecolorat($imgResource, $i, $j))['alpha']);
                }
            }
            //imageAlphaBlending($imgResource, true);

            return $imgResource;
        }
        catch(\Exception $e) {
            var_dump($e);
        }


    }

    static public function pattern(Img $img, $path){
        try {
            //imagealphablending ( $img->getDriver()->getResource(), false );

            $layer = Img::load($path);

            $cols = floor($img->getWidth() / $layer->getWidth());
            $rows = floor($img->getHeight() / $layer->getHeight());

            $startX = 0;
            $startY = 0;

            // For each rows.
            for ($i = 0; $i <= $rows; $i++) {
                for ($j = 0; $j <= $cols; $j++) {
                    imagecopy(
                        $img->getDriver()->getResource(),
                        $layer->getDriver()->getResource(),
                        $startX, $startY,
                        0, 0,
                        $layer->getWidth(), $layer->getHeight()
                    );
                    $startX += $layer->getWidth();
                }
                $startX = 0;
                $startY += $layer->getHeight();
            }

            return $img->getDriver()->getResource();
        }
        catch(\Exception $e) {
            var_dump($e);
        }

    }

    public function crop(){}

    static function merge(Img $img, $path, $target = "CENTER", $opacity = 99, $origin = array(), $margin = NULL) {

        $layer = Img::load($path);

        $x = 0;
        $y = 0;
        switch (strtoupper($target)) {
            case "TOP_LEFT":
                $x = 0 + $margin;
                $y = 0 + $margin;
                break;
            case "TOP":
                $x = ( $img->width - $layer->width) / 2;
                $y = 0 + $margin;
                break;
            case "TOP_RIGHT":
                $x = ($layer->width - $img->width) - $margin;
                $y = 0 - $margin;
                break;
            case "RIGHT":
                $x = ($layer->width - $img->width) - $margin;
                $y = ($layer->height - $img->height) / 2;
                break;
            case "BOTTOM_RIGHT":
                $x = ($layer->width - $img->width) - $margin;
                $y = ($layer->height - $img->height) - $margin;
                break;
            case "BOTTOM":
                $x = ($layer->width - $img->width) / 2;
                $y = ($layer->height - $img->height) - $margin;
                break;
            case "BOTTOM_LEFT" :
                $x = 0 + $margin;
                $y = ($layer->height - $img->height) - $margin;
                break;
            case "LEFT" :
                $x = 0 + $margin;
                $y = ($layer->height - $img->height) / 2;
                break;
            case "CENTER" :
                $x = floor(( $img->width - $layer->width ) / 2);
                $y = floor(( $img->height - $layer->height ) / 2);
                break;
            case "CUSTOM" :
                $x = $origin[0];
                $y = $origin[1];
                break;
            default :
                throw new Exception('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CENTER, CUSTOM');
                break;
        }
        imagecopymerge($img->resource, $layer->resource, $x, $y, 0, 0, $layer->width, $layer->height, $opacity);
        return $img;
    }

    static public function getPalette( Img $img, $fastProcess = false ) {
        $buffer = $img;
        if($fastProcess) {
            $buffer = $buffer->resize(150);
        }

        $hexarray = array();

        for ($y=0; $y < $buffer->getHeight(); $y++)
        {
            for ($x=0; $x < $buffer->getWidth(); $x++)
            {
                $index = imagecolorat( $buffer->getDriver()->getResource(), $x, $y );
                $Colors = imagecolorsforindex( $buffer->getDriver()->getResource(), $index );
                $Colors['red']=intval((($Colors['red'])+15)/32)*32; //ROUND THE COLORS, TO REDUCE THE NUMBER OF COLORS, SO THE WON'T BE ANY NEARLY DUPLICATE COLORS!
                $Colors['green']=intval((($Colors['green'])+15)/32)*32;
                $Colors['blue']=intval((($Colors['blue'])+15)/32)*32;
                if ($Colors['red']>=256)
                    $Colors['red']=240;
                if ($Colors['green']>=256)
                    $Colors['green']=240;
                if ($Colors['blue']>=256)
                    $Colors['blue']=240;
                $hexarray[]=substr("0".dechex($Colors['red']),-2).substr("0".dechex($Colors['green']),-2).substr("0".dechex($Colors['blue']),-2);
            }
        }
        $hexarray=array_count_values($hexarray);
        natsort($hexarray);
        $hexarray=array_reverse($hexarray,true);
        return $hexarray;

    }

}