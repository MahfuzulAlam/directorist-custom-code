<?php

/**
 * @package  Directorist - Statistics
 * 
 */

?>

<div style="margin:50px">
    <div>
        <form method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <span></span> <i class="fa fa-caret-down"></i>
                <input type="hidden" name="start_date" />
                <input type="hidden" name="end_date" />
            </div>
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