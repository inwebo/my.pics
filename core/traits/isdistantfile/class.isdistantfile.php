<?php

namespace LibreMVC\Traits ;

trait IsDistantFile {

    public function isDistantFile( $filename ) {
        if( filter_var( $filename, \FILTER_VALIDATE_URL ) ) {
                $opts = array(
                    'http' => array(
                    'method' => 'HEAD'
                )
            );
            $context = stream_context_create( $opts );
            $file = file_get_contents( $filename, false, $context, null, 0 );
            return ( $file !== false ) ? true : false;
        }
        return false;
    }
}