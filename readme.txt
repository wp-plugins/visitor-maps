=== Visitor Maps and Who's Online ===
Contributors: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8600876
Tags: plugin, plugins, users, user, visitors, visitor, whos, online, map, maps, geo, geolocation, location, country, statistics, stats, widget, sidebar, admin, dashboard, multilingual, wpmu, buddypress
Requires at least: 2.6
Tested up to: 2.9.2
Stable tag: trunk

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar. Has an admin dashboard to view visitor details.

== Description ==

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar to show how many users are online. Includes a Who's Online admin dashboard to view visitor details. The visitor details include: what page the visitor is on, IP address, host lookup, online time, city, state, country, geolocation maps and more. No API key needed. Easy and Quick 4 step install.

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
 * Works with Wordpress 2.6+, WPMU, and BuddyPress
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
(If you do not use widgets and want to add this manually, see FAQ)

5. Updates are automatic. Click on "Upgrade Automatically" if prompted from the admin menu.


== Screenshots ==

1. screenshot-1.gif is the Who's Online sidebar.

2. screenshot-2.gif is the Visitor Maps Viewer.

3. screenshot-3.gif is the Visitor Maps page.

4. screenshot-4.gif is the View Who's Online Dashboard.

5. screenshot-5.gif is the `Settings` page.

6. screenshot-6.gif adding the shortcode `[visitor-maps]` in a Page.

7. screenshot-7.gif is adding the Who's Online sidebar.


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

= I don't use widgets. How can I add "Who's Online" to my sidebar manually? =

Upgrade to version 1.0.5 or higher and add this code to your theme's sidebar.php:

`
<?php
    // display Who's Online
     if (class_exists("VisitorMaps")) {
            $visitor_maps = new VisitorMaps();
     }
     if (isset($visitor_maps)) {
           echo '<ul><li>';
           $visitor_maps->visitor_maps_manual_sidebar();
           echo '</li></ul>';
     }
?>
`


= I can't get the geolite database to install. When I click on it, it tells me "download_file error: cannot write to file, check server permission settings" or "download_file error: reading or opening file" =

Your server's PHP settings is the cause. Possible causes: PHP `safe_mode` could be enabled, you should turn it off. `allow_url_fopen` could be disabled, you should turn it on.

If you can, edit your PHP.ini file (usually located in /etc/php.ini or the root folder of your web site) and make sure these two settings are like this:
`safe_mode = Off`, `allow_url_fopen = On`

The geolite database is really just a 30 meg file. As a workaround, you can manually download the 
[GeoLiteCity.dat.gz file from this URL](http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz), 
unzip it and upload GeoLiteCity.dat to the `/plugins/visitor-maps/` folder. 
The GeoLiteCity.dat file is the database for the location from IP feature. 
If the file is missing, the blog should function fine. When the file is not installed, the location information for a user is skipped.


= Why are all the location pins about 10 pixels too low on the visitor map? =
Some themes interfere with the proper display of the location pins on the Visitor Maps page. 
Uncheck the setting "Enable hover labels for location pins on visitor map page."  

= Can you add charts and graphs of visitor activity like Google Analytics? =

Probably not. Google analytics, webalizer, etc. are already good free web tracking statistics tools.
I would still like to hear from you if you have an idea of how I can improve this. If your suggestion is useful and easy to code, I might add it.
[Contact Mike Challis](http://www.642weather.com/weather/contact_us.php)

= Does this work on WPMU or BuddyPress? =
Yes, If you use WPMU or BuddyPress you can have multiple blogs with individual visitor maps on each one. On WPMU you would install it in `plugins`, not `mu-plugins`. Do not the plugin activate site wide, then each blog owner can have his own visitor map settings and dashboard view.

= Is this plugin available in other languages? =

Yes. To use a translated version, you need to obtain or make the language file for it.
At this point it would be useful to read [Installing WordPress in Your Language](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") from the Codex. You will need an .mo file for this plugin that corresponds with the "WPLANG" setting in your wp-config.php file. Translations are listed below -- if a translation for your language is available, all you need to do is place it in the `/wp-content/plugins/visitor-maps/languages` directory of your WordPress installation. If one is not available, and you also speak good English, please consider doing a translation yourself (see the next question).

The following translations are included in the download zip file:

* Belorussian (be_BY) - Translated by [Marcis G](http://pc.de)
* Brazilian Portuguese (pt_BR) - Translated by Miguel Netto
* Chinese (zh_CN) - Translated by [Awu](http://www.awuit.cn/)
* Dutch (nl_NL) - Translated by [Ton Strijbosch](http://www.westkreek.nl/weblog/)
* French (fr_FR) - Translated by [Whiler](http://blogs.wittwer.fr/)
* German (de_DE) - Translated by [JZDM](http://jzdm.de)
* Hungarian (hu_HU) - Translated by [varnyu]
* Italian (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/)
* Portuguese (pt_PT) - Translated by Jose
* Russian (ru_RU) - Translated by [Zhmenia](http://zhmenia.wordpress.com)
* Spanish (es_ES) - Translated by [Natalia Pujol](http://www.natygames.com)
* Turkish (tr_TR) - Translated by Cenkgursu
* More are needed... Please help translate.

= Can I provide a translation? =

Of course! It will be very gratefully received. Use PoEdit, it makes translation easy. Please read [Translating WordPress](http://codex.wordpress.org/Translating_WordPress "Translating WordPress") first for background information on translating. Then obtain the latest [.pot file](http://svn.wp-plugins.org/visitor-maps/trunk/languages/visitor-maps.pot ".pot file") and translate it.
* There are some strings with a space in front or end -- please make sure you remember the space!
* When you have a translation ready, please send the .po and .mo files to wp-translation at 642weather dot com.
* If you have any questions, feel free to email me also. Thanks!

= Is it possible to merge the translation files I sent to you with the ones of the newest version? =

If you use PoEdit to translate, it is easy to translate for a new version. You can open your current .po file, then select from the PoEdit menu: "Catalog" > "Update from POT file". Now all you have to change are the new language strings.


== Changelog ==

= 1.3.9 =
- (16 Apr 2010) - Fixed missing map image in version 1.3.8

= 1.3.8 =
- (16 Apr 2010) - Split code into smaller files for better memory performance.
- Added PHP Memory Limit increase code in GeoLiteCity data updater/installer.
- Added Belorussian (be_BY) - Translated by [Marcis G](http://pc.de)

= 1.3.7 =
- (26 Mar 2010) - Added optional setting: Do not show administrators on the maps.
- Added optional setting: Optional list of IP addresses for visitors you do not want shown on maps.   
- Added Brazilian Portuguese (pt_BR) - Translated by Miguel Netto

= 1.3.6 =
- (03 Feb 2010) - Fix Fatal error: Call to undefined function wp_timezone_supported

= 1.3.5 =
- (29 Jan 2010) - Fix HTML Strict validation
- Added Portuguese (pt_PT) - Translated by Jose

= 1.3.4 =
- (16 Jan 2010) - Fix for Finland map was actually Scandanavia. Added Finland Map. Thanks to Michael Swanson for the correct maps.
- More fixes to last issue.

= 1.3.3 =
- (15 Jan 2010) - Added new setting to fix when the map image does not work on some servers because of lack of TTF support.
see: http://wordpress.org/support/topic/337775

= 1.3.2 =
- (14 Jan 2010) - Fixed typo on settings page.
- Added Hungarian (hu_HU) - Translated by [varnyu](http://varnyu.info)

= 1.3.1 =
- (12 Dec 2009) - Fix Days to store visitor data setting to not allow integers greater than 10000

= 1.3 =
- (30 Nov 2009) - Fix to be compatible with other plugins that also use the Maxmind Geolocation Database.

= 1.2.9 =
- (10 Nov 2009) - Fix potential admin permissions problem. 
- Stability fix for missing spiders.txt could cause fatal error message.

= 1.2.8 =
- (05 Nov 2009) - Fix for shortcode was not printing from a specific location on the page.

= 1.2.7 =
- (04 Nov 2009) - Fix error: WordPress database error Duplicate entry ...

= 1.2.6 =
- (03 Nov 2009) - Fix settings were not being deleted when plugin is deleted from admin page.

= 1.2.5 =
- (21 Oct 2009) - Fixed problems with state names and "Show bots" on dashboard pages.
- Fixed location decending selectiion on who's been online page.
- Added Chinese (zh_CN) - Translated by [Awu](http://www.awuit.cn/)

= 1.2.4 =
- (12 Oct 2009) - Added Italian (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/)
- Fixed UTF-8 encoding for geolocation city names.
- Fixed preservation of GeoLiteCity.dat file timestamp during plugin upgrade.

= 1.2.3 =
- (11 Oct 2009) - Fixed visitor map images not working on systems with PHP `allow_url_fopen` disabled.
- Added link on who's online page to who's been online page.

= 1.2.2 =
- (10 Oct 2009) - Fixed show bots on Who's Been Online Admin Dashboard

= 1.2.1 =
- (09 Oct 2009) - Fixed broken popup for IP lookup (broken since several versions ago)

= 1.2 =
- (09 Oct 2009) - Added feature: Who's Been Online Admin Dashboard

= 1.1.7 =
- (08 Oct 2009) - Added Russian (ru_RU) - Translated by [Zhmenia](http://zhmenia.wordpress.com)
- Added French (fr_FR) - Translated by [Whiler](http://blogs.wittwer.fr/)

= 1.1.6 =
- (06 Oct 2009) - Added Spanish (es_ES) - Translated by [Natalia Pujol](http://www.natygames.com)
- Added Spain/Portugal visitor maps.
- Added setting to Enable hover labels for location pins on visitor map page (disabled by default).
Some themes interfere with the proper display of the location pins on the Visitor Maps page. After enabling this setting, check your visitor maps page to make sure the pins are placed correctly. If the pins are about 10 pixels too low on the map, undo this setting. 

= 1.1.5 =
- (06 Oct 2009) - Added German (de_DE) - Translated by [JZDM](http://jzdm.de)

= 1.1.4 =
- (05 Oct 2009) - Fixed Visitor Map Viewer link on settings page.
- Added Setting to enable or disable the visitor map on Who's Online dashboard.
- Fixed one string in the language translations.  

= 1.1.3 =
- (05 Oct 2009) - Added option setting to select default time and units for default map for the Visitor Map page.
- Added Dutch (nl_NL) - Translated by [Ton Strijbosch](http://www.westkreek.nl/weblog/)

= 1.1.2 =
- (04 Oct 2009) - Fix some WP themes were messing up the pin locations on the Visitor Maps Page

= 1.1 =
- (03 Oct 2009) - Fixed bug that caused NextGEN Gallery plugin admin subpanel links to not function. 
Sorry about that, it is all fixed in this update.

= 1.0.5 =
- (03 Oct 2009) - 
- Added the ability to add the Who's Online to the sidebar manually if you do not use widgets. (See FAQ)

= 1.0.4 =
- (02 Oct 2009) - Fixed issue where after upgrading the plugin you have to click "Install" to download the Maxmind GeoLiteCity database again.
(because the new code is needed before the upgrade to prevent this issue, you will also have to do it once after applying this update, sorry)
- Fixed some themes cause the location pins to have border margins.
- Fixed path problem in the GeoLiteCity updater class.

= 1.0.3 =
- (01 Oct 2009) - Added Turkish (tr_TR) - Translated by Cenkgursu

= 1.0.2 =
- (01 Oct 2009) - Update title and descriptions.

= 1.0.1 =
- (01 Oct 2009) - Optimize screenshots.

= 1.0 =
- (30 Sep 2009) Initial Release.



