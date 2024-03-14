<?php

/**
 * @package  Directorist - Statistics
 * 
 */

if( ! $data[ 'listing_id' ] ) exit;

?>

<div style="margin:50px">
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