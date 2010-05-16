 <?php

// the visitor maps admin settings page

 if (isset($_GET['do_geo']) ) {
          if ( function_exists('current_user_can') && !current_user_can('manage_options') )
                        die(__('You do not have permissions for managing this option', 'visitor-maps'));

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
          if ( function_exists('current_user_can') && !current_user_can('manage_options') )
                        die(__('You do not have permissions for managing this option', 'visitor-maps'));
   check_admin_referer( 'visitor-maps-options_update'); // nonce
   // post changes to the options array
   $optionarray_update = array(

   'donated' =>                  (isset( $_POST['visitor_maps_donated'] ) ) ? 1 : 0,
   'active_time' =>          absint(trim($_POST['visitor_maps_active_time'])),
   'track_time' =>           absint(trim($_POST['visitor_maps_track_time'])),
   'store_days' =>          ( is_numeric(trim($_POST['visitor_maps_store_days'])) && trim($_POST['visitor_maps_store_days']) <= 10000 ) ? absint(trim($_POST['visitor_maps_store_days'])) : $visitor_maps_option_defaults['store_days'],
   'hide_administrators' =>      (isset( $_POST['visitor_maps_hide_administrators'] ) ) ? 1 : 0,
   'ips_to_ignore' =>               trim($_POST['visitor_maps_ips_to_ignore']),  // can be empty
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
   'hide_text_on_worldmap' =>    (isset( $_POST['visitor_maps_hide_text_on_worldmap'] ) ) ? 1 : 0,
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

<form name="formoptions" method="post" action="<?php echo admin_url( "plugins.php?page=visitor-maps/visitor-maps.php&amp;updated=true" ); ?>">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="visitor_maps_set" value="1" />
        <input type="hidden" name="form_type" value="upload_options" />
        <?php wp_nonce_field('visitor-maps-options_update'); ?>

    <input name="visitor_maps_donated" id="visitor_maps_donated" type="checkbox" <?php if( $visitor_maps_opt['donated'] ) echo 'checked="checked"'; ?> />
    <label for="visitor_maps_donated"><?php echo esc_html( __('I have donated to help contribute for the development of this plugin.', 'visitor-maps')); ?></label>
    <br />

<h3><?php _e('Usage', 'visitor-maps') ?></h3>
	<p>
    <?php echo __('Add the shortcode <b>[visitor-maps]</b> in a Page(not a Post). That page will become your Visitor Maps page.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-6.gif" target="_new"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
    </p>
   	<p>
    <?php echo __('Add the Who\'s Online sidebar. Click on Appearance, Widgets, then drag the Who\'s Online widget to the sidebar column on the right.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-7.gif" target="_new"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
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

      <input name="visitor_maps_hide_text_on_worldmap" id="visitor_maps_hide_text_on_worldmap" type="checkbox" <?php if( $visitor_maps_opt['hide_text_on_worldmap'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_text_on_worldmap"><?php echo esc_html( __('Disable text on geolocation maps (missing map background fix).', 'visitor-maps')); ?></label>
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_hide_text_on_worldmap_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_hide_text_on_worldmap_tip">
      <?php echo esc_html( __('Some PHP servers do not have full support for printing text on the Visitor Map image. Only if the Visitor Map just displays pins and no image for the world or countries, select this setting. After selecting this setting, check your visitor maps page to see if the map is now working.', 'visitor-maps')); ?>
      </div>
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
      <?php echo esc_html( __('Minutes that a visitor is considered "active". Default is 5 minutes.', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_track_time"><?php echo esc_html( __('Inactive time (minutes)', 'visitor-maps')); ?>:</label><input name="visitor_maps_track_time" id="visitor_maps_track_time" type="text" value="<?php echo absint($visitor_maps_opt['track_time']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_track_time_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_track_time_tip">
      <?php echo esc_html( __('Minutes until an inactive visitor is removed from display. Default is 15 minutes.', 'visitor-maps')); ?>
      </div>
      <br />

      <label for="visitor_maps_store_days"><?php echo esc_html( __('Days to store visitor data', 'visitor-maps')); ?>:</label><input name="visitor_maps_store_days" id="visitor_maps_store_days" type="text" value="<?php echo absint($visitor_maps_opt['store_days']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor-maps')); ?>" onclick="toggleVisibility('visitor_maps_store_days_tip');"><?php echo esc_html( __('help', 'visitor-maps')); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_store_days_tip">
      <?php echo esc_html( __('Days to store visitor data in database table. This data is used for the geolocation maps. Default is 30 days.', 'visitor-maps')); ?>
      </div>

      <br />
      <input name="visitor_maps_hide_administrators" id="visitor_maps_hide_administrators" type="checkbox" <?php if( $visitor_maps_opt['hide_administrators'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_administrators"><?php echo esc_html( __('Do not show administrators on the maps.', 'visitor-maps')); ?></label>

      <br />
      <label name="visitor_maps_ips_to_ignore" for="visitor_maps_ips_to_ignore"><?php echo esc_html( __('IP Adresses to ignore', 'si-contact-form')); ?>:</label>
      <a style="cursor:pointer;" title="<?php echo esc_html( __('Click for Help!', 'visitor_maps')); ?>" onclick="toggleVisibility('visitor_maps_ips_to_ignore_tip');"><?php echo esc_html( __('help', 'visitor_maps')); ?></a> <br />
      <div style="text-align:left; display:none" id="visitor_maps_ips_to_ignore_tip">
        <?php _e('Optional list of IP addresses for visitors you do not want shown on maps.', 'visitor_maps') ?><br />
        <?php _e('Start each entry on a new line.', 'visitor_maps'); ?><br />
        <?php _e('Use <strong>*</strong> for wildcards.', 'visitor_maps'); ?><br />
		<?php _e('Examples:', 'visitor_maps'); ?>
		<p style="margin: 2px 0"><span dir="ltr">192.168.1.100</span></p>
		<p style="margin: 2px 0"><span dir="ltr">192.168.1.*</span></p>
		<p style="margin: 2px 0"><span dir="ltr">192.168.*.*</span></p>
      </div>
      <textarea rows="4" cols="20" name="visitor_maps_ips_to_ignore" id="visitor_maps_ips_to_ignore"><?php echo $visitor_maps_opt['ips_to_ignore']; ?></textarea>

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
      <label for="visitor_maps_enable_credit_link"><?php echo esc_html( __('Enable plugin credit link:', 'visitor-maps')) ?></label> <small><?php echo __('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps'); ?></a></small>

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
