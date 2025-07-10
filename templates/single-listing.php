<?php

/**
 * @author  wpWax
 * @since   2.0
 * @version 2.0
 */

if (! defined('ABSPATH')) exit;

$class = ($reviews && count($reviews) > 0) ? 'has-review' : 'no-review';
?>
<div class="google-reviews <?php echo $class; ?>">
    <?php
    foreach (array_slice($reviews, 0, 5) as $r):
        $stars = str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']);
    ?>
        <div class="review">
            <p class="review-title"><strong><?php echo $r['author_name']; ?></strong> <span class="stars"><?php echo $stars; ?></span></p>
            <p class="review-description"><?php echo $r['text']; ?></p>
        </div>
    <?php
    endforeach;
    ?>
</div>