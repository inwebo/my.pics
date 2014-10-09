<?php
namespace LibreMVC\Img\Driver;

class Png extends Driver{

    const QUALITY_MIN = 0;
    const QUALITY_MAX = 9;
    const QUALITY_DEFAULT = 5;

    public function save( $path, $quality = self::QUALITY_DEFAULT ) {
        $quality = ( !is_null($quality) && self::isValidQuality( $quality ) ) ? $quality : self::QUALITY_DEFAULT;
        imagepng( $this->_resource, $path, $quality );
    }

    public function display( $toString = false ) {
        if ( !$toString ) {
            header('Content-Type: image/png');
        }
        imagealphablending( $this->_resource, false );
        imagesavealpha( $this->_resource, true );
        imagepng( $this->_resource );
        imagedestroy( $this->_resource );
        exit;
    }

}