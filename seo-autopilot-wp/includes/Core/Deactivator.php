<?php
/**
 * Deactivator class - handles plugin deactivation logic.
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
 * Deactivator class.
 */
class Deactivator
{
    /**
     * Deactivate the plugin.
     */
    public static function deactivate(): void
    {
        // Clear scheduled cron jobs.
        wp_clear_scheduled_hook('seo_autopilot_weekly_audit');
        wp_clear_scheduled_hook('seo_autopilot_weekly_gsc_sync');
        wp_clear_scheduled_hook('seo_autopilot_monthly_freshness_scan');
        wp_clear_scheduled_hook('seo_autopilot_daily_cleanup');
        
        // Clear transients.
        delete_transient('seo_autopilot_dashboard_stats');
        delete_transient('seo_autopilot_cache_*');
        
        // Log deactivation.
        $logger = new Logger();
        $logger->info('Plugin deactivated', ['context' => 'deactivation']);
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
}
