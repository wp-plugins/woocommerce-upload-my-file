=== WooCommerce Upload My File ===
Contributors: bpluijms
Plugin URI: http://wordpress.geev.nl/product/woocommerce-upload-my-file/
Author URI: http://www.geev.nl
Tags: woocommerce, file upload
Requires at least: 3.8
Tested up to: 3.9.1
Stable tag: 0.3.1
License: GPLv2

This plugin adds the possibility to upload a file after an order is completed and attached it to that order.
== Description ==
***Let customers upload a file after completing an order and attach this to an order.***

The _WooCommerce Upload My File plugin_ allows customers to upload a file an attach it to an order. Once an Order is processed a button will appear in the order overview page and a file upload screen will appear on order-detail page.

The plugin adds a new side panel on the order detail page to allow shop managers to download the attached files. 

For more information, check out [our website](http://wordpress.geev.nl/product/woocommerce-upload-my-file/).

**Free version**

This free version may be used as is. If you want more options and support you can buy WooCommerce Upload My File Pro for only &euro; 29,00.

**PRO version**

With our PRO version it's possible to upload more than one file per product, whitelist or blacklist file types, let users delete files, get a preview of uploaded files and images (thumbs), specifiy file upload titles (example: Page 1, Frontside, Backside, etc). 
For a full list of features, please visit our website.

**Features**

* Upload customize your products
* Allow one upload per product
* Localisation: English, German, Spanish (not completed) & Dutch

Credit where credit is due: This plugin here is inspired and based on the work of patrickgarman, garmantech and the awesome "WooCommerce Pay to Upload" plugin!

== Installation ==

1. Install WooCommerce Upload My File either via the Wordpress.org plugin directory or by uploading the files to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to plugin settings under WooCommerce --> Upload My File
4. Select allowed file type, max upload size, required order statusses (VERY IMPORTANT!)
5. Go to a product and select the "Enable" checkbox under "Upload Files" (normally below your product images)

== Upgrade Notice ==
Please backup first. | We no longer use the template overrides for emails inside woocommerce/upload-my-file/templates/emails. If you're using template overrides for emails inside your theme, you might have to update them.

== Usage ==

Go to WooCommerce > Upload My File, configure and start uploading. Simple!

== Screenshots ==

1. WooCommerce Upload My File settings
2. WooCommerce customized account template file (attached)
3. WooCommerce order detail page with upload fields
4. WooCommerce order detail page with uploaded files

== Changelog ==
***WooCommerce Upload My File***
= 2014.03.12 - version 0.3.1 =
* Changed & Added: A couple of language strings, so please update your translations.
* Fix: E-mail link will work from now - Please update your template overrides! We no longer use the template overrides in woocommerce-upload-my-file/templates/emails

= 2014.01.12 - version 0.3 =
* Ready for WooCommerce 2.1 (Tested on WooCommerce 2.1 Beta 3)
* Several small tweaks

= 2013.12.12 - version 0.2.6 =
* Changed: Minor layout modifications for WordPress 3.8

= 2013.12.06 - version 0.2.5 =
* Fixed: small bug fix on order number display

= 2013.07.29 - version 0.2.4 =
* Fixed: Several small bugfixes - thanks to kaykay84

= 2013.07.27 - version 0.2.3 =
* Added: uninstall.php which removes all plugin data
* Fixed: Several small bugfixes - thanks to Christian Habenicht

= 2013.07.16 - version 0.2.2 =
* Fixed: Several small bugfixes
* Added: German translation, thanks to Leo Filipczak.

= 2013.05.28 - version 0.2.1 =
* Changed: Corrected language string in file templates/myaccount/my-orders.php (Thanks to: Adam)

= 2013.05.28 - version 0.2 =
* Added default CSS

= 2013.03.29 - version 0.1 =
* First release

== Frequently Asked Questions ==
= Can I contact you by email if I have questions about WooCommerce Upload My File FREE? =
No, only customers who have purchased our PRO plugin may contact us by email. The Wordpress.org forums are available for FREE users and we always answer as quick as possible.

= Why are uploads not shown? =
Uploads are only shown if:
1. The order status matches the required configurated order status in plugin settings. Go to WooCommerce --> Upload My File and select a required order status.
2. Uploads are enabled for a purchased product. For this go to Products, edit a product and hit the "Enable" checkbox in the "Upload Files" box (normally in the right column).

= Is it possible to style the custom "My account template"? =
Yes it is, you can place an override file in wp-content/themes/your-theme/woocommerce/myaccount/my-account.php, or just style it using your own stylesheet (CSS).

= Where can I find the plugin template directory to find the my account template override? =
Normally it is in wp-content/plugins/woocommerce-upload-my-file/templates/

= Where can I find more information about this plugin? =
You can find more information on [our website](http://wordpress.geev.nl/product/woocommerce-upload-my-file/).

= Is it possible to upload multiple files =
WooCommerce Upload My File FREE has only one upload box for one file. If you need to upload multiple files check out [our website](http://wordpress.geev.nl/product/woocommerce-upload-my-file/) for Upload My File PRO.

= What is the difference between the Free and Pro versions of this plugin? = 
You may use the free version as it is. When you buy WooCommerce Upload My File PRO you get a lot more options: allow more than one upload per product, white or blacklist file-types, let users delete files, default enable file upload for products and more.
For a full list of features, please check out [our website](http://wordpress.geev.nl/product/woocommerce-upload-my-file/).

= Why is there a PRO version? = 
We want to give everyone the opportunity to use and try our plugins, but if you want to get more options and access to our support section you can buy our PRO version. WooCommerce Upload My File Pro costs only **EUR 29,00**.