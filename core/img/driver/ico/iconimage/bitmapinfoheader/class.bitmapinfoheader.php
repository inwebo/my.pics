<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 11/10/14
 * Time: 08:45
 */

namespace LibreMVC\Img\Driver\Ico\IconImage;


class BitmapInfoHeader {

    protected $_width;
    protected $_height;
    protected $_planes;
    protected $_bitCount;
    protected $_size;

    protected $_compression = 0;
    protected $_xppm = 0;
    protected $_yppm = 0;
    protected $_clrused = 0;
    protected $_clrimportant = 0;

    function __construct($_size, $_height, $_width, $_planes, $_bitCount)
    {
        $this->_size = $_size;
        $this->_height = $_height;
        $this->_width = $_width;
        $this->_planes = $_planes;
        $this->_bitCount = $_bitCount;
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


} 