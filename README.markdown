# My.Pics

Simple picture manipulation with php GD.

Examples given
```php
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

Hope this help.
