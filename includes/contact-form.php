<?php
add_shortcode( 'contact', 'show_contact_form' );
add_action( 'rest_api_init', 'create_rest_endpoint' );
add_action( 'init', 'create_submission_pages' );
add_action( 'add_meta_boxes', 'create_meta_box' );
// add_action( 'manage_submission_posts_columns', 'custom_submission_columns' );
// add_action( 'manage_submission_posts_custom_column', 'fill_submission_columns', 10, 2 );
add_action( 'admin_init', 'setup_search' );
add_action( 'wp_enqueue_scripts', 'enqueue_custom_scripts' );

function enqueue_custom_scripts(){
    wp_enqueue_style( 'contact-plugin-css', MY_PLUGIN_URL . '/assets/contact-plugin.css');
}


function setup_search() {
    global $typenow;

    if ( $typenow == 'submission' ) {
        add_filter( 'posts_search', 'submission_search_override', 10 , 2 );
    }
}

function submission_search_override($search, $query) {
    global $wpdb;

    if ( $query->is_main_query() && !empty($query->query['s']) ) {
        $sql = "
            or exists (
                select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
                and meta_key in ('name','email','number')
                and meta_value like %s
            )
        "; 
        $like = '%' . $wpdb->esc_like($query->query['s']) . '%';
        $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#",
                $wpdb->prepare($sql, $like), $search);
    }

    return $search;
}

function fill_submission_columns( $column, $post_id ){
    switch($column) {

        case 'name':
            echo get_post_meta( $post_id, 'name', true );
            break;
        
        case 'email':
            echo get_post_meta( $post_id, 'email', true );
            break;

        case 'number':
            echo get_post_meta( $post_id, 'number', true );
            break;

        case 'message':
            echo get_post_meta( $post_id, 'message', true );
            break;
            
    }
}

function custom_submission_columns($columns){
    $columns = array(
        'cb' => $columns['cb'],
        'name' => __( 'Name', 'contact-plugin' ),
        'email' => __( 'Email', 'contact-plugin' ),
        'number' => __( 'Number', 'contact-plugin' ),
        'message' => __( 'Message', 'contact-plugin' )
    );

    return $columns;
}

function create_meta_box() {
    add_meta_box( 'custom_contact_form', 'Submission', 'disply_submission', 'submission' );
}

function disply_submission() {
    $postmetas = get_post_meta( get_the_ID() );

    unset( $postmetas['_edit_lock'] );


    // echo '<ul>';
    // foreach ( $postmetas as $key => $value ) {
    //     echo '<li> <strong>' . ucfirst($key) . '</strong>: <br>' . $value[0] . '</li>'; 
    // }
    // echo '</ul>';

        echo '<ul>';
            echo '<li><strong>Name: </strong>' . get_post_meta( get_the_ID(), 'name', true . '</li>' );
            echo '<li><strong>Email: </strong>' . get_post_meta( get_the_ID(), 'email', true . '</li>' );
            echo '<li><strong>Number: </strong>' . get_post_meta( get_the_ID(), 'number', true . '</li>' );
            echo '<li><strong>message: </strong>' . get_post_meta( get_the_ID(), 'message', true . '</li>' );
        echo '</ul>';

}

function create_submission_pages() {
    $args = [
        'public' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submission'
        ],
        'hierarchical'      => true,
        'has_archive'         => true,
        'show_ui'           => true
       
        // 'capability_type'   => 'post',
        // 'capabilities'      => array (
        //     'create_posts'  => false,
        // ),
        // 'map_meta_cap'      => true
    ];

    register_post_type( 'submission', $args );
}


function show_contact_form() 
{
    include MY_PLUGIN_PATH .'/includes/templates/contact-form.php';

}

function create_rest_endpoint()
{
   // print_r( [ 'Hello' ] );
    //register_rest_route( $namespace:string, $route:string, $args:array, $override:boolean );

    register_rest_route( 'v1/contact-form', 'submit', array(
        'methods' => 'POST',
        'callback' => 'handle_inquery'
    ));
}

function handle_inquery($data) 
{
    $params = $data->get_params();

    if ( !wp_verify_nonce( $params['_wpnonce'], 'wp_rest' ) ) {
        return new WP_Rest_Response('Message not sent', 422);
    }

    unset( $params['_wpnonce'] );
    unset( $params['_wp_http_referer'] );

    //Send the email message
    $headers = [];

    $admin_email = get_bloginfo('admin_email');
    $admin_name = get_bloginfo('name');

    $headers = "From: {$admin_name} <{$admin_email}>";
    $headers = "Reply to: {$params['name']} <{$params['email']}>";
    $headers = "Content-Type: Text/html";

    $subject = "New enquery from {$params['name']}";

    $message = '';
    $message .= "<h1>Message has been sent from {$params['name']}<h1>";

    $postarr = [
        'post_title' => $params['name'],
        'post_type' => 'submission',
        'post_status' => 'publish'
    ];

    $post_id = wp_insert_post($postarr);

    foreach ( $params as $label => $value ) {
        $message .= ucfirst($label) . ':' . $value . '<br />' ; 

        add_post_meta( $post_id, $label, $value );
    }

    wp_mail( $admin_email, $subject, $message, $headers );

    return new WP_Rest_Response('Message was sent successfully', 200);

}

// step:1 -> 1.49.43

// step:2 -> 1.53.00
