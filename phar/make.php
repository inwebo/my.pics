<?php
try {
    $phar = new Phar( '../yappf.phar' );
    $phar->buildFromDirectory('../core', '/\.php$/');
    $stub = <<<ENDSTUB
<?php
require_once('phar://yappf.phar/autoload.php');

__HALT_COMPILER();
ENDSTUB;
    $phar->setStub($stub);
    $phar->stopBuffering();
}
catch(Exception $e) {
    echo $e->getMessage();
}