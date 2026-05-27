<?php
/**
 * Activator class - handles plugin activation logic.
 *
 * @package SeoAutopilotWp\Core
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Core;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activator class.
 */
class Activator
{
    /**
     * Activate the plugin.
     */
    public static function activate(): void
    {
        // Create database tables.
        $installer = new Installer();
        $installer->createTables();
        
        // Add capabilities.
        $capabilities = new Capabilities();
        $capabilities->add();
        
        // Schedule cron jobs.
        $cron = new Cron();
        $cron->scheduleJobs();
        
        // Set default options.
        self::setDefaultOptions();
        
        // Log activation.
        $logger = new Logger();
        $logger->info('Plugin activated', ['context' => 'activation']);
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options.
     */
    private static function setDefaultOptions(): void
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
}
