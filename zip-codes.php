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


/* Add styles and js */
add_action( 'admin_enqueue_scripts', 'add_styles_js' );
function add_styles_js() {
    $path_to_style = plugins_url('zip-codes/css/style.css');
    $path_to_js = plugins_url('zip-codes/js/zip-codes.js');

    wp_enqueue_style( 'zip-codes-styles', $path_to_style  );
    wp_enqueue_script( 'zip-codes-js', $path_to_js  );
}


/* Create menu item Zip-codes */
add_action('admin_menu', 'create_zip_codes_menu');
function create_zip_codes_menu() {
    add_options_page('Zip-codes', 'Zip-codes', 'manage_options', 'optionZipCodes', 'pluginSettings');
    add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
    register_setting( 'baw-settings-group', 'selected_post_type' );
}


/* Add plugin settings */
function pluginSettings() {
    echo '<h1>Hello!</h1>';
    echo '<h2>This plugin to select the Zip-code for the state and city.</h2>';
?>
    <form method="post" action="options.php">
        <?php 
            settings_fields( 'baw-settings-group' ); 
            $postTypes = get_post_types( '', 'names' );
        ?>
        <label>Select post type: </label><select id="postTypes" name="postType">
        <?php
            if ($postTypes) {
                foreach ($postTypes as $postType) {                    
                    if ( $postType == get_option('selected_post_type') ) {
                        echo '<option name="selected_post_type" selected="selected">' . $postType;
                        continue;
                    } 
                    echo '<option name="selected_post_type">' . $postType;
                }
                echo '<input id="hiddenPostypes" type="hidden" name="selected_post_type" value=""/>';
            }
        ?>
        </select>
        <input type="submit" class="save-select button-primary" value="<?php _e('Save Changes') ?>" />
    </form>
<?php
}


/* Create table wp_zipcodes in db */
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

    $file_db = plugins_url('zip-codes/wpm_zip.sql');
    $input_data_to_table = file_get_contents($file_db);
    $rows_affected = $wpdb->query( $input_data_to_table );

    wp_die();
}


/* Add Zip-codes rows to post */
add_action('add_meta_boxes', 'add_zips_blocks_to_post');
function add_zips_blocks_to_post() {
    add_meta_box( 'savesZip', 'Your zip-codes', 'get_data_from_db', get_option('selected_post_type') );
}


/* Get states from db */
function get_data_from_db() {
    global $wpdb;
    $currentPost = get_the_id();
    $zipFields = get_post_meta($currentPost, 'zipFields');

    $data = array('-- Select State --');

    $states = $wpdb->get_results( 'SELECT DISTINCT state  FROM wp_zipcodes' );

    ?>
    <div class="wrap-selects">
        <div class="select-item">
            <label>
                <i>State</i>
                <select id="field-state">
                    <?php
                        if ($states) {
                                echo '<option value="0">-- Select State --</option>';
                            foreach ($states as $state) {
                                echo '<option value="' . $state->state . '">' . $state->state . '</option>';
                            }
                        }
                    ?>
                </select>
            </label>
        </div>
    <?php

        echo '<div class="select-item"><div class="wait"></div><label><i>City</i><select id="field-city"><option value="0">-- Select City --</option></select></label></div>';
        echo '<div class="select-item"><label><i>Zip</i><select id="field-zip"><option value="0">-- Select Zip --</option></select></label></div>';
    echo '</div>';
    echo '<div class="zip-items">';    
        if ($zipFields[0]) {
            foreach ($zipFields[0] as $key => $value) {
                $draw_state_block = '<div class="row"><label class="name-tag">State</label><input type="text" name="state" class="value-tag" value="' . $value['state'] . '" readonly><input type="hidden" name="data[' . $value['zip'] . '][state]" value="' . $value['state'] . '"></div>';
                $draw_city_block = '<div class="row"><label class="name-tag">City</label><input type="text" name="city" class="value-tag" value="' . $value['city'] . '" readonly><input type="hidden" name="data[' . $value['zip'] . '][city]" value="' . $value['city'] . '"></div>';
                $draw_zip_block = '<div class="row"><label class="name-tag">Zip</label><input type="text" name="zip" class="value-tag" value="' . $value['zip'] . '" readonly><input type="hidden" name="data[' . $value['zip'] . '][zip]" value="' . $value['zip'] . '"></div>';
                $draw_btn = '<button type="button" id="delSavedRow" class="del-row button button-primary button-large">Delete</button>';

                echo '<div class="zip-row clearfix">' . $draw_state_block . $draw_city_block . $draw_zip_block . $draw_btn . '</div>';
            }
        }
        wp_die();
    echo '</div>';

}


/* Get cities from db */
add_action( 'wp_ajax_addCities', 'prefix_ajax_addCities' );
add_action( 'wp_ajax_nopriv_addCities', 'prefix_ajax_addCities' );
function prefix_ajax_addCities() {
    global $wpdb;
    $currentState = $_POST['state'];

    $cities = $wpdb->get_results( 'SELECT DISTINCT city FROM wp_zipcodes WHERE state = "' . $currentState . '"' );

    if ($cities) {
            echo '<option value="0">-- Select City --</option>';
        foreach ($cities as $city) {
            echo '<option value="' . $city->city . '" ' . $selected . '>' . $city->city . '</option>';
        }
    }
    wp_die();
}


/* Get zip from db */
add_action( 'wp_ajax_addZip', 'prefix_ajax_addZip' );
add_action( 'wp_ajax_nopriv_addZip', 'prefix_ajax_addZip' );
function prefix_ajax_addZip() {
    global $wpdb;
    $currentState = $_POST['city'];

    $zips = $wpdb->get_results( 'SELECT DISTINCT zip FROM wp_zipcodes WHERE city = "' . $currentState . '"' );

    if ($zips) {
            echo '<option value="0">-- Select Zip --</option>';
        foreach ($zips as $zip) {
            echo '<option value="' . $zip->zip . '">' . $zip->zip . '</option>';
        }
    }
    wp_die();
}


/* Save zip meta*/
add_action( 'save_post', 'save_zip_meta', 10, 3 );
function save_zip_meta( $post_id, $post, $update ) {
    $zipFields = $_REQUEST['data'];

    update_post_meta( $post_id, 'zipFields', $zipFields );
}


/* Cleaning after delete plugin */
register_deactivation_hook( __FILE__, 'clean_after_deactivate' );
function clean_after_deactivate( ) {
    $insdb = get_option( 'zipFields' );
    $sPt = get_option('selected_post_type');

    delete_option( 'zipFields' );
    delete_option( 'selected_post_type' );
}