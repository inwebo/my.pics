<?php

/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC\Img;

class Filter {
    
    static public function filter($gdObject, $filtre, $filtre_param_1 = '', $filtre_param_2 = '', $filtre_param_3 = '', $filtre_param_4 = '') {
        switch ($filtre) {

            case IMG_FILTER_NEGATE :
            case IMG_FILTER_GRAYSCALE :
            case IMG_FILTER_EDGEDETECT :
            case IMG_FILTER_EMBOSS :
            case IMG_FILTER_GAUSSIAN_BLUR :
            case IMG_FILTER_SELECTIVE_BLUR :
            case IMG_FILTER_MEAN_REMOVAL :
                imagefilter($gdObject->resource, $filtre);
                break;

            case IMG_FILTER_BRIGHTNESS :
            case IMG_FILTER_CONTRAST :
            case IMG_FILTER_SMOOTH :
                imagefilter($gdObject->resource, $filtre, $filtre_param_1);
                break;

            case IMG_FILTER_COLORIZE :
                imagefilter($gdObject->resource, IMG_FILTER_CONTRAST, $filtre_param_1, $filtre_param_2, $filtre_param_3, $filtre_param_4);
                break;

            case IMG_FILTER_PIXELATE :
                imagefilter($gdObject->resource, IMG_FILTER_PIXELATE, $filtre_param_1, $filtre_param_2);
                break;

            case IMG_FILTER_SEPIA :
                imagefilter($gdObject->resource, IMG_FILTER_GRAYSCALE);
                imagefilter($gdObject->resource, IMG_FILTER_COLORIZE, 100, 50, 0);
                break;
        }
        return $gdObject;
    }
    
}

