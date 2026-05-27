<?php
/**
 * WebPage Schema generator.
 */

namespace SeoAutopilotLite\Schema;

class WebPageSchema {
    
    /**
     * Generate WebPage schema.
     */
    public function generate( \WP_Post $post ): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'url' => get_permalink( $post ),
            'name' => $post->post_title,
            'description' => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 50 ),
            'isPartOf' => [
                '@type' => 'WebSite',
                'url' => home_url(),
                'name' => get_bloginfo( 'name' ),
            ],
        ];
    }
}
