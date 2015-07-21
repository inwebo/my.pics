<?php

namespace LibreMVC\Img {

    use LibreMVC\Img\Interfaces\iDrivers;

    class resourceWriteToFile extends \Exception {}
    class ImgException extends \Exception {}
    class distantResourceException extends \Exception {}

    class Drivers implements iDrivers{

        protected $_resource;

        public function __construct( &$resource ) {
            $this->_resource = $resource;
        }

        public function getResource(){
            return $this->_resource;
        }

        public function setResource($resource) {
            $this->_resource = $resource;
        }

        public function getWidth(){
            return imagesx($this->_resource);
        }

        public function getHeight(){
            return imagesy($this->_resource);
        }

        public function create(){}
        public function display(){}
        public function convertTo( $type ){
            $type = self::NS . ucfirst( strtolower( $type ) );
            return new $type( $this->_resource );
        }

        public function isValidQuality( $quality ) {
            $class = get_called_class();
            return ($class::QUALITY_MIN <= $quality && $class::QUALITY_MAX >= $quality );
        }

    }
}