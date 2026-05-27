<?php
/**
 * Title Rule - checks SEO title quality.
 */

namespace SeoAutopilotLite\SEO\Rules;

use SeoAutopilotLite\Core\Settings;

class TitleRule {
    
    /**
     * Analyze post title.
     */
    public function analyze( \WP_Post $post ): ?array {
        $seo_title = get_post_meta( $post->ID, '_seo_autopilot_title', true );
        $title = ! empty( $seo_title ) ? $seo_title : $post->post_title;
        $focus_keyword = get_post_meta( $post->ID, '_seo_autopilot_focus_keyword', true );
        
        $length = strlen( $title );
        $min_length = 30;
        $max_length = 60;
        
        $issues = [];
        $status = 'good';
        
        // Check if missing.
        if ( empty( $seo_title ) && empty( $post->post_title ) ) {
            return [
                'status' => 'bad',
                'title' => __( 'SEO Title', 'seo-autopilot-lite' ),
                'message' => __( 'No SEO title set.', 'seo-autopilot-lite' ),
                'recommendation' => __( 'Add an SEO title that includes your focus keyword.', 'seo-autopilot-lite' ),
                'weight' => 15,
            ];
        }
        
        // Check length.
        if ( $length < $min_length ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Title is too short (%d characters). Minimum recommended: %d characters.', 'seo-autopilot-lite' ), $length, $min_length );
        } elseif ( $length > $max_length ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Title is too long (%d characters). Maximum recommended: %d characters.', 'seo-autopilot-lite' ), $length, $max_length );
        }
        
        // Check focus keyword.
        if ( ! empty( $focus_keyword ) && false === stripos( $title, $focus_keyword ) ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Focus keyword "%s" not found in title.', 'seo-autopilot-lite' ), $focus_keyword );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'SEO Title', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Title length is %d characters.', 'seo-autopilot-lite' ), $length ),
                'recommendation' => '',
                'weight' => 15,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'SEO Title', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Optimize your title to include the focus keyword and keep it between 30-60 characters.', 'seo-autopilot-lite' ),
            'weight' => 15,
        ];
    }
}
