<?php
/**
 * Snippet Preview Generator
 *
 * Generates Google search snippet preview.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\SEO;

use SEO_Autopilot_Lite\Core\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class SnippetPreview
 */
class SnippetPreview {

	/**
	 * Maximum title length in pixels (approximate).
	 *
	 * @var int
	 */
	const MAX_TITLE_LENGTH = 60;

	/**
	 * Maximum description length in pixels (approximate).
	 *
	 * @var int
	 */
	const MAX_DESCRIPTION_LENGTH = 160;

	/**
	 * Generate snippet preview data.
	 *
	 * @param int   $post_id     Post ID.
	 * @param array $seo_meta    SEO meta data.
	 * @param array $post_data   Post data (title, content, excerpt).
	 * @return array Snippet preview data.
	 */
	public static function generate( int $post_id, array $seo_meta, array $post_data ): array {
		$title       = self::get_preview_title( $post_id, $seo_meta, $post_data );
		$description = self::get_preview_description( $post_id, $seo_meta, $post_data );
		$url         = self::get_preview_url( $post_id );

		return array(
			'title'                  => $title,
			'title_length'           => mb_strlen( $title ),
			'title_too_short'        => mb_strlen( $title ) < 30,
			'title_too_long'         => mb_strlen( $title ) > self::MAX_TITLE_LENGTH,
			'description'            => $description,
			'description_length'     => mb_strlen( $description ),
			'description_too_short'  => mb_strlen( $description ) < 120,
			'description_too_long'   => mb_strlen( $description ) > self::MAX_DESCRIPTION_LENGTH,
			'url'                    => $url,
			'site_name'              => get_bloginfo( 'name' ),
			'post_date'              => get_the_date( '', $post_id ),
			'is_mobile'              => false,
		);
	}

	/**
	 * Get preview title.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $seo_meta SEO meta data.
	 * @param array $post_data Post data.
	 * @return string
	 */
	public static function get_preview_title( int $post_id, array $seo_meta, array $post_data ): string {
		// Use SEO title if set.
		if ( ! empty( $seo_meta['title'] ) ) {
			return $seo_meta['title'];
		}

		// Use post title as fallback.
		if ( ! empty( $post_data['title'] ) ) {
			return $post_data['title'];
		}

		return '';
	}

	/**
	 * Get preview description.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $seo_meta SEO meta data.
	 * @param array $post_data Post data.
	 * @return string
	 */
	public static function get_preview_description( int $post_id, array $seo_meta, array $post_data ): string {
		// Use meta description if set.
		if ( ! empty( $seo_meta['description'] ) ) {
			return $seo_meta['description'];
		}

		// Use excerpt if available.
		if ( ! empty( $post_data['excerpt'] ) ) {
			return Helpers::truncate_text( $post_data['excerpt'], self::MAX_DESCRIPTION_LENGTH );
		}

		// Generate from content.
		if ( ! empty( $post_data['content'] ) ) {
			$content = wp_strip_all_tags( $post_data['content'] );
			$content = strip_shortcodes( $content );
			$content = trim( $content );
			
			return Helpers::truncate_text( $content, self::MAX_DESCRIPTION_LENGTH );
		}

		return '';
	}

	/**
	 * Get preview URL.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function get_preview_url( int $post_id ): string {
		$permalink = get_permalink( $post_id );
		
		if ( ! $permalink ) {
			return home_url();
		}

		// Parse URL to show nice format.
		$parsed = wp_parse_url( $permalink );
		$path   = $parsed['path'] ?? '/';
		
		// Remove trailing slash.
		$path = rtrim( $path, '/' );
		
		// Show domain and path.
		return $parsed['host'] . $path;
	}

	/**
	 * Get title length status.
	 *
	 * @param int $length Title length.
	 * @return string Status.
	 */
	public static function get_title_length_status( int $length ): string {
		if ( $length < 30 ) {
			return 'bad';
		} elseif ( $length > self::MAX_TITLE_LENGTH ) {
			return 'warning';
		} else {
			return 'good';
		}
	}

	/**
	 * Get description length status.
	 *
	 * @param int $length Description length.
	 * @return string Status.
	 */
	public static function get_description_length_status( int $length ): string {
		if ( $length < 120 ) {
			return 'bad';
		} elseif ( $length > self::MAX_DESCRIPTION_LENGTH ) {
			return 'warning';
		} else {
			return 'good';
		}
	}

	/**
	 * Render snippet preview HTML.
	 *
	 * @param array $data Snippet preview data.
	 * @return string HTML.
	 */
	public static function render_html( array $data ): string {
		$status_class = '';
		if ( $data['title_too_long'] || $data['description_too_long'] ) {
			$status_class = 'sap-snippet-warning';
		} elseif ( $data['title_too_short'] || $data['description_too_short'] ) {
			$status_class = 'sap-snippet-needs-improvement';
		}

		ob_start();
		?>
		<div class="sap-snippet-preview <?php echo esc_attr( $status_class ); ?>">
			<div class="sap-snippet">
				<div class="sap-snippet__meta">
					<span class="sap-snippet__url"><?php echo esc_html( $data['url'] ); ?></span>
				</div>
				<h3 class="sap-snippet__title">
					<a href="#" tabindex="-1"><?php echo esc_html( $data['title'] ); ?></a>
				</h3>
				<p class="sap-snippet__description">
					<?php echo esc_html( $data['description'] ); ?>
				</p>
			</div>
			<div class="sap-snippet__stats">
				<span class="sap-snippet__stat">
					<?php printf( 
						/* translators: %d: title length */
						esc_html__( 'Title: %d characters', 'seo-autopilot-lite' ), 
						intval( $data['title_length'] ) 
					); ?>
					<?php if ( $data['title_too_long'] ): ?>
						<span class="sap-snippet__stat-warning dashicons dashicons-warning"></span>
					<?php endif; ?>
				</span>
				<span class="sap-snippet__stat">
					<?php printf( 
						/* translators: %d: description length */
						esc_html__( 'Description: %d characters', 'seo-autopilot-lite' ), 
						intval( $data['description_length'] ) 
					); ?>
					<?php if ( $data['description_too_long'] ): ?>
						<span class="sap-snippet__stat-warning dashicons dashicons-warning"></span>
					<?php endif; ?>
				</span>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get mobile snippet preview data.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $seo_meta SEO meta data.
	 * @param array $post_data Post data.
	 * @return array Mobile snippet data.
	 */
	public static function generate_mobile( int $post_id, array $seo_meta, array $post_data ): array {
		$data = self::generate( $post_id, $seo_meta, $post_data );
		$data['is_mobile'] = true;
		
		// Mobile has shorter limits.
		$data['max_title_length'] = 50;
		$data['max_description_length'] = 140;
		
		return $data;
	}
}
