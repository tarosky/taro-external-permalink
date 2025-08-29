# Taro External Permalink


Tags: notification, news, posts, permalink, url  
Contributors: tarosky, Takahashi_Fumiki, tswallie  
Tested up to: 6.8  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

A WordPress plugin that allows selected post types to redirect to external URLs.

## Description

This plugin adds an "External Link" section to the post editor.

If you're publishing posts that are primarily used to link to external news articles or resources, the plugin overrides the post’s permalink and redirects users to the specified external URL when they click the post.

The "External Link" section has two options:

1. External link (This URL will replace the return value of `the_permalink`)
2. Open in new window (checkbox)

By setting an external link, the link in your widget, post archive and so on, will refer to the new URL you saved.

### Settings

This plugin adds a new section to Writing Settings with 3 different options.

#### 1. Post Types

Lets you select which post types should support external links.

#### 2. Attributes

When set to Automatic, the target and rel attributes will be automatically added to anchor elements linking to the new URL, using jQuery.

When set to Manual, developers must manually add anchor elements. You can either use `tsep_anchor_attributes()` to generate the href, rel and target attributes, or use `the_permalink()` to populate the href attribute and `tsep_target_attributes()` to add the target and rel attributes separately.

**Easy method:**
```php
<a <?php echo tsep_anchor_attributes(123); ?> class="some-class">Click here!</a>
```

**With separate attributes:**
```php
<a href="<?php echo the_permalink(123); ?>" <?php echo tsep_anchor_attributes(123); ?> class="some-class">Click here!</a>
```

#### 3. Single Page Content

This option allows you to manually write an anchor element that will be added to the post's content. Use %link% for the external link, and %rel% for the target and rel attributes.

```php
<a href="%link%" %rel%>Click here!</a>
```

This will produce the following output:

```php
<a href="https://example.com" rel="noopener noreferrer" target="_blank">Click here!</a>
```

## Installation

### From Plugin Repository

Click install and activate it.

### From GitHub

See [releases](https://github.com/tarosky/taro-external-permalink/releases).

## FAQ

### Where can I get supported?

Please create new ticket on the support forum.

### How can I contribute?

Create a new [issue](https://github.com/tarosky/taro-external-permalink/issues) or send [pull requests](https://github.com/tarosky/taro-external-permalink/pulls).

## Changelog

### 1.0.8

* Drop support for PHP 5.6.
* Allow PDF and other format on Media Selector.
* Display notice if no post type is selected.

### 1.0.7

* Support custom post type.
* Fix bug of the content label in single page.

### 1.0.0

* First release.
