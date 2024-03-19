<?php

/**
 * Add your custom php code here
 */

/**
 * Add Custom Fields
 * 
 */

add_action( 'init', function(){
    new Directorist_Custom_Registration_Field('Company Name', 'company_name');
    new Directorist_Custom_Registration_Field('Vat Number', 'vat_number');
    new Directorist_Custom_Registration_Field('Registration Number', 'registration_number');
    new Directorist_Custom_Registration_Field('Company Address', 'company_address', 'textarea');
} );
