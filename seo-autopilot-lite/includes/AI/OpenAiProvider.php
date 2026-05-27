<?php
/**
 * OpenAI Provider
 *
 * AI provider implementation for OpenAI API.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

use SEO_Autopilot_Lite\Core\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Class OpenAiProvider
 */
class OpenAiProvider implements AiProviderInterface {

	/**
	 * API endpoint.
	 *
	 * @var string
	 */
	const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

	/**
	 * Default model.
	 *
	 * @var string
	 */
	const DEFAULT_MODEL = 'gpt-4o-mini';

	/**
	 * Generate response from OpenAI.
	 *
	 * @param string $prompt  Prompt text.
	 * @param array  $options Generation options.
	 * @return AiResponse|WP_Error
	 */
	public function generate( string $prompt, array $options = array() ) {
		$settings = Settings::get_instance()->get_settings();
		$api_key  = ! empty( $settings['openai_api_key'] ) ? Encryption::decrypt( $settings['openai_api_key'] ) : '';

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'OpenAI API key is not configured.', 'seo-autopilot-lite' ) );
		}

		$model       = ! empty( $settings['openai_model'] ) ? $settings['openai_model'] : self::DEFAULT_MODEL;
		$max_tokens  = ! empty( $settings['openai_max_tokens'] ) ? (int) $settings['openai_max_tokens'] : 500;
		$temperature = ! empty( $settings['openai_temperature'] ) ? (float) $settings['openai_temperature'] : 0.7;

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
			),
			'body'    => wp_json_encode( $request_body ),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code ) {
			$error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : __( 'Unknown error from OpenAI API.', 'seo-autopilot-lite' );
			return new WP_Error( 'api_error', $error_message );
		}

		if ( empty( $body['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from OpenAI API.', 'seo-autopilot-lite' ) );
		}

		$content = $body['choices'][0]['message']['content'];
		$usage   = isset( $body['usage'] ) ? $body['usage'] : array();

		return new AiResponse( array(
			'content'      => $content,
			'model'        => $model,
			'provider'     => 'openai',
			'tokens_used'  => isset( $usage['total_tokens'] ) ? $usage['total_tokens'] : 0,
			'prompt_tokens' => isset( $usage['prompt_tokens'] ) ? $usage['prompt_tokens'] : 0,
			'completion_tokens' => isset( $usage['completion_tokens'] ) ? $usage['completion_tokens'] : 0,
			'raw_response' => $body,
		) );
	}

	/**
	 * Test connection to OpenAI API.
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
		return 'OpenAI';
	}

	/**
	 * Get available models.
	 *
	 * @return array
	 */
	public function get_available_models(): array {
		return array(
			'gpt-4o-mini'     => 'GPT-4o Mini (Recommended)',
			'gpt-4o'          => 'GPT-4o',
			'gpt-4-turbo'     => 'GPT-4 Turbo',
			'gpt-3.5-turbo'   => 'GPT-3.5 Turbo',
		);
	}
}
