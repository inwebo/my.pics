<?php

namespace LibreMVC;

use LibreMVC\Img\ImgEditable;

class ImgException extends \Exception {}
class distantResourceException extends \Exception {}


/**
 * Class Img
 *
 * Devrait implementer le trait FileEditable, isDistantFile et le design pattern Fabrique.
 *
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