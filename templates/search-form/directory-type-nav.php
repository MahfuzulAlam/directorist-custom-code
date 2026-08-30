<?php
/**
 * Search form directory type navigation.
 *
 * @package Directorist_Reorder_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$directory_types = directorist_reorder_types_order_directory_types( $searchform->get_listing_type_data() );
?>

<ul class="directorist-listing-type-selection">
	<?php foreach ( $directory_types as $id => $value ) : ?>
		<li class="directorist-listing-type-selection__item">
			<a
				class="search_listing_types directorist-listing-type-selection__link<?php echo (int) $searchform->get_default_listing_type() === (int) $id ? '--current' : ''; ?>"
				data-listing_type="<?php echo esc_attr( $value['term']->slug ); ?>"
				data-listing_type_id="<?php echo esc_attr( $id ); ?>"
				href="#"
			>
				<?php directorist_icon( $value['data']['icon'] ); ?> <?php echo esc_html( $value['name'] ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
