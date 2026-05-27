<?php
/**
 * Traveler Theme Detector.
 */

namespace SeoAutopilotLite\Traveler;

use SeoAutopilotLite\Core\Settings;

class TravelerDetector {
    
    private bool $is_traveler = false;
    private array $traveler_cpts = [ 'st_tours', 'st_activity', 'st_hotel', 'st_rental', 'st_cars', 'st_location' ];
    
    public function __construct() {
        $this->detect_traveler();
        
        if ( $this->is_traveler ) {
            $this->init_traveler_support();
        }
    }
    
    /**
     * Detect if Traveler theme is active.
     */
    private function detect_traveler(): void {
        $theme = wp_get_theme();
        $theme_name = strtolower( $theme->get( 'Name' ) );
        $template = strtolower( $theme->get_template() );
        
        // Check theme name.
        if ( false !== strpos( $theme_name, 'traveler' ) || 
             false !== strpos( $template, 'traveler' ) ) {
            $this->is_traveler = true;
            return;
        }
        
        // Check for Traveler CPTs.
        foreach ( $this->traveler_cpts as $cpt ) {
            if ( post_type_exists( $cpt ) ) {
                $this->is_traveler = true;
                return;
            }
        }
        
        // Check for known Traveler meta keys.
        $traveler_meta_keys = [ 'st_price', 'st_availability', 'st_booking', 'st_tour_type' ];
        
        global $wpdb;
        $result = $wpdb->get_var( $wpdb->prepare(
            "SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key IN (%s, %s, %s, %s) LIMIT 1",
            $traveler_meta_keys
        ) );
        
        if ( $result ) {
            $this->is_traveler = true;
        }
    }
    
    /**
     * Initialize Traveler support.
     */
    private function init_traveler_support(): void {
        // Add Traveler CPTs to supported post types.
        add_filter( 'seo_autopilot_lite_supported_post_types', [ $this, 'add_traveler_cpts' ] );
        
        // Add Traveler meta box fields protection.
        if ( Settings::get_option( 'traveler_protect_booking', true ) ) {
            add_filter( 'wp_insert_post_data', [ $this, 'protect_traveler_fields' ], 10, 2 );
        }
    }
    
    /**
     * Add Traveler CPTs to supported post types.
     */
    public function add_traveler_cpts( array $post_types ): array {
        if ( ! Settings::get_option( 'traveler_cpt_enabled', true ) ) {
            return $post_types;
        }
        
        foreach ( $this->traveler_cpts as $cpt ) {
            if ( post_type_exists( $cpt ) && ! in_array( $cpt, $post_types, true ) ) {
                $post_types[] = $cpt;
            }
        }
        
        return $post_types;
    }
    
    /**
     * Protect Traveler booking/price fields from being modified.
     */
    public function protect_traveler_fields( array $data, array $postarr ): array {
        // This prevents accidental modification of Traveler system fields.
        // The actual protection happens in the MetaBox save method.
        
        return $data;
    }
    
    /**
     * Check if current post is a Traveler CPT.
     */
    public function is_traveler_post( int $post_id ): bool {
        $post_type = get_post_type( $post_id );
        return in_array( $post_type, $this->traveler_cpts, true );
    }
    
    /**
     * Get protected Traveler meta keys.
     */
    public static function get_protected_keys(): array {
        return [
            // Booking fields.
            'st_booking_dates',
            'st_booking_status',
            'st_booking_payment',
            'st_order_id',
            'st_customer_id',
            
            // Price fields.
            'st_price',
            'st_price_adult',
            'st_price_child',
            'st_price_infant',
            'st_discount',
            'st_sale_price',
            
            // Availability fields.
            'st_availability',
            'st_min_day_book',
            'st_max_day_book',
            'st_check_in_time',
            'st_check_out_time',
            
            // Calendar fields.
            'st_calendar',
            'st_unavailable_days',
            
            // Other system fields.
            'st_post_type',
            'st_status',
            'st_traveller_number',
        ];
    }
}
