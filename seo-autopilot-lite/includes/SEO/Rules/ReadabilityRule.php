<?php
/**
 * Readability Rule - checks basic readability.
 */

namespace SeoAutopilotLite\SEO\Rules;

class ReadabilityRule {
    
    /**
     * Analyze readability.
     */
    public function analyze( \WP_Post $post ): ?array {
        $content = wp_strip_all_tags( $post->post_content );
        
        // Split into paragraphs.
        $paragraphs = array_filter( preg_split( '/\n\s*\n/', $content ) );
        $paragraph_count = count( $paragraphs );
        
        // Split into sentences.
        $sentences = preg_split( '/[.!?]+/', $content );
        $sentences = array_filter( $sentences, fn( $s ) => strlen( trim( $s ) ) > 0 );
        $sentence_count = count( $sentences );
        
        // Count words.
        $words = str_word_count( $content );
        
        // Calculate average sentence length.
        $avg_sentence_length = $sentence_count > 0 ? $words / $sentence_count : 0;
        
        // Calculate average paragraph length.
        $avg_paragraph_length = $paragraph_count > 0 ? $words / $paragraph_count : 0;
        
        $issues = [];
        $status = 'good';
        
        // Check paragraph count.
        if ( $paragraph_count < 3 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Content has only %d paragraph(s). Add more paragraphs for better readability.', 'seo-autopilot-lite' ), $paragraph_count );
        }
        
        // Check average sentence length.
        if ( $avg_sentence_length > 25 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Average sentence length is %.1f words. Consider shorter sentences.', 'seo-autopilot-lite' ), $avg_sentence_length );
        }
        
        // Check average paragraph length.
        if ( $avg_paragraph_length > 150 ) {
            $status = 'warning';
            $issues[] = sprintf( __( 'Paragraphs are too long (%.0f words average). Break them up.', 'seo-autopilot-lite' ), $avg_paragraph_length );
        }
        
        // Check for subheadings.
        preg_match_all( '/<h[2-6][^>]*>/i', $post->post_content, $heading_matches );
        $subheading_count = count( $heading_matches[0] ?? [] );
        
        if ( $words > 500 && $subheading_count < 2 ) {
            $status = 'warning';
            $issues[] = __( 'Long content without enough subheadings. Add H2/H3 headings.', 'seo-autopilot-lite' );
        }
        
        if ( empty( $issues ) ) {
            return [
                'status' => 'good',
                'title' => __( 'Readability', 'seo-autopilot-lite' ),
                'message' => sprintf( __( 'Good! Content has %d paragraphs with readable structure.', 'seo-autopilot-lite' ), $paragraph_count ),
                'recommendation' => '',
                'weight' => 10,
            ];
        }
        
        return [
            'status' => $status,
            'title' => __( 'Readability', 'seo-autopilot-lite' ),
            'message' => implode( ' ', $issues ),
            'recommendation' => __( 'Improve readability by using shorter sentences, paragraphs, and adding subheadings.', 'seo-autopilot-lite' ),
            'weight' => 10,
        ];
    }
}
