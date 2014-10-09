<?php

namespace LibreMVC;

use LibreMVC\Img\ImgEditable;

class ImgException extends \Exception {}
class distantResourceException extends \Exception {}


/**
 * Class Img
 * @package LibreMVC
 * @todo : http://php.net/manual/en/function.imagecreatetruecolor.php#99623
 */
class Img extends ImgEditable{


    #region Todo

    protected function enoughMemory() {

    }
    /**
     * @todo
     */
    static function setQualityDefault() {

    }
    #endregion
}