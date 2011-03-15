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
   'dashboard_permissions' =>      (trim($_POST['visitor_maps_dashboard_permissions']) != '' ) ? trim($_POST['visitor_maps_dashboard_permissions']) : $visitor_maps_option_defaults['visitor_maps_dashboard_permissions'], // use default if empty
   'ips_to_ignore' =>               trim($_POST['visitor_maps_ips_to_ignore']),  // can be empty
   'urls_to_ignore' =>              trim($_POST['visitor_maps_urls_to_ignore']),  // can be empty
   'time_format' =>               ( trim($_POST['visitor_maps_time_format']) != '' ) ? trim($_POST['visitor_maps_time_format']) : $visitor_maps_option_defaults['time_format'], // use default if empty
   'time_format_hms' =>           ( trim($_POST['visitor_maps_time_format_hms']) != '' ) ? trim($_POST['visitor_maps_time_format_hms']) : $visitor_maps_option_defaults['time_format_hms'],
   'date_time_format' =>          ( trim($_POST['visitor_maps_date_time_format']) != '' ) ? trim($_POST['visitor_maps_date_time_format']) : $visitor_maps_option_defaults['date_time_format'],
   'geoip_date_format' =>         ( trim($_POST['visitor_maps_geoip_date_format']) != '' ) ? trim($_POST['visitor_maps_geoip_date_format']) : $visitor_maps_option_defaults['geoip_date_format'],
   'whois_url' =>                 ( trim($_POST['visitor_maps_whois_url']) != '' ) ? trim($_POST['visitor_maps_whois_url']) : $visitor_maps_option_defaults['whois_url'], // use default if empty
   'whois_url_popup' =>          (isset( $_POST['visitor_maps_whois_url_popup'] ) ) ? 1 : 0,
   'enable_host_lookups' =>      (isset( $_POST['visitor_maps_enable_host_lookups'] ) ) ? 1 : 0,
   'enable_location_plugin' =>   (isset( $_POST['visitor_maps_enable_location_plugin'] ) ) ? 1 : 0,
   'enable_state_display' =>     (isset( $_POST['visitor_maps_enable_state_display'] ) ) ? 1 : 0,
   'hide_bots' =>                (isset( $_POST['visitor_maps_hide_bots'] ) ) ? 1 : 0,
   'hide_console' =>             (isset( $_POST['visitor_maps_hide_console'] ) ) ? 1 : 0,
   'combine_members' =>          (isset( $_POST['visitor_maps_combine_members'] ) ) ? 1 : 0,
   'hide_text_on_worldmap' =>    (isset( $_POST['visitor_maps_hide_text_on_worldmap'] ) ) ? 1 : 0,
   'enable_visitor_map_hover' => (isset( $_POST['visitor_maps_enable_visitor_map_hover'] ) ) ? 1 : 0,
   'enable_users_map_hover' => (isset( $_POST['visitor_maps_enable_users_map_hover'] ) ) ? 1 : 0,
   'enable_blog_footer' =>       (isset( $_POST['visitor_maps_enable_blog_footer'] ) ) ? 1 : 0,
   'enable_admin_footer' =>      (isset( $_POST['visitor_maps_enable_admin_footer'] ) ) ? 1 : 0,
   'enable_records_page' =>      (isset( $_POST['visitor_maps_enable_records_page'] ) ) ? 1 : 0, 
   'enable_widget_link' =>       (isset( $_POST['visitor_maps_enable_widget_link'] ) ) ? 1 : 0,
   'enable_credit_link' =>       (isset( $_POST['visitor_maps_enable_credit_link'] ) ) ? 1 : 0,
   'enable_dash_map' =>          (isset( $_POST['visitor_maps_enable_dash_map'] ) ) ? 1 : 0,
   'pins_limit' =>           absint(trim($_POST['visitor_maps_pins_limit'])),
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
<a href="http://wordpress.org/extend/plugins/visitor-maps/changelog/" target="_blank"><?php echo __('Changelog', 'visitor-maps'); ?></a> |
<a href="http://wordpress.org/extend/plugins/visitor-maps/faq/" target="_blank"><?php echo __('FAQ', 'visitor-maps'); ?></a> |
<a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_blank"><?php echo __('Rate This', 'visitor-maps'); ?></a> |
<a href="http://wordpress.org/tags/visitor-maps?forum_id=10" target="_blank"><?php echo __('Support', 'visitor-maps'); ?></a> |
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V3BPEZ9WGYEYG" target="_blank"><?php echo __('Donate', 'visitor-maps'); ?></a> |
<a href="http://www.642weather.com/weather/scripts.php" target="_blank"><?php echo __('Free PHP Scripts', 'visitor-maps'); ?></a> |
<a href="http://www.642weather.com/weather/wxblog/support/" target="_blank"><?php echo __('Contact', 'visitor-maps'); ?> Mike Challis</a>
</p>

<?php
if (function_exists('get_transient')) {
  require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

  // Before, try to access the data, check the cache.
  if (false === ($api = get_transient('visitor_maps_info'))) {
    // The cache data doesn't exist or it's expired.

    $api = plugins_api('plugin_information', array('slug' => stripslashes( 'visitor-maps' ) ));

    if ( !is_wp_error($api) ) {
       // cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
       $myexpire = 60 * 15; // Cache data for 15 minutes
       set_transient('visitor_maps_info', $api, $myexpire);
    }
  }
  if ( !is_wp_error($api) ) {
	$plugins_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
								'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
								'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
								'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
								'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
								'img' => array('src' => array(), 'class' => array(), 'alt' => array()));
	//Sanitize HTML
	foreach ( (array)$api->sections as $section_name => $content )
		$api->sections[$section_name] = wp_kses($content, $plugins_allowedtags);
	foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
		$api->$key = wp_kses($api->$key, $plugins_allowedtags);

      if ( ! empty($api->downloaded) ) {
        echo sprintf(__('Downloaded %s times', 'visitor-maps'),number_format_i18n($api->downloaded));
        echo '.';
      }
?>
		<?php if ( ! empty($api->rating) ) : ?>
		<div class="star-holder" title="<?php echo esc_attr(sprintf(__('(Average rating based on %s ratings)', 'visitor-maps'),number_format_i18n($api->num_ratings))); ?>">
			<div class="star star-rating" style="width: <?php echo esc_attr($api->rating) ?>px"></div>
			<div class="star star5"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('5 stars', 'visitor-maps') ?>" /></div>
			<div class="star star4"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('4 stars', 'visitor-maps') ?>" /></div>
			<div class="star star3"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('3 stars', 'visitor-maps') ?>" /></div>
			<div class="star star2"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('2 stars', 'visitor-maps') ?>" /></div>
			<div class="star star1"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('1 star', 'visitor-maps') ?>" /></div>
		</div>
		<small><?php echo sprintf(__('(Average rating based on %s ratings)', 'visitor-maps'),number_format_i18n($api->num_ratings)); ?> <a target="_blank" href="http://wordpress.org/extend/plugins/<?php echo $api->slug ?>/"> <?php _e('rate', 'visitor-maps') ?></a></small>
        <br /> <br />
		<?php endif;
  } // if ( !is_wp_error($api)
 }// end if (function_exists('get_transient'

if (!$visitor_maps_opt['donated']) {
 ?>

  <table style="border:none; width:850px;">
  <tr>
  <td>
  <div style="width:385px;height:200px; float:left;background-color:white;padding: 10px 10px 10px 10px; border: 1px solid #ddd; background-color:#FFFFE0;">
		<div>
        <h3><?php echo __('Donate', 'visitor-maps'); ?></h3>
 <?php
_e('Please donate to keep this plugin FREE', 'visitor-maps'); echo '<br />';
_e('If you find this plugin useful to you, please consider making a small donation to help contribute to my time invested and to further development. Thanks for your kind support!', 'visitor-maps'); ?> - <a style="cursor:pointer;" title="<?php _e('More from Mike Challis', 'visitor-maps'); ?>" onclick="toggleVisibility('mike_challis_tip');"><?php _e('More from Mike Challis', 'visitor-maps'); ?></a>
  <br /><br />
   </div>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="V3BPEZ9WGYEYG" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" style="border:none;" name="submit" alt="Paypal Donate" />
<img alt="" style="border:none;" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>
  </td><td>

  <div style="width:385px;height:200px; float:left;background-color:white;padding: 10px 10px 10px 20px; border: 1px solid #ddd;">
		<div>
			<h3><?php _e('ThemeFuse Original WP Themes', 'visitor-maps'); ?></h3>
            <?php echo sprintf(__('Try <a href="%s" target="_blank">ThemeFuse</a>, they make some amazing original WP themes that have a cool 1 click auto install feature and excellent after care support services. Check out some of their themes!', 'visitor-maps'), 'https://www.e-junkie.com/ecom/gb.php?cl=136641&c=ib&aff=148937'); ?>
		</div>
        <a href="https://www.e-junkie.com/ecom/gb.php?cl=136641&c=ib&aff=148937" target="_blank"><img title="<?php echo esc_attr(__('ThemeFuse', 'visitor-maps')); ?>" alt="<?php echo esc_attr(__('ThemeFuse', 'visitor-maps')); ?>" src="http://themefuse.com/wp-content/themes/themefuse/images/campaigns/themefuse.jpg" width="375" height="85" /></a>
  </div>
  </td>
 </tr>
 </table>

<br />

<div style="text-align:left; display:none" id="mike_challis_tip">
<img src="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/visitor-maps.jpg" width="250" height="185" alt="Mike Challis" /><br />
<?php _e('Mike Challis says: "Hello, I have spent hundreds of hours coding this plugin just for you. If you are satisfied with my programs and support please consider making a small donation. If you are not able to, that is OK.', 'visitor-maps'); ?>
<?php echo ' '; _e('Most people donate $3, $5, $10, $20, or more. Though no amount is too small. Donations can be made with your PayPal account, or securely using any of the major credit cards. Please also rate my plugin."', 'visitor-maps'); ?>
 <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_blank"><?php _e('Rate This', 'visitor-maps'); ?></a>.
<br />
<a style="cursor:pointer;" title="Close" onclick="toggleVisibility('mike_challis_tip');"><?php _e('Close this message', 'visitor-maps'); ?></a>
</div>

<?php
}
?>

<form name="formoptions" method="post" action="<?php echo admin_url( "plugins.php?page=visitor-maps/visitor-maps.php&amp;updated=true" ); ?>">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="visitor_maps_set" value="1" />
        <input type="hidden" name="form_type" value="upload_options" />
        <?php wp_nonce_field('visitor-maps-options_update'); ?>

    <input name="visitor_maps_donated" id="visitor_maps_donated" type="checkbox" <?php if( $visitor_maps_opt['donated'] ) echo 'checked="checked"'; ?> />
    <label for="visitor_maps_donated"><?php echo __('I have donated to help contribute for the development of this plugin.', 'visitor-maps'); ?></label>
    <br />

<h3><?php _e('Usage', 'visitor-maps') ?></h3>
	<p>
    <?php echo __('Add the shortcode [visitor-maps] in a Page(not a Post). That page will become your Visitor Maps page.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-6.gif" target="_new"><?php echo __('help', 'visitor-maps'); ?></a>
    </p>
   	<p>
    <?php echo __('Add the Who\'s Online sidebar. Click on Appearance, Widgets, then drag the Who\'s Online widget to the sidebar column on the right.', 'visitor-maps'); ?> <a href="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/screenshot-7.gif" target="_new"><?php echo __('help', 'visitor-maps'); ?></a>
    </p>
<?php echo '
<p>
<a href="'.admin_url( 'index.php?page=visitor-maps').'">' .  __( 'View Who\'s Online', 'visitor-maps' ) . '</a>
<br />
<a href="'.admin_url( 'index.php?page=whos-been-online').'">' .  __( 'View Who\'s Been Online', 'visitor-maps' ) . '</a>
<br />
'.sprintf( __('<a href="%s">Visitor Map Viewer</a>', 'visitor-maps'),get_bloginfo('url').'?wo_map_console=1" onclick="wo_map_console(this.href); return false;').
"</p>\n";
?>

<h3><?php echo __('Options', 'visitor-maps') ?></h3>

        <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Update Options', 'visitor-maps') ?> &raquo;" />
        </p>

<fieldset class="options">

 <table cellspacing="2" cellpadding="5" class="form-table">

        <tr>
         <th scope="row" style="width: 75px;"><?php echo __('GeoLocation:', 'visitor-maps'); ?></th>
      <td>

      <?php
      echo '<strong>'.  __('Uses GeoLiteCity data created by MaxMind, available from http://www.maxmind.com', 'visitor-maps') .'</strong><br />';
      if ( !is_file($path_visitor_maps.'GeoLiteCity.dat') ) {
        echo '<span style="background-color:#FFE991; padding:4px;"><strong>'.  __('The Maxmind GeoLiteCity database is not yet installed.', 'visitor-maps'). ' <a style="color:red" href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Install Now', 'visitor-maps'). '</a></strong></span>';
      } else if (!$visitor_maps_opt['enable_location_plugin']) {
              echo '<span style="background-color:#FFE991; padding:4px;"><strong>'.  __('The Maxmind GeoLiteCity database is installed but not enabled (check the setting below).', 'visitor-maps'). '</strong></span>';
      } else {
             echo '<span style="background-color:#99CC99; padding:4px;"><strong>'.  __('The Maxmind GeoLiteCity database is installed and enabled.', 'visitor-maps'). '</strong></span>';
      }
      ?>

      <br />
      <input name="visitor_maps_enable_location_plugin" id="visitor_maps_enable_location_plugin" type="checkbox" <?php if( $visitor_maps_opt['enable_location_plugin'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_location_plugin"><?php echo __('Enable geolocation.', 'visitor-maps'); ?></label>
      <?php if( $visitor_maps_opt['enable_location_plugin'] && is_file($path_visitor_maps.'GeoLiteCity.dat')) echo ' <a href="' . wp_nonce_url(admin_url( 'plugins.php?page=visitor-maps/visitor-maps.php' ),'visitor-maps-geo_update') . '&amp;do_geo=1">'. __('Update Now', 'visitor-maps'). '</a>';?>
      <br />

      <input name="visitor_maps_hide_text_on_worldmap" id="visitor_maps_hide_text_on_worldmap" type="checkbox" <?php if( $visitor_maps_opt['hide_text_on_worldmap'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_text_on_worldmap"><?php echo __('Disable text on geolocation maps (missing map background fix).', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_hide_text_on_worldmap_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_hide_text_on_worldmap_tip">
      <?php echo __('Some PHP servers do not have full support for printing text on the Visitor Map image. Only if the Visitor Map just displays pins and no image for the world or countries, select this setting. After selecting this setting, check your visitor maps page to see if the map is now working.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_state_display" id="visitor_maps_enable_state_display" type="checkbox" <?php if( $visitor_maps_opt['enable_state_display'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_state_display"><?php echo __('Enable display of city, state next to country flag.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_state_display_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_state_display_tip">
      <?php echo __('Changes display options on Who\'s Online dashboard pages.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_dash_map" id="visitor_maps_enable_dash_map" type="checkbox" <?php if( $visitor_maps_opt['enable_dash_map'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_dash_map"><?php echo __('Enable visitor map on Who\'s Online dashboard.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_dash_map_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_dash_map_tip">
      <?php echo __('Changes display options on Who\'s Online dashboard pages.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_widget_link" id="visitor_maps_enable_widget_link" type="checkbox" <?php if( $visitor_maps_opt['enable_widget_link'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_widget_link"><?php echo __('Enable visitor map link on Who\'s Online widget.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_widget_link_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_widget_link_tip">
      <?php echo __('Changes display options on Who\'s Online widget.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_visitor_map_hover" id="visitor_maps_enable_visitor_map_hover" type="checkbox" <?php if( $visitor_maps_opt['enable_visitor_map_hover'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_visitor_map_hover"><?php echo __('Enable hover labels for location pins on visitor map page.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_visitor_map_hover_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_visitor_map_hover_tip">
      <?php echo __('Some themes interfere with the proper display of the location pins on the Visitor Maps page. After enabling this setting, check your visitor maps page to make sure the pins are placed correctly. If the pins are about 10 pixels too low on the map, undo this setting.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_users_map_hover" id="visitor_maps_enable_users_map_hover" type="checkbox" <?php if( $visitor_maps_opt['enable_users_map_hover'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_visitor_map_hover"><?php echo __('Enable user names on hover labels for location pins on visitor map page.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_users_map_hover_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_users_map_hover_tip">
      <?php echo __('When enabled, registered users will have green location pins on the Visitor Maps page. Also the hover tag will include the user name.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_hide_console" id="visitor_maps_hide_console" type="checkbox" <?php if( $visitor_maps_opt['hide_console'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_console"><?php echo __('Hide map viewing by non administrators.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_hide_console_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_hide_console_tip">
      <?php echo __('This setting restricts viewing the Visitor Map Viewer page to administrators only.', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_pins_limit"><?php echo __('Limit for map pins', 'visitor-maps'); ?>:</label><input name="visitor_maps_pins_limit" id="visitor_maps_active_time" type="text" value="<?php echo absint($visitor_maps_opt['pins_limit']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_pins_limit_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_pins_limit_tip">
      <?php echo __('This limit protects server resources by limiting pins when displaying maps. Default is 2000. The human eye will not be able to see more than 2000 pins anyway.', 'visitor-maps'); ?>
      </div>
      <br />

      <?php echo __('Default Visitor Map', 'visitor-maps'); ?>
      <label for="visitor_maps_default_map_time"><?php echo __('Time:', 'visitor-maps'); ?></label>
      <input type="text" id="visitor_maps_default_map_time" name="visitor_maps_default_map_time" value="<?php echo absint($visitor_maps_opt['default_map_time']) ?>" size="3" />
      <label for="visitor_maps_default_map_units"><?php echo __('Units:', 'visitor-maps'); ?></label>
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

<label for="visitor_maps_default_map"><?php echo __('Map:', 'visitor-maps'); ?></label>


      <select id="visitor_maps_default_map" name="visitor_maps_default_map">
      <?php
       $default_map_select_array = array(
'1'  => __('World (smallest)', 'visitor-maps'),
'2'  => __('World (small)', 'visitor-maps'),
'3'  => __('World (medium)', 'visitor-maps'),
'4'  => __('World (large)', 'visitor-maps'),
/*'5'  => __('US', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'6'  => __('US', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'7'  => __('Canada and US', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'8'  => __('Canada and US', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'9'  => __('Asia', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'10' => __('Asia', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'11' => __('Australia and NZ', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'12' => __('Australia and NZ', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'13' => __('Europe Central', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'14' => __('Europe Central', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'15' => __('Europe', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'16' => __('Europe', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'17' => __('Scandinavia', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'18' => __('Scandinavia', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'19' => __('Great Britain', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'20' => __('Great Britain', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'21' => __('US Midwest', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'22' => __('US Midwest', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'23' => __('US Upper Midwest', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'24' => __('US Upper Midwest', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'25' => __('US Northeast', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'26' => __('US Northeast', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'27' => __('US Northwest', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'28' => __('US Northwest', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'29' => __('US Rocky Mountain', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'30' => __('US Rocky Mountain', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'31' => __('US South', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'32' => __('US South', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'33' => __('US Southeast', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'34' => __('US Southeast', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'35' => __('US Southwest', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'36' => __('US Southwest', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'37' => __('Spain/Portugal', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'38' => __('Spain/Portugal', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'39' => __('Finland', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'40' => __('Finland', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'41' => __('Finland', 'visitor-maps').' '. __('(yellow)', 'visitor-maps'),
'42' => __('Japan', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'43' => __('Japan', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'44' => __('Netherlands', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'45' => __('Netherlands', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),
'46' => __('Brazil', 'visitor-maps').' '. __('(black)', 'visitor-maps'),
'47' => __('Brazil', 'visitor-maps').' '. __('(brown)', 'visitor-maps'),*/
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
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_default_map_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_default_map_tip">
      <?php echo __('Default map to display on the Visitor Maps page. After setting this, check your visitor maps page to make sure it fits correctly. If the map is too wide, select the next smaller one.', 'visitor-maps'); ?>
      </div>

      <br />
      <?php _e('Users who can view the dashboard pages:', 'visitor-maps') ?></label>
    <?php $this->visitor_maps_perm_dropdown('visitor_maps_dashboard_permissions', $visitor_maps_opt['dashboard_permissions']);  ?>
    <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_dashboard_permissions_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_dashboard_permissions_tip">
      <?php echo __('By default, only Administrators can view the dashboard pages. Change this setting to also allow Editors, Authors, or Contributors to view the dashboard pages. When set to Authors, you are also allowing Administrator and Editors.', 'visitor-maps'); ?>
      </div>


      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo __('Visitors:', 'visitor-maps'); ?></th>
      <td>

      <label for="visitor_maps_active_time"><?php echo __('Active time (minutes)', 'visitor-maps'); ?>:</label><input name="visitor_maps_active_time" id="visitor_maps_active_time" type="text" value="<?php echo absint($visitor_maps_opt['active_time']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_active_time_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_active_time_tip">
      <?php echo __('Minutes that a visitor is considered "active". Default is 5 minutes.', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_track_time"><?php echo __('Inactive time (minutes)', 'visitor-maps'); ?>:</label><input name="visitor_maps_track_time" id="visitor_maps_track_time" type="text" value="<?php echo absint($visitor_maps_opt['track_time']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_track_time_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_track_time_tip">
      <?php echo __('Minutes until an inactive visitor is removed from display. Default is 15 minutes.', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_store_days"><?php echo __('Days to store visitor data', 'visitor-maps'); ?>:</label><input name="visitor_maps_store_days" id="visitor_maps_store_days" type="text" value="<?php echo absint($visitor_maps_opt['store_days']);  ?>" size="3" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_store_days_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_store_days_tip">
      <?php echo __('Days to store visitor data in database table. This data is used for the geolocation maps. Default is 30 days.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_hide_administrators" id="visitor_maps_hide_administrators" type="checkbox" <?php if( $visitor_maps_opt['hide_administrators'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_administrators"><?php echo __('Do not include administrators count or location on the maps.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_hide_administrators_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_hide_administrators_tip">
      <?php echo __('Changes display options on Who\'s Online maps.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_hide_bots" id="visitor_maps_hide_bots" type="checkbox" <?php if( $visitor_maps_opt['hide_bots'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_hide_bots"><?php echo __('Do not include search bots in the visitors online count on widgets.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_hide_bots_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_hide_bots_tip">
      <?php echo __('Changes display options on Who\'s Online widgets.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_combine_members" id="visitor_maps_combine_members" type="checkbox" <?php if( $visitor_maps_opt['combine_members'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_combine_members"><?php echo __('Combine guests and members on widgets so they are only shown as visitors.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_combine_members_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_combine_members_tip">
      <?php echo __('Changes display options on Who\'s Online widgets.', 'visitor-maps'); ?>
      <?php echo ' '; echo __('Use this setting when your site has registration turned off and all your visitors are guests and not members.', 'visitor-maps'); ?>
      </div>
      <br />

      <label name="visitor_maps_ips_to_ignore" for="visitor_maps_ips_to_ignore"><?php echo __('IP Addresses to ignore', 'visitor-maps'); ?>:</label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_ips_to_ignore_tip');"><?php echo __('help', 'visitor-maps'); ?></a> <br />
      <div style="text-align:left; display:none" id="visitor_maps_ips_to_ignore_tip">
        <?php _e('Optional list of IP addresses for visitors you do not want shown on maps.', 'visitor-maps') ?><br />
        <?php _e('Start each entry on a new line.', 'visitor-maps'); ?><br />
        <?php _e('Use <strong>*</strong> for wildcards.', 'visitor-maps'); ?><br />
		<?php _e('Examples:', 'visitor-maps'); ?>
		<p style="margin: 2px 0"><span dir="ltr">192.168.1.100</span></p>
		<p style="margin: 2px 0"><span dir="ltr">192.168.1.*</span></p>
		<p style="margin: 2px 0"><span dir="ltr">192.168.*.*</span></p>
      </div>
      <textarea rows="4" cols="20" name="visitor_maps_ips_to_ignore" id="visitor_maps_ips_to_ignore"><?php echo $visitor_maps_opt['ips_to_ignore']; ?></textarea>
      <br />

      <label name="visitor_maps_urls_to_ignore" for="visitor_maps_urls_to_ignore"><?php echo __('URLs to ignore', 'visitor-maps'); ?>:</label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_urls_to_ignore_tip');"><?php echo __('help', 'visitor-maps'); ?></a> <br />
      <div style="text-align:left; display:none" id="visitor_maps_urls_to_ignore_tip">
        <?php _e('Optional list of URLs on your site you do not want in any Who\'s Online data.', 'visitor-maps') ?><br />
        <?php _e('This feature can be used to block any URLs such as /wp-admin/, or for compatibility with other plugins such as WP SlimStat.', 'visitor-maps'); ?><br />
        <?php _e('Use partial URL or full URL. The filter will match any part of the URL.', 'visitor-maps'); ?><br />
        <?php _e('Start each entry on a new line.', 'visitor-maps'); ?><br />
		<?php _e('Examples:', 'visitor-maps'); ?>
		<p style="margin: 2px 0"><span dir="ltr">wp-slimstat-js.php</span></p>
		<p style="margin: 2px 0"><span dir="ltr">http://www.mysite.com/wp-content/plugins/wp-slimstat-js.php</span></p>
        <p style="margin: 2px 0"><span dir="ltr">/wp-admin/</span></p>
      </div>
      <textarea rows="4" cols="40" name="visitor_maps_urls_to_ignore" id="visitor_maps_urls_to_ignore"><?php echo $visitor_maps_opt['urls_to_ignore']; ?></textarea>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo __('Lookups:', 'visitor-maps'); ?></th>
      <td>

      <label for="visitor_maps_whois_url"><?php echo __('Who Is Lookup URL', 'visitor-maps'); ?>:</label><input name="visitor_maps_whois_url" id="visitor_maps_geoip_date_format" type="text" value="<?php echo $visitor_maps_opt['whois_url'];  ?>" size="55" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_whois_url_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_whois_url_tip">
      <?php echo __('URL to open when an IP address is clicked on.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_whois_url_popup" id="visitor_maps_whois_url_popup" type="checkbox" <?php if( $visitor_maps_opt['whois_url_popup'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_whois_url_popup"><?php echo __('Enable open Who Is Lookup URL on a pop-up.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_whois_url_popup_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_whois_url_popup_tip">
      <?php echo __('Changes display options on Who\'s Online dashboard pages.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_host_lookups" id="visitor_maps_enable_host_lookups" type="checkbox" <?php if( $visitor_maps_opt['enable_host_lookups'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_host_lookups"><?php echo __('Enable host lookups for IP addresses.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_host_lookups_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_host_lookups_tip">
      <?php echo __('Changes display options on Who\'s Online dashboard pages.', 'visitor-maps'); ?>
      </div>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo __('Stats:', 'visitor-maps'); ?></th>
      <td>
      <input name="visitor_maps_enable_blog_footer" id="visitor_maps_enable_blog_footer" type="checkbox" <?php if( $visitor_maps_opt['enable_blog_footer'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_blog_footer"><?php echo __('Enable stats display in blog footer.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_blog_footer_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_blog_footer_tip">
      <?php echo __('Shows how many visitors are online now and records of the most users online at one time.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_admin_footer" id="visitor_maps_enable_admin_footer" type="checkbox" <?php if( $visitor_maps_opt['enable_admin_footer'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_admin_footer"><?php echo __('Enable stats display in admin footer.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_admin_footer_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_admin_footer_tip">
      <?php echo __('Shows how many visitors are online now and records of the most users online at one time.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_records_page" id="visitor_maps_enable_records_page" type="checkbox" <?php if( $visitor_maps_opt['enable_records_page'] ) echo 'checked="checked"'; ?> />
      <label for="visitor_maps_enable_records_page"><?php echo __('Enable stats display on map page.', 'visitor-maps'); ?></label>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_records_page_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_records_page_tip">
      <?php echo __('Shows how many visitors are online now and records of the most users online at one time.', 'visitor-maps'); ?>
      </div>
      <br />

      <input name="visitor_maps_enable_credit_link" id="visitor_maps_enable_credit_link" type="checkbox" <?php if ( $visitor_maps_opt['enable_credit_link'] ) echo ' checked="checked" '; ?> />
      <label for="visitor_maps_enable_credit_link"><?php echo __('Enable plugin credit link:', 'visitor-maps') ?></label> <small><?php echo __('Powered by', 'visitor-maps'). ' <a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_new">'.__('Visitor Maps', 'visitor-maps'); ?></a></small>
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_enable_credit_link_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_enable_credit_link_tip">
      <?php echo __('The credit link is not mandatory, yes you can disable it if you want.', 'visitor-maps'); ?>
      </div>

      </td>
    </tr>

    <tr>
         <th scope="row" style="width: 75px;"><?php echo __('Times:', 'visitor-maps'); ?></th>
      <td>

      <br />
      <a href="http://php.net/date" target="_blank"><?php echo __('Table of date format characters.', 'visitor-maps'); ?></a>
      <br />

      <label for="visitor_maps_time_format"><?php echo __('Time format (Max Users Today)', 'visitor-maps'); ?>:</label><input name="visitor_maps_time_format" id="visitor_maps_time_format" type="text" value="<?php echo $visitor_maps_opt['time_format'];  ?>" size="10" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_time_format_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_time_format_tip">
      <?php echo __('Time format for "Max users today" and "Last refresh time" display. Default, h:i a T (02:25 pm PST)', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_time_format_hms"><?php echo __('Time format (Last Click)', 'visitor-maps'); ?>:</label><input name="visitor_maps_time_format_hms" id="visitor_maps_time_format_hms" type="text" value="<?php echo $visitor_maps_opt['time_format_hms'];  ?>" size="10" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_time_format_hms_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_time_format_hms_tip">
      <?php echo __('Time format for "Entry" and "Last Click" display. Default, h:i:sa (02:25:25pm)', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_date_time_format"><?php echo __('Date/Time format (All Time Records)', 'visitor-maps'); ?>:</label><input name="visitor_maps_date_time_format" id="visitor_maps_date_time_format" type="text" value="<?php echo $visitor_maps_opt['date_time_format'];  ?>" size="15" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_date_time_format_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_date_time_format_tip">
      <?php echo __('Date/Time format for month, year, an all time records. Default, m-d-Y h:i a T (12-14-2008 02:25 pm PST)', 'visitor-maps'); ?>
      </div>
      <br />

      <label for="visitor_maps_geoip_date_format"><?php echo __('Date/Time format (GeoLite data)', 'visitor-maps'); ?>:</label><input name="visitor_maps_geoip_date_format" id="visitor_maps_geoip_date_format" type="text" value="<?php echo $visitor_maps_opt['geoip_date_format'];  ?>" size="15" />
      <a style="cursor:pointer;" title="<?php echo __('Click for Help!', 'visitor-maps'); ?>" onclick="toggleVisibility('visitor_maps_geoip_date_format_tip');"><?php echo __('help', 'visitor-maps'); ?></a>
      <div style="text-align:left; display:none" id="visitor_maps_geoip_date_format_tip">
      <?php echo __('Date/Time format for "The GeoLite data was last updated on...". Default, m-d-Y h:i a T (12-14-2008 02:25 pm PST)', 'visitor-maps'); ?>
      </div>

      </td>
    </tr>

    </table>

 </fieldset>

        <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Update Options', 'visitor-maps') ?> &raquo;" />
        </p>
</form>

<table style="border:none;" width="775">
  <tr>
  <td width="325">
<p><strong><?php _e('More WordPress plugins by Mike Challis:', 'visitor-maps') ?></strong></p>
<ul>
<li><a href="http://www.FastSecureContactForm.com/" target="_blank"><?php echo __('Fast Secure Contact Form', 'visitor-maps'); ?></a></li>
<li><a href="http://wordpress.org/extend/plugins/si-captcha-for-wordpress/" target="_blank"><?php echo __('SI CAPTCHA Anti-Spam', 'visitor-maps'); ?></a></li>
<li><a href="http://wordpress.org/extend/plugins/visitor-maps/" target="_blank"><?php echo __('Visitor Maps and Who\'s Online', 'visitor-maps'); ?></a></li>
</ul>
<?php
  if (!$visitor_maps_opt['donated']) { ?>
   </td><td width="350">
   <?php echo sprintf(__('"I recommend <a href="%s" target="_blank">HostGator Web Hosting</a>. All my sites are hosted there. The prices are great and they offer the most features." - Mike Challis', 'visitor-maps'), 'http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=mchallis-vmwp'); ?>
   </td><td width="100">
    <a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=mchallis-vmwp" target="_blank"><img title="<?php echo esc_attr(__('Web Site Hosting', 'visitor-maps')); ?>" alt="<?php echo esc_attr(__('Web Site Hosting', 'visitor-maps')); ?>" src="<?php echo WP_PLUGIN_URL; ?>/visitor-maps/hostgator-blog.gif" width="100" height="100" /></a>
<?php
  }
 ?>
</td>
</tr>
</table>
</div>
