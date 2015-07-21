<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 11/10/14
 * Time: 08:45
 */

namespace LibreMVC\Img\Drivers\Bmp;

use LibreMVC\Img\Drivers\Bmp;
use LibreMVC\Img\Interfaces\iPackable;
use \LibreMVC\Traits\Bin;
use \LibreMVC\Bin\Unpack;

class InfoHeader implements iPackable{
    /**
     * @return int
     */
    public function getSizeImage()
    {
        return $this->_sizeImage;
    }

    /**
     * The number of bytes required by the structure. Is 40.
     * @var int
     */
    protected $_size = 40;

    /**
     * The width of the bitmap, in pixels.
     * @var int
     */
    protected $_width;

    /**
     * The height of the bitmap, in pixels.
     * @var int
     */
    protected $_height;

    /**
     * The number of planes for the target device. This value must be set to 1.
     * @var int
     */
    protected $_planes = 1;

    /**
     * @var int
     * @see : http://msdn.microsoft.com/en-us/library/windows/desktop/dd183376%28v=vs.85%29.aspx
     */
    protected $_bitCount;

    /**
     * @var int
     */
    protected $_compression;

    /**
     * @var int The size, in bytes, of the image. This may be set to zero for $_compression = BI_RGB bitmaps.
     */
    protected $_sizeImage;

    /**
     * @var int The horizontal resolution, in pixels-per-meter, of the target device for the bitmap. An application can use this value to select a bitmap from a resource group that best matches the characteristics of the current device.
     */
    protected $_xppm;

    /**
     * @var int The vertical resolution, in pixels-per-meter, of the target device for the bitmap.
     */
    protected $_yppm;

    /**
     * @var int
     * @see : http://msdn.microsoft.com/en-us/library/dd183376%28v=vs.85%29.aspx
     */
    protected $_clrUsed;

    /**
     * @var int The number of color indexes that are required for displaying the bitmap. If this value is zero, all colors are required.
     */
    protected $_clrImportant;


    function __construct( $_width, $_height, $_bitCount, $_compression, $_sizeImage, $_xppm, $_yppm, $_clrUsed, $_clrImportant ) {
        $this->_width    = $_width;
        $this->_height   = $_height;
        $this->_bitCount = $_bitCount;
        $this->_compression = $_compression;
        $this->_sizeImage = $_sizeImage;
        $this->_xppm = $_xppm;
        $this->_yppm = $_yppm;
        $this->_clrUsed = $_clrUsed;
        $this->_clrImportant = $_clrImportant;
    }

    #region Getters
    /**
     * @return int
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->_height;
    }

    /**
     * @return int
     */
    public function getBitCount() {
        return $this->_bitCount;
    }

    public function getTotalColours() {
        return pow( 2, $this->getBitCount() );
    }
    #endregion

    static public function loadFromBin( $bin ) {
        return self::unpack( $bin );
    }

    #region iPackable
    public function pack() {

    }

    static public function unpack( $bin ) {
        $f = Bin::binToStream( $bin );

        $infoHeader = unpack(
            'Vsize/Vwidth/lheight/vplanes/vbitCount' .
            '/Vcompression/VsizeImage/Vxppm' .
            '/Vyppm/VclrUsed/VclrImportant',
            fread( $f, Bmp::BMP_HEADER_LENGTH )
        );
        fclose($f);

        return new self(
            $infoHeader['width'], $infoHeader['height'], $infoHeader['bitCount'],
            $infoHeader['compression'], $infoHeader['sizeImage'],
            $infoHeader['xppm'], $infoHeader['yppm'],
            $infoHeader['clrUsed'], $infoHeader['clrImportant']
        );
    }
    #endregion
} 