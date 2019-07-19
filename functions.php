<?php

show_admin_bar(false);
add_theme_support('title-tag');
add_theme_support('post-thumbnails');

// REMOVE EMOJI ICONS
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


add_filter('style_loader_tag', 'codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'codeless_remove_type_attr', 10, 2);
function codeless_remove_type_attr($tag, $handle) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

register_nav_menus(array(
    'top' => 'Main Menu',
    'bottom' => 'Footer Menu'
));

// STYLE
add_action('wp_print_styles', 'add_styles');
if (!function_exists('add_styles')) {
    function add_styles() {
        if(is_admin()) return false;
        wp_enqueue_style( 'main', get_template_directory_uri(). '/assets/prod/css/main.min.css', array(), '1.0.0' );
    }
}

add_action( 'wp_enqueue_scripts', 'jquery_script_method' );
function jquery_script_method() {
    wp_deregister_script('jquery');
    wp_deregister_script( 'wp-embed' );
    //wp_register_script('jquery', get_template_directory_uri() . '/js/jquery-3.3.1.min.js', array(), '', true);
    wp_enqueue_script('jquery');
}

// SCRIPT
add_action('wp_footer', 'add_scripts');
if (!function_exists('add_scripts')) {
    function add_scripts() {
        if(is_admin()) return false;
        //wp_deregister_script('jquery');
        wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/prod/js/main.min.js', array(), '1.0.0', 'in_footer');

    }
}


/**
 * Restore SVG & CSV upload functionality for WordPress 4.9.9 and up
 */
add_filter('wp_check_filetype_and_ext', function($values, $file, $filename, $mimes) {
    if ( extension_loaded( 'fileinfo' ) ) {
        // with the php-extension, a CSV file is issues type text/plain so we fix that back to
        // text/csv by trusting the file extension.
        $finfo     = finfo_open( FILEINFO_MIME_TYPE );
        $real_mime = finfo_file( $finfo, $file );
        finfo_close( $finfo );
        if ( $real_mime === 'text/plain' && preg_match( '/\.(svg)$/i', $filename ) ) {
            $values['ext']  = 'svg';
            $values['type'] = 'image/svg+xml';
        }
        if ( $real_mime === 'text/plain' && preg_match( '/\.(csv)$/i', $filename ) ) {
            $values['ext']  = 'csv';
            $values['type'] = 'text/csv';
        }
    } else {
        // without the php-extension, we probably don't have the issue at all, but just to be sure...
        if ( preg_match( '/\.(csv)$/i', $filename ) ) {
            $values['ext']  = 'svg';
            $values['type'] = 'image/svg+xml';
        }
        if ( preg_match( '/\.(csv)$/i', $filename ) ) {
            $values['ext']  = 'csv';
            $values['type'] = 'text/csv';
        }
    }
    return $values;
}, PHP_INT_MAX, 4);


function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function wpa_fix_svg_thumb() {
    echo '<style>td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail {width: 100% !important;height: auto !important}</style>';
}
add_action('admin_head', 'wpa_fix_svg_thumb');

// Disable comments
function df_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'df_disable_comments_post_types_support');
// Close comments on the front-end
function df_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
// Remove comments page in menu
function df_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');


// Remake menu
add_filter( 'wp_nav_menu_args', 'filter_wp_menu_args' );
function filter_wp_menu_args( $args ) {
    //if ($args['theme_location'] === 'top') {
    $args['container']  = false;
    $args['items_wrap'] = '<ul id="%1$s" class="%2$s">%3$s</ul>';
    //$args['menu_class'] = false;
    //}
    return $args;
}


function atg_menu_classes($classes, $item, $args) {
    //if($args->theme_location == 'top') {
    $classes[] = 'menu__item';
    //}
    return $classes;
}
add_filter('nav_menu_css_class', 'atg_menu_classes', 1, 3);

function add_menuclass($ulclass) {
    return preg_replace('/<a /', '<a class="menu__link" ', $ulclass);
}
add_filter('wp_nav_menu','add_menuclass');


// CONTACT FORM

require_once('classes/Recaptcha.php');

function rw_contacts_send_msg(){

    if(isset($_POST['email']) || wp_verify_nonce( $_POST['nonce'], $_POST['contacts_send_msg'] ) ){
        $response = Recaptcha::recaptcha_verification($_POST['captcha']);
        if ($response==false) {
            echo json_encode(array('status'=>'failed'));
            die;
        }else if ($response==true) {
            $user_name = str_replace('/','',stripslashes(trim(filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING ))));
            $email = str_replace('/','',stripslashes(trim(filter_input( INPUT_POST, 'email', FILTER_SANITIZE_STRING | FILTER_SANITIZE_EMAIL ))));

            $emailTo = 'contact@mediadoro.com';//admin
            $subject = 'From '.$user_name;
            $body = "Name: $user_name \nEmail: $email ";
            //$headers = 'From: '.$user_name.' <'.$emailTo.'>' . 'rn' . 'Reply-To: ' . $email;
            $headers = "From:";
            wp_mail($emailTo, $subject, $body, $headers);
            echo json_encode(array('status'=>'success'));
            die;
        }
    }
    else{
        echo 'input value is null';
    }
}

add_action('wp_ajax_contacts_send_msg', 'rw_contacts_send_msg');
add_action('wp_ajax_nopriv_contacts_send_msg', 'rw_contacts_send_msg');