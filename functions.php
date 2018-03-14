<?php
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();
    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/js/popper.min.js', array(), false);
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
    $query_args = array(
		'family' => 'Open+Sans|Oswald|Dosis|Roboto+Slab|Roboto:100|Raleway:100|Biryani:200|Work+Sans:200|Rajdhani:400',
		'subset' => 'latin,latin-ext',
	);
	wp_enqueue_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
	wp_enqueue_style( 'fontscom',  "//fast.fonts.net/cssapi/b1af4ec6-225e-4db2-900a-c70259483da8.css" ,array(), null ); 
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

add_action( 'customize_register', 'secondstep_customize_register' ); 

function secondstep_customize_register( $wp_customize ) {
	
class Taxonomy_Dropdown_Customize_Control extends WP_Customize_Control {
    public $type = 'taxonomy_dropdown';
    var $defaults = array();
    public $args = array();
 
    public function render_content(){
        // Call wp_dropdown_cats to ad data-customize-setting-link to select tag
        add_action('wp_dropdown_cats', array($this, 'wp_dropdown_cats'));
 
        // Set some defaults for our control
        $this->defaults = array(
            'show_option_none' => __('None'),
            'orderby' => 'name', 
            'hide_empty' => 0,
            'id' => $this->id,
            'selected' => $this->value(),
        );
 
        // Parse defaults against what the user submitted
        $r = wp_parse_args($this->args, $this->defaults);
 
?>
	<label><span class="customize-control-title"><?php echo esc_html($this->label); ?></span></label>
<?php  
        // Generate our select box
        wp_dropdown_categories($r);
    }
 
    function wp_dropdown_cats($output){
        // Search for <select and replace it with <select data-customize=setting-link="my_control_id"
        $output = str_replace('<select', '<select ' . $this->get_link(), $output);
        return $output;
    }
}


$wp_customize->add_section('theme_homepage_category', array(
	'title' => __('Homepage Post Tile Category'),
	'priority' => 36,
	'args' => array(), // arguments for wp_dropdown_categories function...optional
));
 
$wp_customize->add_setting('homepage_category', array(
	'default' => get_option('default_category', ''),
));
 
$wp_customize->add_control( new Taxonomy_Dropdown_Customize_Control($wp_customize, 'homepage_category', array(
	'label' => __('Select Category to show in post tiles'),
	'section' => 'theme_homepage_category',
	'settings' => 'homepage_category',
)));
}


function get_wp_gallery_ids($post_content) {
	
			 //$post_content = $post->post_content;
			 preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
}


function title_header (){
	
$thepostcounter = get_post_meta(get_the_ID(),'incr_number',true);
	?>
					<span class="keyline"></span>
		    		<span class="sub-article">
		    			<span id="post-title" class="subarticle"><?php the_title(); ?></span><span class="sub-article-wrap"><span class="sub-subarticle sub"></br></span></span>
		    		</span>
		 <?php
}

function digidol_site_title() {
    do_action('digidol_site_title');
} // end digidol_site_title

add_action('digidol_site_title','title_header');


function digidol_hero() {
    do_action('digidol_hero');
} // end digidol_hero
// my gallery function, add options to turn on or off caption. // add option to turn on cover text page fed from the post_content.
function digidol_gallery_carousel() {
/*
	global $post;
	$the_content =  $post->post_content;
	$the_content = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $the_content);  # strip shortcodes, keep shortcode content
	//remove_shortcode( 'gallery' );
	//$new_content = apply_filters('the_content',$the_content);
	//echo $new_content;  

	
	
	
	preg_match('/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids);
	if ($ids) {
	$attachments = explode(",", $ids[1]);
*/
	
	$args = array('post_type' => 'slideshow', 'numberposts' => -1); 
			$attachments = get_posts($args);
			
	?>
	
	
	<div class="wrapper" id="wrapper-hero">
	<div class="container-fluid" id="hero-slides">
		<div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="5000">
		<div class="wrapper" id="month-wrap">
			<div class="container">
				
			</div>
		</div>
		<div class="carousel-inner" role="listbox">	
			
			<?php
				
		$loopcount = 1;		
	if ($attachments) {
		foreach ( $attachments as $attachment ) {
		
		$imagethumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($attachment->ID ), 'homepage');
		$imag_alt = get_post_meta($attachment, '_wp_attachment_image_alt', true);
		$post_id = $attachment;
		$article = get_post_meta($attachment->ID,'article',true);
		$incrnumber = get_post_meta($article,'incr_number',true);
		?>
	
				
					<div class="carousel-item <?php if ($loopcount == 1) { echo 'active'; }; ?>" data-id=<?php echo $loopcount ?>>			
						
							<img src="<?php echo $imagethumbnail[0]; ?>" alt="<?php echo $imag_alt;?>" />
							<div id="postincr-<?php echo $loopcount ;?>" class="invisible incr_num"><?php echo $incrnumber;   ?></div>
							<div id="slideposttitle" class="invisible slideposttitle"><?php echo get_the_title( $attachment ); ?></div>
						
					</div>		
					
							
						<?php $loopcount++;
		}
	}
						?>
										</div>


			</div>
		</div>
		
	</div>
						<?php
	
	}


add_action('digidol_hero','digidol_gallery_carousel');


function child_theme_setup() {

	// Make sure featured images are enabled
	
		
	// Add other useful image sizes for use through Add Media modal
	add_image_size( 'folio-image', 1110 );
	add_image_size( 'homepage', 1500 );
	add_image_size( 'grid-image', 890,500, true );
	add_image_size('archive-thumb',208,116, true);
	add_image_size('featured_preview', 55, 55, true);
	
	
	// Register the three useful image sizes for use in Add Media modal
	add_filter( 'image_size_names_choose', 'wpshout_custom_sizes' );
	function wpshout_custom_sizes( $sizes ) {
	    return array_merge( $sizes, array(
	        'folio-image' => __( 'Folio Image 1110' ),
	    ) );
	}
}
add_action( 'after_setup_theme', 'child_theme_setup', 11 );




//adding custom function to show thumbnails on slide admin page

// GET FEATURED IMAGE
function ST4_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
        return $post_thumbnail_img[0];
    }
}
// ADD NEW COLUMN
function ST4_columns_head_only_slideshow($defaults) {
    $defaults['featured_image'] = 'Featured Image';
    return $defaults;
}

// SHOW THE FEATURED IMAGE
function ST4_columns_content_only_slideshow($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
	   
        $post_featured_image = ST4_get_featured_image($post_ID);
        if ($post_featured_image) {
            echo '<img src="' . $post_featured_image . '" />';
        }
        else {
            // NO FEATURED IMAGE, SHOW THE DEFAULT ONE
            echo '<img src="' . get_bloginfo( 'template_url' ) . '/images/default.jpg" />';
        }
    }

  
}

// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_slideshow_posts_columns', 'ST4_columns_head_only_slideshow', 10);
add_action('manage_slideshow_posts_custom_column', 'ST4_columns_content_only_slideshow', 10, 2);
 
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN

function cptui_register_my_cpts_slideshow() {

	/**
	 * Post Type: slides.
	 */

	$labels = array(
		"name" => __( 'slides', 'understrap-child' ),
		"singular_name" => __( 'slide', 'understrap-child' ),
	);

	$args = array(
		"label" => __( 'slides', 'understrap-child' ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slideshow", "with_front" => true ),
		"query_var" => true,
		"supports" => array( "title", "editor", "thumbnail" ),
	);

	register_post_type( "slideshow", $args );
}

add_action( 'init', 'cptui_register_my_cpts_slideshow' );

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_slideshow',
		'title' => 'Slideshow',
		'fields' => array (
			array (
				'key' => 'field_591b521d6d73b',
				'label' => 'Article',
				'name' => 'article',
				'type' => 'post_object',
				'required' => 1,
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_591b52836d73c',
				'label' => 'Post Home Page title',
				'name' => 'post_home_page_title',
				'type' => 'text',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_591b52b46d73d',
				'label' => '',
				'name' => '',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'slideshow',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

