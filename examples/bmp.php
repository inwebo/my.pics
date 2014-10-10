<?php
use LibreMVC\Img;

include('../core/img/autoload.php');

try {
    $img = new Img\Driver\Bmp('../pics/chat2.bmp');
    $img->display();
}
catch (\Exception $e) {
    echo $e->getMessage();
}