# drush site-schema

[![Build Status](https://travis-ci.com/eiriksm/drush-site-schema.svg?branch=master)](https://travis-ci.com/eiriksm/drush-site-schema)
[![Packagist](https://img.shields.io/packagist/v/eiriksm/site-schema.svg)](https://packagist.org/packages/eiriksm/site-schema)

Get the complete picture of a Drupal site schema (updates and post updates).

This can be useful to always check in the database update schema for your site, so you have a conscious relationship to what effect an update will have on your site.

As an example, if you are getting automated updates to contributed modules, maybe you want to not auto-merge and deploy the ones that contain database updates.

This command outputs a complete picture of your site, meaning the database schema version of all modules, and the post_update hooks for all modules. This way you can commit a complete picture of your site, and fail your CI if there is a difference in the file.

## Installation

Install with composer.

```
composer require eiriksm/site-schema
```

## Usage
```
$ drush site-schema --help
Get the site schema for the current site.

Examples:
  site:schema Get the complete schema in text

Options:
  --format[=FORMAT] The format to output. [default: "table"]
  --fields=FIELDS   Limit output to only the listed elements. Name top-level elements by key, e.g. "--fields=name,date", or use dot notation to select a nested element, e.g.
                    "--fields=a.b.c as example".
  --field=FIELD     Select just one field, and force format to 'string'.

Aliases: site-schema
```

## Examples

Output the entire schema

```
$ drush site-schema
 ------------- -------------------------------- ---------------------------------------------------------------------------
  Type          Module                           Value
 ------------- -------------------------------- ---------------------------------------------------------------------------
  schema        admin_toolbar                    8001
  schema        admin_toolbar_tools              8000
  ... shortened here for convenience ...
  schema        views_ui                         8000
  post_update                                    block_content_post_update_add_views_reusable_filter
  post_update                                    block_post_update_disable_blocks_with_missing_contexts
  post_update                                    block_post_update_disabled_region_update
  post_update                                    block_post_update_fix_negate_in_conditions
  post_update                                    comment_post_update_add_ip_address_setting
  post_update                                    comment_post_update_enable_comment_admin_view
  ... and so on
```

Output the entire schema in json

```
$ drush site-schema --format=json
[
    {
        "type": "schema",
        "module": "admin_toolbar",
        "value": "8001"
    },
    {
        "type": "schema",
        "module": "admin_toolbar_tools",
        "value": "8000"
    },
    ... And so on.
```

...or in yaml

```
$ drush site-schema --format=yaml
-
  type: schema
  module: admin_toolbar
  value: '8001'
-
  type: schema
  module: admin_toolbar_tools
  value: '8000'
-
# And so on.
```

And then you probably want to output it to a file? Just do that!

```
$ drush site-schema --format=json > site-schema.json
```

And then you can commit that file, and track your site schema with version control.

## Licence

GPL-2.0+
