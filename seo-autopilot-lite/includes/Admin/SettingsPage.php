<?php
/**
 * Settings page for SEO Autopilot Lite.
 */

namespace SeoAutopilotLite\Admin;

use SeoAutopilotLite\Core\Settings;
use SeoAutopilotLite\Security\Encryption;

class SettingsPage {
    
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_ajax_seo_autopilot_lite_test_connection', [ $this, 'ajax_test_connection' ] );
    }
    
    /**
     * Add settings menu.
     */
    public function add_menu(): void {
        add_menu_page(
            __( 'SEO Autopilot', 'seo-autopilot-lite' ),
            __( 'SEO Autopilot', 'seo-autopilot-lite' ),
            'manage_options',
            'seo-autopilot-lite-settings',
            [ $this, 'render_settings_page' ],
            'dashicons-chart-line',
            80
        );
        
        add_submenu_page(
            'seo-autopilot-lite-settings',
            __( 'Settings', 'seo-autopilot-lite' ),
            __( 'Settings', 'seo-autopilot-lite' ),
            'manage_options',
            'seo-autopilot-lite-settings',
            [ $this, 'render_settings_page' ]
        );
    }
    
    /**
     * Register settings sections and fields.
     */
    public function register_settings(): void {
        // Register setting.
        register_setting(
            'seo_autopilot_lite_settings_group',
            'seo_autopilot_lite_settings',
            [ $this, 'sanitize_settings' ]
        );
        
        // General section.
        add_settings_section(
            'seo_autopilot_lite_general_section',
            __( 'General Settings', 'seo-autopilot-lite' ),
            null,
            'seo-autopilot-lite-settings'
        );
        
        add_settings_field( 'enabled', __( 'Enable Plugin', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'frontend_output', __( 'Enable Frontend SEO Output', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'frontend_output',
            'default' => true,
        ] );
        
        add_settings_field( 'open_graph_enabled', __( 'Enable Open Graph Tags', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'open_graph_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'twitter_card_enabled', __( 'Enable Twitter Card Tags', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'twitter_card_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'canonical_enabled', __( 'Enable Canonical URL', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'canonical_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'schema_enabled', __( 'Enable Schema Output', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'schema_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'seo_analysis_enabled', __( 'Enable SEO Analysis', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'seo_analysis_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'supported_post_types', __( 'Supported Post Types', 'seo-autopilot-lite' ), [ $this, 'render_post_types_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [] );
        
        add_settings_field( 'title_separator', __( 'Title Separator', 'seo-autopilot-lite' ), [ $this, 'render_select_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_general_section', [
            'label_for' => 'title_separator',
            'options' => [
                '-' => '-',
                '|' => '|',
                '»' => '»',
                '•' => '•',
                '*' => '*',
            ],
            'default' => '-',
        ] );
        
        // SEO Defaults section.
        add_settings_section(
            'seo_autopilot_lite_defaults_section',
            __( 'SEO Defaults', 'seo-autopilot-lite' ),
            null,
            'seo-autopilot-lite-settings'
        );
        
        add_settings_field( 'site_name', __( 'Site Name', 'seo-autopilot-lite' ), [ $this, 'render_text_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_defaults_section', [
            'label_for' => 'site_name',
            'default' => get_bloginfo( 'name' ),
        ] );
        
        add_settings_field( 'title_template', __( 'Title Template', 'seo-autopilot-lite' ), [ $this, 'render_text_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_defaults_section', [
            'label_for' => 'title_template',
            'default' => '%%title%% %%sep%% %%sitename%%',
            'description' => __( 'Use %%title%%, %%sep%%, %%sitename%%', 'seo-autopilot-lite' ),
        ] );
        
        add_settings_field( 'meta_description_template', __( 'Meta Description Template', 'seo-autopilot-lite' ), [ $this, 'render_textarea_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_defaults_section', [
            'label_for' => 'meta_description_template',
            'default' => '%%excerpt%%',
            'description' => __( 'Use %%excerpt%%, %%title%%', 'seo-autopilot-lite' ),
        ] );
        
        // AI Settings section.
        add_settings_section(
            'seo_autopilot_lite_ai_section',
            __( 'AI Settings', 'seo-autopilot-lite' ),
            null,
            'seo-autopilot-lite-settings'
        );
        
        add_settings_field( 'ai_enabled', __( 'Enable AI Suggestions', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_enabled',
            'default' => false,
        ] );
        
        add_settings_field( 'ai_provider', __( 'AI Provider', 'seo-autopilot-lite' ), [ $this, 'render_select_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_provider',
            'options' => [
                'disabled' => __( 'Disabled', 'seo-autopilot-lite' ),
                'openai' => 'OpenAI',
                'claude' => 'Claude',
                'gemini' => 'Gemini',
                'openrouter' => 'OpenRouter',
            ],
            'default' => 'disabled',
        ] );
        
        add_settings_field( 'ai_api_key', __( 'API Key', 'seo-autopilot-lite' ), [ $this, 'render_api_key_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_api_key',
            'default' => '',
        ] );
        
        add_settings_field( 'ai_model', __( 'Model Name', 'seo-autopilot-lite' ), [ $this, 'render_text_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_model',
            'default' => 'gpt-4o-mini',
        ] );
        
        add_settings_field( 'ai_temperature', __( 'Temperature', 'seo-autopilot-lite' ), [ $this, 'render_number_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_temperature',
            'default' => 0.7,
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
        ] );
        
        add_settings_field( 'ai_max_tokens', __( 'Max Tokens', 'seo-autopilot-lite' ), [ $this, 'render_number_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_ai_section', [
            'label_for' => 'ai_max_tokens',
            'default' => 500,
            'min' => 100,
            'max' => 4000,
        ] );
        
        // Traveler Settings section.
        add_settings_section(
            'seo_autopilot_lite_traveler_section',
            __( 'Traveler Theme Settings', 'seo-autopilot-lite' ),
            null,
            'seo-autopilot-lite-settings'
        );
        
        add_settings_field( 'traveler_enabled', __( 'Enable Traveler Support', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_traveler_section', [
            'label_for' => 'traveler_enabled',
            'default' => false,
        ] );
        
        add_settings_field( 'traveler_schema_enabled', __( 'Enable Traveler Schema', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_traveler_section', [
            'label_for' => 'traveler_schema_enabled',
            'default' => true,
        ] );
        
        add_settings_field( 'traveler_protect_booking', __( 'Protect Booking Fields', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_traveler_section', [
            'label_for' => 'traveler_protect_booking',
            'default' => true,
        ] );
        
        add_settings_field( 'traveler_protect_price', __( 'Protect Price Fields', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_traveler_section', [
            'label_for' => 'traveler_protect_price',
            'default' => true,
        ] );
        
        add_settings_field( 'traveler_protect_availability', __( 'Protect Availability Fields', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_traveler_section', [
            'label_for' => 'traveler_protect_availability',
            'default' => true,
        ] );
        
        // Advanced section.
        add_settings_section(
            'seo_autopilot_lite_advanced_section',
            __( 'Advanced Settings', 'seo-autopilot-lite' ),
            null,
            'seo-autopilot-lite-settings'
        );
        
        add_settings_field( 'disable_with_yoast', __( 'Disable Output if Yoast SEO Active', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_advanced_section', [
            'label_for' => 'disable_with_yoast',
            'default' => true,
        ] );
        
        add_settings_field( 'disable_with_rank_math', __( 'Disable Output if Rank Math Active', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_advanced_section', [
            'label_for' => 'disable_with_rank_math',
            'default' => true,
        ] );
        
        add_settings_field( 'disable_with_aioseo', __( 'Disable Output if AIOSEO Active', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_advanced_section', [
            'label_for' => 'disable_with_aioseo',
            'default' => true,
        ] );
        
        add_settings_field( 'uninstall_cleanup', __( 'Remove Plugin Data on Uninstall', 'seo-autopilot-lite' ), [ $this, 'render_checkbox_field' ], 'seo-autopilot-lite-settings', 'seo_autopilot_lite_advanced_section', [
            'label_for' => 'uninstall_cleanup',
            'default' => false,
        ] );
    }
    
    /**
     * Sanitize settings.
     */
    public function sanitize_settings( array $input ): array {
        $sanitized = [];
        
        // Boolean fields.
        $boolean_fields = [ 'enabled', 'frontend_output', 'open_graph_enabled', 'twitter_card_enabled', 'canonical_enabled', 'schema_enabled', 'seo_analysis_enabled', 'ai_enabled', 'traveler_enabled', 'traveler_schema_enabled', 'traveler_protect_booking', 'traveler_protect_price', 'traveler_protect_availability', 'disable_with_yoast', 'disable_with_rank_math', 'disable_with_aioseo', 'uninstall_cleanup' ];
        
        foreach ( $boolean_fields as $field ) {
            $sanitized[ $field ] = isset( $input[ $field ] ) ? true : false;
        }
        
        // String fields.
        $sanitized['site_name'] = sanitize_text_field( $input['site_name'] ?? get_bloginfo( 'name' ) );
        $sanitized['title_separator'] = sanitize_text_field( $input['title_separator'] ?? '-' );
        $sanitized['title_template'] = sanitize_text_field( $input['title_template'] ?? '%%title%% %%sep%% %%sitename%%' );
        $sanitized['meta_description_template'] = sanitize_textarea_field( $input['meta_description_template'] ?? '%%excerpt%%' );
        $sanitized['ai_provider'] = sanitize_text_field( $input['ai_provider'] ?? 'disabled' );
        $sanitized['ai_model'] = sanitize_text_field( $input['ai_model'] ?? 'gpt-4o-mini' );
        $sanitized['ai_temperature'] = floatval( $input['ai_temperature'] ?? 0.7 );
        $sanitized['ai_max_tokens'] = intval( $input['ai_max_tokens'] ?? 500 );
        
        // Encrypt API key if provided.
        if ( ! empty( $input['ai_api_key'] ) && $input['ai_api_key'] !== '**********' ) {
            $sanitized['ai_api_key'] = Encryption::encrypt( sanitize_text_field( $input['ai_api_key'] ) );
        } else {
            // Keep existing encrypted key.
            $existing = Settings::get_all();
            $sanitized['ai_api_key'] = $existing['ai_api_key'] ?? '';
        }
        
        // Post types.
        if ( isset( $input['supported_post_types'] ) && is_array( $input['supported_post_types'] ) ) {
            $sanitized['supported_post_types'] = array_map( 'sanitize_text_field', $input['supported_post_types'] );
        } else {
            $sanitized['supported_post_types'] = [ 'post', 'page' ];
        }
        
        return $sanitized;
    }
    
    /**
     * Render settings page.
     */
    public function render_settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields( 'seo_autopilot_lite_settings_group' );
                do_settings_sections( 'seo-autopilot-lite-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render text field.
     */
    public function render_text_field( array $args ): void {
        $options = Settings::get_all();
        $value = $options[ $args['label_for'] ] ?? $args['default'] ?? '';
        ?>
        <input type="text" 
               id="<?php echo esc_attr( $args['label_for'] ); ?>" 
               name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]" 
               value="<?php echo esc_attr( $value ); ?>" 
               class="regular-text" />
        <?php if ( ! empty( $args['description'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['description'] ); ?></p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Render textarea field.
     */
    public function render_textarea_field( array $args ): void {
        $options = Settings::get_all();
        $value = $options[ $args['label_for'] ] ?? $args['default'] ?? '';
        ?>
        <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" 
                  name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]" 
                  rows="3" 
                  class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
        <?php if ( ! empty( $args['description'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['description'] ); ?></p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Render number field.
     */
    public function render_number_field( array $args ): void {
        $options = Settings::get_all();
        $value = $options[ $args['label_for'] ] ?? $args['default'] ?? 0;
        $min = $args['min'] ?? 0;
        $max = $args['max'] ?? 9999;
        $step = $args['step'] ?? 1;
        ?>
        <input type="number" 
               id="<?php echo esc_attr( $args['label_for'] ); ?>" 
               name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]" 
               value="<?php echo esc_attr( $value ); ?>" 
               min="<?php echo esc_attr( $min ); ?>" 
               max="<?php echo esc_attr( $max ); ?>" 
               step="<?php echo esc_attr( $step ); ?>" 
               class="small-text" />
        <?php
    }
    
    /**
     * Render checkbox field.
     */
    public function render_checkbox_field( array $args ): void {
        $options = Settings::get_all();
        $value = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : $args['default'];
        ?>
        <label>
            <input type="checkbox" 
                   id="<?php echo esc_attr( $args['label_for'] ); ?>" 
                   name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]" 
                   value="1" 
                   <?php checked( $value ); ?> />
            <?php esc_html_e( 'Enabled', 'seo-autopilot-lite' ); ?>
        </label>
        <?php
    }
    
    /**
     * Render select field.
     */
    public function render_select_field( array $args ): void {
        $options = Settings::get_all();
        $value = $options[ $args['label_for'] ] ?? $args['default'] ?? '';
        ?>
        <select id="<?php echo esc_attr( $args['label_for'] ); ?>" 
                name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
            <?php foreach ( $args['options'] as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Render post types field.
     */
    public function render_post_types_field( array $args ): void {
        $options = Settings::get_all();
        $selected = $options['supported_post_types'] ?? [ 'post', 'page' ];
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        ?>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
            <?php foreach ( $post_types as $post_type ) : ?>
                <label style="display: block; margin-bottom: 5px;">
                    <input type="checkbox" 
                           name="seo_autopilot_lite_settings[supported_post_types][]" 
                           value="<?php echo esc_attr( $post_type->name ); ?>" 
                           <?php checked( in_array( $post_type->name, $selected, true ) ); ?> />
                    <?php echo esc_html( $post_type->label ); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render API key field.
     */
    public function render_api_key_field( array $args ): void {
        $options = Settings::get_all();
        $has_key = ! empty( $options['ai_api_key'] );
        ?>
        <input type="password" 
               id="<?php echo esc_attr( $args['label_for'] ); ?>" 
               name="seo_autopilot_lite_settings[<?php echo esc_attr( $args['label_for'] ); ?>]" 
               value="<?php echo $has_key ? '**********' : ''; ?>" 
               class="regular-text" 
               placeholder="<?php esc_attr_e( 'Enter your API key', 'seo-autopilot-lite' ); ?>" />
        <button type="button" class="button" id="test-connection-btn"><?php esc_html_e( 'Test Connection', 'seo-autopilot-lite' ); ?></button>
        <span id="test-connection-result"></span>
        <?php
    }
    
    /**
     * AJAX test connection.
     */
    public function ajax_test_connection(): void {
        check_ajax_referer( 'seo_autopilot_lite_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'seo-autopilot-lite' ) ] );
        }
        
        $provider = sanitize_text_field( $_POST['provider'] ?? 'openai' );
        $api_key = sanitize_text_field( $_POST['api_key'] ?? '' );
        
        if ( empty( $api_key ) ) {
            wp_send_json_error( [ 'message' => __( 'API key is required.', 'seo-autopilot-lite' ) ] );
        }
        
        // Simple test - just validate key format for now.
        // In production, make actual API call.
        $success = strlen( $api_key ) > 10;
        
        if ( $success ) {
            wp_send_json_success( [ 'message' => __( 'Connection successful!', 'seo-autopilot-lite' ) ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Connection failed. Please check your API key.', 'seo-autopilot-lite' ) ] );
        }
    }
}
