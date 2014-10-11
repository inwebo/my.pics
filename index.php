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
        $value = "00000001 00000000 00000100 00000000";
        echo $value;
        $highMap = 0xffffffff00000000;
        $lowMap = 0x00000000ffffffff;
        $higher = ($value & $highMap) >>32;
        $lower = $value & $lowMap;
        $packed = pack('NN', $higher, $lower);

        list($higher, $lower) = array_values(unpack('N2', $value));
        $originalValue = $higher << 32 | $lower;
        var_dump($originalValue);

        //$img = new \LibreMVC\Img("http://php.net/favicon.ico");
        //$img = new \LibreMVC\Img("http://cdn.sstatic.net/stackoverflow/img/favicon.ico?v=038622610830");
        //$img = new \LibreMVC\Img("http://static.php.net/www.php.net/images/php.gif");
        //$img->save('pics/foo.jpg');
        //$img = new \LibreMVC\Img("mages/php.gif");
        //$img = new Img("pics/php.gif");
        //var_dump($img);
        //var_dump( getimagesize("pics/php.gif") );
        //var_dump( getimagesize("http://php.net/favicon.ico") );
        //var_dump( memory_get_usage() );
        //$img = new Img();
        //var_dump($img);
        $img = new Img("http://www.cfma.org/files/PageLayoutImages/icon_social_linkedIn.jpg");
        //$img->resize(800,800);
        //$img->save('./pics/big-test.jpg');
        //var_dump($img->getPalette());
        //$img = new Img($jpg);
        //$img->save();
        //var_dump($img);
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
<h3>Jpg</h3>
<a href="examples/jpg.php" target="_blank"><img src="examples/jpg.php" /></a>
<h3>BMP</h3>
<a href="examples/bmp.php" target="_blank"><img src="examples/bmp.php" /></a>
<h3>Png</h3>
<a href="examples/png.php" target="_blank"><img src="examples/png.php" /></a>
<h3>Gif</h3>
<a href="examples/gif.php" target="_blank"><img src="examples/gif.php" /></a>
<h3>Animated Gif</h3>
<a href="examples/agif.php" target="_blank"><img src="examples/agif.php" /></a>
<h3>Ico</h3>
<a href="examples/ico.php" target="_blank"><img src="examples/ico.php" /></a>
</body>
</html>
<hr>

