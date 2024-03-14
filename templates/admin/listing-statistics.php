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
            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <span></span> <i class="fa fa-caret-down"></i>
                <input type="hidden" name="start_date" value="<?php echo isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ] : ''; ?>"/>
                <input type="hidden" name="end_date" value="<?php echo isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ] : ''; ?>"/>
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