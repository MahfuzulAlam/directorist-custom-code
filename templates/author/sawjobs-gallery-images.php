<?php
/**
 * Public tiled gallery on a Directorist author profile.
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_ids = isset( $image_ids ) ? wp_parse_id_list( $image_ids ) : array();

if ( empty( $image_ids ) ) {
	return;
}
?>
<h2 class="directorist-author-listing-top__title"><?php esc_html_e( 'Your Future Work Place', 'directorist-sawjobs-custom-codes' ); ?></h2>
<section class="directorist-card directorist-sawjobs-author-gallery" data-author-gallery>
	<div class="directorist-card__body">
		<div class="directorist-sawjobs-author-gallery__grid">
			<?php foreach ( $image_ids as $index => $attachment_id ) : ?>
				<?php
				$full_url = wp_get_attachment_image_url( $attachment_id, 'full' );

				if ( ! $full_url ) {
					continue;
				}
				?>
				<a
					class="directorist-sawjobs-author-gallery__item"
					href="<?php echo esc_url( $full_url ); ?>"
					data-gallery-lightbox
					data-gallery-index="<?php echo esc_attr( $index ); ?>"
					aria-label="<?php esc_attr_e( 'View gallery image full screen', 'directorist-sawjobs-custom-codes' ); ?>"
				>
					<?php
					echo wp_kses_post(
						wp_get_attachment_image(
							$attachment_id,
							'large',
							false,
							array(
								'class'   => 'directorist-sawjobs-author-gallery__image',
								'loading' => 'lazy',
							)
						)
					);
					?>
					<span class="directorist-sawjobs-author-gallery__zoom" aria-hidden="true"><?php directorist_icon( 'las la-expand' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="directorist-sawjobs-gallery-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Gallery image viewer', 'directorist-sawjobs-custom-codes' ); ?>" data-gallery-modal>
		<button type="button" class="directorist-sawjobs-gallery-lightbox__close" aria-label="<?php esc_attr_e( 'Close image viewer', 'directorist-sawjobs-custom-codes' ); ?>" data-gallery-close>
			<?php directorist_icon( 'las la-times' ); ?>
		</button>
		<button type="button" class="directorist-sawjobs-gallery-lightbox__nav directorist-sawjobs-gallery-lightbox__nav--previous" aria-label="<?php esc_attr_e( 'Previous image', 'directorist-sawjobs-custom-codes' ); ?>" data-gallery-previous>
			<?php directorist_icon( 'las la-angle-left' ); ?>
		</button>
		<figure class="directorist-sawjobs-gallery-lightbox__figure">
			<img class="directorist-sawjobs-gallery-lightbox__image" src="" alt="" data-gallery-modal-image />
			<figcaption class="directorist-sawjobs-gallery-lightbox__counter" data-gallery-modal-counter></figcaption>
		</figure>
		<button type="button" class="directorist-sawjobs-gallery-lightbox__nav directorist-sawjobs-gallery-lightbox__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'directorist-sawjobs-custom-codes' ); ?>" data-gallery-next>
			<?php directorist_icon( 'las la-angle-right' ); ?>
		</button>
	</div>
</section>
