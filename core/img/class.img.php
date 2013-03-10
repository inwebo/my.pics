<?php

/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC;

use LibreMVC\Img\GdResource as GdResource;
use LibreMVC\Img\Edit as Edit;

/**
 * Image object
 */
class Img {

    public $filename = null;
    public $file = null;
    public $path = null;
    public $mime = null;
    public $width = null;
    public $height = null;
    public $resource = null;
    public $bits = null;

    public function __construct($filename) {
        
        $this->getImageInfos($filename);
    }

    protected function getImageInfos($filename) {
        $infos = getimagesize($filename);
        $this->filename = $filename;
        $this->file = basename($filename);
        $this->path = dirname($filename) . "/";
        $this->mime = strtolower($infos['mime']);
        $this->width = $infos[0];
        $this->height = $infos[1];
        $this->resource = $this->resourceFactory($filename);
        $this->bits = $infos['bits'];
    }

    protected function resourceFactory( $filename ) {
        // @link http://www.php.net/manual/fr/function.imagecreatefromstring.php#85909
        return imagecreatefromstring(file_get_contents($filename));
    }

    static public function load( $filename ) {
        return new Img( $filename );
    }

    static public function create( $width, $height, $color = array('r'=>255, 'g'=>255, 'b'=>255, 'alpha'=>127 ) ) {
        return new GdResource( $width, $height, $color );
    }
    
    public function display( $fromFile = true, $flag = "png"  ) {
        
        switch ( strtoupper( $flag ) ) {

            case "JPEG":
            case "JPG":
                if($fromFile) {
                    header('Content-Type: image/jpeg');
                }
                imagejpeg($this->resource);
                imagedestroy($this->resource);
                break;

            case "GIF":
                if($fromFile) {
                    header('Content-Type: image/gif');
                }
                imagegif($this->resource);
                imagedestroy($this->resource);
                break;
            
            case "PNG":
            default:
                if($fromFile) {
                    header('Content-Type: image/png');
                }
                imagepng($this->resource);
                imagedestroy($this->resource);
                break;
        }
    }
    
    public function __sleep() {
        ob_start();
        $this->display(false);
        $imgBase64 = ob_get_contents();
        ob_end_clean();
        $this->resource = base64_encode($imgBase64);
        return array("filename","file","path","path","mime","width","height","resource","bits");
    }
    
    public function save( $toFile, $format = "png", $quality = 5 ) {
        $toFileInfos = pathinfo($toFile);
        switch (strtoupper($format)) {
            case "PNG" :
                ( is_int( $quality ) && $quality >= 0 && $quality <= 9) ?
                    imagepng($this->resource, $toFileInfos['dirname'] . '/' . $this->file . '.png', $quality) :
                    null;
                break;

            case "JPG" :
            case "JPEG" :
                (is_int($quality) && $quality >= 0 && $quality <= 100)  ?
                    imagejpeg($this->_gdPics, $toFileInfos['dirname'] . '/' . $this->file . '.jpg', $quality) :
                    null ;
                break;

            case "GIF" :
                return imagegif($this->_gdPics, $toFileInfos['dirname'] . '/' . $this->file . '.gif');
                break;

            default :
                throw new Exception("Unknown format $format, please try PNG, JPG, GIF");
                break;
        }
    }
    
    public function merge( $toMerge, $target = "TOP" ) {
        return Edit::merge($this, $toMerge, $target);
    }

    public function resize($width = null , $height = null) {
        return Edit::resize($this,$width, $height);
    }

    public function mask($mask) {
        return Edit::mask($this, $mask);
    }
    
    public function pattern($pattern) {
        return Edit::pattern($this, $pattern);
    }
    public function crop($crop, $target = "CENTER" ){
        return Edit::crop($this, $crop, $target);
    }
    public function __wakeup() {
        $this->resource = imagecreatefromstring( base64_decode( $this->resource ) );
    }
}