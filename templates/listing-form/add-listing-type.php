<?php
/**
 * Add listing directory type selection.
 *
 * @package Directorist_Reorder_Types
 */

use Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$directory_types = directorist_reorder_types_order_directory_types( $listing_form->get_listing_types() );
?>

<div class="directorist-add-listing-types directorist-w-100">
	<div class="<?php Helper::directorist_container_fluid(); ?>">
		<div class="<?php Helper::directorist_row(); ?> directorist-justify-content-center">
			<?php foreach ( $directory_types as $value ) : ?>
				<div class="<?php Helper::directorist_column( array( '3' ) ); ?>">
					<a href="<?php echo esc_url( add_query_arg( 'directory_type', $value['term']->slug ) ); ?>" class="directorist-add-listing-types__single__link">
						<?php
						if ( ! empty( $value['data']['icon'] ) ) {
							directorist_icon( $value['data']['icon'] );
						} else {
							directorist_icon( 'las la-home' );
						}
						?>
						<span><?php echo esc_html( $value['name'] ); ?></span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
