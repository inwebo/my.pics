<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 00:43
 */

namespace LibreMVC\Img\Drivers\Ico;

use \LibreMVC\Img\Drivers\Ico\Header;
use \LibreMVC\Bin;
use \LibreMVC\Img\Drivers\Bmp;

class Image {
    
    /**
     * @var ImageMap
     */
    protected $_imageMap;

    /**
     * @var Header
     */
    protected $_header;

    /**
     * @var array
     */
    protected $_colorTable;

    /**
     * @var
     */
    protected $_xorMask;

    /**
     * @var
     */
    protected $_andMask;

    public function __construct( ImageMap $imageMap, Bmp\InfoHeader $header, $colorTable, $xor, $resource) {
        $this->_imageMap         = $imageMap;
        $this->_header           = $header;
        $this->_colorTable       = $colorTable;
        $this->_xorMask          = $xor;
        $this->_resource         = $resource;
        //var_dump($this);

    }

    static public function unpack($data, ImageMap $imageMap, Bmp\InfoHeader $header) {
        //var_dump($data);
        $f = \LibreMVC\Traits\Bin::binToStream($data);
        fseek($f,$imageMap->getOffset());
        $totalColors = $imageMap->getPalette();
        $colors = array();
        $img = imagecreatetruecolor($imageMap->getWidth(), $imageMap->getHeight());

        // Palette
        if( $totalColors > 0 ) {
            for( $i=0; $i < $totalColors;$i++ ) {
                $colors[$i+1] = array(
                    "b" => unpack('C',fread($f,1))[1],
                    "g" => unpack('C',fread($f,1))[1],
                    "r" => unpack('C',fread($f,1))[1],
                    //"a" => unpack('C',fread($f,1))[1]
                );
                fseek($f,ftell($f));
            }
        }
        else {
            // Couleurs non indexées.
            // Skip header
            rewind($f);
            fseek($f,ftell($f)+40);

            // All y
            for( $i = 0; $i < $imageMap->getHeight(); $i++ ) {
                // All x
                for( $j = 0; $j < $imageMap->getWidth(); $j++ ) {
                    // First word blank
                    $colors = array(
                        "b" => unpack('C',fread($f,1))[1],
                        "g" => unpack('C',fread($f,1))[1],
                        "r" => unpack('C',fread($f,1))[1],
                        "a" => unpack('C',fread($f,1))[1]
                    );

                    $alpha = $colors['a'] - 255 >> 1;
                    //var_dump($colors);
                    //var_dump($alpha);
                    imagesetpixel($img,$j,$i,imagecolorallocatealpha($img, $colors['r'],$colors['g'],$colors['b'],$alpha));
                }
            }

        }

        //header('Content-Type: image/png');



        //imagepng( $img );


        return new self($imageMap,$header,$colors,$colors,$img);
    }

} 