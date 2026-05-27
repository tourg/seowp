<?php
/**
 * MetaTitleRule - checks meta title SEO issues.
 *
 * @package SeoAutopilotWp\SEO\Rules
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\SEO\Rules;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MetaTitleRule class.
 */
class MetaTitleRule implements RuleInterface
{
    /**
     * Default minimum length.
     */
    private const MIN_LENGTH = 30;
    
    /**
     * Default maximum length.
     */
    private const MAX_LENGTH = 60;
    
    /**
     * Check for meta title issues.
     *
     * @param int $post_id Post ID to check.
     * @param array<string, mixed> $context Scan context data.
     * @return array<array<string, mixed>> Array of issues found.
     */
    public function check(int $post_id, array $context): array
    {
        $issues = [];
        
        $seo_meta = $context['seo_meta'] ?? [];
        $title = $context['title'] ?? '';
        $seo_title = $seo_meta['title'] ?? $title;
        
        // Check if title is missing.
        if (empty($seo_title)) {
            $issues[] = [
                'issue_key' => 'missing_meta_title',
                'title' => __('Missing Meta Title', 'seo-autopilot-wp'),
                'description' => __('The page does not have a meta title set.', 'seo-autopilot-wp'),
                'severity' => 'high',
                'recommendation' => __('Add a descriptive meta title that includes your target keyword.', 'seo-autopilot-wp'),
                'current_value' => '',
                'suggested_value' => '',
            ];
        } else {
            $length = mb_strlen($seo_title);
            
            // Check if too short.
            if ($length < self::MIN_LENGTH) {
                $issues[] = [
                    'issue_key' => 'meta_title_too_short',
                    'title' => __('Meta Title Too Short', 'seo-autopilot-wp'),
                    'description' => sprintf(
                        /* translators: %d: current length, %d: minimum recommended length */
                        __('Your meta title is %d characters long. It should be at least %d characters.', 'seo-autopilot-wp'),
                        $length,
                        self::MIN_LENGTH
                    ),
                    'severity' => 'medium',
                    'recommendation' => __('Expand your title to include more relevant keywords and information.', 'seo-autopilot-wp'),
                    'current_value' => $seo_title,
                    'suggested_value' => '',
                ];
            }
            
            // Check if too long.
            if ($length > self::MAX_LENGTH) {
                $issues[] = [
                    'issue_key' => 'meta_title_too_long',
                    'title' => __('Meta Title Too Long', 'seo-autopilot-wp'),
                    'description' => sprintf(
                        /* translators: %d: current length, %d: maximum recommended length */
                        __('Your meta title is %d characters long. It should be no more than %d characters.', 'seo-autopilot-wp'),
                        $length,
                        self::MAX_LENGTH
                    ),
                    'severity' => 'medium',
                    'recommendation' => __('Shorten your title to ensure it displays properly in search results.', 'seo-autopilot-wp'),
                    'current_value' => $seo_title,
                    'suggested_value' => '',
                ];
            }
        }
        
        return $issues;
    }
    
    /**
     * Get rule category.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return 'onpage';
    }
    
    /**
     * Get rule name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __('Meta Title Rule', 'seo-autopilot-wp');
    }
}
