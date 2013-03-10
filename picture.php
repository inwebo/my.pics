<?php
namespace LibreMVC;
include 'autoload.php';
//$img = new Img('assets/picture.jpg');

$img = Img::load('assets/picture.jpg')->pattern("assets/pattern.png")->resize(500)->display();
var_dump($img);
