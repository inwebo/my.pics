# YAPPF PHP5.3+, GD2+

Yet another picture php framework.

## Abstract more formats than GD

Add support to BMP, ICO, aGIF to GD. So you can manipulate them with GD base functions. Oh wait, it's OOP ! Extends it to add your own driver.

## Menu

 - Support : BMP, GIF, GIF (animated \o/), ICO, PNG, JPG
 - Non destructive
 - Support local & distant files
 - Abstract GD utilisation, don't care anymore about mime-type before display.
 - Full PHP5+ support, traits, interfaces, abstract, namespaces
 - 1 Phar archive to rule them all, easy install easy update.
 - Chainable functions ( jquery flavoured )
 - Extendable, well maybe someone to write a driver for .abr files ? extrends Drivers class, add a new filter ? extends
   Filters class.
 - Safe saving with locks before write to the file, no more corrupted datas.
 - Serializable php GD resources ! Save them to DB
 - Easy format convert
 - Save actions (resize, merge, filter, convert, etc) for an automated process
 - Merge, Crop, Flip, Pattern, GetPalette, Mask, and many Filters available
 - Feel free to use it

## Examples given
```php
    // Our magic wand
    include('yappf.phar');

    // Load an existing local picture
    Img::load('assets/picture.jpg');

    // Create a new 160x100 px image
    Img::create(160,100);

    // Load a local picture then resize it to 40x40px
    Img::load('assets/picture.jpg')->resize(40,40);

    // Load a remote picture, resize it to 500x20 px, save the new picture as jpg to a new file and display it
    Img::load('http://static.php.net/www.php.net/images/php.gif')->resize(500,20)->saveAs('assets/new.jpg','jpg')->display();

    // Load a local image crop it, resize it and apply pattern the save actions for an automated process
    Img::load('assets/pictures.jpg')->crop(500,20)->resize(100)->pattern('assets/pattern.png')->saveActions('actions/crp.txt');

    // Load an action on a local picture then display result
    Img::load('assets/pictures.jpg')->runActions('actions/crp.txt')->display();
```

## Documentation
Sorry about that, you should look to the source code. And use a good IDE !