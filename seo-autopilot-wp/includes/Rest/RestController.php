<?php
/**
 * RestController base class - provides common REST API functionality.
 *
 * @package SeoAutopilotWp\Rest
 * @since 1.0.0
 */

declare(strict_types=1);

namespace SeoAutopilotWp\Rest;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RestController base class.
 */
abstract class RestController
{
    /**
     * REST API namespace.
     *
     * @var string
     */
    protected const NAMESPACE = 'seo-autopilot/v1';
    
    /**
     * REST route base.
     *
     * @var string
     */
    protected string $route_base = '';
    
    /**
     * Register routes.
     */
    abstract public function register_routes(): void;
    
    /**
     * Get permission callback for admin-only access.
     *
     * @param string $capability Required capability.
     * @return callable Permission callback.
     */
    protected function getPermissionCallback(string $capability = 'manage_seo_autopilot'): callable
    {
        return static function () use ($capability): bool {
            return current_user_can($capability);
        };
    }
    
    /**
     * Create a success response.
     *
     * @param mixed $data Response data.
     * @param int $status HTTP status code.
     * @return WP_REST_Response
     */
    protected function successResponse($data, int $status = 200): WP_REST_Response
    {
        return new WP_REST_Response([
            'success' => true,
            'data' => $data,
        ], $status);
    }
    
    /**
     * Create an error response.
     *
     * @param string $code Error code.
     * @param string $message Error message.
     * @param int $status HTTP status code.
     * @return WP_Error
     */
    protected function errorResponse(string $code, string $message, int $status = 400): WP_Error
    {
        return new WP_Error($code, $message, ['status' => $status]);
    }
    
    /**
     * Validate required parameters.
     *
     * @param WP_REST_Request $request Request object.
     * @param array<string> $required Required parameter names.
     * @return WP_Error|null Error if validation fails, null otherwise.
     */
    protected function validateRequired(WP_REST_Request $request, array $required): ?WP_Error
    {
        foreach ($required as $param) {
            if (!$request->has_param($param)) {
                return $this->errorResponse(
                    'missing_parameter',
                    sprintf(
                        /* translators: %s: parameter name */
                        __('Missing required parameter: %s', 'seo-autopilot-wp'),
                        $param
                    ),
                    400
                );
            }
        }
        
        return null;
    }
    
    /**
     * Sanitize text field.
     *
     * @param string $value Value to sanitize.
     * @return string Sanitized value.
     */
    protected function sanitizeText(string $value): string
    {
        return sanitize_text_field($value);
    }
    
    /**
     * Sanitize textarea field.
     *
     * @param string $value Value to sanitize.
     * @return string Sanitized value.
     */
    protected function sanitizeTextarea(string $value): string
    {
        return sanitize_textarea_field($value);
    }
    
    /**
     * Sanitize email field.
     *
     * @param string $value Value to sanitize.
     * @return string Sanitized value.
     */
    protected function sanitizeEmail(string $value): string
    {
        return sanitize_email($value);
    }
    
    /**
     * Sanitize URL field.
     *
     * @param string $value Value to sanitize.
     * @return string Sanitized value.
     */
    protected function sanitizeUrl(string $value): string
    {
        return esc_url_raw($value);
    }
    
    /**
     * Sanitize integer field.
     *
     * @param mixed $value Value to sanitize.
     * @return int Sanitized value.
     */
    protected function sanitizeInt($value): int
    {
        return intval($value);
    }
    
    /**
     * Sanitize boolean field.
     *
     * @param mixed $value Value to sanitize.
     * @return bool Sanitized value.
     */
    protected function sanitizeBool($value): bool
    {
        return (bool) $value;
    }
    
    /**
     * Get current user ID.
     *
     * @return int
     */
    protected function getCurrentUserId(): int
    {
        return get_current_user_id();
    }
    
    /**
     * Log an action.
     *
     * @param string $level Log level.
     * @param string $message Log message.
     * @param array<string, mixed> $context Context data.
     * @return int|false
     */
    protected function log(string $level, string $message, array $context = [])
    {
        $logger = new Core\Logger();
        return $logger->log($level, $message, $context);
    }
}
