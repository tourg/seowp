<?php
/**
 * SEO Meta Handler
 *
 * Manages SEO meta data for posts.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\SEO;

use SEO_Autopilot_Lite\Core\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Meta
 */
class Meta {

	/**
	 * Meta key prefix.
	 *
	 * @var string
	 */
	const META_PREFIX = '_seo_autopilot_';

	/**
	 * Get SEO title for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_title( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'title', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update SEO title for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $title   SEO title.
	 * @return bool|int
	 */
	public static function update_title( int $post_id, string $title ) {
		if ( empty( $title ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'title' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'title', sanitize_text_field( $title ) );
	}

	/**
	 * Get meta description for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_description( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'description', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update meta description for a post.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $description Meta description.
	 * @return bool|int
	 */
	public static function update_description( int $post_id, string $description ) {
		if ( empty( $description ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'description' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Get focus keyword for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_focus_keyword( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'focus_keyword', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update focus keyword for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $keyword Focus keyword.
	 * @return bool|int
	 */
	public static function update_focus_keyword( int $post_id, string $keyword ) {
		if ( empty( $keyword ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'focus_keyword' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'focus_keyword', sanitize_text_field( $keyword ) );
	}

	/**
	 * Get canonical URL for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_canonical( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'canonical', true );
		return ! empty( $value ) ? esc_url_raw( $value ) : null;
	}

	/**
	 * Update canonical URL for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $url     Canonical URL.
	 * @return bool|int
	 */
	public static function update_canonical( int $post_id, string $url ) {
		if ( empty( $url ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'canonical' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'canonical', esc_url_raw( $url ) );
	}

	/**
	 * Check if post should be noindex.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function is_noindex( int $post_id ): bool {
		return (bool) get_post_meta( $post_id, self::META_PREFIX . 'noindex', true );
	}

	/**
	 * Update noindex setting for a post.
	 *
	 * @param int  $post_id  Post ID.
	 * @param bool $noindex  Noindex value.
	 * @return bool|int
	 */
	public static function update_noindex( int $post_id, bool $noindex ) {
		return update_post_meta( $post_id, self::META_PREFIX . 'noindex', $noindex );
	}

	/**
	 * Check if post should be nofollow.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function is_nofollow( int $post_id ): bool {
		return (bool) get_post_meta( $post_id, self::META_PREFIX . 'nofollow', true );
	}

	/**
	 * Update nofollow setting for a post.
	 *
	 * @param int  $post_id   Post ID.
	 * @param bool $nofollow  Nofollow value.
	 * @return bool|int
	 */
	public static function update_nofollow( int $post_id, bool $nofollow ) {
		return update_post_meta( $post_id, self::META_PREFIX . 'nofollow', $nofollow );
	}

	/**
	 * Get Open Graph title for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_og_title( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'og_title', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update Open Graph title for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $title   OG title.
	 * @return bool|int
	 */
	public static function update_og_title( int $post_id, string $title ) {
		if ( empty( $title ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'og_title' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'og_title', sanitize_text_field( $title ) );
	}

	/**
	 * Get Open Graph description for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_og_description( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'og_description', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update Open Graph description for a post.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $description OG description.
	 * @return bool|int
	 */
	public static function update_og_description( int $post_id, string $description ) {
		if ( empty( $description ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'og_description' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'og_description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Get Open Graph image for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_og_image( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'og_image', true );
		return ! empty( $value ) ? esc_url_raw( $value ) : null;
	}

	/**
	 * Update Open Graph image for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $image   OG image URL.
	 * @return bool|int
	 */
	public static function update_og_image( int $post_id, string $image ) {
		if ( empty( $image ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'og_image' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'og_image', esc_url_raw( $image ) );
	}

	/**
	 * Get Twitter title for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_twitter_title( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'twitter_title', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update Twitter title for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $title   Twitter title.
	 * @return bool|int
	 */
	public static function update_twitter_title( int $post_id, string $title ) {
		if ( empty( $title ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'twitter_title' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'twitter_title', sanitize_text_field( $title ) );
	}

	/**
	 * Get Twitter description for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_twitter_description( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'twitter_description', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update Twitter description for a post.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $description Twitter description.
	 * @return bool|int
	 */
	public static function update_twitter_description( int $post_id, string $description ) {
		if ( empty( $description ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'twitter_description' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'twitter_description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Get Twitter image for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_twitter_image( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'twitter_image', true );
		return ! empty( $value ) ? esc_url_raw( $value ) : null;
	}

	/**
	 * Update Twitter image for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $image   Twitter image URL.
	 * @return bool|int
	 */
	public static function update_twitter_image( int $post_id, string $image ) {
		if ( empty( $image ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'twitter_image' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'twitter_image', esc_url_raw( $image ) );
	}

	/**
	 * Get schema type for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string|null
	 */
	public static function get_schema_type( int $post_id ): ?string {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'schema_type', true );
		return ! empty( $value ) ? $value : null;
	}

	/**
	 * Update schema type for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $type    Schema type.
	 * @return bool|int
	 */
	public static function update_schema_type( int $post_id, string $type ) {
		if ( empty( $type ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'schema_type' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'schema_type', sanitize_text_field( $type ) );
	}

	/**
	 * Get schema JSON for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array|null
	 */
	public static function get_schema_json( int $post_id ): ?array {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'schema_json', true );
		if ( empty( $value ) ) {
			return null;
		}
		return is_array( $value ) ? $value : json_decode( $value, true );
	}

	/**
	 * Update schema JSON for a post.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $schema  Schema JSON array.
	 * @return bool|int
	 */
	public static function update_schema_json( int $post_id, array $schema ) {
		if ( empty( $schema ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'schema_json' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'schema_json', $schema );
	}

	/**
	 * Get SEO score for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return int|null
	 */
	public static function get_seo_score( int $post_id ): ?int {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'seo_score', true );
		return ! empty( $value ) ? (int) $value : null;
	}

	/**
	 * Update SEO score for a post.
	 *
	 * @param int $post_id Post ID.
	 * @param int $score   SEO score.
	 * @return bool|int
	 */
	public static function update_seo_score( int $post_id, int $score ) {
		return update_post_meta( $post_id, self::META_PREFIX . 'seo_score', $score );
	}

	/**
	 * Get SEO analysis results for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array|null
	 */
	public static function get_analysis( int $post_id ): ?array {
		$value = get_post_meta( $post_id, self::META_PREFIX . 'analysis', true );
		if ( empty( $value ) ) {
			return null;
		}
		return is_array( $value ) ? $value : json_decode( $value, true );
	}

	/**
	 * Update SEO analysis results for a post.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $analysis Analysis results.
	 * @return bool|int
	 */
	public static function update_analysis( int $post_id, array $analysis ) {
		if ( empty( $analysis ) ) {
			return delete_post_meta( $post_id, self::META_PREFIX . 'analysis' );
		}
		return update_post_meta( $post_id, self::META_PREFIX . 'analysis', $analysis );
	}

	/**
	 * Get all SEO meta for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public static function get_all_meta( int $post_id ): array {
		return array(
			'title'               => self::get_title( $post_id ),
			'description'         => self::get_description( $post_id ),
			'focus_keyword'       => self::get_focus_keyword( $post_id ),
			'canonical'           => self::get_canonical( $post_id ),
			'noindex'             => self::is_noindex( $post_id ),
			'nofollow'            => self::is_nofollow( $post_id ),
			'og_title'            => self::get_og_title( $post_id ),
			'og_description'      => self::get_og_description( $post_id ),
			'og_image'            => self::get_og_image( $post_id ),
			'twitter_title'       => self::get_twitter_title( $post_id ),
			'twitter_description' => self::get_twitter_description( $post_id ),
			'twitter_image'       => self::get_twitter_image( $post_id ),
			'schema_type'         => self::get_schema_type( $post_id ),
			'schema_json'         => self::get_schema_json( $post_id ),
			'seo_score'           => self::get_seo_score( $post_id ),
			'analysis'            => self::get_analysis( $post_id ),
		);
	}

	/**
	 * Delete all SEO meta for a post.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function delete_all_meta( int $post_id ): void {
		$meta_keys = array(
			'title',
			'description',
			'focus_keyword',
			'canonical',
			'noindex',
			'nofollow',
			'og_title',
			'og_description',
			'og_image',
			'twitter_title',
			'twitter_description',
			'twitter_image',
			'schema_type',
			'schema_json',
			'seo_score',
			'analysis',
		);

		foreach ( $meta_keys as $key ) {
			delete_post_meta( $post_id, self::META_PREFIX . $key );
		}
	}

	/**
	 * Get robots meta value.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function get_robots_value( int $post_id ): string {
		$index   = self::is_noindex( $post_id ) ? 'noindex' : 'index';
		$follow  = self::is_nofollow( $post_id ) ? 'nofollow' : 'follow';
		
		return $index . ',' . $follow;
	}
}
