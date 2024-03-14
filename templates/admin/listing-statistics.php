<?php

/**
 * @package  Directorist - Statistics
 * 
 */

if( ! $data[ 'listing_id' ] ) exit;

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
            <h1>Single Listing - Statistics Overview</h1>
            <h2><?php echo $data['listing_title']; ?></h2>
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
    </div>
</div>