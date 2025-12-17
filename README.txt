=== Tools for Developers ===
Contributors: racmanuel
Donate link: https://racmanuel.dev/
Tags: developer tools, sql, generator, rest api, woocommerce, database, acf, scf
Requires at least: 6.9
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A collection of practical generators for WordPress developers: SQL migration, DB prefix change, plugin headers, REST routes, ACF/SCF fields, and more.

== Description ==

**Tools for Developers** is a productivity plugin that provides a set of interactive generators to help WordPress developers create boilerplate code and SQL faster and more consistently.

The plugin focuses on **developer-friendly UI**, **readable output** (CodeMirror), and **one-click copy to clipboard**.

### Included generators

* **WordPress Migration SQL Generator**
  Generate SQL queries to change site URLs when migrating a WordPress site to a new domain.

  Shortcode:
  `[tools-for-devs-migration-sql]`

* **Database Prefix Generator**
  Generate SQL queries to rename WordPress tables and update related keys. Includes optional multisite support and optional WooCommerce tables.

  Shortcode:
  `[tools-for-devs-db-prefix]`

* **Plugin Header Generator**
  Generate a WordPress plugin header block with common fields and optional requirements.

  Shortcode:
  `[tools-for-devs-plugin-header-generator]`

* **WooCommerce Delete Products SQL Generator**
  Generate SQL queries to delete WooCommerce products (use with caution and always back up first).

  Shortcode:
  `[tools-for-devs-delete-products-sql]`

* **ACF / SCF Field Generator**
  Generate scaffolding/boilerplate for custom fields (ACF / Secure Custom Fields). Intended for developers building custom field types.

  Shortcode:
  `[tools-for-devs-acf-field-generator]`

* **REST Route Generator**
  Generate `register_rest_route()` boilerplate, with support for:
  - permissions
  - dynamic args
  - path params `(?P<id>\d+)`
  - optional Controller Class mode
  - optional CRUD generation
  - curl examples

  Shortcode:
  `[tools-for-devs-rest-route-generator]`

* **Database CRUD Generator**
  Generate `$wpdb` + `dbDelta()` boilerplate for custom tables with upgrades and basic CRUD helpers.

  Shortcode:
  `[tools-for-devs-db-crud-generator]`

### Notes

* The generators **do not run SQL** automatically. They only output code/queries.
* Always create a database backup before running generated SQL.
* Some SQL operations can break serialized data if used incorrectly.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install it via the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Add any generator shortcode to a page or post.

== Frequently Asked Questions ==

= Does this plugin modify my database automatically? =

No. The plugin only generates SQL/code. You decide when and where to execute it.

= Is this plugin safe to use on production? =

The plugin itself is safe because it only generates output. However, executing SQL on production can be risky. Always back up first and test changes on staging.

= Does it support translations? =

Yes. All UI strings are prepared for translation.

= Does it work with Secure Custom Fields (SCF)? =

Yes. The field generator is designed to support ACF and SCF workflows.

== Screenshots ==

1. Migration SQL Generator output with CodeMirror and copy-to-clipboard
2. Database Prefix Generator (multisite + optional WooCommerce)
3. REST Route Generator (Controller Class + CRUD options)
4. Database CRUD Generator (dbDelta + upgrades)
5. Plugin Header Generator
6. ACF / SCF Field Generator

== Changelog ==

= 1.0.0 =
* Initial release with 7 generators:
  - Migration SQL
  - DB Prefix
  - Plugin Header Generator
  - WooCommerce Delete Products SQL
  - ACF / SCF Field Generator
  - REST Route Generator
  - DB CRUD Generator

== Upgrade Notice ==

= 1.0.0 =
Initial public release.
