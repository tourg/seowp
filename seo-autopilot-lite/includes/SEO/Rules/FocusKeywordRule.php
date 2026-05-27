<?php
/**
 * Focus Keyword Rule - checks focus keyword usage.
 */

namespace SeoAutopilotLite\SEO\Rules;

class FocusKeywordRule {
    
    /**
     * Analyze focus keyword usage.
     */
    public function analyze( \WP_Post $post ): ?array {
        $focus_keyword = get_post_meta( $post->ID, '_seo_autopilot_focus_keyword', true );
        
        // Check if missing.
        if ( empty( $focus_keyword ) ) {
            return [
                'status' => 'warning',
                'title' => __( 'Focus Keyword', 'seo-autopilot-lite' ),
                'message' => __( 'No focus keyword set.', 'seo-autopilot-lite' ),
                'recommendation' => __( 'Set a focus keyword to optimize your content.', 'seo-autopilot-lite' ),
                'weight' => 10,
            ];
        }
        
        $issues = [];
        $status = 'good';
        $content = $post->post_content . ' ' . $post->post_title;
        $keyword_count = substr_count( strtolower( $content ), strtolower( $focus_keyword ) );
        
        // Check keyword density.
        $word_count = str_word_count( strip_tags( $content ) );
        $density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
        
        if ( 0 === $keyword_count ) {
            $status = 'bad';
            $issues[] = sprintf( __( 'Focus keyword "%s" not found in content.', 'seo-autopilot-lite' ), $focus_keyword );
        } elseif ( $keyword_count < 2 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Focus keyword appears only %d time(s). Consider using it more.', 'seo-autopilot-lite' ), $keyword_count );
        } elseif ( $density > 3 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Keyword density is too high (%.1f%%). Avoid keyword stuffing.', 'seo-autopilot-lite' ), $density );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Focus Keyword', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Focus keyword "%s" appears %d times.', 'seo-autopilot-lite' ), $focus_keyword, $keyword_count ),
                'recommendation' => '',
                'weight' => 10,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Focus Keyword', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Use your focus keyword naturally throughout the content.', 'seo-autopilot-lite' ),
            'weight' => 10,
        ];
    }
}
