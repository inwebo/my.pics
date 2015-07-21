<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 02:25
 */

namespace LibreMVC\Traits;

trait Bin {
    /**
     * @param $bin Binary string
     * @return resource
     * @see : http://evertpot.com/222/
     */
    static function binToStream( $bin ) {
        $stream = fopen( 'php://memory', 'r+b' );
        fwrite($stream, $bin);
        rewind($stream);
        return $stream;
    }

    static function binToString( $bin ) {
        return pack('H*', base_convert( $bin, 2, 16 ) );
    }

    static function stringToBin( $string ) {
        $buffer = unpack('H*', $string);
        return base_convert($buffer[1], 16, 2);
    }

    /**
     * @param $fileName
     * @return resource
     */
    static function fileToBin( $fileName ) {
        $resource = self::fileToResource($fileName);
        $contents = '';
        while (!feof($resource)) {
            $contents .=fread($resource, 8192);
        }
        return $contents;
    }
    /**
     * @param $fileName
     * @return resource
     */
    static function fileToResource( $fileName ) {
        $resource = @fopen( $fileName ,"rb");
        rewind( $resource );
        ( $resource === false ) ? false : true;
        return $resource;
    }

    /**
     * Is icon image a PNG.
     * @param string Binary value
     * @return bool true if is png else false
     */
    static function isPng( $bin ) {
        $stream = self::binToStream( $bin );
        $res = unpack('c*', fread( $stream, 4 ) );
        return ((int)implode('', $res) === -119807871);
    }

} 