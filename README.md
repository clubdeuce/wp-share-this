# WP Share-This

This library allows for easy implementation of sharing links using the [ShareThis](https://sharethis.com). _*Please note,
this library is in no way affiliated with ShareThis or its subsidiaries.*_

## Installation ##

The easiest method for including this library is via [Composer](https://getcomposer.org). Simply run the follow command
in your project root (_assuming that is where your `composer.json` is located_):

`composer require clubdeuce/wp-share-this`

This library will be autoloaded, assuming you include `vendor/autoload.php` in your project.

You can also download this library and include it manually.

## Usage ##

+ Register service(s): `WP_Share_This::register_service( 'twitter' )`. The full list of supported services can be found [here](http://www.sharethis.com/support/customization/how-to-set-custom-buttons/).
+ Initialize the library: `WP_Share_This::initialize()`.
+ Render the links: `WP_Share_This::the_sharing_links()`.

## Filters ##

This library exposes a number of filters:


| Filter | Description | Since |
| ------ | ----------- | ----- |
| `wpst_link_classes_$service`|This filter allows you to specify classes for each service link, _e.g. `add_filter( 'wpst_classes_twitter', 'my_function' )`| 0.0.1 |
|`wpst_og_url`|Allows you to filter the URL exposed via [OpenGraph](https://ogp.me)|0.0.1|
|`wpst_og_title`|Allows you to filter the title exposed via [OpenGraph](https://ogp.me)|0.0.1|
|`wpst_og_description`|Allows you to filter the description exposed via [OpenGraph](https://ogp.me)|0.0.1|
|`wpst_og_image`|Allows you to filter the image exposed via [OpenGraph](https://ogp.me)|0.0.1|