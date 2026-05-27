<?php
/**
 * Cron class - manages scheduled cron jobs.
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
 * Cron class.
 */
class Cron
{
    /**
     * Schedule plugin cron jobs.
     */
    public function scheduleJobs(): void
    {
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
     * Unschedule all plugin cron jobs.
     */
    public function unscheduleJobs(): void
    {
        wp_clear_scheduled_hook('seo_autopilot_weekly_audit');
        wp_clear_scheduled_hook('seo_autopilot_weekly_gsc_sync');
        wp_clear_scheduled_hook('seo_autopilot_monthly_freshness_scan');
        wp_clear_scheduled_hook('seo_autopilot_daily_cleanup');
    }
    
    /**
     * Get all scheduled job hooks.
     *
     * @return array<string>
     */
    public static function getHooks(): array
    {
        return [
            'seo_autopilot_weekly_audit',
            'seo_autopilot_weekly_gsc_sync',
            'seo_autopilot_monthly_freshness_scan',
            'seo_autopilot_daily_cleanup',
        ];
    }
}
