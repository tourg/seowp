<?php
/**
 * Admin columns for posts list.
 */

namespace SeoAutopilotLite\Admin;

use SeoAutopilotLite\Core\Settings;

class Columns {
    
    public function __construct() {
        $post_types = Settings::get_supported_post_types();
        
        foreach ( $post_types as $post_type ) {
            add_filter( "manage_{$post_type}_posts_columns", [ $this, 'add_columns' ] );
            add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'render_columns' ], 10, 2 );
        }
    }
    
    /**
     * Add SEO columns.
     */
    public function add_columns( array $columns ): array {
        $new_columns = [];
        
        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;
            
            if ( 'title' === $key ) {
                $new_columns['seo_score'] = __( 'SEO Score', 'seo-autopilot-lite' );
                $new_columns['focus_keyword'] = __( 'Focus Keyword', 'seo-autopilot-lite' );
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Render column content.
     */
    public function render_columns( string $column, int $post_id ): void {
        if ( 'seo_score' === $column ) {
            $score = get_post_meta( $post_id, '_seo_autopilot_seo_score', true );
            
            if ( '' === $score ) {
                echo '<span style="color:#999;">' . esc_html__( 'Not analyzed', 'seo-autopilot-lite' ) . '</span>';
            } else {
                $class = 'good';
                if ( $score < 80 ) $class = 'ok';
                if ( $score < 60 ) $class = 'warning';
                if ( $score < 40 ) $class = 'bad';
                
                printf(
                    '<span class="seo-score-badge seo-score-%s">%d</span>',
                    esc_attr( $class ),
                    intval( $score )
                );
            }
        }
        
        if ( 'focus_keyword' === $column ) {
            $keyword = get_post_meta( $post_id, '_seo_autopilot_focus_keyword', true );
            
            if ( empty( $keyword ) ) {
                echo '<span style="color:#999;">' . esc_html__( 'Not set', 'seo-autopilot-lite' ) . '</span>';
            } else {
                echo esc_html( $keyword );
            }
        }
    }
}
