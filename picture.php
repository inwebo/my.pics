<?php
namespace LibreMVC;
include 'autoload.php';
$img = Img::load('assets/picture.jpg')->filter(IMG_FILTER_NEGATE)->display();
//var_dump(IMG_FILTER_NEGATE);

