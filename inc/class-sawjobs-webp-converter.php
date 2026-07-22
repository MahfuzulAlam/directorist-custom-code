<?php
/**
 * WebP conversion service for frontend gallery uploads.
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Converts a validated uploaded image into a temporary WebP file.
 */
final class Directorist_Sawjobs_Custom_Codes_WebP_Converter {

	/** WebP MIME type. */
	private const MIME_TYPE = 'image/webp';

	/** Default WebP compression quality. */
	private const DEFAULT_QUALITY = 82;

	/**
	 * Convert an uploaded image file array to WebP.
	 *
	 * The returned temporary file is intended for media_handle_sideload().
	 * Already-WebP uploads are returned unchanged apart from normalized metadata.
	 *
	 * @param array  $file       A single file array from $_FILES.
	 * @param string $input_mime Validated input MIME type.
	 * @param int    $user_id    User performing the upload.
	 * @return array|WP_Error Converted file array or an error.
	 */
	public static function convert( $file, $input_mime, $user_id ) {
		if ( ! is_array( $file ) || empty( $file['tmp_name'] ) || empty( $file['name'] ) ) {
			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_missing_file',
				__( 'The image could not be prepared for WebP conversion.', 'directorist-sawjobs-custom-codes' )
			);
		}

		$source_path = $file['tmp_name'];
		$input_mime  = sanitize_mime_type( $input_mime );
		$user_id     = absint( $user_id );

		if ( ! is_readable( $source_path ) ) {
			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_unreadable_file',
				__( 'The uploaded image could not be read.', 'directorist-sawjobs-custom-codes' )
			);
		}

		$webp_filename = self::webp_filename( $file['name'] );

		if ( self::MIME_TYPE === $input_mime ) {
			$file['name'] = $webp_filename;
			$file['type'] = self::MIME_TYPE;
			$file['size'] = filesize( $source_path );

			return $file;
		}

		$editor = wp_get_image_editor(
			$source_path,
			array(
				'mime_type'        => $input_mime,
				'output_mime_type' => self::MIME_TYPE,
			)
		);

		if ( is_wp_error( $editor ) ) {
			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_editor_unavailable',
				__( 'This server cannot convert images to WebP.', 'directorist-sawjobs-custom-codes' ),
				$editor
			);
		}

		$quality        = self::get_quality( $user_id, $file );
		$quality_result = $editor->set_quality( $quality );

		if ( is_wp_error( $quality_result ) ) {
			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_quality_error',
				__( 'The WebP image quality could not be configured.', 'directorist-sawjobs-custom-codes' ),
				$quality_result
			);
		}

		$destination_directory = dirname( $source_path );
		$destination_filename  = wp_unique_filename( $destination_directory, $webp_filename );
		$destination_path      = trailingslashit( $destination_directory ) . $destination_filename;
		$saved                 = $editor->save( $destination_path, self::MIME_TYPE );

		if ( is_wp_error( $saved ) ) {
			self::delete_temporary_file( $destination_path );

			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_conversion_failed',
				__( 'The image could not be converted to WebP.', 'directorist-sawjobs-custom-codes' ),
				$saved
			);
		}

		$converted_path = isset( $saved['path'] ) ? $saved['path'] : '';
		$converted_mime = isset( $saved['mime-type'] ) ? $saved['mime-type'] : '';

		if ( self::MIME_TYPE !== $converted_mime || ! $converted_path || ! is_readable( $converted_path ) ) {
			self::delete_temporary_file( $converted_path ? $converted_path : $destination_path );

			return new WP_Error(
				'directorist_sawjobs_custom_codes_gallery_webp_invalid_output',
				__( 'The image editor did not create a valid WebP image.', 'directorist-sawjobs-custom-codes' )
			);
		}

		if ( wp_normalize_path( $source_path ) !== wp_normalize_path( $converted_path ) ) {
			self::delete_temporary_file( $source_path );
		}

		$converted_file = array(
			'name'     => basename( $converted_path ),
			'type'     => self::MIME_TYPE,
			'tmp_name' => $converted_path,
			'error'    => UPLOAD_ERR_OK,
			'size'     => isset( $saved['filesize'] ) ? absint( $saved['filesize'] ) : filesize( $converted_path ),
		);

		do_action( 'directorist_sawjobs_custom_codes_gallery_image_converted_to_webp', $converted_file, $file, $user_id, $quality );

		return $converted_file;
	}

	/**
	 * Delete a generated temporary conversion file when later processing fails.
	 *
	 * @param array $file Converted file array.
	 * @return void
	 */
	public static function cleanup( $file ) {
		if ( is_array( $file ) && ! empty( $file['tmp_name'] ) ) {
			self::delete_temporary_file( $file['tmp_name'] );
		}
	}

	/**
	 * Get filterable WebP compression quality.
	 *
	 * @param int   $user_id User ID.
	 * @param array $file    Original upload file data.
	 * @return int
	 */
	private static function get_quality( $user_id, $file ) {
		/**
		 * Filter gallery WebP compression quality.
		 *
		 * @param int   $quality WebP quality between 1 and 100. Default 82.
		 * @param int   $user_id User ID.
		 * @param array $file    Original upload file data.
		 */
		$quality = apply_filters( 'directorist_sawjobs_custom_codes_gallery_webp_quality', self::DEFAULT_QUALITY, $user_id, $file );

		return min( 100, max( 1, absint( $quality ) ) );
	}

	/**
	 * Build a safe WebP filename from an uploaded filename.
	 *
	 * @param string $filename Original filename.
	 * @return string
	 */
	private static function webp_filename( $filename ) {
		$filename = sanitize_file_name( $filename );
		$basename = pathinfo( $filename, PATHINFO_FILENAME );
		$basename = $basename ? $basename : 'gallery-image';

		return $basename . '.webp';
	}

	/**
	 * Delete a known temporary file.
	 *
	 * @param string $path Temporary file path.
	 * @return void
	 */
	private static function delete_temporary_file( $path ) {
		if ( is_string( $path ) && '' !== $path && is_file( $path ) ) {
			wp_delete_file( $path );
		}
	}
}
