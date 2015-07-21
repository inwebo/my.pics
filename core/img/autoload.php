<?php
// Base class
include('base/class.base.php');

// Interfaces
include('interfaces/idrivers/class.idrivers.php');
include('interfaces/iloadable/class.iloadable.php');
include('interfaces/ipackable/class.ipackable.php');

// Drivers base
include('drivers/class.drivers.php');
include('drivers/png/class.png.php');
include('drivers/jpg/class.jpg.php');
include('drivers/gif/class.gif.php');

// Abstracts
include('abstracts/aimg/class.aimg.php');
include('abstracts/aimgbin/class.aimgbin.php');

// Drivers extended
include('drivers/bmp/infoheader/class.infoheader.php');
include('drivers/ico/image/class.image.php');
include('drivers/bmp/fileheader/class.fileheader.php');
include('drivers/bmp/class.bmp.php');
include('drivers/ico/imagemap/class.imagemap.php');
include('drivers/ico/infoheader/class.infoheader.php');
include('drivers/ico/class.ico.php');

// Img extended
include('base/actionable/class.imgactionable.php');
include('base/actionable/editable/class.editable.php');

// Wrapper
include('class.img.php');

// Helpers
include('edit/class.edit.php');
include('filters/class.filters.php');