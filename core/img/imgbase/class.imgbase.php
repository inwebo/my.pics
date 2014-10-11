<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 09/10/14
 * Time: 16:01
 */

namespace LibreMVC\Img;

use LibreMVC\Img\Driver\Bmp;
use LibreMVC\Img\Driver\Jpg;
use LibreMVC\Img\Driver\Gif;
use LibreMVC\Img\Driver\Png;
use LibreMVC\Img\Driver\Ico;

class ImgException extends \Exception {}
class distantResourceException extends \Exception {}

class ImgBase {
    protected $_path;

    protected $_mimeType;
    protected $_width;
    protected $_height;
    protected $_bits;
    protected $_channels;

    protected $_driver;

    #region Construct
    public function __construct( $path = null ){
        //$this->init();

        // New
        if( is_null( $path ) ) {
            $this->_driver    = $this->defaultResourceFactory();
            $this->_width       = 1;
            $this->_height      = 1;
            $this->_bits        = 8;
            $this->_channels    = 4;
        }
        // Loads
        else {
            $this->setterImgInfos( $path );
            $this->_driver    = $this->resourceFactory( $path );
            $this->_path      = $path;
        }
        $this->_path = $path;

    }

    public function convertTo($type){
        switch($type) {
            case 'png':
                $this->_driver = $this->getDriver()->convertTo('png');
                break;
        }

    }

    static public function load( $path ) {
        $class = get_called_class();
        return new $class( $path );
    }

    protected function init(){
        self::$_memoryLimit = (int) trim( ini_get('memory_limit') ,'M' ) * 1024 * 1024 - memory_get_usage();
        //echo self::$_memoryLimit;
    }

    protected function resourceFactory( $path ) {

        // Selon le mime-type le bon type d'objet.
        switch($this->_mimeType) {
            default:
            case image_type_to_mime_type(IMAGETYPE_PNG):
                return new Png($path);
                break;

            case 'image/jpg':
            case image_type_to_mime_type(IMAGETYPE_JPEG):
                return new Jpg($path);
                break;

            case image_type_to_mime_type(IMAGETYPE_GIF):
                return new Gif($path);
                break;

            case image_type_to_mime_type(IMAGETYPE_BMP):
                return new Bmp($path);
                break;

            case image_type_to_mime_type(IMAGETYPE_ICO):
                return new Ico($path);
                break;
        }

    }

    protected function setterImgInfos( $path ) {
        $infos = @getimagesize( $path );

        if($infos === false) {
            throw new ImgException('File, `' . $path . '` does not exist.');
        }

        $this->_mimeType    = $infos['mime'];
        $this->_width       = $infos[0];
        $this->_height      = $infos[1];
        $this->_bits        = $infos['bits'];

        // @todo : Fixe crade
        $infos['channels'] = (isset($infos['channels'])) ? $infos['channels'] : -1;
        $this->_channels = $infos['channels'];

        /**
         * PNG doesnt return channels
         * @see : http://php.net/manual/en/function.getimagesize.php#105033
         * @see : http://www.fileformat.info/mirror/egff/ch02_02.htm
         */
        //echo (ord(@file_get_contents($this->_path, NULL, NULL, 25, 1)) == 6);
    }

    /**
     * Default 1*1 px transparent
     * @see : http://php.net/manual/en/function.imagecreatetruecolor.php#75298
     * @see : http://php.net/manual/en/function.imagecreatetruecolor.php#54025
     * @todo : Test
     */
    protected function defaultResourceFactory($width = 1, $height = 1) {
        $gd     = imagecreatetruecolor($width, $height);
        $color  = imagecolorallocate($gd,0,0,0);
        imagecolortransparent($gd,$color);
        return $gd;
    }

    static protected function isValidPath( $path ) {
        // Le dossier parent est accessible en ecriture
        return is_writable( dirname( $path ) );
    }
    #endregion

    #region Getters

    public function getWidth() {
        return $this->_width;
    }

    public function getHeight() {
        return $this->_height;
    }

    public function getMimeType() {
        return $this->_mimeType;
    }

    public function getDriver() {
        return $this->_driver;
    }

    public function getBits() {
        return $this->_bits;
    }

    public function getInfos() {
        return array(
            'width'     => $this->getWidth(),
            "height"    => $this->getHeight(),
            "mime-type" => $this->getMimeType(),
            "bits"      => $this->getBits()
        );
    }

    #endregion

    #region Setters
    public function set( $width, $height, $bits, $channels ) {
        $this->_width       = $width;
        $this->_height      = $height;
        $this->_bits        = $bits;
        $this->_channels    = $channels;
    }
    #endregion

    #region Wrapper
    public function display($toString = false) {
        $this->_driver->display( $toString );
    }

    public function save( $path = null, $quality = null ) {
        //Fichier et format d'origine
        if( is_null( $path ) ) {
            // Est un fichier local accessible en ecriture
            if( self::isValidPath( $this->_path ) ) {
                $this->_driver->save($this->_path, $quality);
            }
            // Est un fichier distant nécessite un dossier de destination valide.
            else {
                throw new distantResourceException('Distant file : `' . $this->_path . '` need a target writable destination dir.');
            }
        }
        // Nouveau fichier
        else {
            // Fichier local
            if( self::isValidPath($path) ) {
                $this->_driver->save($path, $quality);
            }
            // Specifier la destination
            else {
                throw new distantResourceException('Cannot save file : '. $path .' in ' . dirname($path) . ' check if is writable destination');
            }
        }
    }

    #endregion Wrapper

    #region Serialize
    /**
     * Permet la sérialization de l'objet courant <b>AVEC</b> la ressource GD associée.
     * @return array Les attributs à sauvegardés en base.
     */
    public function __sleep() {
        ob_start();
        $this->display(false);
        $imgBase64 = ob_get_contents();
        ob_end_clean();
        $this->resource = base64_encode($imgBase64);
        return array("filename", "file", "path", "path", "mime", "width", "height", "resource", "bits");
    }

    /**
     * Permet la désérialization de l'objet courant <b>AVEC</b> la ressource GD associée.
     * @return Img
     */
    public function __wakeup() {
        $this->resource = imagecreatefromstring(base64_decode($this->resource));
    }
    #endregion
} 