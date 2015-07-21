<?php

    ini_set('display_errors', 'on');
    include 'core/autoload.php';
    $distant = 'http://php.net/images/logo.php';

?>
<html>
<head>

</head>
<body style="background-color: whitesmoke">
<h3>Jpg</h3>
<a href="demo/demo.php?f=inwebo.jpg" target="_blank"><img src="demo/demo.php?f=inwebo.jpg"/></a>
<h3>BMP</h3>
<a href="demo/demo.php?f=chat2.bmp" target="_blank"><img src="demo/demo.php?f=chat2.bmp"/></a>
<h3>Png</h3>
<a href="demo/demo.php?f=transparent.png" target="_blank"><img src="demo/demo.php?f=transparent.png"/></a>
<h3>Gif</h3>
<a href="demo/demo.php?f=php.gif" target="_blank"><img src="demo/demo.php?f=php.gif"/></a>
<h3>Animated Gif</h3>
<a href="demo/demo.php?f=animated.gif" target="_blank"><img src="demo/demo.php?f=animated.gif"/></a>
<h3>Ico</h3>
<a href="demo/demo.php?f=wikipedia.ico" target="_blank"><img src="demo/demo.php?f=wikipedia.ico"/></a>
<h3>Distant file</h3>
<a href="demo/demo.php?f=distant" target="_blank"><img src="<?php echo $distant ?>"  width="100" height="100"/></a>
</body>
</html>