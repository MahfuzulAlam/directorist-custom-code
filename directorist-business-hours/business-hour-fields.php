<?php
if ( ! empty( $field_data[ 'value' ] ) ) extract( $field_data[ 'value' ] ); // extract week days timing
$listing_id = ! empty( $field_data[ 'form' ] ) ? $field_data['form']->get_add_listing_id() : '';
$enable247hour = get_post_meta( $listing_id, '_enable247hour', true ); // extract settings
$disable_bz_hour_listing = get_post_meta( $listing_id, '_disable_bz_hour_listing', true ); // extract settings
$bdbh_version = get_post_meta( $listing_id, '_bdbh_version', true );
$db_zone = get_post_meta($listing_id, '_timezone', true);
if (empty($db_zone)) {
    $db_zone = get_directorist_option('timezone', 'America/New_York');
}
// e_var_dump([
//     'listing_id' => $listing_id,
//     'enable247hour' => $enable247hour,
//     'disable_bz_hour_listing' => $disable_bz_hour_listing,
//     'db_zone' => $db_zone,
// ])
?>
<div class="directorist-bh-wrap">
    <div class="directorist-bh-extras">
        <div class="directorist-bh-extras__active-hour">
            <div class="directorist-checkbox directorist-checkbox-circle directorist-checkbox-theme-admin">
                <input type="checkbox" name="enable247hour" value="1" id="directorist-bh-extras__active-hour" <?php echo (!empty($enable247hour)) ? 'checked' : ''; ?> >
                <label class="directorist-checkbox__label" for="directorist-bh-extras__active-hour"> <?php _e('Open 24 hours 7 days', 'directorist-business-hours'); ?> </label>
                <input type="hidden" name="bdbh_version" id="bdbh_version" value="update" >
            </div>
        </div>
        <div class="directorist-bh-extras__disabled">
            <div class="directorist-checkbox directorist-checkbox-circle directorist-checkbox-theme-admin">
                <input type="checkbox" name="disable_bz_hour_listing" value="hide" id="disable_bz_hour_listing" class="directorist-247-alternative" <?php echo (!empty($disable_bz_hour_listing)) ? 'checked' : ''; ?> >
                <label class="directorist-checkbox__label" for="disable_bz_hour_listing"> <?php _e('Hide business hours', 'directorist-business-hours'); ?> </label>
            </div>
        </div>
        <div class="directorist-bh-extras__selected-hours">
            <div class="directorist-checkbox directorist-checkbox-circle directorist-checkbox-theme-admin">
                <input type="checkbox" name="directorist_bh_option" value="open" id="bh_selected-hours" class="directorist-247-alternative" <?php echo ( empty( $enable247hour ) && empty( $disable_bz_hour_listing ) ) ? 'checked' : ''; ?> >
                <label class="directorist-checkbox__label" for="bh_selected-hours"> <?php _e('Open for Selected Hours', 'directorist-business-hours'); ?> </label>
            </div>
        </div>
    </div>

    <div class="directorist-bh-selection <?php echo ( empty( $enable247hour ) && empty( $disable_bz_hour_listing ) ) ? 'directorist-bh-show' : ''; ?>">
        <div class="directorist-bh-dayzone">
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[monday][enable]" id="ds1" value="enable" <?php echo ( ! empty( $monday['enable'] ) && 'enable' == $monday['enable'] ) ? 'checked' : ''; ?> >
                        <label for="ds1" class="directorist-switch-label"><?php _e( 'Monday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $monday['start'] ) ) {
                    $monday_starts = is_array( $monday['start'] ) ? $monday['start'] : array( $monday['start'] );
                    $monday_closes = is_array( $monday['close'] ) ? $monday['close'] : array( $monday['close'] );
                    foreach( $monday_starts as $index => $monday_start ) {
                        $monday_times[] = array(
                            'start' => $monday_start,
                            'close' => $monday_closes[ $index ]
                        );
                    }
                    foreach( $monday_times as $index => $monday_time  ) {
                    ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                            <?php echo atbdp_hours( 'bdbh[monday][start][' . $index . ']', !empty( $monday_time ['start'] ) ? $monday_time ['start'] : '', 'open' ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                            <?php echo atbdp_hours( 'bdbh[monday][close][' . $index . ']', !empty( $monday_time ['close'] ) ? $monday_time ['close'] : '', 'close' ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>

                    <?php }
                    } else { ?>
                        <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                            <?php echo atbdp_hours( 'bdbh[monday][start][0]', !empty( $monday['start'] ) ? $monday['start'] : '', 'open' ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                            <?php echo atbdp_hours( 'bdbh[monday][close][0]', !empty( $monday['close'] ) ? $monday['close'] : '', 'close' ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                   <?php }
                    ?>

                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[monday][remain_close]" value="open" id="dc1" <?php echo ( ! empty( $monday['remain_close']) && 'open' == $monday['remain_close'] ) ? 'checked' : ''; ?> >
                        <label class="directorist-checkbox__label" for="dc1"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[tuesday][enable]" value="enable" id="ds7" <?php echo ( ! empty( $tuesday['enable']) && 'enable' == $tuesday['enable'] ) ? 'checked' : ''; ?> >
                        <label for="ds7" class="directorist-switch-label"><?php _e( 'Tuesday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                <?php
                    if( ! empty( $tuesday['start'] ) ) {
                    $tuesday_starts = is_array( $tuesday['start'] ) ? $tuesday['start'] : array( $tuesday['start'] );
                    $tuesday_closes = is_array( $tuesday['close'] ) ? $tuesday['close'] : array( $tuesday['close'] );
                    foreach( $tuesday_starts as $index => $tuesday_start ) {
                        $tuesday_times[] = array(
                            'start' => $tuesday_start,
                            'close' => $tuesday_closes[ $index ]
                        );
                    }
                    foreach( $tuesday_times as $index => $tuesday_time  ) {
                    ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                            <div class="directorist-selects directorist-select--start">
                                <?php echo atbdp_hours( 'bdbh[tuesday][start][' . $index . ']', !empty( $tuesday_time['start'] ) ? $tuesday_time['start'] : '', 'open'  ); ?>
                            </div>
                            <div class="directorist-selects directorist-select--close">
                                <?php echo atbdp_hours( 'bdbh[tuesday][close][' . $index . ']', !empty( $tuesday_time['close'] ) ? $tuesday_time['close'] : '', 'close'  ); ?>
                            </div>
                            <div class="directorist-select-copy">
                                <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                            </div>
                        </div>

                    <?php }
                    } else { ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                            <div class="directorist-selects directorist-select--start">
                                <?php echo atbdp_hours( 'bdbh[tuesday][start][0]', !empty( $tuesday['start'] ) ? $tuesday['start'] : '', 'open' ); ?>
                            </div>
                            <div class="directorist-selects directorist-select--close">
                                <?php echo atbdp_hours( 'bdbh[tuesday][close][0]', !empty( $tuesday['close'] ) ? $tuesday['close'] : '', 'close'  ); ?>
                            </div>
                            <div class="directorist-select-copy">
                                <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                            </div>
                        </div>


                    <?php
                    }
                    ?>
                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                    </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[tuesday][remain_close]" value="open" <?php echo ( ! empty( $tuesday['remain_close']) && 'open' == $tuesday['remain_close'] ) ? 'checked' : ''; ?> id="dc7">
                        <label class="directorist-checkbox__label" for="dc7"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[wednesday][enable]" value="enable" <?php echo ( ! empty( $wednesday['enable'] ) && 'enable' == $wednesday['enable'] ) ? 'checked' : ''; ?> id="ds6">
                        <label for="ds6" class="directorist-switch-label"><?php _e( 'Wednesday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $wednesday['start'] ) ) {
                    $wednesday_starts = is_array( $wednesday['start'] ) ? $wednesday['start'] : array( $wednesday['start'] );
                    $wednesday_closes = is_array( $wednesday['close'] ) ? $wednesday['close'] : array( $wednesday['close'] );
                    foreach( $wednesday_starts as $index => $wednesday_start ) {
                        $wednesday_times[] = array(
                            'start' => $wednesday_start,
                            'close' => $wednesday_closes[ $index ]
                        );
                    }
                    foreach( $wednesday_times as $index => $wednesday_time  ) {
                    ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[wednesday][start][' . $index . ']', !empty( $wednesday_time['start'] ) ? $wednesday_time['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[wednesday][close][' . $index . ']', !empty( $wednesday_time['close'] ) ? $wednesday_time['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>

                    <?php }
                    } else { ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[wednesday][start][0]', !empty( $wednesday['start'] ) ? $wednesday['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[wednesday][close][0]', !empty( $wednesday['close'] ) ? $wednesday['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>

                    <?php }
                    ?>

                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[wednesday][remain_close]" value="open" <?php echo ( ! empty( $wednesday['remain_close']) && 'open' == $wednesday['remain_close'] ) ? 'checked' : ''; ?> id="dc2">
                        <label class="directorist-checkbox__label" for="dc2"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[thursday][enable]" value="enable" <?php echo ( ! empty( $thursday['enable'] ) && 'enable' == $thursday['enable'] ) ? 'checked' : ''; ?> id="ds2">
                        <label for="ds2" class="directorist-switch-label"><?php _e( 'Thursday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $thursday['start'] ) ) {

                    $thursday_starts = is_array( $thursday['start'] ) ? $thursday['start'] : array( $thursday['start'] );
                    $thursday_closes = is_array( $thursday['close'] ) ? $thursday['close'] : array( $thursday['close'] );
                    foreach( $thursday_starts as $index => $thursday_start ) {
                        $thursday_times[] = array(
                            'start' => $thursday_start,
                            'close' => $thursday_closes[ $index ]
                        );
                    }
                    foreach( $thursday_times as $index => $thursday_time  ) {
                    ?>
                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[thursday][start][' . $index . ']', !empty( $thursday_time['start'] ) ? $thursday_time['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[thursday][close][' . $index . ']', !empty( $thursday_time['close'] ) ? $thursday_time['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php }

                    } else { ?>
                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[thursday][start][0]', !empty( $thursday['start'] ) ? $thursday['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[thursday][close][0]', !empty( $thursday['close'] ) ? $thursday['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[thursday][remain_close]" value="open" <?php echo ( ! empty( $thursday['remain_close']) && 'open' == $thursday['remain_close'] ) ? 'checked' : ''; ?> id="dc3">
                        <label class="directorist-checkbox__label" for="dc3"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[friday][enable]" value="enable" <?php echo ( ! empty( $friday['enable'] ) && 'enable' == $friday['enable'] ) ? 'checked' : ''; ?> id="ds3">
                        <label for="ds3" class="directorist-switch-label"><?php _e( 'Friday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $friday['start'] ) ) {
                    $friday_starts = is_array( $friday['start'] ) ? $friday['start'] : array( $friday['start'] );
                    $friday_closes = is_array( $friday['close'] ) ? $friday['close'] : array( $friday['close'] );
                    foreach( $friday_starts as $index => $friday_start ) {
                        $friday_times[] = array(
                            'start' => $friday_start,
                            'close' => $friday_closes[ $index ]
                        );
                    }
                    foreach( $friday_times as $index => $friday_time  ) {
                    ?>
                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[friday][start][' . $index . ']', !empty( $friday_time['start'] ) ? $friday_time['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[friday][close][' . $index . ']', !empty( $friday_time['close'] ) ? $friday_time['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php }
                    } else { ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[friday][start][0]', !empty( $friday['start'] ) ? $friday['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[friday][close][0]', !empty( $friday['close'] ) ? $friday['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>

                    <?php }
                    ?>
                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[friday][remain_close]" value="open" <?php echo ( ! empty( $friday['remain_close']) && 'open' == $friday['remain_close'] ) ? 'checked' : ''; ?> id="dc4">
                        <label class="directorist-checkbox__label" for="dc4"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[saturday][enable]" value="enable" <?php echo ( ! empty( $saturday['enable'] ) && 'enable' == $saturday['enable'] ) ? 'checked' : ''; ?> id="ds4">
                        <label for="ds4" class="directorist-switch-label"><?php _e( 'Saturday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $saturday['start'] ) ) {
                    $saturday_starts = is_array( $saturday['start'] ) ? $saturday['start'] : array( $saturday['start'] );
                    $saturday_closes = is_array( $saturday['close'] ) ? $saturday['close'] : array( $saturday['close'] );
                    foreach( $saturday_starts as $index => $saturday_start ) {
                        $saturday_times[] = array(
                            'start' => $saturday_start,
                            'close' => $saturday_closes[ $index ]
                        );
                    }
                    foreach( $saturday_times as $index => $saturday_time  ) {
                    ?>
                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[saturday][start][' . $index . ']', !empty( $saturday_time['start'] ) ? $saturday_time['start'] : '', 'open'  ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[saturday][close][' . $index . ']', !empty( $saturday_time['close'] ) ? $saturday_time['close'] : '', 'close'  ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php }
                    } else { ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[saturday][start][0]', !empty( $saturday['start'] ) ? $saturday['start'] : '' ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[saturday][close][0]', !empty( $saturday['close'] ) ? $saturday['close'] : '' ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>

                    <?php }
                    ?>
                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>

                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[saturday][remain_close]" value="open" <?php echo ( ! empty( $saturday['remain_close']) && 'open' == $saturday['remain_close'] ) ? 'checked' : ''; ?> id="dc5">
                        <label class="directorist-checkbox__label" for="dc5"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
            <div class="directorist-bh-dayzone__single">
                <div class="directorist-bh-dayzone__single--swtich">
                    <div class="directorist-switch directorist-switch-primary">
                        <input type="checkbox" class="directorist-switch-input" name="bdbh[sunday][enable]" value="enable" <?php echo ( ! empty( $sunday['enable']) && 'enable' == $sunday['enable'] ) ? 'checked' : ''; ?> id="ds5">
                        <label for="ds5" class="directorist-switch-label"><?php _e( 'Sunday', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
                <div class="directorist-bh-dayzone__single--choice">
                    <?php
                    if( ! empty( $sunday['start'] ) ) {
                    $sunday_starts = is_array( $sunday['start'] ) ? $sunday['start'] : array( $sunday['start'] );
                    $sunday_closes = is_array( $sunday['close'] ) ? $sunday['close'] : array( $sunday['close'] );
                    foreach( $sunday_starts as $index => $sunday_start ) {
                        $sunday_times[] = array(
                            'start' => $sunday_start,
                            'close' => $sunday_closes[ $index ]
                        );
                    }
                    foreach( $sunday_times as $index => $sunday_time  ) {
                    ?>
                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[sunday][start][' . $index . ']', !empty( $sunday_time['start'] ) ? $sunday_time['start'] : '', 'open' ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[sunday][close][' . $index . ']', !empty( $sunday_time['close'] ) ? $sunday_time['close'] : '', 'close' ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php }
                    }else { ?>

                    <div class="directorist-flex directorist-bh-dayzone__single--choice-wrapper">
                        <div class="directorist-selects directorist-select--start">
                        <?php echo atbdp_hours( 'bdbh[sunday][start][0]', !empty( $sunday['start'] ) ? $sunday['start'] : '' ); ?>
                        </div>
                        <div class="directorist-selects directorist-select--close">
                        <?php echo atbdp_hours( 'bdbh[sunday][close][0]', !empty( $sunday['close'] ) ? $sunday['close'] : '' ); ?>
                        </div>
                        <div class="directorist-select-copy">
                            <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-remove"><?php directorist_icon( 'la la-trash' ); ?></a>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline directorist-select-add"><?php directorist_icon( 'la la-plus' ); ?></a>
                </div>
                <div class="directorist-bh-dayzone__single--hour-selection">
                    <div class="directorist-checkbox">
                        <input type="checkbox" name="bdbh[sunday][remain_close]" value="open" <?php echo ( ! empty( $sunday['remain_close']) && 'open' == $sunday['remain_close'] ) ? 'checked' : ''; ?> id="dc6">
                        <label class="directorist-checkbox__label" for="dc6"><?php _e( 'Open 24 hours', 'directorist-business-hours' ); ?></label>
                    </div>
                </div>
            </div> <!-- End of /.directorist-bh-dayzone__single -->
        </div>
    </div>

    <div class="directorist-bh-timezone">
        <label class="directorist-bh-timezone__label" for="dbh-select-timezone"><?php _e('Timezone','directorist-business-hours');?></label>
        <div class="directorist-select">
            <select id="dbh-select-timezone" name="timezone">
                <?php
                $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                foreach ($timezones as $key => $timezone) {
                    $checked = $timezone === $db_zone ? 'selected' : '';
                    printf('<option value="%s" %s>%s</option>', $timezone, $checked, $timezone);
                }
                ?>
            </select>
        </div>
    </div>
</div><!-- ends: .dbh-wrapper -->


