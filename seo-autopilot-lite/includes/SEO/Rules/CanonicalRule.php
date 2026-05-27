<?php
/**
 * Canonical Rule - checks canonical URL.
 */

namespace SeoAutopilotLite\SEO\Rules;

class CanonicalRule {
    
    /**
     * Analyze canonical URL.
     */
    public function analyze( \WP_Post $post ): ?array {
        $canonical = get_post_meta( $post->ID, '_seo_autopilot_canonical', true );
        
        // Check if empty (which is OK, will use default).
        if ( empty( $canonical ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Canonical URL', 'seo-autopilot-lite' ),
                'message' => __( 'Using default canonical URL.', 'seo-autopilot-lite' ),
                'recommendation' => '',
                'weight' => 5,
            ];
        }
        
        $issues = [];
        $status = 'good';
        
        // Check if it's a valid URL.
        if ( ! filter_var( $canonical, FILTER_VALIDATE_URL ) ) {
            $status = 'bad';
            $issues[] = __( 'Canonical URL is not a valid URL.', 'seo-autopilot-lite' );
        }
        
        // Check if it's an absolute URL.
        if ( ! preg_match( '/^https?:\/\//i', $canonical ) ) {
            $status = 'warning';
            $issues[] = __( 'Canonical URL should be absolute (start with http:// or https://).', 'seo-autopilot-lite' );
        }
        
        // Check if it matches the post permalink (if same origin).
        $permalink = get_permalink( $post->ID );
        $site_url = home_url();
        
        if ( false !== strpos( $canonical, $site_url ) && $canonical !== $permalink ) {
            $status = 'warning';
            $issues[] = __( 'Canonical URL differs from post permalink. Ensure this is intentional.', 'seo-autopilot-lite' );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Canonical URL', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Canonical URL is set to: %s', 'seo-autopilot-lite' ), $canonical ),
                'recommendation' => '',
                'weight' => 5,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Canonical URL', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Set a valid, absolute canonical URL.', 'seo-autopilot-lite' ),
            'weight' => 5,
        ];
    }
}
