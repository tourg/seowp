<?php
/**
 * Encryption utility for sensitive data.
 */

namespace SeoAutopilotLite\Security;

class Encryption {
    
    /**
     * Encrypt a string using WordPress salts and keys.
     */
    public static function encrypt( string $data ): string {
        if ( empty( $data ) ) {
            return '';
        }
        
        $key = self::get_encryption_key();
        $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
        
        $encrypted = openssl_encrypt( $data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );
        
        if ( false === $encrypted ) {
            // Fallback to base64 encode if openssl fails.
            return 'base64:' . base64_encode( $data );
        }
        
        return 'enc:' . base64_encode( $iv . $encrypted );
    }
    
    /**
     * Decrypt a string.
     */
    public static function decrypt( string $encrypted_data ): string {
        if ( empty( $encrypted_data ) ) {
            return '';
        }
        
        // Check if it's base64 fallback.
        if ( strpos( $encrypted_data, 'base64:' ) === 0 ) {
            $data = substr( $encrypted_data, 7 );
            $decoded = base64_decode( $data, true );
            return false !== $decoded ? $decoded : '';
        }
        
        // Check if it's encrypted.
        if ( strpos( $encrypted_data, 'enc:' ) !== 0 ) {
            return $encrypted_data;
        }
        
        $data = base64_decode( substr( $encrypted_data, 4 ), true );
        if ( false === $data ) {
            return '';
        }
        
        $key = self::get_encryption_key();
        $iv_length = openssl_cipher_iv_length( 'aes-256-cbc' );
        
        if ( strlen( $data ) <= $iv_length ) {
            return '';
        }
        
        $iv = substr( $data, 0, $iv_length );
        $encrypted = substr( $data, $iv_length );
        
        $decrypted = openssl_decrypt( $encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );
        
        return false !== $decrypted ? $decrypted : '';
    }
    
    /**
     * Get encryption key from WordPress salts.
     */
    private static function get_encryption_key(): string {
        $key = defined( 'AUTH_KEY' ) ? AUTH_KEY : wp_salt( 'auth' );
        $key .= defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : wp_salt( 'secure_auth' );
        $key .= defined( 'LOGGED_IN_KEY' ) ? LOGGED_IN_KEY : wp_salt( 'logged_in' );
        
        // Ensure key is exactly 32 bytes for AES-256.
        return hash( 'sha256', $key, true );
    }
}
