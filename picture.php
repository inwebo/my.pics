<?php
namespace LibreMVC;
include 'autoload.php';
$l = Img::load('assets/picture.jpg')->extractColorPalette();
//$b = Img::load('assets/picture-830.png')->merge($l)->display();
var_dump($l);

