<?php
/**
 * Schema Manager - generates and manages JSON-LD schema.
 */

namespace SeoAutopilotLite\Schema;

use SeoAutopilotLite\Core\Settings;

class SchemaManager {
    
    public function __construct() {
        // Schema output is handled by FrontendOutput class.
    }
    
    /**
     * Generate schema for a post.
     */
    public function generate_schema( \WP_Post $post ): array {
        $schema = [];
        
        // Get custom schema from post meta.
        $custom_schema = get_post_meta( $post->ID, '_seo_autopilot_schema_json', true );
        if ( ! empty( $custom_schema ) ) {
            $decoded = json_decode( $custom_schema, true );
            if ( is_array( $decoded ) ) {
                $schema[] = $decoded;
            }
        }
        
        // Get schema type from post meta.
        $schema_type = get_post_meta( $post->ID, '_seo_autopilot_schema_type', true );
        
        if ( ! empty( $schema_type ) ) {
            $generated = $this->generate_schema_by_type( $schema_type, $post );
            if ( ! empty( $generated ) ) {
                $schema[] = $generated;
            }
        } else {
            // Default to Article/WebPage schema for posts.
            if ( 'post' === $post->post_type ) {
                $generated = (new ArticleSchema())->generate( $post );
                if ( ! empty( $generated ) ) {
                    $schema[] = $generated;
                }
            }
        }
        
        // Add WebPage schema.
        $webpage_schema = (new WebPageSchema())->generate( $post );
        if ( ! empty( $webpage_schema ) ) {
            $schema[] = $webpage_schema;
        }
        
        // Add Breadcrumb schema.
        $breadcrumb_schema = (new BreadcrumbSchema())->generate( $post );
        if ( ! empty( $breadcrumb_schema ) ) {
            $schema[] = $breadcrumb_schema;
        }
        
        // Merge schemas.
        if ( count( $schema ) === 1 ) {
            return $schema[0];
        } elseif ( count( $schema ) > 1 ) {
            return [ '@graph' => $schema ];
        }
        
        return [];
    }
    
    /**
     * Generate schema by type.
     */
    private function generate_schema_by_type( string $type, \WP_Post $post ): array {
        switch ( $type ) {
            case 'Article':
            case 'BlogPosting':
                return (new ArticleSchema())->generate( $post, $type );
            
            case 'FAQPage':
                return (new FaqSchema())->generate( $post );
            
            case 'LocalBusiness':
                return (new LocalBusinessSchema())->generate( $post );
            
            case 'Tour':
            case 'TouristTrip':
            case 'TouristAttraction':
            case 'Hotel':
                if ( Settings::get_option( 'traveler_enabled', false ) ) {
                    return (new TravelerSchema())->generate( $post, $type );
                }
                break;
        }
        
        return [];
    }
}
