<?php
/*
Plugin Name: Zip-codes
Description: This plugin output zip-codes
Version: 1.0b
Author: Dmitriy
*/


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
        'add_new_item'        => 'Add New Zip-code',
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

?>