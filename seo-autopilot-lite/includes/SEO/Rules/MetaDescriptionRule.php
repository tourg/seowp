<?php
/**
 * Meta Description Rule - checks meta description quality.
 */

namespace SeoAutopilotLite\SEO\Rules;

class MetaDescriptionRule {
    
    /**
     * Analyze post meta description.
     */
    public function analyze( \WP_Post $post ): ?array {
        $meta_description = get_post_meta( $post->ID, '_seo_autopilot_description', true );
        $focus_keyword = get_post_meta( $post->ID, '_seo_autopilot_focus_keyword', true );
        
        // Use excerpt or content as fallback.
        if ( empty( $meta_description ) ) {
            $meta_description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 20 );
        }
        
        $length = strlen( $meta_description );
        $min_length = 120;
        $max_length = 160;
        
        $issues = [];
        $status = 'good';
        
        // Check if missing.
        if ( empty( $meta_description ) ) {
            return [
                'status' => 'bad',
                'title' => __( 'Meta Description', 'seo-autopilot-lite' ),
                'message' => __( 'No meta description set.', 'seo-autopilot-lite' ),
                'recommendation' => __( 'Add a compelling meta description that includes your focus keyword.', 'seo-autopilot-lite' ),
                'weight' => 15,
            ];
        }
        
        // Check length.
        if ( $length < $min_length ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Description is too short (%d characters). Minimum recommended: %d characters.', 'seo-autopilot-lite' ), $length, $min_length );
        } elseif ( $length > $max_length ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Description is too long (%d characters). Maximum recommended: %d characters.', 'seo-autopilot-lite' ), $length, $max_length );
        }
        
        // Check focus keyword.
        if ( ! empty( $focus_keyword ) && false === stripos( $meta_description, $focus_keyword ) ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Focus keyword "%s" not found in description.', 'seo-autopilot-lite' ), $focus_keyword );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Meta Description', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Description length is %d characters.', 'seo-autopilot-lite' ), $length ),
                'recommendation' => '',
                'weight' => 15,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Meta Description', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Optimize your description to include the focus keyword and keep it between 120-160 characters.', 'seo-autopilot-lite' ),
            'weight' => 15,
        ];
    }
}
