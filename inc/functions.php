<?php

/**
 * Add your custom php code here
 */

/**
 * Add Custom Fields
 * 
 */


add_action( 'init', function(){
    new Directorist_Custom_Registration_Field('Legal Business Name', 'business_name');
    new Directorist_Custom_Registration_Field('Legal Business Address', 'business_address', 'textarea');
    new Directorist_Custom_Registration_Field('Category of Business', 'business_category');
    new Directorist_Custom_Registration_Field('How many years in business?', 'business_year', 'number');
    new Directorist_Custom_Registration_Field('Website', 'url', 'url');
    new Directorist_Custom_Registration_Field('TIN/eTIN', 'tin');
} );
