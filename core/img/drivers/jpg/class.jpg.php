<?php
namespace LibreMVC\Img\Drivers {

    use LibreMVC\Img\Drivers;

    /**
     * Class Jpg
     * @package LibreMVC\Img\Gd
     * @todo : implemebts iio
     */
    class Jpg extends Drivers {

        const QUALITY_MIN = 0;
        const QUALITY_MAX = 100;
        const QUALITY_DEFAULT = 80;

        public function display( $toString = false ) {
            if ( !$toString ) {
                header('Content-Type: image/jpeg');
            }
            imagejpeg($this->_resource);
            imagedestroy($this->_resource);
            exit;
        }

        public function save( $path, $quality = self::QUALITY_DEFAULT ) {
            $quality = ( !is_null($quality) && self::isValidQuality( $quality ) ) ? $quality : self::QUALITY_DEFAULT;
            $image = @imagejpeg( $this->_resource, $path, $quality );
            if( $image === false ) {
                throw new Img\resourceWriteToFile('Cannot write to file : `' . $path . '`.');
            }
        }
    }
}