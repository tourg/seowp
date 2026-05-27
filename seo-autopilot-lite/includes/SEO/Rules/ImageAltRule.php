<?php
/**
 * Image Alt Rule - checks image alt text.
 */

namespace SeoAutopilotLite\SEO\Rules;

class ImageAltRule {
    
    /**
     * Analyze image alt text.
     */
    public function analyze( \WP_Post $post ): ?array {
        $content = $post->post_content;
        
        // Extract images.
        preg_match_all( '/<img[^>]*>/i', $content, $matches );
        
        $images = $matches[0] ?? [];
        $missing_alt = 0;
        $generic_alt = 0;
        
        $generic_patterns = [ 'image', 'photo', 'picture', 'img', 'untitled', 'dsc_', 'img_' ];
        
        foreach ( $images as $image ) {
            // Check for alt attribute.
            if ( ! preg_match( '/alt=["\'](.*?)["\']/i', $image, $alt_match ) ) {
                $missing_alt++;
                continue;
            }
            
            $alt_text = strtolower( $alt_match[1] ?? '' );
            
            // Check for generic alt text.
            if ( empty( $alt_text ) ) {
                $missing_alt++;
            } else {
                foreach ( $generic_patterns as $pattern ) {
                    if ( false !== strpos( $alt_text, $pattern ) && strlen( $alt_text ) < 20 ) {
                        $generic_alt++;
                        break;
                    }
                }
            }
        }
        
        // Check featured image.
        $featured_image_id = get_post_thumbnail_id( $post->ID );
        $featured_image_missing = false;
        
        if ( $featured_image_id ) {
            $featured_alt = get_post_meta( $featured_image_id, '_wp_attachment_image_alt', true );
            if ( empty( $featured_alt ) ) {
                $featured_image_missing = true;
            }
        }
        
        $issues = [];
        $status = 'good';
        
        if ( $missing_alt > 0 ) {
            $status = 'warning';
            $issues[] = sprintf( __( '%d image(s) missing alt text.', 'seo-autopilot-lite' ), $missing_alt );
        }
        
        if ( $generic_alt > 0 ) {
            $status = 'warning';
            $issues[] = sprintf( __( '%d image(s) have generic alt text.', 'seo-autopilot-lite' ), $generic_alt );
        }
        
        if ( $featured_image_missing ) {
            $status = 'warning';
            $issues[] = __( 'Featured image missing alt text.', 'seo-autopilot-lite' );
        }
        
        if ( 0 === count( $images ) && ! $featured_image_id ) {
            return [
                'status' => 'ok',
                'title' => __( 'Image Alt Text', 'seo-autopilot-lite' ),
                'message' => __( 'No images found in content.', 'seo-autopilot-lite' ),
                'recommendation' => '',
                'weight' => 5,
            ];
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Image Alt Text', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! All %d images have descriptive alt text.', 'seo-autopilot-lite' ), count( $images ) ),
                'recommendation' => '',
                'weight' => 10,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Image Alt Text', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Add descriptive alt text to all images for better accessibility and SEO.', 'seo-autopilot-lite' ),
            'weight' => 10,
        ];
    }
}
