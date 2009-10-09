<?php
/*
Visitor Maps PHP Script by Mike Challis
Free PHP Scripts - www.642weather.com/weather/scripts.php
*/

class WoBeen {
    var $wo_visitor_ip;
    var $ip_addrs_active;
    var $set;

function view_whos_been_online() {
  global $wpdb, $visitor_maps_opt, $url_visitor_maps, $path_visitor_maps;

  $wo_table_wo = $wpdb->prefix . 'visitor_maps_wo';


  $show_arr = array();
  $show_arr[] = array('id' => '',       'text' => esc_attr( __( 'None', 'visitor-maps' ) ));
  $show_arr[] = array('id' => 'all',    'text' => esc_attr( __( 'All', 'visitor-maps' ) ));
  $show_arr[] = array('id' => 'bots',   'text' => esc_attr( __( 'Bots', 'visitor-maps' ) ));
  $show_arr[] = array('id' => 'guests', 'text' => esc_attr( __( 'Guests', 'visitor-maps' ) ));

  $show = '';
  if ( isset($_GET['show']) && $this->validate_show($_GET['show'])) {
    $show = $_GET['show'];
  }

  $sort_by_arr = array();
  $sort_by_arr[] = array('id' => 'who',      'text' => esc_attr( __( 'Who', 'visitor-maps' ) ));
  $sort_by_arr[] = array('id' => 'visits',   'text' => esc_attr( __( 'Visits', 'visitor-maps' ) ));
  $sort_by_arr[] = array('id' => 'time',     'text' => esc_attr( __( 'Last Visit', 'visitor-maps' ) ));
  $sort_by_arr[] = array('id' => 'ip',       'text' => esc_attr( __( 'IP Address', 'visitor-maps' ) ));
  $sort_by_arr[] = array('id' => 'location', 'text' => esc_attr( __( 'Location', 'visitor-maps' ) ));
  $sort_by_arr[] = array('id' => 'url',      'text' => esc_attr( __( 'Last URL', 'visitor-maps' ) ));

  $sort_by_ar = array();
  $sort_by_ar['who'] = 'name';
  $sort_by_ar['visits'] = 'num_visits';
  $sort_by_ar['time'] = 'time_last_click';
  $sort_by_ar['ip'] = 'ip_address';
  $sort_by_ar['location'] = 'country_name, city_name';
  $sort_by_ar['url'] = 'last_page_url';

  $sort_by = 'time';
  if ( isset($_GET['sort_by']) && $this->validate_sort_by($_GET['sort_by'])) {
    $sort_by = $_GET['sort_by'];
  }

  $order_arr = array();
  $order_arr[] = array('id' => 'desc', 'text' => esc_attr( __( 'Descending', 'visitor-maps' ) ));
  $order_arr[] = array('id' => 'asc',  'text' => esc_attr( __( 'Ascending', 'visitor-maps' ) ));

  $order_ar = array();
  $order_ar['desc'] = 'DESC';
  $order_ar['asc'] = 'ASC';

  $order = 'desc';
  if ( isset($_GET['order']) && $this->validate_order($_GET['order'])) {
    $order = $_GET['order'];
  }

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
  $this->set['geolite_path'] = dirname(__FILE__).'/';
  $this->wo_visitor_ip = $this->get_ip_address();


  // http://www.tonymarston.net/php-mysql/pagination.html
  if (isset($_GET['pageno']) && is_numeric($_GET['pageno'])) {
     $pageno = $_GET['pageno'];
  } else {
     $pageno = 1;
  } // if

  $numrows = $wpdb->get_var("SELECT count(*) FROM " . $wo_table_wo);
  $since = $wpdb->get_var("SELECT time_last_click FROM " . $wo_table_wo ." ORDER BY time_last_click ASC LIMIT 1");
  $rows_per_page = 25;
  $lastpage      = ceil($numrows/$rows_per_page);
  $pageno = (int)$pageno;
  if ($pageno > $lastpage) {
      $pageno = $lastpage;
  }
  if ($pageno < 1) {
     $pageno = 1;
  }
  $limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
  $getstring = '&amp;show='.$show.'&amp;order='.$order.'&amp;sort_by='.$sort_by;

echo '<table border="0" width="99%">
 <tr><td>
  <form name="wo_been" action="'.admin_url( 'index.php?page=whos-been-online' ).'" method="get">';
  if ($this->set['allow_profile_display']) echo esc_html( __( 'Profile Display:', 'visitor-maps' ) ). ' ' . $this->draw_pull_down_menu('show', $show_arr, $show, 'onchange="this.form.submit();"') . ' ';
  echo esc_html( __( 'Sort:', 'visitor-maps' ) ). ' ' . $this->draw_pull_down_menu('sort_by', $sort_by_arr, $sort_by, 'onchange="this.form.submit();"').' ';
  echo  $this->draw_pull_down_menu('order', $order_arr, $order, 'onchange="this.form.submit();"') . '<br />';
  echo '<input type="hidden" name="page" value="whos-been-online" />
  </form>
  <a href="'.admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php').'">' . esc_html( __( 'Visitor Maps Options', 'visitor-maps' ) ) . "</a>\n";
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
     <b><?php echo sprintf(__('%1$d visitors since %2$s', 'visitor-maps'),(int)$numrows,date($visitor_maps_opt['date_time_format'],(int)$since)); ?></b>
   </td>
 </tr>
  <tr>
   <td align="left">
     <?php
   if ($pageno == 1) {
     echo ' &laquo;'.__( 'FIRST', 'visitor-maps' ).' &lsaquo;'.__( 'PREV', 'visitor-maps' );
   } else {
       echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=1$getstring>&laquo;".__( 'FIRST', 'visitor-maps' )."</a> ";
          $prevpage = $pageno-1;
       echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$prevpage$getstring>&lsaquo;".__( 'PREV', 'visitor-maps' )."</a> ";
   } // if
    echo ' ('.sprintf(__('Page %1$d of %2$d','visitor-maps'),$pageno, $lastpage).') ';
   if ($pageno == $lastpage) {
      echo " ".__( 'NEXT', 'visitor-maps' )."&rsaquo; ".__( 'LAST', 'visitor-maps' )."&raquo; ";
   } else {
     $nextpage = $pageno+1;
     echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$nextpage$getstring>".__( 'NEXT', 'visitor-maps' )."&rsaquo;</a> ";
     echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$lastpage$getstring>".__( 'LAST', 'visitor-maps' )."&raquo;</a> ";
   } // if

   ?>
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
                  <td>&nbsp;<?php echo esc_html( __( 'Who', 'visitor-maps' ) ); ?></td>
                  <td>&nbsp;<?php echo esc_html( __( 'Visits', 'visitor-maps' ) ) ; ?></td>
                  <td>&nbsp;<?php echo esc_html( __( 'Last Visit', 'visitor-maps' ) ) ; ?></td>
                  <?php if ($this->set['allow_ip_display']) echo '<td>&nbsp;'. esc_html( __( 'IP Address', 'visitor-maps' ) ) .'</td> '; ?>
                  <?php if ($visitor_maps_opt['enable_location_plugin']) echo '<td>&nbsp;'. esc_html( __( 'Location', 'visitor-maps' ) )  .'</td> '; ?>

                  <?php
                                    if( ($this->set['allow_last_url_display']) && ( !isset($_GET['nlurl']) ) && ( ( $this->set['allow_profile_display'] ) && ( !isset($_GET['show']) || $_GET['show'] == '' ) )  ) {
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
            ORDER BY ".$sort_by_ar[$sort_by]." ".$order_ar[$order]." $limit", ARRAY_A);


  $total_sess = 0;
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

  if (!($is_bot && !isset($_GET['bots']))) {

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


        <!-- Visits -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>"><?php echo $whos_online['num_visits'] ?></font></td>

        <!-- Last Visit -->
        <td valign="top">&nbsp;<font color="<?php echo $fg_color; ?>"><?php echo date($visitor_maps_opt['date_time_format'], $whos_online['time_last_click']); ?></font></td>



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
                        echo '<a href="'.$visitor_maps_opt['whois_url'] . $whos_online['ip_address'].'" onc="who_is(this.href); return false;" title="'.$this->wo_sanitize_output($this_host).'">'. $whos_online['ip_address'] . "$this_nick</a>" . "\n";
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
                if (isset($_GET['refresh']) && is_numeric($_GET['refresh']) && $whos_online['time_entry'] > (time() - absint($_GET['refresh']))) {
                   $newguy = true; // Holds the italicized "new lookup" indication for 1 refresh cycle
                 }
             if ($whos_online['city_name'] != '') {
                if ($whos_online['country_name'] == 'United States') {
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


              <?php
              if( ($this->set['allow_last_url_display']) && ( !isset($_GET['nlurl']) ) && ( ( $this->set['allow_profile_display'] ) && ( !isset($_GET['show']) || $_GET['show'] == '' ) )  ) {
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
               if( ($this->set['allow_last_url_display']) && ( ( isset($_GET['nlurl']) ) || ( ( $this->set['allow_profile_display'] ) && ( isset($_GET['show']) && $_GET['show'] != '' ) ))  ) {
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
                if ( (isset($_GET['show']) && $_GET['show'] == 'all') || (( isset($_GET['show']) && $_GET['show'] == 'bots') && $is_bot) || (( isset($_GET['show']) && $_GET['show'] == 'guests') && ( $is_guest || $is_admin || $is_user)) ) {

                    echo "<tr $row_class>\n";
              ?>
                      <td colspan="8"><?php $this->display_details($whos_online); ?></td>
                    </tr>
              <?php
                }
              } // end if ($this->set['allow_profile_display']
        } // closes if (!($is_bot
   } // closes while ($whos_online
?>

                      </table>
                    </td>

                  </tr>
                </table>
              </td>
            </tr>
              <tr>
   <td align="left">
     <?php
   if ($pageno == 1) {
     echo ' &laquo;'.__( 'FIRST', 'visitor-maps' ).' &lsaquo;'.__( 'PREV', 'visitor-maps' );
   } else {
       echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=1$getstring>&laquo;".__( 'FIRST', 'visitor-maps' )."</a> ";
          $prevpage = $pageno-1;
       echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$prevpage$getstring>&lsaquo;".__( 'PREV', 'visitor-maps' )."</a> ";
   } // if
    echo ' ('.sprintf(__('Page %1$d of %2$d','visitor-maps'),$pageno, $lastpage).') ';
   if ($pageno == $lastpage) {
      echo " ".__( 'NEXT', 'visitor-maps' )."&rsaquo; ".__( 'LAST', 'visitor-maps' )."&raquo; ";
   } else {
     $nextpage = $pageno+1;
     echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$nextpage$getstring>".__( 'NEXT', 'visitor-maps' )."&rsaquo;</a> ";
     echo " <a href=".admin_url( 'index.php?page=whos-been-online' )."&amp;pageno=$lastpage$getstring>".__( 'LAST', 'visitor-maps' )."&raquo;</a> ";
   } // if

   ?>
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

 function validate_sort_by($string) {
 // only allow if in array
  $allowed = array('who','visits','time','ip','location','url');
 if ( in_array($string, $allowed) ) {
    return true;
 }
 return false;
} // end function validate_sort_by

 function validate_order($string) {
 // only allow if in array
  $allowed = array('desc','asc');
 if ( in_array($string, $allowed) ) {
    return true;
 }
 return false;
} // end function validate_order

 function validate_show($string) {
 // only allow if in array
  $allowed = array('all','bots','guests');
 if ( in_array($string, $allowed) ) {
    return true;
 }
 return false;
} // end function validate_show

function error_exit($error) {

   echo "$error<br />";
   return;

} // end function error_exit

} // end class

?>