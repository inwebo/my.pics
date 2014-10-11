<?php
use LibreMVC\Img;

include('../core/img/autoload.php');

try {
    //header('Content-Type: image/gif');
    //echo file_get_contents('../pics/php.gif');
    $img = new Img('../pics/php.gif');
    $img->resize(300,67);
    $img->display();
}
catch (\Exception $e) {
    echo $e->getMessage();
}