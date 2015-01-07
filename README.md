PBE Base Bundle
==========

The PBE Base Bundle is a webpage helper bundle for the [eZ Publish Platform](http://ez.no).

Implemented features
--------------------

* Two level **top menu** generation from a folder with folders and links to be included in the pagelayout.

* `pbe_fetch_content` twig function to **load content** in **twig** templates. This could, for example, be used in the pagelayout to load content from an object relation list.

Installation
----------

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

It will return a `\eZ\Publish\Core\Repository\Values\Content\Content`.