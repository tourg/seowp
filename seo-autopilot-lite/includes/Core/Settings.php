<?php
/**
 * Settings manager class.
 */

namespace SeoAutopilotLite\Core;

class Settings {
    
    const OPTION_NAME = 'seo_autopilot_lite_settings';
    
    /**
     * Get all settings.
     */
    public static function get_all(): array {
        return get_option( self::OPTION_NAME, [] );
    }
    
    /**
     * Get a single setting value.
     */
    public static function get_option( string $key, $default = null ) {
        $settings = self::get_all();
        return $settings[ $key ] ?? $default;
    }
    
    /**
     * Update a single setting.
     */
    public static function update_option( string $key, $value ): bool {
        $settings = self::get_all();
        $settings[ $key ] = $value;
        return update_option( self::OPTION_NAME, $settings, 'yes' );
    }
    
    /**
     * Update multiple settings.
     */
    public static function update_settings( array $new_settings ): bool {
        $settings = self::get_all();
        $merged = array_merge( $settings, $new_settings );
        return update_option( self::OPTION_NAME, $merged, 'yes' );
    }
    
    /**
     * Check if plugin is enabled.
     */
    public static function is_enabled(): bool {
        return self::get_option( 'enabled', true );
    }
    
    /**
     * Check if frontend output is enabled.
     */
    public static function is_frontend_output_enabled(): bool {
        return self::get_option( 'frontend_output', true );
    }
    
    /**
     * Check if AI is enabled.
     */
    public static function is_ai_enabled(): bool {
        return self::get_option( 'ai_enabled', false );
    }
    
    /**
     * Get AI provider.
     */
    public static function get_ai_provider(): string {
        return self::get_option( 'ai_provider', 'disabled' );
    }
    
    /**
     * Get AI API key (encrypted).
     */
    public static function get_ai_api_key(): string {
        $key = self::get_option( 'ai_api_key', '' );
        if ( ! empty( $key ) ) {
            return Security\Encryption::decrypt( $key );
        }
        return '';
    }
    
    /**
     * Get supported post types.
     */
    public static function get_supported_post_types(): array {
        $types = self::get_option( 'supported_post_types', [ 'post', 'page' ] );
        if ( ! is_array( $types ) ) {
            $types = [ 'post', 'page' ];
        }
        
        // Add Traveler CPTs if enabled.
        if ( self::get_option( 'traveler_enabled', false ) && self::get_option( 'traveler_cpt_enabled', true ) ) {
            $traveler_cpts = [ 'st_tours', 'st_activity', 'st_hotel', 'st_rental', 'st_cars', 'st_location' ];
            $types = array_unique( array_merge( $types, $traveler_cpts ) );
        }
        
        return $types;
    }
    
    /**
     * Check if Yoast SEO is active and should disable output.
     */
    public static function should_disable_with_yoast(): bool {
        return self::get_option( 'disable_with_yoast', true ) && defined( 'WPSEO_VERSION' );
    }
    
    /**
     * Check if Rank Math is active and should disable output.
     */
    public static function should_disable_with_rank_math(): bool {
        return self::get_option( 'disable_with_rank_math', true ) && defined( 'RANK_MATH_VERSION' );
    }
    
    /**
     * Check if AIOSEO is active and should disable output.
     */
    public static function should_disable_with_aioseo(): bool {
        return self::get_option( 'disable_with_aioseo', true ) && defined( 'AIOSEO_VERSION' );
    }
    
    /**
     * Check if another SEO plugin is active.
     */
    public static function has_competing_seo_plugin(): bool {
        return self::should_disable_with_yoast() || 
               self::should_disable_with_rank_math() || 
               self::should_disable_with_aioseo();
    }
}
