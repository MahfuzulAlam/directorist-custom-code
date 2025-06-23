<?php

/**
 * Add your custom php code here
 */

/**
 * Add Directory Type in the Tags Column
 */
add_filter('manage_edit-at_biz_dir-tags_columns', function($columns) {
    $columns['dir_type'] = 'Directory';
    return $columns;
});

add_filter('manage_at_biz_dir-tags_custom_column', function($out, $column, $term_id) {
    if ($column == 'dir_type') {
        $directory_types = get_term_meta($term_id, 'directory_types', true);
        if ( ! empty( $directory_types ) && is_array( $directory_types ) ) {
            $term_names = [];
            foreach( $directory_types as $directory_type ){
                $directory = get_term( $directory_type, ATBDP_DIRECTORY_TYPE );
                if( $directory ) $term_names[] = $directory->name;
            }
            if( $term_names && ! empty( $term_names ) ) echo implode( ', ', $term_names );
        }
    }
    return $out;
}, 10, 3);


/**
 * Make it draggable
 */

add_action('init', function() {

    add_filter('manage_edit-at_biz_dir-tags_columns', function($columns) {
        $columns['order'] = 'Order';
        return $columns;
    });

    add_filter('manage_at_biz_dir-tags_custom_column', function($out, $column, $term_id) {
        if ($column == 'order') {
            $order = get_term_meta($term_id, 'custom_term_order', true);
            return esc_html($order);
        }
        return $out;
    }, 10, 3);

    add_action('admin_enqueue_scripts', function($hook) {
        if ('edit-tags.php' !== $hook || $_GET['taxonomy'] !== 'at_biz_dir-tags') return;

        wp_enqueue_script('jquery-ui-sortable');
        wp_add_inline_script('jquery-ui-sortable', "
            jQuery(document).ready(function($) {
                var tbody = $('table.wp-list-table tbody');
                tbody.sortable({
                    items: 'tr',
                    cursor: 'move',
                    axis: 'y',
                    update: function(event, ui) {
                        var order = [];
                        tbody.find('tr').each(function(index) {
                            order.push($(this).attr('id').replace('tag-', '') + ':' + index);
                        });
                        $.post(ajaxurl, {
                            action: 'save_term_order',
                            order: order,
                            taxonomy: 'your_taxonomy',
                            _ajax_nonce: '".wp_create_nonce('save_term_order_nonce')."'
                        });
                    }
                });
            });
        ");
    });

    add_action('wp_ajax_save_term_order', function() {
        check_ajax_referer('save_term_order_nonce');

        if (!current_user_can('manage_categories') || empty($_POST['order'])) {
            wp_send_json_error('Permission denied or no data.');
        }

        foreach ($_POST['order'] as $item) {
            list($term_id, $index) = explode(':', $item);
            update_term_meta((int)$term_id, 'custom_term_order', (int)$index);
        }

        wp_send_json_success();
    });

    if( ! get_directorist_option( 'tag_sorting', false ) ) return;

    add_filter('get_terms_args', function ($args, $taxonomies) {
        if (is_admin() && in_array('at_biz_dir-tags', (array) $taxonomies)) {
            $args['meta_key'] = 'custom_term_order';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
        }
        return $args;
    }, 10, 2);

    add_filter('get_terms_orderby', function ($orderby, $args, $taxonomies) {
        if (
            is_admin() &&
            in_array('at_biz_dir-tags', (array) $taxonomies) &&
            isset($args['meta_key']) &&
            $args['meta_key'] === 'custom_term_order'
        ) {
            return 'CAST(wp_termmeta.meta_value AS UNSIGNED)';
        }
        return $orderby;
    }, 10, 3);
});


/**
 * Directory types field
 */

// Add field to 'Add New Tag' form
add_action('at_biz_dir-tags_add_form_fields', function () {
    $directory_types = get_terms([
        'taxonomy'   => ATBDP_DIRECTORY_TYPE,
        'hide_empty' => false,
    ]);
    ?>
    <div class="form-field">
        <label for="directory_types">Directory Types</label>
        <div>
            <?php foreach ($directory_types as $type): ?>
                <label>
                    <input type="checkbox" name="directory_types[]" value="<?php echo esc_attr($type->term_id); ?>">
                    <?php echo esc_html($type->name); ?>
                </label><br>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
});

// Add field to 'Edit Tag' form
add_action('at_biz_dir-tags_edit_form_fields', function ($term) {
    $saved = get_term_meta($term->term_id, 'directory_types', true);
    $saved = is_array($saved) ? $saved : [];
    $directory_types = get_terms([
        'taxonomy'   => ATBDP_DIRECTORY_TYPE,
        'hide_empty' => false,
    ]);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="directory_types">Directory Types</label></th>
        <td>
            <?php foreach ($directory_types as $type): ?>
                <label>
                    <input type="checkbox" name="directory_types[]" value="<?php echo esc_attr($type->term_id); ?>" <?php checked(in_array($type->term_id, $saved)); ?>>
                    <?php echo esc_html($type->name); ?>
                </label><br>
            <?php endforeach; ?>
        </td>
    </tr>
    <?php
});

// Save on add
add_action('created_at_biz_dir-tags', function ($term_id) {
    if (isset($_POST['directory_types']) && is_array($_POST['directory_types'])) {
        update_term_meta($term_id, 'directory_types', array_map('intval', $_POST['directory_types']));
    } else {
        delete_term_meta($term_id, 'directory_types');
    }
});

// Save on edit
add_action('edited_at_biz_dir-tags', function ($term_id) {
    if (isset($_POST['directory_types']) && is_array($_POST['directory_types'])) {
        update_term_meta($term_id, 'directory_types', array_map('intval', $_POST['directory_types']));
    } else {
        delete_term_meta($term_id, 'directory_types');
    }
});

/**
 * Set added term as last order
 */
add_action( 'created_term', function( $term_id, $tt_id, $taxonomy ) {
    if ( 'at_biz_dir-tags' !== $taxonomy ) {
        return;
    }
    // Add the next order value to the newly created term
    add_term_meta( $term_id, 'custom_term_order', 999999, true );
}, 10, 3 );


/**
 * Admin Setting Field
 */
add_filter( 'atbdp_caching_controls', function( $controls ){
    $controls[ 'tag_settings' ] = [
        'title' => __( 'Tag Settings', 'directorist' ),
        'fields'      => [
            'tag_sorting',
        ],
    ];
    return $controls;
} );

add_filter( 'atbdp_listing_type_settings_field_list', function( $fields ) {
    $fields['tag_sorting'] = [
        'type'  => 'toggle',
        'label' => 'Tag Sorting',
        'description' => __( 'Enable tag sorting by order', 'directorist' ),
    ];
    return $fields;
} );