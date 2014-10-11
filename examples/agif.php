<?php
use LibreMVC\Img;

include('../core/img/autoload.php');

try {
    $img = new Img('../pics/animated.gif');
    $img->resize(125,125);
    $img->display();
    //header('Content-Type: image/gif');
    //echo file_get_contents('../pics/animated.gif');
}
catch (\Exception $e) {
    echo $e->getMessage();
}