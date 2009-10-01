=== Visitor Maps and Who's Online ===
Contributors: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8600876
Tags: plugin, plugins, users, user, visitors, visitor, whos, online, map, maps, geolocation, statistics, stats, widget, sidebar, admin, dashboard, multilingual
Requires at least: 2.6
Tested up to: 2.8.4
Stable tag: trunk

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar. Has an admin dashboard to view visitor details.

== Description ==

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar to show how many users are online. Includes a Who's Online admin dashboard to view visitor details. The visitor details include: what page the visitor is on, IP address, host lookup, online time, city, state, country, geolocation maps and more. No API key needed. Easy and Quick 3 step install.

[Plugin URI]: (http://www.642weather.com/weather/scripts-wordpress-visitor-maps.php)

Features:
--------
 * Configure Options from Admin panel.
 * Who's Online Admin dashboard shows visitor details of search bots, members, guests, and you.
 * Optional Who's Online widget for sidebar, or footer. Shows how many guests and members are viewing your blog.
 * Enable display of city, state, and country flag.
 * Enable geolocation for automatic location pin Visitor Maps.
 * Enable "Who Is" lookup.
 * Enable host lookups for IP addresses.
 * Visitor details are stored in a database table for about 30 days.
 * Uses GeoLiteCity data created by MaxMind, available from www.maxmind.com
 * Valid coding for HTML and XHTML.
 * I18n language translation support (see FAQ)

Requirements/Restrictions:
-------------------------
 * Works with Wordpress 2.6+
 * PHP 4.3.9 or above with GD2 library support.
 * 30 megs of server space(with geolocation enabled)

Credits:
-------------------------
* Programmed by [Mike Challis](http://profiles.wordpress.org/mikechallis/), [Contact Mike Challis](http://www.642weather.com/weather/contact_us.php)
* Ported to Wordpress from the [Free Who's Online PHP Script by Mike Challis](http://www.642weather.com/weather/scripts-whos-online.php)
* Inspired by the osCommerce contribution [Who's Online Enhancement](http://addons.oscommerce.com/info/824)
* Geolocation map images contributed by [Jim McMurry](http://jcweather.us)
* Code sample for the map location pins contributed by [pinto](http://www.joske-online.be)
* Uses GeoLiteCity data created by MaxMind, available from www.maxmind.com
* Thanks to all the users who contributed ideas or enhancements.

== Installation ==

1. Upload the `visitor-maps` folder to the `/wp-content/plugins/` directory, or install automatically through the `Plugins`, `Add New` menu in WordPress.

2. Activate the plugin through the `Plugins` menu in WordPress. Look for the Settings link to configure the Options.

3. To display visitor maps on your blog: add the shortcode `[visitor-maps]` in a Page(not a Post). That page will become your Visitor Maps page. Here is how: Log into your blog admin dashboard. Click `Pages`, click `Add New`, add a title to your page, enter the shortcode `[visitor-maps]` in the page, click `Publish`.

4. To add the Who's Online sidebar: Click on Appearance, Widgets, then drag the Who's Online widget to the sidebar column on the right.

5. Updates are automatic. Click on "Upgrade Automatically" if prompted from the admin menu.


== Screenshots ==

1. screenshot-1.jpg is the Who's Online sidebar.

2. screenshot-2.jpg is the Visitor Maps Viewer.

3. screenshot-3.jpg is the Visitor Maps page.

4. screenshot-3.jpg is the View Who's Online Dashboard.

5. screenshot-3.jpg is the `Settings` page.

6. screenshot-3.jpg is adding the Who's Online sidebar.

7. screenshot-4.jpg adding the shortcode `[visitor-maps]` in a Page.


== Frequently Asked Questions ==

= Does this require a map API key? =

No, this uses GeoLiteCity data created by MaxMind, available from http://www.maxmind.com/ it does not require registering an API.
After you install this plugin, the GeoLiteCity data install is automatically processed by clicking a link on the settings page.

= How often does the GeoLiteCity data need to be updated? =

About once monthly an update becomes available, usually around the 1st-3rd of the month.
About 1-2% of the GeoLiteCity database changes each month.
When the update is available, the admin will be notified on the Who's Online Dashboard. The update is automatically downloaded by clicking a link.

= Why is Geolocation sometimes not accurate? =

Sometimes geolocation is close to perfect, sometimes not. Usually only about 85% accuracy.
The lat & lon, city, state parameters the database produces is for the location the ISP has reported for your current IP address.
Many ISPs share one block, or several blocks, of IP addresses with all their users.
Each time you connect you may get a different IP address assignment with different location details.
So the accuracy can even vary according to your current IP assignment.
This can cause the reported city, state, lat & lon from the IP to vary from your actual location.

To check the database itself, enter the IP address in this online demo.
http://www.maxmind.com/app/locate_ip

= Can you add charts and graphs of visitor activity like Google Analytics? =

Probably not. Google analytics, webalizer, etc. are already good free web tracking statistics tools.
I would still like to hear from you if you have an idea of how I can improve this. If your suggestion is useful and easy to code, I might add it.
[Contact Mike Challis](http://www.642weather.com/weather/contact_us.php)

= Is this plugin available in other languages? =

Please wait until this plugin has been in release for a month or so because I may update it frequently in the short term.

= Can I provide a translation? =

Yes but please wait until this plugin has been in release for a month or so because I may update it frequently in the short term.

== Changelog ==

= 1.0.2 =
- (30 Sep 2009) - Update title and descriptions.

= 1.0.1 =
- (30 Sep 2009) - Optimize screenshots.

= 1.0 =
- (30 Sep 2009) Initial Release.



