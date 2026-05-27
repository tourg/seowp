<?php
/**
 * Container class - dependency injection container.
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
 * Container class.
 */
class Container
{
    /**
     * Registered services.
     *
     * @var array<string, callable|object>
     */
    private array $services = [];
    
    /**
     * Resolved service instances.
     *
     * @var array<string, object>
     */
    private array $resolved = [];
    
    /**
     * Register a service.
     *
     * @param string $id Service identifier.
     * @param callable|object $definition Service definition or instance.
     * @return self
     */
    public function register(string $id, $definition): self
    {
        $this->services[$id] = $definition;
        return $this;
    }
    
    /**
     * Get a service by ID.
     *
     * @param string $id Service identifier.
     * @return object|null Service instance or null if not found.
     */
    public function get(string $id): ?object
    {
        // Return already resolved instance.
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }
        
        // Check if service is registered.
        if (!isset($this->services[$id])) {
            return null;
        }
        
        $definition = $this->services[$id];
        
        // If it's already an object, store and return it.
        if (is_object($definition)) {
            $this->resolved[$id] = $definition;
            return $definition;
        }
        
        // If it's a callable, call it to get the instance.
        if (is_callable($definition)) {
            $instance = $definition($this);
            
            if (!is_object($instance)) {
                return null;
            }
            
            $this->resolved[$id] = $instance;
            return $instance;
        }
        
        return null;
    }
    
    /**
     * Check if a service is registered.
     *
     * @param string $id Service identifier.
     * @return bool True if service exists.
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->resolved[$id]);
    }
    
    /**
     * Register common services.
     *
     * @return self
     */
    public function registerDefaults(): self
    {
        // Logger.
        $this->register('logger', function (): Logger {
            return new Logger();
        });
        
        // SEO Scanner.
        $this->register('seo_scanner', function (): SEO\SeoScanner {
            return new SEO\SeoScanner();
        });
        
        // AI Client.
        $this->register('ai_client', function (): AI\AiClient {
            return new AI\AiClient();
        });
        
        // Content Optimizer.
        $this->register('optimizer', function (): Optimizer\ContentOptimizer {
            return new Optimizer\ContentOptimizer();
        });
        
        // Backup Service.
        $this->register('backup', function (): Optimizer\BackupService {
            return new Optimizer\BackupService();
        });
        
        // Schema Generator.
        $this->register('schema', function (): Schema\SchemaGenerator {
            return new Schema\SchemaGenerator();
        });
        
        return $this;
    }
    
    /**
     * Get all resolved services.
     *
     * @return array<string, object>
     */
    public function getResolved(): array
    {
        return $this->resolved;
    }
    
    /**
     * Clear all resolved services.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->resolved = [];
        return $this;
    }
}
