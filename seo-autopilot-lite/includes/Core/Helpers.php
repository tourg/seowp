<?php
/**
 * Core Helpers Utility Class
 *
 * Common helper functions used throughout the plugin.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Helpers
 */
class Helpers {

	/**
	 * Get the plugin instance.
	 *
	 * @return Plugin
	 */
	public static function get_plugin(): Plugin {
		return Plugin::get_instance();
	}

	/**
	 * Get plugin URL.
	 *
	 * @param string $path Optional path to append.
	 * @return string
	 */
	public static function plugin_url( string $path = '' ): string {
		$url = trailingslashit( plugins_url( '', self::get_plugin()->get_file() ) );
		return $path ? $url . ltrim( $path, '/' ) : $url;
	}

	/**
	 * Get plugin path.
	 *
	 * @param string $path Optional path to append.
	 * @return string
	 */
	public static function plugin_path( string $path = '' ): string {
		$path_value = trailingslashit( self::get_plugin()->get_path() );
		return $path ? $path_value . ltrim( $path, '/' ) : $path_value;
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public static function get_version(): string {
		return self::get_plugin()->get_version();
	}

	/**
	 * Format a number with proper separator.
	 *
	 * @param int $number Number to format.
	 * @return string
	 */
	public static function format_number( int $number ): string {
		return number_format_i18n( $number );
	}

	/**
	 * Get readable file size.
	 *
	 * @param int $bytes File size in bytes.
	 * @return string
	 */
	public static function readable_file_size( int $bytes ): string {
		$units = array( 'B', 'KB', 'MB', 'GB' );
		$i     = 0;
		while ( $bytes >= 1024 && $i < count( $units ) - 1 ) {
			$bytes /= 1024;
			$i++;
		}
		return round( $bytes, 2 ) . ' ' . $units[ $i ];
	}

	/**
	 * Truncate text to specified length.
	 *
	 * @param string $text   Text to truncate.
	 * @param int    $length Maximum length.
	 * @param string $suffix Suffix to add if truncated.
	 * @return string
	 */
	public static function truncate_text( string $text, int $length = 160, string $suffix = '...' ): string {
		if ( mb_strlen( $text ) <= $length ) {
			return $text;
		}
		return mb_substr( $text, 0, $length ) . $suffix;
	}

	/**
	 * Count words in content.
	 *
	 * @param string $content Content to analyze.
	 * @return int
	 */
	public static function count_words( string $content ): int {
		// Strip shortcodes and HTML tags.
		$content = strip_shortcodes( $content );
		$content = wp_strip_all_tags( $content );
		
		// Count words.
		return str_word_count( $content );
	}

	/**
	 * Count sentences in content.
	 *
	 * @param string $content Content to analyze.
	 * @return int
	 */
	public static function count_sentences( string $content ): int {
		// Strip shortcodes and HTML tags.
		$content = strip_shortcodes( $content );
		$content = wp_strip_all_tags( $content );
		
		// Count sentences by punctuation.
		preg_match_all( '/[.!?]+/', $content, $matches );
		return count( $matches[0] ) ?: 1;
	}

	/**
	 * Calculate average sentence length.
	 *
	 * @param string $content Content to analyze.
	 * @return float
	 */
	public static function average_sentence_length( string $content ): float {
		$words      = self::count_words( $content );
		$sentences  = self::count_sentences( $content );
		
		if ( $sentences === 0 ) {
			return 0;
		}
		
		return round( $words / $sentences, 2 );
	}

	/**
	 * Count paragraphs in content.
	 *
	 * @param string $content Content to analyze.
	 * @return int
	 */
	public static function count_paragraphs( string $content ): int {
		$content = trim( $content );
		if ( empty( $content ) ) {
			return 0;
		}
		return substr_count( $content, "\n\n" ) + 1;
	}

	/**
	 * Extract images from content.
	 *
	 * @param string $content Content to search.
	 * @return array Array of image URLs.
	 */
	public static function extract_images( string $content ): array {
		preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches );
		return array_unique( $matches[1] ?? array() );
	}

	/**
	 * Extract links from content.
	 *
	 * @param string $content Content to search.
	 * @return array Array of link data.
	 */
	public static function extract_links( string $content ): array {
		preg_match_all( '/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $content, $matches, PREG_SET_ORDER );
		
		$links = array();
		foreach ( $matches as $match ) {
			$links[] = array(
				'url'  => $match[1],
				'text' => strip_tags( $match[2] ),
			);
		}
		
		return $links;
	}

	/**
	 * Check if URL is internal.
	 *
	 * @param string $url URL to check.
	 * @return bool
	 */
	public static function is_internal_url( string $url ): bool {
		$site_url = home_url();
		return strpos( $url, $site_url ) === 0;
	}

	/**
	 * Extract headings from content.
	 *
	 * @param string $content Content to search.
	 * @return array Array of headings with level and text.
	 */
	public static function extract_headings( string $content ): array {
		preg_match_all( '/<h([1-6])[^>]*>(.*?)<\/h\\1>/i', $content, $matches, PREG_SET_ORDER );
		
		$headings = array();
		foreach ( $matches as $match ) {
			$headings[] = array(
				'level' => (int) $match[1],
				'text'  => strip_tags( $match[2] ),
			);
		}
		
		return $headings;
	}

	/**
	 * Get focus keyword variations.
	 *
	 * @param string $keyword Focus keyword.
	 * @return array Array of variations.
	 */
	public static function get_keyword_variations( string $keyword ): array {
		$variations = array();
		
		// Original keyword.
		$variations[] = $keyword;
		
		// Lowercase.
		$variations[] = strtolower( $keyword );
		
		// With hyphens replaced by spaces.
		if ( strpos( $keyword, '-' ) !== false ) {
			$variations[] = str_replace( '-', ' ', $keyword );
		}
		
		// With underscores replaced by spaces.
		if ( strpos( $keyword, '_' ) !== false ) {
			$variations[] = str_replace( '_', ' ', $keyword );
		}
		
		return array_unique( $variations );
	}

	/**
	 * Count keyword occurrences in content.
	 *
	 * @param string $content Content to search.
	 * @param string $keyword Keyword to count.
	 * @return int
	 */
	public static function count_keyword_occurrences( string $content, string $keyword ): int {
		$variations = self::get_keyword_variations( $keyword );
		$count      = 0;
		
		// Strip HTML tags for counting.
		$plain_content = strip_tags( $content );
		$plain_content = strtolower( $plain_content );
		
		foreach ( $variations as $variation ) {
			$variation_lower = strtolower( $variation );
			$count          += substr_count( $plain_content, $variation_lower );
		}
		
		return $count;
	}

	/**
	 * Calculate keyword density.
	 *
	 * @param string $content Content to analyze.
	 * @param string $keyword Keyword to analyze.
	 * @return float Density percentage.
	 */
	public static function calculate_keyword_density( string $content, string $keyword ): float {
		$total_words = self::count_words( $content );
		
		if ( $total_words === 0 ) {
			return 0;
		}
		
		$occurrences = self::count_keyword_occurrences( $content, $keyword );
		return round( ( $occurrences / $total_words ) * 100, 2 );
	}

	/**
	 * Get post type label.
	 *
	 * @param string $post_type Post type name.
	 * @return string
	 */
	public static function get_post_type_label( string $post_type ): string {
		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object && isset( $post_type_object->labels->singular_name ) ) {
			return $post_type_object->labels->singular_name;
		}
		return ucfirst( $post_type );
	}

	/**
	 * Get status badge class.
	 *
	 * @param string $status Status value.
	 * @return string CSS class.
	 */
	public static function get_status_badge_class( string $status ): string {
		switch ( $status ) {
			case 'good':
			case 'success':
				return 'sap-badge-success';
			case 'ok':
			case 'warning':
				return 'sap-badge-warning';
			case 'bad':
			case 'error':
				return 'sap-badge-error';
			default:
				return 'sap-badge-info';
		}
	}

	/**
	 * Get score color class.
	 *
	 * @param int $score Score value (0-100).
	 * @return string CSS class.
	 */
	public static function get_score_color_class( int $score ): string {
		if ( $score >= 80 ) {
			return 'sap-score-good';
		} elseif ( $score >= 60 ) {
			return 'sap-score-ok';
		} elseif ( $score >= 40 ) {
			return 'sap-score-needs-improvement';
		} else {
			return 'sap-score-poor';
		}
	}

	/**
	 * Get score label.
	 *
	 * @param int $score Score value (0-100).
	 * @return string
	 */
	public static function get_score_label( int $score ): string {
		if ( $score >= 80 ) {
			return __( 'Good', 'seo-autopilot-lite' );
		} elseif ( $score >= 60 ) {
			return __( 'OK', 'seo-autopilot-lite' );
		} elseif ( $score >= 40 ) {
			return __( 'Needs Improvement', 'seo-autopilot-lite' );
		} else {
			return __( 'Poor', 'seo-autopilot-lite' );
		}
	}

	/**
	 * Normalize URL.
	 *
	 * @param string $url URL to normalize.
	 * @return string Normalized URL.
	 */
	public static function normalize_url( string $url ): string {
		$url = trim( $url );
		
		// Add http:// if no protocol specified.
		if ( ! empty( $url ) && ! preg_match( '#^https?://#i', $url ) ) {
			$url = 'http://' . $url;
		}
		
		return esc_url_raw( $url );
	}

	/**
	 * Get site default schema.
	 *
	 * @return array
	 */
	public static function get_site_schema(): array {
		$settings = Settings::get_instance()->get_settings();
		
		return array(
			'site_name'         => get_bloginfo( 'name' ),
			'site_url'          => home_url(),
			'logo'              => $settings['site_logo'] ?? '',
			'organization_name' => $settings['organization_name'] ?? get_bloginfo( 'name' ),
			'business_type'     => $settings['business_type'] ?? 'Organization',
			'country'           => $settings['default_country'] ?? 'US',
			'language'          => $settings['default_language'] ?? 'en',
		);
	}

	/**
	 * Is current request an AJAX request?
	 *
	 * @return bool
	 */
	public static function is_ajax(): bool {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Is current request a REST API request?
	 *
	 * @return bool
	 */
	public static function is_rest_request(): bool {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	/**
	 * Get current user ID.
	 *
	 * @return int
	 */
	public static function get_current_user_id(): int {
		return get_current_user_id();
	}

	/**
	 * Check if current user can manage options.
	 *
	 * @return bool
	 */
	public static function current_user_can_manage_options(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Log debug message (if WP_DEBUG is enabled).
	 *
	 * @param mixed  $message Message to log.
	 * @param string $context Context identifier.
	 */
	public static function debug_log( $message, string $context = 'seo-autopilot-lite' ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[' . $context . '] ' . print_r( $message, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Clear cache for a specific post.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function clear_post_cache( int $post_id ): void {
		delete_post_meta( $post_id, '_seo_autopilot_seo_score' );
		delete_post_meta( $post_id, '_seo_autopilot_analysis' );
	}

	/**
	 * Get supported post types.
	 *
	 * @return array
	 */
	public static function get_supported_post_types(): array {
		$settings = Settings::get_instance()->get_settings();
		$post_types = $settings['post_types'] ?? array( 'post', 'page' );
		
		// Ensure at least post and page are included.
		if ( empty( $post_types ) ) {
			$post_types = array( 'post', 'page' );
		}
		
		return apply_filters( 'seo_autopilot_lite_supported_post_types', $post_types );
	}

	/**
	 * Check if post type is supported.
	 *
	 * @param string $post_type Post type to check.
	 * @return bool
	 */
	public static function is_post_type_supported( string $post_type ): bool {
		return in_array( $post_type, self::get_supported_post_types(), true );
	}
}
