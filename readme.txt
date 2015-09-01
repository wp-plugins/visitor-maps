=== Visitor Maps and Who's Online ===
Contributors: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V3BPEZ9WGYEYG
Tags: plugin, plugins, users, visitors, visitor, whos online, map, maps, geolocation, location, country, statistics, stats, widget, sidebar, admin, dashboard, multilingual, wpmu, buddypress
Requires at least: 2.8
Tested up to: 4.3
Stable tag: trunk

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar. Has an admin dashboard to view visitor details.

== Description ==

Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar to show how many users are online. Includes a Who's Online admin dashboard to view visitor details. The visitor details include: what page the visitor is on, IP address, host lookup, online time, city, state, country, geolocation maps and more. No API key needed. Easy and Quick 4 step install.

= Help Keep This Plugin Free =

If you find this plugin useful to you, please consider [__making a small donation__](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V3BPEZ9WGYEYG) to help contribute to my time invested and to further development. Thanks for your kind support! - [__Mike Challis__](http://profiles.wordpress.org/users/MikeChallis/)

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
 * Works with Wordpress 2.8+, WPMU, and BuddyPress. (Wordpress 4.3+ is highly recommended)
 * PHP5
 * 30 megs of server space(with geolocation enabled)
 * PHP register_globals and safe_mode should be set to "Off"

Credits:
-------------------------
* Programmed by [Mike Challis](http://profiles.wordpress.org/mikechallis/), [Contact Mike Challis](http://www.642weather.com/weather/contact_us.php)
* Ported to Wordpress from the [Free Who's Online PHP Script by Mike Challis](http://www.642weather.com/weather/scripts-whos-online.php)
* Inspired by the osCommerce contribution [Who's Online Enhancement](http://addons.oscommerce.com/info/824)
* Geolocation map images contributed by [Jim McMurry](http://jcweather.us)
* Code sample for the map location pins contributed by pinto.
* Geolocation features available with [Visitor Maps Geolocation Addon](http://www.642weather.com/weather/scripts-wordpress-visitor-maps-geoip.php)
* Thanks to all the users who contributed ideas or enhancements.

== Installation ==

1. Install automatically through the `Plugins`, `Add New` menu in WordPress, or upload the `visitor-maps` folder to the `/wp-content/plugins/` directory.

2. Activate the plugin through the `Plugins` menu in WordPress. Look for the Settings link to configure the Options.

3. To display visitor maps on your blog: add the shortcode `[visitor-maps]` in a Page(not a Post). That page will become your Visitor Maps page. Here is how: Log into your blog admin dashboard. Click `Pages`, click `Add New`, add a title to your page, enter the shortcode `[visitor-maps]` in the page, click `Publish`.

4. To add the Who's Online sidebar: Click on Appearance, Widgets, then drag the Who's Online widget to the sidebar column on the right.
(If you do not use widgets and want to add this manually, see FAQ)

5. Updates are automatic. Click on "Upgrade Automatically" if prompted from the admin menu.

6. Install [Visitor Maps Geolocation Addon](http://www.642weather.com/weather/scripts-wordpress-visitor-maps-geoip.php) to enable geolocation. After installation be sure to activate the plugin. Next, go to the Visitor Maps Options menu to click to "Install" the Maxmind GeoLite City Database, then click "Enable Geolocation" and click Update Options. 


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
unzip it and upload GeoLiteCity.dat to the `/wp-content/visitor-maps-geoip/` folder. 
The GeoLiteCity.dat file is the database for the location from IP feature. 
If the file is missing, the blog should still function. When the file is not installed, the location information for a user is skipped.


= Why are all the location pins about 10 pixels too low on the visitor map? =
Some themes interfere with the proper display of the location pins on the Visitor Maps page. 
Uncheck the setting "Enable hover labels for location pins on visitor map page." 

= Why are the location pins different colors on the visitor map? =
Visitors have Red/yellow location pins. Search bots will have blue location pins. Registered users who are logged in will have green location pins.

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
* Bulgarian (bg_BG) - Translated by [Nicolas Nicolov](http://bnproject.com/)
* Chinese (zh_CN) - Translated by [Awu](http://www.awuit.cn/)
* Danish (da_DK) - Translated by [GeorgWP](http://wordpress.blogos.dk/wpdadkdownloads/)
* Dutch (nl_NL) - Translated by [Ton Strijbosch](http://www.westkreek.nl/weblog/)
* French (fr_FR) - Translated by [Whiler](http://blogs.wittwer.fr/)
* German (de_DE) - Translated by [JZDM](http://jzdm.de)
* Hebrew (he_IL) - Translated by [Udi Burg](http://blog.udiburg.com/)
* Hungarian (hu_HU) - Translated by [varnyu]
* Indonesian (id_ID) - Translated by [Masino Sinaga](http://www.masinosinaga.com/)
* Italian (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/)
* Japanese (ja) - Translated by [Chestnut]
* Lithuanian (lt_LT) - Translated by [Vincent G](http://www.Host1Free.com)
* Polish (pl_PL) - Translated by Krzysztof Adamski(http://www.aton-ht.com/)
* Portuguese (pt_PT) - Translated by Jose
* Romanian (ro_RO) - Translated by [Anunturi Jibo](http://www.jibo.ro)
* Russian (ru_RU) - Translated by [Zhmenia](http://zhmenia.wordpress.com)
* Spanish (es_ES) - Translated by [Natalia Pujol](http://www.natygames.com)
* Turkish (tr_TR) - Translated by Cenkgursu
* More are needed... Please help translate.

= Can I provide a new translation? =

Yes, please read [How to translate Visitor Maps for WordPress](http://www.fastsecurecontactform.com/translate-visitor-maps) 

= Can I update a translation? =

Yes, please read [How to update a translation of Visitor Maps for WordPress](http://www.fastsecurecontactform.com/update-translation-visitor-maps) 



== Changelog ==

= 1.5.8.10 =
- (01 Sep 2015) - added the ability to dismiss the admin message about downloading the Visitor Maps Geolocation Addon plugin.

= 1.5.8.9 =
- (31 Aug 2015) - Fixed error undefined function.

= 1.5.8.8 =
- (29 Aug 2015) - Moved the Geolocation features to the new [Visitor Maps Geolocation Addon](http://www.642weather.com/weather/scripts-wordpress-visitor-maps-geoip.php). It was a required change because the Creative Commons License for the Maxmind GeoLite City Database is not compatible with the WordPress GPL License. When the "Visitor Maps Geolocation Addon" plugin is installed with version 1.5.8.8 or higher of Visitor Maps, Visitor Maps functions exactly the same way it did before.
- cleaned up some code. 

= 1.5.8.7 =
- (21 Aug 2015) - Fixed Possible XSS Security Exploit in Visitor Maps - Who's Been Online view

= 1.5.8.6 =
- (28 Dec 2014) - Improved timezone compliance with WP.
- bing bot is recognized now.
- some minor bug fixes.

= 1.5.8.5 =
- (15 May 2014) - Removed themefuse ad
- tested for WP 3.9.1

= 1.5.8.4 =
- (29 Apr 2014) - Fixes for PHP 5.4
- tested for WP 3.9

= 1.5.8.3 =
- (18 Nov 2013) - Fix Array to string conversion notice class-wo-worldmap.php(343)

= 1.5.8.2 =
- (17 Nov 2013) - Fix compatibility with WP 3.7.1
- Fix possible error: preg_match() expects parameter to be string. 
- Added Lithuanian (lt_LT) - Translated by [Vincent G](http://www.Host1Free.com)

= 1.5.8.1 =
- (18 Jan 2012) - Added Hebrew (he_IL) - Translated by [Udi Burg](http://blog.udiburg.com/)
- Updated Italian (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/ "Gianni Diurno")
- Updated Turkish (tr_TR) - Translated by Sergio

= 1.5.8 =
- (07 Dec 2011) - I fixed the code so if you disable the geolocation setting then none of the maps will show, but the stats will.
- I added a new setting "Enable visitor map on Visitor Map shortcode page" you can uncheck if you want to have maps in the dashboard but not in the public side.
- Updated Chinese (zh_CN) - Translated by [Angela](http://www.centilin.com/)

= 1.5.7 =
- (03 Sep 2011) - Replaced default "Who Is Lookup URL" setting with a different provider.

= 1.5.6.5 =
- (05 Jul 2011) - Tested / fixed to be compatible with WP 3.2

= 1.5.6.4 =
- (02 Jun 2010) - Security enhancements for possible low level XSS exploit in admin settings: thanks to [Julio Potier](http://secu.boiteaweb.fr/).

= 1.5.6.3 =
- (05 Mar 2010) - Improved default map setting. Any map can be selected as default map.

= 1.5.6.2 =
- (23 Feb 2010) - Improvement: javascript is loaded in footer.
- New setting: "Limit for map pins". This limit protects server resources by limiting pins when displaying maps. Default is 2000.
- Made compatible with WP Minify.

= 1.5.6.1 =
- (04 Feb 2010) - Dashboard widget is now also controlled by the setting "Users who can view the dashboard pages:".
- Added Romanian (ro_RO) - Translated by [Anunturi Jibo](http://www.jibo.ro)
- Updated German (de_DE) - Translated by [JZDM](http://jzdm.de)
- Updated Japanese (ja) - Translated by [Chestnut]

= 1.5.6 =
- (29 Dec 2010) - Added new setting: "Users who can view the dashboard pages:" By default, only Administrators can view the dashboard pages. Change this setting to also allow Editors, Authors, or Contributors to view the dashboard pages. When set to Authors, you are also allowing Administrator and Editors. 
- Fixed deprecated register_sidebar_widget function. After updating you need to add the "Who's Online" widget again. Click on Appearance, Widgets, then drag the Who's Online widget to the sidebar column on the right.

= 1.5.5.1 =
- (17 Dec 2010) - Updated Japanese (ja) - Translated by [Chestnut]
- Some changes to admin page. 

= 1.5.5 =
- (15 Dec 2010) - Added new setting: "Combine guests and members on widgets so they are only shown as visitors." Use this setting when your site has registration turned off and all your visitors are guests and not members. 
- Added Netherlands map.
- Added Brazil map.
- Added Bulgarian (bg_BG) - Translated by [Nicolas Nicolov](http://bnproject.com/)

= 1.5.4 =
- (15 Oct 2010) - Added Japan map.
- Added Japanese (ja) - Translated by [Chestnut]

= 1.5.3 =
- (28 Sep 2010) - Updated a few languages.

= 1.5.2 =
- (28 Aug 2010) - Added new setting: "Enable user names on hover labels for location pins on visitor map page".
- Added different pin color (blue)for search bots (enable in settings by unchecking: "Do not include search bots in the visitors online count on widgets".
- Added different pin color (green)for registered users (disabled by default, enable in settings: "Enable user names on hover labels ...").
- Added Indonesian (iD_ID - Translated by [Masino Sinaga](http://www.masinosinaga.com/)
- Added Polish (pl_PL) - Translated by Krzysztof Adamski(http://www.aton-ht.com/)

= 1.5.1 =
- (18 Aug 2010) - Critical fix for some servers had 0 visitors, sorry about that.
- New setting: "URLs to ignore". Optional list of URLs on your site you do not want in any Who's Online data.

= 1.5 =
- (17 Aug 2010) - Improvement: Who's Online dashboard pages now remember the display settings.
- Improvement: Added count for search "bots" on the Who's Online Widget.
- New setting: "Hide map viewing by non administrators".
- New setting: "Do not include search bots in the visitors online count on widgets".
- Added download count and star rating on admin options page. 
- Added more help links on admin options page. 

= 1.4.3 =
- (26 Jul 2010) - Fixed MySQL database performance by adding index key.
- Improved GeoLiteCity database updater error reporting. 

= 1.4.2 =
- (11 Jul 2010) - Fixed problem with accent characters on the text on the map images.
- Added Danish (da_DK) - Translated by [GeorgWP](http://wordpress.blogos.dk/wpdadkdownloads/)
- Updated German (de_DE) - Translated by [JZDM](http://jzdm.de)

= 1.4.1 =
- (28 May 2010) - New setting: Enable visitor map link on Who's Online widget.  
- New setting: Enable stats display on map page.
- Updated spiders.txt
- New setting to allow multi-blog sharing of GeoLiteCity.dat (contact me if you need information)


= 1.4 =
- (15 May 2010) - Made WP3 Compatible

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



