<?php

/**
 * @package  Directorist - Statistics
 * 
 */

?>
<div style="margin:50px">
    <div>
        <form method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            <select name="stat_date_time" id="stat_time">
                <option value="">Select an Option</option>
                <option value="today" <?php selected( $_POST['stat_date_time'], 'today' ); ?>>Today</option>
                <option value="yesterday" <?php selected( $_POST['stat_date_time'], 'yesterday' ); ?>>Yesterday</option>
                <option value="cur_month" <?php selected( $_POST['stat_date_time'], 'cur_month' ); ?>>This Month</option>
                <option value="prev_month" <?php selected( $_POST['stat_date_time'], 'prev_month' ); ?>>Last Month</option>
                <option value="cur_year" <?php selected( $_POST['stat_date_time'], 'cur_year' ); ?>>This Year</option>
                <option value="prev_year" <?php selected( $_POST['stat_date_time'], 'prev_year' ); ?>>Last Year</option>
            </select>
            <input type="submit" name="stat_date_time_submit" value="Search" class="button button-primary">
        </form>
        
    </div>
    <div class="statistics-container">
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
</div>