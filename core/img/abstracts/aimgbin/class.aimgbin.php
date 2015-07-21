<?php

namespace LibreMVC\Img\Abstracts {

    use \LibreMVC\Img\Interfaces\iPackable;
    use LibreMVC\Img\Drivers;

    abstract class aImgBin extends Drivers implements iPackable {

        use \LibreMVC\Traits\Bin;

        static public function unpack( $bin ){}
        public function pack(){}
        static public function loadFromBin( $bin ){}
    }
}