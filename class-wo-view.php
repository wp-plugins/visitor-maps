<?php
/*
Visitor Maps PHP Script by Mike Challis
Free PHP Scripts - www.642weather.com/weather/scripts.php
*/

class WoView {
    var $wo_visitor_ip;
    var $ip_addrs_active;
    var $set;

function view_whos_online() {
  global $wpdb, $visitor_maps_opt, $url_visitor_maps, $path_visitor_maps;

  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';


// Automatic refresh times in seconds and display names
//   Time and Display Text order must match between the arrays
//   "None" is handled separately in the code
  $refresh_time = array(     30,    60,     120,     300,    600 );
  $refresh_display = array( '0:30', '1:00', '2:00', '5:00', '10:00' );
  $refresh_values = array();
  $refresh_values[] = array('id' => 'none', 'text' => esc_attr( __( 'None', 'visitor-maps' ) ) );
  $refresh_values[] = array('id' => '30', 'text' => '0:30');
  $refresh_values[] = array('id' => '60', 'text' => '1:00');
  $refresh_values[] = array('id' => '120', 'text' => '2:00');
  $refresh_values[] = array('id' => '300', 'text' => '5:00');
  $refresh_values[] = array('id' => '600', 'text' => '10:00');

  $show_type = array();
  $show_type[] = array('id' => 'none',   'text' => esc_attr( __( 'None', 'visitor-maps' ) ));
  $show_type[] = array('id' => 'all',    'text' => esc_attr( __( 'All', 'visitor-maps' ) ));
  $show_type[] = array('id' => 'bots',   'text' => esc_attr( __( 'Bots', 'visitor-maps' ) ));
  $show_type[] = array('id' => 'guests', 'text' => esc_attr( __( 'Guests', 'visitor-maps' ) ));

  $bots_type = array();
  $bots_type[] = array('id' => '0', 'text' => esc_attr( __( 'No', 'visitor-maps' ) ));
  $bots_type[] = array('id' => '1', 'text' => esc_attr( __( 'Yes', 'visitor-maps' ) ));


  $this->set = array();
  $this->set['allow_refresh'] = 1;
  $this->set['allow_profile_display'] = 1;
  $this->set['allow_ip_display'] = 1;
  $this->set['allow_last_url_display'] = 1;
  $this->set['allow_referer_display'] = 1;

  // three of the strings can be auto wordwrapped
  $this->set['lasturl_wordwrap_chars']   = 100; // <= set to number of characters to wrap to
  $this->set['useragent_wordwrap_chars'] = 100; // <= set to number of characters to wrap to
  $this->set['referer_wordwrap_chars']   = 100; // <= set to number of characters to wrap to

  // Text colors used for table entries - different colored text for different users
  //   Named colors and #Hex values should work fine
  $this->set['color_bot']   = 'maroon';
  $this->set['color_admin'] = 'darkblue';
  $this->set['color_guest'] = 'green';
  $this->set['color_user']  = 'blue';

  // status image names
  // just image names only, do not add any paths
  $this->set['image_active_guest']   = 'active_user.gif'; // active user
  $this->set['image_inactive_guest'] = 'inactive_user.gif'; // inactive user
  $this->set['image_active_bot']     = 'active_bot.gif'; // active bot
  $this->set['image_inactive_bot']   = 'inactive_bot.gif'; // inactive bot
  //$this->set['geolite_path'] = dirname(__FILE__).'/';
  $this->wo_visitor_ip = $this->get_ip_address();


$geoip_old = 0;
if( $visitor_maps_opt['enable_location_plugin'] ){
  $geoip_file_time = filemtime($path_visitor_maps.'GeoLiteCity.dat');
  //$geoip_file_time = strtotime("-1 month"); // for testing the need to update link
  // how many calendar days ago?
  $geoip_days_ago = floor((strtotime(date('Y-m-d'). ' 00:00:00') - strtotime(date('Y-m-d', $geoip_file_time). ' 00:00:00')) / (60*60*24));
  // is it older than the first of this month?
  $geoip_begin_month = strtotime( '01-' .date('m') .'-'. date('Y') );
  if ($geoip_begin_month > $geoip_file_time) {
    $geoip_old = $this->check_geoip_date($geoip_file_time);
  }

}

  $numrows = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo);
  $since = $wpdb->get_var("SELECT time_last_click FROM " . $wo_table_wo ." ORDER BY time_last_click ASC LIMIT 1");

// Time to remove old entries
$xx_mins_ago = (time() - absint(($visitor_maps_opt['track_time'] * 60)));

if ($visitor_maps_opt['store_days'] > 0) {
       // remove visitor entries that have expired after $visitor_maps_opt['store_days'], save nickname friends
       $xx_days_ago_time = (time() - (absint($visitor_maps_opt['store_days']) * 60*60*24));
       $wpdb->query("DELETE from " . $wo_table_wo . "
                 WHERE (time_last_click < '" . absint($xx_days_ago_time) . "' and nickname = '')
                  OR   (time_last_click < '" . absint($xx_days_ago_time) . "' and nickname IS NULL)");
} else {
       // remove visitor entries that have expired after $visitor_maps_opt['track_time'], save nickname friends
       $wpdb->query("DELETE from " . $wo_table_wo . "
                 WHERE (time_last_click < '" . absint($xx_mins_ago) . "' and nickname = '')
                  OR   (time_last_click < '" . absint($xx_mins_ago) . "' and nickname IS NULL)");
}

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
 $refresh = (isset($wo_prefs_arr['refresh'])) ? $wo_prefs_arr['refresh'] : 'none';
 $show = (isset($wo_prefs_arr['show'])) ? $wo_prefs_arr['show'] : 'none';

echo '<table border="0" width="99%">
 <tr><td>
  <form name="wo_view" action="'.admin_url( 'index.php?page=visitor-maps' ).'" method="get">';
  if ($this->set['allow_profile_display']) echo esc_html( __( 'Profile Display:', 'visitor-maps' ) ). ' ' . $this->draw_pull_down_menu('show', $show_type, $show, 'onchange="this.form.submit();"') . ' ';
  if ($this->set['allow_refresh']) echo esc_html( __( 'Refresh Rate:', 'visitor-maps' ) ) . ' ' . $this->draw_pull_down_menu('refresh', $refresh_values, $refresh, 'onchange="this.form.submit();"') . ' ';
  echo esc_html( __( 'Show Bots:', 'visitor-maps' ) ) . ' ' . $this->draw_pull_down_menu('bots', $bots_type, $bots, 'onchange="this.form.submit();"') . ' ';
  echo '<input type="hidden" name="page" value="visitor-maps" />
  </form>
  <a href="'.admin_url( 'index.php?page=whos-been-online').'">' . esc_html( __( 'Who\'s Been Online', 'visitor-maps' ) ) . "</a>\n";
 if ( function_exists('current_user_can') && current_user_can('manage_options') )
  echo '<br /> <a href="'.admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php').'">' . __( 'Visitor Maps Options', 'visitor-maps' ) . "</a>\n";

  if ( $visitor_maps_opt['enable_location_plugin'] ) {
    echo '<br />'.sprintf( __('<a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;')."\n";

  }
  echo '</td>
';
?>

<td>
 <table border="0" cellspacing="2" cellpadding="2" align="right">
 <tr>
  <td><?php echo '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_active_guest'].'" border="0" alt="'.esc_attr( __( 'Active Guest', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Active Guest', 'visitor-maps' ) ).'" /> ' . esc_html( __( 'Active Guest', 'visitor-maps' ) ); ?>
  </td>
  <td><?php echo '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_inactive_guest'].'" border="0" alt="'.esc_attr( __( 'Inactive Guest', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Inactive Guest', 'visitor-maps' ) ).'" /> ' . esc_html( __( 'Inactive Guest', 'visitor-maps' ) ); ?>
  </td>
 </tr>
  <tr>
   <td><?php echo '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_active_bot'].'" border="0" alt="'.esc_attr( __( 'Active Bot', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Active Bot', 'visitor-maps' ) ).'" /> ' . esc_html( __( 'Active Bot', 'visitor-maps' ) ); ?>
   </td>
   <td><?php echo '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_inactive_bot'].'" border="0" alt="'.esc_attr( __( 'Inactive Bot', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Inactive Bot', 'visitor-maps' ) ).'" /> ' . esc_html( __( 'Inactive Bot', 'visitor-maps' ) ); ?>
  </td>
 </tr>
</table>
</td>
</tr>
</table>


 <table border="0" cellspacing="2" cellpadding="2" width="99%">
  <tr>
   <td align="center">
     <b><?php echo sprintf(__('%1$d visitors since %2$s', 'visitor-maps'),(int)$numrows,($numrows > 0)? date($visitor_maps_opt['date_time_format'],(int)$since): __( 'installation', 'visitor-maps' )); ?></b>
   </td>
 </tr>
 <tr>
   <td align="center">
     <b><?php echo esc_html( __( 'Last refresh at', 'visitor-maps' ) ) .' '.  date( $visitor_maps_opt['time_format'] ); ?></b>
   </td>
 </tr>
 <tr>
     <td valign="top">
         <table border="0" cellspacing="0" cellpadding="2" width="99%">
         <tr>
            <td valign="top">
               <table border="0" cellspacing="0" cellpadding="2" width="99%">
               <tr class="table-top">
                  <td>&nbsp;</td>
                  <td>&nbsp;<?php echo esc_html( __( 'Online', 'visitor-maps' ) );  ?></td>
                  <td>&nbsp;<?php echo esc_html( __( 'Who', 'visitor-maps' ) ); ?></td>
                  <?php if ($this->set['allow_ip_display']) echo '<td>&nbsp;'. esc_html( __( 'IP Address', 'visitor-maps' ) ) .'</td> '; ?>
                  <?php if ($visitor_maps_opt['enable_location_plugin']) echo '<td>&nbsp;'. esc_html( __( 'Location', 'visitor-maps' ) )  .'</td> '; ?>
                  <td>&nbsp;<?php echo esc_html( __( 'Entry', 'visitor-maps' ) ) ; ?></td>
                  <td>&nbsp;<?php echo esc_html( __( 'Last Click', 'visitor-maps' ) ) ; ?></td>
                  <?php
                                    if( ($this->set['allow_last_url_display']) && ( !isset($_GET['nlurl']) ) && ( ( $this->set['allow_profile_display'] ) && ( $show == 'none' ) )  ) {
                    echo '<td>&nbsp;'. esc_html( __( 'Last URL', 'visitor-maps' ) ) .'</td> ';
                  }
                  ?>
                  <?php if ($this->set['allow_referer_display']) echo '<td>&nbsp;'. esc_html( __( 'Referer', 'visitor-maps' ) )  .'</td> '; ?>
               </tr>

<?php
  // Order by is on Last Click.

  $total_bots = 0;
  $total_admin = 0;
  $total_guests = 0;
  $total_users = 0;
  $total_dupes = 0;
  $this->ip_addrs_active = array();
  $ip_addrs = array();
  $whos_online_arr = array();
  $even_odd = 0;

  $whos_online_arr = $wpdb->get_results("SELECT
        session_id,
        ip_address,
        user_id,
        name,
        nickname,
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
        num_visits
            FROM " . $wo_table_wo . "
            WHERE time_last_click > '" . $xx_mins_ago . "'
            ORDER BY time_last_click DESC", ARRAY_A);

  $total_sess = 0;
 if ($whos_online_arr) { // check of there are any visitors
  foreach ($whos_online_arr as $whos_online) {

    // skip empty row just incase
    if ($whos_online['name'] == '' || $whos_online['session_id'] == '' || $whos_online['ip_address'] == '') continue;
    $total_sess++;
    $time_online = ($whos_online['time_last_click'] - $whos_online['time_entry']);

    //Check for duplicates
    if (in_array($whos_online['ip_address'],$ip_addrs)) {$total_dupes++;};
    $ip_addrs[] = $whos_online['ip_address'];

    // Display Status
    // Check who it is and set values
    $is_bot = $is_admin = $is_guest = $is_user = false;

    if ($whos_online['name'] != 'Guest' && $whos_online['user_id'] == 0) {
      $total_bots++;
      $fg_color = $this->set['color_bot'];
      $is_bot = true;

    } else if ($whos_online['name'] != 'Guest' && $whos_online['user_id'] > 0 && $whos_online['ip_address'] != $this->wo_visitor_ip) {
      $total_users++;
      $fg_color =  $this->set['color_user'];
      $is_user = true;

      // Admin detection
    } else if ($whos_online['ip_address'] == $this->wo_visitor_ip) {
      $total_admin++;
      $total_users++;
      $fg_color = $this->set['color_admin'];
      $is_admin = true;
      $this->set['hostname'] = $whos_online['hostname'];

    // Guest detection (may include Bots not detected by spiders.txt)
    } else {
      $fg_color = $this->set['color_guest'];
      $is_guest = true;
      $total_guests++;
    }

  if ( !($is_bot && !$bots) ) {

    // alternate row colors
    $row_class = '';
    $even_class = 'class="column-dark"';
    $odd_class = 'class="column-light"';
    if ($even_odd % 2){
        $row_class = $odd_class;
    } else {
        $row_class = $even_class;
    }
    $even_odd++;

    echo '<tr '.$row_class.'>' . "\n";

?>
        <!-- Status Light -->
        <td align="left" valign="top"><?php echo $this->check_status($whos_online); ?></td>

        <!-- Time Online -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>"><?php echo $this->time_online($time_online); ?></font></td>

        <!-- Name -->
        <?php
        echo '
        <td valign="top">&nbsp;<font color="' . $fg_color .'">';

        if ( $is_guest ){
                 echo esc_html( __( 'Guest', 'visitor-maps' ) ) . '&nbsp;';
        } else if ( $is_user ) {
                 echo '<a href="'.admin_url( 'user-edit.php?user_id='.$whos_online['user_id']).'">'.esc_html( $whos_online['name'] )  . '</a>&nbsp;';
        } else if ( $is_admin ) {
                 echo '<a href="'.admin_url( 'user-edit.php?user_id='.$whos_online['user_id']).'">'.esc_html( __( 'You', 'visitor-maps' ) )  . '</a>&nbsp;';
        // Check for Bot
        } else if ( $is_bot ) {
            // Tokenize UserAgent and try to find Bots name
            $tok = strtok($whos_online['name']," ();/");
            while ($tok !== false) {
              if ( strlen(strtolower($tok)) > 3 )
                if ( !strstr(strtolower($tok), "mozilla") &&
                     !strstr(strtolower($tok), "compatible") &&
                     !strstr(strtolower($tok), "msie") &&
                     !strstr(strtolower($tok), "windows")
                     ) {
                     echo "$tok";
                     break;
                }
                $tok = strtok(" ();/");
              }
              } else {
                      echo esc_html( __( 'Error', 'visitor-maps' ) ) ;
              }
              echo '</font></td>' . "\n";

              if ($this->set['allow_ip_display']) {
              ?>

        <!-- IP Address -->
        <td valign="top">&nbsp;
                <?php
                if ( $whos_online['ip_address'] == 'unknown' ) {
                      echo '<font color="' . $fg_color . '">' . $whos_online['ip_address'] . '</font>' . "\n";
                } else {
                         $this_nick = '';
                         if ($whos_online['nickname'] != '') {
                               $this_nick = ' (' . $this->wo_sanitize_output($whos_online['nickname']) . ' - '.$this->wo_sanitize_output($whos_online['num_visits']).' '.esc_html( __( 'visits', 'visitor-maps' ) ) .')';
                         }
                         if ($visitor_maps_opt['enable_host_lookups']) {
                                 $this_host = ($whos_online['hostname'] != '') ? $this->host_to_domain($whos_online['hostname']) : 'n/a';
                         } else {
                                 $this_host = esc_html( __( 'host lookups not enabled', 'visitor-maps' ) );
                         }

                     if ($visitor_maps_opt['whois_url_popup']) {
                        echo '<a href="'.$visitor_maps_opt['whois_url'] . $whos_online['ip_address'].'" onclick="who_is(this.href); return false;" title="'.$this->wo_sanitize_output($this_host).'">'. $whos_online['ip_address'] . "$this_nick</a>" . "\n";
                     } else {
                        echo '<a href="'. $visitor_maps_opt['whois_url'] . $whos_online['ip_address'] . '" title="'.$this->wo_sanitize_output($this_host).'" target="_blank">'. $whos_online['ip_address'] . "$this_nick</a>" . "\n";
                     }
                }
                echo '</td>';

              } // end if ($this->set['allow_ip_display']

         if ( $visitor_maps_opt['enable_location_plugin'] ) {
        ?>
        <!-- Country Flag -->
        <td valign="top">&nbsp;

        <?php
           if ( $whos_online['country_code'] != '' ) {
              $whos_online['country_code'] = strtolower($whos_online['country_code']);
             if ($whos_online['country_code'] == '--'){ // unknown
                echo '<img src="'.$url_visitor_maps .'images-country-flags/unknown.png" alt="'.esc_attr( __( 'unknown', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'unknown', 'visitor-maps' ) ). '" />';
             } else {
                echo '<img src="'.$url_visitor_maps .'images-country-flags/' . $whos_online['country_code']  . '.png" alt="'.esc_attr($whos_online['country_name']).'" title="'.esc_attr($whos_online['country_name']).'" />';
             }
           }

         if ( $visitor_maps_opt['enable_state_display'] ) {
                 $newguy = false;
                if (is_numeric($refresh) && $whos_online['time_entry'] > (time() - absint($refresh))) {
                   $newguy = true; // Holds the italicized "new lookup" indication for 1 refresh cycle
                 }
             if ($whos_online['city_name'] != '') {
                if ($whos_online['country_code'] == 'us') {
                     $whos_online['print'] = $this->wo_sanitize_output($whos_online['city_name']);
                     if ($whos_online['state_code'] != '')
                             $whos_online['print'] = $this->wo_sanitize_output($whos_online['city_name']) . ', ' . $this->wo_sanitize_output(strtoupper($whos_online['state_code']));
                }
                else {      // all non us countries
                     $whos_online['print'] = $this->wo_sanitize_output($whos_online['city_name']) . ', ' . $this->wo_sanitize_output(strtoupper($whos_online['country_code']));
                }
             }
             else {
                  $whos_online['print'] = '~ ' . $whos_online['country_name'];
             }
             if ($newguy)
                echo '<em>';
             echo '<font color="' . $fg_color . '">  ' . $this->wo_sanitize_output($whos_online['print']) . '</font>';
             if ($newguy)
                echo '</em>';
         }
	   echo '</td>';
         }
        ?>

        <!-- Time Entry -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>"><?php echo date($visitor_maps_opt['time_format_hms'], $whos_online['time_entry']); ?></font></td>

        <!-- Last Click -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>"><?php echo date($visitor_maps_opt['time_format_hms'], $whos_online['time_last_click']); ?></font></td>

              <?php
              if( ($this->set['allow_last_url_display']) && ( !isset($_GET['nlurl']) ) && ( ( $this->set['allow_profile_display'] ) && ( $show == 'none' ) )  ) {
              ?>
        <!-- Last URL -->
        <td valign="top">&nbsp;
                <?php
                $display_link = $whos_online['last_page_url'];
                // escape any special characters to conform to HTML DTD
                $temp_url_link = $display_link;
                $uri = parse_url(get_option('siteurl'));
                isset($uri['path']) and $display_link = str_replace($uri['path'],'',$display_link);
                $display_link = htmlspecialchars($display_link);
                //$display_link = wordwrap($display_link, $this->set['lasturl_wordwrap_chars'], "<br />", true);
                echo '<a href="' . htmlspecialchars($temp_url_link) . '" target="_blank">' . $display_link . '</a>';

        echo '</td>' . "\n";
             } // end if ($this->set['allow_last_url_display']

              if ($this->set['allow_referer_display']) {
              ?>
        <!-- Referer -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>">
                <?php
                if ($whos_online['http_referer'] == '') {
                    echo esc_html( __( 'No', 'visitor-maps' ) ) ;
                }else{
                   echo '<a href="' . htmlspecialchars($whos_online['http_referer']) . '" target="_blank">'.esc_html( __( 'Yes', 'visitor-maps' ) ) .'</a>';
                }
                echo '</font></td>' . "\n";

              } // end if ($this->set['allow_referer_display']

              echo '</tr>' . "\n";
               if( ($this->set['allow_last_url_display']) && ( ( isset($_GET['nlurl']) ) || ( $this->set['allow_profile_display']  &&  $show != 'none'  ) ) ) {
                    echo '<tr '.$row_class.'>' . "\n";
                $uri = parse_url(get_option('siteurl'));
                $display_link = $whos_online['last_page_url'];
                isset($uri['path']) and $display_link = str_replace($uri['path'],'',$display_link);
              ?>

              <td style="text-align:left" colspan="8"><?php echo esc_html( __( 'Last URL:', 'visitor-maps' ) ).' <a href="' . htmlspecialchars($whos_online['last_page_url']) . '" target="_blank">' . htmlspecialchars($display_link) . '</a>';  ?></td>
                    </tr>
              <?php
                }

              if ($this->set['allow_profile_display']) {
                if ( $show == 'all' || ( $show == 'bots' && $is_bot) || ( $show == 'guests' && ( $is_guest || $is_admin || $is_user)) ) {

                    echo "<tr $row_class>\n";
              ?>
                      <td colspan="8"><?php $this->display_details($whos_online); ?></td>
                    </tr>
              <?php
                }
              } // end if ($this->set['allow_profile_display']
        } // closes if (!($is_bot
   } // closes while ($whos_online
  } // closes if ($whos_online_arr)
?>
                        <tr>
                          <td colspan="9"><br />
                            <table border="0" cellpadding="0" cellspacing="3" width="600">
                              <tr>
                              <td align="right"><?php print "$total_sess" ?></td>
                              <td align="left"><?php echo sprintf( __( 'Visitors online (Considered inactive after %1$d minutes. Removed after %2$d minutes)', 'visitor-maps'),absint($visitor_maps_opt['active_time']),absint($visitor_maps_opt['track_time'])  );?></td>
                                </tr>
                                <?php
                                if ($total_dupes > 0) {
                                ?>
                                <tr>
                                    <td align="right"><?php print "$total_dupes" ?></td>
                                    <td align="left""><?php echo esc_html( __( 'Duplicate IPs', 'visitor-maps' ) ); ?></td>
                                </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td align="right"><?php print "$total_users" ?></td>
                                    <td><?php echo esc_html( __( 'Members (includes you)', 'visitor-maps' ) ); ?></td>
                                </tr>
                                <tr>
                                    <td align="right"><?php print "$total_guests" ?></td>
                                    <td><?php echo esc_html( __( 'Guests', 'visitor-maps' ) ); if(count($this->ip_addrs_active) > 0) echo ', <font color="' . $this->set['color_guest'] . '">' . count($this->ip_addrs_active) . ' '.esc_html( __( 'are active', 'visitor-maps' ) ) . '</font>'; ?></td>
                                </tr>
                                <tr>
                                    <td align="right"><?php print "$total_bots" ?></td>
                                    <td><?php echo esc_html( __( 'Bots', 'visitor-maps' ) ); ?></td>
                                </tr>
                                <tr>
                                <td align="right"><?php print "$total_admin" ?></td>
                                <td><?php echo esc_html( __( 'You', 'visitor-maps' ) ); ?></td>
                              </tr>
                            </table>
                            <br />
                            <?php
                            if ($this->set['allow_ip_display']) {
                              echo esc_html( __( 'Your IP Address:', 'visitor-maps' ) ) . ' '.$this->wo_sanitize_output($this->wo_visitor_ip);
                            }
                            if ($visitor_maps_opt['enable_host_lookups']) {
                              $this_host = (isset($this->set['hostname']) && $this->set['hostname'] != '') ? $this->host_to_domain($this->set['hostname']) : 'n/a';
                              // Display Hostname
                              echo '<br />
                              '.esc_html( __( 'Your Host:', 'visitor-maps' ) ).' (' . $this->wo_sanitize_output($this_host) . ') '. $this->wo_sanitize_output((isset($this->set['hostname']) && $this->set['hostname'] != '') ? $this->set['hostname'] : 'n/a');
                            }

                            //------------------------ geoip lookup -------------------------
                            if ( $visitor_maps_opt['enable_location_plugin'] ) {
                               echo '<p>'.esc_html( __( 'Uses GeoLiteCity data created by MaxMind, available from http://www.maxmind.com', 'visitor-maps' ) ).'<br />';
                               if( $geoip_old ){
                                   echo '<span style="color:red">'.
                                   sprintf( __('The GeoLiteCity data was last updated on %1$s (%2$d days ago)','visitor-maps'),date($visitor_maps_opt['geoip_date_format'], $geoip_file_time),$geoip_days_ago).' '.
                                   esc_html( __( 'an update is available', 'visitor-maps' ) ).',
                                   <a href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&do_geo=1">'.esc_html( __( 'click here to update', 'visitor-maps' ) ).'</a></span>';
                               } else {
                                   echo sprintf(__('The GeoLiteCity data was last updated on %1$s (%2$d days ago)','visitor-maps'),date($visitor_maps_opt['geoip_date_format'], $geoip_file_time),$geoip_days_ago);                                               ;
                               }
                               echo '</p>';
                            }
                            //------------------------ geoip lookup -------------------------
                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>

                  </tr>
                </table>
              </td>
            </tr>
          </table>

<?php

} // end function view_whos_online

// Determines status of visitor and displays appropriate icon.
  function check_status($whos_online) {
    global $wpdb,$visitor_maps_opt, $path_visitor_maps, $url_visitor_maps;

    // Determine if visitor active/inactive
    $xx_mins_ago_long = (time() - ($visitor_maps_opt['active_time'] * 60));

    if ($whos_online['name'] != 'Guest' && $whos_online['user_id'] == 0) {   // bot
      // inactive bot
      if ($whos_online['time_last_click'] < $xx_mins_ago_long) {
        return '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_inactive_bot'].'" border="0" alt="'.esc_attr( __( 'Inactive Bot', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Inactive Bot', 'visitor-maps' ) ).'" />';
        // active  bot
      } else {
        return '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_active_bot'].'" border="0" alt="'.esc_attr( __( 'Active Bot', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Active Bot', 'visitor-maps' ) ).'" />';
      }

    }else{  // guest
      // inactive guest
      if ($whos_online['time_last_click'] < $xx_mins_ago_long) {
        return '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_inactive_guest'].'" border="0" alt="'.esc_attr( __( 'Inactive Guest', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Inactive Guest', 'visitor-maps' ) ).'" />';
      // active guest
      } else {
            // next 3 lines count active guests without duplicates
            if (!in_array($whos_online['ip_address'],$this->ip_addrs_active)) {
             $whos_online['ip_address'] != $this->wo_visitor_ip and $this->ip_addrs_active[] = $whos_online['ip_address'];
            }
        return '<img src="'.$url_visitor_maps . 'images/' .$this->set['image_active_guest'].'" border="0" alt="'.esc_attr( __( 'Active Guest', 'visitor-maps' ) ).'" title="'.esc_attr( __( 'Active Guest', 'visitor-maps' ) ).'" />';
      }
    }
  } // end function check_status


  // Display the details about a visitor
  function display_details($whos_online) {
    global $visitor_maps_opt;

    // Display User Agent
    echo esc_html( __( 'User Agent:', 'visitor-maps' ) ) . ' ' .  wordwrap($this->wo_sanitize_output($whos_online['user_agent']), $this->set['useragent_wordwrap_chars'] , "<br />", true);
    echo '<br />';

    if ($visitor_maps_opt['enable_host_lookups']) {
      $this_host = ($whos_online['hostname'] != '') ? $this->host_to_domain($whos_online['hostname']) : 'n/a';
      // Display Hostname
      echo esc_html( __( 'Host:', 'visitor-maps' ) ) . ' (' . $this->wo_sanitize_output($this_host) . ') '. $this->wo_sanitize_output($whos_online['hostname']);
      echo '<br />';
    }

    // Display Referer if available
    if($whos_online['http_referer'] != '' ) {
      echo esc_html( __( 'Referer:', 'visitor-maps' ) ) . ' <a href="' . htmlspecialchars($whos_online['http_referer']) . '" target="_blank">' . wordwrap(htmlspecialchars($whos_online['http_referer']), $this->set['referer_wordwrap_chars'], '<br />', true) . '</a>';
      echo '<br />';
    }
    echo '<br clear="all" />';

  } // end function display_details

// Output a form pull down menu
  function draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    global $_GET, $_POST;

    $field = '<select name="' . $this->wo_output_string($name) . '"';

    if ($this->wo_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>'."\n";

    if (empty($default) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $default = stripslashes($_GET[$name]);
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $default = stripslashes($_POST[$name]);
      }
    }

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . $this->wo_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . $this->wo_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>'."\n";
    }
    $field .= '</select>'."\n";

    if ($required == true) $field .= 'Required';

    return $field;
  }

function time_online ($time_online) {
    // takes a time diff in secs and formats to 01:48:08  (hrs:min:secs)
    $hrs = (int) intval($time_online / 3600);
    $time_online = (int) intval($time_online - (3600 * $hrs));
    $mns = (int) intval($time_online / 60);
    $time_online = (int) intval($time_online - (60 * $mns));
    $secs = (int) intval($time_online / 1);
    return sprintf("%02d:%02d:%02d", $hrs, $mns, $secs);
 }

function check_geoip_date($geoip_file_time) {
   global $visitor_maps_opt, $wpdb, $path_visitor_maps;

  // checking for a newer maxmind geo database update file
  // Maxmind usually updates their file on the 1st of the month, but sometimes it is the 2nd, or 3rd of the month.
  // Now it only notifies you when there actually is a new file available.

  $wo_table_ge = $wpdb->prefix . 'visitor_maps_ge';

  // check timestamp
  $time_last_check = $wpdb->get_var("SELECT time_last_check FROM " . $wo_table_ge);

  // was a timestamp there?
  if (!$time_last_check ) {
     // jump start the timestamp now
     //echo "jump starting the timestamp now...<br />";
     $time_last_check   = time() - (7 * 60*60);
     $wpdb->query("INSERT INTO " . $wo_table_ge . " (`time_last_check`) VALUES ('" .absint($time_last_check ) . "');");
  }

  // have I checked this already in the last 6 hours?
  if ($time_last_check < time() - (6 * 60*60) ) { // $time_last_check more than 6 hours ago
           // time to check it again, reset the needs_update flag first
           //echo "resetting the needs_update flag...<br />";
           $wpdb->query("UPDATE " . $wo_table_ge . " SET needs_update = '0'");

           // get last updated time of the maxmind geo database remote file
           // echo "checking the maxmind timestamp now...<br />";
           $remote_file_time = $this->curl_last_mod('http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz');
  } else {
          // using the cached results
          // check needs_update flag
          $update_flag  = $wpdb->get_var("SELECT needs_update FROM " . $wo_table_ge);
          if ($update_flag == 1) {
                  //echo "needs update (cached result)...<br />";
                  return 1;
          } else {
                 //echo "does not need update(cached result from less than 6 hours ago)...<br />";
                 return 0;
          }
  }

  // set a new timestamp
  //echo "set a new timestamp (now)...<br />";
  $wpdb->query("UPDATE " . $wo_table_ge . " SET time_last_check = '" . time() . "'");

  // sanity check the remote date
  if ($remote_file_time < (time() - (365*24*60*60)) ) { // $remote_file_time less than 1 year ago
           echo "Warning: The last modified date of the Maxmind GeoLiteCity database ($remote_file_time) is out of expected range<br />";
           return 0;
  }
  if ($remote_file_time > $geoip_file_time ) {
         //echo "needs update...<br />";
         // set needs_update flag
         $wpdb->query("UPDATE " . $wo_table_ge . " SET needs_update = '1'");
         return 1;
  }
  //echo "does not need update...<br />";
  return 0;
} // end function check_geoip_date


function curl_last_mod($remote_file) {
    // return unix timestamp (last_modified) from a remote URL file

    if ( !function_exists('curl_init') ) {
       return $this->http_last_mod($remote_file,1);
    }

    $last_modified = $ch = $resultString = $headers = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)');
    curl_setopt($ch, CURLOPT_URL, $remote_file);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 5 sec timeout
    curl_setopt($ch, CURLOPT_HEADER, 1);  // make sure we get the header
    curl_setopt($ch, CURLOPT_NOBODY, 1);  // make it a http HEAD request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // write the response to a variable
    curl_setopt($ch, CURLOPT_FILETIME, 1 );

    $i = 1;
    while ($i++ <= 2) {
       if(curl_exec($ch) === false){
               $this->error_exit('curl_last_mod '. __( 'error: could not connect to remote file', 'visitor-maps' )); // could not connect
               //   echo 'Curl error: ' . curl_error($ch);
               //   exit;
       }
       $headers = curl_getinfo($ch);
       if ($headers['http_code'] != 200) {
          sleep(3);  // Let's wait 3 seconds to see if its a temporary network issue.
       } else if ($headers['http_code'] == 200) {
          // we got a good response, drop out of loop.
          break;
       }
    }
    $last_modified = $headers['filetime'];
    if ($headers['http_code'] != 200) $this->error_exit('curl_last_mod '. __( 'error: fetching timestamp failed for URL, 404 not found?', 'visitor-maps' )); // remote file not found
    curl_close ($ch);

  // sanity check the remote_file date
  // sometimes CURL returns -1 instead of the timestamp on some peoples servers
  // use http to check the date instead.
  if ($last_modified < (time() - (365*24*60*60)) ) { // $remote_file_time less than 1 year ago
       return $this->http_last_mod($remote_file,1);
  }

    return $last_modified;
} // end of curl_last_mod function

function http_last_mod($url,$format=0) {
  $url_info=parse_url($url);
  $port = isset($url_info['port']) ? $url_info['port'] : 80;
  $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 15);
  if($fp) {
    $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
    $head .= " HTTP/1.0\r\n";
    $head .= "Host: ".@$url_info['host']."\r\n";
    $head .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)\r\n\r\n";
    fputs($fp, $head);
    while(!feof($fp)) {
      if($header=trim(fgets($fp, 1024))) {
        if($format == 1) {
          $h2 = explode(': ',$header);
          // the first element is the http header type, such as HTTP/1.1 200 OK,
          // it doesn't have a separate name, so we have to check for it.
          if($h2[0] == $header) {
            $headers['status'] = $header;
              if (! preg_match('|HTTP/1.* 200 OK|i',$header)) {
                $this->error_exit('http_last_mod'. __( 'error: fetching timestamp failed for URL 404 not found?', 'visitor-maps' ));
              }
          } else {
            $headers[strtolower($h2[0])] = trim($h2[1]);
          }
        } else {
          $headers[] = $header;
        }
      }
    }
          fclose($fp);
          return strtotime($headers['last-modified']);
  } else {
         $this->error_exit('http_last_mod'. __( 'error: could not connect to remote URL', 'visitor-maps' ));
  }
} // end of function http_last_mod

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


function error_exit($error) {

   echo "$error<br />";
   return;

} // end function error_exit

} // end class
