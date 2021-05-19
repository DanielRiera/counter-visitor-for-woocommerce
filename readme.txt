=== Counter live visitors for Woocomerce ===
Contributors: taxarpro, DanielRiera
Donate link: https://paypal.me/taxarpro
Tags: counter, woocommerce, visitor, live, visitor counter, counter visitor, realtime, counter, visitors, users counter
Requires at least: 4.3
Tested up to: 5.7.1
Requires PHP: 5.0
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

 
== Description ==

> ## Important NOTE
> If your website uses a cache plugin, you can activate the option 'Your site use cache system?', This option will enable an additional call to show the block of active users :)

It is not a simple visitor counter, this counter is shown on each product with the number of users who are currently viewing that same product

Navigate to Woocommerce -> Visitor Counter in the administration menu for configure

You use Elementor or other page builder?

Try [wcvisitor] shortcode, available from 1.1.2 version

Since version 1.2.0 the ***msgone*** and ***msgmore*** parameters are included (optionals) to customize the message for each shortcode, example:

```[wcvisitor msgOne="Only One" msgMore="Now %n users on this product"]```

== Installation ==

1. Upload the plugin files to the /wp-content/plugins/plugin-name directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. Enjoy!
 
== Frequently Asked Questions ==

= My site use cache =

If your website uses a cache plugin, you can activate the option 'Your site use cache system?', This option will enable an additional call to show the block of active users :)

= Shortcode =

Yes! [wcvisitor]

Params:
msgone (Only one user)
msgmore (More than one user)

= Configuration =

Navigate to Woocommerce -> Visitor Counter in the administration menu, from there you can configure cache, language, etc.

= Live Mode (Disabled for default)=

The plugin can update the number of current visitors every x seconds, take into account the resources of your server, as a minimum and for security the seconds are set to 5, you can modify this from the plugin options.

= FontAwesome =

Added the option for the plugin to add FontAwesome to your website, for example if your theme doesn't.

== Changelog ==

= 1.2.1 =
* Upload Version Test with news WordPress Release

= 1.2.0 =
* New shortcode with **msgone** and **msgmore** parameters are included to  customize the message for each shortcode, see description for more information.

= 1.1.5 =
* Minor Fix

= 1.1.4 =
* Add Live Support
* Add FontAwesome Helper

= 1.1.3 =
* Add Shortcode [wcvisitor] :)
* Add Style class for visitors numbers

= 1.1.0 =
* Minor Fix

= 1.1.0 =
* Fake mode :)

= 1.0.9 =
* Style file added, you can overwrite this style with theme customizer

= 1.0.2 =
* Cache options

= 1.0.1 =
* Minor Fixed

= 1.0.0 =
* Release.

== Localization ==

Español (Spanish), English (English US)

== Upgrade Notice ==
= 1.0.9 =
Style file added, you can overwrite this style with theme customizer :)


