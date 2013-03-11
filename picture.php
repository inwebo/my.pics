<?php
namespace LibreMVC;
include 'autoload.php';
$img = Img::load('assets/picture.jpg')->pattern("assets/pattern.png")->resize(40,40)->display();
//var_dump($img);

