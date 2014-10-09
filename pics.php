<?php
use LibreMVC\Img;

ini_set('display_errors', 'on');
include('core/img/autoload.php');

try {
    //$img = new Img('pics/transparent.png');
    $img = new Img('pics/chat.jpg');
    //var_dump($img);
    //$img = new Img('pics/big.jpg');
    //$img = new Img('pics/mask.png');
    $img->resize(150, 150);
    //$img->pattern('pics/chat.jpg');
    //$img->pattern('pics/pattern.png');
    //$img->pattern('pics/mask.png');
    $img->mask('pics/mask.png');
    //$img = new Img('pics/mask.png');
    $img->display();
}
catch (\Exception $e) {
    echo $e->getMessage();
}