<?php
/**
 * User Location Handler Class
 * 
 * Handles getting and storing user's current location (latitude and longitude)
 * Uses JavaScript geolocation API and WordPress transients for storage
 */

if (!defined('ABSPATH')) {
    exit;
}

class Directorist_User_Location {
    
    /**
     * Transient key prefix
     */
    private $transient_prefix = 'directorist_user_location_';
    
    /**
     * Transient expiration time (1 hour in seconds)
     */
    private $expiration = 3600;
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize hooks
     */
    private function init() {
        // AJAX handler for saving location
        add_action('wp_ajax_directorist_save_location', array($this, 'save_location'));
        add_action('wp_ajax_nopriv_directorist_save_location', array($this, 'save_location'));
        
        // Enqueue location script
        add_action('wp_enqueue_scripts', array($this, 'enqueue_location_script'));
        
        // Add inline script with AJAX URL
        add_action('wp_footer', array($this, 'add_location_script'));

        add_filter( 'directorist_all_listings_query_arguments', array($this, 'filter_all_listings_query_arguments') );
    }

    public function filter_all_listings_query_arguments( $args ) {
        $location = $this->get_location();
        if ( $location ) {
            $args['atbdp_geo_query_custom'] = [
                'lat_field'  => '_manual_lat',
                'lng_field'  => '_manual_lng',
                'latitude'   => $location['latitude'],
                'longitude'  => $location['longitude'],
                'min_distance'   => 0,
                'max_distance'   => 100,
                'units'      => 'miles'
            ];
            $args['orderby'] = 'distance';
            $args['order'] = 'ASC';
        }
        return $args;
    }
    /**
     * Get transient key for current user
     */
    private function get_transient_key() {
        // Use IP address as identifier for non-logged-in users
        // For logged-in users, use user ID
        $identifier = is_user_logged_in() ? get_current_user_id() : $this->get_user_ip();
        return $this->transient_prefix . md5($identifier);
    }
    
    /**
     * Get user IP address
     */
    private function get_user_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    
    /**
     * Get user location from transient
     */
    public function get_location() {
        $transient_key = $this->get_transient_key();
        $location = get_transient($transient_key);
        
        if ($location === false) {
            return null;
        }
        
        return $location;
    }
    
    /**
     * Save user location to transient
     */
    public function save_location() {
        // Verify nonce for security
        check_ajax_referer('directorist_location_nonce', 'nonce');
        
        $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        
        // Validate coordinates
        if ($latitude === null || $longitude === null) {
            wp_send_json_error(array(
                'message' => 'Invalid coordinates provided'
            ));
            return;
        }
        
        // Validate latitude range (-90 to 90)
        if ($latitude < -90 || $latitude > 90) {
            wp_send_json_error(array(
                'message' => 'Invalid latitude value'
            ));
            return;
        }
        
        // Validate longitude range (-180 to 180)
        if ($longitude < -180 || $longitude > 180) {
            wp_send_json_error(array(
                'message' => 'Invalid longitude value'
            ));
            return;
        }
        
        $location = array(
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timestamp' => current_time('timestamp')
        );
        
        $transient_key = $this->get_transient_key();
        $saved = set_transient($transient_key, $location, $this->expiration);
        
        if ($saved) {
            wp_send_json_success(array(
                'message' => 'Location saved successfully',
                'location' => $location
            ));
        } else {
            wp_send_json_error(array(
                'message' => 'Failed to save location'
            ));
        }
    }
    
    /**
     * Check if location exists and is valid
     */
    public function has_location() {
        $location = $this->get_location();
        return $location !== null && isset($location['latitude']) && isset($location['longitude']);
    }
    
    /**
     * Get latitude
     */
    public function get_latitude() {
        $location = $this->get_location();
        return $location ? $location['latitude'] : null;
    }
    
    /**
     * Get longitude
     */
    public function get_longitude() {
        $location = $this->get_location();
        return $location ? $location['longitude'] : null;
    }
    
    /**
     * Clear location
     */
    public function clear_location() {
        $transient_key = $this->get_transient_key();
        delete_transient($transient_key);
    }
    
    /**
     * Enqueue location script
     */
    public function enqueue_location_script() {
        // Only enqueue on frontend
        if (is_admin()) {
            return;
        }
        
        wp_enqueue_script('directorist-location-based-listings-script', DIRECTORIST_LOCATION_BASED_LISTINGS_URI . 'assets/js/main.js', array('jquery'), '2.0', true);
        // Create nonce for AJAX security
        wp_localize_script('directorist-location-based-listings-script', 'directoristLocation', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directorist_location_nonce'),
            'hasLocation' => $this->has_location() ? 1 : 0
        ));
    }
    
    /**
     * Add inline location detection script
     */
    public function add_location_script() {
        // Only on frontend
        if (is_admin()) {
            return;
        }
        
        // Check if location already exists
        if ($this->has_location()) {
            return;
        }
        
        ?>
        <script type="text/javascript">
        (function() {
            function getLocation() {
                // Check if geolocation is supported
                if (!navigator.geolocation) {
                    console.log('Geolocation is not supported by this browser.');
                    return;
                }
                
                // Check if location data is already available
                if (typeof directoristLocation !== 'undefined' && directoristLocation.hasLocation === 1) {
                    return;
                }
                
                // Get current position
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Success callback
                        //var latitude = position.coords.latitude;
                        //var longitude = position.coords.longitude;
                        var latitude = 23.723080;
                        var longitude = 90.409138;
                        
                        // Function to send location via AJAX
                        function sendLocation() {
                            if (typeof directoristLocation === 'undefined') {
                                return;
                            }
                            
                            // Use jQuery if available, otherwise use vanilla JS
                            if (typeof jQuery !== 'undefined') {
                                jQuery.ajax({
                                    url: directoristLocation.ajaxUrl,
                                    type: 'POST',
                                    data: {
                                        action: 'directorist_save_location',
                                        latitude: latitude,
                                        longitude: longitude,
                                        nonce: directoristLocation.nonce
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            console.log('Location saved successfully:', response.data.location);
                                        } else {
                                            console.error('Failed to save location:', response.data.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('AJAX error:', error);
                                    }
                                });
                            } else {
                                // Fallback to vanilla JS fetch
                                var formData = new FormData();
                                formData.append('action', 'directorist_save_location');
                                formData.append('latitude', latitude);
                                formData.append('longitude', longitude);
                                formData.append('nonce', directoristLocation.nonce);
                                
                                fetch(directoristLocation.ajaxUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(function(response) {
                                    return response.json();
                                })
                                .then(function(data) {
                                    if (data.success) {
                                        console.log('Location saved successfully:', data.data.location);
                                    } else {
                                        console.error('Failed to save location:', data.data.message);
                                    }
                                })
                                .catch(function(error) {
                                    console.error('AJAX error:', error);
                                });
                            }
                        }
                        
                        // Wait for jQuery if needed, otherwise send immediately
                        if (typeof jQuery === 'undefined' && typeof directoristLocation !== 'undefined') {
                            // Try to send with vanilla JS
                            sendLocation();
                        } else if (typeof jQuery !== 'undefined') {
                            // jQuery is available, send immediately
                            sendLocation();
                        } else {
                            // Wait a bit for scripts to load
                            setTimeout(function() {
                                sendLocation();
                            }, 500);
                        }
                    },
                    function(error) {
                        // Error callback
                        var errorMessage = 'Unknown error';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'User denied the request for Geolocation.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information is unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'The request to get user location timed out.';
                                break;
                        }
                        console.log('Geolocation error:', errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }
            
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', getLocation);
            } else {
                // DOM is already ready
                getLocation();
            }
        })();
        </script>
        <?php
    }
}

// Initialize the class
Directorist_User_Location::instance();

