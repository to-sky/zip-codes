
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

    // $sql = file_get_contents(ABSPATH . 'wp-content/plugins/plugin-name/sql.sql');
    // dbDelta($sql);



    
    
    // if(is_readable($file_db)) {
    //     echo 'File not found or not readable '.$file_db;
    // }

    // $sql_insert = $wp_filesystem->get_contents( $file_db );

    // echo $sql_insert;
    // exit;

    // $rows_affected = $wpdb->query( $sql_insert );


    // $sql_insert = "INSERT INTO $table_name ( zip, city, state ) ";
    // $sql_insert .= $wp_filesystem->get_contents( $file_db );
    // $rows_affected = $wpdb->query( $sql_insert );
// }



    // $rows_affected = $wpdb->insert( $table_name, array( 'zip' => '00210', 'city' => 'Portsmouth', 'state' => 'NH' ) );

    // add_option("zip_db_version", $zip_db_version);
    }

    // $rows_affected = $wpdb->insert( $table_name, array( 'zip' => '00210', 'city' => 'Portsmouth', 'state' => 'NH' ) );

    // add_option("zip_db_version", $zip_db_version);

    // $file_db = plugins_url('zip-codes/wpm_zip.sql');
    // $sql_data = fopen($file_db, "r");

    // $test = fgets($sql_data);



    $rows_affected = $wpdb->query();

    // $sql_insert = file($file_db);

    //$test = "INSERT INTO `wp_zipcodes` (`zip`, `city`, `state`, `latitude`, `longitude`, `timezone`, `dst`) VALUES ('00210', 'Portsmouth', 'NH', 43.0059, -71.0132, -5, 1)";
    //print_r($sql_insert[0]);
    // $rows_affected = $wpdb->query( $sql_insert[0] );
}





function db_data() {
    WP_Filesystem();
    global $wp_filesystem;
    global $wpdb;
    echo 'Current Path is '. __DIR__;
    $file = plugins_url('zip-codes/wpm_zip.sql');;
    if(is_readable($file)) {
        echo 'File not found or not readable '.$file;
    }     
    $table_name = $wpdb->prefix . "tableName";

    $sql_insert = "INSERT INTO $table_name ( zip, city, state ) ";
    $sql_insert .= $wp_filesystem->get_contents( $file );
    $rows_affected = $wpdb->query( $sql_insert );
}