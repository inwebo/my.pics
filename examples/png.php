<?php
use LibreMVC\Img;

include('../core/img/autoload.php');

try {
    $img = new Img('../pics/transparent.png');
    $img->resize(125,125);
    $img->display();
}
catch (\Exception $e) {
    echo $e->getMessage();
}