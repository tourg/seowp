<?php
/**
 * AI Provider Interface.
 */

namespace SeoAutopilotLite\AI;

interface AiProviderInterface {
    
    /**
     * Generate content using AI.
     */
    public function generate( string $prompt, array $options = [] ): ?string;
    
    /**
     * Test API connection.
     */
    public function test_connection(): bool;
}
