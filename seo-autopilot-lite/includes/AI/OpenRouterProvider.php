<?php
/**
 * OpenRouter Provider
 *
 * AI provider implementation for OpenRouter API (unified access to multiple models).
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

use SEO_Autopilot_Lite\Core\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Class OpenRouterProvider
 */
class OpenRouterProvider implements AiProviderInterface {

	/**
	 * API endpoint.
	 *
	 * @var string
	 */
	const API_ENDPOINT = 'https://openrouter.ai/api/v1/chat/completions';

	/**
	 * Default model.
	 *
	 * @var string
	 */
	const DEFAULT_MODEL = 'openai/gpt-4o-mini';

	/**
	 * Generate response from OpenRouter.
	 *
	 * @param string $prompt  Prompt text.
	 * @param array  $options Generation options.
	 * @return AiResponse|WP_Error
	 */
	public function generate( string $prompt, array $options = array() ) {
		$settings = Settings::get_instance()->get_settings();
		$api_key  = ! empty( $settings['openrouter_api_key'] ) ? Encryption::decrypt( $settings['openrouter_api_key'] ) : '';

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'OpenRouter API key is not configured.', 'seo-autopilot-lite' ) );
		}

		$model       = ! empty( $settings['openrouter_model'] ) ? $settings['openrouter_model'] : self::DEFAULT_MODEL;
		$max_tokens  = ! empty( $settings['openrouter_max_tokens'] ) ? (int) $settings['openrouter_max_tokens'] : 500;
		$temperature = ! empty( $settings['openrouter_temperature'] ) ? (float) $settings['openrouter_temperature'] : 0.7;

		$system_prompt = PromptBuilder::get_system_prompt();

		$request_body = array(
			'model'       => $model,
			'messages'    => array(
				array(
					'role'    => 'system',
					'content' => $system_prompt,
				),
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			'max_tokens'  => $max_tokens,
			'temperature' => $temperature,
		);

		$response = wp_remote_post( self::API_ENDPOINT, array(
			'timeout' => 30,
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type'  => 'application/json',
				'HTTP-Referer'  => home_url(),
				'X-Title'       => 'SEO Autopilot Lite',
			),
			'body'    => wp_json_encode( $request_body ),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code ) {
			$error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : __( 'Unknown error from OpenRouter API.', 'seo-autopilot-lite' );
			return new WP_Error( 'api_error', $error_message );
		}

		if ( empty( $body['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from OpenRouter API.', 'seo-autopilot-lite' ) );
		}

		$content = $body['choices'][0]['message']['content'];
		$usage   = isset( $body['usage'] ) ? $body['usage'] : array();

		return new AiResponse( array(
			'content'         => $content,
			'model'           => $model,
			'provider'        => 'openrouter',
			'tokens_used'     => isset( $usage['total_tokens'] ) ? $usage['total_tokens'] : 0,
			'prompt_tokens'   => isset( $usage['prompt_tokens'] ) ? $usage['prompt_tokens'] : 0,
			'completion_tokens' => isset( $usage['completion_tokens'] ) ? $usage['completion_tokens'] : 0,
			'raw_response'    => $body,
		) );
	}

	/**
	 * Test connection to OpenRouter API.
	 *
	 * @return bool|WP_Error
	 */
	public function test_connection() {
		$result = $this->generate( "Say 'Hello, connection successful!' in one short sentence.", array(
			'max_tokens' => 20,
		) );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Get provider name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'OpenRouter';
	}

	/**
	 * Get available models.
	 *
	 * @return array
	 */
	public function get_available_models(): array {
		return array(
			'openai/gpt-4o-mini'          => 'GPT-4o Mini',
			'openai/gpt-4o'               => 'GPT-4o',
			'openai/gpt-4-turbo'          => 'GPT-4 Turbo',
			'anthropic/claude-3-haiku'    => 'Claude 3 Haiku',
			'anthropic/claude-3-sonnet'   => 'Claude 3 Sonnet',
			'anthropic/claude-3-opus'     => 'Claude 3 Opus',
			'google/gemini-pro-1.5'       => 'Gemini Pro 1.5',
			'meta-llama/llama-3-70b-instruct' => 'Llama 3 70B',
			'mistralai/mistral-large'     => 'Mistral Large',
		);
	}
}
