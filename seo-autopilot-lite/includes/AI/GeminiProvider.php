<?php
/**
 * Gemini Provider (Google)
 *
 * AI provider implementation for Google Gemini API.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

use SEO_Autopilot_Lite\Core\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Class GeminiProvider
 */
class GeminiProvider implements AiProviderInterface {

	/**
	 * API endpoint base.
	 *
	 * @var string
	 */
	const API_ENDPOINT_BASE = 'https://generativelanguage.googleapis.com/v1beta/models';

	/**
	 * Default model.
	 *
	 * @var string
	 */
	const DEFAULT_MODEL = 'gemini-1.5-flash';

	/**
	 * Generate response from Gemini.
	 *
	 * @param string $prompt  Prompt text.
	 * @param array  $options Generation options.
	 * @return AiResponse|WP_Error
	 */
	public function generate( string $prompt, array $options = array() ) {
		$settings = Settings::get_instance()->get_settings();
		$api_key  = ! empty( $settings['gemini_api_key'] ) ? Encryption::decrypt( $settings['gemini_api_key'] ) : '';

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Gemini API key is not configured.', 'seo-autopilot-lite' ) );
		}

		$model      = ! empty( $settings['gemini_model'] ) ? $settings['gemini_model'] : self::DEFAULT_MODEL;
		$endpoint   = self::API_ENDPOINT_BASE . '/' . $model . ':generateContent?key=' . $api_key;

		$request_body = array(
			'contents' => array(
				array(
					'parts' => array(
						array(
							'text' => PromptBuilder::get_system_prompt() . "\n\n" . $prompt,
						),
					),
				),
			),
			'generationConfig' => array(
				'maxOutputTokens' => ! empty( $settings['gemini_max_tokens'] ) ? (int) $settings['gemini_max_tokens'] : 500,
				'temperature'     => ! empty( $settings['gemini_temperature'] ) ? (float) $settings['gemini_temperature'] : 0.7,
			),
		);

		$response = wp_remote_post( $endpoint, array(
			'timeout' => 30,
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( $request_body ),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code ) {
			$error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : __( 'Unknown error from Gemini API.', 'seo-autopilot-lite' );
			return new WP_Error( 'api_error', $error_message );
		}

		if ( empty( $body['candidates'][0]['content']['parts'][0]['text'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Gemini API.', 'seo-autopilot-lite' ) );
		}

		$content = $body['candidates'][0]['content']['parts'][0]['text'];

		return new AiResponse( array(
			'content'      => $content,
			'model'        => $model,
			'provider'     => 'gemini',
			'tokens_used'  => 0, // Gemini doesn't return token count in this API version
			'raw_response' => $body,
		) );
	}

	/**
	 * Test connection to Gemini API.
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
		return 'Google Gemini';
	}

	/**
	 * Get available models.
	 *
	 * @return array
	 */
	public function get_available_models(): array {
		return array(
			'gemini-1.5-flash'   => 'Gemini 1.5 Flash (Fast)',
			'gemini-1.5-pro'     => 'Gemini 1.5 Pro (Advanced)',
			'gemini-1.0-pro'     => 'Gemini 1.0 Pro',
		);
	}
}
