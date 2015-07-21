<?php
namespace LibreMVC\Img;

class Base {

    /**
     * @var int
     */
    protected $_width;
    /**
     * @var int
     */
    protected $_height;
    /**
     * @var int
     */
    protected $_mimeType;
    /**
     * @var int
     */
    protected $_channels;
    /**
     * @var int
     */
    protected $_bits;
    /**
     * @var resource
     */
    protected $_resource;

    function __construct( $_width = 1, $_height = 1, $_mimeType = \IMAGETYPE_PNG, $_channels = 4, $_bits = 8) {
        $this->_width       = $_width;
        $this->_height      = $_height;
        $this->_mimeType    = $_mimeType;
        $this->_channels    = $_channels;
        $this->_bits        = $_bits;
        $this->_resource    = $this->resourceFactory( $this->_width, $this->_height );
    }

    static public function loadFromGd( $resource ) {
        $img = new Base();
        $img->_width = imagesx($resource);
        $img->_height = imagesy($resource);
        $img->_resource = $resource;

        return $img;
    }

    static public function resourceFactory( $width, $height ) {
        $resource   = imagecreatetruecolor($width, $height);
        $color      = imagecolorallocate($resource,0,0,0);
        imagecolortransparent( $resource, $color );
        return $resource;
    }

}