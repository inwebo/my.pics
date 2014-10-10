<?php

namespace LibreMVC\Img\Driver;

class resourceWriteToFile extends \Exception {}

class Driver {

    protected $_resource;

    public function __construct( $path ) {

        // Depuis la function static self::load(); le param est un objet de type gd
        if( is_resource( $path ) ) {
            $this->_resource = $path;
            return $this;
        }


        // N'est pas une resource GD
        $fileContent = @file_get_contents( $path );
        if( $fileContent === false ) {
            throw new ImgException('File, `' . $path . '` does not exist.');
        }

        $resource = @imagecreatefromstring( $fileContent );

        if( $resource === false ) {
            throw new ImgException('File, `' . $path . '` is not a supported format.');
        }
        else {
            $this->_resource = $resource;
        }
    }

    static public function loadFromGd( $gd ) {
        $class = get_called_class();
        return new $class($gd);
    }

    public function isValidQuality( $quality ) {
        $class = get_called_class();
        return ($class::QUALITY_MIN <= $quality && $class::QUALITY_MAX >= $quality );
    }

    public function this(){
        return $this;
    }

    public function getResource(){
        return $this->_resource;
    }

    public function setResource( $resource ) {
        return $this->_resource = $resource;
    }

    public function display(){}

    public function toString(){}

    public function convertTo( $type ){
        switch($type) {
            case 'png':
                return Png::loadFromGd($this->getResource());
                break;
        }
    }

}