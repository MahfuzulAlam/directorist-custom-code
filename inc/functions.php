<?php

/**
 * Add your custom php code here
 */


if( ! shortcode_exists( 'beehiiv-iframe' ) ){
    add_shortcode( 'beehiiv-iframe', function(){
        ob_start();
        echo '<iframe src="https://embeds.beehiiv.com/4cf81fac-3786-47d2-a166-01ac7985ff18" data-test-id="beehiiv-embed" width="100%" height="320" frameborder="0" scrolling="no" style="border-radius: 4px; border: 2px solid #e5e7eb; margin: 0; background-color: transparent;"></iframe>';
        return ob_get_clean();
    } );
}