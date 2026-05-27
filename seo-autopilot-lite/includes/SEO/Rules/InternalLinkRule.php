<?php
/**
 * Internal Link Rule - checks internal linking.
 */

namespace SeoAutopilotLite\SEO\Rules;

class InternalLinkRule {
    
    /**
     * Analyze internal links.
     */
    public function analyze( \WP_Post $post ): ?array {
        $content = $post->post_content;
        
        // Extract links.
        preg_match_all( '/<a[^>]*href=["\'](.*?)["\'][^>]*>/i', $content, $matches );
        
        $links = $matches[1] ?? [];
        $internal_links = 0;
        $external_links = 0;
        
        $site_url = home_url();
        
        foreach ( $links as $link ) {
            if ( false !== strpos( $link, $site_url ) || '/' === substr( $link, 0, 1 ) ) {
                $internal_links++;
            } else {
                $external_links++;
            }
        }
        
        $issues = [];
        $status = 'good';
        
        if ( 0 === $internal_links ) {
            $status = 'warning';
            $issues[] = __( 'No internal links found.', 'seo-autopilot-lite' );
        } elseif ( $internal_links < 2 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Only %d internal link found. Consider adding more.', 'seo-autopilot-lite' ), $internal_links );
        }
        
        if ( 0 === $external_links && 0 === $internal_links ) {
            $status = 'warning';
            $issues[] = __( 'No links found in content.', 'seo-autopilot-lite' );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Internal Links', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Found %d internal link(s) and %d external link(s).', 'seo-autopilot-lite' ), $internal_links, $external_links ),
                'recommendation' => '',
                'weight' => 10,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Internal Links', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Add relevant internal links to improve site navigation and SEO.', 'seo-autopilot-lite' ),
            'weight' => 10,
        ];
    }
}
