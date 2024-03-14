<?php

/**
 * @package  Directorist - Statistics
 * 
 */

?>
<div style="margin:50px">
    <!-- <div>
        <h1>Directorist - Import Taxonomies</h1>

        
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
        
    </div> -->
    <div>
        <h1>Directorist - Statistics Overview</h1>
        </br>
        <table class="tg">
        <thead>
            <tr>
                <th class="tg-0lax">Total Visits</th>
                <td class="tg-0lax"><?php echo $data[ 'total' ]; ?></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="tg-0lax">Total Unique Visits</th>
                <td class="tg-0lax"><?php echo $data[ 'unique' ]; ?></td>
            </tr>
        </tbody>
        </table>
    </div>

    <div>
        <h1>Top 10 listings</h1>
        </br>
        <table class="tg">
        <thead>
            <tr>
                <th class="tg-0lax">Listing</th>
                <th class="tg-0lax">Total Views</th>
                <th class="tg-0lax">Unique Views</th>
            </tr>
            </thead>
            <tbody>
            <?php if( $this->top_ten ): ?>
                <?php foreach( $this->top_ten as $listing ): ?>
                <tr>
                    <td class="tg-0lax"><a href="<?php echo $_SERVER['REQUEST_URI'] . '&listing_id=' .$listing->listing ; ?>"><?php echo get_the_title( $listing->listing ); ?></a></td>
                    <td class="tg-0lax"><?php echo $listing->total_count; ?></td>
                    <td class="tg-0lax"><?php echo $listing->new_count; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        </table>
    </div>
</div>