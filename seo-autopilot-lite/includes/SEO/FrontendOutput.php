<?php
/**
 * Frontend SEO Output - outputs meta tags on frontend.
 */

namespace SeoAutopilotLite\SEO;

use SeoAutopilotLite\Core\Settings;

class FrontendOutput {
    
    public function __construct() {
        // Only add hooks if plugin is enabled and frontend output is enabled.
        if ( ! Settings::is_enabled() || ! Settings::is_frontend_output_enabled() ) {
            return;
        }
        
        // Skip if competing SEO plugin is active and setting says to disable.
        if ( Settings::has_competing_seo_plugin() ) {
            return;
        }
        
        add_action( 'wp_head', [ $this, 'output_meta_tags' ], 1 );
        add_filter( 'pre_get_document_title', [ $this, 'filter_title' ], 99 );
        add_filter( 'wpseo_title', [ $this, 'filter_yoast_title' ], 99 );
        add_filter( 'wpseo_metadesc', [ $this, 'filter_yoast_description' ], 99 );
    }
    
    /**
     * Output all meta tags in head.
     */
    public function output_meta_tags(): void {
        global $post;
        
        if ( ! $post instanceof \WP_Post ) {
            return;
        }
        
        // Only output on singular pages.
        if ( ! is_singular() ) {
            return;
        }
        
        $post_id = $post->ID;
        
        // Get SEO data.
        $seo_title = $this->get_seo_title( $post );
        $meta_description = $this->get_meta_description( $post );
        $canonical = $this->get_canonical_url( $post );
        $robots = $this->get_robots_value( $post );
        
        // Output title (if not already filtered).
        // Title is handled by filter_title method.
        
        // Output meta description.
        if ( ! empty( $meta_description ) && Settings::get_option( 'frontend_output', true ) ) {
            echo "\n" . '<meta name="description" content="' . esc_attr( $meta_description ) . '" />' . "\n";
        }
        
        // Output canonical URL.
        if ( ! empty( $canonical ) && Settings::get_option( 'canonical_enabled', true ) ) {
            echo "\n" . '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
        }
        
        // Output robots meta.
        if ( 'index,follow' !== $robots ) {
            echo "\n" . '<meta name="robots" content="' . esc_attr( $robots ) . '" />' . "\n";
        }
        
        // Output Open Graph tags.
        if ( Settings::get_option( 'open_graph_enabled', true ) ) {
            $this->output_open_graph( $post );
        }
        
        // Output Twitter Card tags.
        if ( Settings::get_option( 'twitter_card_enabled', true ) ) {
            $this->output_twitter_card( $post );
        }
        
        // Output schema JSON-LD.
        if ( Settings::get_option( 'schema_enabled', true ) ) {
            $this->output_schema( $post );
        }
    }
    
    /**
     * Filter document title.
     */
    public function filter_title( string $title ): string {
        global $post;
        
        if ( ! $post instanceof \WP_Post ) {
            return $title;
        }
        
        $seo_title = $this->get_seo_title( $post );
        
        if ( ! empty( $seo_title ) ) {
            return $seo_title;
        }
        
        return $title;
    }
    
    /**
     * Filter Yoast title (for compatibility).
     */
    public function filter_yoast_title( string $title ): string {
        global $post;
        
        if ( ! $post instanceof \WP_Post ) {
            return $title;
        }
        
        $seo_title = $this->get_seo_title( $post );
        
        if ( ! empty( $seo_title ) ) {
            return $seo_title;
        }
        
        return $title;
    }
    
    /**
     * Filter Yoast meta description (for compatibility).
     */
    public function filter_yoast_description( string $description ): string {
        global $post;
        
        if ( ! $post instanceof \WP_Post ) {
            return $description;
        }
        
        $meta_description = $this->get_meta_description( $post );
        
        if ( ! empty( $meta_description ) ) {
            return $meta_description;
        }
        
        return $description;
    }
    
    /**
     * Get SEO title.
     */
    private function get_seo_title( \WP_Post $post ): string {
        $seo_title = get_post_meta( $post->ID, '_seo_autopilot_title', true );
        
        if ( ! empty( $seo_title ) ) {
            return $seo_title;
        }
        
        // Use template.
        $template = Settings::get_option( 'title_template', '%%title%% %%sep%% %%sitename%%' );
        $separator = Settings::get_option( 'title_separator', '-' );
        $site_name = Settings::get_option( 'site_name', get_bloginfo( 'name' ) );
        
        $replacements = [
            '%%title%%' => $post->post_title,
            '%%sep%%' => $separator,
            '%%sitename%%' => $site_name,
        ];
        
        return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
    }
    
    /**
     * Get meta description.
     */
    private function get_meta_description( \WP_Post $post ): string {
        $meta_description = get_post_meta( $post->ID, '_seo_autopilot_description', true );
        
        if ( ! empty( $meta_description ) ) {
            return $meta_description;
        }
        
        // Use excerpt or content as fallback.
        if ( has_excerpt( $post ) ) {
            return get_the_excerpt( $post );
        }
        
        return wp_trim_words( $post->post_content, 30 );
    }
    
    /**
     * Get canonical URL.
     */
    private function get_canonical_url( \WP_Post $post ): string {
        $canonical = get_post_meta( $post->ID, '_seo_autopilot_canonical', true );
        
        if ( ! empty( $canonical ) ) {
            return $canonical;
        }
        
        return get_permalink( $post->ID );
    }
    
    /**
     * Get robots value.
     */
    private function get_robots_value( \WP_Post $post ): string {
        $noindex = get_post_meta( $post->ID, '_seo_autopilot_noindex', true );
        $nofollow = get_post_meta( $post->ID, '_seo_autopilot_nofollow', true );
        
        $index = empty( $noindex ) ? 'index' : 'noindex';
        $follow = empty( $nofollow ) ? 'follow' : 'nofollow';
        
        return $index . ',' . $follow;
    }
    
    /**
     * Output Open Graph tags.
     */
    private function output_open_graph( \WP_Post $post ): void {
        $og_title = get_post_meta( $post->ID, '_seo_autopilot_og_title', true );
        $og_description = get_post_meta( $post->ID, '_seo_autopilot_og_description', true );
        $og_image = get_post_meta( $post->ID, '_seo_autopilot_og_image', true );
        
        // Fallbacks.
        if ( empty( $og_title ) ) {
            $og_title = $this->get_seo_title( $post );
        }
        
        if ( empty( $og_description ) ) {
            $og_description = $this->get_meta_description( $post );
        }
        
        if ( empty( $og_image ) ) {
            $og_image = $this->get_featured_image_url( $post );
        }
        
        echo "\n" . '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( get_permalink( $post->ID ) ) . '" />' . "\n";
        
        if ( ! empty( $og_image ) ) {
            echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
        }
    }
    
    /**
     * Output Twitter Card tags.
     */
    private function output_twitter_card( \WP_Post $post ): void {
        $twitter_title = get_post_meta( $post->ID, '_seo_autopilot_twitter_title', true );
        $twitter_description = get_post_meta( $post->ID, '_seo_autopilot_twitter_description', true );
        $twitter_image = get_post_meta( $post->ID, '_seo_autopilot_twitter_image', true );
        
        // Fallbacks.
        if ( empty( $twitter_title ) ) {
            $twitter_title = $this->get_seo_title( $post );
        }
        
        if ( empty( $twitter_description ) ) {
            $twitter_description = $this->get_meta_description( $post );
        }
        
        if ( empty( $twitter_image ) ) {
            $twitter_image = $this->get_featured_image_url( $post );
        }
        
        echo "\n" . '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $twitter_title ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $twitter_description ) . '" />' . "\n";
        
        if ( ! empty( $twitter_image ) ) {
            echo '<meta name="twitter:image" content="' . esc_url( $twitter_image ) . '" />' . "\n";
        }
    }
    
    /**
     * Output schema JSON-LD.
     */
    private function output_schema( \WP_Post $post ): void {
        $schema_manager = new Schema\SchemaManager();
        $schema = $schema_manager->generate_schema( $post );
        
        if ( ! empty( $schema ) ) {
            echo "\n" . '<script type="application/ld+json">' . "\n";
            echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
            echo "\n" . '</script>' . "\n";
        }
    }
    
    /**
     * Get featured image URL.
     */
    private function get_featured_image_url( \WP_Post $post ): string {
        $featured_image_id = get_post_thumbnail_id( $post->ID );
        
        if ( $featured_image_id ) {
            $image_url = wp_get_attachment_url( $featured_image_id );
            if ( $image_url ) {
                return $image_url;
            }
        }
        
        return '';
    }
}
