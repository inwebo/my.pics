<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 00:43
 */

namespace LibreMVC\Img\Driver\Ico;

use  \LibreMVC\Img\Driver\Ico\Image\Header;
use \LibreMVC\Img\Driver\Ico\ImageMap;
class Image {

    protected $_data;

    protected $_imageMap;

    protected $_bitmapInfoHeader;

    protected $_colorTable;

    protected $_xorMask;

    protected $_andMask;

    public function __construct($data, ImageMap $imagemap, Header $bitmapinfoheader) {
        $this->_data             = $data;
        $this->_imageMap         = $imagemap;
        $this->_bitmapInfoHeader = $bitmapinfoheader;
    }

} 