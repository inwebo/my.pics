<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 11/10/14
 * Time: 06:19
 */

namespace LibreMVC\Img\Driver\Ico;


class ImageMap {
    /**
     * Specifies image width in pixels. Can be any number between 0 and 255. Value 0 means image width is 256 pixels.
     * @var int
     */
    protected $_width;
    /**
     * Specifies image height in pixels. Can be any number between 0 and 255. Value 0 means image height is 256 pixels.
     * @var int
     */
    protected $_height;
    /**
     * Specifies number of colors in the color palette. Should be 0 if the image does not use a color palette.
     * @var int
     */
    protected $_palette;
    /**
     * In ICO format: Specifies color planes. Should be 0 or 1.
     * In CUR format: Specifies the horizontal coordinates of the hotspot in number of pixels from the left.
     * @var int
     */
    protected $_colorplanes;
    /**
     * In ICO format: Specifies bits per pixel.
     * In CUR format: Specifies the vertical coordinates of the hotspot in number of pixels from the top.
     * @var int
     */
    protected $_bits;
    /**
     * Specifies the size of the image's data in bytes
     * @var int
     */
    protected $_size;

    /**
     * @return int
     */
    public function getBits()
    {
        return $this->_bits;
    }

    /**
     * @return int
     */
    public function getColorplanes()
    {
        return $this->_colorplanes;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * @return int
     */
    public function getPalette()
    {
        return $this->_palette;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }
    /**
     * Specifies the offset of BMP or PNG data from the beginning of the ICO/CUR file
     * @var int
     */
    protected $_offset;

    function __construct($_width, $_height, $_palette, $_colorplanes, $_bits, $_size, $_offset)
    {
        $this->_width       = ($_width === 0) ? 256 : $_width;
        $this->_height      = ($_height === 0) ? 256 : $_height;
        $this->_palette     = $_palette;
        $this->_colorplanes = $_colorplanes;
        $this->_bits        = $_bits;
        $this->_size        = $_size;
        $this->_offset      = $_offset;
    }


    public function get() {
        return $this;
    }

}