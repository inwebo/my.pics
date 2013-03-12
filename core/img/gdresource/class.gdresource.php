<?php
/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC\Img;

use LibreMVC\Img as Img;

/**
 * Image object
 */
class GdResource extends Img {

    public $filename = null;
    public $file = null;
    public $path = null;
    public $mime = null;
    public $width = null;
    public $height = null;
    protected $resource = null;
    public $bits = null;

    public function __construct($width, $height, $color) {
        $this->resource = $this->resourceFactory($width, $height, $color);
        $this->height = imagesy($this->resource);
        $this->width = imagesx($this->resource);
    }

    protected function resourceFactory($width = 150, $height=80, $color = array('r'=> 255,'g'=>255,'b'=>255,"alpha"=>127), $colorMode = "TRUE_COLOR" ) {
        switch (strtolower($colorMode)) {
            default :
            case "true_color" :
                $pictures = imagecreatetruecolor($width, $height);
                $background = imagecolorallocatealpha($pictures, $color['r'], $color['g'], $color['b'], $color['alpha']);
                imagefill($pictures, 0, 0, $background);
                imagesavealpha($pictures, true);
                $this->bits = 32;
                break;

            case "indexed" :
                $pictures = imagecreate($width, $height);
                $background = imagecolorallocatea($pictures, $color['r'], $color['g'], $color['b']);
                imagefill($pictures, 0, 0, $background);
                $this->bits = 1;
                break;

        }
        
        return $pictures;
    }
    
}