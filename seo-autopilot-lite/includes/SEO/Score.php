<?php
/**
 * SEO Score Calculator
 *
 * Calculates overall SEO score based on analysis results.
 *
 * @package SEO_Autopilot_Lite
 * @since 1.0.0
 */

namespace SEO_Autopilot_Lite\SEO;

defined( 'ABSPATH' ) || exit;

/**
 * Class Score
 */
class Score {

	/**
	 * Score weights for different categories.
	 *
	 * @var array
	 */
	private static $weights = array(
		'title'          => 20,
		'description'    => 15,
		'focus_keyword'  => 20,
		'content'        => 15,
		'headings'       => 10,
		'images'         => 8,
		'internal_links' => 7,
		'readability'    => 5,
	);

	/**
	 * Calculate overall SEO score from analysis results.
	 *
	 * @param array $analysis Analysis results.
	 * @return int Score (0-100).
	 */
	public static function calculate( array $analysis ): int {
		if ( empty( $analysis ) ) {
			return 0;
		}

		$total_score   = 0;
		$total_weight  = 0;

		foreach ( $analysis as $result ) {
			if ( ! isset( $result['status'] ) || ! isset( $result['weight'] ) ) {
				continue;
			}

			$status_points = self::get_status_points( $result['status'] );
			$weight        = (int) $result['weight'];

			$total_score  += $status_points * $weight;
			$total_weight += $weight;
		}

		if ( $total_weight === 0 ) {
			return 0;
		}

		// Calculate percentage score.
		$score = round( ( $total_score / ( $total_weight * 100 ) ) * 100 );

		return min( 100, max( 0, $score ) );
	}

	/**
	 * Get points for a status.
	 *
	 * @param string $status Status value.
	 * @return int Points (0-100).
	 */
	public static function get_status_points( string $status ): int {
		switch ( $status ) {
			case 'good':
				return 100;
			case 'ok':
				return 75;
			case 'warning':
				return 50;
			case 'bad':
				return 25;
			default:
				return 50;
		}
	}

	/**
	 * Get score label based on score value.
	 *
	 * @param int $score Score value.
	 * @return string
	 */
	public static function get_label( int $score ): string {
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
	 * Get score color class.
	 *
	 * @param int $score Score value.
	 * @return string
	 */
	public static function get_color_class( int $score ): string {
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
	 * Get score icon.
	 *
	 * @param int $score Score value.
	 * @return string Dashicon class.
	 */
	public static function get_icon( int $score ): string {
		if ( $score >= 80 ) {
			return 'dashicons-yes-alt';
		} elseif ( $score >= 60 ) {
			return 'dashicons-warning';
		} else {
			return 'dashicons-no-alt';
		}
	}

	/**
	 * Get default weights.
	 *
	 * @return array
	 */
	public static function get_weights(): array {
		return apply_filters( 'seo_autopilot_lite_score_weights', self::$weights );
	}

	/**
	 * Calculate score for individual rule.
	 *
	 * @param array $rule_result Rule analysis result.
	 * @return int Score for this rule (0-100).
	 */
	public static function calculate_rule_score( array $rule_result ): int {
		if ( ! isset( $rule_result['status'] ) ) {
			return 50;
		}

		return self::get_status_points( $rule_result['status'] );
	}

	/**
	 * Get score breakdown by category.
	 *
	 * @param array $analysis Analysis results.
	 * @return array Score breakdown.
	 */
	public static function get_breakdown( array $analysis ): array {
		$breakdown = array();

		foreach ( self::$weights as $category => $weight ) {
			$category_results = array_filter(
				$analysis,
				function ( $result ) use ( $category ) {
					return isset( $result['category'] ) && $result['category'] === $category;
				}
			);

			if ( ! empty( $category_results ) ) {
				$category_score = self::calculate( $category_results );
			} else {
				$category_score = 0;
			}

			$breakdown[ $category ] = array(
				'score'  => $category_score,
				'weight' => $weight,
				'label'  => self::get_category_label( $category ),
			);
		}

		return $breakdown;
	}

	/**
	 * Get human-readable category label.
	 *
	 * @param string $category Category slug.
	 * @return string
	 */
	public static function get_category_label( string $category ): string {
		$labels = array(
			'title'          => __( 'Title', 'seo-autopilot-lite' ),
			'description'    => __( 'Description', 'seo-autopilot-lite' ),
			'focus_keyword'  => __( 'Focus Keyword', 'seo-autopilot-lite' ),
			'content'        => __( 'Content', 'seo-autopilot-lite' ),
			'headings'       => __( 'Headings', 'seo-autopilot-lite' ),
			'images'         => __( 'Images', 'seo-autopilot-lite' ),
			'internal_links' => __( 'Internal Links', 'seo-autopilot-lite' ),
			'readability'    => __( 'Readability', 'seo-autopilot-lite' ),
		);

		return $labels[ $category ] ?? ucfirst( $category );
	}
}
