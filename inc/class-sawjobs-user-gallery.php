<?php
/**
 * Frontend user gallery for Directorist authors.
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a gallery manager to the dashboard and renders its images on authors.
 */
final class Directorist_Sawjobs_Custom_Codes_User_Gallery {

	/** User meta containing the ordered attachment ID array. */
	public const META_KEY = '_directorist_sawjobs_custom_codes_gallery_images';

	/** Default maximum number of gallery images per user. */
	public const DEFAULT_MAX_IMAGES = 10;

	/** AJAX nonce action. */
	private const NONCE_ACTION = 'directorist_sawjobs_custom_codes_gallery';

	/** AJAX action used to upload an image. */
	private const AJAX_UPLOAD_ACTION = 'directorist_sawjobs_custom_codes_gallery_upload';

	/** AJAX action used to remove an image. */
	private const AJAX_REMOVE_ACTION = 'directorist_sawjobs_custom_codes_gallery_remove';

	/** Register WordPress and Directorist hooks. */
	public static function register() {
		add_filter( 'directorist_dashboard_tabs', array( __CLASS__, 'add_dashboard_tab' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		add_action( 'wp_ajax_' . self::AJAX_UPLOAD_ACTION, array( __CLASS__, 'ajax_upload_image' ) );
		add_action( 'wp_ajax_' . self::AJAX_REMOVE_ACTION, array( __CLASS__, 'ajax_remove_image' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_UPLOAD_ACTION, array( __CLASS__, 'ajax_authentication_required' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_REMOVE_ACTION, array( __CLASS__, 'ajax_authentication_required' ) );
	}

	/**
	 * Add the Gallery Images dashboard tab.
	 *
	 * @param array $tabs Existing dashboard tabs.
	 * @return array
	 */
	public static function add_dashboard_tab( $tabs ) {
		$user_id = get_current_user_id();

		if ( ! $user_id || ! is_array( $tabs ) || ! class_exists( '\\Directorist\\Helper' ) ) {
			return $tabs;
		}

		$image_ids = self::get_image_ids( $user_id );
		$max_images = self::get_max_images( $user_id );

		$tabs['dashboard_sawjobs_gallery_images'] = array(
			'title'   => __( 'Gallery Images', 'directorist-sawjobs-custom-codes' ),
			'icon'    => 'las la-images',
			'content' => \Directorist\Helper::get_template_contents(
				'dashboard/tab-sawjobs-gallery-images',
				array(
					'user_id'    => $user_id,
					'image_ids'  => $image_ids,
					'max_images' => $max_images,
				)
			),
		);

		return $tabs;
	}

	/**
	 * Render the public gallery for a Directorist author model.
	 *
	 * @param object $author Directorist author model.
	 * @return void
	 */
	public static function render_author_gallery( $author ) {
		if ( ! is_object( $author ) || ! method_exists( $author, 'get_id' ) || ! class_exists( '\\Directorist\\Helper' ) ) {
			return;
		}

		$user_id   = absint( $author->get_id() );
		$image_ids = self::get_image_ids( $user_id );

		if ( empty( $image_ids ) ) {
			return;
		}

		\Directorist\Helper::get_template(
			'author/sawjobs-gallery-images',
			array(
				'author'    => $author,
				'user_id'   => $user_id,
				'image_ids' => $image_ids,
			)
		);
	}

	/** Enqueue gallery assets on configured dashboard and author pages. */
	public static function enqueue_assets() {
		if ( ! self::is_gallery_context() ) {
			return;
		}

		wp_enqueue_style(
			'directorist-sawjobs-custom-codes-gallery',
			DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI . 'assets/css/sawjobs-gallery-images.css',
			array( 'directorist-sawjobs-custom-codes-style' ),
			DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION
		);

		wp_enqueue_script(
			'directorist-sawjobs-custom-codes-gallery',
			DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI . 'assets/js/sawjobs-gallery-images.js',
			array(),
			DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION,
			true
		);

		wp_localize_script(
			'directorist-sawjobs-custom-codes-gallery',
			'DirectoristSawjobsGallery',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => is_user_logged_in() ? wp_create_nonce( self::NONCE_ACTION ) : '',
				'uploadAction'  => self::AJAX_UPLOAD_ACTION,
				'removeAction'  => self::AJAX_REMOVE_ACTION,
				'uploadingText' => __( 'Uploading images...', 'directorist-sawjobs-custom-codes' ),
				'tooManyText'   => __( 'Only %d more images can be uploaded.', 'directorist-sawjobs-custom-codes' ),
				'removeLabel'   => __( 'Remove image permanently', 'directorist-sawjobs-custom-codes' ),
				'confirmRemove' => __( 'Remove this image permanently? This also deletes it from the Media Library.', 'directorist-sawjobs-custom-codes' ),
				'genericError'  => __( 'Something went wrong. Please try again.', 'directorist-sawjobs-custom-codes' ),
			)
		);
	}

	/**
	 * Get the valid ordered attachment IDs stored for a user.
	 *
	 * @param int $user_id User ID.
	 * @return int[]
	 */
	public static function get_image_ids( $user_id ) {
		$user_id = absint( $user_id );

		if ( ! $user_id ) {
			return array();
		}

		$image_ids = wp_parse_id_list( get_user_meta( $user_id, self::META_KEY, true ) );

		return array_values(
			array_filter(
				array_unique( $image_ids ),
				static function ( $attachment_id ) {
					return 'attachment' === get_post_type( $attachment_id ) && wp_attachment_is_image( $attachment_id );
				}
			)
		);
	}

	/**
	 * Get the filterable maximum image count for a user.
	 *
	 * @param int $user_id User ID.
	 * @return int
	 */
	public static function get_max_images( $user_id ) {
		/**
		 * Filter the maximum gallery image count for a user.
		 *
		 * @param int $maximum Maximum image count. Default 10.
		 * @param int $user_id User ID.
		 */
		$maximum = apply_filters( 'directorist_sawjobs_custom_codes_gallery_max_images', self::DEFAULT_MAX_IMAGES, absint( $user_id ) );

		return max( 1, absint( $maximum ) );
	}

	/** Handle an authenticated frontend image upload. */
	public static function ajax_upload_image() {
		$user_id    = self::validate_ajax_request();
		$image_ids  = self::get_image_ids( $user_id );
		$max_images = self::get_max_images( $user_id );

		if ( count( $image_ids ) >= $max_images ) {
			wp_send_json_error(
				array( 'message' => self::limit_message( $max_images ) ),
				409
			);
		}

		if ( empty( $_FILES['gallery_image'] ) || ! is_array( $_FILES['gallery_image'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Please choose an image to upload.', 'directorist-sawjobs-custom-codes' ) ),
				400
			);
		}

		$file = $_FILES['gallery_image'];

		if ( ! empty( $file['error'] ) || empty( $file['tmp_name'] ) || empty( $file['name'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The selected image could not be uploaded.', 'directorist-sawjobs-custom-codes' ) ),
				400
			);
		}

		$allowed_mimes = self::get_allowed_mime_types( $user_id );
		$filename      = sanitize_file_name( $file['name'] );
		$filetype      = wp_check_filetype_and_ext( $file['tmp_name'], $filename, $allowed_mimes );
		$mime_type     = ! empty( $filetype['type'] ) ? $filetype['type'] : '';

		if ( ! $mime_type || 0 !== strpos( $mime_type, 'image/' ) || ! in_array( $mime_type, $allowed_mimes, true ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Only JPG, PNG, GIF, and WebP images are allowed.', 'directorist-sawjobs-custom-codes' ) ),
				415
			);
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$converted_file = Directorist_Sawjobs_Custom_Codes_WebP_Converter::convert( $file, $mime_type, $user_id );

		if ( is_wp_error( $converted_file ) ) {
			wp_send_json_error(
				array( 'message' => $converted_file->get_error_message() ),
				422
			);
		}

		$attachment_id = media_handle_sideload(
			$converted_file,
			0,
			null,
			array( 'post_author' => $user_id )
		);

		if ( is_wp_error( $attachment_id ) ) {
			Directorist_Sawjobs_Custom_Codes_WebP_Converter::cleanup( $converted_file );

			wp_send_json_error(
				array( 'message' => $attachment_id->get_error_message() ),
				400
			);
		}

		$attachment_id = absint( $attachment_id );

		if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
			if ( $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}

			wp_send_json_error(
				array( 'message' => __( 'The uploaded file is not a valid image.', 'directorist-sawjobs-custom-codes' ) ),
				415
			);
		}

		wp_update_post(
			array(
				'ID'          => $attachment_id,
				'post_author' => $user_id,
			)
		);

		$image_ids = self::get_image_ids( $user_id );

		if ( count( $image_ids ) >= $max_images ) {
			wp_delete_attachment( $attachment_id, true );
			wp_send_json_error(
				array( 'message' => self::limit_message( $max_images ) ),
				409
			);
		}

		$image_ids[] = $attachment_id;
		update_user_meta( $user_id, self::META_KEY, array_values( $image_ids ) );

		do_action( 'directorist_sawjobs_custom_codes_gallery_image_uploaded', $attachment_id, $user_id, $image_ids );

		wp_send_json_success(
			array(
				'message'   => __( 'Image uploaded.', 'directorist-sawjobs-custom-codes' ),
				'image'     => self::get_image_data( $attachment_id ),
				'count'     => count( $image_ids ),
				'maxImages' => $max_images,
			)
		);
	}

	/** Handle an authenticated permanent image removal. */
	public static function ajax_remove_image() {
		$user_id       = self::validate_ajax_request();
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
		$image_ids     = self::get_image_ids( $user_id );

		if ( ! $attachment_id || ! in_array( $attachment_id, $image_ids, true ) ) {
			wp_send_json_error(
				array( 'message' => __( 'This image is not in your gallery.', 'directorist-sawjobs-custom-codes' ) ),
				404
			);
		}

		$attachment = get_post( $attachment_id );

		if ( $attachment && absint( $attachment->post_author ) !== $user_id ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to delete this image.', 'directorist-sawjobs-custom-codes' ) ),
				403
			);
		}

		if ( $attachment && ! wp_delete_attachment( $attachment_id, true ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The image could not be deleted.', 'directorist-sawjobs-custom-codes' ) ),
				500
			);
		}

		$image_ids = array_values( array_diff( $image_ids, array( $attachment_id ) ) );
		update_user_meta( $user_id, self::META_KEY, $image_ids );

		do_action( 'directorist_sawjobs_custom_codes_gallery_image_removed', $attachment_id, $user_id, $image_ids );

		wp_send_json_success(
			array(
				'message'   => __( 'Image removed.', 'directorist-sawjobs-custom-codes' ),
				'imageId'   => $attachment_id,
				'count'     => count( $image_ids ),
				'maxImages' => self::get_max_images( $user_id ),
			)
		);
	}

	/** Return a consistent authentication error for logged-out AJAX requests. */
	public static function ajax_authentication_required() {
		wp_send_json_error(
			array( 'message' => __( 'You must be logged in to manage gallery images.', 'directorist-sawjobs-custom-codes' ) ),
			401
		);
	}

	/**
	 * Get data needed by the dashboard JavaScript for one attachment.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return array
	 */
	private static function get_image_data( $attachment_id ) {
		$thumbnail = wp_get_attachment_image_url( $attachment_id, 'medium' );
		$full      = wp_get_attachment_image_url( $attachment_id, 'full' );

		return array(
			'id'        => absint( $attachment_id ),
			'thumbnail' => $thumbnail ? $thumbnail : $full,
			'full'      => $full,
			'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		);
	}

	/**
	 * Validate a gallery AJAX request and return its current user ID.
	 *
	 * @return int
	 */
	private static function validate_ajax_request() {
		if ( ! is_user_logged_in() ) {
			self::ajax_authentication_required();
		}

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		return get_current_user_id();
	}

	/**
	 * Get allowed upload MIME types.
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	private static function get_allowed_mime_types( $user_id ) {
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
			'webp'         => 'image/webp',
		);

		return (array) apply_filters( 'directorist_sawjobs_custom_codes_gallery_allowed_mime_types', $mimes, absint( $user_id ) );
	}

	/** Determine whether the current frontend request can render this feature. */
	private static function is_gallery_context() {
		if ( is_admin() ) {
			return false;
		}

		$is_context = false;
		$page_id    = get_queried_object_id();

		if ( function_exists( 'get_directorist_option' ) ) {
			$gallery_pages = array_filter(
				array(
					absint( get_directorist_option( 'user_dashboard' ) ),
					absint( get_directorist_option( 'author_profile_page' ) ),
				)
			);

			$is_context = $page_id && in_array( $page_id, $gallery_pages, true );
		}

		if ( ! $is_context && $page_id ) {
			$post = get_post( $page_id );

			if ( $post ) {
				$is_context = has_shortcode( $post->post_content, 'directorist_user_dashboard' )
					|| has_shortcode( $post->post_content, 'directorist_author_profile' );
			}
		}

		return (bool) apply_filters( 'directorist_sawjobs_custom_codes_gallery_enqueue_assets', $is_context );
	}

	/**
	 * Build the translated maximum-count error.
	 *
	 * @param int $maximum Maximum image count.
	 * @return string
	 */
	private static function limit_message( $maximum ) {
		return sprintf(
			/* translators: %d: maximum number of gallery images. */
			_n( 'You can upload a maximum of %d gallery image.', 'You can upload a maximum of %d gallery images.', $maximum, 'directorist-sawjobs-custom-codes' ),
			$maximum
		);
	}
}
