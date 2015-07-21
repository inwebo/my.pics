<?php

namespace LibreMVC\Img\Abstracts;

use \LibreMVC\Img\Base;
use \LibreMVC\Img\Interfaces\iLoadable;
use \LibreMVC\Img\Interfaces\iDrivers;

abstract class aImg extends Base implements iLoadable, iDrivers {
    /**
     * @var \LibreMVC\Img\Interfaces\iDrivers
     */
    protected $_driver;

    static public function loadFromFile( $fileName ) {}
    static public function loadFromGd( $resource ){}
    static public function loadFromBin( $binaryData ){}

    public function create(){}
    public function display(){}
    public function convertTo($type){}

    /**
     * Not in interface, each images type is different, like quality, alpha on BMP etc ..
     */
    public function save(){}

} 