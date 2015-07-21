<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 13/10/14
 * Time: 03:07
 */

namespace LibreMVC\Img\Interfaces;


interface iPackable {

    public function pack();
    static public function unpack( $bin );
    static public function loadFromBin( $bin );
} 