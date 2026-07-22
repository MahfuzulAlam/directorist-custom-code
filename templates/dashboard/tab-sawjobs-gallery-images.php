<?php
/**
 * Directorist dashboard Gallery Images tab.
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_ids     = isset( $image_ids ) ? wp_parse_id_list( $image_ids ) : array();
$max_images    = isset( $max_images ) ? max( 1, absint( $max_images ) ) : 10;
$current_count = count( $image_ids );
$is_full       = $current_count >= $max_images;
?>

<div
	class="directorist-sawjobs-gallery-dashboard"
	data-current-count="<?php echo esc_attr( $current_count ); ?>"
	data-max-images="<?php echo esc_attr( $max_images ); ?>"
>
	<div class="directorist-sawjobs-gallery-dashboard__header">
		<div>
			<h3 class="directorist-sawjobs-gallery-dashboard__title"><?php esc_html_e( 'Gallery Images', 'directorist-sawjobs-custom-codes' ); ?></h3>
			<p class="directorist-sawjobs-gallery-dashboard__description">
				<?php
				printf(
					/* translators: %d: maximum number of gallery images. */
					esc_html__( 'Upload JPG, PNG, GIF, or WebP images. You can keep up to %d images in your gallery.', 'directorist-sawjobs-custom-codes' ),
					esc_html( $max_images )
				);
				?>
			</p>
		</div>

		<p class="directorist-sawjobs-gallery-dashboard__count" aria-live="polite">
			<span data-gallery-count><?php echo esc_html( number_format_i18n( $current_count ) ); ?></span>
			<span aria-hidden="true">/</span>
			<span data-gallery-limit><?php echo esc_html( number_format_i18n( $max_images ) ); ?></span>
			<span class="screen-reader-text"><?php esc_html_e( 'images used', 'directorist-sawjobs-custom-codes' ); ?></span>
		</p>
	</div>

	<div class="directorist-sawjobs-gallery-upload<?php echo $is_full ? ' is-disabled' : ''; ?>" data-gallery-dropzone>
		<input
			type="file"
			id="directorist-sawjobs-gallery-files"
			class="directorist-sawjobs-gallery-upload__input"
			accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
			multiple
			<?php disabled( $is_full ); ?>
			data-gallery-file-input
		/>
		<label class="directorist-sawjobs-gallery-upload__label" for="directorist-sawjobs-gallery-files">
			<span class="directorist-sawjobs-gallery-upload__icon" aria-hidden="true"><?php directorist_icon( 'las la-cloud-upload-alt' ); ?></span>
			<span class="directorist-sawjobs-gallery-upload__title"><?php esc_html_e( 'Choose images or drop them here', 'directorist-sawjobs-custom-codes' ); ?></span>
			<span class="directorist-sawjobs-gallery-upload__hint"><?php esc_html_e( 'Images are converted to WebP and saved automatically.', 'directorist-sawjobs-custom-codes' ); ?></span>
		</label>
	</div>

	<div class="directorist-sawjobs-gallery-message" role="status" aria-live="polite" hidden data-gallery-message></div>

	<div class="directorist-sawjobs-gallery-dashboard__grid" data-gallery-grid>
		<?php foreach ( $image_ids as $attachment_id ) : ?>
			<article class="directorist-sawjobs-gallery-dashboard__item" data-gallery-item="<?php echo esc_attr( $attachment_id ); ?>">
				<?php
				echo wp_kses_post(
					wp_get_attachment_image(
						$attachment_id,
						'medium',
						false,
						array(
							'class'   => 'directorist-sawjobs-gallery-dashboard__image',
							'loading' => 'lazy',
						)
					)
				);
				?>
				<button
					type="button"
					class="directorist-sawjobs-gallery-dashboard__remove"
					data-gallery-remove="<?php echo esc_attr( $attachment_id ); ?>"
					aria-label="<?php esc_attr_e( 'Remove image permanently', 'directorist-sawjobs-custom-codes' ); ?>"
				>
					<?php directorist_icon( 'las la-trash' ); ?>
				</button>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="directorist-sawjobs-gallery-dashboard__empty"<?php echo $current_count ? ' hidden' : ''; ?> data-gallery-empty>
		<span aria-hidden="true"><?php directorist_icon( 'las la-images' ); ?></span>
		<p><?php esc_html_e( 'Your gallery does not have any images yet.', 'directorist-sawjobs-custom-codes' ); ?></p>
	</div>
</div>
