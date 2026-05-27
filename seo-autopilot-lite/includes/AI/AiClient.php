<?php
/**
 * AI Client - manages AI provider connections.
 */

namespace SeoAutopilotLite\AI;

use SeoAutopilotLite\Core\Settings;

class AiClient {
    
    private ?AiProviderInterface $provider = null;
    
    public function __construct() {
        $this->initialize_provider();
    }
    
    /**
     * Initialize the AI provider based on settings.
     */
    private function initialize_provider(): void {
        if ( ! Settings::is_ai_enabled() ) {
            return;
        }
        
        $provider_name = Settings::get_ai_provider();
        $api_key = Settings::get_ai_api_key();
        
        if ( empty( $api_key ) ) {
            return;
        }
        
        switch ( $provider_name ) {
            case 'openai':
                $this->provider = new OpenAiProvider( $api_key );
                break;
            
            case 'claude':
                $this->provider = new ClaudeProvider( $api_key );
                break;
            
            case 'gemini':
                $this->provider = new GeminiProvider( $api_key );
                break;
            
            case 'openrouter':
                $this->provider = new OpenRouterProvider( $api_key );
                break;
        }
    }
    
    /**
     * Generate content using AI.
     */
    public function generate( string $prompt, array $options = [] ): ?string {
        if ( ! $this->provider instanceof AiProviderInterface ) {
            return null;
        }
        
        return $this->provider->generate( $prompt, $options );
    }
    
    /**
     * Test API connection.
     */
    public function test_connection(): bool {
        if ( ! $this->provider instanceof AiProviderInterface ) {
            return false;
        }
        
        return $this->provider->test_connection();
    }
    
    /**
     * Check if AI is available.
     */
    public function is_available(): bool {
        return $this->provider instanceof AiProviderInterface;
    }
}
