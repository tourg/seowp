<?php
/**
 * Deactivator class - runs on plugin deactivation.
 */

namespace SeoAutopilotLite\Core;

class Deactivator {
    
    public function deactivate(): void {
        // Clear any scheduled cron events.
        $this->unschedule_events();
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
    
    private function unschedule_events(): void {
        // No recurring events in lite version.
    }
}
