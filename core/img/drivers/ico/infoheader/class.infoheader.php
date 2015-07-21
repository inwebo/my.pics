<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 11/10/14
 * Time: 08:45
 */

namespace LibreMVC\Img\Drivers\Ico;

use \LibreMVC\Img\Drivers\Bmp\InfoHeader as BmpInfoHeader;

class InfoHeader extends BmpInfoHeader{

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
    function __construct( $_width, $_height, $_bitCount ) {
        $this->_width    = ($_width === 1) ? 256 : $_width;
        $this->_height   = ($_height === 1) ? 256 : $_height;
        $this->_bitCount = $_bitCount;
    }

    static public function unpack($bin) {
        $parent = parent::unpack($bin);
        return new self($parent->getWidth(), $parent->getHeight(), $parent->getBitCount());
    }
}