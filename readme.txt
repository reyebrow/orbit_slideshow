=== Basic Slideshow ===
Contributors: raychaser42
Tags: slideshow, jquery, jquerycycle, cycle, basic
Requires at least: 2.9
Tested up to: 3.2
Stable tag: 4.3

A basic, theme-able slideshow plugin using jQueryCycle and custom content types.

== Description ==

This is a very simple slideshow we developed for use on our clients' sites. It is very basic and customizable.

It's based heavily on the blog post at http://www.paddsolutions.com/how-to-integrate-jquery-cycle-plugin-to-wordpress-theme/

It assumes that you're going to want to do some CSS styling for your theme so the CSS code is included for convenience in the settings page. 

Features:
* Set Width / height of a slide in pixels
* Set Teaser Length
* Set Slide Time
* Set Pause on hover over a slide
* Set Transition speed
* Set Transition effect
* Video slides (Youtube in particular. Results may vary on other oembed types.)
* Link slides to any URL

Adding the slideshow to a page can be accomplished with a shortcode, with the included widget or programmatically by adding the plugin's main function to your functions.php in a theme. 


== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= How do I use the slideshow in a theme? =

Simply add the shortcode [basic_slideshow] to whatever page or post you want to see it on. 

You can use this theme as a widge. Look for the "Basic Slideshow" widget in your widgets listing.

 but that might not be flexible enough for you. If you want to do things manually drop the following code into your theme and it should give you what you want.

`<?php if (function_exists('re_slideshow_featured_posts')) re_slideshow_featured_posts(); ?>`
  
= Help! My images aren't resizing after I resize the slideshow! =

If you change the size of your slideshow above you're going to need to resize your thumbnails. Luckily a guy named Alex (Viper007Bond) wrote a plugin called "Regenerate Thumbnails" so you can install that and run it every time you resize the slideshow.

= How do I theme my slideshow? =

This plugin exists with the expectation that you WILL need to style it. Below is the CSS that you might want to change. Drop this code into one of your theme's CSS files and you should be good to go.

== Screenshots ==

1. Slideshow on a page
1. Administration interface
1. Custom Node Creation

== Changelog ==

= 1.0 =
* The plugin is created

