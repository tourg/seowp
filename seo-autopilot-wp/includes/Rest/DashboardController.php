<?php
/**
 * DashboardController - REST API controller for dashboard data.
 *
 * @package SeoAutopilotWp\Rest
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Rest;

use WP_REST_Request;
use WP_REST_Response;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DashboardController class.
 */
class DashboardController extends RestController
{
    /**
     * Route base.
     *
     * @var string
     */
    protected string $route_base = 'dashboard';
    
    /**
     * Register routes.
     */
    public function register_routes(): void
    {
        // GET /dashboard
        register_rest_route(self::NAMESPACE, '/' . $this->route_base, [
            'methods' => 'GET',
            'callback' => [$this, 'getDashboard'],
            'permission_callback' => $this->getPermissionCallback('seo_autopilot_view_dashboard'),
        ]);
        
        // GET /dashboard/trends
        register_rest_route(self::NAMESPACE, '/' . $this->route_base . '/trends', [
            'methods' => 'GET',
            'callback' => [$this, 'getTrends'],
            'permission_callback' => $this->getPermissionCallback('seo_autopilot_view_dashboard'),
        ]);
        
        // GET /dashboard/issues
        register_rest_route(self::NAMESPACE, '/' . $this->route_base . '/issues', [
            'methods' => 'GET',
            'callback' => [$this, 'getIssues'],
            'permission_callback' => $this->getPermissionCallback('seo_autopilot_view_dashboard'),
        ]);
        
        // GET /dashboard/activity
        register_rest_route(self::NAMESPACE, '/' . $this->route_base . '/activity', [
            'methods' => 'GET',
            'callback' => [$this, 'getActivity'],
            'permission_callback' => $this->getPermissionCallback('seo_autopilot_view_dashboard'),
        ]);
    }
    
    /**
     * Get dashboard data.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function getDashboard(WP_REST_Request $request)
    {
        global $wpdb;
        
        // Try to get cached data.
        $cache_key = 'seo_autopilot_dashboard_stats';
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $this->successResponse($cached);
        }
        
        $audits_table = $wpdb->prefix . 'seo_autopilot_audits';
        $issues_table = $wpdb->prefix . 'seo_autopilot_issues';
        $drafts_table = $wpdb->prefix . 'seo_autopilot_drafts';
        
        // Get overall stats.
        $total_scanned = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$audits_table}");
        
        $avg_score = (int) $wpdb->get_var("SELECT COALESCE(ROUND(AVG(overall_score)), 0) FROM {$audits_table}");
        
        $open_issues = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$issues_table} WHERE status = 'open'");
        
        $critical_issues = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$issues_table} WHERE severity = 'critical' AND status = 'open'");
        
        $pending_approvals = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$drafts_table} WHERE status = 'pending'");
        
        $published_optimizations = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$drafts_table} WHERE status = 'published'");
        
        // Get issue distribution.
        $issue_distribution = $wpdb->get_results(
            "SELECT severity, COUNT(*) as count 
             FROM {$issues_table} 
             WHERE status = 'open' 
             GROUP BY severity",
            ARRAY_A
        );
        
        // Build response.
        $data = [
            'overall_score' => $avg_score,
            'total_scanned' => $total_scanned,
            'open_issues' => $open_issues,
            'critical_issues' => $critical_issues,
            'pending_approvals' => $pending_approvals,
            'published_optimizations' => $published_optimizations,
            'issue_distribution' => $issue_distribution ?: [],
            'last_updated' => current_time('mysql'),
        ];
        
        // Cache for 5 minutes.
        set_transient($cache_key, $data, 300);
        
        return $this->successResponse($data);
    }
    
    /**
     * Get SEO score trends.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function getTrends(WP_REST_Request $request)
    {
        global $wpdb;
        
        $audits_table = $wpdb->prefix . 'seo_autopilot_audits';
        
        $days = $request->get_param('days') ?? 30;
        $days = min((int) $days, 90);
        
        // Get daily average scores.
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE(created_at) as date, 
                        ROUND(AVG(overall_score)) as avg_score,
                        ROUND(AVG(technical_score)) as technical_score,
                        ROUND(AVG(onpage_score)) as onpage_score,
                        ROUND(AVG(content_score)) as content_score,
                        COUNT(*) as audited_count
                 FROM {$audits_table}
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
                 GROUP BY DATE(created_at)
                 ORDER BY date ASC",
                $days
            ),
            ARRAY_A
        );
        
        return $this->successResponse([
            'trends' => $results ?: [],
            'period_days' => $days,
        ]);
    }
    
    /**
     * Get issues summary.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function getIssues(WP_REST_Request $request)
    {
        global $wpdb;
        
        $issues_table = $wpdb->prefix . 'seo_autopilot_issues';
        
        $limit = min((int) ($request->get_param('limit') ?? 50), 200);
        $severity = $request->get_param('severity');
        $status = $request->get_param('status') ?? 'open';
        
        $where = ['1=1'];
        $params = [];
        
        if ($severity) {
            $where[] = 'severity = %s';
            $params[] = $severity;
        }
        
        if ($status) {
            $where[] = 'status = %s';
            $params[] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT i.*, p.post_title, p.post_type
                 FROM {$issues_table} i
                 LEFT JOIN {$wpdb->posts} p ON i.post_id = p.ID
                 WHERE {$where_clause}
                 ORDER BY 
                    CASE i.severity
                        WHEN 'critical' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                        ELSE 5
                    END ASC,
                    i.created_at DESC
                 LIMIT %d",
                array_merge($params, [$limit])
            ),
            ARRAY_A
        );
        
        return $this->successResponse([
            'issues' => $results ?: [],
            'total' => count($results),
        ]);
    }
    
    /**
     * Get recent activity.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function getActivity(WP_REST_Request $request)
    {
        global $wpdb;
        
        $logs_table = $wpdb->prefix . 'seo_autopilot_logs';
        
        $limit = min((int) ($request->get_param('limit') ?? 20), 100);
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT l.*, u.display_name as user_name
                 FROM {$logs_table} l
                 LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
                 WHERE l.level IN ('info', 'notice', 'warning', 'error')
                 ORDER BY l.created_at DESC
                 LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
        
        return $this->successResponse([
            'activity' => $results ?: [],
            'total' => count($results),
        ]);
    }
}
