<?php
/**
 * Activator class - runs on plugin activation.
 */

namespace SeoAutopilotLite\Core;

class Activator {
    
    public function activate(): void {
        // Set default options.
        $this->set_default_options();
        
        // Add capabilities to admin users.
        $this->add_capabilities();
        
        // Flush rewrite rules.
        flush_rewrite_rules();
        
        // Schedule any cron events if needed.
        $this->schedule_events();
    }
    
    private function set_default_options(): void {
        $defaults = [
            'enabled' => true,
            'frontend_output' => true,
            'open_graph_enabled' => true,
            'twitter_card_enabled' => true,
            'canonical_enabled' => true,
            'schema_enabled' => true,
            'seo_analysis_enabled' => true,
            'ai_enabled' => false,
            'ai_provider' => 'disabled',
            'ai_api_key' => '',
            'ai_model' => 'gpt-4o-mini',
            'ai_temperature' => 0.7,
            'ai_max_tokens' => 500,
            'traveler_enabled' => false,
            'traveler_schema_enabled' => true,
            'traveler_cpt_enabled' => true,
            'traveler_protect_booking' => true,
            'traveler_protect_price' => true,
            'traveler_protect_availability' => true,
            'supported_post_types' => [ 'post', 'page' ],
            'title_separator' => '-',
            'title_template' => '%%title%% %%sep%% %%sitename%%',
            'meta_description_template' => '%%excerpt%%',
            'site_name' => get_bloginfo( 'name' ),
            'organization_name' => '',
            'logo_url' => '',
            'default_social_image' => '',
            'default_country' => '',
            'default_language' => get_locale(),
            'business_type' => 'LocalBusiness',
            'business_address' => '',
            'business_phone' => '',
            'social_links' => [],
            'disable_with_yoast' => true,
            'disable_with_rank_math' => true,
            'disable_with_aioseo' => true,
            'uninstall_cleanup' => false,
        ];
        
        $existing = get_option( 'seo_autopilot_lite_settings', [] );
        $merged = wp_parse_args( $existing, $defaults );
        
        update_option( 'seo_autopilot_lite_settings', $merged, 'yes' );
    }
    
    private function add_capabilities(): void {
        $role = get_role( 'administrator' );
        
        if ( $role ) {
            $capabilities = [
                'manage_seo_autopilot_lite',
                'edit_seo_autopilot_lite_posts',
            ];
            
            foreach ( $capabilities as $cap ) {
                $role->add_cap( $cap );
            }
        }
    }
    
    private function schedule_events(): void {
        // No recurring events needed for lite version.
    }
}
