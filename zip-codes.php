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


/* Add styles */
add_action( 'admin_enqueue_scripts', 'add_zip_codes_styles' );
function add_zip_codes_styles() {
    $path_to_style = plugins_url('zip-codes/css/style.css');

    wp_enqueue_style( 'zip-codes-styles', $path_to_style  );
}


/* Add scripts */
add_action( 'admin_enqueue_scripts', 'add_zip_codes_js' );
function add_zip_codes_js() {
    $path_to_style = plugins_url('zip-codes/js/acf-load-zip-codes.js');

    wp_enqueue_script( 'zip-codes-js', $path_to_style  );
}


/* Create menu item Zip-codes */
add_action('admin_menu', 'create_zip_codes_menu');
function create_zip_codes_menu() {
    add_options_page('Zip-codes', 'Zip-codes', 'manage_options', 'optionZipCodes', 'pluginSettings');
    add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
    //register our settings
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

// global $post;
// add_action('admin_init', 'test');
// function test() {
//     global $post;
//     print_r($post);
//     print_r($post->post_type);
//     print_r(get_the_ID());
//     exit;
// }

// function () {
    // $currentPostType = get_the_id();

//     if (current_post_type == get_option('post_type')) {
//         echo '<select>';
//         echo '<select>';
//         echo '<select>';    
//     }
    
// }

/* Add fields to Zip-codes post type */
add_action( 'init', 'add_zip_codes' );
function add_zip_codes() {
    if( function_exists( "register_field_group" ) ) {
        register_field_group(array (
            'id' => 'acf_zip',
            'title' => 'Add Zip-code',
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
                        'value' => get_option('post_type'),
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

    $file_db = plugins_url('zip-codes/wpm_zip.sql');
    $input_data_to_table = file_get_contents($file_db);
    $rows_affected = $wpdb->query( $input_data_to_table );
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
    $selected = '';
    $currentState = $_POST['state'];

    $cities = $wpdb->get_results( 'SELECT DISTINCT city FROM wp_zipcodes WHERE state = "' . $currentState . '"' );

    echo '<option value="0">-- Select City --</option>';

    if ($cities) {
        foreach ($cities as $city) {
            if (get_field('fields[zip_city]') == $city->city) {
                $selected = 'selected="selected"';
            }

            echo '<option value="' . $city->city . '" ' . $selected . '>' . $city->city . '</option>';
        }
    }

    wp_die();
}


/* Select zip from db */
add_action( 'wp_ajax_addZip', 'prefix_ajax_addZip' );
add_action( 'wp_ajax_nopriv_addZip', 'prefix_ajax_addZip' );
function prefix_ajax_addZip() {
    global $wpdb;
    $currentState = $_POST['city'];

    echo '<option value="0">-- Select Zip --</option>';

    $zips = $wpdb->get_results( 'SELECT DISTINCT zip FROM wp_zipcodes WHERE city = "' . $currentState . '"' );

    if ($zips) {
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


/* Add Zip-codes rows to post */
add_action('add_meta_boxes', 'add_zips_blocks_to_post');
function add_zips_blocks_to_post() {
    add_meta_box( 'savesZip', 'Your zip-codes', 'get_data_from_db', 'post' );

}

/* Get data from db */
function get_data_from_db() {
    $currentPost = get_the_id();
    $zipFields = get_post_meta($currentPost, 'zipFields');

    if ($zipFields[0]) {
        foreach ($zipFields[0] as $key => $value) {
            $draw_state_block = '<div class="row"><label class="name-tag">State</label><input type="text" name="state" class="value-tag" value="' . $value['state'] . '" readonly></div>';
            $draw_city_block = '<div class="row"><label class="name-tag">City</label><input type="text" name="city" class="value-tag" value="' . $value['city'] . '" readonly></div>';
            $draw_zip_block = '<div class="row"><label class="name-tag">Zip</label><input type="text" name="zip" class="value-tag" value="' . $value['zip'] . '" readonly></div>';

            echo "<div>" . $draw_state_block . $draw_city_block . $draw_zip_block . "<div>";
        }
    }
}