<?php

namespace LibreMVC;

include 'autoload.php';


$projectName = 'My.Pictures';
$projectVersion = '01-01-2013';
$projectKeywords = 'inwebo';
$projectShortDescription = 'Non destructive pictures manipulation';
error_reporting(E_ALL);
?>
<?php ini_set('display_errors', TRUE); ?>

<!doctype html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="<?php echo $projectKeywords; ?>" />
    <meta name="author" lang="fr" content="Inwebo" />
    <meta name="copyright" content="Creative commons" />
    <meta name="date" content="2012" />
    <title><?php echo $projectName; ?></title>
    <meta name="description" content="<?php echo $projectShortDescription; ?>">
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="css/style.css">
</head>
<?php
$img = new Img('assets/picture.jpg');
var_dump($img);
$serialize = serialize($img);
$img = unserialize($serialize);
//$img->save("public/test.png");
var_dump($img);

$l = Img::load('assets/picture.jpg')->extractColorPalette();
var_dump($l);
$i=0;
foreach($l as $key => $value ) {
    //echo '<div style="background-color:#'. trim($key,"'")  .'">&nbsp;</div>';
    ++$i;
    echo '<div style="background-color:#'. $key  .';width:50px;">' . $key . '</div>';
}
?>
<html>
    <body>
        <header>
            <a name="top"></a>
            <h1><?php echo $projectName; ?> <span id="version">version : <span><?php echo $projectVersion; ?></span></span></h1>
        </header>
        <div role="main">
            <?php
            if (isset($_GET["q"]) && $_GET['q'] == 1) {
                echo '<h2>PHAR</h2><p><a href="module.phar">Download me</a></p>';

                try {
                    $phar = new Phar(__DIR__ . '/phar/' . $projectName . '.phar');
                    $phar->buildFromDirectory('core/', '/\.php$/');
                    $phar->stopBuffering();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            ?>
            <h2>Pictures</h2>
            <img src="picture.php">
            <h2>Creation archive PHAR <a href="#top">TOP</a></h2>
            <p>
                <a href="http://cweiske.de/tagebuch/php-phar-files.htm" target="_blank" title="Why PHAR archive ?">Why PHAR archives ?</a>,
                <a href="index.php?q=1" target="_self">Make <?php echo $projectName . '.phar' ?> phar archive</a>, php.ini doit être configuré correctement !
                <br><br><br>
                <code>
                    phar.readonly= 0;
                </code>
            </p>
        </div>
        <footer>
            <p>
                <a title="Julien Hannotin" href="http://julien.hannotin.is.free.fr" target="_blank" title="Résumé">Jool</a> | <a href="http://creativecommons.org/licenses/by-nc-sa/2.0/fr/" title="Creative Commons 2"  target="_blank">creative commons 2</a> | <a title="Git repository" target="_blank" href="https://github.com/inwebo/">Github Repository</a> | <a href="#top">top</a>
            </p>
        </footer>
    </body>
</html>