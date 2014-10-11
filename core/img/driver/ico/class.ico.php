<?php

namespace LibreMVC\Img\Driver;

use \LibreMVC\Img;
use LibreMVC\Img\Driver\Ico\IconImage\BitmapInfoHeader;

class Ico extends Driver {

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

    protected $_imagesDirs;
    protected $_images;

    protected $_resources;

    public function __construct( $path ) {
        //parent::__construct($path);
        $this->_path        = $path;
        $this->_imagesDirs  = array();
        $this->_images      = array();
        $this->_resources   = array();
        $this->read();

    }

    public function read() {
        $f = fopen($this->_path,"rb");

        // ICONDIR structure
        $reserved           = unpack("vreserved/vtype/vimages", fread($f,6));
        $this->_types       = $reserved['type'];
        $this->_imagesCount = $reserved['images'];

        // Structure of image directory
        for($i = 1; $i <= $this->_imagesCount; $i++) {
            $this->_imagesDirs[] = $this->getImageDirectoryStructure($f);

        }

        //$this->_imagesCount->rewind();
        // Image
        $i = 0;
        foreach( $this->_imagesDirs as $v ) {
            //var_dump( $this->getImage($f,$this->_imagesDirs[$i]));
            $this->_images[] = $this->getImage($f,$v);
            $this->_resources[] = $this->gdFactory($f,$this->_imagesDirs[$i],$this->_images[$i]);
                $i++;
        }
        var_dump($this);
    }

    protected function getImageDirectoryStructure( $f ){
        $structure = unpack("Cwidth/Cheight/cpalette/creserved/vcolorplanes/vbits/Vsize/Voffset", fread($f,16) );
        $width = ($structure['width'] === 0) ? 256 : $structure['width'];
        $height = ($structure['height'] === 0) ? 256 : $structure['height'];
        return new Img\Driver\Ico\ImagesDirs($width, $height, $structure['palette'],$structure['colorplanes'], $structure['bits'], $structure['size'], $structure['offset']);

    }

    protected function getImage($f, Img\Driver\Ico\ImagesDirs $imgd) {
        fseek($f, $imgd->getOffset());
        $size= $this->unpackDWord($f);
        $width= $this->unpackDWord($f);
        $height= $this->unpackDWord($f) >> 1 ;
        $planes= $this->unpackWord($f);
        $bitCount= $this->unpackWord($f);
        return new BitmapInfoHeader($size, $height,$width,$planes,$bitCount);
    }

    protected function gdFactory($f, ImgageDirs $imgageDirs, BitmapInfoHeader $bitmapInfoHeader) {

    }

    protected function unpackWord( $f ) {
        return unpack('v', fread( $f,8 ))[1];
    }

    protected function unpackDWord( $f ) {
        return unpack('V', fread( $f,4 ))[1];
    }

    protected function unpackLong( $f ) {
        //fseek($f,ftell($f)+1);
        //var_dump(ftell($f));
        $r = unpack('v',fread($f,4));

        //fseek($f,ftell($f)-1);
        //$r2 = unpack('c',fread($f,4));
        //fread($f,1)
        //var_dump($r,$r2);
        //var_dump($r2[1].$r[1]);
        //var_dump(ftell($f));
        //fseek($f,ftell($f));
        //var_dump(ftell($f));
        return $r[1];
    }

    protected function pad($f) {

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
}