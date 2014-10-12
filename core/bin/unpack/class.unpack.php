<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 00:33
 */

namespace LibreMVC\Bin;


trait Unpack {

    protected $_highMap = 0xffffffff00000000;
    protected $_lowMap  = 0x00000000ffffffff;

    static public function word( $bin ) {
        return unpack('v', $bin )[1];
    }

    static public function dword( $bin ) {
        return unpack('V', $bin )[1];
    }

    static public function int64( $bin ) {
        list($higher, $lower) = array_values(unpack('N2', $bin));
        return $higher << 32 | $lower;
    }
} 