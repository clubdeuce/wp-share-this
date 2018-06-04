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

## Methods ##

| Method | Description | Since |
| ------ | ----------- | ----- |
|register_id( _string_ $share_this_id )|Specify the ShareThis user id for tracking|0.0.1|
|register_service( _string_ $service_name, _array_ $params )|Specify a service to be included. You can pass parameters specific to this service _(see below for a list of parameters)_.|0.0.1|
|the_sharing_links( _null/array_ $params )|Output the sharing links. You can pass parameters specific to this block of links _(see below for a list of parameters)_.|0.0.1|
|use_og( _bool_ $use )|Specify whether to include the [OpenGraph](https://ogp.me) information in the `<head>` of the document. Default: _true_.|0.0.2|


## Parameters ##

The following parameters can be specified for the sharing links on a per-service basis _*(not all items apply to every service)*_:

| Name | Description |
| ---- | ----------- |
|url|Specifies URL (can be shortened URL) that you would like shared. Default: current global `$post` permalink.|
|short_url|Specifies a shortened URL that can be used in conjunction with the data-url. Default: _null_|
|title|Specifies title that you would like shared. Defaults to current global `$post` title.|
|image|Specifies link to image you would like displayed in the shared content. Default: current global `$post` featured image if present.|
|description|Specifies summary text/description you wish to share. Default: current global `$post` excerpt.|
|username|Specifies your Twitter username.|
|message|Specifies the body copy of e-mail shares. Default: current global `$post` excerpt.|
|share_count|Specify whether to include the share count for this individual link item. Default: true.|

### Examples: ###

To register services with unique parameters:

```
WP_Share_This::register_service( 'twitter', array(
    'username'    => 'myTwitterHandle',
    'share_count' => false,
) );

WP_Share_This::register_service( 'facebook', array(
    'image' => 'https://example.com/image.png',
);

WP_Share_This::register_service( 'email', array(
    'message' => __( 'Look at this really interesting article I found!', 'txt_domain' ),
) );  
```

To render the links with overrides to the defaults:

```
WP_Share_This::the_sharing_links( array(
    'share_count' => false,
) );
```

To render sharing links for a post other than the current global `$post` item:

```
WP_Share_This::the_sharing_links( array(
    'post' => get_post( $post_id ),
) );
```

## Filters ##

This library exposes a number of filters:


| Filter | Description | Parameters| Since |
| ------ | ----------- | --------- | ----- |
|`wpst_link_classes_$service`|This filter allows you to specify classes for each service link, _e.g. `add_filter( 'wpst_classes_twitter', 'my_function' )`|_array_ classnames|0.0.1 |
|`wpst_og_url`|Allows you to filter the URL exposed via [OpenGraph](https://ogp.me)|_string_ URL|0.0.1|
|`wpst_og_title`|Allows you to filter the title exposed via [OpenGraph](https://ogp.me)|_string_ title|0.0.1|
|`wpst_og_description`|Allows you to filter the description exposed via [OpenGraph](https://ogp.me)|_string_ description|0.0.1|
|`wpst_og_image`|Allows you to filter the image exposed via [OpenGraph](https://ogp.me)|_string_ image URL|0.0.1|
|`wpst_link_text`|Allows you to filter the text displayed for the sharing link _*(defaults to the service name)*_|_string_ text|0.0.1|
