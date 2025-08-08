<?php

/**
 * Add your custom php code here
 */

/**
 * Add Custom Fields
 * 
 */

// add_action( 'init', function(){
//     new Directorist_Custom_Registration_Field('Company Name', 'company_name');
//     new Directorist_Custom_Registration_Field('Vat Number', 'vat_number');
//     new Directorist_Custom_Registration_Field('Registration Number', 'registration_number', 'number');
//     new Directorist_Custom_Registration_Field('Company Address', 'company_address', 'textarea');
//     new Directorist_Custom_Registration_Field('Document', 'document', 'file');
// } );

add_action( 'init', function(){
    new Directorist_Custom_Registration_Field('Contact number', 'contact_number');
    new Directorist_Custom_Registration_Field('Profile Image', 'pro_pic', 'file');
} );