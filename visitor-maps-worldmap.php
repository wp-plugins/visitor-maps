<?php
/*

Visitor Maps PHP Script by Mike Challis
Free PHP Scripts - www.642weather.com/weather/scripts.php

This file is part of the function get_visitor_maps_worldmap

*/

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
$C['image_worldmap_17'] = 'wo-scan-black-map.png';    // Scandinavia (black)
$C['image_worldmap_18'] = 'wo-scan-brown-map.png';    // Scandinavia (brown)
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
$C['image_worldmap_39'] = 'wo-finland-black-map.png';   // Finland (black)
$C['image_worldmap_40'] = 'wo-finland-brown-map.png';   // Finland (brown)
$C['image_worldmap_41'] = 'wo-finland-yellow-map.png';   // Finland (yellow)
$C['image_worldmap_42'] = 'wo-jp-black-map.png';   // Japan (black)
$C['image_worldmap_43'] = 'wo-jp-brown-map.png';   // Japan (brown)
$C['image_worldmap_44'] = 'wo-nl-black-map.png';   // Netherlands (black)
$C['image_worldmap_45'] = 'wo-nl-brown-map.png';   // Netherlands (brown)
$C['image_worldmap_46'] = 'wo-br-black-map.png';   // Brazil (black)
$C['image_worldmap_47'] = 'wo-br-brown-map.png';   // Brazil (brown)
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
  $image_worldmap_path = $path_visitor_maps .'images/' . $C['image_worldmap'];  // default
  $G['map'] = 1;
  if ( isset($MS['map']) && is_numeric($MS['map']) ) {
     $G['map'] = floor($MS['map']);
     $image_worldmap = $url_visitor_maps . 'images/' . $C['image_worldmap_'.$G['map']];
     $image_worldmap_path = $path_visitor_maps . 'images/' . $C['image_worldmap_'.$G['map']];
     if (!file_exists($path_visitor_maps . 'images/' . $C['image_worldmap_'.$G['map']])) {
          $image_worldmap = $url_visitor_maps . 'images/' . $C['image_worldmap'];  // default
          $image_worldmap_path = $path_visitor_maps . 'images/' . $C['image_worldmap'];  // default
          $G['map'] = 1;
     }
  }
  // this is a hack to fix servers with image header problems.
  if($visitor_maps_opt['hide_text_on_worldmap']){
   $image_worldmap2 = $image_worldmap;
  }
  // select pin image
  $image_pin = $url_visitor_maps .'images/' . $C['image_pin'];  // default
  $image_pin_path = $path_visitor_maps .'images/' . $C['image_pin'];  // default
  $G['pin'] = 1;
  if ( isset($MS['pin']) && is_numeric($MS['pin']) ) {
     $G['pin'] = floor($MS['pin']);
     $image_pin = $url_visitor_maps . 'images/'. $C['image_pin_'.$G['pin']];
     $image_pin_path = $path_visitor_maps . 'images/'. $C['image_pin_'.$G['pin']];
     if (!file_exists($path_visitor_maps .'images/' . $C['image_pin_'.$G['pin']])) {
          $image_pin = $url_visitor_maps .'images/' . $C['image_pin'];  // default
          $image_pin_path = $path_visitor_maps .'images/' . $C['image_pin'];  // default
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
  list($image_worldmap_width, $image_worldmap_height, $image_worldmap_type) = getimagesize($image_worldmap_path);
  list($image_pin_width, $image_pin_height, $image_pin_type) = getimagesize($image_pin_path);

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
    '&amp;wp-minify-off=1'.
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
    '&wp-minify-off=1'. 
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
// this is a hack to fix servers with image header problems.
 if($visitor_maps_opt['hide_text_on_worldmap']){
  $image_worldmap = $image_worldmap2;
 }
$string .= '<div style="position:relative; border:none; background-image:url('.$image_worldmap.'); width:'.$image_worldmap_width.'px; height:'.$image_worldmap_height.'px;">';
$string .= "\n".'<!--[if lte IE 8 ]>
<div style="position:relative; margin-top: -11px;">
<![endif]-->';
  $string .= "\n";

 $rows_arr = array();
  if (!$visitor_maps_opt['hide_bots']) {
       // all visitors
       $rows_arr = $wpdb->get_results("
                 SELECT SQL_CALC_FOUND_ROWS user_id, name, nickname, country_name, country_code, city_name, state_name, state_code, latitude, longitude
                 FROM ".$wo_table_wo."
                 WHERE time_last_click > '" . absint($xx_secs_ago) . "' LIMIT ".$visitor_maps_opt['pins_limit'] ."",ARRAY_A );

       $rows_count = $wpdb->get_var("SELECT FOUND_ROWS()");

  } else {
       // guests and members, no bots
       $rows_arr = $wpdb->get_results("
                 SELECT SQL_CALC_FOUND_ROWS user_id, name, nickname, country_name, country_code, city_name, state_name, state_code, latitude, longitude
                 FROM ".$wo_table_wo."
                 WHERE (name = 'Guest' AND time_last_click > '" . absint($xx_secs_ago) . "')
                 OR (name != 'Guest' AND user_id > 0 AND time_last_click > '" . absint($xx_secs_ago) . "') LIMIT ".$visitor_maps_opt['pins_limit'] ."",ARRAY_A );

       $rows_count = $wpdb->get_var("SELECT FOUND_ROWS()");
  }

  // create pin on the map
  $count = 0;
if ($rows_arr) { // check of there are any visitors

    // see if the user is a spider (bot) or not
    // based on a list of spiders in spiders.txt file
    if (!$visitor_maps_opt['hide_bots'])
       $spiders = file($path_visitor_maps.'spiders.txt');

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
      $title_pre = '';
      $this_image_pin = $image_pin;
      if ($visitor_maps_opt['enable_users_map_hover'] && $row['user_id'] > 0 && $row['name'] != '') {
         // find name for logged in user
         // different pin color for logged in user
         $title_pre = $this->wo_sanitize_output($row['name']).' '.__('from', 'visitor-maps').' ';
         if($G['pin'] == 1){
           $this_image_pin = str_replace('.jpg','-user.jpg',$image_pin);
         }
      }
      if ( !$visitor_maps_opt['hide_bots'] && $row['user_id'] == 0 && $row['name'] != 'Guest') {
         //  find name for bot
         // different pin color for bot
         if ( $this->wo_not_null($row['name'])  ) {
           for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
               if ($this->wo_not_null($spiders[$i]) && is_integer(strpos($row['name'], trim($spiders[$i]))) ) {
                   // Tokenize UserAgent and try to find Bots name
                   $tok = strtok($row['name']," ();/");
                   while ($tok !== false) {
                     if ( strlen(strtolower($tok)) > 3 )
                       if ( !strstr(strtolower($tok), "mozilla") &&
                           !strstr(strtolower($tok), "compatible") &&
                           !strstr(strtolower($tok), "msie") &&
                           !strstr(strtolower($tok), "windows")
                           ) {
                           $title_pre = $this->wo_sanitize_output($tok).' '.__('from', 'visitor-maps').' ';
                           if($G['pin'] == 1){
                               $this_image_pin = str_replace('.jpg','-bot.jpg',$image_pin);
                           }
                           break;
                       }
                       $tok = strtok(" ();/");
                     }
                     break;
                 }
            }
         }
      }
      $title = '';
      if ( $visitor_maps_opt['enable_state_display'] ) {
              if ($row['city_name'] != '') {
                if ($row['country_code'] == 'US') {
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
      $title = $title_pre . $title;
      $string .= '<div style="cursor:pointer;position:absolute; top:'.$y.'px; left:'.$x.'px;">
      <img src="'.$this_image_pin.'" style="border:0; margin:0; padding:0;" width="'.$image_pin_width.'" height="'.$image_pin_height.'" alt="" title="'.$this->wo_sanitize_output($title).'" />
      </div>';
      $string .= "\n";
    }
  } // end foreach
 } // end if ($rows_arr) {
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

// end file