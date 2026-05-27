/**
 * Plugin Name: SEO Autopilot for WordPress
 * Plugin URI: https://example.com/seo-autopilot-wp
 * Description: AI-powered SEO automation, audit, optimization, Traveler theme SEO, schema generation, approvals, backups, rollback, reports, and Google Search Console insights.
 * Version: 1.0.0
 * Author: SEO Autopilot
 * Text Domain: seo-autopilot-wp
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.2
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace SeoAutopilotWp;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('SEO_AUTOPILOT_WP_VERSION', '1.0.0');
define('SEO_AUTOPILOT_WP_PLUGIN_FILE', __FILE__);
define('SEO_AUTOPILOT_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SEO_AUTOPILOT_WP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SEO_AUTOPILOT_WP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check PHP version requirement.
if (version_compare(PHP_VERSION, '8.2', '<')) {
    add_action('admin_notices', function (): void {
        echo '<div class="notice notice-error"><p>' . 
            esc_html__('SEO Autopilot requires PHP 8.2 or higher.', 'seo-autopilot-wp') . 
            '</p></div>';
    });
    return;
}

// Check WordPress version requirement.
global $wp_version;
if (version_compare($wp_version, '6.4', '<')) {
    add_action('admin_notices', function (): void {
        echo '<div class="notice notice-error"><p>' . 
            sprintf(
                esc_html__('SEO Autopilot requires WordPress 6.4 or higher. You are using version %s.', 'seo-autopilot-wp'),
                esc_html($wp_version)
            ) . 
            '</p></div>';
    });
    return;
}

// Load Composer autoloader if it exists.
$autoload_file = SEO_AUTOPILOT_WP_PLUGIN_DIR . 'vendor/autoload.php';
if (file_exists($autoload_file)) {
    require_once $autoload_file;
} else {
    // Fallback to manual class loading for development without Composer.
    spl_autoload_register(function (string $class): void {
        $prefix = 'SeoAutopilotWp\\';
        $base_dir = SEO_AUTOPILOT_WP_PLUGIN_DIR . 'includes/';
        
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

/**
 * Main plugin class.
 * 
 * @since 1.0.0
 */
final class Plugin
{
    /**
     * Single instance of the plugin.
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;
    
    /**
     * Core components container.
     *
     * @var array<string, object>
     */
    private array $components = [];
    
    /**
     * Get plugin instance.
     *
     * @return Plugin
     */
    public static function getInstance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - private to enforce singleton pattern.
     */
    private function __construct()
    {
        $this->initHooks();
    }
    
    /**
     * Initialize WordPress hooks.
     */
    private function initHooks(): void
    {
        // Activation and deactivation hooks.
        register_activation_hook(SEO_AUTOPILOT_WP_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(SEO_AUTOPILOT_WP_PLUGIN_FILE, [$this, 'deactivate']);
        register_uninstall_hook(SEO_AUTOPILOT_WP_PLUGIN_FILE, [Uninstaller::class, 'uninstall']);
        
        // Load text domain.
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
        
        // Initialize core components.
        add_action('plugins_loaded', [$this, 'init'], 0);
        
        // Register REST routes.
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        
        // Register admin menus.
        add_action('admin_menu', [$this, 'registerAdminMenus']);
        
        // Enqueue admin assets.
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        
        // Register cron schedules.
        add_filter('cron_schedules', [$this, 'registerCronSchedules']);
        
        // Schedule cron jobs.
        add_action('wp', [$this, 'scheduleCronJobs']);
        
        // Frontend SEO output (if no SEO plugin detected).
        add_action('wp_head', [$this, 'outputFrontendSeo'], 1);
        add_filter('wp_title', [$this, 'filterTitle'], 10, 2);
    }
    
    /**
     * Plugin activation.
     */
    public function activate(): void
    {
        // Create database tables.
        $installer = new Core\Installer();
        $installer->createTables();
        
        // Add capabilities.
        $capabilities = new Core\Capabilities();
        $capabilities->add();
        
        // Schedule cron jobs.
        $cron = new Core\Cron();
        $cron->scheduleJobs();
        
        // Set default options.
        $this->setDefaultOptions();
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation.
     */
    public function deactivate(): void
    {
        // Clear scheduled cron jobs.
        wp_clear_scheduled_hook('seo_autopilot_weekly_audit');
        wp_clear_scheduled_hook('seo_autopilot_weekly_gsc_sync');
        wp_clear_scheduled_hook('seo_autopilot_monthly_freshness_scan');
        wp_clear_scheduled_hook('seo_autopilot_daily_cleanup');
        
        // Clear transients.
        delete_transient('seo_autopilot_dashboard_stats');
        delete_transient('seo_autopilot_cache_*');
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options.
     */
    private function setDefaultOptions(): void
    {
        $defaults = [
            'enabled' => true,
            'post_types' => ['post', 'page'],
            'include_pages' => true,
            'scan_frequency' => 'weekly',
            'auto_detect_seo_plugins' => true,
            'enable_logs' => true,
            'data_retention_days' => 90,
            'require_approval' => true,
            'create_backup' => true,
            'allow_auto_publish' => false,
            'allow_rollback' => true,
            'rollback_retention_days' => 30,
            'preserve_shortcodes' => true,
            'preserve_blocks' => true,
            'traveler_enabled' => true,
            'traveler_auto_detect' => true,
            'default_language' => 'en',
            'default_country' => 'US',
            'meta_title_min' => 30,
            'meta_title_max' => 60,
            'meta_description_min' => 120,
            'meta_description_max' => 160,
        ];
        
        $existing = get_option('seo_autopilot_settings', []);
        $settings = wp_parse_args($existing, $defaults);
        
        update_option('seo_autopilot_settings', $settings, 'yes');
    }
    
    /**
     * Load plugin text domain.
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'seo-autopilot-wp',
            false,
            dirname(SEO_AUTOPILOT_WP_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Initialize core components.
     */
    public function init(): void
    {
        // Initialize core components.
        $this->components['logger'] = new Core\Logger();
        $this->components['container'] = new Core\Container();
        
        // Initialize admin components.
        if (is_admin()) {
            $this->components['admin_menu'] = new Admin\AdminMenu();
            $this->components['admin_assets'] = new Admin\Assets();
        }
        
        // Initialize SEO plugin detector.
        $this->components['seo_detector'] = new Optimizer\SeoPluginDetector();
        
        // Initialize Traveler detector.
        if (get_option('seo_autopilot_settings', [])['traveler_enabled'] ?? true) {
            $this->components['traveler_detector'] = new Traveler\TravelerDetector();
        }
        
        /**
         * Fires after the plugin has been initialized.
         *
         * @since 1.0.0
         * @param Plugin $plugin The plugin instance.
         */
        do_action('seo_autopilot_loaded', $this);
    }
    
    /**
     * Register REST API routes.
     */
    public function registerRestRoutes(): void
    {
        $controllers = [
            Rest\DashboardController::class,
            Rest\AuditController::class,
            Rest\OptimizerController::class,
            Rest\ApprovalsController::class,
            Rest\GeneratorController::class,
            Rest\KeywordsController::class,
            Rest\SchemaController::class,
            Rest\ReportsController::class,
            Rest\AutomationController::class,
            Rest\TravelerController::class,
            Rest\GscController::class,
            Rest\SettingsController::class,
        ];
        
        foreach ($controllers as $controller_class) {
            if (class_exists($controller_class)) {
                $controller = new $controller_class();
                $controller->register_routes();
            }
        }
    }
    
    /**
     * Register admin menu pages.
     */
    public function registerAdminMenus(): void
    {
        if (!current_user_can('manage_seo_autopilot')) {
            return;
        }
        
        $admin_menu = new Admin\AdminMenu();
        $admin_menu->register();
    }
    
    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueueAdminAssets(string $hook): void
    {
        // Only load on our admin pages.
        if (strpos($hook, 'seo-autopilot') === false) {
            return;
        }
        
        $assets = new Admin\Assets();
        $assets->enqueue($hook);
    }
    
    /**
     * Register custom cron schedules.
     *
     * @param array<string, array<string, int|string>> $schedules Existing cron schedules.
     * @return array<string, array<string, int|string>> Modified cron schedules.
     */
    public function registerCronSchedules(array $schedules): array
    {
        $schedules['seo_autopilot_weekly'] = [
            'interval' => WEEK_IN_SECONDS,
            'display' => __('Once Weekly', 'seo-autopilot-wp'),
        ];
        
        $schedules['seo_autopilot_monthly'] = [
            'interval' => MONTH_IN_SECONDS,
            'display' => __('Once Monthly', 'seo-autopilot-wp'),
        ];
        
        return $schedules;
    }
    
    /**
     * Schedule plugin cron jobs.
     */
    public function scheduleCronJobs(): void
    {
        $settings = get_option('seo_autopilot_settings', []);
        
        // Weekly SEO audit.
        if (!wp_next_scheduled('seo_autopilot_weekly_audit')) {
            wp_schedule_event(time(), 'seo_autopilot_weekly', 'seo_autopilot_weekly_audit');
        }
        
        // Weekly GSC sync.
        if (!wp_next_scheduled('seo_autopilot_weekly_gsc_sync')) {
            wp_schedule_event(time(), 'seo_autopilot_weekly', 'seo_autopilot_weekly_gsc_sync');
        }
        
        // Monthly content freshness scan.
        if (!wp_next_scheduled('seo_autopilot_monthly_freshness_scan')) {
            wp_schedule_event(time(), 'seo_autopilot_monthly', 'seo_autopilot_monthly_freshness_scan');
        }
        
        // Daily cleanup.
        if (!wp_next_scheduled('seo_autopilot_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'seo_autopilot_daily_cleanup');
        }
    }
    
    /**
     * Output frontend SEO tags if no SEO plugin is active.
     */
    public function outputFrontendSeo(): void
    {
        // Skip if SEO plugin is detected.
        $settings = get_option('seo_autopilot_settings', []);
        if ($settings['auto_detect_seo_plugins'] ?? true) {
            $detector = new Optimizer\SeoPluginDetector();
            if ($detector->detect()) {
                return;
            }
        }
        
        // Only output on singular posts/pages.
        if (!is_singular()) {
            return;
        }
        
        $post_id = get_queried_object_id();
        if (!$post_id) {
            return;
        }
        
        $schema_injector = new Schema\SchemaInjector();
        $schema_injector->inject($post_id);
    }
    
    /**
     * Filter page title for frontend SEO.
     *
     * @param string $title Current title.
     * @param string $sep Title separator.
     * @return string Modified title.
     */
    public function filterTitle(string $title, string $sep = ''): string
    {
        // Skip if SEO plugin is detected.
        $settings = get_option('seo_autopilot_settings', []);
        if ($settings['auto_detect_seo_plugins'] ?? true) {
            $detector = new Optimizer\SeoPluginDetector();
            if ($detector->detect()) {
                return $title;
            }
        }
        
        if (!is_singular()) {
            return $title;
        }
        
        $post_id = get_queried_object_id();
        if (!$post_id) {
            return $title;
        }
        
        // Try to get SEO title from post meta.
        $seo_title = get_post_meta($post_id, '_seo_autopilot_title', true);
        
        if ($seo_title) {
            return $seo_title;
        }
        
        return $title;
    }
    
    /**
     * Get a component by key.
     *
     * @param string $key Component key.
     * @return object|null Component instance or null if not found.
     */
    public function getComponent(string $key): ?object
    {
        return $this->components[$key] ?? null;
    }
    
    /**
     * Get all components.
     *
     * @return array<string, object> All components.
     */
    public function getComponents(): array
    {
        return $this->components;
    }
}

// Initialize the plugin.
Plugin::getInstance();
