<?php
/**
 * Default functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Default
 * @since 1.0
 */
define('THEME_URI', get_template_directory_uri());
add_theme_support('post-thumbnails');
add_theme_support( 'menus' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'custom-header' );
add_theme_support( 'custom-background' );
add_theme_support( 'html5' );

// Option Page
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}
// Option Page

// Enqueue scripts and styles
function default_scripts()
{
    wp_enqueue_style('default-screen', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css', array());
    wp_enqueue_style('default-screen1', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css', array());
    wp_enqueue_style('default-screen2', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700|Vollkorn:400,700', array());
    wp_enqueue_style('default-screen3', THEME_URI . '/assets/css/style.css', array(), rand(111,9999), 'all');
    wp_enqueue_style('default-screen4', THEME_URI . '/style.css', array());
    wp_enqueue_style('default-screen5', THEME_URI . '/assets/css/theme-768.css', array() , false , '(min-width: 767px)');
    wp_enqueue_style('default-screen6', THEME_URI . '/assets/css/theme-1024.css', array() , false , '(min-width: 1023px)');

    wp_enqueue_script('default-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js', array('jquery'), false, true); 
    wp_enqueue_script('default-script1', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js', array('jquery'), false, true);
    wp_enqueue_script('default-script2', 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', array('jquery'), false, true);
    wp_enqueue_script('default-script3', THEME_URI . '/assets/js/script.js', array('jquery'), false, true);   
}

add_action('wp_enqueue_scripts', 'default_scripts');
// Enqueue scripts and styles

// Pagination Setup
function custom_pagination() {
    global $wp_query;
    $big = 999999999;
    $pages = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
            'prev_next' => false,
            'type'  => 'array',
            'prev_next'   => TRUE,
            'prev_text'    => __('<span aria-hidden="true">prev</span>'),
            'next_text'    => __('<span aria-hidden="true">next</span>'),
        ) );
    $output = '';

    if( is_array( $pages ) ) {
        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var( 'paged' );

        $output .=  '<ul class="pagination">';
        foreach ( $pages as $page ) {
            $output .= "<li class='page-item'>$page</li>";
        }
        $output .= '</ul>';
        $dom = new \DOMDocument();
        $dom->loadHTML( mb_convert_encoding( $output, 'HTML-ENTITIES', 'UTF-8' ) );
        // Create an instance of DOMXpath and all elements with the class 'page-numbers' 
        $xpath = new \DOMXpath( $dom );
        $page_numbers = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' page-numbers ')]" );

        foreach ( $page_numbers as $page_numbers_item ) {

            // Replace the class 'current' with 'active'
            $page_numbers_item->attributes->item(0)->value = str_replace( 
                            'current',
                            'active',
                            $page_numbers_item->attributes->item(0)->value );

            // Replace the class 'page-numbers' with 'page-link'
            $page_numbers_item->attributes->item(0)->value = str_replace( 
                            'page-numbers',
                            'page-link',
                            $page_numbers_item->attributes->item(0)->value );
        }
        $output = $dom->saveHTML();

    }
    return $output;
    // if( is_array( $pages ) ) {
    //     $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    //     echo '<ul class="pagination mb-30">';
    //     foreach ( $pages as $page ) {
    //         if (strpos($page, 'current') !== false) {
    //             echo "<li class='active'>$page</li>"; }
    //             else {
    //             echo "<li>$page</li>"; 
    //             }
    //     }
    //     echo '</ul>';
    // }
}
// Pagination Setup

// Menu Setup
function setup_menu() {
    register_nav_menus(array(
        'main_menu' => 'Home',
        'footer_menu' => 'Footer',
        'mobile_menu' => 'Mobile',
    ), '');
}
setup_menu();
// Menu Setup

//remove ul
// function remove_ul ( $menu ){
//   return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
// }
// add_filter( 'wp_nav_menu', 'remove_ul' );

//add class on li menu
function add_classes_on_li($classes, $item, $args) {
  $classes[] = 'nav-item';
  return $classes;
}
add_filter('nav_menu_css_class','add_classes_on_li',1,3);

//add class on a menu
function add_classes_on_a($ulclass) {
  return preg_replace('/<a/', '<a class="nav-link pr-3 pl-3"', $ulclass, -1);
}
add_filter('wp_nav_menu','add_classes_on_a');

//change class sub-menu
function new_submenu_class($menu) {    
  $menu = preg_replace('/ class="sub-menu"/','/ class="dropdown-menu p-0" /',$menu);        
  return $menu;      
}

add_filter('wp_nav_menu','new_submenu_class'); 

//change class li sub-menu
function new_submenu_li_class($menu) {    
  $menu = preg_replace('/ class="menu-item-has-children"/','/ class="dropdown" /',$menu);        
  return $menu;      
}

add_filter('wp_nav_menu','new_submenu_li_class'); 

//add class active menu
function remove_active_class($class) {
  return ( $class == 'active' ) ? FALSE : TRUE;
}

function special_nav_class ($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
      $classes[] = 'active ';
  }
  else if (in_array('current-page-ancestor', $classes) ){
      $classes[] = 'active ';
  }
  else if(is_single() && $item->title == 'News'){
      $classes[] = 'current-menu-item active';
  }
  else if(is_single() && $item->title == 'Berita'){
      $classes[] = 'current-menu-item active';
  }
  else if(is_singular( 'product' )){
    $classes = array_filter( $classes, 'remove_active_class' );

       if( in_array( 'product1', $classes) ) {
         $classes[] = 'current-menu-item active';
       }
 }
  return $classes;
}
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);

// Hide content editor
add_action( 'admin_init', 'hide_editor' );
function hide_editor() {
  // Get the Post ID.
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
  if( !isset( $post_id ) ) return;
  // Hide the editor on the page titled 'Homepage'
  $homepgname = get_the_title($post_id);
  if($homepgname == 'Home'){ 
    remove_post_type_support('page', 'editor');
  }
  // Hide the editor on a page with a specific page template
  // Get the name of the Page Template file.
  $template_file = get_post_meta($post_id, '_wp_page_template', true);
  if($template_file == 'my-page-template.php'){ // the filename of the page template
    remove_post_type_support('page', 'editor');
  }
}

// remove wp version in css and js
add_filter( 'style_loader_src', 'sdt_remove_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999 );
function sdt_remove_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
// remove wp version
remove_action('wp_head', 'wp_generator');

/**
 * Disable the emoji's
 */
function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );    
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param    array  $plugins  
 * @return   array             Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

// Remove comment-reply.min.js from footer
function comments_clean_header_hook(){
    wp_deregister_script( 'comment-reply' ); 
}
add_action('init','comments_clean_header_hook');

// Remove some admin menu
function custom_menu_page_removing() {
  // remove_menu_page( 'index.php' );                  //Dashboard
  // remove_menu_page( 'jetpack' );                    //Jetpack* 
  // remove_menu_page( 'edit.php' );                   //Posts
  // remove_menu_page( 'upload.php' );                 //Media
  // remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'edit-comments.php' );          //Comments
  // remove_menu_page( 'themes.php' );                 //Appearance
  // remove_menu_page( 'plugins.php' );                //Plugins
  // remove_menu_page( 'users.php' );                  //Users
  remove_menu_page( 'tools.php' );                  //Tools
  // remove_menu_page( 'options-general.php' );        //Settings
}
add_action( 'admin_menu', 'custom_menu_page_removing' );

function get_breadcrumb() {
    echo '<li class="breadcrumb-item"><a href="'.home_url().'" rel="nofollow">Home</a></li>';
    if (is_category() || is_single()) {
        $category_detail=get_the_category($post->ID);
        echo '<li class="breadcrumb-item">';
        foreach($category_detail as $cd){
          echo ' ';
          echo $cd->cat_name; }
        echo '</li>';
      if (is_single()) {
          echo '<li class="breadcrumb-item">';
          the_title();
          echo '</li>';
      }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#47;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#47;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

function language_selector_flags(){
        $languages = icl_get_languages('skip_missing=0&orderby=code');
            if(!empty($languages)){
                foreach($languages as $l){
                if($l['active']) {
                echo '<a class="active" href="'.$l['url'].'">';
                }
                else{
                echo '<a class="nav-link" href="'.$l['url'].'">';
                }
                echo $l['language_code'];
                echo '</a>';
        }
    }
}

// Post Type Settings
function init_post_type() {
  register_post_type('product', array(
      'label' => 'Product',
      'description' => '',
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_icon' => 'dashicons-products',
      'capability_type' => 'post',
      'map_meta_cap' => true,
      'hierarchical' => false,
      'rewrite' => array('slug' => 'product', 'with_front' => true),
      'query_var' => true,
      'supports' => array('title','editor','thumbnail','post-formats','categories'),
      'labels' => array (
      'name' => 'Product List',
      'singular_name' => 'Product List',
      'menu_name' => 'Product',
      'add_new' => 'Add Product',
      'add_new_item' => 'Add New Product',
      'edit' => 'Edit',
      'edit_item' => 'Edit Product',
      'new_item' => 'New Product',
      'view' => 'View Product',
      'view_item' => 'View Product',
      'search_items' => 'Search Product',
      'not_found' => 'No Product Found',
      'not_found_in_trash' => 'No Product Found in Trash',
      'parent' => 'Parent Product',
      ),
      'public' => true,
      'has_archive' => false,
  ));
  
  $category_labels = array(
      'label' => 'Product Category',
      'hierarchical' => true,
      'query_var' => true,
  );

  register_taxonomy('product_category', 'product', $category_labels);
}
add_action('init', 'init_post_type');

// Add the custom column to the post type -- replace product with your CPT slug
add_filter( 'manage_product_posts_columns', 'itsg_add_custom_column' );
function itsg_add_custom_column( $columns ) {
    $columns['product_category'] = 'Category';

    return $columns;
}

// Add the data to the custom column -- replace product with your CPT slug
add_action( 'manage_product_posts_custom_column' , 'itsg_add_custom_column_data', 10, 2 );
function itsg_add_custom_column_data( $column, $post_id ) {
    switch ( $column ) {
        case 'Category' :
            echo get_post_meta( $post_id , '_product_category' , true ); // the data that is displayed in the column
            break;
    }
}

// Make the custom column sortable -- replace product with your CPT slug
add_filter( 'manage_edit-product_sortable_columns', 'itsg_add_custom_column_make_sortable' );
function itsg_add_custom_column_make_sortable( $columns ) {
	$columns['product_category'] = 'Category';

	return $columns;
}

// Add custom column sort request to post list page
add_action( 'load-edit.php', 'itsg_add_custom_column_sort_request' );
function itsg_add_custom_column_sort_request() {
	add_filter( 'request', 'itsg_add_custom_column_do_sortable' );
}

// Handle the custom column sorting
function itsg_add_custom_column_do_sortable( $vars ) {

	// check if post type is being viewed -- replace product with your CPT slug
	if ( isset( $vars['post_type'] ) && 'product' == $vars['post_type'] ) {

		// check if sorting has been applied
		if ( isset( $vars['orderby'] ) && 'Category' == $vars['orderby'] ) {

			// apply the sorting to the post list
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => '_product_category',
					'orderby' => 'meta_value_num'
				)
			);
		}
	}

	return $vars;
}

// remove width and height in images
add_filter( 'post_thumbnail_html', 'remove_wps_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_wps_width_attribute', 10 );
  
function remove_wps_width_attribute( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
