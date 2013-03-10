<?php

/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC\Img;

class Filter {
    
    static public function filter($resource, $filtre, $filtre_param_1 = '', $filtre_param_2 = '', $filtre_param_3 = '', $filtre_param_4 = '') {
        switch ($filtre) {

            case IMG_FILTER_NEGATE :
            case IMG_FILTER_GRAYSCALE :
            case IMG_FILTER_EDGEDETECT :
            case IMG_FILTER_EMBOSS :
            case IMG_FILTER_GAUSSIAN_BLUR :
            case IMG_FILTER_SELECTIVE_BLUR :
            case IMG_FILTER_MEAN_REMOVAL :
                imagefilter($resource, $filtre);
                break;

            case IMG_FILTER_BRIGHTNESS :
            case IMG_FILTER_CONTRAST :
            case IMG_FILTER_SMOOTH :
                imagefilter($resource, $filtre, $filtre_param_1);
                break;

            case IMG_FILTER_COLORIZE :
                imagefilter($resource, IMG_FILTER_CONTRAST, $filtre_param_1, $filtre_param_2, $filtre_param_3, $filtre_param_4);
                break;

            case IMG_FILTER_PIXELATE :
                imagefilter($resource, IMG_FILTER_PIXELATE, $filtre_param_1, $filtre_param_2);
                break;

            case IMG_FILTER_SEPIA :
                imagefilter($this->_gdPics, IMG_FILTER_GRAYSCALE);
                imagefilter($resource, IMG_FILTER_COLORIZE, 100, 50, 0);
                break;
        }
        return $resource;
    }
    
}

