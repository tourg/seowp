<?php
/**
 * Plugin uninstall handler.
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if this is an uninstall request.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Load the uninstaller class.
require_once __DIR__ . '/includes/Core/Settings.php';
require_once __DIR__ . '/includes/Core/Uninstaller.php';

// Run cleanup.
SeoAutopilotLite\Core\Uninstaller::uninstall();
