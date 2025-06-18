# Taro External Permalink


Tags: notification, news, posts, permalink, url  
Contributors: tarosky, Takahashi_Fumiki  
Tested up to: 6.8  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

A WordPress plugin to allow some posts to have external permalink.

## Description

This plugin adds an "External Link" section to your editor.

If you have posts that are mainly used to link to news articles on other websites, this plugin will override the post's permalink, redirecting users who click on the post directly to the source.

The "External Link" section has two options:

1. External link (This URL will replace the return value of `the_permalink`)
2. Open in new window (checkbox)

By setting an external link, the link in your widget, post archive and so on, will refer to the new URL you saved.

### Settings

This plugin adds a new section to Writing Settings with 3 different options.

#### Post Types

Lets you select which Post Types are allowed to have an external link.

#### Attributes

When set to Automatic, the target and rel attributes will be automatically added to anchor elements linking to the new URL, using jQuery.

When set to Manual, developers are expected to add anchors manually. You can either use `tsep_anchor_attributes()` to generate the href, rel and target attributes, or use `the_permalink()` to populate the href attribute and `tsep_target_attributes()` to add the target and rel attributes separately.

<pre>
&lt;a &lt;?php echo tsep_anchor_attributes(); ?&gt; class="some-class"&gt;Click here!&lt;/a&gt;
</pre>

#### Single Page Content

This option allows you to manually write an anchor element that will be added to the post's content. Use %link% for the external link, and %rel% for the target and rel attributes.

<pre>
&lt;a href="%link%"%ref%&gt;Click here!&lt;/a&gt;
</pre>

This will produce the following output:

<pre>
&lt;a href="https://example.com" rel="noopener noreferrer" target="_black"&gt;Click here!&lt;/a&gt;
</pre>

## Installation

### From Plugin Repository

Click install and activate it.

### From Github

See [releases](https://github.com/tarosky/taro-external-permalink/releases).

## FAQ

### Where can I get supported?

Please create new ticket on support forum.

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
