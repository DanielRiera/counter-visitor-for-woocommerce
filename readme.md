=== Counter live visitors for WooCommerce ===
Contributors: DanielRiera
Donate Link: https://www.paypal.com/donate/?hosted_button_id=EZ67DG78KMXWQ
Tags: woocommerce, live visitors, visitor counter, social proof, product views
Requires at least: 6.8
Tested up to: 7.1.3
Requires PHP: 8.0
Requires Plugins: woocommerce
WC requires at least: 3.0
WC tested up to: 10.6.1
Stable tag: 1.4.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display live visitor counts on WooCommerce product pages to add social proof and help customers make buying decisions.
 
== Description ==

Counter Live Visitors for WooCommerce displays how many customers are currently viewing each product. Use this live visitor counter to add social proof to your WooCommerce product pages and create confidence around popular products.

The counter tracks views per product, so customers see the number of visitors looking at the same item. Configure its placement, text, update behavior, and display settings from **WooCommerce → Visitor Counter**.

### Features

* Show the number of current visitors on individual WooCommerce product pages.
* Add social proof near the add-to-cart button, after the short description, after the price, or at another supported WooCommerce position.
* Use Live Mode to refresh the visitor count automatically.
* Enable cache compatibility for sites using a page cache.
* Hide the counter when only one visitor is viewing a product.
* Use Fake Mode with a configurable range when needed.
* Insert the counter anywhere with the `[wcvisitor]` shortcode.
* Customize the messages shown for one visitor and multiple visitors.
* View visitor statistics and choose how long detailed statistics are retained.
* Available in English and Spanish.

### Cache compatibility

If your WordPress site uses a cache plugin, activate **Your site uses a cache system?** in the plugin settings and add `wcvisitor` to your cache plugin exclusions. This lets the visitor block load separately and show the current count.

### Shortcode for Elementor and page builders

Use the `[wcvisitor]` shortcode in Elementor, Gutenberg, or any WordPress page builder that supports shortcodes. You can customize the messages for one or more visitors:

```
[wcvisitor msgOne="Only One" msgMore="Now %n users on this product"]
```

Use `%n` in `msgMore` to display the current number of visitors.

== Installation ==

1. Upload the plugin files to the /wp-content/plugins/plugin-name directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **WooCommerce → Visitor Counter** to configure the counter.
4. Visit a product page to see the live visitor counter.
 
== Frequently Asked Questions ==

= Does this plugin work with cache plugins? =

Yes. Activate **Your site uses a cache system?** in the plugin settings and add `wcvisitor` to the cache exclusions. The plugin will load the visitor counter separately so it can show the current number of visitors.

= Can I use the visitor counter with Elementor or another page builder? =

Yes. Add the `[wcvisitor]` shortcode wherever you want the live visitor count to appear.

You can customize its messages with `msgOne` for one visitor and `msgMore` for multiple visitors. Use `%n` in `msgMore` for the current count.

= Where can I configure the WooCommerce visitor counter? =

Go to **WooCommerce → Visitor Counter**. There you can configure the position, display order, cache compatibility, Live Mode, Fake Mode, messages, and other settings.

= What is Live Mode? =

Live Mode refreshes the number of current visitors automatically. It is disabled by default. The minimum refresh interval is 5 seconds; choose an interval that suits your server resources.

= Can I hide the counter when only one customer is viewing a product? =

Yes. Enable the option to hide the counter when there is only one visitor.

= Does the plugin include an icon library? =

The plugin can load Font Awesome if your theme does not already provide it.

= What does the display order setting do? =

The display order controls where the visitor counter appears when multiple elements share the same WooCommerce position. A higher number displays the counter later.

== Changelog ==

= 1.4.2 =
* Update compatibility information.
* Add German, French, Italian, Brazilian Portuguese, Dutch, Polish, Russian, and Japanese translations.
* New brand style

= 1.4.1 =
* Fix visitor tracking with cache enabled and refresh activity in Live Mode without inflating page views.
* Fix Live Mode recovery after connection errors and restore counters hidden when only one visitor is present.
* Preserve custom shortcode messages and update counters displayed after the price.
* Set fake visitor cookies before page output, keep values for 25 minutes and validate the configured range.
* Add configurable statistics retention (90 days by default, minimum 30) and scheduled cleanup in batches. Older visits are aggregated into daily totals before deletion; detailed statistics cover retained visits only.
* Declare WooCommerce dependency and PHP 8.0 requirement, and prevent errors when WooCommerce is inactive.
* Translate default visitor messages and update Spanish translations.

= 1.4.0 =
* Replace legacy file-based visitor storage with database storage.
* Add visitor statistics screen with filters and pagination.
* Add legacy storage notice with safe cleanup action for old visitor files.
* Improve options page design and update translations.
* Minor fixes

= 1.3.7 =
* Fix Vulnerability detected

= 1.3.6 =
* New option for add counter after price XXX $ | 1 user are viewing this product, for this deactivate counter on other places (Deactivate option on positions).
* Add after Short description position
* Add deactivate position
* Add weight option for block (Read Description)
* Add option for hide if only one visitor
* Fix Warnings
* Minor Fixes

= 1.3.5 =
* High-Performance Order Storage (HPOS) Compatibility
* WooCommerce 8.0.1 Compatibility
* WordPress 6.3.0 Compatibility

= 1.3.0 =
* Delete old files from plugin options
* Delete old files with WP Cron
* Update version tested
* Set WooCommerce version tested
* Minor Fixes

= 1.2.2 =
* Thanks +1000 install message
* Up to 5.7.2 version tested
* Change readme to md extension

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

