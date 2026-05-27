<?php
/**
 * Traveler Field Mapper - Centralizes all Traveler custom field extraction.
 * 
 * This class provides a unified interface for accessing Traveler theme fields
 * with safe fallbacks, caching, and extensibility via filters.
 *
 * @package SeoAutopilotLite\Traveler
 */

namespace SeoAutopilotLite\Traveler;

class TravelerFieldMapper {
    
    /**
     * Cache of field values per post ID to avoid repeated DB queries.
     *
     * @var array
     */
    private static array $field_cache = [];
    
    /**
     * Known Traveler CPTs.
     *
     * @var array
     */
    private array $traveler_cpts = [
        'st_tours',
        'st_activity',
        'st_hotel',
        'st_rental',
        'st_cars',
        'st_location',
        'hotel_room',
    ];
    
    /**
     * Check if a post type is a Traveler CPT.
     *
     * @param string $post_type Post type slug.
     * @return bool
     */
    public function is_traveler_post_type( string $post_type ): bool {
        /**
         * Filter to modify Traveler CPTs.
         *
         * @param array $traveler_cpts Array of Traveler CPT slugs.
         */
        $cpts = apply_filters( 'seo_autopilot_lite_traveler_cpts', $this->traveler_cpts );
        return in_array( $post_type, $cpts, true );
    }
    
    /**
     * Get supported Traveler post types.
     *
     * @return array
     */
    public function get_supported_post_types(): array {
        $cpts = [];
        
        foreach ( $this->traveler_cpts as $cpt ) {
            if ( post_type_exists( $cpt ) ) {
                $cpts[] = $cpt;
            }
        }
        
        /**
         * Filter supported Traveler post types.
         *
         * @param array $cpts Array of existing Traveler CPTs.
         */
        return apply_filters( 'seo_autopilot_lite_traveler_supported_post_types', $cpts );
    }
    
    /**
     * Get tour/activity price.
     *
     * @param int $post_id Post ID.
     * @return float|null Price value or null if not found.
     */
    public function get_price( int $post_id ): ?float {
        return $this->get_cached_field( $post_id, 'price', function() use ( $post_id ) {
            /**
             * Filter price meta keys.
             *
             * @param array $keys Array of meta keys to check for price.
             */
            $keys = apply_filters( 'seo_autopilot_lite_traveler_price_keys', [
                'st_price',
                'st_price_adult',
                'price',
                '_price',
                'regular_price',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                return floatval( $value );
            }
            
            return null;
        } );
    }
    
    /**
     * Get regular/original price.
     *
     * @param int $post_id Post ID.
     * @return float|null Price value or null if not found.
     */
    public function get_regular_price( int $post_id ): ?float {
        return $this->get_cached_field( $post_id, 'regular_price', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_regular_price_keys', [
                'st_price_regular',
                'st_original_price',
                'regular_price',
                '_regular_price',
                'st_price',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                return floatval( $value );
            }
            
            return null;
        } );
    }
    
    /**
     * Get sale/discount price.
     *
     * @param int $post_id Post ID.
     * @return float|null Price value or null if not found.
     */
    public function get_sale_price( int $post_id ): ?float {
        return $this->get_cached_field( $post_id, 'sale_price', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_sale_price_keys', [
                'st_price_sale',
                'st_sale_price',
                'sale_price',
                '_sale_price',
                'discount_price',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                return floatval( $value );
            }
            
            return null;
        } );
    }
    
    /**
     * Get currency code.
     *
     * @param int $post_id Post ID.
     * @return string|null Currency code (e.g., 'USD', 'EUR') or null.
     */
    public function get_currency( int $post_id ): ?string {
        return $this->get_cached_field( $post_id, 'currency', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_currency_keys', [
                'st_currency',
                'currency',
                '_currency',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                return strtoupper( sanitize_text_field( $value ) );
            }
            
            // Fallback to global WooCommerce/Traveler currency.
            $global_currency = get_option( 'woocommerce_currency', 'USD' );
            if ( ! empty( $global_currency ) ) {
                return $global_currency;
            }
            
            return null;
        } );
    }
    
    /**
     * Get tour duration.
     *
     * @param int $post_id Post ID.
     * @return string|null Duration string (e.g., "8 hours", "3 days") or null.
     */
    public function get_duration( int $post_id ): ?string {
        return $this->get_cached_field( $post_id, 'duration', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_duration_keys', [
                'st_duration',
                'duration',
                'tour_duration',
                'st_tour_duration',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                return sanitize_text_field( $value );
            }
            
            return null;
        } );
    }
    
    /**
     * Get destination name.
     *
     * @param int $post_id Post ID.
     * @return string|null Destination name or null.
     */
    public function get_destination( int $post_id ): ?string {
        return $this->get_cached_field( $post_id, 'destination', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_destination_keys', [
                'st_destination',
                'destination',
                'st_tour_destination',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                return sanitize_text_field( $value );
            }
            
            // Try to get from taxonomy.
            $destinations = wp_get_post_terms( $post_id, 'st_destination', [ 'fields' => 'names' ] );
            if ( ! is_wp_error( $destinations ) && ! empty( $destinations ) ) {
                return reset( $destinations );
            }
            
            return null;
        } );
    }
    
    /**
     * Get location name.
     *
     * @param int $post_id Post ID.
     * @return string|null Location name or null.
     */
    public function get_location( int $post_id ): ?string {
        return $this->get_cached_field( $post_id, 'location', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_location_keys', [
                'st_location',
                'location',
                'st_tour_location',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                return sanitize_text_field( $value );
            }
            
            // Try to get from taxonomy.
            $locations = wp_get_post_terms( $post_id, 'st_location', [ 'fields' => 'names' ] );
            if ( ! is_wp_error( $locations ) && ! empty( $locations ) ) {
                return reset( $locations );
            }
            
            return null;
        } );
    }
    
    /**
     * Get full address.
     *
     * @param int $post_id Post ID.
     * @return array|null Address array or null.
     */
    public function get_address( int $post_id ): ?array {
        return $this->get_cached_field( $post_id, 'address', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_address_keys', [
                'st_address',
                'address',
                'st_hotel_address',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                return [
                    'streetAddress' => sanitize_text_field( $value ),
                ];
            }
            
            // Try individual address fields.
            $address = [];
            $street = get_post_meta( $post_id, 'st_street', true );
            $city = get_post_meta( $post_id, 'st_city', true );
            $state = get_post_meta( $post_id, 'st_state', true );
            $country = get_post_meta( $post_id, 'st_country', true );
            $zip = get_post_meta( $post_id, 'st_zip', true );
            
            if ( ! empty( $street ) ) {
                $address['streetAddress'] = sanitize_text_field( $street );
            }
            if ( ! empty( $city ) ) {
                $address['addressLocality'] = sanitize_text_field( $city );
            }
            if ( ! empty( $state ) ) {
                $address['addressRegion'] = sanitize_text_field( $state );
            }
            if ( ! empty( $country ) ) {
                $address['addressCountry'] = sanitize_text_field( $country );
            }
            if ( ! empty( $zip ) ) {
                $address['postalCode'] = sanitize_text_field( $zip );
            }
            
            return ! empty( $address ) ? $address : null;
        } );
    }
    
    /**
     * Get gallery images.
     *
     * @param int $post_id Post ID.
     * @return array Array of image URLs.
     */
    public function get_gallery_images( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'gallery_images', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_gallery_keys', [
                'st_gallery',
                'gallery',
                'st_images',
                'st_photo_gallery',
            ] );
            
            $images = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    // Could be comma-separated IDs or JSON.
                    if ( is_string( $value ) ) {
                        // Try JSON first.
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $image_ids = $decoded;
                        } else {
                            // Comma-separated IDs.
                            $image_ids = array_map( 'intval', explode( ',', $value ) );
                        }
                    } elseif ( is_array( $value ) ) {
                        $image_ids = $value;
                    } else {
                        continue;
                    }
                    
                    foreach ( $image_ids as $img_id ) {
                        $img_url = wp_get_attachment_url( (int) $img_id );
                        if ( $img_url ) {
                            $images[] = $img_url;
                        }
                    }
                    
                    if ( ! empty( $images ) ) {
                        break;
                    }
                }
            }
            
            return array_unique( $images );
        } );
    }
    
    /**
     * Get featured image or first gallery image.
     *
     * @param int $post_id Post ID.
     * @return string|null Image URL or null.
     */
    public function get_featured_or_gallery_image( int $post_id ): ?string {
        // First try featured image.
        $featured_id = get_post_thumbnail_id( $post_id );
        if ( $featured_id ) {
            $url = wp_get_attachment_url( $featured_id );
            if ( $url ) {
                return $url;
            }
        }
        
        // Fall back to gallery.
        $gallery = $this->get_gallery_images( $post_id );
        if ( ! empty( $gallery ) ) {
            return reset( $gallery );
        }
        
        return null;
    }
    
    /**
     * Get average rating.
     *
     * @param int $post_id Post ID.
     * @return float|null Rating value (0-5) or null.
     */
    public function get_rating( int $post_id ): ?float {
        return $this->get_cached_field( $post_id, 'rating', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_rating_keys', [
                'st_rate',
                'st_rating',
                'st_average_rate',
                'average_rating',
                '_wc_average_rating',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                $rating = floatval( $value );
                // Ensure rating is within valid range.
                if ( $rating >= 0 && $rating <= 5 ) {
                    return round( $rating, 2 );
                }
            }
            
            return null;
        } );
    }
    
    /**
     * Get review count.
     *
     * @param int $post_id Post ID.
     * @return int|null Review count or null.
     */
    public function get_review_count( int $post_id ): ?int {
        return $this->get_cached_field( $post_id, 'review_count', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_review_count_keys', [
                'st_review_count',
                'st_total_review',
                'review_count',
                '_wc_review_count',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                $count = intval( $value );
                if ( $count >= 0 ) {
                    return $count;
                }
            }
            
            return null;
        } );
    }
    
    /**
     * Get reviews data.
     *
     * @param int $post_id Post ID.
     * @return array Array of review data arrays.
     */
    public function get_reviews( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'reviews', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_reviews_keys', [
                'st_reviews',
                'reviews',
                'st_review_data',
            ] );
            
            $reviews = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $reviews = $decoded;
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $reviews = $value;
                        break;
                    }
                }
            }
            
            // Format reviews for schema.
            $formatted = [];
            foreach ( $reviews as $review ) {
                if ( is_array( $review ) ) {
                    $formatted_review = [];
                    
                    if ( ! empty( $review['name'] ) || ! empty( $review['author'] ) ) {
                        $formatted_review['author'] = [
                            '@type' => 'Person',
                            'name' => sanitize_text_field( $review['name'] ?? $review['author'] ),
                        ];
                    }
                    
                    if ( ! empty( $review['comment'] ) || ! empty( $review['text'] ) ) {
                        $formatted_review['reviewBody'] = sanitize_textarea_field( $review['comment'] ?? $review['text'] );
                    }
                    
                    if ( ! empty( $review['rate'] ) || ! empty( $review['rating'] ) ) {
                        $formatted_review['reviewRating'] = [
                            '@type' => 'Rating',
                            'ratingValue' => intval( $review['rate'] ?? $review['rating'] ),
                        ];
                    }
                    
                    if ( ! empty( $review['date'] ) ) {
                        $formatted_review['datePublished'] = sanitize_text_field( $review['date'] );
                    }
                    
                    if ( ! empty( $formatted_review ) ) {
                        $formatted[] = $formatted_review;
                    }
                }
            }
            
            return $formatted;
        } );
    }
    
    /**
     * Get FAQs.
     *
     * @param int $post_id Post ID.
     * @return array Array of FAQ items with 'question' and 'answer'.
     */
    public function get_faqs( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'faqs', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_faq_keys', [
                'st_faqs',
                'faqs',
                'st_faq',
                'st_tour_faq',
            ] );
            
            $faqs = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $faqs = $decoded;
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $faqs = $value;
                        break;
                    }
                }
            }
            
            // Format FAQs.
            $formatted = [];
            foreach ( $faqs as $faq ) {
                if ( is_array( $faq ) ) {
                    $question = $faq['title'] ?? $faq['question'] ?? '';
                    $answer = $faq['content'] ?? $faq['answer'] ?? '';
                    
                    if ( ! empty( $question ) && ! empty( $answer ) ) {
                        $formatted[] = [
                            'question' => sanitize_text_field( $question ),
                            'answer' => wp_kses_post( $answer ),
                        ];
                    }
                }
            }
            
            return $formatted;
        } );
    }
    
    /**
     * Get itinerary.
     *
     * @param int $post_id Post ID.
     * @return array Array of itinerary items.
     */
    public function get_itinerary( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'itinerary', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_itinerary_keys', [
                'st_itinerary',
                'itinerary',
                'st_tour_itinerary',
            ] );
            
            $itinerary = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $itinerary = $decoded;
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $itinerary = $value;
                        break;
                    }
                }
            }
            
            return $itinerary;
        } );
    }
    
    /**
     * Get tour highlights.
     *
     * @param int $post_id Post ID.
     * @return array Array of highlight strings.
     */
    public function get_highlights( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'highlights', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_highlights_keys', [
                'st_highlights',
                'highlights',
                'st_tour_highlights',
                'st_highlight',
            ] );
            
            $highlights = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $highlights = $decoded;
                            break;
                        }
                        
                        // Could be newline-separated.
                        $lines = explode( "\n", $value );
                        if ( count( $lines ) > 1 ) {
                            $highlights = array_map( 'trim', $lines );
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $highlights = $value;
                        break;
                    }
                }
            }
            
            return array_filter( array_map( 'sanitize_text_field', $highlights ) );
        } );
    }
    
    /**
     * Get included items.
     *
     * @param int $post_id Post ID.
     * @return array Array of included item strings.
     */
    public function get_includes( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'includes', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_includes_keys', [
                'st_include',
                'st_includes',
                'includes',
            ] );
            
            $includes = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $includes = $decoded;
                            break;
                        }
                        
                        $lines = explode( "\n", $value );
                        if ( count( $lines ) > 1 ) {
                            $includes = array_map( 'trim', $lines );
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $includes = $value;
                        break;
                    }
                }
            }
            
            return array_filter( array_map( 'sanitize_text_field', $includes ) );
        } );
    }
    
    /**
     * Get excluded items.
     *
     * @param int $post_id Post ID.
     * @return array Array of excluded item strings.
     */
    public function get_excludes( int $post_id ): array {
        return $this->get_cached_field( $post_id, 'excludes', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_excludes_keys', [
                'st_exclude',
                'st_excludes',
                'excludes',
            ] );
            
            $excludes = [];
            
            foreach ( $keys as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                
                if ( ! empty( $value ) ) {
                    if ( is_string( $value ) ) {
                        $decoded = json_decode( $value, true );
                        if ( is_array( $decoded ) ) {
                            $excludes = $decoded;
                            break;
                        }
                        
                        $lines = explode( "\n", $value );
                        if ( count( $lines ) > 1 ) {
                            $excludes = array_map( 'trim', $lines );
                            break;
                        }
                    } elseif ( is_array( $value ) ) {
                        $excludes = $value;
                        break;
                    }
                }
            }
            
            return array_filter( array_map( 'sanitize_text_field', $excludes ) );
        } );
    }
    
    /**
     * Get booking URL.
     *
     * @param int $post_id Post ID.
     * @return string|null Booking URL or null.
     */
    public function get_booking_url( int $post_id ): ?string {
        return $this->get_cached_field( $post_id, 'booking_url', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_booking_url_keys', [
                'st_booking_url',
                'booking_url',
                'st_book_link',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( ! empty( $value ) ) {
                $url = esc_url_raw( $value );
                if ( ! empty( $url ) ) {
                    return $url;
                }
            }
            
            // Fallback to post permalink.
            return get_permalink( $post_id );
        } );
    }
    
    /**
     * Get minimum guests.
     *
     * @param int $post_id Post ID.
     * @return int|null Minimum guests or null.
     */
    public function get_min_guests( int $post_id ): ?int {
        return $this->get_cached_field( $post_id, 'min_guests', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_min_guests_keys', [
                'st_min_group',
                'st_min_guests',
                'min_guests',
                'st_min_traveller',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                $min = intval( $value );
                if ( $min > 0 ) {
                    return $min;
                }
            }
            
            return null;
        } );
    }
    
    /**
     * Get maximum guests.
     *
     * @param int $post_id Post ID.
     * @return int|null Maximum guests or null.
     */
    public function get_max_guests( int $post_id ): ?int {
        return $this->get_cached_field( $post_id, 'max_guests', function() use ( $post_id ) {
            $keys = apply_filters( 'seo_autopilot_lite_traveler_max_guests_keys', [
                'st_max_group',
                'st_max_guests',
                'max_guests',
                'st_max_traveller',
            ] );
            
            $value = $this->get_first_meta_value( $post_id, $keys );
            
            if ( null !== $value ) {
                $max = intval( $value );
                if ( $max > 0 ) {
                    return $max;
                }
            }
            
            return null;
        } );
    }
    
    /**
     * Get a cached field value or compute it.
     *
     * @param int      $post_id Post ID.
     * @param string   $field_name Field name for cache key.
     * @param callable $callback Function to compute value if not cached.
     * @return mixed
     */
    private function get_cached_field( int $post_id, string $field_name, callable $callback ) {
        $cache_key = $post_id . '_' . $field_name;
        
        if ( isset( self::$field_cache[ $cache_key ] ) ) {
            return self::$field_cache[ $cache_key ];
        }
        
        $value = $callback();
        
        /**
         * Filter field value.
         *
         * @param mixed  $value Field value.
         * @param int    $post_id Post ID.
         * @param string $field_name Field name.
         */
        $value = apply_filters( 'seo_autopilot_lite_traveler_field_value', $value, $post_id, $field_name );
        
        self::$field_cache[ $cache_key ] = $value;
        
        return $value;
    }
    
    /**
     * Get first non-empty meta value from a list of keys.
     *
     * @param int   $post_id Post ID.
     * @param array $keys Array of meta keys to check.
     * @return mixed|null First non-empty value or null.
     */
    private function get_first_meta_value( int $post_id, array $keys ) {
        foreach ( $keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            
            if ( ! empty( $value ) ) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * Clear field cache for a post.
     *
     * @param int $post_id Post ID.
     */
    public function clear_cache( int $post_id ): void {
        foreach ( array_keys( self::$field_cache ) as $key ) {
            if ( strpos( $key, $post_id . '_' ) === 0 ) {
                unset( self::$field_cache[ $key ] );
            }
        }
    }
    
    /**
     * Clear all field cache.
     */
    public function clear_all_cache(): void {
        self::$field_cache = [];
    }
}
