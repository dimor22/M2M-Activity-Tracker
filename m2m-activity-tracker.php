<?php
/*
Plugin Name:  M2M Activity Tracker
Plugin URI:   http://dlopez.xyz/plugins/m2m-activity-tracker
Description:  Simple wordpress plugin that keeps track of the activity between ward members and ward missionaries
Version:      1.0
Author:       David Lopez Gamez
Author URI:   http://dlopez.xyz
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/



/** CREATE TABLE */
global $mmat_db_version;
$mmat_db_version = '1.0';

function mmat_install() {
    global $wpdb;
    global $mmat_db_version;

    $table_people = $wpdb->prefix . 'mmat_people';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = [];

    $sql[] = "CREATE TABLE $table_people (
        name varchar(100) NOT NULL,
        phone varchar(20) DEFAULT '',
        email varchar(100) DEFAULT '',
		id int(11) NOT NULL AUTO_INCREMENT,
		PRIMARY KEY  (id),
		UNIQUE KEY  (phone),
		UNIQUE KEY  (email)
	) $charset_collate;";

    $table_people_activity = $wpdb->prefix . 'mmat_people_activity';

    $sql[] = "CREATE TABLE $table_people_activity (
        id int(11) NOT NULL AUTO_INCREMENT,
        people_id int(11) NOT NULL,
        activity_id int(11) NOT NULL,
        with_friend int(1) NOT NULL,
        friend_name varchar(100) NOT NULL,
        date varchar(15) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

    $table_activity = $wpdb->prefix . 'mmat_activity';

    $sql[] = "CREATE TABLE $table_activity (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

    /* INSERTS */

    $sql[] = "INSERT INTO $table_activity (id, name) VALUES (NULL, 'Visita'), (NULL, 'Noche de Hogar'), (NULL, 'Comida');";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'mmat_db_version', $mmat_db_version );
}
register_activation_hook( __FILE__, 'mmat_install' );

/** STYLES AND SCRIPTS */
function callback_for_setting_up_scripts() {
    wp_register_style( 'm2m-activity-tracker-css', plugins_url('css/main.css',__FILE__ ) );
    wp_enqueue_style( 'm2m-activity-tracker-css' );

    wp_register_script( 'm2m-activity-tracker-js', plugins_url('js/main.js',__FILE__ ));

    wp_register_script( 'sortable', plugins_url('js/sorttable.js',__FILE__ ));

    wp_localize_script( 'm2m-activity-tracker-js', 'mmat_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );

    wp_enqueue_script('m2m-activity-tracker-js', null, array('jquery'), null, true);
    wp_enqueue_script('sortable', null, array('jquery'), null, true);

    wp_register_style( 'Animate_css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css' );
    wp_enqueue_style('Animate_css');
}
add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');


/** ADMIN MENU */
function my_plugin_menu() {
    add_options_page( 'M2M Activity Tracker', 'M2MA Tracker', 'manage_options', 'm2m-activity-tracker', 'my_plugin_options' );
}
function my_plugin_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<ul><li><p><strong>Añade</strong>  este shortcode a la pagina donde quieras tener el formulario para anadir familias [addpeople]</p></li><li><p><strong>Añade</strong>  este shortcode a la pagina donde quieras anadir activades [addactivities]</p></li></ul>';
    echo '</div>';
}
add_action( 'admin_menu', 'my_plugin_menu' );


/**
 * Add a custom link to the end of a specific menu that uses the wp_nav_menu() function
 */
add_filter('wp_nav_menu_items', 'add_admin_link', 1, 2);
function add_admin_link($items, $args){
    $redirect_page = get_home_url();
    if ( get_page_by_path('show-list') != NULL ) {
        $redirect_page .= '/show-list';
    }
    if ( is_user_logged_in() ) {
        $items .= '<li><a title="Admin" href="'. wp_logout_url( get_home_url() ) .'">Salir</a></li>';
    } else {
        $items .= '<li><a title="Admin" href="'. wp_login_url( $redirect_page) .'"><span class="dashicons dashicons-admin-users"></span></a></li>';
    }


    return $items;
}


/** Adding Dashicons in WordPress Front-end */

add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
function load_dashicons_front_end() {
    wp_enqueue_style( 'dashicons' );
}


/**  SHORTCODES */

function add_people_form_func(){
    include dirname(__FILE__) . '/pages/add-people.php';
}
add_shortcode( 'addpeople', 'add_people_form_func' );

function add_activity_form_func(){

    global $wpdb;

    $activity_table = $wpdb->prefix . "mmat_activity";

    $people_table = $wpdb->prefix . "mmat_people";

    $activities = $wpdb->get_results("SELECT * FROM $activity_table");
    $people = $wpdb->get_results("SELECT * FROM $people_table ORDER BY name");

    include dirname(__FILE__) . '/pages/add-activity.php';
}
add_shortcode( 'addactivities', 'add_activity_form_func' );

function show_activity_list_func(){

    ob_start();

    global $wpdb;

    $activity_table = $wpdb->prefix . "mmat_activity";

    $people_table = $wpdb->prefix . "mmat_people";

    $people_activity_table = $wpdb->prefix . "mmat_people_activity";

    $interactions = $wpdb->get_results("
      SELECT p.id, p.name AS username, p.phone, p.email, pa.id AS pa_id, pa.with_friend, pa.friend_name, pa.date, a.id AS activity_id, a.name AS activity_name FROM $people_table p 
      LEFT JOIN $people_activity_table pa ON p.id = pa.people_id 
      LEFT JOIN $activity_table a ON pa.activity_id = a.id
      ORDER BY p.name");

    include dirname(__FILE__) . '/pages/list.php';

    return ob_get_clean();
}
add_shortcode( 'showlist', 'show_activity_list_func' );



/** HANDLE FORMS */

function add_people_form_function(){

    //you can access $_POST, $GET and $_REQUEST values here.

    if ( isset( $_POST['name'] ) ){

        global $wpdb;

        $tablename = $wpdb->prefix .'mmat_people';

        $data = [
            'name' => ucwords($_POST['name']),
            'phone' => $_POST['phone'],
            'email' => $_POST['email']
        ];

        $format = [ '%s', '%s', '%s'];

        $wpdb->insert( $tablename, $data, $format);

        if ($wpdb->insert_id) {
            wp_redirect($_SERVER['HTTP_REFERER']);
        } else {
            echo "there was an issue saving the data";
        }

    }

    //apparently when finished, die(); is required.
}
add_action('admin_post_add_people','add_people_form_function');

function add_activity_form_function(){

    //you can access $_POST, $GET and $_REQUEST values here.

    if ( isset( $_POST['people-id'] ) ){

        global $wpdb;

        $tablename = $wpdb->prefix .'mmat_people_activity';

        $date = $_POST['date'];
        $time = $_POST['time'];
        $timestamp = strtotime("$date $time");

        $with_friend = ($_POST['with-friend'] == null ? 0 : 1);

        $data = [
            'people_id'     => $_POST['people-id'],
            'activity_id'   => $_POST['activity-id'],
            'with_friend'   => $with_friend,
            'friend_name'   => ucwords($_POST['friend-name']),
            'date'          => $timestamp
        ];

        $format = [ '%d', '%d', '%s', '%s', '%d'];

        $wpdb->insert( $tablename, $data, $format);

        if ($wpdb->insert_id) {
            wp_redirect($_SERVER['HTTP_REFERER']);
        } else {
            echo "there was an issue saving the data";
        }

    }

    //apparently when finished, die(); is required.
}
add_action('admin_post_add_activity','add_activity_form_function');

/** AJAX HOOKS */

function search_box_q_func(){

    global $wpdb;

    $people_table = $wpdb->prefix . "mmat_people";

    $html = '';

    if (! empty($_POST['search_q'])) {
        $q = "%" . $_POST['search_q'] . "%";

        $people = $wpdb->get_results($wpdb->prepare(
            "SELECT  * 
                    FROM    $people_table 
                    WHERE   name
                    LIKE    %s
                    OR      phone
                    LIKE    %s
                    OR      email
                    LIKE    %s",
                        [$q, $q, $q, $q]
                    )
        );

        foreach( $people as $p) {
            $html .= '<li class="search-result-item" data-user-id="' . $p->id . '"  data-user-name="' . $p->name . '"><p class="result-name">' . $p->name . '</p><p class="result-phone">' . $p->phone . '</p><p class="result-email">' . $p->email . '</p></li>';
        }
    }

    echo $html;

    die();

}

add_action( 'wp_ajax_search_box_q', 'search_box_q_func');


/** HELPER FUNCS */

/**
 * @param $friend
 * @param $activity
 * @return null|string
 */
function get_image_class($friend, $activity){
    $class = '';

    if ( is_null($activity) ) {
        return NULL;
    }
    switch ($activity) {
        case 1 :
            $class = "visit";
            break;
        case 2 :
            $class = "fhe";
            break;
        case 3 :
            $class = "meal";
            break;
    }

    if ($friend) {
        $class .= "-friend";
    }

    return $class;
}