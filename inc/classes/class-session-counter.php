<?php
/**
 * @author  wpWax
 * @since   1.0
 * @version 1.0
 */
class DLVL_Session_Counter {
    private $show_modal = false;

    public function __construct() {
        add_action('template_redirect', [ $this, 'track_unique_listing_views' ]);
        add_action('wp_footer', [ $this, 'redirection_popup' ]); // To view in browser footer
    }

    public function track_unique_listing_views() {
        if (is_singular('at_biz_dir')) {
            $post_id = get_the_ID();
            // Get current cookie or start fresh
            $viewed_ids = isset($_COOKIE['dlvl_viewed_listings']) 
                ? json_decode(stripslashes($_COOKIE['dlvl_viewed_listings']), true) 
                : [];

            if (!is_array($viewed_ids)) {
                $viewed_ids = [];
            }

            // Check if the user has pricing plan
            if( $this->has_active_plan() ) {
                $this->show_modal = false;
                return;
            }

            if ( count($viewed_ids) >= 5 ) {
                // Show the popup
                $this->show_modal = true;
                return;
            }

            // Add new unique post ID
            if ( ! in_array($post_id, $viewed_ids) ) {
                $viewed_ids[] = $post_id;
                $this->set_cookie( $viewed_ids);
            }
        }
    }

    public function set_cookie( $viewed_ids = [] )
    {
        // Save updated list to cookie (session only)
        setcookie(
            'dlvl_viewed_listings',
            json_encode($viewed_ids),
            0, // session cookie
            COOKIEPATH,
            COOKIE_DOMAIN
        );
    }

    public function debug_cookie_data() {
        echo '<pre>';
        print_r($_COOKIE);
        echo '</pre>';
    }

    /**
     * Check if the user has an active pricing plan
     */
    public function has_active_plan() {
        $user_id = get_current_user_id();

        $orders = new WP_Query( [
            'post_type'      => 'atbdp_orders',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => $user_id,
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'   => '_payment_status',
                    'value' => 'completed',
                ],
            ],
        ] );
        
        if( ! $orders->have_posts() ) return false;

        foreach ( $orders->posts as $order_id ) {
            $plan_id    = get_post_meta( $order_id, '_fm_plan_ordered', true ); //data form order table
            //if( $plan_id ) echo directorist_plan_lifetime( $plan_id ) . '</br>';

            $package_length = directorist_plan_lifetime( $plan_id );
            $package_length = $package_length ? $package_length : '1';

            $start_date = get_the_date( '', $order_id );

            if ( directorist_validate_date( $start_date ) ) {
                $date = new DateTime( $start_date );
                $date->add( new DateInterval( "P{$package_length}D" ) ); // set the interval in days
                // Comapre with present time

                $now = new DateTime();

                if ( $now < $date ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function redirection_popup()
    {
        if( $this->show_modal ):
        ?>
            <script type="text/javascript">
                jQuery( document ).ready( function(){
                    Swal.fire({
                        title: 'Access Restricted',
                        text: 'You have reached your limit. Please upgrade your plan to view more listings.',
                        icon: 'warning',
                        showConfirmButton: true,
                        confirmButtonText: 'Get Access',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showCloseButton: false,
                        didOpen: () => {
                            // Prevent closing with keyboard
                            document.addEventListener('keydown', function(e) {
                            e.preventDefault();
                            }, { once: true });
                        }
                        }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'https://your-login-url.com'; // Replace with your URL
                        }
                    });
                } );
            </script>
        <?php
        endif;

        //$this->debug_cookie_data();
    }
}

new DLVL_Session_Counter();
