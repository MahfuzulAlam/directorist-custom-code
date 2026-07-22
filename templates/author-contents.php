<?php
/**
 * Directorist author profile contents with the custom user gallery.
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="directorist-wrapper directorist-author-profile-content directorist-w-100">

	<div class="<?php Helper::directorist_container_fluid(); ?>">
		<?php
		$author->header_template();
		$author->about_template();
		Directorist_Sawjobs_Custom_Codes_User_Gallery::render_author_gallery( $author );
		$author->author_listings_template();
		?>
	</div>

</div>
