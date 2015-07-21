<?php

    use LibreMVC\Img;

    ini_set('display_errors', 'on');

    include( '../core/traits/autoload.php' );
    include( '../core/img/autoload.php' );
    include( '../core/bin/autoload.php' );


    $picsDir = '../pics/';
    $distant = 'http://www.cfma.org/files/PageLayoutImages/icon_social_linkedIn.jpg';
    $picsFile = ( $_GET['f'] === "distant" ? $distant : $_GET['f']  );
    $picsPath = ( $picsFile === "distant" ? $distant : $picsDir . $picsFile  );
try{

    $img = new Img($picsPath);
    if( !isset($_GET['d']) ) {

        $img->display();
    }

}
catch (\Exception $e) {
    var_dump($e);
}