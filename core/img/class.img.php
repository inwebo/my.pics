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
 * <p>Représentation sous forme d'objet d'une image locale ou provenant du web.</p>
 * <p>Ces objets sont facilement éditables. Ils peuvent être rogner (crop), redimmensionner
 * leurs ajouter un masque de transparence ou leur appliquer un filtre prédéfini.</p>
 * <p>Pour chaques manipulation d'un objet le traitement est sauvegardé dans l'attribut
 * publique <code>$actions</code>. Ce qui permet une automatisation des traitements de lots
 * d'image plus simple. Les actions sont sauvegardé dans un fichier séparé de l'image.Ce
 * qui permet une sauvegarde non destructives des images.</p>
 * <p>Tous les objet images <b>sont</b> sérializables. Grâce aux methodes __sleep & __wakeup.
 * ce qui leur permet d'être facilement sauvegardables en BDD</p>
 * <p>La syntaxe d'écriture permet de chainer les modifications, elle ressemble fortement à
 * Jquery</p>
 * 
 * <code>
 * // Charge une image, la redimmensionne en 200x250 px puis la sauvegarde dans un nouveau fichier
 * // Rogne un carré de 20x20 px en son centre et retourne le résulat des modifications.
 * Img::Load('test.jp')->resize(200,250)->saveAs('test2.png','PNG')->crop(20,20)->display().
 * </code>
 * 
 * @package LibreMVC
 * @subpackage Img
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
     * Les modifications effectuées sur la ressource courante.
     * @var array 
     */
    public $actions = array();

    /**
     * Peuple les attributs de l'objet
     * 
     * @link http://php.net/manual/fr/function.file-get-contents.php Le périmètre de validité des images.
     * @param string $filname Un chemin locale d'une image OU une image provenant du web.
     * @return void
     */
    public function __construct($filename) {
        $this->getImageInfos($filename);
    }
    
    /**
     * Setter attributs
     * @param string $filename
     */
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
     * Contrtuit la ressource courante depuis une chaine de caractères.
     * 
     * @link http://php.net/manual/fr/function.imagecreatefromstring.php Fonctionnement
     * @param type $filename A local or remote picture.
     * @return gd Gdresource created from file.
     */
    protected function resourceFactory( $filename ) {
        return imagecreatefromstring(file_get_contents($filename));
    }

    /**
     * Charge une image existante et valide.
     * @param string $filename Chemin d'accès d'une image valide.
     * @return \LibreMVC\Img
     */
    static public function load($filename) {
        return new Img($filename);
    }

    /**
     * Contruit une nouvelle ressource d'image GD.
     * 
     * @param int $width Largeur
     * @param int $height Hauteur
     * @param array $color Tableau associatif. Par défaut : <code>
     * array('r'=>255,'g'=>255, 'b'=>255, "alpha"=>127);
     * </code><p>Ou r,g,b vaut de [0,255] et alpha [0,127]
     * @return \LibreMVC\Img\GdResource
     */
    static public function create($width, $height, $color = array('r' => 255, 'g' => 255, 'b' => 255, 'alpha' => 127)) {
        return new GdResource($width, $height, $color);
    }

    /**
     * Affiche dans la sortie courante la ressource courante.
     * @param bool $fromFile Si true retourne le header, sinon une chaine de caractères.
     * @param string $flag Le mime type de l'image souhaité, parmi JPEG, GIF, PNG
     */
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

    /**
     * Sauvegarde la ressource courante dans un nouveau fichier.
     * <b>Attention la destination doit être disponible en écriture.</b>
     * @param string $toFile Fichier de destination
     * @param string $format Mime type de l'image de destination
     * @param int $quality Si est un PNG [0,9], si JPG [0,100]
     * @return \LibreMVC\Img
     * @throws Exception Si le mime type souhaité est inconnu.
     */
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
    
    /**
     * Sauvegarde la ressource courante dans son fichier d'origine.
     * <b>Attention la destination doit être disponible en écriture.</b>
     * @param string $format Mime type de l'image de destination
     * @param int $quality Si est un PNG [0,9], si JPG [0,100]
     * @return \LibreMVC\Img
     * @throws Exception Si le mime type souhaité est inconnu.
     */
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

    /**
     * Fusionne <code>$toMerge</code> dans la ressource courante
     * @param string $toMerge Un fichier image
     * @param string $target Ou la fusion doit elle être faite dans la ressource courante.
     * Parmi :<code>TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, TOP_LEFT</code>
     * @return Img
     */
    public function merge($toMerge, $target = "TOP") {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::merge($this, $toMerge, $target);
    }

    /**
     * Redimmensionne la ressource courante.
     * @param int $width Largeur
     * @param int $height Hauteur
     * @return Img
     */
    public function resize($width = null, $height = null) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::resize($this, $width, $height);
    }

    /**
     * Applique un masque d'opacité sur la ressource courante.
     * @param string $mask Un masque d'opacité, <b>La canal alpha de l'image doit être présent</b>
     * @return Img
     */
    public function mask($mask) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::mask($this, $mask);
    }

    /**
     * Applique un pattern sur l'ensemble de la ressource courante.
     * @param string $pattern Chemin d'accés d'un fichier pattern.<b>La canal alpha de l'image doit être présent</b>
     * @return Img
     */
    public function pattern($pattern) {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::pattern($this, $pattern);
    }

    /**
     * Rogne la ressource courante.
     * @param array $crop Un tableau d'entier <code>array(200,20)</code> Largeur, hauteur.
     * @param string $target Ou le rognage doit-il commencé
     * @return Img
     */
    public function crop($crop, $target = "CENTER") {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Edit::crop($this, $crop, $target);
    }

    /**
     * Filtre sur la ressource courante.
     * @link http://www.php.net/manual/fr/function.imagefilter.php A voir pour les paramétres
     * @return Img
     */
    public function filter($filtre, $filtre_param_1 = '', $filtre_param_2 = '', $filtre_param_3 = '', $filtre_param_4 = '') {
        $this->actions[] = array(__FUNCTION__, func_get_args());
        return Filter::filter($this, $filtre, $filtre_param_1, $filtre_param_2, $filtre_param_3, $filtre_param_4);
    }

    /**
     * Sauvegarde les modifications effectuées sur la ressource courante dans un fichier $actionsFile
     * @param string $actionsFile Le fichier de destination.
     */
    public function saveActions($actionsFile) {
        $fp = fopen($actionsFile, 'w+');
        fwrite($fp, serialize($this->actions));
        fclose($fp);
    }

    /**
     * Applique un ensemble d'action sur la ressource courante.
     * @param string $actionsFile Le fichier source des actions.
     */
    public function runActions($actionsFile) {
        $f = file($actionsFile);
        $actions = unserialize( $f[0] );
        foreach ($actions as $key => $value) {
            call_user_func_array(array($this, $value[0]), $value[1]);
        }
        return $this;
    }

    /**
     * Remise à zéro des actions.
     */
    public function resetActions() {
        $this->actions = array();
    }

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

}