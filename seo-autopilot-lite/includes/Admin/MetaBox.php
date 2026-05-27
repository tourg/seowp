<?php
/**
 * SEO Meta Box for posts/pages.
 */

namespace SeoAutopilotLite\Admin;

use SeoAutopilotLite\Core\Settings;
use SeoAutopilotLite\SEO\Analyzer;

class MetaBox {
    
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_meta_box' ], 10, 2 );
        add_action( 'wp_ajax_seo_autopilot_lite_generate_ai', [ $this, 'ajax_generate_ai' ] );
    }
    
    /**
     * Add SEO meta box to supported post types.
     */
    public function add_meta_box(): void {
        $post_types = Settings::get_supported_post_types();
        
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'seo-autopilot-lite-metabox',
                __( 'SEO Autopilot', 'seo-autopilot-lite' ),
                [ $this, 'render_meta_box' ],
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Render meta box content.
     */
    public function render_meta_box( \WP_Post $post ): void {
        wp_nonce_field( 'seo_autopilot_lite_metabox', 'seo_autopilot_lite_nonce' );
        
        // Get existing values.
        $seo_title = get_post_meta( $post->ID, '_seo_autopilot_title', true );
        $meta_description = get_post_meta( $post->ID, '_seo_autopilot_description', true );
        $focus_keyword = get_post_meta( $post->ID, '_seo_autopilot_focus_keyword', true );
        $canonical = get_post_meta( $post->ID, '_seo_autopilot_canonical', true );
        $noindex = get_post_meta( $post->ID, '_seo_autopilot_noindex', true );
        $nofollow = get_post_meta( $post->ID, '_seo_autopilot_nofollow', true );
        $og_title = get_post_meta( $post->ID, '_seo_autopilot_og_title', true );
        $og_description = get_post_meta( $post->ID, '_seo_autopilot_og_description', true );
        $og_image = get_post_meta( $post->ID, '_seo_autopilot_og_image', true );
        $twitter_title = get_post_meta( $post->ID, '_seo_autopilot_twitter_title', true );
        $twitter_description = get_post_meta( $post->ID, '_seo_autopilot_twitter_description', true );
        $twitter_image = get_post_meta( $post->ID, '_seo_autopilot_twitter_image', true );
        $schema_type = get_post_meta( $post->ID, '_seo_autopilot_schema_type', true );
        $schema_json = get_post_meta( $post->ID, '_seo_autopilot_schema_json', true );
        $seo_score = get_post_meta( $post->ID, '_seo_autopilot_seo_score', true );
        
        // Run analysis if enabled.
        $analysis = [];
        if ( Settings::get_option( 'seo_analysis_enabled', true ) ) {
            $analyzer = new Analyzer();
            $analysis = $analyzer->analyze_post( $post );
        }
        
        $separator = Settings::get_option( 'title_separator', '-' );
        $site_name = Settings::get_option( 'site_name', get_bloginfo( 'name' ) );
        
        ?>
        <div class="seo-autopilot-lite-metabox">
            <!-- Tabs -->
            <div class="sap-tabs">
                <button type="button" class="sap-tab active" data-tab="seo"><?php esc_html_e( 'SEO', 'seo-autopilot-lite' ); ?></button>
                <button type="button" class="sap-tab" data-tab="social"><?php esc_html_e( 'Social', 'seo-autopilot-lite' ); ?></button>
                <button type="button" class="sap-tab" data-tab="schema"><?php esc_html_e( 'Schema', 'seo-autopilot-lite' ); ?></button>
                <?php if ( Settings::is_ai_enabled() ) : ?>
                    <button type="button" class="sap-tab" data-tab="ai"><?php esc_html_e( 'AI Tools', 'seo-autopilot-lite' ); ?></button>
                <?php endif; ?>
            </div>
            
            <!-- SEO Tab -->
            <div class="sap-tab-content active" id="sap-seo-tab">
                <!-- Google Preview -->
                <div class="sap-google-preview">
                    <h4><?php esc_html_e( 'Google Preview', 'seo-autopilot-lite' ); ?></h4>
                    <div class="sap-snippet">
                        <div class="sap-snippet-title">
                            <?php echo esc_html( ! empty( $seo_title ) ? $seo_title : ( $post->post_title . ' ' . $separator . ' ' . $site_name ) ); ?>
                        </div>
                        <div class="sap-snippet-url">
                            <?php echo esc_url( get_permalink( $post->ID ) ); ?>
                        </div>
                        <div class="sap-snippet-description">
                            <?php echo esc_html( ! empty( $meta_description ) ? $meta_description : wp_trim_words( $post->post_content, 30 ) ); ?>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Score -->
                <?php if ( ! empty( $seo_score ) || ! empty( $analysis ) ) : ?>
                <div class="sap-seo-score">
                    <h4><?php esc_html_e( 'SEO Score', 'seo-autopilot-lite' ); ?></h4>
                    <div class="sap-score-indicator sap-score-<?php echo esc_attr( $this->get_score_class( $seo_score ) ); ?>">
                        <span class="sap-score-value"><?php echo esc_html( $seo_score ?? 0 ); ?>/100</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Fields -->
                <table class="form-table">
                    <tr>
                        <th><label for="seo_autopilot_title"><?php esc_html_e( 'SEO Title', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="text" 
                                   id="seo_autopilot_title" 
                                   name="seo_autopilot_title" 
                                   value="<?php echo esc_attr( $seo_title ); ?>" 
                                   class="large-text" 
                                   placeholder="<?php esc_attr_e( 'Leave empty to use default template', 'seo-autopilot-lite' ); ?>" />
                            <p class="description"><?php esc_html_e( 'Recommended: 30-60 characters', 'seo-autopilot-lite' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_description"><?php esc_html_e( 'Meta Description', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <textarea id="seo_autopilot_description" 
                                      name="seo_autopilot_description" 
                                      rows="3" 
                                      class="large-text" 
                                      placeholder="<?php esc_attr_e( 'Leave empty to use default template', 'seo-autopilot-lite' ); ?>"><?php echo esc_textarea( $meta_description ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Recommended: 120-160 characters', 'seo-autopilot-lite' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_focus_keyword"><?php esc_html_e( 'Focus Keyword', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="text" 
                                   id="seo_autopilot_focus_keyword" 
                                   name="seo_autopilot_focus_keyword" 
                                   value="<?php echo esc_attr( $focus_keyword ); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_canonical"><?php esc_html_e( 'Canonical URL', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="url" 
                                   id="seo_autopilot_canonical" 
                                   name="seo_autopilot_canonical" 
                                   value="<?php echo esc_attr( $canonical ); ?>" 
                                   class="large-text" 
                                   placeholder="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Robots', 'seo-autopilot-lite' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       name="seo_autopilot_noindex" 
                                       value="1" 
                                       <?php checked( $noindex ); ?> />
                                <?php esc_html_e( 'No Index', 'seo-autopilot-lite' ); ?>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox" 
                                       name="seo_autopilot_nofollow" 
                                       value="1" 
                                       <?php checked( $nofollow ); ?> />
                                <?php esc_html_e( 'No Follow', 'seo-autopilot-lite' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <!-- Analysis Results -->
                <?php if ( ! empty( $analysis ) ) : ?>
                <div class="sap-analysis-results">
                    <h4><?php esc_html_e( 'SEO Analysis', 'seo-autopilot-lite' ); ?></h4>
                    <ul class="sap-analysis-list">
                        <?php foreach ( $analysis as $item ) : ?>
                            <li class="sap-analysis-item sap-status-<?php echo esc_attr( $item['status'] ); ?>">
                                <span class="dashicons dashicons-<?php echo esc_attr( $this->get_status_icon( $item['status'] ) ); ?>"></span>
                                <strong><?php echo esc_html( $item['title'] ); ?></strong>
                                <p><?php echo esc_html( $item['message'] ); ?></p>
                                <?php if ( ! empty( $item['recommendation'] ) ) : ?>
                                    <p class="sap-recommendation"><?php echo esc_html( $item['recommendation'] ); ?></p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Social Tab -->
            <div class="sap-tab-content" id="sap-social-tab">
                <h4><?php esc_html_e( 'Open Graph / Facebook', 'seo-autopilot-lite' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th><label for="seo_autopilot_og_title"><?php esc_html_e( 'OG Title', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="text" 
                                   id="seo_autopilot_og_title" 
                                   name="seo_autopilot_og_title" 
                                   value="<?php echo esc_attr( $og_title ); ?>" 
                                   class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_og_description"><?php esc_html_e( 'OG Description', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <textarea id="seo_autopilot_og_description" 
                                      name="seo_autopilot_og_description" 
                                      rows="2" 
                                      class="large-text"><?php echo esc_textarea( $og_description ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_og_image"><?php esc_html_e( 'OG Image URL', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="url" 
                                   id="seo_autopilot_og_image" 
                                   name="seo_autopilot_og_image" 
                                   value="<?php echo esc_attr( $og_image ); ?>" 
                                   class="large-text" />
                        </td>
                    </tr>
                </table>
                
                <h4><?php esc_html_e( 'Twitter Card', 'seo-autopilot-lite' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th><label for="seo_autopilot_twitter_title"><?php esc_html_e( 'Twitter Title', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="text" 
                                   id="seo_autopilot_twitter_title" 
                                   name="seo_autopilot_twitter_title" 
                                   value="<?php echo esc_attr( $twitter_title ); ?>" 
                                   class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_twitter_description"><?php esc_html_e( 'Twitter Description', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <textarea id="seo_autopilot_twitter_description" 
                                      name="seo_autopilot_twitter_description" 
                                      rows="2" 
                                      class="large-text"><?php echo esc_textarea( $twitter_description ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_twitter_image"><?php esc_html_e( 'Twitter Image URL', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <input type="url" 
                                   id="seo_autopilot_twitter_image" 
                                   name="seo_autopilot_twitter_image" 
                                   value="<?php echo esc_attr( $twitter_image ); ?>" 
                                   class="large-text" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Schema Tab -->
            <div class="sap-tab-content" id="sap-schema-tab">
                <table class="form-table">
                    <tr>
                        <th><label for="seo_autopilot_schema_type"><?php esc_html_e( 'Schema Type', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <select id="seo_autopilot_schema_type" name="seo_autopilot_schema_type" class="regular-text">
                                <option value=""><?php esc_html_e( 'None', 'seo-autopilot-lite' ); ?></option>
                                <option value="Article" <?php selected( $schema_type, 'Article' ); ?>><?php esc_html_e( 'Article', 'seo-autopilot-lite' ); ?></option>
                                <option value="BlogPosting" <?php selected( $schema_type, 'BlogPosting' ); ?>><?php esc_html_e( 'Blog Posting', 'seo-autopilot-lite' ); ?></option>
                                <option value="WebPage" <?php selected( $schema_type, 'WebPage' ); ?>><?php esc_html_e( 'Web Page', 'seo-autopilot-lite' ); ?></option>
                                <option value="FAQPage" <?php selected( $schema_type, 'FAQPage' ); ?>><?php esc_html_e( 'FAQ Page', 'seo-autopilot-lite' ); ?></option>
                                <option value="LocalBusiness" <?php selected( $schema_type, 'LocalBusiness' ); ?>><?php esc_html_e( 'Local Business', 'seo-autopilot-lite' ); ?></option>
                                <?php if ( Settings::get_option( 'traveler_enabled', false ) ) : ?>
                                    <option value="Tour" <?php selected( $schema_type, 'Tour' ); ?>><?php esc_html_e( 'Tour', 'seo-autopilot-lite' ); ?></option>
                                    <option value="TouristAttraction" <?php selected( $schema_type, 'TouristAttraction' ); ?>><?php esc_html_e( 'Tourist Attraction', 'seo-autopilot-lite' ); ?></option>
                                    <option value="Hotel" <?php selected( $schema_type, 'Hotel' ); ?>><?php esc_html_e( 'Hotel', 'seo-autopilot-lite' ); ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="seo_autopilot_schema_json"><?php esc_html_e( 'Custom Schema JSON-LD', 'seo-autopilot-lite' ); ?></label></th>
                        <td>
                            <textarea id="seo_autopilot_schema_json" 
                                      name="seo_autopilot_schema_json" 
                                      rows="8" 
                                      class="large-text code" 
                                      placeholder="<?php esc_attr_e( 'Paste custom JSON-LD schema here', 'seo-autopilot-lite' ); ?>"><?php echo esc_textarea( $schema_json ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Optional: Add custom JSON-LD schema. This will be added to the page head.', 'seo-autopilot-lite' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- AI Tab -->
            <?php if ( Settings::is_ai_enabled() ) : ?>
            <div class="sap-tab-content" id="sap-ai-tab">
                <div class="sap-ai-tools">
                    <p><?php esc_html_e( 'Use AI to generate SEO content:', 'seo-autopilot-lite' ); ?></p>
                    
                    <button type="button" class="button sap-ai-generate" data-action="title">
                        <?php esc_html_e( 'Generate SEO Title', 'seo-autopilot-lite' ); ?>
                    </button>
                    <button type="button" class="button sap-ai-generate" data-action="description">
                        <?php esc_html_e( 'Generate Meta Description', 'seo-autopilot-lite' ); ?>
                    </button>
                    <button type="button" class="button sap-ai-generate" data-action="faq">
                        <?php esc_html_e( 'Generate FAQ', 'seo-autopilot-lite' ); ?>
                    </button>
                    <button type="button" class="button sap-ai-generate" data-action="schema">
                        <?php esc_html_e( 'Generate Schema', 'seo-autopilot-lite' ); ?>
                    </button>
                    
                    <div class="sap-ai-loading" style="display:none;">
                        <span class="spinner is-active"></span>
                        <span><?php esc_html_e( 'Generating...', 'seo-autopilot-lite' ); ?></span>
                    </div>
                    
                    <div class="sap-ai-result"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Save meta box data.
     */
    public function save_meta_box( int $post_id, \WP_Post $post ): void {
        // Verify nonce.
        if ( ! isset( $_POST['seo_autopilot_lite_nonce'] ) || 
             ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seo_autopilot_lite_nonce'] ) ), 'seo_autopilot_lite_metabox' ) ) {
            return;
        }
        
        // Check autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        
        // Check permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        // Save fields.
        $fields = [
            'seo_autopilot_title' => '_seo_autopilot_title',
            'seo_autopilot_description' => '_seo_autopilot_description',
            'seo_autopilot_focus_keyword' => '_seo_autopilot_focus_keyword',
            'seo_autopilot_canonical' => '_seo_autopilot_canonical',
            'seo_autopilot_og_title' => '_seo_autopilot_og_title',
            'seo_autopilot_og_description' => '_seo_autopilot_og_description',
            'seo_autopilot_og_image' => '_seo_autopilot_og_image',
            'seo_autopilot_twitter_title' => '_seo_autopilot_twitter_title',
            'seo_autopilot_twitter_description' => '_seo_autopilot_twitter_description',
            'seo_autopilot_twitter_image' => '_seo_autopilot_twitter_image',
            'seo_autopilot_schema_type' => '_seo_autopilot_schema_type',
            'seo_autopilot_schema_json' => '_seo_autopilot_schema_json',
        ];
        
        foreach ( $fields as $post_key => $meta_key ) {
            if ( isset( $_POST[ $post_key ] ) ) {
                $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
                if ( '_seo_autopilot_schema_json' === $meta_key ) {
                    $value = wp_unslash( $_POST[ $post_key ] ); // Allow JSON.
                }
                update_post_meta( $post_id, $meta_key, $value );
            } else {
                delete_post_meta( $post_id, $meta_key );
            }
        }
        
        // Save checkboxes.
        $checkboxes = [
            'seo_autopilot_noindex' => '_seo_autopilot_noindex',
            'seo_autopilot_nofollow' => '_seo_autopilot_nofollow',
        ];
        
        foreach ( $checkboxes as $post_key => $meta_key ) {
            $value = isset( $_POST[ $post_key ] ) ? '1' : '';
            update_post_meta( $post_id, $meta_key, $value );
        }
        
        // Re-run analysis and save score.
        if ( Settings::get_option( 'seo_analysis_enabled', true ) ) {
            $analyzer = new Analyzer();
            $analysis = $analyzer->analyze_post( $post );
            $score = $analyzer->calculate_score( $analysis );
            update_post_meta( $post_id, '_seo_autopilot_seo_score', $score );
            update_post_meta( $post_id, '_seo_autopilot_analysis', wp_json_encode( $analysis ) );
        }
    }
    
    /**
     * AJAX generate AI content.
     */
    public function ajax_generate_ai(): void {
        check_ajax_referer( 'seo_autopilot_lite_metabox', 'nonce' );
        
        if ( ! current_user_can( 'edit_post' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'seo-autopilot-lite' ) ] );
        }
        
        $post_id = intval( $_POST['post_id'] ?? 0 );
        $action = sanitize_text_field( $_POST['action_type'] ?? '' );
        
        if ( ! $post_id || ! $action ) {
            wp_send_json_error( [ 'message' => __( 'Invalid request.', 'seo-autopilot-lite' ) ] );
        }
        
        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error( [ 'message' => __( 'Post not found.', 'seo-autopilot-lite' ) ] );
        }
        
        // Generate based on action.
        // In production, call actual AI provider.
        $result = $this->generate_ai_content( $post, $action );
        
        if ( $result ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( [ 'message' => __( 'Failed to generate content.', 'seo-autopilot-lite' ) ] );
        }
    }
    
    /**
     * Generate AI content (placeholder).
     */
    private function generate_ai_content( \WP_Post $post, string $action ): ?array {
        // In production, integrate with AI providers.
        // This is a placeholder implementation.
        
        switch ( $action ) {
            case 'title':
                return [ 'title' => $post->post_title . ' - Best Guide | ' . get_bloginfo( 'name' ) ];
            
            case 'description':
                $excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 30 );
                return [ 'description' => $excerpt ];
            
            case 'faq':
                return [ 
                    'schema' => json_encode( [
                        '@context' => 'https://schema.org',
                        '@type' => 'FAQPage',
                        'mainEntity' => []
                    ], JSON_PRETTY_PRINT )
                ];
            
            case 'schema':
                return [
                    'schema' => json_encode( [
                        '@context' => 'https://schema.org',
                        '@type' => 'Article',
                        'headline' => $post->post_title,
                        'datePublished' => $post->post_date,
                        'dateModified' => $post->post_modified,
                        'author' => [
                            '@type' => 'Person',
                            'name' => get_the_author_meta( 'display_name', $post->post_author )
                        ]
                    ], JSON_PRETTY_PRINT )
                ];
            
            default:
                return null;
        }
    }
    
    /**
     * Get score class for styling.
     */
    private function get_score_class( int $score ): string {
        if ( $score >= 80 ) {
            return 'good';
        } elseif ( $score >= 60 ) {
            return 'ok';
        } elseif ( $score >= 40 ) {
            return 'warning';
        } else {
            return 'bad';
        }
    }
    
    /**
     * Get status icon.
     */
    private function get_status_icon( string $status ): string {
        $icons = [
            'good' => 'yes-alt',
            'ok' => 'yes',
            'warning' => 'warning',
            'bad' => 'dismiss',
        ];
        return $icons[ $status ] ?? 'info';
    }
}
