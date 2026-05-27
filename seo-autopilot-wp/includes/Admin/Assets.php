<?php
/**
 * Assets class - handles admin asset loading.
 *
 * @package SeoAutopilotWp\Admin
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Admin;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Assets class.
 */
class Assets
{
    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue(string $hook): void
    {
        // Get asset manifest for versioning.
        $manifest = $this->getManifest();
        
        // Enqueue styles.
        $css_file = 'css/admin.css';
        $css_version = $manifest['css/admin.css']['version'] ?? SEO_AUTOPILOT_WP_VERSION;
        
        wp_enqueue_style(
            'seo-autopilot-admin',
            SEO_AUTOPILOT_WP_PLUGIN_URL . 'build/css/admin.css',
            [],
            $css_version
        );
        
        // Enqueue scripts.
        $js_file = 'js/admin.js';
        $js_version = $manifest['js/admin.js']['version'] ?? SEO_AUTOPILOT_WP_VERSION;
        
        wp_enqueue_script(
            'seo-autopilot-admin',
            SEO_AUTOPILOT_WP_PLUGIN_URL . 'build/js/admin.js',
            ['wp-element', 'wp-api-fetch', 'wp-components', 'wp-i18n'],
            $js_version,
            true
        );
        
        // Localize script with config.
        wp_localize_script('seo-autopilot-admin', 'seoAutopilotConfig', [
            'apiUrl' => rest_url('seo-autopilot/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => admin_url(),
            'pluginUrl' => SEO_AUTOPILOT_WP_PLUGIN_URL,
            'version' => SEO_AUTOPILOT_WP_VERSION,
            'settings' => get_option('seo_autopilot_settings', []),
            'userCapabilities' => [
                'canManage' => current_user_can('manage_seo_autopilot'),
                'canViewDashboard' => current_user_can('seo_autopilot_view_dashboard'),
                'canRunAudits' => current_user_can('seo_autopilot_run_audits'),
                'canGenerateAi' => current_user_can('seo_autopilot_generate_ai'),
                'canApproveChanges' => current_user_can('seo_autopilot_approve_changes'),
                'canPublishChanges' => current_user_can('seo_autopilot_publish_changes'),
                'canManageSettings' => current_user_can('seo_autopilot_manage_settings'),
                'canViewReports' => current_user_can('seo_autopilot_view_reports'),
            ],
            'i18n' => [
                'loading' => __('Loading...', 'seo-autopilot-wp'),
                'error' => __('An error occurred', 'seo-autopilot-wp'),
                'save' => __('Save', 'seo-autopilot-wp'),
                'cancel' => __('Cancel', 'seo-autopilot-wp'),
                'delete' => __('Delete', 'seo-autopilot-wp'),
                'edit' => __('Edit', 'seo-autopilot-wp'),
                'view' => __('View', 'seo-autopilot-wp'),
                'success' => __('Success', 'seo-autopilot-wp'),
                'warning' => __('Warning', 'seo-autopilot-wp'),
                'confirm' => __('Are you sure?', 'seo-autopilot-wp'),
            ],
        ]);
        
        // Add inline script for WordPress admin compatibility.
        wp_add_inline_script(
            'seo-autopilot-admin',
            'window.seoAutopilot = window.seoAutopilot || {};',
            'before'
        );
    }
    
    /**
     * Get asset manifest for versioning.
     *
     * @return array<string, mixed>
     */
    private function getManifest(): array
    {
        static $manifest = null;
        
        if ($manifest !== null) {
            return $manifest;
        }
        
        $manifest_path = SEO_AUTOPILOT_WP_PLUGIN_DIR . 'build/manifest.json';
        
        if (file_exists($manifest_path)) {
            $manifest_content = file_get_contents($manifest_path);
            if ($manifest_content !== false) {
                $manifest = json_decode($manifest_content, true);
                if (is_array($manifest)) {
                    return $manifest;
                }
            }
        }
        
        $manifest = [];
        return $manifest;
    }
    
    /**
     * Register admin assets without enqueuing.
     */
    public function register(): void
    {
        // Register styles.
        wp_register_style(
            'seo-autopilot-admin',
            SEO_AUTOPILOT_WP_PLUGIN_URL . 'build/css/admin.css',
            [],
            SEO_AUTOPILOT_WP_VERSION
        );
        
        // Register scripts.
        wp_register_script(
            'seo-autopilot-admin',
            SEO_AUTOPILOT_WP_PLUGIN_URL . 'build/js/admin.js',
            ['wp-element', 'wp-api-fetch', 'wp-components', 'wp-i18n'],
            SEO_AUTOPILOT_WP_VERSION,
            true
        );
    }
}
