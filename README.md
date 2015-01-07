PBE Base Bundle
==========

The PBE Base Bundle is a webpage helper bundle for the [eZ Publish 5 Platform](http://ez.no).

You may want to visit the [PBEBaseBundle Project Webpage](http://www.bekerle.com/PBEBaseBundle) for more informations.

Implemented features
--------------------

* Two level **top menu** generation from a folder with **folders and links** to be included in the pagelayout.

* `pbe_fetch_content` twig function to **load content** in **twig** templates. This could, for example, be used in the pagelayout to load content from an **object relation list**.

Installation
----------

First you'll need **composer**, if you don't already have it. You can execute this in your eZ Publish root directory.

```shell
curl -sS https://getcomposer.org/installer | php
```

then you can require the bundle

```shell
php composer.phar require pbe/base-bundle
```

now you have to add the bundle to your `ezpublish/EzPublishKernel.php`

```php
use PBE\BaseBundle\PBEBaseBundle;

...

public function registerBundles()
{
   $bundles = array(
       new FrameworkBundle(),
       ...
       new PBEBaseBundle()
   );

   ...
}
```

To override code of the PBEBaseBundle you can also make it parent of your bundle.

```php
public function getParent()
{
    return 'PBEBaseBundle';
}
```

Take a look at [How to Use Bundle Inheritance to Override Parts of a Bundle](http://symfony.com/doc/current/cookbook/bundles/inheritance.html) for more information.


Usage
-----

### top menu generator
Just include this code into your `pagelayout.html.twig` where you want to put your main menu. In this example `86` is the location id of the parent folder.

```twig
{{ render( controller( "PBEBaseBundle:Menu:topMenuFromFolder", { 'parentFolderLocationId': 86 } ) ) }}
```
#### This can look like this

![Screenhot top-menu](screenshot-top-menu.png)

### pbe_fetch_content

You can use this **twig function** in your templates to **load content**. In this example `57` is the content id.

```twig
{% set content = pbe_fetch_content( 57 ) %}
```

It will return a `\eZ\Publish\Core\Repository\Values\Content\Content` object.