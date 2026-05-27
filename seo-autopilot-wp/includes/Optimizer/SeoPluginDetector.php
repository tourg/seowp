<?php
/**
 * SeoPluginDetector - detects installed SEO plugins.
 *
 * @package SeoAutopilotWp\Optimizer
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Optimizer;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SeoPluginDetector class.
 */
class SeoPluginDetector
{
    /**
     * Known SEO plugins.
     *
     * @var array<string, array<string, string>>
     */
    private const SEO_PLUGINS = [
        'yoast' => [
            'name' => 'Yoast SEO',
            'class' => 'WPSEO_Frontend',
            'file' => 'wordpress-seo/wp-seo.php',
            'meta_prefix' => '_yoast_wpseo_',
        ],
        'rankmath' => [
            'name' => 'Rank Math',
            'class' => 'RankMath',
            'file' => 'seo-by-rank-math/rank-math.php',
            'meta_prefix' => 'rank_math_',
        ],
        'aioseo' => [
            'name' => 'All in One SEO',
            'class' => 'AIOSEO',
            'file' => 'all-in-one-seo-pack/aioseopack.php',
            'meta_prefix' => '_aioseo_',
        ],
    ];
    
    /**
     * Detect active SEO plugin.
     *
     * @return string|false Plugin slug if detected, false otherwise.
     */
    public function detect()
    {
        // Check by class existence (most reliable).
        foreach (self::SEO_PLUGINS as $slug => $plugin) {
            if (class_exists($plugin['class'])) {
                return $slug;
            }
        }
        
        // Check by active plugins list.
        $active_plugins = get_option('active_plugins', []);
        
        foreach (self::SEO_PLUGINS as $slug => $plugin) {
            if (in_array($plugin['file'], $active_plugins, true)) {
                return $slug;
            }
        }
        
        // Check for network-activated plugins (multisite).
        if (is_multisite()) {
            $network_plugins = get_site_option('active_sitewide_plugins', []);
            
            foreach (self::SEO_PLUGINS as $slug => $plugin) {
                if (isset($network_plugins[$plugin['file']])) {
                    return $slug;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get detected plugin info.
     *
     * @return array<string, mixed>|null Plugin info or null if not detected.
     */
    public function getDetectedInfo(): ?array
    {
        $slug = $this->detect();
        
        if ($slug === false) {
            return null;
        }
        
        $plugin = self::SEO_PLUGINS[$slug];
        
        return [
            'slug' => $slug,
            'name' => $plugin['name'],
            'file' => $plugin['file'],
            'meta_prefix' => $plugin['meta_prefix'],
            'is_active' => true,
        ];
    }
    
    /**
     * Check if a specific SEO plugin is active.
     *
     * @param string $slug Plugin slug (yoast, rankmath, aioseo).
     * @return bool True if plugin is active.
     */
    public function isPluginActive(string $slug): bool
    {
        if (!isset(self::SEO_PLUGINS[$slug])) {
            return false;
        }
        
        $plugin = self::SEO_PLUGINS[$slug];
        
        // Check by class.
        if (class_exists($plugin['class'])) {
            return true;
        }
        
        // Check by active plugins.
        $active_plugins = get_option('active_plugins', []);
        return in_array($plugin['file'], $active_plugins, true);
    }
    
    /**
     * Get all known SEO plugins.
     *
     * @return array<string, array<string, string>>
     */
    public static function getKnownPlugins(): array
    {
        return self::SEO_PLUGINS;
    }
    
    /**
     * Get meta prefix for a plugin.
     *
     * @param string $slug Plugin slug.
     * @return string|null Meta prefix or null if plugin not found.
     */
    public function getMetaPrefix(string $slug): ?string
    {
        if (!isset(self::SEO_PLUGINS[$slug])) {
            return null;
        }
        
        return self::SEO_PLUGINS[$slug]['meta_prefix'];
    }
}
