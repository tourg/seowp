<?php
/**
 * RuleInterface - interface for SEO rules.
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
 * RuleInterface interface.
 */
interface RuleInterface
{
    /**
     * Check for SEO issues.
     *
     * @param int $post_id Post ID to check.
     * @param array<string, mixed> $context Scan context data.
     * @return array<array<string, mixed>> Array of issues found.
     */
    public function check(int $post_id, array $context): array;
    
    /**
     * Get rule category.
     *
     * @return string Category name (technical, onpage, content, links, freshness, schema, local_seo).
     */
    public function getCategory(): string;
    
    /**
     * Get rule name.
     *
     * @return string Rule name.
     */
    public function getName(): string;
}
