<?php
/**
 * Uninstaller class - handles plugin uninstallation logic.
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
 * Uninstaller class.
 */
class Uninstaller
{
    /**
     * Uninstall the plugin.
     * 
     * Called when plugin is deleted via WordPress admin.
     */
    public static function uninstall(): void
    {
        // Check if user wants to remove all data.
        $remove_data = get_option('seo_autopilot_remove_data_on_uninstall', false);
        
        if (!$remove_data) {
            return;
        }
        
        // Verify user has permission.
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Delete database tables.
        self::dropTables();
        
        // Delete all options.
        self::deleteOptions();
        
        // Delete all transients.
        self::deleteTransients();
        
        // Clear scheduled cron jobs.
        self::clearCronJobs();
        
        // Log uninstallation.
        $logger = new Logger();
        $logger->info('Plugin uninstalled with data cleanup', ['context' => 'uninstall']);
    }
    
    /**
     * Drop all custom database tables.
     */
    private static function dropTables(): void
    {
        global $wpdb;
        
        $tables = [
            'audits',
            'issues',
            'drafts',
            'approvals',
            'backups',
            'publish_history',
            'keywords',
            'keyword_clusters',
            'reports',
            'automation_rules',
            'jobs',
            'logs',
            'token_usage',
            'gsc_connections',
            'gsc_metrics',
            'traveler_fields',
            'traveler_audits',
        ];
        
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . 'seo_autopilot_' . $table;
            $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
        }
    }
    
    /**
     * Delete all plugin options.
     */
    private static function deleteOptions(): void
    {
        $options = [
            'seo_autopilot_settings',
            'seo_autopilot_version',
            'seo_autopilot_db_version',
            'seo_autopilot_gsc_tokens',
            'seo_autopilot_ai_keys',
            'seo_autopilot_remove_data_on_uninstall',
        ];
        
        foreach ($options as $option) {
            delete_option($option);
        }
        
        // Delete network options if multisite.
        if (is_multisite()) {
            foreach ($options as $option) {
                delete_site_option($option);
            }
        }
    }
    
    /**
     * Delete all plugin transients.
     */
    private static function deleteTransients(): void
    {
        global $wpdb;
        
        // Delete transients matching our pattern.
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_seo_autopilot_%' 
             OR option_name LIKE '_transient_timeout_seo_autopilot_%'"
        );
        
        // Delete site transients if multisite.
        if (is_multisite()) {
            $wpdb->query(
                "DELETE FROM {$wpdb->sitemeta} 
                 WHERE meta_key LIKE '_site_transient_seo_autopilot_%' 
                 OR meta_key LIKE '_site_transient_timeout_seo_autopilot_%'"
            );
        }
    }
    
    /**
     * Clear all scheduled cron jobs.
     */
    private static function clearCronJobs(): void
    {
        $hooks = [
            'seo_autopilot_weekly_audit',
            'seo_autopilot_weekly_gsc_sync',
            'seo_autopilot_monthly_freshness_scan',
            'seo_autopilot_daily_cleanup',
        ];
        
        foreach ($hooks as $hook) {
            wp_clear_scheduled_hook($hook);
        }
    }
}
