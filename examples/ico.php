<?php
use LibreMVC\Img;

include('../core/img/autoload.php');
include('../core/bin/autoload.php');

try {
    ini_set('display_errors', 'on');
    //$img = new Img('../pics/wikipedia.ico');
    //$img = new Img\Driver\Ico('../pics/wikipedia.ico');
    $img = new Img\Driver\Ico('../pics/a.ico');

    //var_dump($img);
    //var_dump($img->display());
    //header('Content-Type: image/ico');
    //echo file_get_contents('../pics/wikipedia.ico');
}
catch (\Exception $e) {
    echo $e->getMessage();
}