<?php
/**
 * Uninstaller - cleans up plugin data on uninstall.
 */

namespace SeoAutopilotLite\Core;

class Uninstaller {
    
    /**
     * Run uninstall cleanup.
     */
    public static function uninstall(): void {
        // Check if user wants to clean up data.
        $settings = Settings::get_all();
        
        if ( empty( $settings['uninstall_cleanup'] ) ) {
            return;
        }
        
        // Delete plugin options.
        delete_option( 'seo_autopilot_lite_settings' );
        
        // Delete post meta for all posts.
        global $wpdb;
        
        $meta_keys = [
            '_seo_autopilot_title',
            '_seo_autopilot_description',
            '_seo_autopilot_focus_keyword',
            '_seo_autopilot_canonical',
            '_seo_autopilot_noindex',
            '_seo_autopilot_nofollow',
            '_seo_autopilot_og_title',
            '_seo_autopilot_og_description',
            '_seo_autopilot_og_image',
            '_seo_autopilot_twitter_title',
            '_seo_autopilot_twitter_description',
            '_seo_autopilot_twitter_image',
            '_seo_autopilot_schema_type',
            '_seo_autopilot_schema_json',
            '_seo_autopilot_seo_score',
            '_seo_autopilot_analysis',
        ];
        
        foreach ( $meta_keys as $meta_key ) {
            $wpdb->delete(
                $wpdb->postmeta,
                [ 'meta_key' => $meta_key ],
                [ '%s' ]
            );
        }
        
        // Clear any transients.
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_seo_autopilot_lite_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_seo_autopilot_lite_%'" );
    }
}
