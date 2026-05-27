<?php
/**
 * Plugin Name: SEO Autopilot Lite
 * Plugin URI: https://example.com/seo-autopilot-lite
 * Description: Lightweight AI-powered WordPress SEO plugin with meta tags, schema, SEO analysis, focus keywords, and Traveler theme support.
 * Version: 1.0.0
 * Author: SEO Autopilot
 * Text Domain: seo-autopilot-lite
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'SEO_AUTOPILOT_LITE_VERSION', '1.0.0' );
define( 'SEO_AUTOPILOT_LITE_PLUGIN_FILE', __FILE__ );
define( 'SEO_AUTOPILOT_LITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEO_AUTOPILOT_LITE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SEO_AUTOPILOT_LITE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Check PHP version.
if ( version_compare( PHP_VERSION, '8.2', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'SEO Autopilot Lite requires PHP 8.2 or higher.', 'seo-autopilot-lite' ) . '</p></div>';
    } );
    return;
}

// Check WordPress version.
global $wp_version;
if ( version_compare( $wp_version, '6.4', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'SEO Autopilot Lite requires WordPress 6.4 or higher.', 'seo-autopilot-lite' ) . '</p></div>';
    } );
    return;
}

// Load Composer autoload if available.
if ( file_exists( SEO_AUTOPILOT_LITE_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once SEO_AUTOPILOT_LITE_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Manual class loading fallback.
    spl_autoload_register( function( $class ) {
        $prefix = 'SeoAutopilotLite\\';
        $base_dir = SEO_AUTOPILOT_LITE_PLUGIN_DIR . 'includes/';
        
        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            return;
        }
        
        $relative_class = substr( $class, $len );
        $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
        
        if ( file_exists( $file ) ) {
            require $file;
        }
    } );
}

/**
 * Main plugin class.
 */
final class SeoAutopilotLite_Plugin {
    
    private static ?self $instance = null;
    
    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks(): void {
        register_activation_hook( SEO_AUTOPILOT_LITE_PLUGIN_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( SEO_AUTOPILOT_LITE_PLUGIN_FILE, [ $this, 'deactivate' ] );
        
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'init', [ $this, 'init_components' ] );
    }
    
    public function activate(): void {
        $activator = new \SeoAutopilotLite\Core\Activator();
        $activator->activate();
    }
    
    public function deactivate(): void {
        $deactivator = new \SeoAutopilotLite\Core\Deactivator();
        $deactivator->deactivate();
    }
    
    public function load_textdomain(): void {
        load_plugin_textdomain( 'seo-autopilot-lite', false, dirname( SEO_AUTOPILOT_LITE_PLUGIN_BASENAME ) . '/languages' );
    }
    
    public function init_components(): void {
        // Initialize core components.
        new \SeoAutopilotLite\Core\Settings();
        new \SeoAutopilotLite\Core\Assets();
        
        // Initialize admin components.
        if ( is_admin() ) {
            new \SeoAutopilotLite\Admin\SettingsPage();
            new \SeoAutopilotLite\Admin\MetaBox();
            new \SeoAutopilotLite\Admin\Columns();
        }
        
        // Initialize frontend output.
        new \SeoAutopilotLite\SEO\FrontendOutput();
        
        // Initialize schema manager.
        new \SeoAutopilotLite\Schema\SchemaManager();
        
        // Initialize Traveler support if enabled.
        if ( \SeoAutopilotLite\Core\Settings::get_option( 'traveler_enabled', false ) ) {
            new \SeoAutopilotLite\Traveler\TravelerDetector();
        }
    }
}

// Bootstrap the plugin.
function seo_autopilot_lite_init(): SeoAutopilotLite_Plugin {
    return SeoAutopilotLite_Plugin::get_instance();
}
seo_autopilot_lite_init();
