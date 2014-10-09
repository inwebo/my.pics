<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 09/10/14
 * Time: 16:19
 */

namespace LibreMVC\Img;


class ImgActionable extends ImgBase{
    protected $_actions = array();

    /**
     * Sauvegarde des differents traitement éffectués sur l'image MAIS ne modifie pas l'image source.
     *
     * @arguments 	STRING $file Chemin d'un fichier pour sauvegarder les traitements
     *
     *
     * @return    	BOOL 1 En cas de succès de la sauvegarde
     *
     * @throw EXCEPTION si $file n'est pas disponible en écriture
     */
    public function saveActions( $file ) {
        if ( !is_writable( $file ) && !fopen( $file , "w" ) ) {
            throw new Exception("$file is not writable");
        }
        $string   = "";
        $compteur = 1;

        foreach( $this->stack as $value ) {
            $string.= serialize($value) . "\n";
            ++$compteur;
        }

        $fp = fopen( $file, 'w+' );
        fwrite( $fp, utf8_encode ( $string ) );
        fclose( $fp );
        return TRUE;
    }


    /**
     * Charge une série de traitement à éffectués sur une image, ATTENTION le fichier sauvegardé DOIT
     * être dans le même dossier que l'image source.
     *
     * @arguments 	STRING $path Chemin d'accès pour charger les traitements
     *
     *
     * @return    	OBJECT Un nouvel objet Pics
     *
     */
    public function loadActions( $path ) {

        $lines = file($path);
        $compteur = 0;

        foreach ($lines as $content) {
            $temp = unserialize($content);
            // Juste le nom de la méthode à appliquée
            $methode = $temp[0];
            array_shift($temp);

            // Si ce n'est pas le constructeur
            if( $compteur != 0 ) {
                call_user_func_array( array($ret, $methode),  $temp[0]);
            }
            // Si c'est le constructeur nous utilisons la reflectivité pour passer des arguments à la méthode __construct
            // chose impossible avec call_user_func_array()
            else {
                // Les arguments doivent être sous forme de chaine et non pas de tableau
                $callArgs = '';
                foreach($temp as $args) {
                    $callArgs .= $args;
                }
                $reflect	= new ReflectionClass( get_class() );
                $ret		= $reflect->newInstanceArgs( $args );
            }
            $compteur++;
        }

        return $ret;
    }
} 