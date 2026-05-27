<?php
/**
 * Traveler Schema generator.
 * 
 * Generates comprehensive schema for Traveler theme content including
 * TouristTrip, Product, Offer, AggregateRating, Review, FAQPage, and more.
 */

namespace SeoAutopilotLite\Schema;

use SeoAutopilotLite\Core\Settings;
use SeoAutopilotLite\Traveler\TravelerFieldMapper;

class TravelerSchema {
    
    /**
     * Traveler field mapper instance.
     *
     * @var TravelerFieldMapper
     */
    private TravelerFieldMapper $field_mapper;
    
    public function __construct() {
        $this->field_mapper = new TravelerFieldMapper();
    }
    
    /**
     * Generate Traveler-related schema (Tour, Hotel, etc.).
     *
     * @param \WP_Post $post Post object.
     * @param string   $type Schema type.
     * @return array Schema array or empty if not applicable.
     */
    public function generate( \WP_Post $post, string $type ): array {
        $post_type = get_post_type( $post );
        
        switch ( $post_type ) {
            case 'st_tours':
                return $this->generate_tour_schema( $post );
            
            case 'st_activity':
                return $this->generate_activity_schema( $post );
            
            case 'st_hotel':
                return $this->generate_hotel_schema( $post );
            
            case 'st_rental':
                return $this->generate_rental_schema( $post );
            
            case 'st_cars':
                return $this->generate_car_schema( $post );
            
            default:
                return [];
        }
    }
    
    /**
     * Generate Tour schema with Product, Offer, Rating, Review, FAQ.
     *
     * @param \WP_Post $post Post object.
     * @return array Complete tour schema graph.
     */
    private function generate_tour_schema( \WP_Post $post ): array {
        $schema_graph = [];
        $post_id = $post->ID;
        
        // Get Traveler field data.
        $price = $this->field_mapper->get_price( $post_id );
        $regular_price = $this->field_mapper->get_regular_price( $post_id );
        $sale_price = $this->field_mapper->get_sale_price( $post_id );
        $currency = $this->field_mapper->get_currency( $post_id ) ?? 'USD';
        $duration = $this->field_mapper->get_duration( $post_id );
        $destination = $this->field_mapper->get_destination( $post_id );
        $location = $this->field_mapper->get_location( $post_id );
        $rating = $this->field_mapper->get_rating( $post_id );
        $review_count = $this->field_mapper->get_review_count( $post_id );
        $reviews = $this->field_mapper->get_reviews( $post_id );
        $faqs = $this->field_mapper->get_faqs( $post_id );
        $highlights = $this->field_mapper->get_highlights( $post_id );
        $includes = $this->field_mapper->get_includes( $post_id );
        $excludes = $this->field_mapper->get_excludes( $post_id );
        $images = $this->field_mapper->get_gallery_images( $post_id );
        $featured_image = $this->field_mapper->get_featured_or_gallery_image( $post_id );
        
        // Main TouristTrip schema.
        $tour_schema = [
            '@type' => 'TouristTrip',
            'name' => sanitize_text_field( $post->post_title ),
            'url' => get_permalink( $post_id ),
            'description' => $this->get_description( $post ),
        ];
        
        // Add image(s).
        if ( ! empty( $images ) ) {
            $tour_schema['image'] = array_map( fn( $img ) => [
                '@type' => 'ImageObject',
                'url' => esc_url( $img ),
            ], $images );
        } elseif ( $featured_image ) {
            $tour_schema['image'] = [
                '@type' => 'ImageObject',
                'url' => esc_url( $featured_image ),
            ];
        }
        
        // Add duration if available.
        if ( ! empty( $duration ) ) {
            $tour_schema['touristType'] = sanitize_text_field( $duration );
        }
        
        // Add destination/location.
        if ( ! empty( $destination ) || ! empty( $location ) ) {
            $place = [
                '@type' => 'Place',
                'name' => sanitize_text_field( $destination ?? $location ),
            ];
            
            $address = $this->field_mapper->get_address( $post_id );
            if ( ! empty( $address ) ) {
                $place['address'] = $address;
            }
            
            $tour_schema['itinerary'] = [
                '@type' => 'Place',
                'name' => sanitize_text_field( $destination ?? $location ),
            ];
        }
        
        // Add highlights as description enhancements.
        if ( ! empty( $highlights ) ) {
            $tour_schema['description'] .= ' Highlights: ' . implode( ', ', array_slice( $highlights, 0, 5 ) );
        }
        
        $schema_graph[] = $tour_schema;
        
        // Add Product schema if price exists.
        if ( null !== $price && $price > 0 ) {
            $product_schema = [
                '@type' => 'Product',
                'name' => sanitize_text_field( $post->post_title ),
                'description' => $this->get_description( $post ),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => floatval( $sale_price ?? $price ),
                    'priceCurrency' => strtoupper( $currency ),
                    'availability' => 'https://schema.org/InStock',
                    'url' => get_permalink( $post_id ),
                ],
            ];
            
            // Add regular price if sale price exists.
            if ( null !== $sale_price && null !== $regular_price && $regular_price > $sale_price ) {
                $product_schema['offers']['price'] = floatval( $regular_price );
                $product_schema['offers']['priceValidUntil'] = date( 'Y-12-31' );
            }
            
            // Add min/max guests.
            $min_guests = $this->field_mapper->get_min_guests( $post_id );
            $max_guests = $this->field_mapper->get_max_guests( $post_id );
            if ( null !== $min_guests ) {
                $product_schema['offers']['minQuantity'] = intval( $min_guests );
            }
            if ( null !== $max_guests ) {
                $product_schema['offers']['maxQuantity'] = intval( $max_guests );
            }
            
            $schema_graph[] = $product_schema;
        }
        
        // Add AggregateRating if rating and review count exist.
        if ( null !== $rating && null !== $review_count && $review_count > 0 ) {
            $schema_graph[] = [
                '@type' => 'AggregateRating',
                'ratingValue' => floatval( $rating ),
                'reviewCount' => intval( $review_count ),
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }
        
        // Add individual reviews if available.
        if ( ! empty( $reviews ) ) {
            foreach ( array_slice( $reviews, 0, 5 ) as $review ) {
                if ( ! empty( $review ) ) {
                    $review['@type'] = 'Review';
                    $schema_graph[] = $review;
                }
            }
        }
        
        // Add FAQPage schema if FAQs exist.
        if ( ! empty( $faqs ) ) {
            $faq_schema = [
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ];
            
            foreach ( $faqs as $faq ) {
                if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
                    $faq_schema['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => sanitize_text_field( $faq['question'] ),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => wp_kses_post( $faq['answer'] ),
                        ],
                    ];
                }
            }
            
            if ( ! empty( $faq_schema['mainEntity'] ) ) {
                $schema_graph[] = $faq_schema;
            }
        }
        
        /**
         * Filter the tour schema graph.
         *
         * @param array    $schema_graph Array of schema objects.
         * @param \WP_Post $post Post object.
         */
        return apply_filters( 'seo_autopilot_lite_schema_tourist_trip', $schema_graph, $post );
    }
    
    /**
     * Generate Activity schema.
     *
     * @param \WP_Post $post Post object.
     * @return array Activity schema graph.
     */
    private function generate_activity_schema( \WP_Post $post ): array {
        // Activities use similar schema to tours.
        return $this->generate_tour_schema( $post );
    }
    
    /**
     * Generate Hotel schema with Product, Offer, Rating.
     *
     * @param \WP_Post $post Post object.
     * @return array Hotel schema graph.
     */
    private function generate_hotel_schema( \WP_Post $post ): array {
        $schema_graph = [];
        $post_id = $post->ID;
        
        // Get Traveler field data.
        $price = $this->field_mapper->get_price( $post_id );
        $currency = $this->field_mapper->get_currency( $post_id ) ?? 'USD';
        $rating = $this->field_mapper->get_rating( $post_id );
        $review_count = $this->field_mapper->get_review_count( $post_id );
        $address = $this->field_mapper->get_address( $post_id );
        $featured_image = $this->field_mapper->get_featured_or_gallery_image( $post_id );
        
        // Main Hotel schema.
        $hotel_schema = [
            '@type' => 'Hotel',
            'name' => sanitize_text_field( $post->post_title ),
            'url' => get_permalink( $post_id ),
            'description' => $this->get_description( $post ),
        ];
        
        // Add address.
        if ( ! empty( $address ) ) {
            $hotel_schema['address'] = $address;
        }
        
        // Add image.
        if ( $featured_image ) {
            $hotel_schema['image'] = [
                '@type' => 'ImageObject',
                'url' => esc_url( $featured_image ),
            ];
        }
        
        // Add star rating from Traveler meta.
        $star_rating = get_post_meta( $post_id, 'star_rate', true );
        if ( ! empty( $star_rating ) ) {
            $hotel_schema['starRating'] = [
                '@type' => 'Rating',
                'ratingValue' => intval( $star_rating ),
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }
        
        $schema_graph[] = $hotel_schema;
        
        // Add Product/Offer schema if price exists.
        if ( null !== $price && $price > 0 ) {
            $schema_graph[] = [
                '@type' => 'Product',
                'name' => sanitize_text_field( $post->post_title ),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => floatval( $price ),
                    'priceCurrency' => strtoupper( $currency ),
                    'availability' => 'https://schema.org/InStock',
                    'url' => get_permalink( $post_id ),
                ],
            ];
        }
        
        // Add AggregateRating if available.
        if ( null !== $rating && null !== $review_count && $review_count > 0 ) {
            $schema_graph[] = [
                '@type' => 'AggregateRating',
                'ratingValue' => floatval( $rating ),
                'reviewCount' => intval( $review_count ),
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }
        
        /**
         * Filter the hotel schema graph.
         *
         * @param array    $schema_graph Array of schema objects.
         * @param \WP_Post $post Post object.
         */
        return apply_filters( 'seo_autopilot_lite_schema_hotel', $schema_graph, $post );
    }
    
    /**
     * Generate Rental schema.
     *
     * @param \WP_Post $post Post object.
     * @return array Rental schema graph.
     */
    private function generate_rental_schema( \WP_Post $post ): array {
        // Rentals use similar schema to hotels.
        return $this->generate_hotel_schema( $post );
    }
    
    /**
     * Generate Car schema.
     *
     * @param \WP_Post $post Post object.
     * @return array Car schema graph.
     */
    private function generate_car_schema( \WP_Post $post ): array {
        $schema_graph = [];
        $post_id = $post->ID;
        
        // Get Traveler field data.
        $price = $this->field_mapper->get_price( $post_id );
        $currency = $this->field_mapper->get_currency( $post_id ) ?? 'USD';
        
        // Main Product schema for car rental.
        $car_schema = [
            '@type' => 'Product',
            'name' => sanitize_text_field( $post->post_title ),
            'url' => get_permalink( $post_id ),
            'description' => $this->get_description( $post ),
        ];
        
        // Add offer if price exists.
        if ( null !== $price && $price > 0 ) {
            $car_schema['offers'] = [
                '@type' => 'Offer',
                'price' => floatval( $price ),
                'priceCurrency' => strtoupper( $currency ),
                'availability' => 'https://schema.org/InStock',
                'url' => get_permalink( $post_id ),
            ];
        }
        
        $schema_graph[] = $car_schema;
        
        /**
         * Filter the car schema graph.
         *
         * @param array    $schema_graph Array of schema objects.
         * @param \WP_Post $post Post object.
         */
        return apply_filters( 'seo_autopilot_lite_schema_car', $schema_graph, $post );
    }
    
    /**
     * Get description for schema.
     *
     * @param \WP_Post $post Post object.
     * @return string Cleaned description.
     */
    private function get_description( \WP_Post $post ): string {
        if ( has_excerpt( $post ) ) {
            return sanitize_textarea_field( get_the_excerpt( $post ) );
        }
        
        return wp_trim_words( sanitize_textarea_field( $post->post_content ), 50 );
    }
}
