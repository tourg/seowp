<?php
/**
 * SeoScanner class - scans content for SEO issues.
 *
 * @package SeoAutopilotWp\SEO
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\SEO;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SeoScanner class.
 */
class SeoScanner
{
    /**
     * SEO rules instances.
     *
     * @var array<Rules\RuleInterface>
     */
    private array $rules = [];
    
    /**
     * Constructor - initialize rules.
     */
    public function __construct()
    {
        $this->initializeRules();
    }
    
    /**
     * Initialize SEO rules.
     */
    private function initializeRules(): void
    {
        $this->rules = [
            new Rules\MetaTitleRule(),
            new Rules\MetaDescriptionRule(),
            new Rules\CanonicalRule(),
            new Rules\ImageAltRule(),
            new Rules\ContentQualityRule(),
            new Rules\DuplicateMetaRule(),
            new Rules\ReadabilityRule(),
            new Rules\LocalSeoRule(),
            new Rules\InternalLinksRule(),
            new Rules\SitemapRule(),
            new Rules\RobotsTxtRule(),
        ];
    }
    
    /**
     * Scan a post for SEO issues.
     *
     * @param int $post_id Post ID to scan.
     * @return array<string, mixed> Scan results.
     */
    public function scan(int $post_id): array
    {
        $post = get_post($post_id);
        
        if (!$post) {
            return [
                'success' => false,
                'error' => 'Post not found',
            ];
        }
        
        // Get post content and metadata.
        $content = $post->post_content;
        $title = $post->post_title;
        $excerpt = $post->post_excerpt;
        
        // Get SEO meta (from SEO plugin or our own).
        $seo_meta = $this->getSeoMeta($post_id);
        
        // Run all rules.
        $issues = [];
        $scores = [
            'technical' => 100,
            'onpage' => 100,
            'content' => 100,
            'links' => 100,
            'freshness' => 100,
            'schema' => 100,
            'local_seo' => 100,
        ];
        
        foreach ($this->rules as $rule) {
            $rule_issues = $rule->check($post_id, [
                'post' => $post,
                'content' => $content,
                'title' => $title,
                'excerpt' => $excerpt,
                'seo_meta' => $seo_meta,
            ]);
            
            foreach ($rule_issues as $issue) {
                $issues[] = $issue;
                
                // Deduct score based on severity.
                $category = $rule->getCategory();
                $severity_penalty = $this->getSeverityPenalty($issue['severity']);
                $scores[$category] = max(0, $scores[$category] - $severity_penalty);
            }
        }
        
        // Calculate overall score (weighted average).
        $overall_score = $this->calculateOverallScore($scores);
        
        return [
            'success' => true,
            'post_id' => $post_id,
            'post_type' => $post->post_type,
            'post_title' => $title,
            'overall_score' => $overall_score,
            'scores' => $scores,
            'issues' => $issues,
            'issues_count' => count($issues),
            'scanned_at' => current_time('mysql'),
        ];
    }
    
    /**
     * Get SEO meta for a post.
     *
     * @param int $post_id Post ID.
     * @return array<string, string|null>
     */
    private function getSeoMeta(int $post_id): array
    {
        $detector = new Optimizer\SeoPluginDetector();
        $seo_plugin = $detector->detect();
        
        $meta = [
            'title' => null,
            'description' => null,
            'focus_keyword' => null,
            'canonical' => null,
        ];
        
        if ($seo_plugin === 'yoast') {
            $meta['title'] = get_post_meta($post_id, '_yoast_wpseo_title', true) ?: null;
            $meta['description'] = get_post_meta($post_id, '_yoast_wpseo_metadesc', true) ?: null;
            $meta['focus_keyword'] = get_post_meta($post_id, '_yoast_wpseo_focuskw', true) ?: null;
            $meta['canonical'] = get_post_meta($post_id, '_yoast_wpseo_canonical', true) ?: null;
        } elseif ($seo_plugin === 'rankmath') {
            $meta['title'] = get_post_meta($post_id, 'rank_math_title', true) ?: null;
            $meta['description'] = get_post_meta($post_id, 'rank_math_description', true) ?: null;
            $meta['focus_keyword'] = get_post_meta($post_id, 'rank_math_focus_keyword', true) ?: null;
            $meta['canonical'] = get_post_meta($post_id, 'rank_math_canonical_url', true) ?: null;
        } elseif ($seo_plugin === 'aioseo') {
            $meta['title'] = get_post_meta($post_id, '_aioseo_title', true) ?: null;
            $meta['description'] = get_post_meta($post_id, '_aioseo_description', true) ?: null;
        } else {
            // Use our own meta.
            $meta['title'] = get_post_meta($post_id, '_seo_autopilot_title', true) ?: null;
            $meta['description'] = get_post_meta($post_id, '_seo_autopilot_description', true) ?: null;
        }
        
        return $meta;
    }
    
    /**
     * Get penalty points for issue severity.
     *
     * @param string $severity Issue severity.
     * @return int Penalty points.
     */
    private function getSeverityPenalty(string $severity): int
    {
        $penalties = [
            'critical' => 25,
            'high' => 15,
            'medium' => 8,
            'low' => 3,
            'info' => 0,
        ];
        
        return $penalties[$severity] ?? 0;
    }
    
    /**
     * Calculate overall SEO score.
     *
     * @param array<string, int> $scores Category scores.
     * @return int Overall score (0-100).
     */
    private function calculateOverallScore(array $scores): int
    {
        $weights = [
            'technical' => 0.20,
            'onpage' => 0.20,
            'content' => 0.25,
            'links' => 0.15,
            'freshness' => 0.10,
            'schema' => 0.05,
            'local_seo' => 0.05,
        ];
        
        $weighted_sum = 0;
        
        foreach ($scores as $category => $score) {
            $weight = $weights[$category] ?? 0;
            $weighted_sum += $score * $weight;
        }
        
        return (int) round($weighted_sum);
    }
    
    /**
     * Bulk scan multiple posts.
     *
     * @param array<int> $post_ids Post IDs to scan.
     * @param array<string, mixed> $options Scan options.
     * @return array<string, mixed> Bulk scan results.
     */
    public function bulkScan(array $post_ids, array $options = []): array
    {
        $results = [];
        $total = count($post_ids);
        $processed = 0;
        
        foreach ($post_ids as $post_id) {
            $results[$post_id] = $this->scan($post_id);
            $processed++;
            
            // Check for timeout (process in batches if needed).
            if ($processed % 10 === 0) {
                // Allow WordPress to breathe.
                usleep(10000); // 10ms
            }
        }
        
        return [
            'success' => true,
            'total' => $total,
            'processed' => $processed,
            'results' => $results,
        ];
    }
    
    /**
     * Get all available rules.
     *
     * @return array<Rules\RuleInterface>
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
