<?php

namespace LibreMVC\Img;

use LibreMVC\Img;

class Edit {

    static public function resize( $img, $new_width = null, $new_height = null ) {
        $src =  $img->getDriver()->getResource();
        // Resize fixed width and height
        if( isset( $new_width ) && isset( $new_height ) ) {
            $width  = $new_width;
            $height = $new_height;
        }
        // Resize by new width
        elseif ( is_null( $new_height ) && isset( $new_width ) ) {
            // Ratio by width
            if ( $new_width > $img->getWidth() ) {
                $ratio  = $new_width / $img->getWidth();
                $width  = $new_width;
                $height = round($img->getHeight() * $ratio);
            } else {
                $ratio  = $img->getWidth() / $new_width;
                $width  = $new_width;
                $height = round($img->getHeight() / $ratio);
            }
        }
        // Resize by new height
        elseif ( isset( $new_height ) && is_null( $new_width ) ) {
            // Ratio by height
            if ($new_height > $img->getHeight()) {
                $ratio = $new_height / $img->getHeight();
                $width = round($img->getWidth() * $ratio);
                $height = $new_height;
            } else {
                $ratio = $img->getHeight() / $new_height;
                $width = round($img->getWidth() / $ratio);
                $height = $new_height;
            }
        }

        $image_mini = imagecreatetruecolor($width, $height);

        imagealphablending ( $image_mini, false );

        imagesavealpha($image_mini, true);
        imagecopyresized( $image_mini, $src, 0, 0, 0, 0, $width , $height, $img->getWidth(), $img->getHeight() );

        imagealphablending ( $image_mini, true );
        return $image_mini;
    }

    static public function mask( Img $img, $path ){

        try {
            $img->convertTo('png');
            $layer = Img::load($path);
            $imgResource = $img->getDriver()->getResource();

            imagealphablending($imgResource, false);


            $layerResource = $layer->getDriver()->getResource();

            if( $layer->getWidth() !== $img->getWidth() || $layer->getHeight() !== $img->getHeight()) {
                $layer->resize($img->getWidth(),$img->getHeight());
            }

            for ($i = 0; $i < $img->getWidth(); ++$i) {
                for ($j = 0; $j < $img->getHeight(); ++$j) {

                    $pxl_alpha = imagecolorsforindex(
                        $layerResource,
                        imagecolorat(
                            $layerResource,
                            $i, $j
                        )
                    );

                    $pxl_color = imagecolorsforindex(
                        $imgResource,
                        imagecolorat(
                            $imgResource,
                            $i, $j
                        )
                    );

                    $color = imagecolorallocatealpha(
                        $imgResource,
                        $pxl_color['red'], $pxl_color['green'], $pxl_color['blue'], $pxl_alpha['alpha']
                    );

                    //var_dump($pxl_alpha['alpha'] );
                    //echo 'Origin alpha : ' . $pxl_color['alpha'] . ' <br>';
                    //echo 'New alpha : ' . $pxl_alpha['alpha'] . ' <br>';
                    imagesetpixel($imgResource, $i, $j, $color);
                    //echo 'Current alpha : ' .imagecolorsforindex($imgResource, imagecolorat($imgResource, $i, $j))['alpha'] ;
                    //echo '<br>--------<br>';
                    //var_dump(imagecolorsforindex($imgResource, imagecolorat($imgResource, $i, $j))['alpha']);
                }
            }
            //imageAlphaBlending($imgResource, true);

            return $imgResource;
        }
        catch(\Exception $e) {
            var_dump($e);
        }


    }

    static public function pattern(Img $img, $path){
        try {
            //imagealphablending ( $img->getDriver()->getResource(), false );

            $layer = Img::load($path);

            $cols = floor($img->getWidth() / $layer->getWidth());
            $rows = floor($img->getHeight() / $layer->getHeight());

            $startX = 0;
            $startY = 0;

            // For each rows.
            for ($i = 0; $i <= $rows; $i++) {
                for ($j = 0; $j <= $cols; $j++) {
                    imagecopy(
                        $img->getDriver()->getResource(),
                        $layer->getDriver()->getResource(),
                        $startX, $startY,
                        0, 0,
                        $layer->getWidth(), $layer->getHeight()
                    );
                    $startX += $layer->getWidth();
                }
                $startX = 0;
                $startY += $layer->getHeight();
            }

            return $img->getDriver()->getResource();
        }
        catch(\Exception $e) {
            var_dump($e);
        }

    }

    public function crop(){}

    public function merge(){}

    static public function getPalette( Img $img, $fastProcess = false ) {
        $buffer = $img;
        if($fastProcess) {
            $buffer = $buffer->resize(150);
        }

        $hexarray = array();

        for ($y=0; $y < $buffer->getHeight(); $y++)
        {
            for ($x=0; $x < $buffer->getWidth(); $x++)
            {
                $index = imagecolorat( $buffer->getDriver(),$x,$y);
                $Colors = imagecolorsforindex($buffer->getDriver(),$index);
                $Colors['red']=intval((($Colors['red'])+15)/32)*32; //ROUND THE COLORS, TO REDUCE THE NUMBER OF COLORS, SO THE WON'T BE ANY NEARLY DUPLICATE COLORS!
                $Colors['green']=intval((($Colors['green'])+15)/32)*32;
                $Colors['blue']=intval((($Colors['blue'])+15)/32)*32;
                if ($Colors['red']>=256)
                    $Colors['red']=240;
                if ($Colors['green']>=256)
                    $Colors['green']=240;
                if ($Colors['blue']>=256)
                    $Colors['blue']=240;
                $hexarray[]=substr("0".dechex($Colors['red']),-2).substr("0".dechex($Colors['green']),-2).substr("0".dechex($Colors['blue']),-2);
            }
        }
        $hexarray=array_count_values($hexarray);
        natsort($hexarray);
        $hexarray=array_reverse($hexarray,true);
        return $hexarray;

    }

}