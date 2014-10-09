<?php
include('../helpers/files/iio/class.iio.php');
use LibreMVC\Img;

    ini_set('display_errors', 'on');
    include('core/img/autoload.php');
    //$img = \LibreMVC\Img::load("http://cdn.sstatic.net/stackoverflow/img/favicon.ico?v=038622610830");

    $folder = './pics/';

    $jpg ="./pics/chat.jpg";
    $gif ="./pics/php.gif";
    $png ="./pics/transparent.png";

    $ico ="http://php.net/favicon.ico";

    try {
        //$img = new \LibreMVC\Img("http://php.net/favicon.ico");
        //$img = new \LibreMVC\Img("http://cdn.sstatic.net/stackoverflow/img/favicon.ico?v=038622610830");
        //$img = new \LibreMVC\Img("http://static.php.net/www.php.net/images/php.gif");
        //$img->save();
        //$img = new \LibreMVC\Img("mages/php.gif");
        //$img = new Img("pics/php.gif");
        //var_dump($img);
        //var_dump( getimagesize("pics/php.gif") );
        //var_dump( getimagesize("http://php.net/favicon.ico") );
        //var_dump( memory_get_usage() );
        //$img = new Img();
        //var_dump($img);
        $img = new Img("http://www.cfma.org/files/PageLayoutImages/icon_social_linkedIn.jpg");
        $img->resize(800,800);
        //$img->save('./pics/big.jpg');
        //var_dump($img->getPalette());
        //$img = new Img($jpg);
        //$img->save();
        var_dump($img);
        //$img = new Img('pics/transparent.png');
        //var_dump($img);
        //var_dump(getimagesize("t"));
        //$img = new Img($jpg);
    }
    catch (\Exception $e) {
        echo $e->getMessage();
    }

/**
 * @param $va Convert ini data 32M en octets.
 */
function getIniDirective( $va ) {

}

?>
<html>
<head>

</head>
<body style="background-color: darkgray">
<a href="pics.php" target="_blank"><img src="pics.php" /></a>
</body>
</html>
<hr>

