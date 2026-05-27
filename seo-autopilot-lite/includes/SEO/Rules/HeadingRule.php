<?php
/**
 * Heading Rule - checks heading structure.
 */

namespace SeoAutopilotLite\SEO\Rules;

class HeadingRule {
    
    /**
     * Analyze heading structure.
     */
    public function analyze( \WP_Post $post ): ?array {
        $content = $post->post_content;
        
        // Extract headings.
        preg_match_all( '/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER );
        
        $headings = [];
        foreach ( $matches as $match ) {
            $headings[] = [
                'level' => intval( $match[1] ),
                'text' => wp_strip_all_tags( $match[2] ),
            ];
        }
        
        $issues = [];
        $status = 'good';
        
        // Check for H1.
        $h1_count = count( array_filter( $headings, fn( $h ) => 1 === $h['level'] ) );
        
        if ( 0 === $h1_count ) {
            $status = 'warning';
            $issues[] = __( 'No H1 heading found.', 'seo-autopilot-lite' );
        } elseif ( $h1_count > 1 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Multiple H1 headings found (%d). Use only one H1.', 'seo-autopilot-lite' ), $h1_count );
        }
        
        // Check for subheadings.
        $subheading_count = count( array_filter( $headings, fn( $h ) => $h['level'] > 1 ) );
        
        if ( $subheading_count < 2 ) {
            $status = 'warning';
            $issues[] = __( 'Consider adding more subheadings (H2-H6) to improve readability.', 'seo-autopilot-lite' );
        }
        
        // Check focus keyword in headings.
        $focus_keyword = get_post_meta( $post->ID, '_seo_autopilot_focus_keyword', true );
        if ( ! empty( $focus_keyword ) ) {
            $keyword_in_headings = false;
            foreach ( $headings as $heading ) {
                if ( false !== stripos( $heading['text'], $focus_keyword ) ) {
                    $keyword_in_headings = true;
                    break;
                }
            }
            
            if ( ! $keyword_in_headings ) {
                $status = 'warning';
                $issues[] = sprintf( __( 'Focus keyword "%s" not found in headings.', 'seo-autopilot-lite' ), $focus_keyword );
            }
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Headings', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Found %d headings with proper structure.', 'seo-autopilot-lite' ), count( $headings ) ),
                'recommendation' => '',
                'weight' => 10,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Headings', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Use a clear heading hierarchy with one H1 and multiple H2-H6 subheadings.', 'seo-autopilot-lite' ),
            'weight' => 10,
        ];
    }
}
