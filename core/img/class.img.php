<?php

/**
 * Yeah
 * @package LibreMVC
 * @subpackage Image
 */

namespace LibreMVC;

use LibreMVC\Img\GdResource as GdResource;
use LibreMVC\Img\Edit as Edit;
use LibreMVC\Img\Filter as Filter;

/**
 * Image object
 */
class Img {

    /**
     * Full path to local or remote pictures
     * @var string
     */
    public $filename = null;

    /**
     * File name with its extention
     * @var string
     */
    public $file = null;

    /**
     * Full path to local or remote pictures without file.
     * @var string
     */
    public $path = null;

    /**
     * Current picture mime-type
     * @var string
     */
    public $mime = null;

    /**
     * Current picture width
     * @var int
     */
    public $width = null;

    /**
     * Current picture height
     * @var int
     */
    public $height = null;

    /**
     * Current picture gd resource
     * @var GD
     */
    public $resource = null;

    /**
     * Bit color per channel
     * @var int
     */
    public $bits = null;

    /**
     * 
     * @var array
     */
    public $actions = array();

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

    /**
     * 
     * @param type $filename A local or remote picture.
     * @return gd Gdresource created from file.
     */
    protected function resourceFactory($filename) {
        return imagecreatefromstring(file_get_contents($filename));
    }

    static public function load($filename) {
        return new Img($filename);
    }

    static public function create($width, $height, $color = array('r' => 255, 'g' => 255, 'b' => 255, 'alpha' => 127)) {
        return new GdResource($width, $height, $color);
    }

    public function preserveAlpha() {
        imagealphablending($this->resource, false);

        $colorTransparent = imagecolorallocatealpha
                (
                $this->resource, 255, 255, 255, 0
        );

        imagefill($this->resource, 0, 0, $colorTransparent);
        imagesavealpha($this->resource, true);
    }

    public function display($fromFile = true, $flag = "png") {
        switch (strtoupper($flag)) {

            case "JPEG":
            case "JPG":
                if ($fromFile) {
                    header('Content-Type: image/jpeg');
                }
                imagejpeg($this->resource);
                imagedestroy($this->resource);
                break;

            case "GIF":
                if ($fromFile) {
                    header('Content-Type: image/gif');
                }
                imagegif($this->resource);
                imagedestroy($this->resource);
                break;

            case "PNG":
            default:
                if ($fromFile) {
                    header('Content-Type: image/png');
                }
                imagealphablending($this->resource, false);
                imagesavealpha($this->resource, true);
                imagepng($this->resource);
                imagedestroy($this->resource);
                break;
        }
    }

    public function saveAs($toFile, $format = "png", $quality = 5) {
        $toFileInfos = pathinfo($toFile);
        switch (strtoupper($format)) {
            case "PNG" :
                ( is_int($quality) && $quality >= 0 && $quality <= 9) ?
                                imagepng($this->resource, $toFileInfos['dirname'] . '/' . $toFileInfos['basename'], $quality) :
                                null;
                break;

            case "JPG" :
            case "JPEG" :
                ( is_int($quality) && $quality >= 0 && $quality <= 100) ?
                                imagejpeg($this->resource, $toFileInfos['dirname'] . '/' . $toFileInfos['basename'], $quality) :
                                null;
                break;

            case "GIF" :
                return imagegif($this->resource, $toFileInfos['dirname'] . '/' . $toFileInfos['basename'] . '.gif');
                break;

            default :
                throw new Exception("Unknown format $format, please try PNG, JPG, GIF");
                break;
        }
        return $this;
    }

    public function save($format = "png", $quality = 5) {
        switch (strtoupper($format)) {
            case "PNG" :
                
                imagealphablending($this->resource, false);
                imagesavealpha($this->resource, true);
                
                ( is_int($quality) && $quality >= 0 && $quality <= 9) ?
                                imagepng($this->resource, $this->filename, $quality) :
                                null;
                break;

            case "JPG" :
            case "JPEG" :
                ( is_int($quality) && $quality >= 0 && $quality <= 100) ?
                                imagejpeg($this->resource, $this->filename, $quality) :
                                null;
                break;

            case "GIF" :
                return imagegif($this->resource, $this->filename);
                break;

            default :
                throw new Exception("Unknown format $format, please try PNG, JPG, GIF");
                break;
        }
    }

    public function merge($toMerge, $target = "TOP") {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::merge($this, $toMerge, $target);
    }

    public function resize($width = null, $height = null) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::resize($this, $width, $height);
    }

    public function mask($mask) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::mask($this, $mask);
    }

    public function pattern($pattern) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::pattern($this, $pattern);
    }

    public function crop($crop, $target = "CENTER") {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::crop($this, $crop, $target);
    }

    public function filter($filtre, $filtre_param_1 = '', $filtre_param_2 = '', $filtre_param_3 = '', $filtre_param_4 = '') {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Filter::filter($this, $filtre, $filtre_param_1, $filtre_param_2, $filtre_param_3, $filtre_param_4);
    }

    public function saveActions($actionsFile) {
        $fp = fopen($actionsFile, 'w+');
        fwrite($fp, serialize($this->actions));
        fclose($fp);
    }

    public function runActions($actionsFile) {
        $actions = unserialize(file($actionsFile)[0]);
        foreach ($actions as $key => $value) {
            call_user_func_array(array($this, $value[0]), $value[1]);
        }
        return $this;
    }

    public function resetActions() {
        $this->actions = array();
    }

    public function __sleep() {
        ob_start();
        $this->display(false);
        $imgBase64 = ob_get_contents();
        ob_end_clean();
        $this->resource = base64_encode($imgBase64);
        return array("filename", "file", "path", "path", "mime", "width", "height", "resource", "bits");
    }

    public function __wakeup() {
        $this->resource = imagecreatefromstring(base64_decode($this->resource));
    }

}