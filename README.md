Custom Item Schema
==================

Add a Schema.org-compatible structured data field to the site. The field is powered by CodeMirror
to allow for inline editing of the JSON. By default, it is enabled for all public post types and
taxonomies.

## Installation

Copy the plugin to the `wp-content/plugins` directory and activate via Plugins Admin page.

## Hooks

`custom_item_schema_post_types`: Control the post types that the field is included on.

`custom_item_schema_taxonomies`: Control the taxonomies that the field is included on.

## Changelog

### Roadmap
- Add templates for Schema.org field.

### Added
- Added initial plugin files to support Schema.org editor for posts and terms.
