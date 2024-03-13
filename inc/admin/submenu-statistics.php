<?php

/**
 * @package  Directorist - Statistics
 * 
 */


// Import CSV
if (isset($_POST['taxonomy_import']) && isset($_POST['taxonomy_name'])) {
    // File extension
    $extension = pathinfo($_FILES['taxonomy_file']['name'], PATHINFO_EXTENSION);
    $taxonomy_name = isset($_POST['taxonomy_name']) && !empty($_POST['taxonomy_name']) ? $_POST['taxonomy_name'] : 'at_biz_dir-category';

    // If file extension is 'csv'
    if (!empty($_FILES['taxonomy_file']['name']) && $extension == 'csv') {
        // Open file in read mode
        $csvFile = fopen($_FILES['taxonomy_file']['tmp_name'], 'r');
        directorist_save_uploaded_taxonomy_from_csv($taxonomy_name, $csvFile);
    }
}

?>
<div style="margin:50px">
    <div>
        <h1>Directorist - Import Taxonomies</h1>

        <!-- Form -->
        <form method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            </br>
            <label for="taxonomy_name">Taxonomy Name</Label></br>
            <select name="taxonomy_name" id="taxonomy_name">
                <option value="at_biz_dir-category" selected>Category</option>
                <option value="at_biz_dir-location">Location</option>
            </select>
            </br>
            </br>
            <label for="taxonomy_file">Import CSV File</Label></br>
            <input type="file" name="taxonomy_file" id="taxonomy_file" accept=".csv">
            </br>
            </br>
            <input type="submit" name="taxonomy_import" value="Import" class="button button-primary">
        </form>
        <!-- Form -->
    </div>
    <div>

        </br>
        </br>
        <h1>Directorist - Export Taxonomies</h1>
        </br>
        <a href="<?php echo admin_url('edit.php?post_type=at_biz_dir&page=taxonomy-export-import') ?>&action=download_csv&taxonomy=at_biz_dir-category&_wpnonce=<?php echo wp_create_nonce('download_csv') ?>" class="button button-primary"><?php _e('Export Categories', 'my-plugin-slug'); ?></a>
        <a href="<?php echo admin_url('edit.php?post_type=at_biz_dir&page=taxonomy-export-import') ?>&action=download_csv&taxonomy=at_biz_dir-location&_wpnonce=<?php echo wp_create_nonce('download_csv') ?>" class="button button-primary"><?php _e('Export Locations', 'my-plugin-slug'); ?></a>
    </div>
</div>