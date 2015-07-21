<?php

namespace LibreMVC\Img\Drivers;


use LibreMVC\Bin\Pack;
use \LibreMVC\Img\Abstracts\aImgBin;
use LibreMVC\Img\Drivers\Bmp\FileHeader;
use LibreMVC\Img\Drivers\Bmp\InfoHeader;
use LibreMVC\Img\Edit;
use \LibreMVC\Img\Interfaces\iPackable;
use \LibreMVC\Traits\Bin;
use \LibreMVC\Traits\Modifiable;

/**
 * Class Bmp
 * @package LibreMVC\Img\Drivers
 * @see : https://en.wikipedia.org/wiki/BMP_file_format
 */
class Bmp extends aImgBin implements iPackable{

    const FILE_HEADER_LENGTH    = 14;
    const BMP_HEADER_LENGTH     = 40;

    /**
     * @var \LibreMVC\Img\Drivers\Bmp\FileHeader
     */
    protected $_fileHeader;

    /**
     * @var \LibreMVC\Img\Drivers\Bmp\InfoHeader
     */
    protected $_InfoHeader;

    /**
     * @param FileHeader $fileHeader
     * @param InfoHeader $infoHeader
     * @param $palette
     * @param $resource
     */
    public function __construct( FileHeader $fileHeader, InfoHeader $infoHeader, $palette, $resource ) {
        $this->_fileHeader  = $fileHeader;
        $this->_InfoHeader  = $infoHeader;
        $this->_palette     = $palette;
        $this->_resource    = $resource;
        //var_dump(Pack::dword($this->_fileHeader->getSize()));
        //var_dump(self::dword($this->_fileHeader->getSize()));
    }

    #region ~back
    private static function byte3($n)
    {
        return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255);
    }

    private static function dword($n)
    {
        return pack("V", $n);
    }

    private static function word($n)
    {
        return pack("v", $n);
    }

    /**
     * http://de77.com/downloads/BMP_v3.zip
     * @param $img
     * @param bool $filename
     */
    static public function imagebmp(&$img, $filename = false)
    {
        $wid = imagesx($img);
        $hei = imagesy($img);
        $wid_pad = str_pad('', $wid % 4, "\0");

        $size = 54 + ($wid + $wid_pad) * $hei * 3; //fixed

        //prepare & save infoheader
        $header['identifier']		= 'BM';
        $header['file_size']		= self::dword($size);

        $d = fopen('../tests-bin/imagebmp','w+b');
        fwrite($d, $header['identifier'] . $header['file_size']);
        $header['reserved']			= self::dword(0);
        $header['bitmap_data']		= self::dword(54);
        $header['header_size']		= self::dword(40);
        $header['width']			= self::dword($wid);
        $header['height']			= self::dword($hei);
        $header['planes']			= self::word(1);
        $header['bits_per_pixel']	= self::word(24);
        $header['compression']		= self::dword(0);
        $header['data_size']		= self::dword(0);
        $header['h_resolution']		= self::dword(0);
        $header['v_resolution']		= self::dword(0);
        $header['colors']			= self::dword(0);
        $header['important_colors']	= self::dword(0);

        if ($filename)
        {
            $f = fopen($filename, "wb");
            foreach ($header AS $h)
            {
                fwrite($f, $h);
            }

            //save pixels
            for ($y=$hei-1; $y>=0; $y--)
            {
                for ($x=0; $x<$wid; $x++)
                {
                    $rgb = imagecolorat($img, $x, $y);
                    fwrite($f, byte3($rgb));
                }
                fwrite($f, $wid_pad);
            }
            fclose($f);
        }
        else
        {
            foreach ($header AS $h)
            {
                echo $h;
            }

            //save pixels
            for ($y=$hei-1; $y>=0; $y--)
            {
                for ($x=0; $x<$wid; $x++)
                {
                    $rgb = imagecolorat($img, $x, $y);
                    echo self::byte3($rgb);
                }
                echo $wid_pad;
            }
        }
    }
    #endregion

    static public function loadFromBin( $bin ) {
        return self::unpack($bin);
    }

    static public function loadFromFile( $fileName ) {
        return self::loadFromBin( self::fileToBin( $fileName ) );
    }

    #region iPackable
    static public function unpack( $bin ) {
        $f              = Bin::binToStream( $bin );
        $fileHeader     = self::getFileHeader($f);
        $bmpInfoHeader  = self::getBmpInfoHeader($f);
        $palette        = self::getPalette($f, $bmpInfoHeader);
        $resource       = self::resourceFactory($f,$fileHeader, $bmpInfoHeader, $palette);

        return new self( $fileHeader, $bmpInfoHeader, $palette, $resource );
    }

    public function pack() {
        $pad = str_pad('', $this->_InfoHeader->getWidth() % 4, "\0");

        $bin = 'BM';
        $bin .= Pack::dword($this->_fileHeader->getSize());
        $d = fopen('../tests-bin/pack','w+b');
        fwrite($d,$bin);
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(self::BMP_HEADER_LENGTH + self::FILE_HEADER_LENGTH);
        $bin .= Pack::dword(self::BMP_HEADER_LENGTH);
        $bin .= Pack::dword($this->_InfoHeader->getWidth());
        $bin .= Pack::dword($this->_InfoHeader->getHeight());
        $bin .= Pack::word(1);
        $bin .= Pack::word($this->_InfoHeader->getBitCount());
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(0);
        $bin .= Pack::dword(0);

        // Save pixels
        for ($y=$this->_InfoHeader->getHeight()-1; $y>=0; $y--)
        {
            for ($x=0; $x<$this->_InfoHeader->getWidth(); $x++)
            {
                $rgb = imagecolorat($this->_resource, $x, $y);
                $bin .= self::byte3($rgb);
            }

            $bin .= $pad;

        }
        return $bin;
    }

    static public function getFileHeader( $f ) {
        return Bmp\FileHeader::unpack( fread( $f, self::FILE_HEADER_LENGTH ) );
    }

    static public function getBmpInfoHeader( $f ) {
        return Bmp\InfoHeader::loadFromBin(fread( $f, self::BMP_HEADER_LENGTH ));
    }

    static public function getPalette( $f, $bmpHeader ) {
        $palette = array();

        if ( $bmpHeader->getTotalColours() < 16777216)
        {
            //$palette = unpack( 'V' . $bmpHeader['colors'], fread($f,$bmpHeader['colors']*4));
        }
        return $palette;
    }

    static public function resourceFactory($f, FileHeader $fileHeader, Bmp\InfoHeader $infoHeader, $palette, $skipHeader = false) {
        // Caneva
        $img = imagecreatetruecolor( $infoHeader->getWidth(), $infoHeader->getHeight() );

        // To pixels data
        fseek($f, $fileHeader->getOffset());

        $pad = self::getRowPad( $infoHeader->getBitCount(), $infoHeader->getWidth() );
        // All y
        for( $i = 0; $i < $infoHeader->getHeight(); $i++ ) {
            // All x
            for( $j = 0; $j < $infoHeader->getWidth(); $j++ ) {
                $b = unpack( "C", fread( $f, 1 ) )[1];     #B
                $g = unpack( "C", fread( $f, 1 ) )[1];     #G
                $r = unpack( "C", fread( $f, 1 ) )[1];     #R

                imagesetpixel($img,$j,$i,imagecolorallocate($img,$r,$g,$b));
            }
            // Line padding
            fseek($f,ftell( $f ) + $pad );
        }
        fclose($f);
        if( self::isBottomUp($infoHeader) ) {
            Edit::flip($img, 2);
        }
        return $img;
    }
    #endregion

    #region Helpers
    static protected function isBottomUp(InfoHeader $infoHeader) {
        return ( $infoHeader->getHeight() > 0 );
    }

    static protected function getRowPad( $bitCount, $width ) {
        $bpp = $bitCount / 8;
        $pad = $width * $bpp / 4;
        $pad -= floor( $width * $bpp / 4 );
        $pad = 4 - ( 4 * $pad );
        return ( $pad == 4 ) ? 0 : (int) $pad;
    }
    #endregion

    public function display( $toString = false ) {

        if ( !$toString ) {
            header('Content-Type: image/bmp');
        }
        //$this->imagebmp($this->_resource);
        echo $this->pack();
        imagedestroy($this->_resource);
        exit;
    }
}