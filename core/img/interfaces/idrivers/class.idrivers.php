<?php
/**
 * Created by PhpStorm.
 * User: inwebo
 * Date: 12/10/14
 * Time: 23:27
 */

namespace LibreMVC\Img\Interfaces;


interface iDrivers {

    const NS = '\\LibreMVC\\Img\\Drivers\\';

    public function create();
    public function display();
    public function convertTo($type);

} 