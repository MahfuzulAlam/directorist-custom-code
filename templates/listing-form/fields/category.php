<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.8.0
 */

$data = $data[ 'data' ];

$placeholder = $data['placeholder'] ?? '';
$data_max    = $data['max'] ?? '';
$data_new    = $data['create_new_cat'] ?? '';
$multiple    = $data['type'] && $data['type'] === 'multiple' ? 'multiple' : '';
$lazy_load   = $data['lazy_load'] ?? false;

$current_terms = $listing_form->add_listing_terms( ATBDP_CATEGORY );

$current_ids = array_map( function( $term ) {
	return $term->term_id;
}, $current_terms );

$current_labels = array_map( function( $term ) {
	return $term->name;
}, $current_terms );

$current_ids_as_string    = implode( ',', $current_ids );
$current_labels_as_string = implode( ',', $current_labels );

/**
 * Custom Category Pulling
 */
$parents = get_terms( [
	'taxonomy'   => ATBDP_CATEGORY,
	'hide_empty' => false,
	'parent'     => 0,
] );

$children = [];
$children_options = [];

foreach ( $parents as $parent ) {
	$children_temrs = get_terms(
		[
			'taxonomy'   => ATBDP_CATEGORY,
			'parent'     => $parent->term_id,
			'hide_empty' => false,
		]
	);

	if ( !empty( $children_temrs ) ) {
		$children[$parent->term_id] = $children_temrs;

		foreach ( $children_temrs as $children_term ) {
			$children_options[] = $children_term;
		}

	}

}

?>

<div class="directorist-form-group directorist-form-categories-field">

	<input type="hidden" id="category_children" value='<?php echo json_encode( $children ); ?>' />

	<?php $listing_form->field_label_template( $data );?>

	<select name="admin_category_select_parent" id="at_biz_dir-categories_parent" class="directorist-form-element" data-selected-id="<?php echo esc_attr( $current_ids_as_string ) ?>" data-selected-label="<?php echo esc_attr( $current_labels_as_string ) ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-max="<?php echo esc_attr( $data_max ); ?>" data-allow_new="<?php echo esc_attr( $data_new ); ?>" <?php $listing_form->required( $data ); ?>>

		<?php
			echo '<option value="">' . esc_attr( $placeholder ) . '</option>';

		if ( ! $lazy_load ) {
			foreach ( $parents as $parent ) {
                ?>
                <option value="<?php echo $parent->term_id; ?>"><?php echo $parent->name ?></option>
            <?php
                }
		}
		?>

	</select>

	<?php $listing_form->field_description_template( $data ); ?>

</div>

<div class="directorist-form-sub-categories-field">

	<div class="directorist-form-group directorist-form-categories-field">

		<div class="directorist-form-label">Select Sub Category:</div>

		<select name="admin_category_select_child[]" id="at_biz_dir-categories_child" class="directorist-form-element admin_category_select_child" data-selected-id="<?php echo esc_attr( $current_ids_as_string ) ?>" data-selected-label="<?php echo esc_attr( $current_labels_as_string ) ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-max="<?php echo esc_attr( $data_max ); ?>" data-allow_new="<?php echo esc_attr( $data_new ); ?>" <?php echo esc_attr( $multiple ); ?> <?php $listing_form->required( $data ); ?>>

			<?php

			if ( ! $lazy_load ) {
				foreach ( $children_options as $child_option ) {
					?>
					<option value="<?php echo $child_option->term_id; ?>"><?php echo $child_option->name ?></option>
				<?php
					}
			}
			?>

		</select>

		<?php $listing_form->field_description_template( $data ); ?>

	</div>

</div>

<div class="directorist-form-group directorist-form-categories-field  directorist-form-categories-field-main">

	<?php $listing_form->field_label_template( $data );?>

	<select name="admin_category_select[]" id="at_biz_dir-categories" class="directorist-form-element" data-selected-id="<?php echo esc_attr( $current_ids_as_string ) ?>" data-selected-label="<?php echo esc_attr( $current_labels_as_string ) ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-max="<?php echo esc_attr( $data_max ); ?>" data-allow_new="<?php echo esc_attr( $data_new ); ?>" <?php echo esc_attr( $multiple ); ?> <?php $listing_form->required( $data ); ?>>

		<?php
		if ($data['type'] !== 'multiple') {
			echo '<option value="">' . esc_attr( $placeholder ) . '</option>';
		}

		if ( ! $lazy_load ) {
			echo directorist_kses( $listing_form->add_listing_cat_fields(), 'form_input' );
		}
		?>

	</select>

	<?php $listing_form->field_description_template( $data ); ?>

</div>