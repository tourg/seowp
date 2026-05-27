<?php
/**
 * LocalBusiness Schema generator.
 */

namespace SeoAutopilotLite\Schema;

use SeoAutopilotLite\Core\Settings;

class LocalBusinessSchema {
    
    /**
     * Generate LocalBusiness schema.
     */
    public function generate( \WP_Post $post ): array {
        $business_type = Settings::get_option( 'business_type', 'LocalBusiness' );
        $organization_name = Settings::get_option( 'organization_name', get_bloginfo( 'name' ) );
        $logo_url = Settings::get_option( 'logo_url', '' );
        $address = Settings::get_option( 'business_address', '' );
        $phone = Settings::get_option( 'business_phone', '' );
        $social_links = Settings::get_option( 'social_links', [] );
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $business_type,
            'name' => $organization_name,
            'url' => home_url(),
        ];
        
        // Add logo.
        if ( ! empty( $logo_url ) ) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                'url' => $logo_url,
            ];
        }
        
        // Add address.
        if ( ! empty( $address ) ) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
            ];
        }
        
        // Add phone.
        if ( ! empty( $phone ) ) {
            $schema['telephone'] = $phone;
        }
        
        // Add social links.
        if ( ! empty( $social_links ) && is_array( $social_links ) ) {
            $schema['sameAs'] = array_values( $social_links );
        }
        
        return $schema;
    }
}
