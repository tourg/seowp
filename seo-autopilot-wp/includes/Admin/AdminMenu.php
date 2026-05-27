<?php
/**
 * AdminMenu class - registers admin menu pages.
 *
 * @package SeoAutopilotWp\Admin
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Admin;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AdminMenu class.
 */
class AdminMenu
{
    /**
     * Menu slug.
     *
     * @var string
     */
    private const MENU_SLUG = 'seo-autopilot';
    
    /**
     * Registered page hooks.
     *
     * @var array<string, string>
     */
    private array $page_hooks = [];
    
    /**
     * Register admin menus.
     */
    public function register(): void
    {
        // Main menu.
        $this->page_hooks['dashboard'] = add_menu_page(
            __('SEO Autopilot Dashboard', 'seo-autopilot-wp'),
            __('SEO Autopilot', 'seo-autopilot-wp'),
            'seo_autopilot_view_dashboard',
            self::MENU_SLUG,
            [$this, 'renderDashboard'],
            'dashicons-chart-line',
            80
        );
        
        // Dashboard submenu.
        $this->page_hooks['dashboard_sub'] = add_submenu_page(
            self::MENU_SLUG,
            __('Dashboard', 'seo-autopilot-wp'),
            __('Dashboard', 'seo-autopilot-wp'),
            'seo_autopilot_view_dashboard',
            self::MENU_SLUG,
            [$this, 'renderDashboard']
        );
        
        // SEO Audit.
        $this->page_hooks['audit'] = add_submenu_page(
            self::MENU_SLUG,
            __('SEO Audit', 'seo-autopilot-wp'),
            __('SEO Audit', 'seo-autopilot-wp'),
            'seo_autopilot_run_audits',
            self::MENU_SLUG . '-audit',
            [$this, 'renderAudit']
        );
        
        // Content Optimizer.
        $this->page_hooks['optimizer'] = add_submenu_page(
            self::MENU_SLUG,
            __('Content Optimizer', 'seo-autopilot-wp'),
            __('Content Optimizer', 'seo-autopilot-wp'),
            'seo_autopilot_generate_ai',
            self::MENU_SLUG . '-optimizer',
            [$this, 'renderOptimizer']
        );
        
        // AI Generator.
        $this->page_hooks['generator'] = add_submenu_page(
            self::MENU_SLUG,
            __('AI Generator', 'seo-autopilot-wp'),
            __('AI Generator', 'seo-autopilot-wp'),
            'seo_autopilot_generate_ai',
            self::MENU_SLUG . '-generator',
            [$this, 'renderGenerator']
        );
        
        // Approvals.
        $this->page_hooks['approvals'] = add_submenu_page(
            self::MENU_SLUG,
            __('Approvals', 'seo-autopilot-wp'),
            __('Approvals', 'seo-autopilot-wp'),
            'seo_autopilot_approve_changes',
            self::MENU_SLUG . '-approvals',
            [$this, 'renderApprovals']
        );
        
        // Traveler SEO.
        $this->page_hooks['traveler'] = add_submenu_page(
            self::MENU_SLUG,
            __('Traveler SEO', 'seo-autopilot-wp'),
            __('Traveler SEO', 'seo-autopilot-wp'),
            'seo_autopilot_view_dashboard',
            self::MENU_SLUG . '-traveler',
            [$this, 'renderTraveler']
        );
        
        // Keywords.
        $this->page_hooks['keywords'] = add_submenu_page(
            self::MENU_SLUG,
            __('Keywords', 'seo-autopilot-wp'),
            __('Keywords', 'seo-autopilot-wp'),
            'seo_autopilot_view_dashboard',
            self::MENU_SLUG . '-keywords',
            [$this, 'renderKeywords']
        );
        
        // Schema.
        $this->page_hooks['schema'] = add_submenu_page(
            self::MENU_SLUG,
            __('Schema', 'seo-autopilot-wp'),
            __('Schema', 'seo-autopilot-wp'),
            'seo_autopilot_view_dashboard',
            self::MENU_SLUG . '-schema',
            [$this, 'renderSchema']
        );
        
        // Google Search Console.
        $this->page_hooks['gsc'] = add_submenu_page(
            self::MENU_SLUG,
            __('Google Search Console', 'seo-autopilot-wp'),
            __('Google Search Console', 'seo-autopilot-wp'),
            'seo_autopilot_manage_settings',
            self::MENU_SLUG . '-gsc',
            [$this, 'renderGsc']
        );
        
        // Reports.
        $this->page_hooks['reports'] = add_submenu_page(
            self::MENU_SLUG,
            __('Reports', 'seo-autopilot-wp'),
            __('Reports', 'seo-autopilot-wp'),
            'seo_autopilot_view_reports',
            self::MENU_SLUG . '-reports',
            [$this, 'renderReports']
        );
        
        // Automation.
        $this->page_hooks['automation'] = add_submenu_page(
            self::MENU_SLUG,
            __('Automation', 'seo-autopilot-wp'),
            __('Automation', 'seo-autopilot-wp'),
            'seo_autopilot_manage_settings',
            self::MENU_SLUG . '-automation',
            [$this, 'renderAutomation']
        );
        
        // Settings.
        $this->page_hooks['settings'] = add_submenu_page(
            self::MENU_SLUG,
            __('Settings', 'seo-autopilot-wp'),
            __('Settings', 'seo-autopilot-wp'),
            'seo_autopilot_manage_settings',
            self::MENU_SLUG . '-settings',
            [$this, 'renderSettings']
        );
        
        // Logs.
        $this->page_hooks['logs'] = add_submenu_page(
            self::MENU_SLUG,
            __('Logs', 'seo-autopilot-wp'),
            __('Logs', 'seo-autopilot-wp'),
            'seo_autopilot_manage_settings',
            self::MENU_SLUG . '-logs',
            [$this, 'renderLogs']
        );
    }
    
    /**
     * Render dashboard page.
     */
    public function renderDashboard(): void
    {
        $this->renderApp('dashboard');
    }
    
    /**
     * Render audit page.
     */
    public function renderAudit(): void
    {
        $this->renderApp('audit');
    }
    
    /**
     * Render optimizer page.
     */
    public function renderOptimizer(): void
    {
        $this->renderApp('optimizer');
    }
    
    /**
     * Render generator page.
     */
    public function renderGenerator(): void
    {
        $this->renderApp('generator');
    }
    
    /**
     * Render approvals page.
     */
    public function renderApprovals(): void
    {
        $this->renderApp('approvals');
    }
    
    /**
     * Render traveler page.
     */
    public function renderTraveler(): void
    {
        $this->renderApp('traveler');
    }
    
    /**
     * Render keywords page.
     */
    public function renderKeywords(): void
    {
        $this->renderApp('keywords');
    }
    
    /**
     * Render schema page.
     */
    public function renderSchema(): void
    {
        $this->renderApp('schema');
    }
    
    /**
     * Render GSC page.
     */
    public function renderGsc(): void
    {
        $this->renderApp('gsc');
    }
    
    /**
     * Render reports page.
     */
    public function renderReports(): void
    {
        $this->renderApp('reports');
    }
    
    /**
     * Render automation page.
     */
    public function renderAutomation(): void
    {
        $this->renderApp('automation');
    }
    
    /**
     * Render settings page.
     */
    public function renderSettings(): void
    {
        $this->renderApp('settings');
    }
    
    /**
     * Render logs page.
     */
    public function renderLogs(): void
    {
        $this->renderApp('logs');
    }
    
    /**
     * Render React app container.
     *
     * @param string $page Page identifier.
     */
    private function renderApp(string $page): void
    {
        echo '<div id="seo-autopilot-app" data-page="' . esc_attr($page) . '"></div>';
    }
    
    /**
     * Get registered page hooks.
     *
     * @return array<string, string>
     */
    public function getPageHooks(): array
    {
        return $this->page_hooks;
    }
}
