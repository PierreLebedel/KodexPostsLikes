=== Kodex Posts Likes ===
Contributors: Pierre Lebedel
Tags: posts, like, dislike, voting, vote
Requires at least: 4.0
Tested up to: 6.4.2
Stable tag: 2.5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple AJaX based WordPress Plugin which allows your visitors to like or dislike posts, pages and cutom post types. 

== Description ==

The Kodex Posts Likes plugin allows your visitors and logged in users to like or dislike your posts, pages, and custom post types.
The AJaX based interface is clean and fully customizable.
The Dislike button is not required, and the buttons labels are editables.
The buttons shows their counter.
Shortcodes are availables for display the buttons on your post content and for the Like counter only.
In the admin interface, columns are showing the actual count, and there is a metabox in the editon page.


== Installation ==

= Installing manually: =

1. Unzip all files to the `/wp-content/plugins/kodex-posts-likes` directory
2. Log into WordPress admin and activate the **Kodex Posts Likes** plugin through the 'Plugins' menu
3. Go to **Settings > Kodex Posts Likes** in the left-hand menu to configure plugin options


== Screenshots ==

1. Plugin settings page
2. Plugin documentation page
3. Posts list view
4. Post edit view
5. Front-end view


== Changelog ==

= 2.5.1 =
* Prepare strings for translate.wordpress.org

= 2.5.0 =
* Fixes security issus (CSRF)

= 2.4.0 =
* Admin dashboard stats of last days likes
* Buttons shortcode parameter for custom like and dislike text

= 2.3.0 =
* Save post action counter fix (counters began to 1)
* Sortable posts by like/dislike count in admin area
* Admin columns style and position
 
= 2.2.0 =
* Buttons and Counter shortcodes takes postid parameter for a display outside the loop
* Counter shortcode takes format parameter : html|number
* Changes in the admin settings area
* Screenshots update
 
= 2.1.0 =
* Source code based on WordPress Plugin Boilerplate