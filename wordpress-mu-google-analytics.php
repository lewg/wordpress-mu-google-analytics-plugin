<?php
/*
Plugin Name: Google Analytics
Plugin URI: 
Description:
Author: Andrew Billits (Incsub)
Version: 1.0.1
Author URI:
WDP ID: 51
 */

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('wpmu_options', 'google_analytics_site_admin_options');
add_action('update_wpmu_options', 'google_analytics_site_admin_options_process');
add_action('admin_menu', 'google_analytics_plug_pages');
add_action('wp_footer', 'google_analytics_output');
add_action('admin_footer', 'google_analytics_output');
add_action('supporter_settings', 'google_analytics_supporter_setting');
add_action('supporter_settings_process', 'google_analytics_supporter_setting_process');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function google_analytics_supporter_setting_process() {
  update_site_option( "google_analytics_supporter", $_POST[ 'google_analytics_supporter' ] );
}

function google_analytics_site_admin_options_process() {
  update_site_option( 'google_analytics_tracking_code' , trim( $_POST['google_analytics_tracking_code'] ) );
}

function google_analytics_plug_pages() {
  $enable_disable = 'enable';
  if ( function_exists('is_supporter') ) {
    if ( get_site_option( "google_analytics_supporter", "no" ) == 'yes' ) {
      if ( is_supporter() ) {
        $enable_disable = 'enable';
      } else {
        $enable_disable = 'disable';
      }
    } else {
      $enable_disable = 'enable';
    }
  } else {
    $enable_disable = 'enable';
  }
  if ( $enable_disable == 'enable' ) {
    if ( current_user_can('manage_options') ) {
      add_submenu_page('options-general.php', 'Google Analytics', 'Google Analytics', 'manage_options', 'google-analytics', 'google_analytics_manage_output' );
    }
  }
}

//------------------------------------------------------------------------//
//---Output Functions-----------------------------------------------------//
//------------------------------------------------------------------------//

function google_analytics_supporter_setting() {
?>
                <tr valign="top"> 
                <th scope="row"><?php _e('Google Analytics Supporter Only') ?></th> 
                <td><select name="google_analytics_supporter">
                <option value="yes" <?php if (get_site_option( "google_analytics_supporter", "no" ) == 'yes') echo 'selected="selected"'; ?>><?php _e('Yes') ?></option>
                <option value="no" <?php if (get_site_option( "google_analytics_supporter", "no" ) == 'no') echo 'selected="selected"'; ?>><?php _e('No') ?></option>
                </select>
                <br /><?php _e('Enable Google Analytics for supporter blogs only.'); ?></td> 
                </tr>
<?php
}

function google_analytics_output() {
  $enable_disable = 'enable';
  if ( function_exists('is_supporter') ) {
    if ( get_site_option( "google_analytics_supporter", "no" ) == 'yes' ) {
      if ( is_supporter() ) {
        $enable_disable = 'enable';
      } else {
        $enable_disable = 'disable';
      }
    } else {
      $enable_disable = 'enable';
    }
  } else {
    $enable_disable = 'enable';
  }
  if ( $enable_disable == 'enable' ) {
    $google_analytics_blog_tracking_code = get_option('google_analytics_tracking_code');
  }
  $google_analytics_site_tracking_code = get_site_option('google_analytics_tracking_code');
  if ( $google_analytics_site_tracking_code == $google_analytics_blog_tracking_code ) {
    unset( $google_analytics_blog_tracking_code );
  }
  if ( !empty( $google_analytics_site_tracking_code ) || !empty( $google_analytics_blog_tracking_code ) ) {
?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
<?php
    if ( !empty( $google_analytics_site_tracking_code ) ) {
?>
      _gaq.push(['_setAccount', '<?php echo $google_analytics_site_tracking_code; ?>']);
      _gaq.push(['_trackPageview']);
      _gaq.push(['_trackPageLoadTime']);
<?php
    }
    if ( !empty( $google_analytics_blog_tracking_code ) ) {
?>
      _gaq.push(['b._setAccount', '<?php echo $google_analytics_blog_tracking_code; ?>']);
      _gaq.push(['b._trackPageview']);
      _gaq.push(['b._trackPageLoadTime']);
<?php
    }
?>
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
<?php
  }

}

function google_analytics_site_admin_options() {
?>
    <h3><?php _e('Google Analytics Settings') ?></h3> 
    <table class="form-table">
      <tr valign="top"> 
        <th scope="row"><?php _e('Tracking Code') ?></th> 
        <td>
                  <input type="text" name="google_analytics_tracking_code" id="google_analytics_tracking_code" class="regular-text" tabindex='40' maxlength="200" value="<?php echo get_site_option('google_analytics_tracking_code'); ?>" />
          <br />
          <?php _e('Ex: UA-XXXXX-2') ?>
        </td>
      </tr>
    </table>
<?php
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function google_analytics_manage_output() {
  if(!current_user_can('manage_options')) {
    echo "<p>" . __('Nice Try...') . "</p>";
    return;
  }
  /*
  if (isset($_GET['updated'])) {
    ?><div id="message" class="updated fade"><p><?php _e('' . urldecode($_GET['updatedmsg']) . '') ?></p></div><?php
  }
   */
  echo '<div class="wrap">';
  switch( $_GET[ 'action' ] ) {
    //---------------------------------------------------//
  default:
?>
            <h2><?php _e('Google Analytics') ?></h2>
            <form method="post" action="options-general.php?page=google-analytics&action=update"> 
            <table class="form-table">
                <tr valign="top"> 
                    <th scope="row"><?php _e('Tracking Code') ?></th> 
                    <td>
                        <input type="text" name="google_analytics_tracking_code" id="google_analytics_tracking_code" class="regular-text" tabindex='40' maxlength="200" value="<?php echo get_option('google_analytics_tracking_code'); ?>" />
                        <span class="setting-description"><?php _e('Ex: UA-XXXXX-2') ?></span>
                    </td>
                </tr>
            </table>
            <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
            </p>
            </form>
            <h2><?php _e("What's Google Analytics? How do I set this up?") ?></h2>
            <p><?php _e("<a href=\"https://www.google.com/analytics/\">Google Analytics</a> is the free stats tracking system supplied by Google and produces very attractive (and comprehensive) stats.") ?></p>
            <p><?php _e("To get going, just <a href=\"http://www.google.com/analytics/sign_up.html\">sign up for Analytics</a>, set up a new account and copy the tracking code you receive (it'll start with 'UA-') into the box above and press 'Save' - it can take several hours before you see any stats, but once it is you've got access to one heck of a lot of data!") ?></p>
            <p><?php _e("For more information on finding the tracking code, please visit <a href=\"http://www.google.com/support/analytics/bin/answer.py?hl=en&amp;answer=55603\">this Google help site</a>.") ?></p>

<?php
    break;
    //---------------------------------------------------//
  case "update":
    update_option('google_analytics_tracking_code', trim( $_POST['google_analytics_tracking_code'] ) );
    echo "
      <SCRIPT LANGUAGE='JavaScript'>
    window.location='options-general.php?page=google-analytics&updated=true&updatedmsg=" . urlencode(__('Settings saved.')) . "';
      </script>
      ";
    break;
    //---------------------------------------------------//
  }
  echo '</div>';
}

?>
