Custom Item Schema
==================

Add a Schema.org-compatible structured data field to the site. The field is powered by CodeMirror
to allow for inline editing of the JSON. By default, it is enabled for all public post types and
taxonomies.

<img width="1210" alt="screen shot 2018-08-10 at 6 24 18 pm" src="https://user-images.githubusercontent.com/346399/43983823-cddaf8da-9cca-11e8-8d81-5b4222676a24.png">

## Requirements

- PHP 7+
- WordPress 4.9+
- [Fieldmanager](https://github.com/alleyinteractive/wordpress-fieldmanager)
- [wp-seo](https://github.com/alleyinteractive/wp-seo)

## Installation

Copy the plugin to the `wp-content/plugins` directory and activate via WordPress Plugins Admin page.

## Hooks

`custom_item_schema_post_types`: Control the post types that the field is included on.

`custom_item_schema_taxonomies`: Control the taxonomies that the field is included on.

## Changelog

### Roadmap
- Add templates for Schema.org field.
- Add support for more formatting tags that aren't dependent on wp-seo.

### Added
- Added initial plugin files to support Schema.org editor for posts and terms.
