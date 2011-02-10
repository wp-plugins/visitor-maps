<?php
/*

This file reads the whos-online database and makes a PNG image worldmap to display
all visitors for the last 15 minutes
thanks to pinto (www.joske-online.be) for the idea and the initial code sample

Visitor Maps PHP Script by Mike Challis
Free PHP Scripts - www.642weather.com/weather/scripts.php
*/


class WoViewMaps {
    var $set;
    var $gvar;

function display_map() {

  // reads the whos-online database and makes html code to display a visitors last 15 minutes
  // thanks to pinto (www.joske-online.be) for the idea and code sample to get started
  // Mike Challis coded final version
  global $visitor_maps_opt, $wpdb, $path_visitor_maps, $url_visitor_maps;

  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';

  if (!$visitor_maps_opt['enable_location_plugin']) {
     return '<p>get_visitor_maps_worldmap '.' '.__('error: geolocation data not enabled or installed','visitor-maps').'</p>';
  }

$this->set = array();
$this->gvar = array();
// worldmap image names (also set in class-wo-map-page.php and visitor-maps.php)
// just image names only, do not add any paths
$this->set['image_worldmap']    = 'wo-worldmap-smallest.jpg';// World (smallest) do not delete this one, it is the default
$this->set['image_worldmap_1']  = 'wo-worldmap-smallest.jpg';// World (smallest) do not delete this one, it is the default
$this->set['image_worldmap_2']  = 'wo-worldmap-small.jpg';   // World (small)
$this->set['image_worldmap_3']  = 'wo-worldmap-medium.jpg';  // World (medium)
$this->set['image_worldmap_4']  = 'wo-worldmap-large.jpg';   // World (large)
$this->set['image_worldmap_5']  = 'wo-us-black-map.png';     // US (black)
$this->set['image_worldmap_6']  = 'wo-us-brown-map.png';     // US (brown)
$this->set['image_worldmap_7']  = 'wo-akus-black-map.png';   // Canada and US (black)
$this->set['image_worldmap_8']  = 'wo-akus-brown-map.png';   // Canada and US (brown)
$this->set['image_worldmap_9']  = 'wo-asia-black-map.png';   // Asia (black)
$this->set['image_worldmap_10']  = 'wo-asia-brown-map.png';   // Asia (brown)
$this->set['image_worldmap_11']  = 'wo-aus-nz-black-map.png'; // Australia and NZ (black)
$this->set['image_worldmap_12'] = 'wo-aus-nz-brown-map.png'; // Australia and NZ (brown)
$this->set['image_worldmap_13'] = 'wo-ceu-black-map.png';    // Europe Central (black)
$this->set['image_worldmap_14'] = 'wo-ceu-brown-map.png';    // Europe Central (brown)
$this->set['image_worldmap_15'] = 'wo-eu-black-map.png';     // Europe (black)
$this->set['image_worldmap_16'] = 'wo-eu-brown-map.png';     // Europe (brown)
$this->set['image_worldmap_17'] = 'wo-scan-black-map.png';    // Scandinavia (black)
$this->set['image_worldmap_18'] = 'wo-scan-brown-map.png';    // Scandinavia (brown)
$this->set['image_worldmap_19'] = 'wo-gb-black-map.png';     // Great Britain (black)
$this->set['image_worldmap_20'] = 'wo-gb-brown-map.png';     // Great Britain (brown)
$this->set['image_worldmap_21'] = 'wo-mwus-black-map.png';   // US Midwest (black)
$this->set['image_worldmap_22'] = 'wo-mwus-brown-map.png';   // US Midwest (brown)
$this->set['image_worldmap_23'] = 'wo-ncus-black-map.png';   // US Upper Midwest (black)
$this->set['image_worldmap_24'] = 'wo-ncus-brown-map.png';   // US Upper Midwest (brown)
$this->set['image_worldmap_25'] = 'wo-neus-black-map.png';   // US Northeast (black)
$this->set['image_worldmap_26'] = 'wo-neus-brown-map.png';   // US Northeast (brown)
$this->set['image_worldmap_27'] = 'wo-nwus-black-map.png';   // US Northwest (black)
$this->set['image_worldmap_28'] = 'wo-nwus-brown-map.png';   // US Northwest (brown)
$this->set['image_worldmap_29'] = 'wo-rmus-black-map.png';   // US Rocky Mountain (black)
$this->set['image_worldmap_30'] = 'wo-rmus-brown-map.png';   // US Rocky Mountain (brown)
$this->set['image_worldmap_31'] = 'wo-scus-black-map.png';   // US South (black)
$this->set['image_worldmap_32'] = 'wo-scus-brown-map.png';   // US South (brown)
$this->set['image_worldmap_33'] = 'wo-seus-black-map.png';   // US Southeast (black)
$this->set['image_worldmap_34'] = 'wo-seus-brown-map.png';   // US Southeast (brown)
$this->set['image_worldmap_35'] = 'wo-swus-black-map.png';   // US Southwest (black)
$this->set['image_worldmap_36'] = 'wo-swus-brown-map.png';   // US Southwest (brown)
$this->set['image_worldmap_37'] = 'wo-es-pt-black-map.png';   // Spain/Portugal (black)
$this->set['image_worldmap_38'] = 'wo-es-pt-brown-map.png';   // Spain/Portugal (brown)
$this->set['image_worldmap_39'] = 'wo-finland-black-map.png';   // Finland (black)
$this->set['image_worldmap_40'] = 'wo-finland-brown-map.png';   // Finland (brown)
$this->set['image_worldmap_41'] = 'wo-finland-yellow-map.png';  // Finland (yellow)
$this->set['image_worldmap_42'] = 'wo-jp-black-map.png';   // Japan (black)
$this->set['image_worldmap_43'] = 'wo-jp-brown-map.png';   // Japan (brown)
$this->set['image_worldmap_44'] = 'wo-nl-black-map.png';   // Netherlands (black)
$this->set['image_worldmap_45'] = 'wo-nl-brown-map.png';   // Netherlands (brown)
$this->set['image_worldmap_46'] = 'wo-br-black-map.png';   // Brazil (black)
$this->set['image_worldmap_47'] = 'wo-br-brown-map.png';   // Brazil (brown)
// you can add more, just increment the numbers

$this->set['image_pin']   = 'wo-pin.jpg'; // do not delete this one, it is the default
$this->set['image_pin_1'] = 'wo-pin.jpg'; // do not delete this one, it is the default
$this->set['image_pin_2'] = 'wo-pin5x5.png';
$this->set['image_pin_3'] = 'wo-pin-green5x5.jpg';
// you can add more, just increment the numbers

  // set lat lon coordinates for worldmaps and custom regional maps.
  $ul_lat=0; $ul_lon=0; $lr_lat=360; $lr_lon=180; // default worldmap
  if ( isset($_GET['ul_lat']) && is_numeric($_GET['ul_lat'])  ) {
     $ul_lat = $_GET['ul_lat'];
  }
  if ( isset($_GET['ul_lon']) && is_numeric($_GET['ul_lon'])  ) {
     $ul_lon = $_GET['ul_lon'];
  }
  if ( isset($_GET['lr_lat']) && is_numeric($_GET['lr_lat'])  ) {
     $lr_lat = $_GET['lr_lat'];
  }
  if ( isset($_GET['lr_lon']) && is_numeric($_GET['lr_lon'])  ) {
     $lr_lon = $_GET['lr_lon'];
  }
  $offset_x = $offset_y = 0;
  if ( isset($_GET['offset_x']) && is_numeric($_GET['offset_x'])  ) {
     $offset_x = floor($_GET['offset_x']);
  }
  if ( isset($_GET['offset_y']) && is_numeric($_GET['offset_y'])  ) {
     $offset_y = floor($_GET['offset_y']);
  }
  // select text on or off
  $this->gvar['text_display'] = false; // default
  if ( isset($_GET['text']) && $_GET['text'] == 'on' ) {
    $this->gvar['text_display']  = true;
  }
  // select text align
  $this->gvar['text_align']  = 'cb'; // default center bottom
  if( isset($_GET['textalign']) && $this->validate_text_align($_GET['textalign']) ) {
    $this->gvar['text_align'] =  $_GET['textalign'];
  }
  // select text color by hex code
  $this->gvar['text_color']  = '800000'; // default blue
  if( isset($_GET['textcolor']) && $this->validate_color_wo($_GET['textcolor']) ) {
    $this->gvar['text_color'] =  str_replace('#','',$_GET['textcolor']);  // hex
  }
  // select text shadow color by hex code
  $this->gvar['text_shadow_color']  = 'C0C0C0'; // default white
  if( isset($_GET['textshadow']) && $this->validate_color_wo($_GET['textshadow']) ) {
    $this->gvar['text_shadow_color'] =  str_replace('#','',$_GET['textshadow']);  // hex
  }
  // select pins on or off
  $this->gvar['pins_display'] = true;  // default
  if ( isset($_GET['pins']) && $_GET['pins'] == 'off' ) {
    $this->gvar['pins_display'] = false;
  }

  // select time units
  if ( isset($_GET['time']) && is_numeric($_GET['time']) && isset($_GET['units']) ) {
      $time  = floor($_GET['time']);
      $units = $_GET['units'];
      $units_filtered = '';
     if ( $time > 0 && ($units == 'minute' || $units == 'minutes') ) {
           $seconds_ago = ($time * 60); // minutes
           $units_filtered = $units;
           $units_lang = __('minutes', 'visitor-maps');
     } else if( $time > 0 && ($units == 'hour' || $units == 'hours') ) {
           $seconds_ago = ($time * 60*60); // hours
           $units_filtered = $units;
           $units_lang = __('hours', 'visitor-maps');
     } else if( $time > 0 && ($units == 'day' || $units == 'days') ) {
           $seconds_ago = ($time * 60*60*24); // days
           $units_filtered = $units;
           $units_lang = __('days', 'visitor-maps');
     } else {
           $seconds_ago = absint($visitor_maps_opt['track_time'] * 60); // default
     }

  } else {
          $seconds_ago = absint($visitor_maps_opt['track_time'] * 60); // default
  }

  // select map image
  $image_worldmap_path = $path_visitor_maps .'images/' . $this->set['image_worldmap'];  // default
  if ( isset($_GET['map']) && is_numeric($_GET['map']) ) {
     $image_worldmap_path = $path_visitor_maps . 'images/' . $this->set['image_worldmap_'.floor($_GET['map'])];
     if (!file_exists($path_visitor_maps . 'images/' . $this->set['image_worldmap_'.floor($_GET['map'])])) {
          $image_worldmap_path = $path_visitor_maps . 'images/' . $this->set['image_worldmap'];  // default
     }
  }

  // select pin image
  $image_pin_path = $path_visitor_maps .'images/' . $this->set['image_pin'];  // default
  if ( isset($_GET['pin']) && is_numeric($_GET['pin']) ) {
     $image_pin_path = $path_visitor_maps . 'images/'. $this->set['image_pin_'.floor($_GET['pin'])];
     if (!file_exists($path_visitor_maps . 'images/'. $this->set['image_pin_'.floor($_GET['pin'])])) {
          $image_pin_path = $path_visitor_maps . 'images/'. $this->set['image_pin'];  // default
     }
  }

  // get image data
  list($image_worldmap_width, $image_worldmap_height, $image_worldmap_type) = getimagesize($image_worldmap_path);
  list($image_pin_width, $image_pin_height, $image_pin_type) = getimagesize($image_pin_path);

  switch($image_worldmap_type) {
        case "1": $map_im = imagecreatefromgif("$image_worldmap_path");
        break;
        case "2": $map_im = imagecreatefromjpeg("$image_worldmap_path");
        break;
        case "3": $map_im = imagecreatefrompng("$image_worldmap_path");
        break;
  }
  switch($image_pin_type) {
        case "1": $pin_im = imagecreatefromgif("$image_pin_path");
        break;
        case "2":
        $pin_im = imagecreatefromjpeg("$image_pin_path");
        $image_pin_path_user = str_replace('.jpg','-user.jpg',$image_pin_path);
        $pin_im_user = imagecreatefromjpeg("$image_pin_path_user");
        $image_pin_path_bot = str_replace('.jpg','-bot.jpg',$image_pin_path);
        $pin_im_bot =  imagecreatefromjpeg("$image_pin_path_bot");
        break;
        case "3": $pin_im = imagecreatefrompng("$image_pin_path");
        break;
  }

    // map parameters
  $scale = 360 / $image_worldmap_width;

  //$green = imagecolorallocate ($map_im, 0,255,0);

  // Time to remove old entries
  $xx_secs_ago = (time() - $seconds_ago);

  $rows_arr = array();
  if ($visitor_maps_opt['hide_bots']) {

       $rows_arr = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS user_id, name, longitude, latitude FROM ".$wo_table_wo."
                 WHERE name = 'Guest' AND time_last_click > '" . $xx_secs_ago . "' LIMIT ".$visitor_maps_opt['pins_limit'] ."",ARRAY_A );

       $rows_count = $wpdb->get_var("SELECT FOUND_ROWS()");

  } else {

       $rows_arr = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS user_id, name, longitude, latitude FROM ".$wo_table_wo."
                 WHERE time_last_click > '" . $xx_secs_ago . "' LIMIT ".$visitor_maps_opt['pins_limit'] ."",ARRAY_A );

       $rows_count = $wpdb->get_var("SELECT FOUND_ROWS()");
  }

  $count = 0;
  // create pins on the map
  if ($rows_arr) { // check of there are any visitors
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
      // Now mark the point on the map using a green 2 pixel rectangle
      //imagefilledrectangle($map_im,$x-1,$y-1,$x+1,$y+1,$green);
      if ($this->gvar['pins_display']) {
           $this_pin_im = $pin_im;
           if ($visitor_maps_opt['enable_users_map_hover'] && $row['user_id'] > 0 && $row['name'] != '') {
             // different pin color for logged in user
             $this_pin_im = $pin_im_user;
           }
           if ( !$visitor_maps_opt['hide_bots'] && $row['user_id'] == 0 && $row['name'] != 'Guest') {
                // different pin color for search bot
                $this_pin_im = $pin_im_bot;
           }
        // put pin image on map image
        imagecopy($map_im, $this_pin_im, $x, $y, 0, 0, $image_pin_width, $image_pin_height);
      }
    }
   } // end foreach
 } // end if ($rows_arr) {

  if ( $this->gvar['text_display'] && !$visitor_maps_opt['hide_text_on_worldmap']) {
     if ($units_filtered != '') {
             // 5 visitors since 15 (minutes|hours|days) ago
            $text = sprintf( __('%1$d visitors since %2$d %3$s ago', 'visitor-maps'),$rows_count,$time,$units_lang) ;
     } else {
            // 5 visitors since 15 minutes ago
            $text = sprintf( __('%1$d visitors since %2$d ago', 'visitor-maps'),$rows_count,floor($visitor_maps_opt['track_time'])) ;
     }
     $this->textoverlay($text, $map_im, $image_worldmap_width, $image_worldmap_height);
  }

  // Return the map image
  if ( isset($_GET['type']) && $_GET['type'] == 'jpg' ) {
        header("Content-Type: image/jpeg");
        imagejpeg($map_im);
  } else if( isset($_GET['type']) && $_GET['type'] == 'png' ) {
        header("Content-Type: image/png");
        imagepng($map_im);
  } else {
        header("Content-Type: image/png");
        imagepng($map_im);
  }

  imagedestroy($map_im);
  imagedestroy($pin_im);

} // end function display_map

// begin functions -------------------------------------------------------------

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

function textoverlay($text, $image_p, $new_width, $new_height) {

    $fontstyle = 5; // 1,2,3,4 or 5
    $fontcolor = $this->gvar['text_color'];
    $fontshadowcolor = $this->gvar['text_shadow_color'];
    $ttfont = (isset($this->set['map_text_font'])) ? $this->set['map_text_font'] : WP_PLUGIN_DIR . '/visitor-maps/vmfont.ttf';
    $fontsize = 11; # size for True Type Font $ttfont only (8-18 recommended)
    $textalign = $this->gvar['text_align'];
    $xmargin = 5;
    $ymargin = 0;

    if (!preg_match('#[a-z0-9]{6}#i', $fontcolor)) $fontcolor = 'FFFFFF';  # default white
    if (!preg_match('#[a-z0-9]{6}#i', $fontshadowcolor)) $fontshadowcolor = '808080'; # default grey
    $fcint = hexdec("#$fontcolor");
    $fsint = hexdec("#$fontshadowcolor");
    $fcarr = array("red" => 0xFF & ($fcint >> 0x10),"green" => 0xFF & ($fcint >> 0x8),"blue" => 0xFF & $fcint);
    $fsarr = array("red" => 0xFF & ($fsint >> 0x10),"green" => 0xFF & ($fsint >> 0x8),"blue" => 0xFF & $fsint);
    $fcolor  = imagecolorallocate($image_p, $fcarr["red"], $fcarr["green"], $fcarr["blue"]);
    $fscolor = imagecolorallocate($image_p, $fsarr["red"], $fsarr["green"], $fsarr["blue"]);
    if ($ttfont != '') {
       # using ttf fonts
       $alpha   = range("a", "z");
       $alpha_u = range("A", "Z");
       $alpha = $alpha.$alpha_u.range(0, 9);
       $_b = imageTTFBbox($fontsize,0,$ttfont,$alpha);
       $fontheight = abs($_b[7]-$_b[1]);
    } else {
      $font = $fontstyle;
      # using built in fonts, find alignment
      if($font < 0 || $font > 5){ $font = 1; }
          $fontwidth = ImageFontWidth($font);
          $fontheight = ImageFontHeight($font);
      }
      $text = preg_replace("/\r/",'',$text);
      # wordwrap line if too many characters on one line
      if ($ttfont != '') {
         # array lines
         $lines = explode("\n", $text);
         $lines = $this->ttf_wordwrap($lines,$ttfont,$fontsize,floor($new_width - ($xmargin * 2)));
      } else {
        $maxcharsperline = floor(($new_width - ($xmargin * 2)) / $fontwidth);
        $text = wordwrap($text, $maxcharsperline, "\n", 1);
        # array lines
        $lines = explode("\n", $text);
      }
      # determine alignment
      $align = 'ul'; # default upper left
      if ($textalign == 'll') $align = 'll'; // lowerleft
      if ($textalign == 'ul') $align = 'ul'; // upperleft
      if ($textalign == 'lr') $align = 'lr'; // lowerright
      if ($textalign == 'ur') $align = 'ur'; // upperright
      if ($textalign == 'c')  $align = 'c';  // center
      if ($textalign == 'ct') $align = 'ct'; // centertop
      if ($textalign == 'cb') $align = 'cb'; // centerbottom
      # find start position for each text position type
      if ($align == 'ul') { $x = $xmargin; $y = $ymargin;}
      if ($align == 'll') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'ur') $y = $ymargin;
      if ($align == 'lr') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'ct') $y = $ymargin;
      if ($align == 'cb') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'c') $y = ($new_height/2) - ((count($lines) * $fontheight)/2);
      if ($ttfont != '') $y +=$fontsize; # fudge adjustment for truetype margin
         while (list($numl, $line) = each($lines)) {
             # adjust position for each text position type
             if ($ttfont != '') {
                $_b = imageTTFBbox($fontsize,0,$ttfont,$line);
                $stringwidth = abs($_b[2]-$_b[0]);
             }else{
                $stringwidth = strlen($line) * $fontwidth;
             }
             if ($align == 'ur'||$align == 'lr') $x = ($new_width - ($stringwidth) - $xmargin);
             if ($align == 'ct'||$align == 'cb'||$align == 'c') $x = $new_width/2 - $stringwidth/2;
             if ($ttfont != '') {
                # write truetype font text with slight SE shadow to standout
                imagettftext($image_p, $fontsize, 0, $x-1, $y, $fscolor, $ttfont, $line);
                imagettftext($image_p, $fontsize, 0, $x, $y-1, $fcolor, $ttfont, $line);
             }else{
                # write text with slight SE shadow to standout
                imagestring($image_p,$font,$x-1,$y,$line,$fscolor);
                imagestring($image_p,$font,$x,$y-1,$line,$fcolor);
             }
             # adjust position for each text position type
             if ($align == 'ul'||$align == 'ur'||$align == 'ct'||$align == 'c') $y += $fontheight;
             if ($align == 'll'||$align == 'lr'||$align == 'cb') $y -= $fontheight;
         } # end while
} // end function textoverlay



function ttf_wordwrap($srcLines,$font,$textSize,$width) {
    $dstLines = Array(); // The destination lines array.
    foreach ($srcLines as $currentL) {
        $line = '';
        $words = explode(" ", $currentL); //Split line into words.
        foreach ($words as $word) {
            $dimensions = imagettfbbox($textSize, 0, $font, $line.' '.$word);
            $lineWidth = $dimensions[4] - $dimensions[0]; // get the length of this line, if the word is to be included
            if ($lineWidth > $width && !empty($line) ) { // check if it is too big if the word was added, if so, then move on.
                $dstLines[] = trim($line); //Add the line like it was without spaces.
                $line = '';
            }
            $line .= $word.' ';
        }
        $dstLines[] =  trim($line); //Add the line when the line ends.
    }
    return $dstLines;
} // end of ttf_wordwrap function

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
} // end function $this->validate_color_wo

function validate_text_align($string) {
 // only allow proper text align codes
  $allowed = array('ll','ul','lr','ur','c','ct','cb');
 if ( in_array($string, $allowed) ) {
    return true;
 }
 return false;
} // end function validate_text_align

} // end class

?>