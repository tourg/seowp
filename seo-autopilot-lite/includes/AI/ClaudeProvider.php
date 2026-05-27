<?php
/**
 * Claude Provider (Anthropic)
 *
 * AI provider implementation for Anthropic Claude API.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

use SEO_Autopilot_Lite\Core\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Class ClaudeProvider
 */
class ClaudeProvider implements AiProviderInterface {

	/**
	 * API endpoint.
	 *
	 * @var string
	 */
	const API_ENDPOINT = 'https://api.anthropic.com/v1/messages';

	/**
	 * Default model.
	 *
	 * @var string
	 */
	const DEFAULT_MODEL = 'claude-3-haiku-20240307';

	/**
	 * API version header.
	 *
	 * @var string
	 */
	const API_VERSION = '2023-06-01';

	/**
	 * Generate response from Claude.
	 *
	 * @param string $prompt  Prompt text.
	 * @param array  $options Generation options.
	 * @return AiResponse|WP_Error
	 */
	public function generate( string $prompt, array $options = array() ) {
		$settings = Settings::get_instance()->get_settings();
		$api_key  = ! empty( $settings['claude_api_key'] ) ? Encryption::decrypt( $settings['claude_api_key'] ) : '';

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Claude API key is not configured.', 'seo-autopilot-lite' ) );
		}

		$model       = ! empty( $settings['claude_model'] ) ? $settings['claude_model'] : self::DEFAULT_MODEL;
		$max_tokens  = ! empty( $settings['claude_max_tokens'] ) ? (int) $settings['claude_max_tokens'] : 500;

		$request_body = array(
			'model'       => $model,
			'max_tokens'  => $max_tokens,
			'system'      => PromptBuilder::get_system_prompt(),
			'messages'    => array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
		);

		$response = wp_remote_post( self::API_ENDPOINT, array(
			'timeout' => 30,
			'headers' => array(
				'x-api-key'           => $api_key,
				'anthropic-version'   => self::API_VERSION,
				'Content-Type'        => 'application/json',
			),
			'body'    => wp_json_encode( $request_body ),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code ) {
			$error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : __( 'Unknown error from Claude API.', 'seo-autopilot-lite' );
			return new WP_Error( 'api_error', $error_message );
		}

		if ( empty( $body['content'][0]['text'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Claude API.', 'seo-autopilot-lite' ) );
		}

		$content = $body['content'][0]['text'];
		$usage   = isset( $body['usage'] ) ? $body['usage'] : array();

		return new AiResponse( array(
			'content'         => $content,
			'model'           => $model,
			'provider'        => 'claude',
			'tokens_used'     => ( isset( $usage['input_tokens'] ) ? $usage['input_tokens'] : 0 ) + ( isset( $usage['output_tokens'] ) ? $usage['output_tokens'] : 0 ),
			'prompt_tokens'   => isset( $usage['input_tokens'] ) ? $usage['input_tokens'] : 0,
			'completion_tokens' => isset( $usage['output_tokens'] ) ? $usage['output_tokens'] : 0,
			'raw_response'    => $body,
		) );
	}

	/**
	 * Test connection to Claude API.
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
		return 'Claude (Anthropic)';
	}

	/**
	 * Get available models.
	 *
	 * @return array
	 */
	public function get_available_models(): array {
		return array(
			'claude-3-haiku-20240307'  => 'Claude 3 Haiku (Fast)',
			'claude-3-sonnet-20240229' => 'Claude 3 Sonnet (Balanced)',
			'claude-3-opus-20240229'   => 'Claude 3 Opus (Most Capable)',
			'claude-3-5-sonnet-20240620' => 'Claude 3.5 Sonnet (Latest)',
		);
	}
}
