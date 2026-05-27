<?php
/**
 * Breadcrumb Schema generator.
 */

namespace SeoAutopilotLite\Schema;

class BreadcrumbSchema {
    
    /**
     * Generate BreadcrumbList schema.
     */
    public function generate( \WP_Post $post ): array {
        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];
        
        // Home item.
        $breadcrumbs['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __( 'Home', 'seo-autopilot-lite' ),
            'item' => home_url(),
        ];
        
        // Get post ancestors.
        $ancestors = get_post_ancestors( $post );
        $ancestors = array_reverse( $ancestors );
        
        $position = 2;
        foreach ( $ancestors as $ancestor_id ) {
            $ancestor = get_post( $ancestor_id );
            if ( $ancestor ) {
                $breadcrumbs['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $ancestor->post_title,
                    'item' => get_permalink( $ancestor ),
                ];
                $position++;
            }
        }
        
        // Current page.
        $breadcrumbs['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $post->post_title,
            'item' => get_permalink( $post ),
        ];
        
        return $breadcrumbs;
    }
}
