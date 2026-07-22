<?php
/**
 * Public tiled gallery on a Directorist author profile.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_ids = isset( $image_ids ) ? wp_parse_id_list( $image_ids ) : array();

if ( empty( $image_ids ) ) {
	return;
}
?>
<h2 class="directorist-author-listing-top__title"><?php esc_html_e( 'Your Future Work Place', 'directorist-custom-code' ); ?></h2>
<section class="directorist-card directorist-custom-author-gallery" data-author-gallery>
	<div class="directorist-card__body">
		<div class="directorist-custom-author-gallery__grid">
			<?php foreach ( $image_ids as $index => $attachment_id ) : ?>
				<?php
				$full_url = wp_get_attachment_image_url( $attachment_id, 'full' );

				if ( ! $full_url ) {
					continue;
				}
				?>
				<a
					class="directorist-custom-author-gallery__item"
					href="<?php echo esc_url( $full_url ); ?>"
					data-gallery-lightbox
					data-gallery-index="<?php echo esc_attr( $index ); ?>"
					aria-label="<?php esc_attr_e( 'View gallery image full screen', 'directorist-custom-code' ); ?>"
				>
					<?php
					echo wp_kses_post(
						wp_get_attachment_image(
							$attachment_id,
							'large',
							false,
							array(
								'class'   => 'directorist-custom-author-gallery__image',
								'loading' => 'lazy',
							)
						)
					);
					?>
					<span class="directorist-custom-author-gallery__zoom" aria-hidden="true"><?php directorist_icon( 'las la-expand' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="directorist-custom-gallery-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Gallery image viewer', 'directorist-custom-code' ); ?>" data-gallery-modal>
		<button type="button" class="directorist-custom-gallery-lightbox__close" aria-label="<?php esc_attr_e( 'Close image viewer', 'directorist-custom-code' ); ?>" data-gallery-close>
			<?php directorist_icon( 'las la-times' ); ?>
		</button>
		<button type="button" class="directorist-custom-gallery-lightbox__nav directorist-custom-gallery-lightbox__nav--previous" aria-label="<?php esc_attr_e( 'Previous image', 'directorist-custom-code' ); ?>" data-gallery-previous>
			<?php directorist_icon( 'las la-angle-left' ); ?>
		</button>
		<figure class="directorist-custom-gallery-lightbox__figure">
			<img class="directorist-custom-gallery-lightbox__image" src="" alt="" data-gallery-modal-image />
			<figcaption class="directorist-custom-gallery-lightbox__counter" data-gallery-modal-counter></figcaption>
		</figure>
		<button type="button" class="directorist-custom-gallery-lightbox__nav directorist-custom-gallery-lightbox__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'directorist-custom-code' ); ?>" data-gallery-next>
			<?php directorist_icon( 'las la-angle-right' ); ?>
		</button>
	</div>
</section>
