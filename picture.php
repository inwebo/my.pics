<?php
namespace LibreMVC;
include 'autoload.php';
$img = new Img('assets/picture.jpg');
$img->merge("assets/id-inwebo.jpg")->resize(30,500)->pattern("assets/pattern.png")->display();