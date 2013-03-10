<?php
    function merge($gdObject, $fileToMerge, $target = "CENTER", $opacity = 99, $origin = array(), $margin = NULL) {
        // var_dump($picturestomerge);
        $gdMerge = new Img($fileToMerge);
  
        static $x;
        static $y;

        switch ($target) {

            case "TOP_LEFT":
                $x = 0 + $margin;
                $y = 0 + $margin;
                break;

            case "TOP":
                $x = ($this->getWidth() - $gdMerge->width) / 2;
                $y = 0 + $margin;
                break;

            case "TOP_RIGHT":
                $x = ($dest_im[0] - $src_im[0]) - $margin;
                $y = 0 - $margin;
                break;

            case "RIGHT":
                $x = ($dest_im[0] - $src_im[0]) - $margin;
                $y = ($dest_im[1] - $src_im[1]) / 2;
                break;

            case "BOTTOM_RIGHT":
                $x = ($dest_im[0] - $src_im[0]) - $margin;
                $y = ($dest_im[1] - $src_im[1]) - $margin;
                break;

            case "BOTTOM":
                $x = ($dest_im[0] - $src_im[0]) / 2;
                $y = ($dest_im[1] - $src_im[1]) - $margin;
                break;

            case "BOTTOM_LEFT" :
                $x = 0 + $margin;
                $y = ($dest_im[1] - $src_im[1]) - $margin;
                break;

            case "LEFT" :
                $x = 0 + $margin;
                $y = ($dest_im[1] - $src_im[1]) / 2;
                break;

            case "CENTER" :
                $x = floor(( $this->getWidth() - $gdMerge->width ) / 2);
                $y = floor(( $this->getHeight() - $gdMerge->height ) / 2);
                break;

            case "CUSTOM" :
                $x = $origin[0];
                $y = $origin[1];
                break;

            default :
                throw new Exception('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CENTER, CUSTOM');
                break;
        }

        imagecopymerge($gdObject->resource, $temp->_gdPics, $x, $y, 0, 0, $gdMerge->width, $gdMerge->height, $opacity);

        //$this->stack[] = array(__FUNCTION__, func_get_args());

        return $gdObject;
    }