=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://https://github.com/davesuy
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

# Safe Media Delete Plugin Documentation

1. I've added a feature to the Term Add and Term Edit pages where they may upload an image in PNG or JPG format. I'm using the CMB2 Library for this. Please see https://prnt.sc/Bt5I02h5fSvO and https://prnt.sc/w4B5i-WNa9VB for screenshot references.

2.If a user attempts to delete an image from the media library, they are informed that they cannot do so since it is being utilized in the Featured Image, Post Body, or Term edit page. Please see screenshot link for reference: https://prnt.sc/zGjuUj2t7Ad8

3. In the media library, I created a column called attached object, which displays the object to which the attachment is attached. See screenshot https://prnt.sc/VIaZvzsVxqcq


## REST API

4. I have created the Rest API.

a.) Viewing all the attachment ID with their attached objects:
http://localhost/wordpress_testing/wp-json/assignment/v1/attached-media

b.) Viewing single attachment ID with their attached objects:

example id:
http://localhost/wordpress_testing/wp-json/assignment/v1/attached-media/15040

c.) Deleting single attachment ID with their attached objects:

example id:
http://localhost/wordpress_testing/wp-json/assignment/v1/attached-media-delete/15040

If the attachment ID has an attached objects, it will return 404; otherwise, it will return 200 true.

For this DELETE method, I utilized Postman App to make an API call.


## PHP Unit tests

1. I have setup a PHPunit testing using WP CLI and from the The WordPress core PHPUnit library Repository and tested the critical functions for this plugin

For quick demonstration please see this loom recording here.
https://www.loom.com/share/d14864fab5684ef6a810cbbbcacdd488