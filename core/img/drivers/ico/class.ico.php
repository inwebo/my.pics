<?php

namespace LibreMVC\Img\Drivers;

use LibreMVC\Img\Drivers\Bmp\InfoHeader;
use \LibreMVC\Traits\Bin;
use \LibreMVC\Img;
use \LibreMVC\Img\Drivers\Bmp\InfoHeader as BmpInfoHeader;
use \LibreMVC\Img\Drivers\Ico\ImageMap;
use \LibreMVC\Img\Drivers\Ico\Image;
use LibreMVC\Img\Abstracts\aImgBin;
use \LibreMVC\Img\Drivers\Ico\InfoHeader as IcoInfoHeader;

class Ico extends aImgBin {

    const HEADER_LENGTH = 40;

    /**
     * Specifies image type: 1 for icon (.ICO) image, 2 for cursor (.CUR) image. Other values are invalid.
     * @var int
     */
    protected $_types;

    /**
     * Specifies number of images in the file.
     * @var int
     */
    protected $_imagesCount;

    /**
     * @var array LibreMVC\Img\Driver\Ico\ImageMap collection
     */
    protected $_imagesMaps;

    /**
     * @var array LibreMVC\Img\Driver\Ico\Image\Header
     */
    protected $_imagesHeaders;

    /**
     * @var array LibreMVC\Img\Driver\Ico\Image
     */
    protected $_images;

    /**
     * @var array GD resource
     */
    protected $_resources;

    public function __construct( $type, $imagesCount, $imagesMaps, $imagesHeaders, $images, $resources ) {
        $this->_types           = $type;
        $this->_imagesCount     = $imagesCount;
        $this->_imagesMaps      = $imagesMaps;
        $this->_imagesHeaders   = $imagesHeaders;
        $this->_images          = $images;
        $this->_resources       = $resources;
        //var_dump($this);
    }

    static public function loadFromBin( $bin ){
        return self::unpack($bin);
    }

    static public function loadFromFile( $fileName ) {
        return self::loadFromBin( self::fileToBin( $fileName ) );
    }


    static protected function getImagesMaps( $f ){
        return ImageMap::loadFromBin( fread($f,16) );
    }

    static protected function getHeaders($f, ImageMap $imgMap) {
        // Look @ img bytes.
        fseek( $f, $imgMap->getOffset() );
        return IcoInfoHeader::loadFromBin( fread( $f, self::HEADER_LENGTH ) );
    }

    public function imageico() {

    }

    public function display( $icon = 1, $toString = false ) {
        if ( !$toString ) {
            //infoheader('Content-Type: image/bmp');
            //echo $this->_resources;
            //$this->read();
            var_dump($this);
        }

        exit;
    }

    public function pack(){}

    /**
     * @param $bin
     * @return Ico|void
     */
    static function unpack( $bin ){

        $f = self::binToStream( $bin );
        //var_dump($f);
        $reserved           = unpack("vreserved/vtype/vimages", fread($f,6));
        $_types     = $reserved['type'];
        $_imagesCount = $reserved['images'];
        $_imagesMaps = array();

        // Images maps
        for($i = 1; $i <= $_imagesCount ; $i++) {
            $_imagesMaps[] = self::getImagesMaps($f);
        }

        //var_dump($_imagesMaps[] = self::getImagesMaps($f));

        $_imagesHeaders = array();
        // Image headers
        // If image is PNG, doesn't got any headers, look at _ImagesMaps.
        foreach( $_imagesMaps as $v ) {
            $_imagesHeaders[] = self::getHeaders( $f, $v) ;
        }

        // Resources
        // Load images
        $j = -1 ;
        while( isset( $_imagesMaps[++$j] ) ) {
            fseek( $f, $_imagesMaps[$j]->getOffset() );
            $data = fread( $f, $_imagesMaps[$j]->getSize() );
            //$this->_images[] = new Image( $data, $this->_imagesMaps[$j], $this->_imagesHeaders[$j] );
            $_images = array();
            // Is png ?
            if( Bin::isPng($data) ) {
                $gd         = imagecreatefromstring($data);
                $img        = Img::loadFromGd($gd);
                $_images[]  = $img;
            }
            // Is ico
            else {
                // Got palette ?

                // No palette

                //
                // BmpFileHeader, skip header
                $_images[] = Ico\Image::unpack($data, $_imagesMaps[$j],$_imagesHeaders[$j]);
                //$_images[] = Bmp::loadFromBin($data);
            }

        }
        return new self($_types, $_imagesCount, $_imagesMaps, $_imagesHeaders, $_images, array());
    }

}