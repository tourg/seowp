<?php
/**
 * Capabilities class - manages plugin capabilities.
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
 * Capabilities class.
 */
class Capabilities
{
    /**
     * Plugin capabilities.
     *
     * @var array<string, string>
     */
    private const CAPABILITIES = [
        'manage_seo_autopilot' => 'Manage SEO Autopilot',
        'seo_autopilot_view_dashboard' => 'View Dashboard',
        'seo_autopilot_run_audits' => 'Run Audits',
        'seo_autopilot_generate_ai' => 'Generate AI Content',
        'seo_autopilot_approve_changes' => 'Approve Changes',
        'seo_autopilot_publish_changes' => 'Publish Changes',
        'seo_autopilot_manage_settings' => 'Manage Settings',
        'seo_autopilot_view_reports' => 'View Reports',
    ];
    
    /**
     * Add plugin capabilities.
     */
    public function add(): void
    {
        // Get administrator role.
        $admin_role = get_role('administrator');
        
        if (!$admin_role) {
            return;
        }
        
        // Add each capability to administrator role.
        foreach (array_keys(self::CAPABILITIES) as $cap) {
            $admin_role->add_cap($cap, true);
        }
    }
    
    /**
     * Remove plugin capabilities.
     */
    public function remove(): void
    {
        // Get administrator role.
        $admin_role = get_role('administrator');
        
        if (!$admin_role) {
            return;
        }
        
        // Remove each capability from administrator role.
        foreach (array_keys(self::CAPABILITIES) as $cap) {
            $admin_role->remove_cap($cap);
        }
    }
    
    /**
     * Get all capability keys.
     *
     * @return array<string>
     */
    public static function getAll(): array
    {
        return array_keys(self::CAPABILITIES);
    }
    
    /**
     * Get capability label.
     *
     * @param string $cap Capability key.
     * @return string Capability label.
     */
    public static function getLabel(string $cap): string
    {
        return self::CAPABILITIES[$cap] ?? '';
    }
}
