<?php
namespace LibreMVC\Img\Driver;

use \LibreMVC\Helpers\Files;
use \LibreMVC\Img;

/**
 * Class Jpg
 * @package LibreMVC\Img\Gd
 * @todo : implemebts iio
 */
class Gif extends Driver {

    /**
     * @return bool
     * @see : http://php.net/manual/en/function.imagecreatefromgif.php#59787
     */
    function isAnimated()
    {
        $filecontents = $this->_fileContent;

        $str_loc=0;
        $count=0;
        while ($count < 2) # There is no point in continuing after we find a 2nd frame
        {

            $where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
            if ($where1 === FALSE)
            {
                break;
            }
            else
            {
                $str_loc=$where1+1;
                $where2=strpos($filecontents,"\x00\x2C",$str_loc);
                if ($where2 === FALSE)
                {
                    break;
                }
                else
                {
                    if ($where1+8 == $where2)
                    {
                        $count++;
                    }
                    $str_loc=$where2+1;
                }
            }
        }

        if ($count > 1)
        {
            return(true);

        }
        else
        {
            return(false);
        }
    }

    public function display( $toString = false ) {
        if ( !$toString ) {
            header('Content-Type: image/gif');
        }
        if( $this->isAnimated() ) {
            echo $this->_fileContent;
        }
        else {
            imagegif($this->_resource);
            imagedestroy($this->_resource);
        }
        exit;
    }

    public function save( $path ) {
        $image = @imagegif( $this->_resource, $path );
        if( $image === false ) {
            throw new Img\resourceWriteToFile('Cannot write to file : `' . $path . '`.');
        }
    }
}