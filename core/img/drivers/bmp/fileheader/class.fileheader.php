<?php

namespace LibreMVC\Img\Drivers\Bmp;

use LibreMVC\Img\Interfaces\iPackable;

class FileHeader implements iPackable{
    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    protected $_type;

    protected $_size;

    protected $_offset;

    public function __construct( $type, $size, $offset ) {
        $this->_type    = self::typeToString( $type );
        $this->_size    = $size;
        $this->_offset  = $offset;
    }

    static protected function typeToString( $bin ) {
        return $bin;
    }

    public function pack() {

    }

    static public function unpack( $bin ) {
        $results = unpack( "vtype/Vsize/Vreserved/Voffset", $bin );
        return new self( $results['type'], $results['size'], $results['offset']);
    }

    static public function loadFromBin( $bin ) {

    }

} 