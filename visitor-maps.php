<?php
/*
Plugin Name: Visitor Maps and Who's Online
Plugin URI: http://www.642weather.com/weather/scripts-wordpress-visitor-maps.php
Description: Displays Visitor Maps with location pins, city, and country. Includes a Who's Online Sidebar to show how many users are online. Includes a Who's Online admin dashboard to view visitor details. The visitor details include: what page the visitor is on, IP address, host lookup, online time, city, state, country, geolocation maps and more. No API key needed.  <a href="plugins.php?page=visitor-maps/visitor-maps.php">Settings</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V3BPEZ9WGYEYG">Donate</a>
Version: 1.5.8.7
Author: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
*/
/*  Copyright (C) 2008-2015 Mike Challis  (http://www.642weather.com/weather/contact_us.php)

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

// settings get deleted when plugin is deleted from admin plugins page
// this must be outside the class or it does not work
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
  delete_option('visitor_maps_upgrade_1');

} // end function visitor_maps_unset_options



if (!class_exists('VisitorMaps')) {

 class VisitorMaps {
     var $visitor_maps_error;
     var $visitor_maps_add_script;

 // upgrade path from version 1.4.2 or older
function visitor_maps_upgrade_1() {
  global $wpdb, $wp_version;
  if (!get_option('visitor_maps_upgrade_1')) {
    // just now updating, run upgrade patch
    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
    $wpdb->query("ALTER TABLE `". $wo_table_wo . "`
    ADD INDEX nickname_time_last_click (`nickname`, `time_last_click`)");
    add_option('visitor_maps_upgrade_1',  array( 'upgraded' => 'true' ), '', 'yes');
  }
} // end function visitor_maps_upgrade_1

function visitor_maps_add_tabs() {
    global $visitor_maps_opt;

    add_submenu_page('plugins.php', __('Visitor Maps Options', 'visitor-maps'), __('Visitor Maps Options', 'visitor-maps'), 'manage_options', __FILE__,array(&$this,'visitor_maps_options_page'));
    add_submenu_page('index.php', __('Who\'s Online', 'visitor-maps'), __('Who\'s Online', 'visitor-maps'), $visitor_maps_opt['dashboard_permissions'], 'visitor-maps',array(&$this,'visitor_maps_admin_view'));
    add_submenu_page('index.php', __('Who\'s Been Online', 'visitor-maps'), __('Who\'s Been Online', 'visitor-maps'), $visitor_maps_opt['dashboard_permissions'], 'whos-been-online',array(&$this,'visitor_maps_whos_been_online'));
}

function visitor_maps_perm_dropdown($select_name, $checked_value='') {
        // choices: Display text => role
        $choices = array (
                 __('Administrators', 'visitor-maps') => 'manage_options',
                 __('Editors', 'visitor-maps') => 'moderate_comments',
                 __('Authors', 'visitor-maps') => 'edit_publish_posts',
                 __('Contributors', 'visitor-maps') => 'edit_posts',
                 );
        // print the <select> and loop through <options>
        echo '<select name="' . $select_name . '" id="' . $select_name . '">' . "\n";
        foreach ($choices as $text => $capability) :
                if ($capability == $checked_value) $checked = ' selected="selected" ';
                echo "\t". '<option value="' . $capability . '"' . $checked . ">$text</option> \n";
                $checked = '';
        endforeach;
        echo "\t</select>\n";
 } // end function visitor_maps_perm_dropdown

function visitor_maps_whos_been_online(){
     global $visitor_maps_opt;

     if ( function_exists('current_user_can') && !current_user_can($visitor_maps_opt['dashboard_permissions']) )
         die(__('You do not have permissions for managing this option', 'visitor-maps'));

    // show admin Who's Been Online page
    echo '<div class="wrap">
    <h2>'.__('Visitor Maps', 'visitor-maps').' - '.__('Who\'s Been Online', 'visitor-maps').'</h2>';
    require_once(dirname(__FILE__) .'/class-wo-been.php');
    $wo_view = new WoBeen();
    $wo_view->view_whos_been_online();

    if ($visitor_maps_opt['enable_location_plugin'] && $visitor_maps_opt['enable_dash_map'] ) {
     echo '<br /><br />';
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
    if (!$visitor_maps_opt['hide_console'] || ($visitor_maps_opt['hide_console'] && current_user_can('manage_options')) ) {
      echo '<p>'.sprintf( __('View more maps in the <a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;').'</p>';
    }
  }
  if ($visitor_maps_opt['enable_credit_link']) {
    echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
  }
  echo '</div>';

} // end function visitor_maps_whos_been_online

function visitor_maps_admin_view(){
     global $visitor_maps_opt;

     if ( function_exists('current_user_can') && !current_user_can($visitor_maps_opt['dashboard_permissions']) )
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

   if (!$visitor_maps_opt['hide_console'] || ($visitor_maps_opt['hide_console'] && current_user_can('manage_options')) ) {
     echo '<p>'.sprintf( __('View more maps in the <a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;').'</p>';
   }
 }
  if ($visitor_maps_opt['enable_credit_link']) {
    echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
  }
  echo '</div>';

} // end function visitor_maps_view

// outputs the map console page from a $_GET method
function visitor_maps_do_map_console() {
  global $visitor_maps_opt, $visitor_maps_stats;


  if( isset($_GET['wo_map_console']) ) {
     if ($visitor_maps_opt['hide_console'] && !current_user_can('manage_options') ) {
       return;
     }
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
   global $visitor_maps_opt, $wpdb, $visitor_maps_add_script;

   $visitor_maps_add_script = true;
   $string = '';

   if ($visitor_maps_opt['enable_location_plugin'] && $visitor_maps_opt['enable_page_map']) {
     // show the map on View Who's Online page
     if ( $visitor_maps_opt['enable_visitor_map_hover'] || $visitor_maps_opt['hide_text_on_worldmap'] ) {
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
         $string .= $this->get_visitor_maps_worldmap($map_settings);

     } else {
        // had to disable the dynamic map and replace with this because some WP themes were messing up the pin locations
        $string .= '<img alt="'.__('Visitor Maps', 'visitor-maps').'" src="'.get_bloginfo('url').'?do_wo_map=1&amp;time='.$visitor_maps_opt['default_map_time'].'&amp;units='.$visitor_maps_opt['default_map_units'].'&amp;map='.$visitor_maps_opt['default_map'].'&amp;pin=1&amp;pins=on&amp;text=on&amp;textcolor=000000&amp;textshadow=FFFFFF&amp;textalign=cb&amp;ul_lat=0&amp;ul_lon=0&amp;lr_lat=360&amp;lr_lon=180&amp;offset_x=0&amp;offset_y=0&amp;type=png&amp;wp-minify-off=1" />';
     }
     if (!$visitor_maps_opt['hide_console'] || ($visitor_maps_opt['hide_console'] && current_user_can('manage_options')) ) {
       $string .= '<p>'.__('View more maps in the ', 'visitor-maps').'<a href="'.get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;">'.__('Visitor Map Viewer', 'visitor-maps').'</a></p>';
     }
   }
   if ($visitor_maps_opt['enable_records_page']) {
     $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
     // fetch the day, month, year, all time records
     $visitors_arr = $wpdb->get_results("SELECT type, count, time FROM " . $wo_table_st, ARRAY_A);

     foreach( $visitors_arr as $visitors ) {
        if($visitors['type'] == 'day')
           $day = esc_html( __('Max visitors today', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['time_format'],strtotime(current_time($visitors['time'])));
        if($visitors['type'] == 'month')
           $month = esc_html( __('This month', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
        if($visitors['type'] == 'year')
           $year = esc_html( __('This year', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
        if($visitors['type'] == 'all')
           $all = esc_html( __('All time', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
     }

     $string .= '<p>'.__('Records of the most visitors online at once:', 'visitor-maps');
     $string .= "<br />$day";
     $string .= "<br />$month";
     $string .= "<br />$year";
     $string .= "<br />$all";
     $string .= '</p>';
   }


     if ($visitor_maps_opt['enable_credit_link']) {
          $string .= '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
     }
  //else {
  //  $string .= '<p>'.__('Visitor Maps geolocation is disabled in settings.', 'visitor-maps').'</p>';
 // }

  return $string;

} // end function visitor_maps_map_short_code

// header code for the public visitor-maps page
function visitor_maps_add_script() {
  global $visitor_maps_opt, $visitor_maps_add_script;
  // only load this javascript on the blog pages where the visitor-map shortcode is

  //if (!$visitor_maps_add_script)   // forgot about sidebar widget
  //    return;
?>
<!-- begin visitor maps  -->
<script type="text/javascript">
//<![CDATA[
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//]]>
</script>
<!-- end visitor maps -->
<?php
} // end function visitor_maps_public_header


// header code for the admin view whos online page
function visitor_maps_admin_view_header() {
  global $visitor_maps_opt;

// only load this header stuff on the whos online view page
if(isset($_GET['page']) && $_GET['page'] == 'visitor-maps' ) {

   // defaults
   $wo_prefs_arr_def = array (
     'bots' => '0',
     'refresh' => 'none',
     'show' => 'none',
   );

   if ( ( !$wo_prefs_arr = get_option( 'visitor_maps_wop' ) ) || !is_array($wo_prefs_arr) ) {
     // install the option defaults
     update_option('visitor_maps_wop', $wo_prefs_arr_def);
     $wo_prefs_arr = $wo_prefs_arr_def;
   }

   $bots = (isset($wo_prefs_arr['bots'])) ? $wo_prefs_arr['bots'] : '0';
   if ( isset($_GET['bots']) && in_array($_GET['bots'], array('0','1')) ) {
     // bots
      $wo_prefs_arr['bots'] = $_GET['bots'];
      $bots = $_GET['bots'];
   }
   $refresh = (isset($wo_prefs_arr['refresh'])) ? $wo_prefs_arr['refresh'] : 'none';
   if ( isset($_GET['refresh']) && in_array($_GET['refresh'], array('none','30','60','120','300','600')) ) {
     // refresh
      $wo_prefs_arr['refresh'] = $_GET['refresh'];
      $refresh = $_GET['refresh'];
   }
   $show = (isset($wo_prefs_arr['show'])) ? $wo_prefs_arr['show'] : 'none';
   if ( isset($_GET['show']) && in_array($_GET['show'], array('none','all','bots','guests')) ) {
     // show
     $wo_prefs_arr['show'] = $_GET['show'];
     $show = $_GET['show'];
   }

   // save settings
   update_option('visitor_maps_wop', $wo_prefs_arr);

   echo '<!-- begin visitor maps - whos online page header code -->'."\n";
   if ( isset($wo_prefs_arr['refresh']) && in_array($wo_prefs_arr['refresh'], array('30','60','120','300','600')) ) {
         $query = '&amp;refresh='. $wo_prefs_arr['refresh'];
         if ( isset($wo_prefs_arr['show']) && in_array($wo_prefs_arr['show'], array('all','bots','guests')) ) {
               $query .= '&amp;show='. $wo_prefs_arr['show'];
         }
         if ( isset($wo_prefs_arr['bots']) && in_array($wo_prefs_arr['bots'], array('0','1')) ) {
              $query .= '&amp;bots='. $wo_prefs_arr['bots'];
         }
         echo '<meta http-equiv="refresh" content="' . $wo_prefs_arr['refresh'] . ';URL=' . admin_url( 'index.php?page=visitor-maps' ) . $query . '" />
          ';
  }


  // save settings
  //update_option('visitor_maps_wop', $wo_prefs_arr);
?>
<script type="text/javascript">
//<![CDATA[
function who_is(url) {
  window.open(url,"who_is_lookup","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//]]>
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
if(isset($_GET['page']) && is_string($_GET['page']) && preg_match('/visitor-maps.php$/',$_GET['page']) ) {
?>
<!-- begin visitor maps - settings page header code -->
<script type="text/javascript">
<!--
function wo_map_console(url) {
  window.open(url,"wo_map_console","height=650,width=800,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//-->
</script>
<style type="text/css">
div.vm-star-holder { position: relative; height:19px; width:100px; font-size:19px;}
div.vm-star {height: 100%; position:absolute; top:0px; left:0px; background-color: transparent; letter-spacing:1ex; border:none;}
.vm-star1 {width:20%;} .vm-star2 {width:40%;} .vm-star3 {width:60%;} .vm-star4 {width:80%;} .vm-star5 {width:100%;}
.vm-star.vm-star-rating {background-color: #fc0;}
.vm-star img{display:block; position:absolute; right:0px; border:none; text-decoration:none;}
div.vm-star img {width:19px; height:19px; border-left:1px solid #fff; border-right:1px solid #fff;}
div.star img {width:19px; height:19px; border-left:1px solid #fff; border-right:1px solid #fff;}
</style>
<!-- end visitor maps - settings page header code -->
<?php
  } // end if(isset($_GET['page'])


// only load this header stuff on the whos online settings page
if(isset($_GET['page']) && $_GET['page'] == 'whos-been-online' ) {
?>
<!-- begin visitor maps - whos been online page header code -->
<script type="text/javascript">
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
<!-- end visitor maps - whos been online page header code -->
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

  if ($visitor_maps_opt['enable_admin_footer'] && ( function_exists('current_user_can') && current_user_can($visitor_maps_opt['dashboard_permissions']) ) ) {
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
  echo '<div id="message" class="error"><p><strong>'.__('Visitor Maps plugin needs the Maxmind GeoLiteCity database installed.', 'visitor-maps').' <a href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Install Now', 'visitor-maps'). '</a></strong></p></div>';
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
        PRIMARY KEY  (`session_id`),
        KEY `nickname_time_last_click` (`nickname`,`time_last_click`))");
	}
    $now = current_time( 'mysql' );
    if($wpdb->get_var("show tables like '". $wo_table_st . "'") != $wo_table_st) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wo_table_st . "` (
        `type`  varchar(14) NOT NULL default '',
        `count` mediumint(8) NOT NULL default '0',
        `time`  datetime NOT NULL default '0000-00-00 00:00:00',
         PRIMARY KEY  (`type`))");

       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('day', '1', `". $now . "`)");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('month', '1', `". $now . "`)");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('year', '1', `". $now . "`)");
       $wpdb->query("INSERT INTO `". $wo_table_st . "` (`type` ,`count` ,`time`) VALUES ('all', '1', `". $now . "`)");
	}

    if($wpdb->get_var("show tables like '". $wo_table_ge . "'") != $wo_table_ge) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wo_table_ge . "` (
         `time_last_check` int(10) unsigned NOT NULL default '0',
         `needs_update` tinyint(1) unsigned NOT NULL default '0')");
	}

    // add this so the upgrade patch will not be triggered on a fresh install
    add_option('visitor_maps_upgrade_1',  array( 'upgraded' => 'true' ), '', 'yes');

} // end function visitor_maps_install

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

 // set timezone according to wp admin - settings - general - timezone  (PHP5 only)
 //if (  function_exists( 'date_default_timezone_set' ) && function_exists( 'timezone_identifiers_list' ) && function_exists( 'timezone_open' ) && function_exists( 'timezone_offset_get' ) && $timezone_string = get_option( 'timezone_string' ) ) {
      // Set timezone in PHP5 manner
 //     @date_default_timezone_set( $timezone_string );
 //}

 if (function_exists('load_plugin_textdomain')) {
      load_plugin_textdomain('visitor-maps', false, 'visitor-maps/languages' );
 }

} // end function visitor_maps_init

function visitor_maps_get_options() {
   global $visitor_maps_opt, $visitor_maps_option_defaults;

  $visitor_maps_option_defaults = array(
   'donated' => 0,
   'active_time' => 5,
   'track_time' =>  15,
   'store_days' =>  30,
   'hide_administrators' =>  0,
   'dashboard_permissions' => 'manage_options',
   'ips_to_ignore' =>          '',
   'urls_to_ignore' =>         'wp-slimstat-js.php',
   'time_format' =>            'h:i a',
   'time_format_hms' =>        'h:i:sa' ,
   'date_time_format' =>       'm-d-Y h:i a',
   'geoip_date_format' =>      'm-d-Y h:i a',
   'whois_url' =>              'http://www.ip-adress.com/ip_tracer/',
   'whois_url_popup' =>        1,
   'enable_host_lookups' =>    1,
   'enable_location_plugin' => 1,
   'enable_state_display' =>   1,
   'hide_bots'  =>             0,
   'hide_console' =>           0,
   'combine_members'  =>       0,
   'hide_text_on_worldmap' =>  0,
   'enable_visitor_map_hover' => 0,
   'enable_users_map_hover'   => 0,
   'enable_blog_footer' =>     0,
   'enable_admin_footer' =>    1,
   'enable_records_page' =>    1,
   'enable_widget_link' =>     1,
   'enable_credit_link' =>     0,
   'enable_dash_map' =>        1,
   'enable_page_map' =>        1,
   'pins_limit' =>          2000,
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

   // geo-location no longer works and must be replaced now. 09-03-11
   if( $visitor_maps_opt['whois_url'] == 'http://www.geo-location.com/cgi-bin/index.cgi?s=' )
       $visitor_maps_opt['whois_url'] = 'http://www.ip-adress.com/ip_tracer/';

} // end function visitor_maps_get_options

function visitor_maps_options_page() {
  global $visitor_maps_opt, $path_visitor_maps, $visitor_maps_option_defaults;

    require_once(dirname(__FILE__) .'/visitor-maps-admin.php');

}// end function options_page


// update user activity
function visitor_maps_activity_do() {
  global $visitor_maps_opt, $wpdb, $path_visitor_maps, $current_user, $user_ID;

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

	$ip_address    = $this->get_ip_address();
    $last_page_url = $this->get_request_uri();

    // ignore these URLs set in options
    $urls_to_ignore = array();
    $urls_to_ignore = explode("\n",$visitor_maps_opt['urls_to_ignore']);
	if(!empty($urls_to_ignore) && !empty($ip_address)) {
		foreach($urls_to_ignore as $checked_url) {
		   $regexp = trim($checked_url);
		   if(preg_match("|$regexp|i", $last_page_url)) {
              // ignore this url
              $ip_address = '';
            }
		}
	}

    $http_referer  = $this->get_http_referer();
    $user_agent    = $this->get_http_user_agent();
    $user_agent_lower = strtolower($user_agent);
    $current_time  = (int) current_time( 'timestamp' );
    $xx_mins_ago   = ($current_time - absint(($visitor_maps_opt['track_time'] * 60)));

    // see if the user is a spider (bot) or not
    // based on a list of spiders in spiders.txt file
    $spider_flag = 0;
    if ($this->wo_not_null($user_agent_lower) && $spiders = file($path_visitor_maps.'spiders.txt') ) {
       for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
         if ($this->wo_not_null($spiders[$i]) && is_integer(strpos($user_agent_lower, trim($spiders[$i]))) ) {
           $spider_flag = $spiders[$i];
           break;
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
    // truncate to 64 chars or less
    $name = substr($name,0,64);

    if ($visitor_maps_opt['store_days'] > 0) {
            // remove visitor entries that have expired after $visitor_maps_opt['store_days'], save nickname friends
            $xx_days_ago_time = ($current_time - ($visitor_maps_opt['store_days'] * 60*60*24));
            $wpdb->query( $wpdb->prepare("DELETE from " . $wo_table_wo . "
                      WHERE (time_last_click < %d and nickname = '')
                      OR   (time_last_click < %d and nickname IS NULL)", $xx_days_ago_time, $xx_days_ago_time));
    } else {
            // remove visitor entries that have expired after $visitor_maps_opt['track_time'], save nickname friends
            $wpdb->query( $wpdb->prepare("DELETE from " . $wo_table_wo . "
                      WHERE (time_last_click < %d and nickname = '')
                      OR   (time_last_click < %d and nickname IS NULL)", $xx_mins_ago, $xx_mins_ago));
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

    // ignore these IPs
    $ips_to_ignore = array();
    $ips_to_ignore = explode("\n",$visitor_maps_opt['ips_to_ignore']);

	if(!empty($ips_to_ignore) && !empty($ip_address)) {
		foreach($ips_to_ignore as $checked_ip) {
			$regexp = str_replace ('.', '\\.', $checked_ip);
			$regexp = str_replace ('*', '.+', $regexp);
			if(preg_match("/^$regexp$/", $ip_address)) {
			    // ignore this user
                $wpdb->query( $wpdb->prepare("DELETE from " . $wo_table_wo . " WHERE ip_address = %s",$ip_address) );
                $ip_address = '';
                break;
			}
		}
	}


    // see if WP user
    // get_currentuserinfo(); ... already got this in some lines above
    if ($visitor_maps_opt['hide_administrators'] && $user_ID != '' && current_user_can('level_10') ){
      // hide admin activity
      $ip_address = '';
      $wpdb->query($wpdb->prepare("DELETE from " . $wo_table_wo . " WHERE name = %s",$name) );
    }

    if ($name != '' && $ip_address != '') { // skip if empty
      if (isset($stored_user) && $stored_user->ip_address != '') {

        // have an entry, update it
        $query = "UPDATE " . $wo_table_wo . "
        SET
        user_id          = '" . esc_sql($wo_user_id) . "',
        name             = '" . esc_sql($name) . "',
        ip_address       = '" . esc_sql($ip_address) . "',";

        // sometimes the country is blank, look it up again
        // this can happen if you just enabled the location plugin
        if ($visitor_maps_opt['enable_location_plugin'] && $stored_user->country_code == '') {
            $location_info = $this->get_location_info($ip_address);

            $query .= "country_name = '" . esc_sql($location_info['country_name']) . "',
                       country_code = '" . esc_sql($location_info['country_code']) . "',
                       city_name    = '" . esc_sql($location_info['city_name']) . "',
                       state_name   = '" . esc_sql($location_info['state_name']) . "',
                       state_code   = '" . esc_sql($location_info['state_code']) . "',
                       latitude     = '" . esc_sql($location_info['latitude']) . "',
                       longitude    = '" . esc_sql($location_info['longitude']) . "',";
        }
        // is a nickname user coming back online? then need to re-set the time entry and online time
        if ( $stored_user->time_last_click < $xx_mins_ago ) {
            $hostname = ($visitor_maps_opt['enable_host_lookups']) ? $this->gethostbyaddr_timeout($ip_address,2) : '';
            $query .= "num_visits       = '" . esc_sql($stored_user->num_visits + 1) . "',
                       time_entry       = '" . esc_sql($current_time) . "',
                       time_last_click  = '" . esc_sql($current_time) . "',
                       last_page_url    = '" . esc_sql($last_page_url) . "',
                       http_referer     = '" . esc_sql($http_referer) . "',
                       hostname         = '" . esc_sql($hostname) . "',
                       user_agent       = '" . esc_sql($user_agent) . "'
                       WHERE session_id = '" . esc_sql($ip_address) . "'";
        } else {
            if ($visitor_maps_opt['enable_host_lookups']) {
                    $hostname = (empty($stored_user->hostname)) ? $this->gethostbyaddr_timeout($ip_address,2) : $stored_user->hostname;
            } else {
                    $hostname = '';
            }
            $query .= "time_last_click  = '" . esc_sql($current_time) . "',
                       hostname         = '" . esc_sql($hostname) . "',
                       last_page_url    = '" . esc_sql($last_page_url) . "'
                       WHERE session_id = '" . esc_sql($ip_address) . "'";
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

        $query = "INSERT IGNORE INTO " . $wo_table_wo . "
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
                '" . esc_sql($ip_address) . "',
                '" . esc_sql($ip_address) . "',
                '" . esc_sql($wo_user_id) . "',
                '" . esc_sql($name) . "',
                '" . esc_sql($country_name) . "',
                '" . esc_sql($country_code) . "',
                '" . esc_sql($city_name) . "',
                '" . esc_sql($state_name) . "',
                '" . esc_sql($state_code) . "',
                '" . esc_sql($latitude) . "',
                '" . esc_sql($longitude) . "',
                '" . esc_sql($last_page_url) . "',
                '" . esc_sql($http_referer) . "',
                '" . esc_sql($user_agent) . "',
                '" . esc_sql($hostname) . "',
                '" . esc_sql($current_time) . "',
                '" . esc_sql($current_time) . "',
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

  require_once($path_visitor_maps.'include-whos-online-geoip.php');

  $gi = geoip_open_VMWO($path_visitor_maps.'GeoLiteCity.dat', VMWO_GEOIP_STANDARD);

  $record = geoip_record_by_addr_VMWO($gi, "$user_ip");
  geoip_close_VMWO($gi);

  $location_info = array();    // Create Result Array

  $location_info['provider']     = '';
  $location_info['city_name']    = (isset($record->city)) ? $record->city : '';
  $location_info['state_name']   = (isset($record->country_code) && isset($record->region)) ? $GEOIP_REGION_NAME[$record->country_code][$record->region] : '';
  $location_info['state_code']   = (isset($record->region)) ? strtoupper($record->region) : '';
  $location_info['country_name'] = (isset($record->country_name)) ? $record->country_name : '--';
  $location_info['country_code'] = (isset($record->country_code)) ? strtoupper($record->country_code) : '--';
  $location_info['latitude']     = (isset($record->latitude)) ? $record->latitude : '0';
  $location_info['longitude']    = (isset($record->longitude)) ? $record->longitude : '0';

  // this fixes accent characters on UTF-8, only when the blog charset is set to UTF-8
  if ( strtolower(get_option('blog_charset')) == 'utf-8' && function_exists('utf8_encode') ) {
    if ($location_info['city_name'] != '' ) {
       $location_info['city_name'] = utf8_encode($location_info['city_name']);
    }
    if ($location_info['state_name'] != '') {
       $location_info['state_name'] = utf8_encode($location_info['state_name']);
    }
    if ($location_info['country_name'] != '') {
       $location_info['country_name'] = utf8_encode($location_info['country_name']);
    }
  }

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
  $mysql_now = current_time( 'mysql' );
  $current_time = (int) current_time( 'timestamp' );
  $query_time = ($current_time - absint(($visitor_maps_opt['track_time'] * 60)));
  if ($visitor_maps_opt['hide_bots']) {
       // select the 'visitors online now' count, except for bots and our nickname friends not online now
       $visitors_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE (name = 'Guest' AND time_last_click > %d)
       OR (user_id > '0' AND time_last_click > %d)",$query_time, $query_time ) );
  } else {
       // select the 'visitors online now' count, all users
       $visitors_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE time_last_click > %d",$query_time ) );
  }

  // set today record if day changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (day('".$mysql_now."') != day(time) AND type = 'day')
     OR (count < '" . absint($visitors_count) . "' AND type = 'day')");

  // set month record if month changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (month('".$mysql_now."') != month(time) AND type = 'month')
     OR (count < '" . absint($visitors_count) . "' AND type = 'month')");

  // set year record if year changes or count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE (year('".$mysql_now."') != year(time) AND type = 'year')
     OR (count < '" . absint($visitors_count) . "' AND type = 'year')");

  // set all time record if count is higher than stored count
  $wpdb->query("UPDATE " . $wo_table_st . "
  SET
  count = '" . absint($visitors_count) . "',
  time = '".$mysql_now."'
  WHERE count < '" . absint($visitors_count) . "'
  AND type = 'all'");

  // return the 'visitors online now' count ( I recycle )
  return $visitors_count;

} // end function set_whos_records

function get_whos_records($visitors_count) {
  // get the day, month, year, all time records for display on web site,
  // use the recycled the 'visitors online now' count
  global $visitor_maps_stats, $visitor_maps_opt, $wpdb;

  $wo_table_st = $wpdb->prefix . 'visitor_maps_st';
  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
  $current_time = (int) current_time( 'timestamp' );
  $query_time = ($current_time - absint(($visitor_maps_opt['track_time'] * 60)));
  if ($visitor_maps_opt['hide_bots']) {
    $guests_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
     WHERE user_id = '0' AND name = 'Guest' AND time_last_click > %d",$query_time ) );
  } else {
    $guests_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
     WHERE user_id = '0' AND name = 'Guest' AND time_last_click > %d",$query_time ) );

    $bots_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
     WHERE user_id = '0' AND name != 'Guest' AND time_last_click > %d",$query_time ) );
  }

  $members_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
  WHERE user_id > '0' AND time_last_click > %d",$query_time ) );

  $visitor_maps_stats['visitors'] = sprintf( __('%d visitors online now','visitor-maps'),$visitors_count);
  $visitor_maps_stats['guests'] = sprintf( __('%d guests','visitor-maps'),$guests_count);
  if (!$visitor_maps_opt['hide_bots']) {
    $visitor_maps_stats['bots'] = sprintf( __('%d bots','visitor-maps'),$bots_count);
  }
  $visitor_maps_stats['members'] = sprintf( __('%d members','visitor-maps'),$members_count);
  $string = $visitor_maps_stats['visitors'] .'<br />';
  $string .= $visitor_maps_stats['guests'].', ';
  if (!$visitor_maps_opt['hide_bots']) {
    $string .= $visitor_maps_stats['bots'].', ';
  }
  $string .= $visitor_maps_stats['members'].'<br />';

  // fetch the day, month, year, all time records
  $visitors_arr = $wpdb->get_results("SELECT type, count, time FROM " . $wo_table_st, ARRAY_A);

  foreach( $visitors_arr as $visitors ) {
     if($visitors['type'] == 'day') {
        $visitor_maps_stats['today'] = esc_html( __('Max visitors today', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['time_format'],strtotime(current_time($visitors['time'])));
        $string .= esc_html( __('Max visitors today', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['time_format'],strtotime(current_time($visitors['time']))).'<br />';
     }
     if($visitors['type'] == 'month'){
       $visitor_maps_stats['month'] = esc_html( __('This month', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
       $string .= esc_html( __('This month', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '. date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time']))).'<br />';
     }
     if($visitors['type'] == 'year') {
       $visitor_maps_stats['year'] = esc_html( __('This year', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
       $string .= esc_html( __('This year', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time']))).'<br />';
     }
     if($visitors['type'] == 'all') {
        $visitor_maps_stats['all'] = esc_html( __('All time', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time'])));
        $string .= esc_html( __('All time', 'visitor-maps')).': ' . $visitors['count'] .' '.esc_html( __('at', 'visitor-maps')).' '.  date($visitor_maps_opt['date_time_format'],strtotime(current_time($visitors['time']))).'<br />';
     }
  }
  return $string;

} // end function get_whos_records

function get_visitor_maps_worldmap ($MS = 0) {
  // reads the whos-online database and makes html code to display a visitors last 15 minutes
  // thanks to pinto (www.joske-online.be) for the idea and code sample to get started
  // Mike Challis coded final version
  global $visitor_maps_opt, $wpdb, $path_visitor_maps, $url_visitor_maps;

  require_once(dirname(__FILE__) .'/visitor-maps-worldmap.php');

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
/*   if (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
   } else*/
   if (isset($_SERVER['REMOTE_ADDR'])) {
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

 if ( is_string($string) && preg_match("/^#[a-f0-9]{6}$/i", trim($string))) {
    return true;
 }
 if ( is_string($string) && preg_match("/^[a-f0-9]{6}$/i", trim($string))) {
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
 //$host = isset($output[0]) ? end ( explode (' ', $output[0])) : $ip; // plan a continues
 // mjc applied fix for PHP 5.4  Strict Standards: Only variables should be passed by reference error
 if ( isset($output[0]) ) {
         $array = explode (' ', $output[0]);
         $host = end( $array );
 } else {
         $host = $ip;
 }
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
        //if (get_magic_quotes_gpc()) {
        // wordpress always has magic_quotes On regardless of PHP settings!!
                return stripslashes($string);
       // } else {
       //         return $string;
       //}
}

// functions for protecting output against XSS. encode  < > & " ' (less than, greater than, ampersand, double quote, single quote).
function wo_output_string($string) {
    $string = str_replace('&', '&amp;', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '&#39;', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    return $string;
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
      //if (get_magic_quotes_gpc()) {
      // wordpress always has magic_quotes On regardless of PHP settings!!
        // Remove not needed escapes
        $input = stripslashes($input);
     // }
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
  global $visitor_maps_opt;
  if ( function_exists('current_user_can') && current_user_can($visitor_maps_opt['dashboard_permissions']) )
	wp_add_dashboard_widget('visitor_maps_dashboard_widget', __('Visitor Maps', 'visitor-maps') .' - '.__('Who\'s Online', 'visitor-maps') , array(&$this,'visitor_maps_dashboard_widget'));
}
function visitor_maps_dashboard_widget() {
    global $visitor_maps_stats, $visitor_maps_opt;

    echo "<p>$visitor_maps_stats</p>";
    if ($visitor_maps_opt['enable_credit_link']) {
      echo '<p><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps').'</a></small></p>';
    }

}
function visitor_maps_register_widget() {
	wp_register_sidebar_widget( 'visitor-maps', __('Who\'s Online', 'visitor-maps'), array(&$this,'visitor_maps_widget'));
}
function visitor_maps_widget($args) {
    extract($args);
    echo $before_widget . $before_title . __('Who\'s Online','visitor-maps') .$after_title;
    $this->visitor_maps_widget_content();
    echo $after_widget;
} // end function visitor_maps_widget

function visitor_maps_manual_sidebar() {

    echo '<h2>'. __('Who\'s Online','visitor-maps') .'</h2>';
    $this->visitor_maps_widget_content();
} // end visitor_maps_manual_sidebar


function visitor_maps_widget_content() {
    global $visitor_maps_stats, $visitor_maps_opt, $wpdb;

    $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';
    $current_time = (int) current_time( 'timestamp' );
    $query_time = ($current_time - absint(($visitor_maps_opt['track_time'] * 60)));
    if ($visitor_maps_opt['hide_bots']) {
       $visitors_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE (name = 'Guest' AND time_last_click > %d)
       OR (user_id > '0' AND time_last_click > %d)",$query_time, $query_time ) );

       $guests_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE user_id = '0' AND name = 'Guest' AND time_last_click > %d",$query_time ) );

    } else {
       $visitors_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE time_last_click > %d",$query_time ) );

       $guests_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE user_id = '0' AND name = 'Guest' AND time_last_click > %d",$query_time ) );

       $bots_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
       WHERE user_id = '0' AND name != 'Guest' AND time_last_click > %d",$query_time ) );
    }

    $members_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM " . $wo_table_wo ."
    WHERE user_id > '0' AND time_last_click > %d",$query_time ) );

    $stats_visitors = sprintf( __('%d visitors online now','visitor-maps'),$visitors_count);
    $stats_guests   = sprintf( __('%d guests','visitor-maps'),$guests_count);
    $stats_members  = sprintf( __('%d members','visitor-maps'),$members_count);

    if (!$visitor_maps_opt['hide_bots']) {
       $stats_bots  = sprintf( __('%d bots','visitor-maps'),$bots_count);
       if (!$visitor_maps_opt['combine_members']) {
            echo "<div>$stats_visitors</div><div><span style=\"white-space:nowrap\">$stats_guests,</span> <span style=\"white-space:nowrap\">$stats_bots,</span> <span style=\"white-space:nowrap\">$stats_members</span>";
       } else {
            $stats_guests   = sprintf( __('%d guests','visitor-maps'),($guests_count + $members_count));
            echo "<div>$stats_visitors</div><div><span style=\"white-space:nowrap\">$stats_guests,</span> <span style=\"white-space:nowrap\">$stats_bots</span>";
       }
    } else {
       if (!$visitor_maps_opt['combine_members'])
           echo "<div>$stats_visitors</div><div><span style=\"white-space:nowrap\">$stats_guests,</span> <span style=\"white-space:nowrap\">$stats_members</span>";
       else
           echo "<div>$stats_visitors";
    }
    if ($visitor_maps_opt['enable_widget_link'] && $visitor_maps_opt['enable_location_plugin'] ){
      if (!$visitor_maps_opt['hide_console'] || ($visitor_maps_opt['hide_console'] && current_user_can('manage_options')) ) {
        echo '</div><div>'. sprintf( __('<a id="visitor-maps-link" href="%s">Map of Visitors</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;');
      }
    }
    if ($visitor_maps_opt['enable_credit_link'])
      echo '</div><div><small>'.__('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/">'.__('Visitor Maps', 'visitor-maps').'</a></small>';
    echo "</div>";

} // end function visitor_maps_widget

function visitor_maps_upgrader_backup() {
    global $path_visitor_maps;
    // prevent plugin updater from deleting the GeoLiteCity.dat file
    $from = $path_visitor_maps.'GeoLiteCity.dat';
    $to = WP_CONTENT_DIR .'/visitor-maps-backup';
    if (is_file($from)) {
        if (!is_dir($to)) mkdir($to);
        if (is_dir($to))  rename($from, $to.'/GeoLiteCity.dat');
    }

} // end function visitor_maps_upgrader_backup

function visitor_maps_upgrader_restore() {
    global $path_visitor_maps;
    // prevent plugin updater from deleting the GeoLiteCity.dat file
    $to = $path_visitor_maps.'GeoLiteCity.dat';
    $from = WP_CONTENT_DIR .'/visitor-maps-backup';
    if (is_file($from.'/GeoLiteCity.dat')) {
        rename($from.'/GeoLiteCity.dat', $to);
        chmod($to, 0644);
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

  $url_visitor_maps  = plugin_dir_url( __FILE__ );  // http://www.yoursite.com/wp-content/plugins/visitor-maps/

  if ( defined('PATH_VISITOR_MAPS') ) {
      // define('PATH_VISITOR_MAPS', '/home/nflfirst/public_html/nfl_blog/wp-content/plugins/visitor-maps/');
     $path_visitor_maps = PATH_VISITOR_MAPS;
  } else {
     $path_visitor_maps = plugin_dir_path( __FILE__ );  // /path/to/wp-content/plugins/visitor-maps/
  }



  // visitor_maps init plugin
  add_action('init', array(&$visitor_maps, 'visitor_maps_init'));

  // get the options now
  $visitor_maps->visitor_maps_get_options();

  // versions upgraded from < 1.4.2 need a forced database table patch
  // will not be triggered on a fresh install
  if ( !get_option('visitor_maps_upgrade_1') ) {
     $visitor_maps->visitor_maps_upgrade_1();
  }

  add_action('plugins_loaded', array(&$visitor_maps,'visitor_maps_register_widget'));

  add_action('wp_dashboard_setup', array(&$visitor_maps,'visitor_maps_add_dashboard_widget'));

  // remind admin to install the GeoLite database
  if (
     (isset($_POST['visitor_maps_enable_location_plugin']) && !is_file($path_visitor_maps.'GeoLiteCity.dat') )
   ||
     (!isset($_POST['visitor_maps_set']) && !isset($_GET['do_geo']) && isset($visitor_maps_opt['enable_location_plugin']) && $visitor_maps_opt['enable_location_plugin'] && !is_file($path_visitor_maps.'GeoLiteCity.dat'))
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

  // add map link javascript
  // add javascript (conditionally to footer)
  // http://scribu.net/wordpress/optimal-script-loading.html
  add_action('wp_footer', array(&$visitor_maps,'visitor_maps_add_script'));

  // use shortcode in a page for the visitor maps feature
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
     register_uninstall_hook(__FILE__, 'visitor_maps_unset_options');
}

// end of file