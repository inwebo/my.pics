<?php

namespace LibreMVC\Img\Driver;

use \LibreMVC\Img;
use \LibreMVC\Img\Driver\Ico\Image\Header;
use \LibreMVC\Img\Driver\Ico\ImageMap;

class Ico extends Driver {

    //use Pack;
    //use Unpack;

    /**
     * Specifies image type: 1 for icon (.ICO) image, 2 for cursor (.CUR) image. Other values are invalid.
     * @var
     */
    protected $_types;

    /**
     * Specifies number of images in the file.
     * @var
     */
    protected $_imagesCount;

    /**
     * @var array ImageMap collection
     */
    protected $_imagesMaps;

    /**
     * @var array Icon\Image
     */
    protected $_images;

    protected $_resources;

    public function __construct( $path ) {
        //parent::__construct($path);
        $this->_path        = $path;
        $this->_imagesMaps  = array();
        $this->_images      = array();
        $this->_resources   = array();
        $this->read();
    }

    public function read() {
        $f = fopen($this->_path,"rb");

        // Reserved
        $reserved           = unpack("vreserved/vtype/vimages", fread($f,6));
        $this->_types       = $reserved['type'];
        $this->_imagesCount = $reserved['images'];

        // Structure of image directory
        for($i = 1; $i <= $this->_imagesCount; $i++) {
            $this->_imagesMaps[] = $this->getImagesMaps($f);
        }

        // Image headers
        foreach( $this->_imagesMaps as $v ) {
            $this->_images[] = $this->getImage( $f, $v) ;
        }

        var_dump($this);
    }

    protected function getImagesMaps( $f ){
        $structure = unpack("Cwidth/Cheight/cpalette/creserved/vcolorplanes/vbits/Vsize/Voffset", fread($f,16) );
        return new ImageMap($structure['width'], $structure['height'], $structure['palette'],$structure['colorplanes'], $structure['bits'], $structure['size'], $structure['offset']);
    }

    protected function getImage($f, ImageMap $imgd) {
        fseek($f, $imgd->getOffset());
        echo $imgd->getOffset() .'<br>';
        $size= $this->unpackDWord(fread($f,4));
        $width= $this->unpackDWord(fread($f,4));
        $height= $this->unpackDWord(fread($f,4)) >> 1 ;
        //Reserved next bytes
        fseek($f, ftell($f)+4);
        $planes= $this->unpackWord(fread($f,2));
        $bitCount= $this->unpackWord(fread($f,4));

        $header = new Header(  $height, $width, $bitCount );

        fseek($f, $imgd->getOffset() );


        return Header::load( fread( $f, 40 ) );
    }

     protected function unpackWord( $data ) {
        return unpack('v', $data )[1];
    }

    protected function unpackDWord( $data ) {
        return unpack('V', $data )[1];
    }

    public function imageico() {

    }

    public function display( $toString = false ) {
        if ( !$toString ) {
            //header('Content-Type: image/bmp');
            //echo $this->_fileContent;
            $this->read();
            //var_dump($this);
        }

        exit;
    }

    public function pack(){}
    public function unpack(){}

}