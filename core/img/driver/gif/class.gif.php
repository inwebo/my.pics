<?php
namespace LibreMVC\Img\Driver;

use \LibreMVC\Helpers\Files;
use \LibreMVC\Img;

/**
 * Class Jpg
 * @package LibreMVC\Img\Gd
 * @todo : implemebts iio
 */
class Gif extends Driver {

    public function display( $toString = false ) {
        if ( !$toString ) {
            header('Content-Type: image/gif');
        }
        imagegif($this->_resource);
        imagedestroy($this->_resource);
        exit;
    }

    public function save( $path) {
        $image = @imagegif( $this->_resource, $path );
        if( $image === false ) {
            throw new Img\resourceWriteToFile('Cannot write to file : `' . $path . '`.');
        }
    }
}