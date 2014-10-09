<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 09/10/14
 * Time: 16:07
 */

namespace LibreMVC\Img;


class ImgEditable extends ImgActionable{

    #region Edit

    public function resize( $width = null, $height = null ) {
        $this->_actions[] = array(__FUNCTION__, func_get_args());
        $this->getDriver()->setResource( Edit::resize( $this, $width, $height ) );
        $this->_width       = (!is_null($width)) ? $width : $this->_width;
        $this->_height      = (!is_null($height)) ? $height : $this->_height;
    }

    public function pattern( $path ) {
        try {
            $this->_actions[] = array(__FUNCTION__, func_get_args());
            $this->getDriver()->setResource( Edit::pattern( $this, $path ) );
        }
        catch(\Exception $e) {
            var_dump($e);
        }
    }

    public function mask($path) {
        try {
            $this->_actions[] = array(__FUNCTION__, func_get_args());
            $this->getDriver()->setResource( Edit::mask( $this, $path ) );
        }
        catch(\Exception $e) {
            var_dump($e);
        }
    }

    public function getPalette() {
        return Edit::getPalette($this);
    }

    #enregion
} 