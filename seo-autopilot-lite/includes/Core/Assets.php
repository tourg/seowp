<?php
/**
 * Assets loader for admin styles and scripts.
 */

namespace SeoAutopilotLite\Core;

class Assets {
    
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }
    
    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets( string $hook ): void {
        // Load on plugin settings page.
        if ( 'seo-autopilot-lite_page_seo-autopilot-lite-settings' === $hook ) {
            $this->enqueue_settings_assets();
        }
        
        // Load on post edit screens for supported post types.
        if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
            global $post;
            if ( $post && in_array( $post->post_type, Settings::get_supported_post_types(), true ) ) {
                $this->enqueue_metabox_assets();
            }
        }
    }
    
    /**
     * Enqueue settings page assets.
     */
    private function enqueue_settings_assets(): void {
        wp_enqueue_style(
            'seo-autopilot-lite-admin',
            SEO_AUTOPILOT_LITE_PLUGIN_URL . 'assets/css/admin.css',
            [],
            SEO_AUTOPILOT_LITE_VERSION
        );
        
        wp_enqueue_script(
            'seo-autopilot-lite-admin',
            SEO_AUTOPILOT_LITE_PLUGIN_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            SEO_AUTOPILOT_LITE_VERSION,
            true
        );
        
        wp_localize_script( 'seo-autopilot-lite-admin', 'seoAutopilotLiteAdmin', [
            'nonce' => wp_create_nonce( 'seo_autopilot_lite_admin' ),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'strings' => [
                'testConnection' => __( 'Testing connection...', 'seo-autopilot-lite' ),
                'connectionSuccess' => __( 'Connection successful!', 'seo-autopilot-lite' ),
                'connectionFailed' => __( 'Connection failed. Please check your API key.', 'seo-autopilot-lite' ),
                'saveSettings' => __( 'Settings saved.', 'seo-autopilot-lite' ),
            ],
        ] );
    }
    
    /**
     * Enqueue metabox assets.
     */
    private function enqueue_metabox_assets(): void {
        wp_enqueue_style(
            'seo-autopilot-lite-admin',
            SEO_AUTOPILOT_LITE_PLUGIN_URL . 'assets/css/admin.css',
            [],
            SEO_AUTOPILOT_LITE_VERSION
        );
        
        wp_enqueue_script(
            'seo-autopilot-lite-metabox',
            SEO_AUTOPILOT_LITE_PLUGIN_URL . 'assets/js/metabox.js',
            [ 'jquery' ],
            SEO_AUTOPILOT_LITE_VERSION,
            true
        );
        
        wp_localize_script( 'seo-autopilot-lite-metabox', 'seoAutopilotLiteMetabox', [
            'nonce' => wp_create_nonce( 'seo_autopilot_lite_metabox' ),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'aiEnabled' => Settings::is_ai_enabled(),
            'strings' => [
                'generating' => __( 'Generating...', 'seo-autopilot-lite' ),
                'generateTitle' => __( 'Generate with AI', 'seo-autopilot-lite' ),
                'generateDescription' => __( 'Generate with AI', 'seo-autopilot-lite' ),
                'generateFAQ' => __( 'Generate FAQ with AI', 'seo-autopilot-lite' ),
                'generateSchema' => __( 'Generate Schema with AI', 'seo-autopilot-lite' ),
            ],
        ] );
    }
}
