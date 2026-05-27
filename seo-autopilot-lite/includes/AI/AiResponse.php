<?php
/**
 * AI Response Object
 *
 * Represents a response from an AI provider.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Class AiResponse
 */
class AiResponse {

	/**
	 * Response content.
	 *
	 * @var string
	 */
	public $content;

	/**
	 * Model used.
	 *
	 * @var string
	 */
	public $model;

	/**
	 * Provider name.
	 *
	 * @var string
	 */
	public $provider;

	/**
	 * Tokens used.
	 *
	 * @var int
	 */
	public $tokens_used;

	/**
	 * Prompt tokens.
	 *
	 * @var int
	 */
	public $prompt_tokens;

	/**
	 * Completion tokens.
	 *
	 * @var int
	 */
	public $completion_tokens;

	/**
	 * Raw response data.
	 *
	 * @var array
	 */
	public $raw_response;

	/**
	 * Constructor.
	 *
	 * @param array $data Response data.
	 */
	public function __construct( array $data ) {
		$this->content           = $data['content'] ?? '';
		$this->model             = $data['model'] ?? '';
		$this->provider          = $data['provider'] ?? '';
		$this->tokens_used       = $data['tokens_used'] ?? 0;
		$this->prompt_tokens     = $data['prompt_tokens'] ?? 0;
		$this->completion_tokens = $data['completion_tokens'] ?? 0;
		$this->raw_response      = $data['raw_response'] ?? array();
	}

	/**
	 * Get content as string.
	 *
	 * @return string
	 */
	public function get_content(): string {
		return trim( $this->content );
	}

	/**
	 * Get content as JSON array.
	 *
	 * @return array|null
	 */
	public function get_json() {
		$content = trim( $this->content );
		
		// Try to extract JSON from content if it contains extra text.
		if ( strpos( $content, '{' ) !== false || strpos( $content, '[' ) !== false ) {
			preg_match( '/(\{.*\}|\[.*\])/s', $content, $matches );
			if ( ! empty( $matches[1] ) ) {
				$content = $matches[1];
			}
		}

		$decoded = json_decode( $content, true );
		return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
	}

	/**
	 * Check if response is valid.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		return ! empty( $this->content );
	}

	/**
	 * Get token usage summary.
	 *
	 * @return string
	 */
	public function get_token_summary(): string {
		if ( $this->tokens_used === 0 ) {
			return '';
		}

		return sprintf(
			/* translators: %d: total tokens, %d: prompt tokens, %d: completion tokens */
			__( '%d tokens (%d prompt + %d completion)', 'seo-autopilot-lite' ),
			$this->tokens_used,
			$this->prompt_tokens,
			$this->completion_tokens
		);
	}

	/**
	 * Convert to array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		return array(
			'content'           => $this->content,
			'model'             => $this->model,
			'provider'          => $this->provider,
			'tokens_used'       => $this->tokens_used,
			'prompt_tokens'     => $this->prompt_tokens,
			'completion_tokens' => $this->completion_tokens,
			'is_valid'          => $this->is_valid(),
		);
	}
}
