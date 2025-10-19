<?php

/**
 * Add your custom php code here
 */


add_action( 'init', function(){
    new Directorist_Custom_Registration_Field('Company Name', 'company_name', 'text');
    new Directorist_Custom_Registration_Field('Vat Number', 'vat_number', 'text');
} );