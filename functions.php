<?php
// Start the engine
include_once( get_template_directory() . '/lib/init.php' );

// Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Pods IO Genesis Child Theme' );
define( 'CHILD_THEME_URL', 'http://www.studiopress.com/' );
define( 'CHILD_THEME_VERSION', '1.0' );
define( 'CHILD_DOMAIN', 'podsio-genesis');


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Twenty Fifteen 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 680;
}

// Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,500,700', array(), CHILD_THEME_VERSION );
}

// Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

// Add Accessibility support
// add_theme_support( 'genesis-accessibility', array( 'headings', 'drop-down-menu', 'search-form', 'skip-links', 'rems' ) );
add_theme_support( 'genesis-accessibility', array( 'headings', 'search-form', 'skip-links', 'rems' ) );

// Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom background
add_theme_support( 'custom-background' );

// Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

/**********************************
 *
 * Replace Header Site Title with Inline Logo
 *
 * Fixes Genesis bug - when using static front page and blog page (admin reading settings) Home page is <p> tag and Blog page is <h1> tag
 *
 * Replaces "is_home" with "is_front_page" to correctly display Home page wit <h1> tag and Blog page with <p> tag
 *
 * @author AlphaBlossom / Tony Eppright
 * @link http://www.alphablossom.com/a-better-wordpress-genesis-responsive-logo-header/
 *
 * @edited by Sridhar Katakam
 * @link http://www.sridharkatakam.com/use-inline-logo-instead-background-image-genesis/
 *
************************************/
add_filter( 'genesis_seo_title', 'custom_header_inline_logo', 10, 3 );
function custom_header_inline_logo( $title, $inside, $wrap ) {

	$logo = '<img src="' . get_stylesheet_directory_uri() . '/images/logo.png" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="300" height="60" />';

	$inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), $logo );

	// Determine which wrapping tags to use - changed is_home to is_front_page to fix Genesis bug
	$wrap = is_front_page() && 'title' === genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';

	// A little fallback, in case an SEO plugin is active - changed is_home to is_front_page to fix Genesis bug
	$wrap = is_front_page() && ! genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;

	// And finally, $wrap in h1 if HTML5 & semantic headings enabled
	$wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

	return sprintf( '<%1$s %2$s>%3$s</%1$s>', $wrap, genesis_attr( 'site-title' ), $inside );

}

// Remove the site description
/* remove_action( 'genesis_site_description', 'genesis_seo_site_description' ); */

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'custom_scripts_styles_mobile_responsive' );
function custom_scripts_styles_mobile_responsive() {

	wp_enqueue_script( 'responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), '1.2.0', true );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'podsio-fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');

	wp_localize_script( 'responsive-menu', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'expand child menu', 'podsio-genesis' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'collapse child menu', 'podsio-genesis' ) . '</span>',
	) );

}

// Customize the previous page link
add_filter ( 'genesis_prev_link_text' , 'sp_previous_page_link' );
function sp_previous_page_link ( $text ) {
	return g_ent( '&laquo; ' ) . __( 'Previous Page', CHILD_DOMAIN );
}

// Customize the next page link
add_filter ( 'genesis_next_link_text' , 'sp_next_page_link' );
function sp_next_page_link ( $text ) {
	return __( 'Next Page', CHILD_DOMAIN ) . g_ent( ' &raquo; ' );
}

/**
 * Remove Genesis Page Templates
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/remove-genesis-page-templates
 *
 * @param array $page_templates
 * @return array
 */
function be_remove_genesis_page_templates( $page_templates ) {
	unset( $page_templates['page_archive.php'] );
	unset( $page_templates['page_blog.php'] );
	return $page_templates;
}
add_filter( 'theme_page_templates', 'be_remove_genesis_page_templates' );


function type_label ($post_type) {
	if ($post_type == 'page') { return 'Documentation'; }
	$post_type_obj = get_post_type_object( $post_type );
	$post_type_label = $post_type_obj->labels->singular_name;
	return $post_type_label;
}

/* Add Menu Social Links filters */
add_filter( 'storm_social_icons_use_latest', '__return_true' );

/* Add Post Excerpt to Pages */
add_action( 'init', 'my_add_excerpts_to_pages' );

function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

/* Remove JetPack Sharing from Excerpt Display */
function jetpacktweak_remove_share () {
	remove_filter( 'the_excerpt', 'sharing_display', 19 );
}

add_action( 'loop_start', 'jetpacktweak_remove_share' );

function change_doc_entry_text( $title ) {
	$screen = get_current_screen();
	if  ( 'page' == $screen->post_type ) {
		$title = 'Enter Documentation Title';
	}
	return $title;
}
add_filter( 'enter_title_here', 'change_doc_entry_text' );

/* Output filter for my_date
   Use this against a date field in your Pods Fields like so:
   {@post_date,my_date}

   The Function below should be in your functions.php
*/


function return_date($input_date) {
    return date("F d, Y", strtotime($input_date));
}

function return_age($input_date) {
	$now = date("Y-m-d");
	return date_diff(date_create($input_date), date_create($now))->format('%a days ago');
}

/* This filter fixes an issue where the Blog page is highlighted as a menu item
 * for archives/singles of other post types.
 */

function custom_type_nav_class($classes, $item) {
     $post_type = get_post_type();

     // Remove current_page_parent from classes if the current item is the blog page
     // Note: The object_id property seems to be the ID of the menu item's target.
     if ($post_type != 'post' && $item->object_id == get_option('page_for_posts')) {
         $current_value = "current_page_parent";
         $classes = array_filter($classes, function ($element) use ($current_value) { return ($element != $current_value); } );
     }

     // Now look for post-type-<name> in the classes. A menu item with this class
     // should be given a class that will highlight it.
     $this_type_class = 'post-type-' . $post_type;
     if (in_array( $this_type_class, $classes )) {
         array_push($classes, 'current_page_parent');
     };

     return $classes;
}
add_filter('nav_menu_css_class', 'custom_type_nav_class', 10, 2);
