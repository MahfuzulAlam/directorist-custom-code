<?php

/**
 * @author  wpWax
 * @since   2.0
 * @version 2.0
 */

if (! defined('ABSPATH')) exit;

$value = isset($_REQUEST['custom_field']['author_search']) && ! empty($_REQUEST['custom_field']['author_search']) ? $_REQUEST['custom_field']['author_search'] : '';

$authors = new \Directorist\Directorist_All_Authors;
?>

<div class="directorist-search-field directorist-form-group">

    <div class="directorist-select directorist-search-field__input">

        <?php if (! empty($data['label'])) : ?>
            <label class="directorist-search-field__label"><?php echo esc_attr($data['label']); ?></label>
        <?php endif; ?>

        <select name="custom_field[<?php echo esc_attr($data['widget_name']); ?>]" data-isSearch="true" data-placeholder="<?php echo $data['label']; ?>" id="author_search">

            <option value=""><?php echo $data['label']; ?></option>

            <?php
            foreach ($authors->author_list() as $author) {
            ?>
                <option value="<?php echo $author->data->ID; ?>" <?php echo selected($value, $author->data->ID); ?>>
                    <?php echo $author->data->user_nicename; ?>
                </option>
            <?php
            }
            ?>

        </select>

    </div>

    <div class="directorist-search-field__btn directorist-search-field__btn--clear">
        <?php directorist_icon('fas fa-times-circle'); ?>
    </div>

</div>