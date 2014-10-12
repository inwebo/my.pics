<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 11/10/14
 * Time: 08:45
 */

namespace LibreMVC\Img\Driver\Ico\Image;

use LibreMVC\Bin;
use LibreMVC\Bin\Unpack;

class Header {

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
     * @var int
     * @see : http://msdn.microsoft.com/en-us/library/windows/desktop/dd183376%28v=vs.85%29.aspx
     */
    protected $_bitCount;

    /**
     * The number of bytes required by the structure. Is 40.
     * @var int
     */
    protected $_size = 40;

    /**
     * Useless values
     */
    protected $_compression  = 0;
    protected $_xppm         = 0;
    protected $_yppm         = 0;
    protected $_clrused      = 0;
    protected $_clrimportant = 0;
    /**
     * The number of planes for the target device. This value must be set to 1.
     * @var int
     */
    protected $_planes = 1;

    /**S
     * @param int $_height
     * @param int $_width
     * @param int $_bitCount
     */
    function __construct( $_height, $_width, $_bitCount ) {
        $this->_width    = ($_width === 1) ? 256 : $_width;
        $this->_height   = ($_height === 1) ? 256 : $_height;
        $this->_bitCount = $_bitCount;
    }

    /**
     * Bug lorsque l'icon fait 256px
     * @param $bin
     * @return Header
     */
    static public function load( $bin ) {
        $f      = Bin::binToStream( $bin );
        // Reserved, size = 40
        fseek( $f, ftell( $f ) + 4 );
        $width = (unpack('V', fread($f, 4))[1]);
        $height = Unpack::dword( fread( $f, 4 ) ) >> 1;
        // Reserved
        fseek($f, ftell( $f ) + 2 );

        $bitCount = Unpack::word( fread( $f, 4 ) );
        return new Header( $height, $width, $bitCount );
    }

    /**
     * @return mixed
     */
    public function getSizeHeader()
    {
        return $this->_sizeHeader;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @return mixed
     */
    public function getPlanes()
    {
        return $this->_planes;
    }

    /**
     * @return mixed
     */
    public function getBitCount()
    {
        return $this->_bitCount;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->_size;
    }

    static public function pack() {

    }

    static public function unpack() {

    }
} 