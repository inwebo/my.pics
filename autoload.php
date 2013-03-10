<?php
/**
  * My Framework : My.Forms
  *
  * LICENCE
  *
  * You are free:
  * to Share ,to copy, distribute and transmit the work to Remix —
  * to adapt the work to make commercial use of the work
  *
  * Under the following conditions:
  * Attribution, You must attribute the work in the manner specified by
  *   the author or licensor (but not in any way that suggests that they
  *   endorse you or your use of the work).
  *
  * Share Alike, If you alter, transform, or build upon
  *     this work, you may distribute the resulting work only under the
  *     same or similar license to this one.
  *
  *
  * @category   My.Forms
  * @package    Extra
  * @copyright  Copyright (c) 2005-2011 Inwebo (http://www.inwebo.net)
  * @license    http://http://creativecommons.org/licenses/by-nc-sa/3.0/
  * @version    $Id:$
  * @link       https://github.com/inwebo/My.Forms
  * @since      File available since Beta 01-10-2011
  */

/**
 * Permet d'inclure automatiquement les fichiers de classes, interfaces, modules
 * 
 * Recherche en premier le fichier dans ou $class est le nom de la class voulue.
 * 
 * core/$class/class.$class.php
 * core/$class/interface.$class.php
 * sites/default/modules/class.$class.php
 * modules/class.$class.php
 * 
 * @param String $class Le nom de la classe
 * @return void
 */

function __autoload($class) {

    $pathAsArray = explode('\\', $class);
    $core = "core";
    $currentModules ="sites/default";
    $pathAsArray = array_map("strtolower",$pathAsArray);
    $file = 'class.' . strtolower($pathAsArray[count($pathAsArray) -1 ]) . '.php';
    array_push($pathAsArray, $file);
    $coreClass = str_replace('libremvc', $core, implode("/", $pathAsArray));
    
    // Core classes
    if( is_file($coreClass) ) {
        include ($coreClass);
        return;
    }
    
    // Shared modules
    $modules = str_replace('libremvc', $currentModules, implode("/", $pathAsArray));
    if( is_file($modules) ) {
        include ($modules);
        return;
    }

    // Current modules
    $modules = trim(str_replace('libremvc', '', implode("/", $pathAsArray)),"/");
    if( is_file($modules) ) {
        include ($modules);
        return;
    }

}
