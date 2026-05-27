<?php
/**
 * AI Prompt Builder
 *
 * Builds prompts for AI SEO generation.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\AI;

use SEO_Autopilot_Lite\Core\Settings;
use SEO_Autopilot_Lite\Core\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class PromptBuilder
 */
class PromptBuilder {

	/**
	 * Build prompt for SEO title generation.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_title_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'focus_keyword'    => '',
			'site_name'        => get_bloginfo( 'name' ),
			'brand_name'       => '',
			'language'         => 'en',
			'country'          => 'US',
			'destination'      => '',
			'post_type'        => 'post',
			'current_title'    => '',
			'seo_issues'       => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert. Generate an optimized SEO title for the following content.\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "- Length: 30-60 characters\n";
		$prompt .= "- Include the focus keyword naturally\n";
		$prompt .= "- Make it compelling and click-worthy\n";
		$prompt .= "- Include brand name at the end if appropriate\n";
		$prompt .= "- Avoid clickbait\n";
		$prompt .= "- Match search intent\n\n";

		if ( ! empty( $data['focus_keyword'] ) ) {
			$prompt .= "Focus Keyword: " . $data['focus_keyword'] . "\n\n";
		}

		if ( ! empty( $data['destination'] ) ) {
			$prompt .= "Destination/Location: " . $data['destination'] . "\n\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";

		if ( ! empty( $data['current_title'] ) ) {
			$prompt .= "Current SEO Title: " . $data['current_title'] . "\n\n";
		}

		if ( ! empty( $data['seo_issues'] ) ) {
			$prompt .= "SEO Issues to Address:\n";
			foreach ( $data['seo_issues'] as $issue ) {
				$prompt .= "- " . $issue . "\n";
			}
			$prompt .= "\n";
		}

		$prompt .= "Content Preview:\n";
		$prompt .= self::truncate_content( $data['post_content'], 500 );

		$prompt .= "\n\nGenerate ONLY the SEO title, nothing else.";

		return apply_filters( 'seo_autopilot_lite_title_prompt', $prompt, $data );
	}

	/**
	 * Build prompt for meta description generation.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_description_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'post_excerpt'     => '',
			'focus_keyword'    => '',
			'site_name'        => get_bloginfo( 'name' ),
			'brand_name'       => '',
			'language'         => 'en',
			'country'          => 'US',
			'destination'      => '',
			'post_type'        => 'post',
			'current_description' => '',
			'seo_issues'       => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert. Generate an optimized meta description for the following content.\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "- Length: 120-160 characters\n";
		$prompt .= "- Include the focus keyword naturally\n";
		$prompt .= "- Write a compelling summary that encourages clicks\n";
		$prompt .= "- Include a call-to-action when appropriate\n";
		$prompt .= "- Accurately describe the content\n";
		$prompt .= "- Avoid duplicate phrases\n\n";

		if ( ! empty( $data['focus_keyword'] ) ) {
			$prompt .= "Focus Keyword: " . $data['focus_keyword'] . "\n\n";
		}

		if ( ! empty( $data['destination'] ) ) {
			$prompt .= "Destination/Location: " . $data['destination'] . "\n\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";

		if ( ! empty( $data['current_description'] ) ) {
			$prompt .= "Current Meta Description: " . $data['current_description'] . "\n\n";
		}

		if ( ! empty( $data['seo_issues'] ) ) {
			$prompt .= "SEO Issues to Address:\n";
			foreach ( $data['seo_issues'] as $issue ) {
				$prompt .= "- " . $issue . "\n";
			}
			$prompt .= "\n";
		}

		if ( ! empty( $data['post_excerpt'] ) ) {
			$prompt .= "Excerpt:\n" . $data['post_excerpt'] . "\n\n";
		}

		$prompt .= "Content Preview:\n";
		$prompt .= self::truncate_content( $data['post_content'], 500 );

		$prompt .= "\n\nGenerate ONLY the meta description, nothing else.";

		return apply_filters( 'seo_autopilot_lite_description_prompt', $prompt, $data );
	}

	/**
	 * Build prompt for FAQ generation.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_faq_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'focus_keyword'    => '',
			'post_type'        => 'post',
			'destination'      => '',
			'num_questions'    => 5,
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert. Generate FAQ questions and answers for the following content.\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "- Generate {$data['num_questions']} relevant questions\n";
		$prompt .= "- Questions should be commonly searched by users\n";
		$prompt .= "- Answers should be concise and helpful (2-4 sentences)\n";
		$prompt .= "- Include focus keyword naturally where appropriate\n";
		$prompt .= "- Format as JSON with 'question' and 'answer' fields\n\n";

		if ( ! empty( $data['focus_keyword'] ) ) {
			$prompt .= "Focus Keyword: " . $data['focus_keyword'] . "\n\n";
		}

		if ( ! empty( $data['destination'] ) ) {
			$prompt .= "Destination/Location: " . $data['destination'] . "\n\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";
		$prompt .= "Post Type: " . $data['post_type'] . "\n\n";

		$prompt .= "Content:\n";
		$prompt .= self::truncate_content( $data['post_content'], 1000 );

		$prompt .= "\n\nGenerate ONLY valid JSON in this format:\n";
		$prompt .= "[\n";
		$prompt .= "  {\"question\": \"Question 1?\", \"answer\": \"Answer 1.\"},\n";
		$prompt .= "  {\"question\": \"Question 2?\", \"answer\": \"Answer 2.\"}\n";
		$prompt .= "]";

		return apply_filters( 'seo_autopilot_lite_faq_prompt', $prompt, $data );
	}

	/**
	 * Build prompt for schema generation.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_schema_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'post_type'        => 'post',
			'schema_type'      => 'Article',
			'site_name'        => get_bloginfo( 'name' ),
			'site_url'         => home_url(),
			'author_name'      => '',
			'publish_date'     => '',
			'destination'      => '',
			'price'            => '',
			'rating'           => '',
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert. Generate JSON-LD schema markup for the following content.\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "- Generate valid JSON-LD schema.org markup\n";
		$prompt .= "- Use schema type: {$data['schema_type']}\n";
		$prompt .= "- Include all required properties\n";
		$prompt .= "- Do NOT invent fake prices, ratings, or availability\n";
		$prompt .= "- Use only information provided or clearly inferable\n";
		$prompt .= "- Format as valid JSON\n\n";

		$prompt .= "Schema Type: " . $data['schema_type'] . "\n\n";
		$prompt .= "Site Name: " . $data['site_name'] . "\n";
		$prompt .= "Site URL: " . $data['site_url'] . "\n\n";

		if ( ! empty( $data['author_name'] ) ) {
			$prompt .= "Author: " . $data['author_name'] . "\n\n";
		}

		if ( ! empty( $data['publish_date'] ) ) {
			$prompt .= "Published Date: " . $data['publish_date'] . "\n\n";
		}

		if ( ! empty( $data['destination'] ) ) {
			$prompt .= "Destination: " . $data['destination'] . "\n\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";

		$prompt .= "Content:\n";
		$prompt .= self::truncate_content( $data['post_content'], 1000 );

		$prompt .= "\n\nGenerate ONLY valid JSON-LD schema, nothing else.";

		return apply_filters( 'seo_autopilot_lite_schema_prompt', $prompt, $data );
	}

	/**
	 * Build prompt for content improvement suggestions.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_improvement_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'focus_keyword'    => '',
			'post_type'        => 'post',
			'seo_score'        => 0,
			'seo_issues'       => array(),
			'word_count'       => 0,
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert. Analyze this content and provide specific improvement suggestions.\n\n";
		$prompt .= "Current SEO Score: {$data['seo_score']}/100\n";
		$prompt .= "Word Count: {$data['word_count']}\n\n";

		if ( ! empty( $data['focus_keyword'] ) ) {
			$prompt .= "Focus Keyword: " . $data['focus_keyword'] . "\n\n";
		}

		if ( ! empty( $data['seo_issues'] ) ) {
			$prompt .= "Identified SEO Issues:\n";
			foreach ( $data['seo_issues'] as $issue ) {
				$prompt .= "- " . $issue . "\n";
			}
			$prompt .= "\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";

		$prompt .= "Content:\n";
		$prompt .= self::truncate_content( $data['post_content'], 1500 );

		$prompt .= "\n\nProvide specific, actionable suggestions to improve SEO. ";
		$prompt .= "Format as a numbered list with clear recommendations. ";
		$prompt .= "Do NOT rewrite the entire content, only provide suggestions.";

		return apply_filters( 'seo_autopilot_lite_improvement_prompt', $prompt, $data );
	}

	/**
	 * Build prompt for Traveler-specific optimization.
	 *
	 * @param array $data Prompt data.
	 * @return string
	 */
	public static function build_traveler_prompt( array $data ): string {
		$defaults = array(
			'post_title'       => '',
			'post_content'     => '',
			'focus_keyword'    => '',
			'traveler_type'    => 'tour',
			'destination'      => '',
			'price'            => '',
			'duration'         => '',
			'highlights'       => array(),
			'inclusions'       => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$prompt = "You are an SEO expert specializing in travel content. Optimize this Traveler theme content for SEO.\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "- Focus on destination-based keywords\n";
		$prompt .= "- Highlight unique selling points\n";
		$prompt .= "- Include seasonal and temporal relevance\n";
		$prompt .= "- Maintain accurate pricing and availability info\n";
		$prompt .= "- DO NOT invent or change prices, dates, or availability\n\n";

		$prompt .= "Traveler Type: " . ucfirst( $data['traveler_type'] ) . "\n";
		$prompt .= "Destination: " . $data['destination'] . "\n\n";

		if ( ! empty( $data['focus_keyword'] ) ) {
			$prompt .= "Focus Keyword: " . $data['focus_keyword'] . "\n\n";
		}

		if ( ! empty( $data['duration'] ) ) {
			$prompt .= "Duration: " . $data['duration'] . "\n\n";
		}

		if ( ! empty( $data['highlights'] ) ) {
			$prompt .= "Highlights:\n";
			foreach ( $data['highlights'] as $highlight ) {
				$prompt .= "- " . $highlight . "\n";
			}
			$prompt .= "\n";
		}

		$prompt .= "Post Title: " . $data['post_title'] . "\n\n";

		$prompt .= "Content:\n";
		$prompt .= self::truncate_content( $data['post_content'], 1000 );

		$prompt .= "\n\nProvide SEO optimization suggestions specifically for travel booking content.";

		return apply_filters( 'seo_autopilot_lite_traveler_prompt', $prompt, $data );
	}

	/**
	 * Truncate content for prompt.
	 *
	 * @param string $content Content to truncate.
	 * @param int    $max_length Maximum length.
	 * @return string
	 */
	private static function truncate_content( string $content, int $max_length ): string {
		$content = strip_shortcodes( $content );
		$content = wp_strip_all_tags( $content );
		$content = trim( $content );

		if ( mb_strlen( $content ) <= $max_length ) {
			return $content;
		}

		return mb_substr( $content, 0, $max_length ) . "... [content truncated]";
	}

	/**
	 * Get default system prompt.
	 *
	 * @return string
	 */
	public static function get_system_prompt(): string {
		return "You are an SEO expert assistant helping WordPress site owners optimize their content for search engines. " .
		       "Provide accurate, actionable advice. Never invent facts, prices, or availability. " .
		       "Always prioritize user experience and content quality over keyword stuffing.";
	}
}
