<?php
/**
 * Article Schema generator.
 */

namespace SeoAutopilotLite\Schema;

class ArticleSchema {
    
    /**
     * Generate Article or BlogPosting schema.
     */
    public function generate( \WP_Post $post, string $type = 'Article' ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'headline' => $post->post_title,
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified' => get_the_modified_date( 'c', $post ),
            'url' => get_permalink( $post ),
        ];
        
        // Add author.
        $author_id = $post->post_author;
        $author_name = get_the_author_meta( 'display_name', $author_id );
        if ( ! empty( $author_name ) ) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $author_name,
            ];
        }
        
        // Add publisher (site info).
        $site_name = get_bloginfo( 'name' );
        if ( ! empty( $site_name ) ) {
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name' => $site_name,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo(),
                ],
            ];
        }
        
        // Add featured image.
        $featured_image_id = get_post_thumbnail_id( $post->ID );
        if ( $featured_image_id ) {
            $image_url = wp_get_attachment_url( $featured_image_id );
            if ( $image_url ) {
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $image_url,
                ];
            }
        }
        
        // Add description.
        $description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 50 );
        if ( ! empty( $description ) ) {
            $schema['description'] = $description;
        }
        
        return $schema;
    }
    
    /**
     * Get site logo URL.
     */
    private function get_site_logo(): string {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_url = wp_get_attachment_url( $custom_logo_id );
            if ( $logo_url ) {
                return $logo_url;
            }
        }
        
        return get_template_directory_uri() . '/screenshot.png';
    }
}
