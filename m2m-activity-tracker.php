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
    wp_localize_script( 'm2m-activity-tracker-js', 'mmat_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
    wp_enqueue_script('m2m-activity-tracker-js', null, array('jquery'), null, true);

    wp_register_script( 'sortable', plugins_url('js/sorttable.js',__FILE__ ));
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


    global $wpdb;

    $activity_table = $wpdb->prefix . "mmat_activity";

    $people_table = $wpdb->prefix . "mmat_people";

    $people_activity_table = $wpdb->prefix . "mmat_people_activity";

    $interactions = $wpdb->get_results("
      SELECT p.id, p.name AS username, p.phone, p.email, pa.id AS pa_id, pa.with_friend, pa.friend_name, pa.date, a.id AS activity_id, a.name AS activity_name FROM $people_table p 
      LEFT JOIN $people_activity_table pa ON p.id = pa.people_id 
      LEFT JOIN $activity_table a ON pa.activity_id = a.id
      ORDER BY p.name, pa.date");


    $trs = [];
    $tr = [];
    //    $tr = [
//        'info' => [
//            'name' => '',
//            'phone' => '',
//            'email' => '',
//            'user_id'   => ''
//        ],
//        'visits' => [
//            [
//                'class' => '',
//                'pa_id' => '',
//                'date'  => '',
//                'friend_name'   => '',
//            ],
//            [
//                'class' => '',
//                'pa_id' => '',
//                'date'  => '',
//                'friend_name'   => '',
//            ]
//
//        ],
//        'total' => ''
//    ];

    $user_id = 0;
    $i = 0;

    foreach( $interactions as $int) {

        $tr['info']['name']     = $int->username;
        $tr['info']['phone']    = $int->phone;
        $tr['info']['email']    = $int->email;
        $tr['info']['id']       = $int->id;

        if ( $user_id != $int->id ) {
            $trs[] = $tr;
        }

        $user_id = $int->id;
    }


    foreach ( $trs as $k => $v) {
        $interaction_counter    = 0;
        $i = 0;
        foreach ( $interactions as $int ) {
            if ( $trs[$k]['info']['id'] == $int->id ) {

                $trs[$k]['visits'][$i]['class'] = get_image_class($int->with_friend, $int->activity_id);

                $trs[$k]['visits'][$i]['date']          = '';
                $trs[$k]['visits'][$i]['pa_id']         = '';
                $trs[$k]['visits'][$i]['friend_name']   = '';
                $trs[$k]['visits'][$i]['activity_name'] = '';
                $trs[$k]['total']                       = 0;


                if ( ! empty($int->date) ) {
                    $trs[$k]['visits'][$i]['date']          = gmdate("j/n/Y, g:i a", $int->date);
                    $trs[$k]['visits'][$i]['pa_id']         = $int->pa_id;
                    $trs[$k]['visits'][$i]['friend_name']   = $int->friend_name;
                    $trs[$k]['visits'][$i]['activity_name'] = $int->activity_name;
                    $trs[$k]['total']                       = ++$interaction_counter;
                }
                $i++;
            }
        }
    }

    //populate_test_data();

    include dirname(__FILE__) . '/pages/list.php';

}
add_shortcode( 'showlist', 'show_activity_list_func' );



/** HANDLE FORMS */

function add_people_form_function(){

    //you can access $_POST, $GET and $_REQUEST values here.

    if ( isset( $_POST['name'] ) ){

        global $wpdb;

        $tablename = $wpdb->prefix .'mmat_people';

        $data = [
            'name' => ucwords($_POST['lname']) . ", " . ucwords($_POST['name']),
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

function populate_test_data(){

    /**
     * Use this js to collect the string of names
     *
     * https://www.lds.org/directory/?lang=eng
     *
     * $.each( $('#middleScrollerColumn ul li a'), function( index, value) { console.log($(value).text()) })
     */

    global $wpdb;

    $table_people = $wpdb->prefix . 'mmat_people';


    $users = "Abad Aguilera, David & Sonia,,Abellan García, María Dolores,,Acaro Becerra, Miguel Alejandro,,Adame Coedo, Antonio Samuel & Monica,,Aguilera, David Tirso & María del Rosario,,Agustin Conejos, Emiliana Azucena,,Alapont Soler, Francisco Ramon,,Alfonso Almiñana, Manuel & María de los Ángeles,,Alfonso Segarra, Nefi & Luisa Fernanda,,Alfonso Vila, María,,Alfonso, Salvador & María de la Concepción,,Almenar Carreres, Jesús & America,,Almenar Carreres, José & María Almudena,,Almerich Pruñonosa, José & Maria del Rosario,,Amblar Abellan, Arantxa,,Amblar Abellan, Jonatan & Vanessa,,Amorós Agulló, Francisco,,Antezana Gomez, Gerardo & Mary Luz de,,Antoriano Ibañez, Otto Spenser,,Antoriano Vivanco, Otto Nelson & Becky,,Arnandis Marti, Gloria,,Artal Cerveto, Juan Vicente,,Arteaga Cevallos, Dayra Jazmin,,Ballester Iborra, Ana Isabel,,Barahona Martinez, Jaime Fernando & Claudia Geraldina,,Barriga Pineiro, Miguel Angel,,Bermúdez Segarra, Antonio,,Bestue Cartagena, Ceferino,,Blanch Jorge, Electra,,Bleda, Ramón & María del Sol,,Botet Lozano, Maria Cristina,,Brizuela, Romina,,Brotons Sayol, Antonio José,,Caballero Espert, Bautista,,Cabral Ribeiro, Paulo & Amalia Cabral,,Cabrera Soto, Isabel Mercedes,,Cabuchola Llorca, Juan,,Caceres Maldonado, Angel Luis,,Caceres Soldado, Jacinto & Carmen,,Calvó Coronado, Jose María & Keila,,Carreres Chulia, Miguela,,Casaña Medall, Josefina,,Castella Rabasa, Victor Manuel,,Castillo Guerra, Fanny,,Castro Palacios, Juan Pedro & Isabel,,Cebrian Salillas, Rosa,,Chacón Córdoba, Karol Marcela,,Chaques Mari, Patricia,,Chavez Vidal, Liliana de,,Chenique Gorreta, Mauricio,,Chuva Villavicencio, Leonor Eufemia,,Ciscar Segarra, Paula,,Ciscar Segarra, Ramón,,Clemente, José & Manuela,,Clemente, Pedro Francisco & María Dolores,,Conde Paredes, Rair Gerardo,,Cristea, Ioan,,Cubas, Agustin & María del Carmen,,Degaut Barbosa, Dalva Maria,,Delgado Juarez, Giovanna Consuelo,,Delhom, Francisco & Vicenta,,Diego Cardona, Angel,,Domingo Gimeno, Amparo,,Dos Santos Lobo Assumpcao, Luis Eduardo,,Emeka Nnamezie, Michael,,Estrada Martín, Andrés & Ivonne Sofía,,Estrems Dalmau, Josefa,,Fernandez de la Hoz, Jair Orlando & Elsa Viviana,,Fernandez Gonzalez, Santiago,,Fernández Alonso, Juan,,Frances Tortosa, María del Mar,,Freijeiro, Gonzalo & Francisca,,Fuentes Fernández, María Ramona,,Gallego Blanco, Francisco,,Gallego Blanco, María del Mar,,Gallego Blanco, María Dolores,,García Almansa, María José,,García Castillo, Purificacion,,García González, Julia,,García Talamantes, Antonio,,García Talamantes, María Vicenta,,Garrigues Medina, Sabina,,Gasso Martins, Juan Luis,,Gastaldo Sanfelix, Jose & Josefa,,Gelardo Sandemetrio, Santiago,,Genovart Martinez, Alicia,,Giménez Nieto, María Luisa,,Gomez Rojas, Segundo Alejandro & Miriam,,Gomis Martín, María del Rosario,,Gonzalez Agredo, Floralba de,,Gonzalez Agredo, Luz Alba,,Granell Corrales, Estefania,,Guigo Lozano, Segundo,,Gámez Fuentes, Raúl & Beatriz,,Gómez Delgado, Ángel & Josefa,,Gómez Rolón, Rosa Claudelina,,Hellin Peñalver, José Luis,,Hernández Ruano, Rafael,,Hernández Silva, Thais María Luisa,,Herrera, Sandra Monteiro de Carvalho,,Hompanera, Celestino & Alicia,,Iborra Martínez, Martín,,Ibáñez Peris, María Nieves Concepción,,Igual Agustín, Jesús & Yolanda,,Igual Agustín, Vicente & Mirna Ruth,,Ihezie, Jerry Anayochi,,Iturbe Gutierrez, Cecilia Jeanette,,Jara Cisneros, Moisés Favio & Sandra Veronica,,Jaramillo Ponce, Cristian Andres & Natalie Mabel,,Jaramillo Ruales, Nelson Enrique,,Javier Geribes, Bernardo,,Jiminez Ortiz, Juan José,,Joyeux Ferrándiz, Antonella,,Krutsch Ospina, Daniel Felipe,,Leal Villanueva, Pablo,,León León, Antonio & María del Consuelo,,León Sánchez, Carmen,,Linares Vargas, José Manuel,,Llumiquinga Caiza, Monica Yolanda,,Lobo Perez, Flor Alba,,Lorente Rosales, Joaquin,,Lorente Vergada, Aaron Nefi,,Lorente Vergada, Ruben & María de los Angeles,,Lozano Valera, Aurora,,Lucas Canales, Maria Cristina,,Lujo Veneros, Jhovan & Edilse,,López Bauset, David & María Luisa,,López Gámez, David & Hilda Feliciana,,López Romano, José & Purificación,,Maestro Valle, Virtudes,,Maldonado Carpio, Katy Johana,,Mari Montserrat, Amparo,,Marin Tamayo, Juan Gabriel,,Martinez Marco, Juan Pablo,,Martinez, Carmen,,Martínez Alises, María Jesus,,Martínez Cabañas, María Pilar,,Mateo Gardo, Josefa del Carmen,,Mateos Villalba, María Amparo,,Medina, Angel Fabian & Elizabeth Justina,,Mendoza Alarcon, Giovana,,Meson, Raul Fernando,,Miranda Anachuri, Wilma,,Monsonis Boquera, Purificación,,Montes Gómez, José,,Moreno Zamora, Ivan,,Moscardó Ginestar, Vicente & María Amparo,,Munoz Lenerz, Joel Christian,,Muñoz Sanbartolome, María José,,Narciso, Bruna Filipa Rebelo,,Navarro Asunción, Victor,,Navarro de la Hoz, María del Mar,,Nnah, Nathan Chimaeze,,North, Bernard Victor,,Olguin Figueroa, Wilson,,Orande Tamara, Lolita,,Panadero Rocher, Maria Concepción,,Pannone Garcia, Renaldo,,Paredes Solis, Angelo Ruben,,Paredes Solís, Sandra Jennifer,,Pedrajas Gallardo, Jose Manuel & Raquel,,Pereiro Ramos, Sara,,Perez Ravelo, Jenny Vicenta,,Picornell Almeida, Gabriel & Kenia Rosmery,,Piña Galdon, Salvador,,Porcuna, Ignacio juan,,Pulgar Granell, Brian,,Quiles Martínez, Francisco Javier,,Quiñones Bullon, Enrique Alberto & María Isabel,,Rama, Andreina Paola,,Ramirez Escalante, Luis Norberto,,Ramos Herrero, Magdalena,,Ramírez Fernández, Miguel & Luz Divina,,Rivas Garcia, Raysa,,Rodriguez Campo, Angela,,Rodriguez Sanchez Carlos Voltaire, Carlos Voltaire & Diana del Pilar,,Rodríguez Barroso, Aramiel,,Rodríguez Gimeno, Francisco & María,,Rodríguez Rubio, Julia,,Romero Castaño, Miguel,,Romero Molina, Fernando,,Romero Molina, Isabel,,Romero, Carlos & Asuncion,,Romero, Sebastian & Carlota,,Roses Iglesias, María Inmaculada,,Rubio Guapacha, Helamán & Elisa,,Salas Torres, Jason Felipe,,Salgado Encarnación, Ángel Byron & Jenny Fabiola de,,Sandemetrio Hervas, Concepción,,Sangucho Galiendo, Jhoselin Eileen,,Santana, Edward Rafael,,Segarra Alfonso, Lucía Antonia,,Segarra Alfonso, Maria del Carmen,,Segarra Alfonso, Miguela Salvadora,,Serrano Alonso, Anibal,,Silva, Jefferson Henrique Nunes da & Janeth Heidi,,Soben Olivares, David,,Solanilla, Fructuoso & María Julia,,Toro Díaz, Francisca,,Torremocha García, Noemi,,Trujillo Ghandi, Lenin Lombardo,,Trujillo, Jesus & Emilia,,Vallejos Chavez, Aydee Virginia,,Valles Cabrelles, Rosa,,Vega Gil, Elvira María,,Vega, Armando Favio & Gabriela Carolina,,Velez Aguirre, Yeini Veronica,,Vidal Balaguer, José Vicente,,Vilar, José Francisco & María José,,Villalba Gayozo, Carlos Andres,,Villegas Moreno, María del Pilar,,Villegas Saez, Rafaela Nelida,,Vázquez García, Juan Miguel & Silvia,,Yagües Pascual, Jose & Juana Briyit,,Zahonero Ferrer, María Luisa,,Zamorano Ruiz, Stevenson,,Ácaro Castillo, Miguel Ángel";
    $users_array = explode(',,', $users);


    $demo_phone_number = '698000000';


    foreach ($users_array as $u) {

        $demo_phone_number++;

        $demo_email = str_replace(' ', '_', str_split( strtolower( $u ), 10 )[0])  . '@gmail.com';

        $wpdb->insert(
            $table_people,
            [
                'name'=> $u,
                'phone' => $demo_phone_number,
                'email' => $demo_email
            ],
            [
                '%s',
                '%s',
                '%s'
            ]
        );


    }

    $wpdb->flush();
}