<?php
/*
Plugin Name: Visitor Maps and Who's Online
Plugin URI: http://www.642weather.com/weather/scripts-wordpress-visitor-maps.php
Description: Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar to show how many users are online. Includes a Who's Online admin dashboard to view visitor details. The visitor details include: what page the visitor is on, IP address, host lookup, online time, city, state, country, geolocation maps and more. No API key needed.  <a href="plugins.php?page=visitor-maps/visitor-maps.php">Settings</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8600876">Donate</a>
Version: 1.1.6
Author: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
*/

/*  Copyright (C) 2008 Mike Challis  (http://www.642weather.com/weather/contact_us.php)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!class_exists('VisitorMaps')) {

 class VisitorMaps {
     var $visitor_maps_error;

function visitor_maps_add_tabs() {
    add_submenu_page('plugins.php', __('Visitor Maps Options', 'visitor-maps'), __('Visitor Maps Options', 'visitor-maps'), 'manage_options', __FILE__,array(&$this,'visitor_maps_options_page'));
    add_submenu_page('index.php', __('View Who\'s Online', 'visitor-maps'), __('View Who\'s Online', 'visitor-maps'), 'manage_options', 'visitor-maps',array(&$this,'visitor_maps_admin_view'));
}

function visitor_maps_admin_view(){
     global $visitor_maps_opt;

     if ( function_exists('current_user_can') && !current_user_can('manage_options') )
         die(__('You do not have permissions for managing this option', 'visitor-maps'));

    // show admin View Who's Online page
    echo '<div class="wrap">
    <h2>'.__('Visitor Maps', 'visitor-maps').' - '.__('View Who\'s Online', 'visitor-maps').'</h2>';
    require_once(dirname(__FILE__) .'/class-wo-view.php');
    $wo_view = new WoView();
    $wo_view->view_whos_online();


  if ($visitor_maps_opt['enable_location_plugin'] && $visitor_maps_opt['enable_dash_map'] ) {
    // show the map on the bottom of the admin View Who's Online page
    $map_settings = array(
       // html map settings
       // set these settings as needed
       'time'       => $visitor_maps_opt['track_time'],      // digits of time
       'units'      => 'minutes', // minutes, hours, or days (with or without the "s")
       'map'        => '2',       // 1,2 3, etc. (you can add more map images in settings)
       'pin'        => '1',       // 1,2,3, etc. (you can add more pin images in settings)
       'pins'       => 'off',     // off (off is required for html map)
       'text'       => 'on',      // on or off
       'textcolor'  => '000000',  // any hex color code
       'textshadow' => 'FFFFFF',  // any hex color code
       'textalign'  => 'cb',      // ll, ul, lr, ur, c, ct, cb (codes for: lower left, upper left, upper right, center, center top, center bottom)
       'ul_lat'     => '0',       // default 0 for worldmap
       'ul_lon'     => '0',       // default 0 for worldmap
       'lr_lat'     => '360',     // default 360 for worldmap
       'lr_lon'     => '180',     // default 180 for worldmap
       'offset_x'   => '0',       // + or - offset for x axis  - moves pins left, + moves pins right
       'offset_y'   => '0',       // + or - offset for y axis  - moves pins up,   + moves pins down
       'type'       => 'png',     // jpg or png (map output type)
             );
    echo $this->get_visitor_maps_worldmap($map_settings);
    echo '<p>'.sprintf( __('View more maps in the <a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;').'</p>';
  }
  if ($visitor_maps_opt['enable_credit_link']) {
    echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
  }
  echo '</div>';

} // end function visitor_maps_view

// outputs the map console page from a $_GET method
function visitor_maps_do_map_console() {
  global $visitor_maps_opt, $visitor_maps_stats;


  if( isset($_GET['wo_map_console']) ) {
    // this puts the visitor into the whos online database
    $visitor_maps_stats = $this->visitor_maps_activity_do();
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php echo __('Visitor Maps', 'visitor-maps').' - '; bloginfo('name'); ?></title>
<style type="text/css">
table.wo_map {
margin-left:auto;
margin-right:auto;
}
#wrapper {
margin-left:auto;
margin-right:auto;
text-align:center;
}
h3 {
font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
font-size: 14px;
}
p {
font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
font-size: 12px;
}
</style>
<script type="text/javascript"><!--

function getRefToDivMod( divID, oDoc ) {
	if( !oDoc ) { oDoc = document; }
	if( document.layers ) {
		if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
			for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
				y = getRefToDivNest(divID,oDoc.layers[x].document); }
			return y; } }
	if( document.getElementById ) { return oDoc.getElementById(divID); }
	if( document.all ) { return oDoc.all[divID]; }
	return oDoc[divID];
}

function resizeWinTo( idOfDiv ) {
	var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
	var x = window; x.resizeTo( screen.availWidth, screen.availWidth );
	var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
	var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
	x.resizeTo( oW + 200, oH + 200 );
	var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
	if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
	else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
	else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
	if( window.opera && !document.childNodes ) { myW += 16; }
	//second sample, as the table may have resized
	var oH2 = getRefToDivMod( idOfDiv );
	var oW2 = oH2.clip ? oH2.clip.width : oH2.offsetWidth;
	var oH2 = oH2.clip ? oH2.clip.height : oH2.offsetHeight;
	x.resizeTo( oW2 + ( ( oW + 200 ) - myW ), oH2 + ( (oH + 200 ) - myH ) );
}

//--></script>
</head>

<?php if ($visitor_maps_opt['enable_location_plugin']) {

// http://www.howtocreate.co.uk/perfectPopups.html
?>
<body onload="resizeWinTo('wrapper');" style="padding:0;margin:0;">


<div style="position:absolute;left:0px;top:0px;" id="wrapper">
<table><tr><td>
<?php
       require_once(dirname(__FILE__) .'/class-wo-map-page.php');
       $wo_map_page = new WoMapPage();
       $wo_map_page->do_map_page();

       echo '<p><a href="javascript:window.close()">'.__('Close', 'visitor-maps').'</a></p>';
           if ($visitor_maps_opt['enable_credit_link']) {
              echo '<p><small>'.__('Powered by Visitor Maps', 'visitor-maps').'</small></p>';
           }
       echo'</td></tr></table></div>';

    } else {
         echo '<body><p>'.__('Visitor Maps geolocation is disabled in settings.', 'visitor-maps').'</p>';
    }
    // footer
?>
</body>
</html>
<?php
    exit;
  }

} // end function visitor_maps_do_maps


// outputs a map image from a $_GET method
function visitor_maps_do_map_image() {
  global $visitor_maps_opt;

if( isset($_GET['do_wo_map']) ) {
  if ($visitor_maps_opt['enable_location_plugin']) {
     // begin whos online map
     require_once(dirname(__FILE__) .'/class-wo-worldmap.php');
     $wo_view_map = new WoViewMaps();
     $wo_view_map->display_map();
  }
    exit;
}

} // end function visitor_maps_do_maps

// this function prints a whos online map on a blog page
function visitor_maps_map_short_code() {
   global $visitor_maps_opt;

   if ($visitor_maps_opt['enable_location_plugin']) {
     // show the map on View Who's Online page
     if ($visitor_maps_opt['enable_visitor_map_hover']) {
        $map_settings = array(
          // html map settings
          // set these settings as needed
          'time'       => $visitor_maps_opt['default_map_time'], // digits of time
          'units'      => $visitor_maps_opt['default_map_units'], // minutes, hours, or days (with or without the "s")
          'map'        => $visitor_maps_opt['default_map'], // 1,2 3, etc.
          'pin'        => '1',       // 1,2,3, etc. (you can add more pin images in settings)
          'pins'       => 'off',     // off (off is required for html map)
          'text'       => 'on',      // on or off
          'textcolor'  => '000000',  // any hex color code
          'textshadow' => 'FFFFFF',  // any hex color code
          'textalign'  => 'cb',      // ll, ul, lr, ur, c, ct, cb (codes for: lower left, upper left, upper right, center, center top, center bottom)
          'ul_lat'     => '0',       // default 0 for worldmap
          'ul_lon'     => '0',       // default 0 for worldmap
          'lr_lat'     => '360',     // default 360 for worldmap
          'lr_lon'     => '180',     // default 180 for worldmap
          'offset_x'   => '0',       // + or - offset for x axis  - moves pins left, + moves pins right
          'offset_y'   => '0',       // + or - offset for y axis  - moves pins up,   + moves pins down
          'type'       => 'png',     // jpg or png (map output type)
             );
         echo $this->get_visitor_maps_worldmap($map_settings);

     } else {
        // had to disable the dynamic map and replace with this because some WP themes were messing up the pin locations
        echo '<img alt="'.__('Visitor Maps', 'visitor-maps').'" src="'.get_bloginfo('url').'?do_wo_map=1&amp;time='.$visitor_maps_opt['default_map_time'].'&amp;units='.$visitor_maps_opt['default_map_units'].'&amp;map='.$visitor_maps_opt['default_map'].'&amp;pin=1&amp;pins=on&amp;text=on&amp;textcolor=000000&amp;textshadow=FFFFFF&amp;textalign=cb&amp;ul_lat=0&amp;ul_lon=0&amp;lr_lat=360&amp;lr_lon=180&amp;offset_x=0&amp;offset_y=0&amp;type=png" />';
     }

     echo '<p>'.__('View more maps in the ', 'visitor-maps').'<a href="'.get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;">'.__('Visitor Map Viewer', 'visitor-maps').'</a></p>';
     if ($visitor_maps_opt['enable_credit_link']) {
          echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
     }
  } else {
    echo '<p>'.__('Visitor Maps geolocation is disabled in settings.', 'visitor-maps').'</p>';
  }

} // end function visitor_maps_map_short_code

// header code for the admin view whos online page
function visitor_maps_public_header() {
  global $visitor_maps_opt;
?>
<!-- begin visitor maps header code -->
<script type="text/javascript" language="JavaScript">
<!--
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//-->
</script>
<!-- end visitor maps header code -->
<?php
} // end function visitor_maps_public_header


// header code for the admin view whos online page
function visitor_maps_admin_view_header() {
  global $visitor_maps_opt;

// only load this header stuff on the whos online view page
if(isset($_GET['page']) && $_GET['page'] == 'visitor-maps' ) {
    echo '<!-- begin visitor maps - whos online page header code -->'."\n";
   if(  isset($_GET['refresh']) && is_numeric($_GET['refresh']) ){
         $query = '&amp;refresh='. $_GET['refresh'];
         if(  isset($_GET['show']) && $_GET['show'] != '') {
            if ( $_GET['show'] == 'all' || $_GET['show'] == 'bots' || $_GET['show'] == 'guests' ){
               $query .= '&amp;show='. $_GET['show'];
            }
         }
         if(  isset($_GET['bots']) ) {
              $query .= '&amp;bots=1';
         }
         echo '<meta http-equiv="refresh" content="' . $_GET['refresh'] . ';URL=' . admin_url( 'index.php?page=visitor-maps' ) . $query . '" />
          ';
  }
?>
<script type="text/javascript" language="JavaScript">
<!--
function who_is(url) {
  window.open(url,"who_is_lookup","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//-->
</script>
<style type="text/css">
.table-top {
  color: black;
  background-color: #96C6F5;
  text-align: left;
  font-weight: bold;
}
.column-dark {
  color: black;
  background-color: #F1F8FE;
  white-space: nowrap;
}
.column-light {
  color: black;
  background-color: white;
  white-space: nowrap;
}
</style>
<!-- end visitor maps - whos online page header code -->
<?php
  } // end if(isset($_GET['page'])

// only load this header stuff on the whos online settings page
if(isset($_GET['page']) && $_GET['page'] == 'visitor-maps/visitor-maps.php' ) {
?>
<!-- begin visitor maps - settings page header code -->
<script type="text/javascript" language="JavaScript">
<!--
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//-->
</script>
<!-- end visitor maps - settings page header code -->
<?php
  } // end if(isset($_GET['page'])

} // end function visitor_maps_view_header


// start whos online user activity
function visitor_maps_activity() {
    global $visitor_maps_stats;

    // this puts the visitor into the whos online database
    $visitor_maps_stats = $this->visitor_maps_activity_do();

}

// call print stats in admin footer
function visitor_maps_admin_footer_stats() {
    global $visitor_maps_opt, $visitor_maps_stats;

  if ($visitor_maps_opt['enable_admin_footer']) {
    echo
      '<div class="footer" style="text-align:center"><p>
      '.$visitor_maps_stats.'</p>
      </div>
      ';
  }
}

// call print stats in public footer
function visitor_maps_public_footer_stats() {
   global $visitor_maps_opt, $visitor_maps_stats;

  if ($visitor_maps_opt['enable_blog_footer']) {
    echo $visitor_maps_stats;
  }
}

function visitor_maps_activation_notice(){
  // print message reminding to install  Maxmind GeoLiteCity database
  echo '<div class="error fade"><p><strong>'.__('Visitor Maps plugin needs the Maxmind GeoLiteCity database installed.', 'visitor-maps').' <a href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Install Now', 'visitor-maps'). '</a></strong></p></div>';
}

function visitor_maps_install() {
	global $wpdb, $wp_version;

	$wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
    $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
    $wo_table_ge = $wpdb->prefix . 'visitor_maps_ge';


	if($wpdb->get_var("show tables like '". $wo_table_wo . "'") != $wo_table_wo) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wo_table_wo . "` (
       `session_id`      varchar(128) NOT NULL default '',
       `ip_address`      varchar(20) NOT NULL default '',
       `user_id`         bigint(20) unsigned NOT NULL default '0',
       `name`            varchar(64) NOT NULL default '',
       `nickname`        varchar(20) default NULL,
       `country_name`    varchar(50) default NULL,
       `country_code`    char(2) default NULL,
       `city_name`       varchar(50) default NULL,
       `state_name`      varchar(50) default NULL,
       `state_code`      char(2) default NULL,
       `latitude`        decimal(10,4) default '0.0000',
       `longitude`       decimal(10,4) default '0.0000',
       `last_page_url`   text NOT NULL,
       `http_referer`    varchar(255) default NULL,
       `user_agent`      varchar(255) NOT NULL default '',
       `hostname`        varchar(255) default NULL,
       `provider`        varchar(255) default NULL,
       `time_entry`      int(10) unsigned NOT NULL default '0',
       `time_last_click` int(10) unsigned NOT NULL default '0',
       `num_visits`      int(10) unsigned NOT NULL default '0',
        PRIMARY KEY  (`session_id`) )");
	}

    if($wpdb->get_var("show tables like '". $wo_table_st . "'") != $wo_table_st) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wo_table_st . "` (
        `type`  varchar(14) NOT NULL default '',
        `count` mediumint(8) NOT NULL default '0',
        `time`  datetime NOT NULL default '0000-00-00 00:00:00',
         PRIMARY KEY  (`type`))");

       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('day', '1', now())");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('month', '1', now())");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('year', '1', now())");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('all', '1', now())");

	}

    if($wpdb->get_var("show tables like '". $wo_table_ge . "'") != $wo_table_ge) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wo_table_ge . "` (
         `time_last_check` int(10) unsigned NOT NULL default '0',
         `needs_update` tinyint(1) unsigned NOT NULL default '0')");
	}

} // end function visitor_maps_install


// called when deleting plugin
function visitor_maps_unset_options() {
   	global $wpdb, $wp_version;

  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
  $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
  $wo_table_ge = $wpdb->prefix . 'visitor_maps_ge';

  $wpdb->query("DROP TABLE IF EXISTS `". $wo_table_wo . "`");
  $wpdb->query("DROP TABLE IF EXISTS `". $wo_table_st . "`");
  $wpdb->query("DROP TABLE IF EXISTS `". $wo_table_ge . "`");

  delete_option('visitor_maps');

} // end function visitor_maps_unset_options


function visitor_maps_plugin_action_links( $links, $file ) {
    //Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ){
	   $settings_link = '<a href="plugins.php?page=visitor-maps/visitor-maps.php">' . esc_html( __( 'Settings', 'visitor-maps' ) ) . '</a>';
       array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

function visitor_maps_init() {

  // should set timezone according to wp general options  (PHP5 only)
 // If you've updated to WordPress v2.8 and the new Timezone support isn't available to you,
 // check the version of PHP your blog is working with.
 // If it's still using PHP4, then ask the hosting company how to get PHP5 enabled.
 if ( wp_timezone_supported() && $timezone_string = get_option( 'timezone_string' )) {
      // Set timezone in PHP5 manner
      @date_default_timezone_set( $timezone_string );
 }


 if (function_exists('load_plugin_textdomain')) {
      load_plugin_textdomain('visitor-maps', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)).'/languages', dirname(plugin_basename(__FILE__)).'/languages' );
 }

} // end function visitor_maps_init

function visitor_maps_get_options() {
   global $visitor_maps_opt, $visitor_maps_option_defaults;

  $visitor_maps_option_defaults = array(
   'donated' => 0,
   'active_time' => 5,
   'track_time' =>  15,
   'store_days' =>  30,
   'time_format' =>            'h:i a T',
   'time_format_hms' =>        'h:i:sa' ,
   'date_time_format' =>       'm-d-Y h:i a T',
   'geoip_date_format' =>      'm-d-Y h:i a T',
   'whois_url' =>              'http://www.geo-location.com/cgi-bin/index.cgi?s=',
   'whois_url_popup' =>        1,
   'enable_host_lookups' =>    1,
   'enable_location_plugin' => 1,
   'enable_state_display' =>   1,
   'show_bots_on_worldmap' =>  1,
   'enable_visitor_map_hover' => 0,
   'enable_blog_footer' =>     1,
   'enable_admin_footer' =>    1,
   'enable_credit_link' =>     1,
   'enable_dash_map' =>        1,
   'default_map' =>            1,
   'default_map_time' =>       30,
   'default_map_units' =>      'days',

 );
  // install the option defaults
  add_option('visitor_maps', $visitor_maps_option_defaults, '', 'yes');

  // get the options from the database
  $visitor_maps_opt = get_option('visitor_maps');

  // array merge incase this version has added new options
  $visitor_maps_opt = array_merge($visitor_maps_option_defaults, $visitor_maps_opt);

  // strip slashes on get options array
  foreach($visitor_maps_opt as $key => $val) {
           $visitor_maps_opt[$key] = $this->wo_stripslashes($val);
  }

} // end function visitor_maps_get_options

function visitor_maps_options_page() {
  global $visitor_maps_opt, $visitor_maps_option_defaults;

      if ( function_exists('current_user_can') && !current_user_can('manage_options') )
                        die(__('You do not have permissions for managing this option', 'visitor-maps'));

 if (isset($_GET['do_geo']) ) {
    check_admin_referer( 'visitor-maps-geo_update'); // nonce
    // install or update the geolocation data file
    require_once(dirname(__FILE__) .'/class-wo-update.php');
    $wo_update = new WoProGeoLocUpdater();
    $wo_update->update_now();
    $visitor_maps_opt['enable_location_plugin'] = 1;
    // save updated option to the database
    update_option('visitor_maps', $visitor_maps_opt);
    return;
 }

  if (isset($_POST['submit'])) {
   check_admin_referer( 'visitor-maps-options_update'); // nonce
   // post changes to the options array
   $optionarray_update = array(

   'donated' =>                  (isset( $_POST['visitor_maps_donated'] ) ) ? 1 : 0,
   'active_time' =>          absint(trim($_POST['visitor_maps_active_time'])),
   'track_time' =>           absint(trim($_POST['visitor_maps_track_time'])),
   'store_days' =>           absint(trim($_POST['visitor_maps_store_days'])),
   'time_format' =>               ( trim($_POST['visitor_maps_time_format']) != '' ) ? trim($_POST['visitor_maps_time_format']) : $visitor_maps_option_defaults['time_format'], // use default if empty
   'time_format_hms' =>           ( trim($_POST['visitor_maps_time_format_hms']) != '' ) ? trim($_POST['visitor_maps_time_format_hms']) : $visitor_maps_option_defaults['time_format_hms'],
   'date_time_format' =>          ( trim($_POST['visitor_maps_date_time_format']) != '' ) ? trim($_POST['visitor_maps_date_time_format']) : $visitor_maps_option_defaults['date_time_format'],
   'geoip_date_format' =>         ( trim($_POST['visitor_maps_geoip_date_format']) != '' ) ? trim($_POST['visitor_maps_geoip_date_format']) : $visitor_maps_option_defaults['geoip_date_format'],
   'whois_url' =>                 ( trim($_POST['visitor_maps_whois_url']) != '' ) ? trim($_POST['visitor_maps_whois_url']) : $visitor_maps_option_defaults['whois_url'], // use default if empty
   'whois_url_popup' =>          (isset( $_POST['visitor_maps_whois_url_popup'] ) ) ? 1 : 0,
   'enable_host_lookups' =>      (isset( $_POST['visitor_maps_enable_host_lookups'] ) ) ? 1 : 0,
   'enable_location_plugin' =>   (isset( $_POST['visitor_maps_enable_location_plugin'] ) ) ? 1 : 0,
   'enable_state_display' =>     (isset( $_POST['visitor_maps_enable_state_display'] ) ) ? 1 : 0,
   'show_bots_on_worldmap' =>    (isset( $_POST['visitor_maps_show_bots_on_worldmap'] ) ) ? 1 : 0,
   'enable_visitor_map_hover' => (isset( $_POST['visitor_maps_enable_visitor_map_hover'] ) ) ? 1 : 0,
   'enable_blog_footer' =>       (isset( $_POST['visitor_maps_enable_blog_footer'] ) ) ? 1 : 0,
   'enable_admin_footer' =>      (isset( $_POST['visitor_maps_enable_admin_footer'] ) ) ? 1 : 0,
   'enable_credit_link' =>       (isset( $_POST['visitor_maps_enable_credit_link'] ) ) ? 1 : 0,
   'enable_dash_map' =>          (isset( $_POST['visitor_maps_enable_dash_map'] ) ) ? 1 : 0,
   'default_map' =>          absint(trim($_POST['visitor_maps_default_map'])),
   'default_map_time' =>     absint(trim($_POST['visitor_maps_default_map_time'])),
   'default_map_units' =>           trim($_POST['visitor_maps_default_map_units']),
  );

    // deal with quotes
    foreach($optionarray_update as $key => $val) {
           $optionarray_update[$key] = str_replace('&quot;','"',trim($val));
    }

    // save updated options to the database
    update_option('visitor_maps', $optionarray_update);

    // get the options from the database
    $visitor_maps_opt = get_option('visitor_maps');

    // strip slashes on get options array
    foreach($visitor_maps_opt as $key => $val) {
           $visitor_maps_opt[$key] = $this->wo_stripslashes($val);
    }

    if (function_exists('wp_cache_flush')) {
	     wp_cache_flush();
	}

  } // end if (isset($_POST['submit']))

?>
<?php if ( !empty($_POST ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'visitor-maps') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Visitor Maps and Who\'s Online Options', 'visitor-maps') ?></h2>

<script type="text/javascript">
    function toggleVisibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
</script>

<p>
<a href="http://wordpress.org/extend/plugins/visitor-maps/changelog/" target="_blank"><?php echo esc_html( __('Changelog', 'visitor-maps')); ?></a> |
<a href="http://wordpress.org/extend/plugins/visitor-maps/faq/" target="_blank"><?php echo esc_html( __('FAQ', 'visitor-maps')); ?></a> |
<a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_blank"><?php echo esc_html( __('Rate This', 'visitor-maps')); ?></a> |
<a href="http://wordpress.org/tags/visitor-maps?forum_id=10" target="_blank"><?php echo esc_html( __('Support', 'visitor-maps')); ?></a> |
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8600876" target="_blank"><?php echo esc_html( __('Donate', 'visitor-maps')); ?></a> |
<a href="http://www.642weather.com/weather/scripts.php" target="_blank"><?php echo esc_html( __('Free PHP Scripts', 'visitor-maps')); ?></a> |
<a href="http://www.642weather.com/weather/contact_us.php" target="_blank"><?php echo esc_html( __('Contact', 'visitor-maps')); ?> Mike Challis</a>
</p>

<?php
if (!$visitor_maps_opt['donated']) {
?>
<h3><?php echo esc_html( __('Donate', 'visitor-maps')); ?></h3>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">

<table style="background-color:#FFE991; border:none; margin: -5px 0;" width="500">
        <tr>
        <td>
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="8600876" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" style="border:none;" name="submit" alt="Paypal Donate" />
<img alt="" style="border:none;" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</td>
<td><?php echo esc_html( __('If you find this plugin useful to you, please consider making a small donation to help contribute to further development. Thanks for your kind support!', 'visitor-maps')); ?> - Mike Challis</td>
</tr></table>
</form>
<br />

<?php
}
?>

<form name="formoptions" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>&amp;updated=true">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="visitor_maps_set" value="1" />
        <input type="hidden" name="form_type" value="upload_options" />
        <?php wp_nonce_field('visitor-maps-options_update'); ?>

    <input name="visitor_maps_donated" id="visitor_maps_donated" type="checkbox" <?php if( $visitor_maps_opt['donated'] ) echo 'checked="checked"'; ?> />
    <label for="visitor_maps_donated"><?php echo esc_html( __('I have donated to help contribute for the development of this plugin.', 'visitor-maps')); ?></label>
    <br />

<h3><?php _e('Usage', 'visitor-maps') ?></h3>
	<p>
    <?php echo __('Add the shortcode <b>[visitor-maps]</b> in a Page(not a Post). That page will become your Visitor Maps page.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-6.jpg" target="_new"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
    </p>
   	<p>
    <?php echo __('Add the Who\'s Online sidebar. Click on Appearance, Widgets, then drag the Who\'s Online widget to the sidebar column on the right.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-7.jpg" target="_new"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
    </p>
<?php echo '
<p>
<a href="'.admin_url( 'index.php?page=visitor-maps').'">' . esc_html( __( 'View Who\'s Online', 'visitor-maps' ) ) . '</a>
<br />
'.sprintf( __('<a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;').
"</p>\n";
?>

<h3><?php echo esc_html( __('Options', 'visitor-maps')) ?></h3>

        <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Update Options', 'visitor-maps') ?> &raquo;" />
        </p>

<fieldset class="options">

 <table cellspacing="2" cellpadding="5" class="form-table">

        <tr>
         <th scope="row" style="width: 75px;"><?php echo esc_html( __('GeoLocation:', 'visitor-maps')); ?></th>
      <td>

      <?php
      echo '<strong>'. esc_html( __('Uses GeoLiteCity data created by MaxMind, available from http://www.maxmind.com', 'visitor-maps')) .'</strong><br />';
      if ( !is_file(dirname(__FILE__) .'/GeoLiteCity.dat') ) {
        echo '<span style="background-color:#FFE991; padding:4px;"><strong>'. esc_html( __('The Maxmind GeoLiteCity database is not yet installed.', 'visitor-maps')). ' <a style="color:red" href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Install Now', 'visitor-maps'). '</a></strong></span>';
      } else if (!$visitor_maps_opt['enable_location_plugin']) {
              echo '<span style="background-color:#FFE991; padding:4px;"><strong>'. esc_html( __('The Maxmind GeoLiteCity database is installed but not enabled (check the setting below).', 'visitor-maps')). '</strong></span>';
      } else {
             echo '<span style="background-color:#99CC99; padding:4px;"><strong>'. esc_html( __('The Maxmind GeoLiteCity database is installed and enabled.', 'visitor-maps')). '</strong></span>';
      }
      ?>

      <br />
      <input name="visitor_maps_enable_location_plugin" id="visitor_maps_enable_location_plugin" type="checkbox" <?php if( $visitor_maps_opt['enable_location_plugin'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_location_plugin"><?php echo esc_html( __('Enable geolocation.', 'visitor-maps')); ?></label>
      <?php if( $visitor_maps_opt['enable_location_plugin'] && is_file(dirname(__FILE__) .'/GeoLiteCity.dat')) echo ' <a href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Update Now', 'visitor-maps'). '</a>';?>
      <br />

      <input name="visitor_maps_show_bots_on_worldmap" id="visitor_maps_show_bots_on_worldmap" type="checkbox" <?php if( $visitor_maps_opt['show_bots_on_worldmap'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_show_bots_on_worldmap"><?php echo esc_html( __('Enable display of bots on geolocation maps.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_state_display" id="visitor_maps_enable_state_display" type="checkbox" <?php if( $visitor_maps_opt['enable_state_display'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_state_display"><?php echo esc_html( __('Enable display of city, state next to country flag.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_dash_map" id="visitor_maps_enable_dash_map" type="checkbox" <?php if( $visitor_maps_opt['enable_dash_map'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_dash_map"><?php echo esc_html( __('Enable visitor map on Who\'s Online dashboard.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_visitor_map_hover" id="visitor_maps_enable_visitor_map_hover" type="checkbox" <?php if( $visitor_maps_opt['enable_visitor_map_hover'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_visitor_map_hover"><?php echo esc_html( __('Enable hover labels for location pins on visitor map page.', 'visitor-maps')); ?></label>
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_enable_visitor_map_hover_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_visitor_map_hover_tip">
      <?php echo esc_html( __('Some themes interfere with the proper display of the location pins on the Visitor Maps page. After enabling this setting, check your visitor maps page to make sure the pins are placed correctly. If the pins are about 10 pixels too low on the map, undo this setting.', 'visitor-maps')); ?>
      </div>
      <br />


      <?php echo esc_html( __('Default Visitor Map', 'visitor-maps')); ?>
      <label for="visitor_maps_default_map_time"><?php echo esc_html(__('Time:', 'visitor-maps')); ?></label>
      <input type="text" id="visitor_maps_default_map_time" name="visitor_maps_default_map_time" value="<?php echo absint($visitor_maps_opt['default_map_time']) ?>" size="3" />
      <label for="visitor_maps_default_map_units"><?php echo esc_html(__('Units:', 'visitor-maps')); ?></label>
      <select id="visitor_maps_default_map_units" name="visitor_maps_default_map_units">
<?php
$map_units_array =array(
'minutes' => esc_attr(__('minutes', 'visitor-maps')),
'hours' => esc_attr(__('hours', 'visitor-maps')),
'days' => esc_attr(__('days', 'visitor-maps')),
);
$selected = '';
foreach ($map_units_array as $k => $v) {
 if ($visitor_maps_opt['default_map_units'] == "$k")  $selected = ' selected="selected"';
 echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>'."\n";
 $selected = '';
}
?>
</select>

<label for="visitor_maps_default_map"><?php echo esc_html(__('Map:', 'visitor-maps')); ?></label>


      <select id="visitor_maps_default_map" name="visitor_maps_default_map">
      <?php
       $default_map_select_array = array(
          '1'  => __('World (smallest)', 'visitor-maps'),
          '2'  => __('World (small)', 'visitor-maps'),
          '3'  => __('World (medium)', 'visitor-maps'),
          '4'  => __('World (large)', 'visitor-maps'),
           );

      $selected = '';
      foreach ($default_map_select_array as $k => $v)  {
          if ($visitor_maps_opt['default_map'] == $k) {
                    $selected = 'selected="selected"';
          }
          echo '<option value="' . esc_attr($k) . '" ' . $selected . '>' . esc_attr($v) . '</option>' . "\n";
          $selected = '';
      }

      echo '</select>';

      ?>
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_default_map_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_default_map_tip">
      <?php echo esc_html( __('Default map to display on the Visitor Maps page. After setting this, check your visitor maps page to make sure it fits correctly. If the map is too wide, select the next smaller one.', 'visitor-maps')); ?>
      </div>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo esc_html( __('Visitors:', 'visitor-maps')); ?></th>
      <td>

      <label for="visitor_maps_active_time"><?php echo esc_html( __('Active time (minutes)', 'visitor-maps')); ?>:</label><input name="visitor_maps_active_time" id="visitor_maps_active_time" type="text" value="<?php echo absint($visitor_maps_opt['active_time']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_active_time_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_active_time_tip">
      <?php echo esc_html( __('Seconds that a visitor is considered "active". Default is 5 minutes.', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_track_time"><?php echo esc_html( __('Inactive time (minutes)', 'visitor-maps')); ?>:</label><input name="visitor_maps_track_time" id="visitor_maps_track_time" type="text" value="<?php echo absint($visitor_maps_opt['track_time']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_track_time_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_track_time_tip">
      <?php echo esc_html( __('Seconds before a inactive visitor is removed from display. Default is 15 minutes.', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_store_days"><?php echo esc_html( __('Days to store visitor data', 'visitor-maps')); ?>:</label><input name="visitor_maps_store_days" id="visitor_maps_store_days" type="text" value="<?php echo absint($visitor_maps_opt['store_days']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_store_days_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_store_days_tip">
      <?php echo esc_html( __('Days to store visitor data in database table. This data is used for the geolocation maps. Default is 30 days.', 'visitor-maps')); ?>
      </div>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo esc_html( __('Lookups:', 'visitor-maps')); ?></th>
      <td>

      <label for="visitor_maps_whois_url"><?php echo esc_html( __('Who Is Lookup URL', 'visitor-maps')); ?>:</label><input name="visitor_maps_whois_url" id="visitor_maps_geoip_date_format" type="text" value="<?php echo $visitor_maps_opt['whois_url'];  ?>" size="55" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_whois_url_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_whois_url_tip">
      <?php echo esc_html( __('URL to open when an IP address is clicked on.', 'visitor-maps')); ?>
      </div>
      <br />

      <input name="visitor_maps_whois_url_popup" id="visitor_maps_whois_url_popup" type="checkbox" <?php if( $visitor_maps_opt['whois_url_popup'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_whois_url_popup"><?php echo esc_html( __('Enable open Who Is Lookup URL on a pop-up.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_host_lookups" id="visitor_maps_enable_host_lookups" type="checkbox" <?php if( $visitor_maps_opt['enable_host_lookups'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_host_lookups"><?php echo esc_html( __('Enable host lookups for IP addresses.', 'visitor-maps')); ?></label>
      <br />

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo esc_html( __('Stats:', 'visitor-maps')); ?></th>
      <td>
      <input name="visitor_maps_enable_blog_footer" id="visitor_maps_enable_blog_footer" type="checkbox" <?php if( $visitor_maps_opt['enable_blog_footer'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_blog_footer"><?php echo esc_html( __('Enable stats display in blog footer.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_admin_footer" id="visitor_maps_enable_admin_footer" type="checkbox" <?php if( $visitor_maps_opt['enable_admin_footer'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_admin_footer"><?php echo esc_html( __('Enable stats display in admin footer.', 'visitor-maps')); ?></label>
      <br />

      <input name="visitor_maps_enable_credit_link" id="visitor_maps_enable_credit_link" type="checkbox" <?php if ( $visitor_maps_opt['enable_credit_link'] ) echo ' checked="checked" '; ?> />
      <label for="visitor_maps_enable_credit_link"><?php echo esc_html( __('Enable plugin credit link:', 'visitor-maps')) ?></label> <small><?php echo __('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps'); ?></a></small>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo esc_html( __('Times:', 'visitor-maps')); ?></th>
      <td>

      <br />
      <a href="http://php.net/date" target="_blank"><?php echo esc_html( __('Table of date format characters.', 'visitor-maps')); ?></a>
      <br />

      <label for="visitor_maps_time_format"><?php echo esc_html( __('Time format (Max Users Today)', 'visitor-maps')); ?>:</label><input name="visitor_maps_time_format" id="visitor_maps_time_format" type="text" value="<?php echo $visitor_maps_opt['time_format'];  ?>" size="10" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_time_format_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_time_format_tip">
      <?php echo esc_html( __('Time format for "Max users today" and "Last refresh time" display. Default, h:i a T (02:25 pm PST)', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_time_format_hms"><?php echo esc_html( __('Time format (Last Click)', 'visitor-maps')); ?>:</label><input name="visitor_maps_time_format_hms" id="visitor_maps_time_format_hms" type="text" value="<?php echo $visitor_maps_opt['time_format_hms'];  ?>" size="10" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_time_format_hms_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_time_format_hms_tip">
      <?php echo esc_html( __('Time format for "Entry" and "Last Click" display. Default, h:i:sa (02:25:25pm)', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_date_time_format"><?php echo esc_html( __('Date/Time format (All Time Records)', 'visitor-maps')); ?>:</label><input name="visitor_maps_date_time_format" id="visitor_maps_date_time_format" type="text" value="<?php echo $visitor_maps_opt['date_time_format'];  ?>" size="15" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_date_time_format_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_date_time_format_tip">
      <?php echo esc_html( __('Date/Time format for month, year, an all time records. Default, m-d-Y h:i a T (12-14-2008 02:25 pm PST)', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_geoip_date_format"><?php echo esc_html( __('Date/Time format (GeoLite data)', 'visitor-maps')); ?>:</label><input name="visitor_maps_geoip_date_format" id="visitor_maps_geoip_date_format" type="text" value="<?php echo $visitor_maps_opt['geoip_date_format'];  ?>" size="15" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_geoip_date_format_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_geoip_date_format_tip">
      <?php echo esc_html( __('Date/Time format for "The GeoLite data was last updated on...". Default, m-d-Y h:i a T (12-14-2008 02:25 pm PST)', 'visitor-maps')); ?>
      </div>

      </td>
    </tr>

    </table>

 </fieldset>

        <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Update Options', 'visitor-maps') ?> &raquo;" />
        </p>
</form>

<p><?php _e('More WordPress plugins by Mike Challis:', 'si-contact-form') ?></p>
<ul>
<li><a href="http://wordpress.org/extend/plugins/si-contact-form/" target="_blank"><?php echo esc_html( __('Fast and Secure Contact Form', 'visitor-maps')); ?></a></li>
<li><a href="http://wordpress.org/extend/plugins/si-captcha-for-wordpress/" target="_blank"><?php echo esc_html( __('SI CAPTCHA Anti-Spam', 'visitor-maps')); ?></a></li>
<li><a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_blank"><?php echo esc_html( __('Visitor Maps and Who\'s Online', 'visitor-maps')); ?></a></li>
</ul>
</div>
<?php
}// end function options_page


// update user activity
function visitor_maps_activity_do() {
  global $visitor_maps_opt, $wpdb, $path_visitor_maps, $current_user, $user_ID;

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

	$ip_address    = $this->get_ip_address();
    $last_page_url = $this->get_request_uri();
    $http_referer  = $this->get_http_referer();
    $user_agent    = $this->get_http_user_agent();
    $user_agent_lower = strtolower($user_agent);
    $current_time  = time();
    $xx_mins_ago   = ($current_time - absint(($visitor_maps_opt['track_time'] * 60)));

    // see if the user is a spider (bot) or not
    // based on a list of spiders in spiders.txt file
    $spider_flag = 0;
    if ($this->wo_not_null($user_agent_lower)) {
      $spiders = file($path_visitor_maps.'spiders.txt') or die('visitor-maps plugin could not open spiders.txt');
      for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
        if ($this->wo_not_null($spiders[$i])) {
          if (is_integer(strpos($user_agent_lower, trim($spiders[$i])))) {
            $spider_flag = $spiders[$i];
            break;
          }
        }
      }
    }

    // see if WP user
    get_currentuserinfo();
    $wo_user_id = 0;
    if ( $spider_flag ){
      // is a bot, the bot name is extracted from the User Agent name later on in the whos-online viewer script
      $name = $user_agent_lower;
    } else if($user_ID != '' && $current_user->user_login != ''){
      // logged in wp user
      $name = $current_user->user_login;
      $wo_user_id = $user_ID;
    } else {
      // is not a bot, must be a regular visitor
      $name = 'Guest';
    }

    if ($visitor_maps_opt['store_days'] > 0) {
            // remove visitor entries that have expired after $visitor_maps_opt['store_days'], save nickname friends
            $xx_days_ago_time = (time() - ($visitor_maps_opt['store_days'] * 60*60*24));
            $wpdb->query("DELETE from " . $wo_table_wo . "
                      WHERE (time_last_click < '" . $xx_days_ago_time . "' and nickname = '')
                      OR   (time_last_click < '" . $xx_days_ago_time . "' and nickname IS NULL)");
    } else {
            // remove visitor entries that have expired after $visitor_maps_opt['track_time'], save nickname friends
            $wpdb->query("DELETE from " . $wo_table_wo . "
                      WHERE (time_last_click < '" . $xx_mins_ago . "' and nickname = '')
                      OR   (time_last_click < '" . $xx_mins_ago . "' and nickname IS NULL)");
    }

    // see if the current site visitor has an entry
    $stored_user = $wpdb->get_row( $wpdb->prepare("
              SELECT ip_address, country_code, nickname, hostname, time_last_click, num_visits
              FROM " . $wo_table_wo . "
              WHERE session_id = %s", $ip_address) );

    if ($visitor_maps_opt['enable_location_plugin']) {
       clearstatcache();
       // make sure the location plugin is installed
       if ( !file_exists($path_visitor_maps.'include-whos-online-geoip.php') ) {
             // not going to work
             $visitor_maps_opt['enable_location_plugin'] = 0;
       }

       if ( !file_exists($path_visitor_maps.'GeoLiteCity.dat') ) {
             // give up, this way the whole site does not error
             $visitor_maps_opt['enable_location_plugin'] = 0;
       }
    }

    if ($name != '' && $ip_address != '') { // skip if empty
      if (isset($stored_user) && $stored_user->ip_address != '') {

        // have an entry, update it
        $query = "UPDATE " . $wo_table_wo . "
        SET
        user_id          = '" . $wpdb->escape($wo_user_id) . "',
        name             = '" . $wpdb->escape($name) . "',
        ip_address       = '" . $wpdb->escape($ip_address) . "',";

        // sometimes the country is blank, look it up again
        // this can happen if you just enabled the location plugin
        if ($visitor_maps_opt['enable_location_plugin'] && $stored_user->country_code == '') {
            $location_info = $this->get_location_info($ip_address);

            $query .= "country_name = '" . $wpdb->escape($location_info['country_name']) . "',
                       country_code = '" . $wpdb->escape($location_info['country_code']) . "',
                       city_name    = '" . $wpdb->escape($location_info['city_name']) . "',
                       state_name   = '" . $wpdb->escape($location_info['state_name']) . "',
                       state_code   = '" . $wpdb->escape($location_info['state_code']) . "',
                       latitude     = '" . $wpdb->escape($location_info['latitude']) . "',
                       longitude    = '" . $wpdb->escape($location_info['longitude']) . "',";
        }
        // is a nickname user coming back online? then need to re-set the time entry and online time
        if ( $stored_user->time_last_click < $xx_mins_ago ) {
            $hostname = ($visitor_maps_opt['enable_host_lookups']) ? $this->gethostbyaddr_timeout($ip_address,2) : '';
            $query .= "num_visits       = '" . $wpdb->escape($stored_user->num_visits + 1) . "',
                       time_entry       = '" . $wpdb->escape($current_time) . "',
                       time_last_click  = '" . $wpdb->escape($current_time) . "',
                       last_page_url    = '" . $wpdb->escape($last_page_url) . "',
                       http_referer     = '" . $wpdb->escape($http_referer) . "',
                       hostname         = '" . $wpdb->escape($hostname) . "',
                       user_agent       = '" . $wpdb->escape($user_agent) . "'
                       WHERE session_id = '" . $wpdb->escape($ip_address) . "'";
        } else {
            if ($visitor_maps_opt['enable_host_lookups']) {
                    $hostname = (empty($stored_user->hostname)) ? $this->gethostbyaddr_timeout($ip_address,2) : $stored_user->hostname;
            } else {
                    $hostname = '';
            }
            $query .= "time_last_click  = '" . $wpdb->escape($current_time) . "',
                       hostname         = '" . $wpdb->escape($hostname) . "',
                       last_page_url    = '" . $wpdb->escape($last_page_url) . "'
                       WHERE session_id = '" . $wpdb->escape($ip_address) . "'";
        }
          //echo 'updated';
      } else {
        // do not have an entry, insert it

        if ($visitor_maps_opt['enable_location_plugin']) {
               $location_info = $this->get_location_info($ip_address);
               $country_name = $location_info['country_name'];
               $country_code = $location_info['country_code'];
               $city_name    = $location_info['city_name'];
               $state_name   = $location_info['state_name'];
               $state_code   = $location_info['state_code'];
               $latitude     = $location_info['latitude'];
               $longitude    = $location_info['longitude'];
        } else {
               $country_name = '';
               $country_code = '';
               $city_name    = '';
               $state_name   = '';
               $state_code   = '';
               $latitude     = '0.0000';
               $longitude    = '0.0000';
        }

        $hostname = ($visitor_maps_opt['enable_host_lookups']) ? $this->gethostbyaddr_timeout($ip_address,2) : '';  

        $query = "INSERT into " . $wo_table_wo . "
        (session_id,
        ip_address,
        user_id,
        name,
        country_name,
        country_code,
        city_name,
        state_name,
        state_code,
        latitude,
        longitude,
        last_page_url,
        http_referer,
        user_agent,
        hostname,
        time_entry,
        time_last_click,
        num_visits)
        values (
                '" . $wpdb->escape($ip_address) . "',
                '" . $wpdb->escape($ip_address) . "',
                '" . $wpdb->escape($wo_user_id) . "',
                '" . $wpdb->escape($name) . "',
                '" . $wpdb->escape($country_name) . "',
                '" . $wpdb->escape($country_code) . "',
                '" . $wpdb->escape($city_name) . "',
                '" . $wpdb->escape($state_name) . "',
                '" . $wpdb->escape($state_code) . "',
                '" . $wpdb->escape($latitude) . "',
                '" . $wpdb->escape($longitude) . "',
                '" . $wpdb->escape($last_page_url) . "',
                '" . $wpdb->escape($http_referer) . "',
                '" . $wpdb->escape($user_agent) . "',
                '" . $wpdb->escape($hostname) . "',
                '" . $wpdb->escape($current_time) . "',
                '" . $wpdb->escape($current_time) . "',
                '1')";

                 //echo 'inserted';
      } // end  else  do not have an entry, insert it

       $wpdb->query("$query");
    }// end skip if empty

    // set the day, month, year, all time records and return the 'visitors online now' count
    $visitors_count = $this->set_whos_records();

    // get the day, month, year, all time records for display on web site,
    // recycle the 'visitors online now' count ( I am feeling thrifty )
    $visitor_maps_stats = $this->get_whos_records($visitors_count);


    // return the day, month, year, all time records for display on web site
    return $visitor_maps_stats;

} // end function visitor_maps_activity

function get_location_info($user_ip) {
  // this function looks up location info from the maxmind geoip database
  // and returns $country_info array
  global $path_visitor_maps;

  // lookup country info for this ip
  // geoip lookup
  if (!function_exists('geoip_open')) {
     require_once($path_visitor_maps.'include-whos-online-geoip.php');
  }
  $gi = geoip_open($path_visitor_maps.'GeoLiteCity.dat', GEOIP_STANDARD);

  $record = geoip_record_by_addr($gi, "$user_ip");
  geoip_close($gi);

  $location_info = array();    // Create Result Array

  $location_info['provider']     = '';
  $location_info['city_name']    = (isset($record->city)) ? $record->city : '';
  $location_info['state_name']   = (isset($record->country_code) && isset($record->region)) ? $GEOIP_REGION_NAME[$record->country_code][$record->region] : '';
  $location_info['state_code']   = (isset($record->region)) ? strtoupper($record->region) : '';
  $location_info['country_name'] = (isset($record->country_name)) ? $record->country_name : '--';
  $location_info['country_code'] = (isset($record->country_code)) ? strtoupper($record->country_code) : '--';
  $location_info['latitude']     = (isset($record->latitude)) ? $record->latitude : '0';
  $location_info['longitude']    = (isset($record->longitude)) ? $record->longitude : '0';

  return $location_info;
}

function set_whos_records() {
  // this function updates the day, month, year, all time records
  // and returns 'visitors online now' count
    global $visitor_maps_opt, $wpdb;

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
    $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
    $wo_table_ge = $wpdb->prefix . 'visitor_maps_ge';

  // now() adjusted to php timezone, othersize mysql date time could be off
  $mysql_now = date( 'Y-m-d H:i:s' );

  // select the 'visitors online now' count, except for our nickname friends not online now
  $visitors_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
  WHERE time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

  // set today record if day changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (day('".$mysql_now."') != day(time) and type = 'day')
     OR (count < '" . absint($visitors_count) . "' and type = 'day')");

  // set month record if month changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (month('".$mysql_now."') != month(time) and type = 'month')
     OR (count < '" . absint($visitors_count) . "' and type = 'month')");

  // set year record if year changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (year('".$mysql_now."') != year(time) and type = 'year')
     OR (count < '" . absint($visitors_count) . "' and type = 'year')");

  // set all time record if count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE count < '" . absint($visitors_count) . "'
  and type = 'all'");

  // return the 'visitors online now' count ( I recycle )
  return $visitors_count;

} // end function set_whos_records

function get_whos_records($visitors_count) {
  // get the day, month, year, all time records for display on web site,
  // use the recycled the 'visitors online now' count
  global $visitor_maps_stats, $visitor_maps_opt, $wpdb;

  $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

  $guests_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
  WHERE user_id = '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

  $members_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
  WHERE user_id > '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

  $visitor_maps_stats['visitors'] = sprintf( __('%d visitors online now','visitor-maps'),$visitors_count);
  $visitor_maps_stats['guests'] = sprintf( __('%d guests','visitor-maps'),$guests_count);
  $visitor_maps_stats['members'] = sprintf( __('%d members','visitor-maps'),$members_count);
  $string = $visitor_maps_stats['visitors'] .'<br />';
  $string .= $visitor_maps_stats['guests'].', ';
  $string .= $visitor_maps_stats['members'].'<br />';

  // fetch the day, month, year, all time records
  $visitors_arr = $wpdb->get_results("SELECT type, count, time FROM " . $wo_table_st, ARRAY_A);

  foreach( $visitors_arr as $visitors ) {
     if($visitors['type'] == 'day') {
        $visitor_maps_stats['today'] = esc_html( __('Max visitors today', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['time_format'],strtotime($visitors['time']));
        $string .= esc_html( __('Max visitors today', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['time_format'],strtotime($visitors['time'])).'<br />';
     }
     if($visitors['type'] == 'month'){
       $visitor_maps_stats['month'] = esc_html( __('This month', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['date_time_format'],strtotime($visitors['time']));
       $string .= esc_html( __('This month', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['date_time_format'],strtotime($visitors['time'])).'<br />';
     }
     if($visitors['type'] == 'year') {
       $visitor_maps_stats['year'] = esc_html( __('This year', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime($visitors['time']));
       $string .= esc_html( __('This year', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime($visitors['time'])).'<br />';
     }
     if($visitors['type'] == 'all') {
        $visitor_maps_stats['all'] = esc_html( __('All time', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime($visitors['time']));
        $string .= esc_html( __('All time', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime($visitors['time'])).'<br />';
     }
  }
  return $string;

} // end function get_whos_records

function get_visitor_maps_worldmap ($MS = 0) {
  // reads the whos-online database and makes html code to display a visitors last 15 minutes
  // thanks to pinto (www.joske-online.be) for the idea and code sample to get started
  // Mike Challis coded final version
  global $visitor_maps_opt, $wpdb, $path_visitor_maps, $url_visitor_maps;

  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

  if (!$visitor_maps_opt['enable_location_plugin']) {
     return '<p>get_visitor_maps_worldmap '.' '.__('error: geolocation data not enabled or installed','visitor-maps').'</p>';
  }

$C = $G = array();
// worldmap image names
// just image names only, do not add any paths
$C['image_worldmap']    = 'wo-worldmap-smallest.jpg';// World (smallest) do not delete this one, it is the default
$C['image_worldmap_1']  = 'wo-worldmap-smallest.jpg';// World (smallest) do not delete this one, it is the default
$C['image_worldmap_2']  = 'wo-worldmap-small.jpg';   // World (small)
$C['image_worldmap_3']  = 'wo-worldmap-medium.jpg';  // World (medium)
$C['image_worldmap_4']  = 'wo-worldmap-large.jpg';   // World (large)
$C['image_worldmap_5']  = 'wo-us-black-map.png';     // US (black)
$C['image_worldmap_6']  = 'wo-us-brown-map.png';     // US (brown)
$C['image_worldmap_7']  = 'wo-akus-black-map.png';   // Canada and US (black)
$C['image_worldmap_8']  = 'wo-akus-brown-map.png';   // Canada and US (brown)
$C['image_worldmap_9']  = 'wo-asia-black-map.png';   // Asia (black)
$C['image_worldmap_10']  = 'wo-asia-brown-map.png';   // Asia (brown)
$C['image_worldmap_11']  = 'wo-aus-nz-black-map.png'; // Australia and NZ (black)
$C['image_worldmap_12'] = 'wo-aus-nz-brown-map.png'; // Australia and NZ (brown)
$C['image_worldmap_13'] = 'wo-ceu-black-map.png';    // Europe Central (black)
$C['image_worldmap_14'] = 'wo-ceu-brown-map.png';    // Europe Central (brown)
$C['image_worldmap_15'] = 'wo-eu-black-map.png';     // Europe (black)
$C['image_worldmap_16'] = 'wo-eu-brown-map.png';     // Europe (brown)
$C['image_worldmap_17'] = 'wo-fin-black-map.png';    // Finland (black)
$C['image_worldmap_18'] = 'wo-fin-brown-map.png';    // Finland (brown)
$C['image_worldmap_19'] = 'wo-gb-black-map.png';     // Great Britain (black)
$C['image_worldmap_20'] = 'wo-gb-brown-map.png';     // Great Britain (brown)
$C['image_worldmap_21'] = 'wo-mwus-black-map.png';   // US Midwest (black)
$C['image_worldmap_22'] = 'wo-mwus-brown-map.png';   // US Midwest (brown)
$C['image_worldmap_23'] = 'wo-ncus-black-map.png';   // US Upper Midwest (black)
$C['image_worldmap_24'] = 'wo-ncus-brown-map.png';   // US Upper Midwest (brown)
$C['image_worldmap_25'] = 'wo-neus-black-map.png';   // US Northeast (black)
$C['image_worldmap_26'] = 'wo-neus-brown-map.png';   // US Northeast (brown)
$C['image_worldmap_27'] = 'wo-nwus-black-map.png';   // US Northwest (black)
$C['image_worldmap_28'] = 'wo-nwus-brown-map.png';   // US Northwest (brown)
$C['image_worldmap_29'] = 'wo-rmus-black-map.png';   // US Rocky Mountain (black)
$C['image_worldmap_30'] = 'wo-rmus-brown-map.png';   // US Rocky Mountain (brown)
$C['image_worldmap_31'] = 'wo-scus-black-map.png';   // US South (black)
$C['image_worldmap_32'] = 'wo-scus-brown-map.png';   // US South (brown)
$C['image_worldmap_33'] = 'wo-seus-black-map.png';   // US Southeast (black)
$C['image_worldmap_34'] = 'wo-seus-brown-map.png';   // US Southeast (brown)
$C['image_worldmap_35'] = 'wo-swus-black-map.png';   // US Southwest (black)
$C['image_worldmap_36'] = 'wo-swus-brown-map.png';   // US Southwest (brown)
$C['image_worldmap_37'] = 'wo-es-pt-black-map.png';   // Spain/Portugal (black)
$C['image_worldmap_38'] = 'wo-es-pt-brown-map.png';   // Spain/Portugal (brown)
// you can add more, just increment the numbers

$C['image_pin']   = 'wo-pin.jpg'; // do not delete this one, it is the default
$C['image_pin_1'] = 'wo-pin.jpg'; // do not delete this one, it is the default
$C['image_pin_2'] = 'wo-pin5x5.png';
$C['image_pin_3'] = 'wo-pin-green5x5.jpg';
// you can add more, just increment the numbers

  // set lat lon coordinates for worldmaps and custom regional maps.
  $ul_lat=0; $ul_lon=0; $lr_lat=360; $lr_lon=180; // default worldmap

  if ( isset($MS['ul_lat']) && is_numeric($MS['ul_lat'])  ) {
     $ul_lat = $MS['ul_lat'];
  }
  if ( isset($MS['ul_lon']) && is_numeric($MS['ul_lon'])  ) {
     $ul_lon = $MS['ul_lon'];
  }
  if ( isset($MS['lr_lat']) && is_numeric($MS['lr_lat'])  ) {
     $lr_lat = $MS['lr_lat'];
  }
  if ( isset($MS['lr_lon']) && is_numeric($MS['lr_lon'])  ) {
     $lr_lon = $MS['lr_lon'];
  }
  $offset_x = $offset_y = 0;
  if ( isset($MS['offset_x']) && is_numeric($MS['offset_x'])  ) {
     $offset_x = floor($MS['offset_x']);
  }
  if ( isset($MS['offset_y']) && is_numeric($MS['offset_y'])  ) {
     $offset_y = floor($MS['offset_y']);
  }
  //echo "ul_lat=$ul_lat ul_lon=$ul_lon lr_lat=$lr_lat lr_lon=$lr_lon";

  // select text on or off
  $G['text_display'] = 'off'; // default
  if ( isset($MS['text']) && $MS['text'] == 'on' ) {
    $G['text_display']  = 'on';
  }
  // select text align
  $G['text_align']  = 'cb'; // default center bottom
  if( isset($MS['textalign']) && $this->validate_text_align($MS['textalign']) ) {
    $G['text_align'] =  $MS['textalign'];
  }
  // select text color by hex code
  $G['text_color']  = '336699'; // default blue
  if( isset($MS['textcolor']) && $this->validate_color_wo($MS['textcolor']) ) {
    $G['text_color'] =  str_replace('#','',$MS['textcolor']);  // hex
  }
  // select text shadow color by hex code
  $G['text_shadow_color']  = 'FFFFFF'; // default white
  if( isset($MS['textshadow']) && $this->validate_color_wo($MS['textshadow']) ) {
    $G['text_shadow_color'] =  str_replace('#','',$MS['textshadow']);  // hex
  }
  // select pins on or off
  $G['pins_display'] = true;  // default
  if ( isset($MS['pins']) && $MS['pins'] == 'off' ) {
    $G['pins_display'] = false;
  }

  // select time units
  $G['time'] = absint($visitor_maps_opt['track_time']);
  $G['units'] = 'minutes';
  if ( isset($MS['time']) && is_numeric($MS['time']) && isset($MS['units']) ) {
      $time  = floor($MS['time']);
      $units = $MS['units'];
      $units_filtered = '';
     if ( $time > 0 && ($units == 'minute' || $units == 'minutes') ) {
           $seconds_ago = ($time * 60); // minutes
           $units_filtered = $units;
             $G['time'] = $time;
             $G['units'] = $units;
     } else if( $time > 0 && ($units == 'hour' || $units == 'hours') ) {
           $seconds_ago = ($time * 60*60); // hours
           $units_filtered = $units;
           $G['time'] = $time;
           $G['units'] = $units;
     } else if( $time > 0 && ($units == 'day' || $units == 'days') ) {
           $seconds_ago = ($time * 60*60*24); // days
           $units_filtered = $units;
           $G['time'] = $time;
           $G['units'] = $units;
     } else {
           $seconds_ago = absint($visitor_maps_opt['track_time'] * 60); // default
     }

  } else {
          $seconds_ago = absint($visitor_maps_opt['track_time'] * 60); // default
  }

  // select map image
  $image_worldmap = $url_visitor_maps .'images/' . $C['image_worldmap'];  // default
  $G['map'] = 1;
  if ( isset($MS['map']) && is_numeric($MS['map']) ) {
     $G['map'] = floor($MS['map']);
     $image_worldmap = $url_visitor_maps . 'images/' . $C['image_worldmap_'.$G['map']];
     if (!file_exists($path_visitor_maps . 'images/' . $C['image_worldmap_'.$G['map']])) {
          $image_worldmap = $url_visitor_maps . 'images/' . $C['image_worldmap'];  // default
          $G['map'] = 1;
     }
  }
  // select pin image
  $image_pin = $url_visitor_maps .'images/' . $C['image_pin'];  // default
  $G['pin'] = 1;
  if ( isset($MS['pin']) && is_numeric($MS['pin']) ) {
     $G['pin'] = floor($MS['pin']);
     $image_pin = $url_visitor_maps . 'images/'. $C['image_pin_'.$G['pin']];
     if (!file_exists($path_visitor_maps .'images/' . $C['image_pin_'.$G['pin']])) {
          $image_pin = $url_visitor_maps .'images/' . $C['image_pin'];  // default
          $G['pin'] = 1;
     }
  }
  // select the map image type
  if ( isset($MS['type']) && $MS['type'] == 'jpg' ) {
        $type = 'jpg';
  } else if( isset($MS['type']) && $MS['type'] == 'png' ) {
        $type = 'png';
  } else {
        $type = 'png';
  }

  $xx_secs_ago = (time() - $seconds_ago);
  // get image data
  list($image_worldmap_width, $image_worldmap_height, $image_worldmap_type) = getimagesize($image_worldmap);
  list($image_pin_width, $image_pin_height, $image_pin_type) = getimagesize($image_pin);

  // map parameters
  $scale = 360 / $image_worldmap_width;

  $image_worldmap_link = '';
  if ( is_array($MS) ) {
    $image_worldmap = get_bloginfo('url') .
    '?do_wo_map=1'.
    '&amp;time='.$G['time'].
    '&amp;units='.$G['units'].
    '&amp;map='.$G['map'].
    '&amp;pin='.$G['pin'].
    '&amp;pins=off'.
    '&amp;text='.$G['text_display'].
    '&amp;textcolor='.$G['text_color'].
    '&amp;textshadow='.$G['text_shadow_color'].
    '&amp;textalign='.$G['text_align'].
    '&amp;ul_lat='.$ul_lat.
    '&amp;ul_lon='.$ul_lon.
    '&amp;lr_lat='.$lr_lat.
    '&amp;lr_lon='.$lr_lon.
    '&amp;offset_x='.$offset_x.
    '&amp;offset_y='.$offset_y.
    '&amp;type='.$type;

    $image_worldmap_link = get_bloginfo('url') .
    '?do_wo_map=1'.
    '&time='.$G['time'].
    '&units='.$G['units'].
    '&map='.$G['map'].
    '&pin='.$G['pin'].
    '&pins=on'.
    '&text='.$G['text_display'].
    '&textcolor='.$G['text_color'].
    '&textshadow='.$G['text_shadow_color'].
    '&textalign='.$G['text_align'].
    '&ul_lat='.$ul_lat.
    '&ul_lon='.$ul_lon.
    '&lr_lat='.$lr_lat.
    '&lr_lon='.$lr_lon.
    '&offset_x='.$offset_x.
    '&offset_y='.$offset_y.
    '&type='.$type;

$image_worldmap_array = '$map_settings = array(
// html map settings
// set these settings as needed
\'time\'       => \''.$G['time'].'\',  // digits of time
\'units\'      => \''.$G['units'].'\', // minutes, hours, or days (with or without the "s")
\'map\'        => \''.$G['map'].'\',       // 1,2,3 etc. (you can add more map images in settings)
\'pin\'        => \''.$G['pin'].'\',       // 1,2,3 etc. (you can add more pin images in settings)
\'pins\'       => \'off\',     // off (off is required for html map)
\'text\'       => \''.$G['text_display'].'\',      // on or off
\'textcolor\'  => \''.$G['text_color'].'\',  // any hex color code
\'textshadow\' => \''.$G['text_shadow_color'].'\',  // any hex color code
\'textalign\'  => \''.$G['text_align'].'\',      // ll, ul, lr, ur, c, ct, cb (codes for: lower left, upper left, upper right, center, center top, center bottom)
\'ul_lat\'     => \''.$ul_lat.'\',     // default 0 for worldmap
\'ul_lon\'     => \''.$ul_lon.'\',     // default 0 for worldmap
\'lr_lat\'     => \''.$lr_lat.'\',     // default 360 for worldmap
\'lr_lon\'     => \''.$lr_lon.'\',     // default 180 for worldmap
\'offset_x\'   => \''.$offset_x.'\',   // + or - offset for x axis  - moves pins left, + moves pins right
\'offset_y\'   => \''.$offset_y.'\',   // + or - offset for y axis  - moves pins up,   + moves pins down
\'type\'       => \''.$type.'\',     // jpg or png (map output type)
);
echo get_visitor_maps_worldmap($map_settings);';
  }

  // show worldmap
$string = '<!--
This link is for your reference, it can be pasted into a browser:
'.$image_worldmap_link.'

'.$image_worldmap_array.'
-->
';

// HTML maps automatically printed inside tables?
// (this is workaround for an IE problem. The map will be wrapped in a html table)
$maps_in_tables = 1;

$maps_in_tables and $string .= '<table class="wo_map" border="0" cellpadding="0" cellspacing="0">
 <tr>
   <td>
';

$string .= '<div style="position:relative; border:none; background-image:url('.$image_worldmap.'); width:'.$image_worldmap_width.'px; height:'.$image_worldmap_height.'px;">';
$string .= "\n".'<!--[if lte IE 8 ]>
<div style="position:relative; margin-top: -11px;">
<![endif]-->';
  $string .= "\n";

  if ($visitor_maps_opt['show_bots_on_worldmap']) {
       // all visitors
       $rows_arr = $wpdb->get_results("
                 SELECT nickname, country_name, country_code, city_name, state_name, state_code, latitude, longitude
                 FROM ".$wo_table_wo."
                 WHERE time_last_click > '" . absint($xx_secs_ago) . "'",ARRAY_A );
  } else {
       // guests and members, no bots
       $rows_arr = $wpdb->get_results("
                 SELECT nickname, country_name, country_code, city_name, state_name, state_code, latitude, longitude
                 FROM ".$wo_table_wo."
                 WHERE (name = 'Guest' AND time_last_click > '" . absint($xx_secs_ago) . "')
                 OR (name != 'Guest' AND user_id > 0 AND time_last_click > '" . absint($xx_secs_ago) . "')",ARRAY_A );
  }

  // create pin on the map
  $count = 0;
  foreach($rows_arr as $row) {
    if ($row['longitude'] != '0.0000' && $row['latitude'] != '0.0000') {
      if ($ul_lat == 0) { // must be the world map
            $count++;
			$x = floor ( ( $row['longitude'] + 180 ) / $scale );
			$y = floor ( ( 180 - ( $row['latitude'] + 90 ) ) / $scale );
	  } else {      // its a custom map
           // filter out what we do not want
           if ( ($row['latitude'] > $lr_lat && $row['latitude'] < $ul_lat) &&
                ($row['longitude'] < $lr_lon && $row['longitude'] > $ul_lon) ) {
            $count++;
            $x = floor ($image_worldmap_width * ($row['longitude'] - $ul_lon) / ($lr_lon - $ul_lon)+ $offset_x);
            $y = floor ($image_worldmap_height * ($row['latitude'] - $ul_lat) / ($lr_lat - $ul_lat)+ $offset_y);

            // discard pixels that are outside the image because of offsets
            if ( ($x < 0 || $x > $image_worldmap_width ) || ($y < 0 || $y > $image_worldmap_height) ) {
               $count--;
               continue;
            }
          } else {
                  continue;
          }
      }
	  $title = '';
      if ( $visitor_maps_opt['enable_state_display'] ) {
              if ($row['city_name'] != '') {
                if ($row['country_name'] == 'United States') {
                     $title = $this->wo_sanitize_output($row['city_name']);
                     if ($row['state_code'] != '')
                             $title = $this->wo_sanitize_output($row['city_name']) . ', ' . $this->wo_sanitize_output(strtoupper($row['state_code']));
                }
                else {      // all non us countries
                     $title = $this->wo_sanitize_output($row['city_name']) . ', ' . $this->wo_sanitize_output(strtoupper($row['country_code']));
                }
             }
             else {
                  $title = '~ ' . $row['country_name'];
             }
      } else {
             $title = $row['country_name'];
      }

      $string .= '<div style="cursor:pointer;position:absolute; top:'.$y.'px; left:'.$x.'px;">
      <img src="'.$image_pin.'" style="border:0; margin:0; padding:0;" width="'.$image_pin_width.'" height="'.$image_pin_height.'" alt="" title="'.$this->wo_sanitize_output($title).'" />
      </div>';
      $string .= "\n";
    }
  } // end foreach
  $string .= '<!--[if lte IE 8 ]>
</div>
<![endif]-->';
  $string .= "\n";
  $string .= '</div>
';
$maps_in_tables and $string .= '</td>
 </tr>
</table>
';

  return $string;
} // end function get_visitor_maps_worldmap

function findYcoord($myLat, $lr_lat, $mapHeight, $rfactor) {
      //$mapHeight = 396;
      //$rfactor = 290; // map scale
      $radBtm = deg2rad($lr_lat);
      $radPixel = deg2rad($myLat);
      $sinRadBtm = sin($radBtm);
      $sinRadPixel = sin($radPixel);
      $convHtBtm = $rfactor * log((1 + $sinRadPixel)/(1 - $sinRadPixel));
      $convHtPixel = $rfactor * log((1 + $sinRadBtm)/(1 - $sinRadBtm));
      $myTotHt = abs($convHtPixel - $convHtBtm);
      $myYcoord = round($mapHeight - $myTotHt, 3);
      return $myYcoord;
}

function get_request_uri() {
  // used for the $last_page_url
  if (isset($_SERVER['REQUEST_URI'])) {
      $uri = $_SERVER['REQUEST_URI'];
  } else {
    if (isset($_SERVER['argv'])) {
      $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
    } else {
      $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
    }
  }
  return $uri;
}

function get_ip_address() {
   // determine the visitors ip address
   if (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
   } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
   } else {
        $ip = 'unknown';
   }
   return $ip;
} // end function get_ip_address

function get_http_user_agent() {
   // determine the visitors user agent (browser)
   if (getenv('HTTP_USER_AGENT')) {
        $agent = getenv('HTTP_USER_AGENT');
   } else if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $agent = $_SERVER['HTTP_USER_AGENT'];
   } else {
        $agent = 'unknown';
   }
   return $agent;
}

function get_http_referer() {
   // determine the visitors http referer (url they clicked on to get to your site)
   if (getenv('HTTP_REFERER')) {
        $referer = getenv('HTTP_REFERER');
   } else if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
   } else {
        // sometimes it is just empty
        $referer = '';
   }
   return $referer;
}

function validate_color_wo($string) {
 // protect form input color fields from hackers and check for valid css color code hex
 // only allow simple 6 char hex codes with or without # like this 336699 or #336699

 if (preg_match("/^#[a-f0-9]{6}$/i", trim($string))) {
    return true;
 }
 if (preg_match("/^[a-f0-9]{6}$/i", trim($string))) {
    return true;
 }
 return false;
} // end function validate_color_wo

function validate_text_align($string) {
 // only allow proper text align codes
  $allowed = array('ll','ul','lr','ur','c','ct','cb');
 if ( in_array($string, $allowed) ) {
    return true;
 }
 return false;
} // end function validate_text_align

function host_to_domain($host) {
    if ($host == 'n/a' || !preg_match("/.*\.[a-zA-Z]{2,3}/", $host))  return $host;
    $isp = array_reverse(explode('.', $host));
    $domain = $isp[1].'.'.$isp[0];
    $slds = array(
'\.com\.au',
'\.net\.au',
'\.org\.au',
'\.on\.net',
'\.ac\.uk',
'\.co\.uk',
'\.gov\.uk',
'\.ltd\.uk',
'\.me\.uk',
'\.mod\.uk',
'\.net\.uk',
'\.nic\.uk',
'\.nhs\.uk',
'\.org\.uk',
'\.plc\.uk',
'\.police\.uk',
'\.sch\.uk',);
   foreach ($slds as $k) {
      if(preg_match("/$k$/i", $host)){
        $domain = $isp[2].'.'.$isp[1].'.'.$isp[0];
        break;
      }
   }
    return (preg_match("/[0-9]{1,3}\.[0-9]{1,3}/", $domain)) ? 'n/a' : $domain;
} // end function host_to_domain

function gethostbyaddr_timeout ($ip,$timeout_secs = 2) {
 if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    //echo 'This is a server using Windows';
    return $this->gethost_win($ip,$timeout_secs);
 } else {
    //echo 'This is a server not using Windows';
    return $this->gethost_lin($ip,$timeout_secs);
 }
} // end function gethostbyaddr_timeout

function gethost_lin ($ip,$timeout_secs = 2) {
 // linux gethostbyaddr with timeout by mike challis
 $time_start = microtime(true); // set a timer
 @exec('host -W '.escapeshellarg($timeout_secs).' '.escapeshellarg($ip), $output); // plan a
 $time_end = microtime(true);  // check the timer
 if(($time_end - $time_start) > $timeout_secs) return 'n/a'; // bail because it timed out
 if (empty($output)) return gethostbyaddr($ip); // plan b, but without timeout
 $host = (($output[0] ? end ( explode (' ', $output[0])) : $ip)); // plan a continues
 $host = rtrim($host, "\n");
 $host = rtrim($host, '.');
 return (preg_match("/.*\.[a-zA-Z]{2,3}/", $host)) ? $host : 'n/a';
} // end function gethost_lin

function gethost_win ($ip,$timeout_secs = 2) {
 // win32 gethostbyaddr with timeout by mike challis
 $time_start = microtime(true); // set a timer
 @exec('nslookup -timeout='.escapeshellarg($timeout_secs).' '.escapeshellarg($ip), $output); // plan a
 $time_end = microtime(true);  // check the timer
 if(($time_end - $time_start) > $timeout_secs) return 'n/a'; // bail because it timed out
 if (empty($output)) return gethostbyaddr($ip); // plan b, but without timeout
 foreach($output as $line) { // plan a continues
  if(preg_match("/^Name:\s+(.*)$/", $line,$parts)) {
   $host = trim( (isset($parts[1])) ? $parts[1] : '' );
   return (preg_match("/.*\.[a-zA-Z]{2,3}/", $host)) ? $host : 'n/a';
  }
 }
 return 'n/a';
} // end function gethost_win

// check for empty variable, empty if null, empty if 0, empty if ''
function wo_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
}

// functions for protecting and validating form input vars
function wo_clean_input($string) {
    if (is_string($string)) {
      return trim($this->wo_sanitize_string(strip_tags($this->wo_stripslashes($string))));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = $this->wo_clean_input($value);
      }
      return $string;
    } else {
      return $string;
    }
}

function wo_sanitize_string($string) {
    $string = preg_replace("/ +/", ' ', trim($string));
    return preg_replace("/[<>]/", '_', $string);
}

function wo_stripslashes($string) {
        if (get_magic_quotes_gpc()) {
                return stripslashes($string);
        } else {
                return $string;
        }
}

function wo_output_string($string) {
    return str_replace('"', '&quot;', $string);
}

function wo_db_sanitize_input($input) {
    // Parse array
    if (is_array($input)) {
      foreach ($input as $key => $var)
        $input[$key] = $this->wo_db_sanitize_input($var);

      // Parse string
    }
    else {
      // Check if already escaped
      if (get_magic_quotes_gpc()) {
        // Remove not needed escapes
        $input = stripslashes($input);
      }
      // Use proper escape
      $input = mysql_real_escape_string(trim($input));
    }

    // Return sanitized string
    return $input;
} // end function db_sanitize_input

function wo_sanitize_output($output) {
    // Return sanitized string
    return htmlspecialchars($output);
} // end function wo_sanitize_output

function visitor_maps_add_dashboard_widget() {
	wp_add_dashboard_widget('visitor_maps_dashboard_widget', __('Visitor Maps', 'visitor-maps') .' - '.__('Who\'s Online', 'visitor-maps') , array(&$this,'visitor_maps_dashboard_widget'));
}
function visitor_maps_dashboard_widget() {
    global $visitor_maps_stats, $visitor_maps_opt;

    echo "<p>$visitor_maps_stats</p>";
    if ($visitor_maps_opt['enable_credit_link']) {
      echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
    }

}
function visitor_maps_register_widget() {
	register_sidebar_widget( __('Who\'s Online', 'visitor-maps'), array(&$this,'visitor_maps_widget'));
}
function visitor_maps_widget($args) {
    global $visitor_maps_opt, $wpdb;
    extract($args);

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

    $visitors_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $guests_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE user_id = '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $members_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE user_id > '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $stats_visitors = sprintf( __('%d visitors online now','visitor-maps'),$visitors_count);
    $stats_guests   = sprintf( __('%d guests','visitor-maps'),$guests_count);
    $stats_members  = sprintf( __('%d members','visitor-maps'),$members_count);

    echo $before_widget . $before_title . __('Who\'s Online','visitor-maps') .$after_title;
    echo "<p>$stats_visitors<br />$stats_guests, $stats_members</p>";
    if ($visitor_maps_opt['enable_credit_link']) {
      echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
    }
    echo $after_widget;
} // end function visitor_maps_widget

function visitor_maps_manual_sidebar() {
    global $visitor_maps_opt, $wpdb;

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

    $visitors_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $guests_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE user_id = '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $members_count = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE user_id > '0' and time_last_click > '" . (time() - absint(($visitor_maps_opt['track_time'] * 60))) . "'");

    $stats_visitors = sprintf( __('%d visitors online now','visitor-maps'),$visitors_count);
    $stats_guests   = sprintf( __('%d guests','visitor-maps'),$guests_count);
    $stats_members  = sprintf( __('%d members','visitor-maps'),$members_count);

    echo '<h2>'. __('Who\'s Online','visitor-maps') .'</h2>';
    echo "<p>$stats_visitors<br />$stats_guests, $stats_members</p>";
    if ($visitor_maps_opt['enable_credit_link']) {
      echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
    }
} // end visitor_maps_manual_sidebar

function visitor_maps_upgrader_backup() {
    // prevent plugin updater from deleting the GeoLiteCity.dat file
    $from = dirname(__FILE__).'/GeoLiteCity.dat';
    $to = WP_CONTENT_DIR .'/visitor-maps-backup';
    if (is_file($from)) {
        if (!is_dir($to)) mkdir($to);
        if (is_dir($to))  copy($from, $to.'/GeoLiteCity.dat');
    }

} // end function visitor_maps_upgrader_backup

function visitor_maps_upgrader_restore() {
    // prevent plugin updater from deleting the GeoLiteCity.dat file
    $to = dirname(__FILE__).'/GeoLiteCity.dat';
    $from = WP_CONTENT_DIR .'/visitor-maps-backup';
    if (is_file($from.'/GeoLiteCity.dat')) {
        copy($from.'/GeoLiteCity.dat', $to);
        chmod($to, 0644);
        unlink($from.'/GeoLiteCity.dat');
	    rmdir($from);
    }

} // end function visitor_maps_upgrader_restore



} // end of class
} // end of if class

// Pre-2.8 compatibility
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return wp_specialchars( $text );
	}
}

// Pre-2.8 compatibility
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return attribute_escape( $text );
	}
}

if (class_exists("VisitorMaps")) {
 $visitor_maps = new VisitorMaps();
}

if (isset($visitor_maps)) {

  $url_visitor_maps  = WP_PLUGIN_URL . '/visitor-maps/';
  $path_visitor_maps = WP_PLUGIN_DIR . '/visitor-maps/';

  // visitor_maps init plugin
  add_action('init', array(&$visitor_maps, 'visitor_maps_init'));

  // get the options now
  $visitor_maps->visitor_maps_get_options();

  add_action('plugins_loaded', array(&$visitor_maps,'visitor_maps_register_widget'));

  add_action('wp_dashboard_setup', array(&$visitor_maps,'visitor_maps_add_dashboard_widget'));

  // remind admin to install the GeoLite database
  if (
     (isset($_POST['visitor_maps_enable_location_plugin']) && !is_file(dirname(__FILE__) .'/GeoLiteCity.dat') )
   ||
     (!isset($_POST['visitor_maps_set']) && !isset($_GET['do_geo']) && $visitor_maps_opt['enable_location_plugin'] && !is_file(dirname(__FILE__) .'/GeoLiteCity.dat'))
   ) {

      add_action( 'admin_notices', array(&$visitor_maps,'visitor_maps_activation_notice'),1);
  }

  // admin options
  add_action('admin_menu', array(&$visitor_maps,'visitor_maps_add_tabs'),1);

  // adds "Settings" link to the plugin action page
  add_filter('plugin_action_links', array(&$visitor_maps,'visitor_maps_plugin_action_links'),10,2);

  // process user actvity during header hooks
  add_action('wp_head', array(&$visitor_maps,'visitor_maps_activity'),1);
  add_action('admin_head', array(&$visitor_maps,'visitor_maps_activity'),1);

  // call print stats in public  footer
  add_action('wp_footer', array(&$visitor_maps,'visitor_maps_public_footer_stats'),1);

  // call print stats in admin footer
  add_action('admin_footer', array(&$visitor_maps,'visitor_maps_admin_footer_stats'),1);

  // add map link javascript header hooks
  add_action('wp_head', array(&$visitor_maps,'visitor_maps_public_header'),2);

  // use shortcode ina page for the visitor maps feature
  add_shortcode('visitor-maps', array(&$visitor_maps,'visitor_maps_map_short_code'),1);

  // header for the admin whos online view page
  add_action('admin_head', array(&$visitor_maps,'visitor_maps_admin_view_header'),1);

  // this is for displaying the map display console.
  add_action('parse_request', array(&$visitor_maps,'visitor_maps_do_map_console'),1);

  // this is for displaying the map images.
  add_action('parse_request', array(&$visitor_maps,'visitor_maps_do_map_image'),2);

  register_activation_hook(__FILE__, array(&$visitor_maps, 'visitor_maps_install'), 1);

  // prevent plugin updater from deleting the GeoLiteCity.dat file
  add_filter('upgrader_pre_install', array(&$visitor_maps, 'visitor_maps_upgrader_backup'), 10, 2);
  add_filter('upgrader_post_install', array(&$visitor_maps, 'visitor_maps_upgrader_restore'), 10, 2);

  // options deleted when this plugin is deleted in WP 2.7+
  if ( function_exists('register_uninstall_hook') )
     register_uninstall_hook(__FILE__, array(&$visitor_maps, 'visitor_maps_unset_options'), 1);
}

?>