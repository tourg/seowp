<?php
/**
 * Content Length Rule - checks content length.
 */

namespace SeoAutopilotLite\SEO\Rules;

class ContentLengthRule {
    
    /**
     * Analyze content length.
     */
    public function analyze( \WP_Post $post ): ?array {
        $content = wp_strip_all_tags( $post->post_content );
        $word_count = str_word_count( $content );
        
        $min_words = 300;
        $recommended_words = 500;
        
        if ( 0 === $word_count ) {
            return [
                'status' => 'bad',
                'title' => __( 'Content Length', 'seo-autopilot-lite' ),
                'message' => __( 'No content found.', 'seo-autopilot-lite' ),
                'recommendation' => __( 'Add quality content to your post.', 'seo-autopilot-lite' ),
                'weight' => 10,
            ];
        }
        
        if ( $word_count < $min_words ) {
            return [
                'status' => 'warning',
                'title' => __( 'Content Length', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Content is too short (%d words). Minimum recommended: %d words.', 'seo-autopilot-lite' ), $word_count, $min_words ),
                'recommendation' => sprintf( __( 'Consider expanding your content to at least %d words for better SEO.', 'seo-autopilot-lite' ), $recommended_words ),
                'weight' => 10,
            ];
        }
        
        return [
            'status' => 'good',
            'title' => __( 'Content Length', 'seo-autopilot-lite' ),
            'message' => sprintf( __( 'Good! Content length is %d words.', 'seo-autopilot-lite' ), $word_count ),
            'recommendation' => '',
            'weight' => 10,
        ];
    }
}
