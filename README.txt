=== Admin Notes WP ===
Contributors: stevepuddick
Donate link: https://webrockstar.net
Tags: admin, help, support, video, note, training, embed
Requires at least: 3.5
Tested up to: 5.7
Stable tag: 1.1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add 'how to' instructional videos, slide shows, and more directly in individual WordPress admin pages.

== Description ==

Admin Notes WP allows you to embed instructional 'how to' videos and other [embeddable media from major platforms](https://www.wpbeginner.com/beginners-guide/how-to-easily-embed-videos-in-wordpress-blog-posts/) directly in the WordPress admin help tabs. Useful, instructional videos can be included on the relevant admin pages to help website admin users learn how your plugins and theme work.

https://youtu.be/Ylhb-SmepJM

https://youtu.be/3sqjw-nN7Bc

For more quick videos on the features of Admin Notes WP visit the [Web Rockstar YouTube channel](https://www.youtube.com/channel/UCxAgzitpxI9kPZdFI3Z2ZmA)

**Other features include:**

* Along with embedded media, rich text can also be included in help tab content to explain how features work
* Custom notices can be placed on any admin screen, communicating important information about the page and how it works
* The custom admin footer text provide a subtle and unobtrusive information about about the company or person who setup/created the site
* Some users are not aware of the WordPress help tab. A bounce animation (similar to MacOS icon bounce) displays once per user until clicked to help them discover it for the first time
* Determining what 'screen id' a help tab should display on can be tricky. The Admin Screen ID Identifier Tool allows you to copy the screen id with the click of a button
* A default WordPress install comes with many built in help tabs that may not be relevant for your website and cause confusion. These default help tabs can be hidden if desired.

Components that are not relevant for your unique needs can be easily deactivated, removing them from the admin menus.

== Installation ==

After plugin activation go to /wp-admin/options-general.php?page=crb_carbon_fields_container_wp_admin_notes.php to configure
Admin Notes WP to your needs. This link is available in **Settings** > **Admin Notes WP**.


== Frequently Asked Questions ==

= Screen ID Helper Tool Not Displaying =

The 'Help Tab' or 'Notice' post type must be enabled in order for the 'Screen ID' header toolbar link to display.
'Help Tab' and 'Notice' post type enabling/disabling can be managed on the settings page in **Settings** > **Admin Notes WP Settings**.

= How do I find the screen id I want to display a help tab or notice on? =

The easiest way to obtain the screen id for the desired admin page is to use the 'Screen ID' helper tool. This is located
in the admin header toolbar. Just visit the desired admin page you would like the notice or help tab to appear on.
Click on the submenu link and the screen id will automatically be copied to your clipboard.  Refer to the screenshot
section to see what this looks like.

= User Capabilities =

In order to access the Admin Notes WP settings page, the user must have the **manage_options** capability. It should be
noted that the built in 'Administrator' role has this capability. In addition, the 'Administrator' role is also given the
following capabilities related to the 'Help Tab' and 'Notice' post type are also added to the Administrator role:

- edit_wpan_help_tab
- edit_wpan_help_tabs
- edit_others_wpan_help_tabs
- publish_wpan_help_tabs
- read_wpan_help_tab
- read_private_wpan_help_tabs
- delete_wpan_help_tab
- edit_published_wpan_help_tabs
- delete_published_wpan_help_tabs

- edit_wpan_notice
- edit_wpan_notices
- edit_others_wpan_notices
- publish_wpan_notices
- read_wpan_notice
- read_private_wpan_notices
- delete_wpan_notice
- edit_published_wpan_notices
- delete_published_wpan_notices

You can assign these capabilities to other user roles if desired. This can be done directly in your website code (https://developer.wordpress.org/plugins/users/roles-and-capabilities/#adding-capabilities)
or with a third party plugin like [User Role Editor](https://en-ca.wordpress.org/plugins/user-role-editor/).

= Screen ID 'copy to clipboard' link is not working =

The WordPress Admin must be viewed under SSL (the https:// URL) for the 'copy to clipboard' functionality to work.

= Help Tab not Appearing =

If you are using the WordPress Block Editor, the post edit page does not support help tabs. It will not display on that
particular page.

== Screenshots ==

1. The Global Help Tab is separate from the Help Tab post type. This special help tab appears on every admin page.
2. The admin footer text is a great, subtle way to leave your 'calling card'. It can be used to briefly state the company
or individual that created the site and contact information.
3. The Screen ID helper link in the toolbar makes identifying WordPress admin screen ids very easy
4. The main Admin Notes WP settings page
5. The Help Tab post type 'edit post' admin page
6. The Help Tab post type display
7. The Notice post type 'edit post' admin page
8. The Notice post type display
9. To help with discoverability of the help tab (some users are not aware of it), a bounce animation (similar to the MacOS icon bounce) can be enabled to occur once per user

== Useful Third Party Plugins and Other Techniques ==

The following are some additional third party plugins and useful techniques which can nicely enhance the functionality
of Admin Notes WP.

**Post Expirator**

The [Post Expirator](https://wordpress.org/plugins/post-expirator/) can allow you to set Help Tab and Notice
posts to expire (not display) after a certain time or date.

**Post Dating Posts**

Setting the Help Tab or Notice post published date in the future will cause that post to only start appearing once that
date and time has been reached.

**User Role Editor**

The [User Role Editor](https://en-ca.wordpress.org/plugins/user-role-editor/) plugin allows you to manage the user capabilities
associated with Admin Notes WP. Refer to the Frequently Asked Questions section for the full list.

== Changelog ==

= 1.1.0 =
* Rich text editor to Help Tab post type and Global help tab
* auto render URLs as clickable links
* improved field instructions
* ability to include iframes (videos) in help tab content
* improved i18n of field text

= 1.0.0 =
* Initial Release
