<?php
/**
 * Plugin Name: News Ticker Widget for Elementor
 * Plugin URI: https://flickdevs.com/elementor/
 * description: It showcases your most recent posts in a ticker style.
 * Version: 1.3.2
 * Elementor tested up to: 3.19.0
 * Author: FlickDevs
 * Author URI: https://flickdevs.com
 * Text Domain: elementor-news-ticker
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
define('ELMENTOR_NT_WIDGET_URL', plugins_url('/', __FILE__));  // Define Plugin URL 
define('ELMENTOR_NT_WIDGET_PATH', plugin_dir_path(__FILE__));  // Define Plugin Directory Path
define('ELEMENTORNEWTICKER_DOMAIN','elementor-news-ticker');

/* load the plugin Category */
require_once ELMENTOR_NT_WIDGET_PATH.'inc/elementor-helper.php';

/* Load scripts and styles for front */
add_action('wp_enqueue_scripts','ele_news_ticker_style' );

/* register the widgtes file in elementor widgtes. */
//add_action('elementor/widgets/widgets_registered','add_elementor_nt_widget');
add_action('elementor/widgets/register','add_elementor_nt_widget');

function add_elementor_nt_widget() {
    require_once ELMENTOR_NT_WIDGET_PATH.'elements/news-ticker-widget.php';
}

function ele_news_ticker_style() {
    wp_enqueue_style('fd-nt-style', ELMENTOR_NT_WIDGET_URL.'assets/css/ele-news-ticker.css', true);
	wp_enqueue_style('ticker-style', ELMENTOR_NT_WIDGET_URL.'assets/css/ticker.css', true);
	wp_enqueue_script('ticker-script',ELMENTOR_NT_WIDGET_URL.'assets/js/ticker.js', array('jquery'),'1.0', true);

	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}



/**
 *   Check the elementor current version.
 */
function fd_nt_elementor_load_plugin() {
    load_plugin_textdomain('ELEMENTORNEWTICKER_DOMAIN');
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'fd_nt_elementor_widgets_fail_load');
        return;
    }
    $elementor_version_required ='1.1.2';
    if (!version_compare(ELEMENTOR_VERSION, $elementor_version_required, '>=')) {
        add_action('admin_notices','fd_nt_elementor_fail_load_out_of_date');
        return;
    }
}
add_action('plugins_loaded','fd_nt_elementor_load_plugin');
function fd_nt_elementor_widgets_fail_load() {
    $screen = get_current_screen();
    if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
        return;
    }
    $plugin = 'elementor/elementor.php';
    if (_is_elementor_installed()) {
        if (!current_user_can('activate_plugins')) {
            return;
        }
        $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
        $message = '<p>' . __('Elementor News Ticker not working because you need to activate the Elementor plugin.', ELEMENTORNEWTICKER_DOMAIN) . '</p>';
        $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $activation_url, __('Activate Elementor Now', ELEMENTORNEWTICKER_DOMAIN)) . '</p>';
    } else {
        if (!current_user_can('install_plugins')) {
            return;
        }
        $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
        $message = '<p>' . __('Elementor News Ticker not working because you need to install the Elemenor plugin', ELEMENTORNEWTICKER_DOMAIN) . '</p>';
        $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $install_url, __('Install Elementor Now', ELEMENTORNEWTICKER_DOMAIN)) . '</p>';
    }
    echo '<div class="error"><p>' . $message . '</p></div>';
}
function fd_nt_elementor_fail_load_out_of_date() {
    if (!current_user_can('update_plugins')) {
        return;
    }
    $file_path = 'elementor/elementor.php';
    $upgrade_link = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file_path, 'upgrade-plugin_' . $file_path);
    $message = '<p>' . __('Elementor News Ticker not working because you are using an old version of Elementor.', ELEMENTORNEWTICKER_DOMAIN) . '</p>';
    $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $upgrade_link, __('Update Elementor Now', ELEMENTORNEWTICKER_DOMAIN)) . '</p>';
    echo '<div class="error">' . $message . '</div>';
}
if (!function_exists('_is_elementor_installed')) {
    function _is_elementor_installed() {
        $file_path = 'elementor/elementor.php';
        $installed_plugins = get_plugins();
        return isset($installed_plugins[$file_path]);
    }
}
/* Register deactivation hook. */
register_deactivation_hook( __FILE__, 'nt_plugin_deactivate' );
/* Register activation hook. */
register_activation_hook( __FILE__, 'nt_admin_notice_activation_hook' );
/**
 * Runs only when the plugin is activated.
 * @since 1.1.0
 */

function nt_admin_notice_activation_hook() {
	update_option( 'show_notice', 'show' );
}
/* Add admin notice */
add_action( 'admin_notices', 'nt_admin_reviews_notice' );



function nt_plugin_deactivate() {
	delete_option( 'current_date' );
	delete_option( 'week_notice_date' );
	delete_option( 'show_notice' );
}

/** 
 * Disable notice for 7 days
 */ 

add_action( 'wp_ajax_user_dismiss_notice', 'user_dismiss_notice' );
add_action( 'wp_ajax_nopriv_user_dismiss_notice', 'user_dismiss_notice' );
function user_dismiss_notice() {
	$week = date('d/m/Y', strtotime(' +7 day'));
    update_option('week_notice_date', $week);
	wp_die();
}

/** 
 * Disable notice 
 */

add_action( 'wp_ajax_disable_notice', 'disable_notice' );
add_action( 'wp_ajax_nopriv_disable_notice', 'disable_notice' );
function disable_notice() {
    update_option('show_notice','hide');
	wp_die();
}
/**
 * Admin Notice on Activation.
 * @since 1.1.0
 */

function nt_admin_reviews_notice() {
	    $today = date('d/m/Y');
        $week = get_option('week_notice_date');
		$show_notice = get_option('show_notice');
		if ( ! is_admin() ) {
			return;
		}
		else if ( ! is_user_logged_in() ) {
			return;
		}
		else if ( ! current_user_can( 'update_core' ) ) {
			return;
		}
		if ( (is_plugin_active( 'elementor-news-ticker/elementor-news-ticker.php' )) && ( $show_notice == 'show' ) && ( $today > $week)) {

        ?>
			<div class="notice updated">
				<p><b>Hi there! You've been using Elementor News Ticker on your site, I hope it's been useful. If you're enjoying my plugin, would you mind rating it 5-star to help spread the word? It wan't take more than a minute. </b></p>
				<p><a href="https://wordpress.org/plugins/fd-elementor-button-plus/" target="_blank" class="rating-link"><b> Yes, you deserv it </b></a></p>   
					<p><b><a href="#" class="dismiss right-notice">I'll do it later</a></b></p>
					<?php if ( !empty($week) ) { ?>
					<p><b><a href="#" class="disable-notice"> Never show again</a></b></p>
					<?php } ?>  
				</div>
			<?php	
		}
}
add_action('admin_head','save_nt_date');
function save_nt_date(){
	?>
	<script type="text/javascript" >
		jQuery('document').ready( function() { 
			jQuery('.dismiss').click(function() {
				var data = {
					'action': 'user_dismiss_notice',
				};
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data);
				location.reload(true);
				});
				jQuery('.disable-notice,rating-link').click(function() {
				var data1 = {
					'action': 'disable_notice',
				};
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data1);
				location.reload(true);
				});
		});
	</script>
	<?php
}
?>