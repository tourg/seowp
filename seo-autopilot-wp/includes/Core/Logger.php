<?php
/**
 * Logger class - handles plugin logging.
 *
 * @package SeoAutopilotWp\Core
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Core;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Logger class.
 */
class Logger
{
    /**
     * Log levels.
     *
     * @var array<string, string>
     */
    private const LEVELS = [
        'debug' => 'DEBUG',
        'info' => 'INFO',
        'notice' => 'NOTICE',
        'warning' => 'WARNING',
        'error' => 'ERROR',
        'critical' => 'CRITICAL',
    ];
    
    /**
     * Log a message.
     *
     * @param string $level Log level.
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function log(string $level, string $message, array $context = [])
    {
        global $wpdb;
        
        // Validate log level.
        if (!isset(self::LEVELS[$level])) {
            $level = 'info';
        }
        
        // Get current user ID.
        $user_id = get_current_user_id();
        
        // Extract post_id from context if available.
        $post_id = $context['post_id'] ?? null;
        
        // Prepare context for storage.
        $context_json = wp_json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($context_json === false) {
            $context_json = '{}';
        }
        
        // Insert log entry.
        $table_name = $wpdb->prefix . 'seo_autopilot_logs';
        
        return $wpdb->insert(
            $table_name,
            [
                'level' => $level,
                'message' => $message,
                'context' => $context_json,
                'user_id' => $user_id ?: null,
                'post_id' => $post_id,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%d', '%d', '%s']
        );
    }
    
    /**
     * Log a debug message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function debug(string $message, array $context = [])
    {
        return $this->log('debug', $message, $context);
    }
    
    /**
     * Log an info message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function info(string $message, array $context = [])
    {
        return $this->log('info', $message, $context);
    }
    
    /**
     * Log a notice message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function notice(string $message, array $context = [])
    {
        return $this->log('notice', $message, $context);
    }
    
    /**
     * Log a warning message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function warning(string $message, array $context = [])
    {
        return $this->log('warning', $message, $context);
    }
    
    /**
     * Log an error message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function error(string $message, array $context = [])
    {
        return $this->log('error', $message, $context);
    }
    
    /**
     * Log a critical message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false Database insert ID or false on failure.
     */
    public function critical(string $message, array $context = [])
    {
        return $this->log('critical', $message, $context);
    }
    
    /**
     * Get recent logs.
     *
     * @param int $limit Maximum number of logs to retrieve.
     * @param string|null $level Filter by log level.
     * @param int|null $post_id Filter by post ID.
     * @return array<string, mixed>[]
     */
    public function getRecent(int $limit = 100, ?string $level = null, ?int $post_id = null): array
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_logs';
        
        $where = ['1=1'];
        $params = [];
        $types = [];
        
        if ($level !== null) {
            $where[] = 'level = %s';
            $params[] = $level;
            $types[] = '%s';
        }
        
        if ($post_id !== null) {
            $where[] = 'post_id = %d';
            $params[] = $post_id;
            $types[] = '%d';
        }
        
        $where_clause = implode(' AND ', $where);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d",
                array_merge($params, [$limit])
            ),
            ARRAY_A
        );
        
        return is_array($results) ? $results : [];
    }
    
    /**
     * Clear old logs.
     *
     * @param int $days_retention Number of days to retain logs.
     * @return int|false Number of rows deleted or false on failure.
     */
    public function clearOld(int $days_retention = 90)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_logs';
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days_retention} days"));
        
        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_name} WHERE created_at < %s",
                $cutoff_date
            )
        );
    }
}
