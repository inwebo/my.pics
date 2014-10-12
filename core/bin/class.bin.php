<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 02:25
 */

namespace LibreMVC;

trait Bin {
    /**
     * @param $bin
     * @return resource
     * @see : http://evertpot.com/222/
     */
    static function binToStream($bin) {
        $stream = fopen( 'php://memory', 'r+b' );
        fwrite($stream, $bin);
        rewind($stream);
        return $stream;
    }
} 