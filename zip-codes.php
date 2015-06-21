<?php
/*
Plugin Name: Zip-codes
Description: This plugin output zip-codes
Version: 1.0b
Author: Dmitriy
*/


/* Debuger */
function pr($data) {
    echo '<pre>';
        print_r($data);
    echo '</pre>';
}


/* Create post-type Zip-codes */
add_action( 'init', 'zip_post_type' );
function zip_post_type() {
  register_post_type( 'zip-code',
    array(
      'labels' => array(
        'name' => 'Zip-codes',
        'singular_name' => 'Zip-code',
        'menu_name'           => 'Zip-codes',
        'parent_item_colon'   => 'Parent Zip-code',
        'all_items'           => 'All Zip-codes',
        'view_item'           => 'View Zip-code',
        'add_new_item'        => 'Add Zip-code',
        'add_new'             => 'Add New',
        'edit_item'           => 'Edit Zip-code',
        'update_item'         => 'Update Zip-code',
        'search_items'        => 'Search Zip-code',
        'not_found'           => 'Not Found',
        'not_found_in_trash'  => 'Not found in Trash',
      ),
        'menu_icon'           => 'dashicons-location',
        'supports'            => array( 'title' ),
        'public' => true,
        'has_archive' => true,
    )
  );
}


/* Add fields to Zip-codes post type */
add_action( 'init', 'add_zip_codes' );
function add_zip_codes() {
    if( function_exists( "register_field_group" ) ) {
        register_field_group(array (
            'id' => 'acf_zip',
            'title' => 'Zip',
            'fields' => array (
                array (
                    'key' => 'zip_state',
                    'label' => 'State',
                    'name' => 'state',
                    'type' => 'select',
                    'choices' => array (
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'zip_city',
                    'label' => 'City',
                    'name' => 'city',
                    'type' => 'select',
                    'choices' => array (
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'zip_zip',
                    'label' => 'Zip',
                    'name' => 'zip',
                    'type' => 'select',
                    'choices' => array (
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'zip-code',
                        'order_no' => 0,
                        'group_no' => 0,
                    ),
                ),
            ),
            'options' => array (
                'position' => 'normal',
                'layout' => 'default',
                'hide_on_screen' => array (
                ),
            ),
            'menu_order' => 0,
        ));
    }
}


/* Create table  */
register_activation_hook( __FILE__, 'zip_install' );

global $zip_db_version;
$zip_db_version = "1.0";

function zip_install () {
    global $wpdb;
    global $zip_db_version;

    $table_name = $wpdb->prefix . "zipcodes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

    $sql = "CREATE TABLE " . $table_name . " (
        `zip` char(5) NOT NULL,
        `city` varchar(64) DEFAULT NULL,
        `state` char(2) DEFAULT NULL,
        `latitude` float DEFAULT NULL,
        `longitude` float NOT NULL,
        `timezone` int(11) DEFAULT NULL,
        `dst` int(20) NOT NULL,
        PRIMARY KEY (`zip`)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    }

    $file_db = plugins_url('zip-codes/dump_db.sql');
    $sql_insert = file($file_db);
    $rows_affected = $wpdb->query( $sql_insert[0] );
}


/* Add styles */
add_action( 'admin_enqueue_scripts', 'add_zip_codes_styles' );
function add_zip_codes_styles() {
    $path_to_style = plugins_url('zip-codes/css/style.css');

    wp_enqueue_style( 'zip-codes-styles', $path_to_style  );
}


/* Add Ajax and select States from db */
add_filter('acf/load_field/name=state', 'acf_load_zip_field_choices');
function acf_load_zip_field_choices( $field ) {
    global $wpdb;

    $data = array('-- Select State --');
    $states = $wpdb->get_results( 'SELECT DISTINCT state FROM wp_zipcodes' );

    if ($states) {
        foreach ($states as $state) {
            $data[$state->state] = $state->state;
        }    
    }

    $field['choices'] = $data;
    wp_enqueue_script( 'acf-load-codes', plugins_url('zip-codes/js/acf-load-zip-codes.js') );

    return $field;
}


/* Select cities from db */
add_action( 'wp_ajax_addCities', 'prefix_ajax_addCities' );
add_action( 'wp_ajax_nopriv_addCities', 'prefix_ajax_addCities' );
function prefix_ajax_addCities() {
    global $wpdb;
    $currentState = $_POST['state'];

    $data = array();
    $cities = $wpdb->get_results( 'SELECT DISTINCT city FROM wp_zipcodes WHERE state = "' . $currentState . '"' );

    if ($cities) {
        foreach ($cities as $city) {
            $data[$city->city] = $city->city;
        }    
    }

    print_r($data);
    exit;
}