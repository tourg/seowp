<?php
/**
 * FAQ Schema generator.
 */

namespace SeoAutopilotLite\Schema;

class FaqSchema {
    
    /**
     * Generate FAQPage schema.
     */
    public function generate( \WP_Post $post ): array {
        // Try to get FAQ from post meta first.
        $faq_meta = get_post_meta( $post->ID, '_seo_autopilot_faq', true );
        
        if ( ! empty( $faq_meta ) && is_array( $faq_meta ) ) {
            return $this->build_faq_schema( $faq_meta );
        }
        
        // Try to extract FAQ from content (look for FAQ pattern).
        $faqs = $this->extract_faqs_from_content( $post->post_content );
        
        if ( ! empty( $faqs ) ) {
            return $this->build_faq_schema( $faqs );
        }
        
        return [];
    }
    
    /**
     * Build FAQ schema from FAQ data.
     */
    private function build_faq_schema( array $faqs ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];
        
        foreach ( $faqs as $faq ) {
            if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
                continue;
            }
            
            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }
        
        return $schema;
    }
    
    /**
     * Extract FAQs from content (basic pattern matching).
     */
    private function extract_faqs_from_content( string $content ): array {
        $faqs = [];
        
        // Look for FAQ blocks or patterns.
        // This is a simple implementation - in production, parse Gutenberg blocks.
        preg_match_all( '/<h[2-4][^>]*>(.*?)<\/h[2-4]>[\s\n]*<p>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $heading = wp_strip_all_tags( $match[1] );
            $paragraph = wp_strip_all_tags( $match[2] );
            
            // Check if heading looks like a question.
            if ( false !== strpos( $heading, '?' ) || 
                 false !== stripos( $heading, 'how' ) ||
                 false !== stripos( $heading, 'what' ) ||
                 false !== stripos( $heading, 'when' ) ||
                 false !== stripos( $heading, 'where' ) ||
                 false !== stripos( $heading, 'why' ) ) {
                $faqs[] = [
                    'question' => $heading,
                    'answer' => $paragraph,
                ];
            }
        }
        
        return $faqs;
    }
}
