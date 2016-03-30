<?php
 
/*
Plugin Name: Multi Slide Box
Plugin URI: http://cypresscreative.com
Description: Creates a metabox for adding and sorting multiple images
Author: Daniel Murphy
Version: 1.2.0
Author URI: http://danielmurphy.org
*/


/**
 * Adds a meta box to the post editing screen
 */
function msb_custom_meta() {
    add_meta_box( 'msb_meta', __( 'Background Slides', 'msb-textdomain' ), 'msb_meta_callback' );
}
add_action( 'add_meta_boxes', 'msb_custom_meta' );


/**
 * Outputs the content of the meta box
 */
function msb_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'msb_nonce' );
    $msb_stored_meta = get_post_meta( $post->ID );
    ?>
 
    <ul class="meta-thumbs"></ul>

	<p>
	    <input type="hidden" name="msb-images" id="msb-images" value="<?php if ( isset ( $msb_stored_meta['msb-images'] ) ) echo $msb_stored_meta['msb-images'][0]; ?>" />
	    <input type="button" id="msb-images-button" class="button" value="<?php _e( 'Add Slide', 'msb-textdomain' )?>" />
	</p>
 
    <?php
}


/**
 * Saves the custom meta input
 */
function msbSave( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'msb_nonce' ] ) && wp_verify_nonce( $_POST[ 'msb_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

	// Checks for input and saves if needed
	if( isset( $_POST[ 'msb-images' ] ) ) {
	    update_post_meta( $post_id, 'msb-images', $_POST[ 'msb-images' ] );
	}

 
}
add_action( 'save_post', 'msbSave' );


/**
 * Adds the meta box stylesheet when appropriate
 * change the $typenow check as needed
 */
function msb_admin_styles(){
    global $typenow;
    if( $typenow == 'post' || $typenow == 'page') {
        wp_enqueue_style( 'msb_meta_box_styles', plugin_dir_url( __FILE__ ) . 'main.css' );
    }
}
add_action( 'admin_print_styles', 'msb_admin_styles' );


/**
 * Loads the image management javascript
 */
function msbEnqueue() {
    global $typenow;
    if( $typenow == 'post' || $typenow == 'page' ) {
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'multi-slide-box', plugin_dir_url( __FILE__ ) . 'multi-slide-box.js', array( 'jquery' ) );
        wp_localize_script( 'multi-slide-box', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'msb-textdomain' ),
                'button' => __( 'Use this image', 'msb-textdomain' ),
            )
        );
        
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'multi-slide-box' );
    }
}
add_action( 'admin_enqueue_scripts', 'msbEnqueue' );



add_action( 'init', 'addThumbSize' );
function addThumbSize() {
    add_image_size( 'admin-thumb', 120, 120, true ); //admin thumb
}

/**
 * returns an array of the images stored in the msb-images metabox
 * args: post_id
 */
function msbGetSlides( $post_id ){
    $msb_slides = get_post_meta( $post_id, 'msb-images');
    return explode(',', $msb_slides[0]);
}

?>