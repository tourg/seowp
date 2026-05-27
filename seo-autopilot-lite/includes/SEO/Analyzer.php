<?php
/**
 * SEO Analyzer - analyzes posts for SEO quality.
 */

namespace SeoAutopilotLite\SEO;

use SeoAutopilotLite\Core\Settings;

class Analyzer {
    
    /**
     * Analyze a post and return analysis results.
     */
    public function analyze_post( \WP_Post $post ): array {
        $results = [];
        
        // Run all rules.
        $results[] = (new Rules\TitleRule())->analyze( $post );
        $results[] = (new Rules\MetaDescriptionRule())->analyze( $post );
        $results[] = (new Rules\FocusKeywordRule())->analyze( $post );
        $results[] = (new Rules\ContentLengthRule())->analyze( $post );
        $results[] = (new Rules\HeadingRule())->analyze( $post );
        $results[] = (new Rules\ImageAltRule())->analyze( $post );
        $results[] = (new Rules\InternalLinkRule())->analyze( $post );
        $results[] = (new Rules\ReadabilityRule())->analyze( $post );
        $results[] = (new Rules\CanonicalRule())->analyze( $post );
        
        // Filter out null results.
        return array_values( array_filter( $results ) );
    }
    
    /**
     * Calculate overall SEO score from analysis results.
     */
    public function calculate_score( array $analysis ): int {
        if ( empty( $analysis ) ) {
            return 0;
        }
        
        $total_points = 0;
        $earned_points = 0;
        
        $status_weights = [
            'good' => 10,
            'ok' => 7,
            'warning' => 4,
            'bad' => 0,
        ];
        
        foreach ( $analysis as $item ) {
            $weight = $item['weight'] ?? 10;
            $status = $item['status'] ?? 'bad';
            
            $total_points += $weight;
            $earned_points += ($status_weights[ $status ] ?? 0) * ($weight / 10);
        }
        
        if ( $total_points === 0 ) {
            return 0;
        }
        
        return round( ($earned_points / $total_points) * 100 );
    }
}
