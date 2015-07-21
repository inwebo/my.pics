<?php

namespace LibreMVC;

use LibreMVC\Img\Abstracts\aImg;

use LibreMVC\Img\Drivers\Bmp;
use LibreMVC\Img\Drivers\Ico;
use LibreMVC\Img\Edit;

class ImgException extends \Exception {}

/**
 * Class Img strategy selon entrée le bon chargement.
 * @package LibreMVC
 */
class Img extends aImg {

    public function __construct( $input ) {
        // Is resource
        if( is_resource( $input ) ) {
            $this->_width       = imagesx( $input );
            $this->_height      = imagesy( $input );
            $this->_mimeType    = image_type_to_mime_type( \IMAGETYPE_PNG );
            $this->_channels    = 4;
            $this->_bits        = 8;
            $driver             = self::NS . 'Png';
            $this->_driver      = new $driver($input);
            return $this;
        }
        // Is file
        else {
            $fileContent = @file_get_contents( $input );
            if( $fileContent === false ) {
                throw new ImgException('Is not a valid file : ' . $input . "\n");
            }
            else {
                $this->setterImgInfos( $input );
                // Is GD known type
                $resource = @imagecreatefromstring( $fileContent );
                if( $resource !== false ) {
                    // Native GD support
                    $driver          = self::NS . self::mimeToClassName( $this->_mimeType ) ;
                    $this->_driver   = new $driver( $resource );
                }
                // Is supported type ?
                else {

                    switch($this->_mimeType) {
                        case image_type_to_mime_type( \IMAGETYPE_ICO ):
                            $this->_driver = Ico::loadFromFile( $input );
                            break;

                        case image_type_to_mime_type( \IMAGETYPE_BMP ):
                            $this->_driver   = Bmp::loadFromFile( $input );
                            break;

                        default:
                            throw new ImgException('Unknow image type : ' . $input . "\n");
                            break;
                    }
                }
            }
        }
    }

    static public function mimeToClassName( $mime ) {
        $class = ucfirst(strtolower(explode('/', $mime)[1]));
        return ($class === 'Jpeg') ? 'Jpg' : $class;
    }

    protected function setterImgInfos( $path ) {
        $infos = @getimagesize( $path );

        $this->_mimeType    = $infos['mime'];
        $this->_width       = $infos[0];
        $this->_height      = $infos[1];
        $this->_bits        = $infos['bits'];

        // @todo : Fixe crade
        $infos['channels'] = (isset($infos['channels'])) ? $infos['channels'] : -1;
        $this->_channels = $infos['channels'];
    }

    static public function load( $filename ) {
        return self::loadFromFile($filename);
    }

    static public function loadFromFile( $fileName ) {
        return new self( $fileName );
    }

    static public function loadFromGd( $resource ) {
        return new self( $resource );
    }

    public function create(){}

    public function display( $toString = false ) {
        $this->_driver->display( $toString );
    }

    public function convertTo($type){
        $type->_driver = $this->_driver->convertTo( $type );
    }

    public function getDriver() {
        return $this->_driver;
    }

    public function save(){

    }
}