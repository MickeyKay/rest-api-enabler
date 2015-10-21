=== REST API Enabler ===
Contributors:      McGuive7
Donate link:       http://wordpress.org/plugins/rest-api-enabler
Tags:              REST, API, custom, post, type, field, meta, taxonomy, category
Requires at least: 3.5
Tested up to:      4.4
Stable tag:        1.0.1
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Enable the REST API for custom post types and custom fields.

== Description ==

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/rest-api-enabler).**

By default, custom post types and custom fields are not accessible via the WordPress REST API. REST API Enabler allows you to:

1. Choose which custom post types to enable, and specify their REST API endpoint.
2. Choose which custom fields to include in API responses for posts, pages, and custom post types.

The plugin is compatible with v2+ of the WordPress Rest API.

= Usage =

1. Activate the plugin, then go to **Settings &rarr; REST API Enabler** in the admin.
2. Click the **Post Types** tab to enable/disable post types and customize their endpoints.
3. Click the **Post Meta** tab to enable/disable post meta (custom fields).

**NOTE:** by default, the plugin does not display settings for protected post meta (post meta that begins with an underscore and is intended for internal use only). If you wish to include protected post meta in the plugin settings, you can use the `rae_include_protected_meta` filter to do so. The following code can be placed in your theme's `functions.php` file, or in a custom plugin (on `init` priority 10 or earlier):

`
add_filter( 'rae_include_protected_meta', '__return_true' );
`


== Installation ==

= Manual Installation =

1. Upload the entire `/rest-api-enabler` directory to the `/wp-content/plugins/` directory.
2. Activate REST API Enabler through the 'Plugins' menu in WordPress.


== Screenshots ==

1. Enabling post types and customizing their endpoints.


== Changelog ==

= 1.0.1 =
* Fix typo preventing post meta enabling.
* Fix post meta alphabetical sorting.

= 1.0.0 =
* First release

== Upgrade Notice ==

= 1.0.1 =
* Fix typo preventing post meta enabling.
* Fix post meta alphabetical sorting.

= 1.0.0 =
First Release