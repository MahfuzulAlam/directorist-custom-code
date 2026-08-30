<?php
/**
 * Directory type ordering settings and helpers.
 *
 * @package Directorist_Reorder_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the shared display order for Directorist directory types.
 */
final class Directorist_Reorder_Types_Directory_Type_Order {

	const OPTION_NAME = 'directorist_reorder_types_order';
	const LEGACY_OPTION_NAME = 'directorist_custom_code_directory_type_order';
	const PAGE_SLUG   = 'directorist-reorder-types';
	const SAVE_ACTION = 'directorist_reorder_types_save_order';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_page' ), 99 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'admin_post_' . self::SAVE_ACTION, array( __CLASS__, 'save_order' ) );
	}

	/**
	 * Add the ordering screen to the Directorist menu.
	 *
	 * @return void
	 */
	public static function add_admin_page() {
		add_submenu_page(
			'edit.php?post_type=at_biz_dir',
			__( 'Directory Type Order', 'directorist-reorder-types' ),
			__( 'Directory Type Order', 'directorist-reorder-types' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_admin_page' )
		);
	}

	/**
	 * Load sortable behavior only on this plugin's admin page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook_suffix ) {
		if ( 'at_biz_dir_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'directorist-reorder-types-admin',
			DIRECTORIST_REORDER_TYPES_URI . 'assets/css/admin-directory-type-order.css',
			array(),
			DIRECTORIST_REORDER_TYPES_VERSION
		);

		wp_enqueue_script(
			'directorist-reorder-types-admin',
			DIRECTORIST_REORDER_TYPES_URI . 'assets/js/admin-directory-type-order.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			DIRECTORIST_REORDER_TYPES_VERSION,
			true
		);
	}

	/**
	 * Return the saved directory type IDs in display order.
	 *
	 * @return int[]
	 */
	public static function get_saved_order() {
		$order = get_option( self::OPTION_NAME, null );

		if ( null === $order ) {
			$order = get_option( self::LEGACY_OPTION_NAME, array() );
		}

		return wp_parse_id_list( $order );
	}

	/**
	 * Sort Directorist template data or WP_Term objects using the saved order.
	 *
	 * Unknown/new directory types retain their relative order and are appended.
	 * Array keys are preserved because Directorist uses term IDs as keys.
	 *
	 * @param array $directory_types Directory type values.
	 * @return array
	 */
	public static function order_directory_types( $directory_types ) {
		if ( ! is_array( $directory_types ) || count( $directory_types ) < 2 ) {
			return $directory_types;
		}

		$saved_order = self::get_saved_order();
		if ( empty( $saved_order ) ) {
			return $directory_types;
		}

		$rank      = array_flip( $saved_order );
		$unranked  = count( $rank );
		$decorated = array();
		$position  = 0;

		foreach ( $directory_types as $key => $directory_type ) {
			$term_id = self::get_term_id( $key, $directory_type );

			$decorated[] = array(
				'key'      => $key,
				'value'    => $directory_type,
				'rank'     => isset( $rank[ $term_id ] ) ? $rank[ $term_id ] : $unranked + $position,
				'position' => $position,
			);

			++$position;
		}

		usort(
			$decorated,
			static function ( $first, $second ) {
				if ( $first['rank'] === $second['rank'] ) {
					return $first['position'] - $second['position'];
				}

				return $first['rank'] - $second['rank'];
			}
		);

		$ordered = array();
		foreach ( $decorated as $item ) {
			$ordered[ $item['key'] ] = $item['value'];
		}

		return $ordered;
	}

	/**
	 * Save a submitted order to the WordPress options table.
	 *
	 * @return void
	 */
	public static function save_order() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage directory type order.', 'directorist-reorder-types' ) );
		}

		check_admin_referer( self::SAVE_ACTION );

		$submitted = isset( $_POST[ self::OPTION_NAME ] )
			? wp_parse_id_list( wp_unslash( $_POST[ self::OPTION_NAME ] ) )
			: array();

		$valid_ids = directorist_get_directories(
			array(
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);

		if ( is_wp_error( $valid_ids ) ) {
			$valid_ids = array();
		}

		$valid_ids = wp_parse_id_list( $valid_ids );
		$order     = array_values( array_intersect( $submitted, $valid_ids ) );

		update_option( self::OPTION_NAME, $order, false );

		$redirect_url = add_query_arg(
			array(
				'post_type' => 'at_biz_dir',
				'page'      => self::PAGE_SLUG,
				'updated'   => '1',
			),
			admin_url( 'edit.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Render the directory type ordering screen.
	 *
	 * @return void
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$directory_types = directorist_get_directories( array( 'hide_empty' => false ) );
		if ( is_wp_error( $directory_types ) ) {
			$directory_types = array();
		}

		$directory_types = self::order_directory_types( $directory_types );
		$ordered_ids     = array();

		foreach ( $directory_types as $directory_type ) {
			$ordered_ids[] = (int) $directory_type->term_id;
		}
		?>
		<div class="wrap directorist-reorder-types">
			<h1><?php esc_html_e( 'Directory Type Order', 'directorist-reorder-types' ); ?></h1>

			<?php if ( isset( $_GET['updated'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['updated'] ) ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Directory type order saved.', 'directorist-reorder-types' ); ?></p></div>
			<?php endif; ?>

			<p><?php esc_html_e( 'Drag and drop the directory types into the order in which they should appear on add listing, archive, and search forms.', 'directorist-reorder-types' ); ?></p>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="<?php echo esc_attr( self::SAVE_ACTION ); ?>">
				<?php wp_nonce_field( self::SAVE_ACTION ); ?>
				<input
					type="hidden"
					id="directorist-reorder-types-order-value"
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>"
					value="<?php echo esc_attr( implode( ',', $ordered_ids ) ); ?>"
				>

				<?php if ( ! empty( $directory_types ) ) : ?>
					<ul id="directorist-reorder-types-order-list" class="directorist-reorder-types__list">
						<?php foreach ( $directory_types as $directory_type ) : ?>
							<li class="directorist-reorder-types__item" data-term-id="<?php echo esc_attr( $directory_type->term_id ); ?>">
								<span class="dashicons dashicons-move" aria-hidden="true"></span>
								<span class="screen-reader-text"><?php esc_html_e( 'Drag to reorder', 'directorist-reorder-types' ); ?></span>
								<strong><?php echo esc_html( $directory_type->name ); ?></strong>
								<span class="directorist-reorder-types__meta">
									<?php
									printf(
										/* translators: %d: Directory type term ID. */
										esc_html__( 'ID: %d', 'directorist-reorder-types' ),
										(int) $directory_type->term_id
									);
									?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php submit_button( __( 'Save Order', 'directorist-reorder-types' ) ); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No directory types were found.', 'directorist-reorder-types' ); ?></p>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Resolve a term ID from the data formats Directorist uses in templates.
	 *
	 * @param mixed $key            Current array key.
	 * @param mixed $directory_type Current array value.
	 * @return int
	 */
	private static function get_term_id( $key, $directory_type ) {
		if ( $directory_type instanceof WP_Term ) {
			return (int) $directory_type->term_id;
		}

		if ( is_array( $directory_type ) && isset( $directory_type['term'] ) && $directory_type['term'] instanceof WP_Term ) {
			return (int) $directory_type['term']->term_id;
		}

		return absint( $key );
	}
}

if ( ! function_exists( 'directorist_reorder_types_order_directory_types' ) ) {
	/**
	 * Apply the configured order to directory type template data.
	 *
	 * @param array $directory_types Directory types to order.
	 * @return array
	 */
	function directorist_reorder_types_order_directory_types( $directory_types ) {
		return Directorist_Reorder_Types_Directory_Type_Order::order_directory_types( $directory_types );
	}
}
