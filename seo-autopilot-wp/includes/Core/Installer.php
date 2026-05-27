<?php
/**
 * Installer class - handles database table creation and migrations.
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
 * Installer class.
 */
class Installer
{
    /**
     * Database version number.
     * 
     * Increment this value when making schema changes.
     */
    private const DB_VERSION = '1.0.0';
    
    /**
     * Create all database tables.
     */
    public function createTables(): void
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        $this->createAuditsTable();
        $this->createIssuesTable();
        $this->createDraftsTable();
        $this->createApprovalsTable();
        $this->createBackupsTable();
        $this->createPublishHistoryTable();
        $this->createKeywordsTable();
        $this->createKeywordClustersTable();
        $this->createReportsTable();
        $this->createAutomationRulesTable();
        $this->createJobsTable();
        $this->createLogsTable();
        $this->createTokenUsageTable();
        $this->createGscConnectionsTable();
        $this->createGscMetricsTable();
        $this->createTravelerFieldsTable();
        $this->createTravelerAuditsTable();
        
        // Store database version.
        update_option('seo_autopilot_db_version', self::DB_VERSION);
    }
    
    /**
     * Create audits table.
     */
    private function createAuditsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_audits';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            post_title VARCHAR(255) NOT NULL DEFAULT '',
            overall_score INT(3) NOT NULL DEFAULT 0,
            technical_score INT(3) NOT NULL DEFAULT 0,
            onpage_score INT(3) NOT NULL DEFAULT 0,
            content_score INT(3) NOT NULL DEFAULT 0,
            links_score INT(3) NOT NULL DEFAULT 0,
            freshness_score INT(3) NOT NULL DEFAULT 0,
            schema_score INT(3) NOT NULL DEFAULT 0,
            local_seo_score INT(3) NOT NULL DEFAULT 0,
            issues_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
            critical_issues INT(10) UNSIGNED NOT NULL DEFAULT 0,
            high_issues INT(10) UNSIGNED NOT NULL DEFAULT 0,
            medium_issues INT(10) UNSIGNED NOT NULL DEFAULT 0,
            low_issues INT(10) UNSIGNED NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            metadata LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY post_type (post_type),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create issues table.
     */
    private function createIssuesTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_issues';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            audit_id BIGINT(20) UNSIGNED NOT NULL,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            issue_key VARCHAR(100) NOT NULL DEFAULT '',
            title VARCHAR(255) NOT NULL DEFAULT '',
            description TEXT,
            severity VARCHAR(20) NOT NULL DEFAULT 'medium',
            recommendation TEXT,
            current_value TEXT,
            suggested_value TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'open',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY audit_id (audit_id),
            KEY post_id (post_id),
            KEY issue_key (issue_key),
            KEY severity (severity),
            KEY status (status)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create drafts table.
     */
    private function createDraftsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_drafts';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            user_id BIGINT(20) UNSIGNED NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'optimization',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            original_title TEXT,
            original_content LONGTEXT,
            original_excerpt TEXT,
            suggested_title TEXT,
            suggested_content LONGTEXT,
            suggested_excerpt TEXT,
            seo_meta LONGTEXT,
            ai_provider VARCHAR(50),
            token_usage INT(10) UNSIGNED DEFAULT 0,
            cost_estimate DECIMAL(10,4) DEFAULT 0.0000,
            expected_score_improvement INT(3) DEFAULT 0,
            notes TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            published_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY type (type)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create approvals table.
     */
    private function createApprovalsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_approvals';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            draft_id BIGINT(20) UNSIGNED NOT NULL,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(20) NOT NULL DEFAULT 'pending',
            previous_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            new_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            edited_changes LONGTEXT,
            approval_notes TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY draft_id (draft_id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY action (action)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create backups table.
     */
    private function createBackupsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_backups';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            user_id BIGINT(20) UNSIGNED NOT NULL,
            backup_type VARCHAR(50) NOT NULL DEFAULT 'pre_publish',
            title TEXT,
            content LONGTEXT,
            excerpt TEXT,
            post_status VARCHAR(20) NOT NULL DEFAULT 'publish',
            seo_meta LONGTEXT,
            custom_fields LONGTEXT,
            traveler_fields LONGTEXT,
            schema_data LONGTEXT,
            is_restored TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY backup_type (backup_type),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create publish history table.
     */
    private function createPublishHistoryTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_publish_history';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            draft_id BIGINT(20) UNSIGNED NOT NULL,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL DEFAULT 'publish',
            previous_score INT(3) DEFAULT 0,
            new_score INT(3) DEFAULT 0,
            changes_summary LONGTEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'success',
            error_message TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY draft_id (draft_id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY action (action)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create keywords table.
     */
    private function createKeywordsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_keywords';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            keyword VARCHAR(255) NOT NULL DEFAULT '',
            intent VARCHAR(50) DEFAULT 'informational',
            cluster_id BIGINT(20) UNSIGNED DEFAULT NULL,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            source VARCHAR(50) NOT NULL DEFAULT 'manual',
            volume INT(10) UNSIGNED DEFAULT NULL,
            difficulty INT(3) DEFAULT NULL,
            priority INT(3) DEFAULT 50,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            metadata LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY keyword (keyword),
            KEY intent (intent),
            KEY cluster_id (cluster_id),
            KEY post_id (post_id),
            KEY status (status)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create keyword clusters table.
     */
    private function createKeywordClustersTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_keyword_clusters';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL DEFAULT '',
            parent_cluster_id BIGINT(20) UNSIGNED DEFAULT NULL,
            keyword_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
            primary_intent VARCHAR(50) DEFAULT 'informational',
            topic VARCHAR(255) DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY parent_cluster_id (parent_cluster_id),
            KEY primary_intent (primary_intent)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create reports table.
     */
    private function createReportsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_reports';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            report_type VARCHAR(50) NOT NULL DEFAULT 'audit',
            title VARCHAR(255) NOT NULL DEFAULT '',
            date_from DATE NOT NULL,
            date_to DATE NOT NULL,
            data LONGTEXT,
            format VARCHAR(20) NOT NULL DEFAULT 'html',
            file_path VARCHAR(500) DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY report_type (report_type),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create automation rules table.
     */
    private function createAutomationRulesTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_automation_rules';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL DEFAULT '',
            type VARCHAR(50) NOT NULL DEFAULT 'scheduled',
            frequency VARCHAR(50) NOT NULL DEFAULT 'weekly',
            post_types LONGTEXT,
            settings LONGTEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            last_run_at DATETIME DEFAULT NULL,
            next_run_at DATETIME DEFAULT NULL,
            run_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY status (status),
            KEY next_run_at (next_run_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create jobs table.
     */
    private function createJobsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_jobs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            rule_id BIGINT(20) UNSIGNED DEFAULT NULL,
            job_type VARCHAR(50) NOT NULL DEFAULT 'manual',
            status VARCHAR(20) NOT NULL DEFAULT 'queued',
            progress INT(3) NOT NULL DEFAULT 0,
            total_items INT(10) UNSIGNED NOT NULL DEFAULT 0,
            processed_items INT(10) UNSIGNED NOT NULL DEFAULT 0,
            failed_items INT(10) UNSIGNED NOT NULL DEFAULT 0,
            error_message TEXT,
            started_at DATETIME DEFAULT NULL,
            completed_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY rule_id (rule_id),
            KEY job_type (job_type),
            KEY status (status)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create logs table.
     */
    private function createLogsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            level VARCHAR(20) NOT NULL DEFAULT 'info',
            message TEXT NOT NULL,
            context LONGTEXT,
            user_id BIGINT(20) UNSIGNED DEFAULT NULL,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY level (level),
            KEY user_id (user_id),
            KEY post_id (post_id),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create token usage table.
     */
    private function createTokenUsageTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_token_usage';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            provider VARCHAR(50) NOT NULL DEFAULT '',
            model VARCHAR(100) NOT NULL DEFAULT '',
            prompt_tokens INT(10) UNSIGNED NOT NULL DEFAULT 0,
            completion_tokens INT(10) UNSIGNED NOT NULL DEFAULT 0,
            total_tokens INT(10) UNSIGNED NOT NULL DEFAULT 0,
            cost DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            purpose VARCHAR(100) DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY provider (provider),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create GSC connections table.
     */
    private function createGscConnectionsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_gsc_connections';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            property_url VARCHAR(500) NOT NULL DEFAULT '',
            property_type VARCHAR(50) NOT NULL DEFAULT 'domain',
            access_token TEXT,
            refresh_token TEXT,
            token_expires_at DATETIME DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            last_synced_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_type (property_type),
            KEY is_active (is_active)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create GSC metrics table.
     */
    private function createGscMetricsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_gsc_metrics';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            connection_id BIGINT(20) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            page_url VARCHAR(2000) NOT NULL DEFAULT '',
            query VARCHAR(2000) DEFAULT '',
            device VARCHAR(50) DEFAULT 'desktop',
            country VARCHAR(10) DEFAULT '',
            clicks INT(10) UNSIGNED NOT NULL DEFAULT 0,
            impressions INT(10) UNSIGNED NOT NULL DEFAULT 0,
            ctr DECIMAL(5,4) NOT NULL DEFAULT 0.0000,
            position DECIMAL(8,2) NOT NULL DEFAULT 0.00,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY connection_id (connection_id),
            KEY date (date),
            KEY page_url (page_url(255)),
            KEY query (query(255))
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create Traveler fields table.
     */
    private function createTravelerFieldsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_traveler_fields';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            field_name VARCHAR(255) NOT NULL DEFAULT '',
            field_type VARCHAR(50) NOT NULL DEFAULT 'text',
            field_category VARCHAR(50) NOT NULL DEFAULT 'seo_safe',
            is_protected TINYINT(1) NOT NULL DEFAULT 0,
            field_value LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY field_name (field_name),
            KEY field_category (field_category),
            KEY is_protected (is_protected)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
    
    /**
     * Create Traveler audits table.
     */
    private function createTravelerAuditsTable(): void
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'seo_autopilot_traveler_audits';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            cpt_type VARCHAR(50) NOT NULL DEFAULT '',
            overall_score INT(3) NOT NULL DEFAULT 0,
            protected_fields_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
            booking_fields_preserved TINYINT(1) NOT NULL DEFAULT 1,
            price_fields_preserved TINYINT(1) NOT NULL DEFAULT 1,
            availability_fields_preserved TINYINT(1) NOT NULL DEFAULT 1,
            schema_generated TINYINT(1) NOT NULL DEFAULT 0,
            issues LONGTEXT,
            recommendations LONGTEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY cpt_type (cpt_type),
            KEY status (status)
        ) {$charset_collate};";
        
        dbDelta($sql);
    }
}
