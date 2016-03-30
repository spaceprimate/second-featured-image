<?php
 
/*
Plugin Name: DTG Second Featured Image
Plugin URI: http://cypresscreative.com
Description: Creates an additional meta-box for creating an additional featured image
Author: Cypress Creative
Version: 0.2.0
Author URI: http://cypresscreative.com
*/


/**
 * Adds a meta box to the post editing screen
 */
function sfi_custom_meta() {
    add_meta_box( 'sfi_meta', __( 'App Featured Image', 'sfi-textdomain' ), 'sfi_meta_callback' );
}
add_action( 'add_meta_boxes', 'sfi_custom_meta' );


/**
 * Outputs the content of the meta box
 */
function sfi_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'sfi_nonce' );
    $sfi_stored_meta = get_post_meta( $post->ID );
    ?>
 
    <ul class="meta-thumbs"></ul>

	<p>
	    <input type="hidden" name="sfi-images" id="sfi-images" value="<?php if ( isset ( $sfi_stored_meta['sfi-images'] ) ) echo $sfi_stored_meta['sfi-images'][0]; ?>" />
	    <input type="button" id="sfi-images-button" class="button" value="<?php _e( 'Add Slide', 'sfi-textdomain' )?>" />
	</p>
 
    <?php
}


/**
 * Saves the custom meta input
 */
function sfiSave( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'sfi_nonce' ] ) && wp_verify_nonce( $_POST[ 'sfi_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

	// Checks for input and saves if needed
	if( isset( $_POST[ 'sfi-images' ] ) ) {
	    update_post_meta( $post_id, 'sfi-images', $_POST[ 'sfi-images' ] );
	}

 
}
add_action( 'save_post', 'sfiSave' );


/**
 * Adds the meta box stylesheet when appropriate
 * change the $typenow check as needed
 */
function sfi_admin_styles(){
    global $typenow;
    if( $typenow == 'post' || $typenow == 'page') {
        wp_enqueue_style( 'sfi_meta_box_styles', plugin_dir_url( __FILE__ ) . 'main.css' );
    }
}
add_action( 'admin_print_styles', 'sfi_admin_styles' );


/**
 * Loads the image management javascript
 */
function sfiEnqueue() {
    global $typenow;
    if( $typenow == 'post' || $typenow == 'page' ) {
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'multi-slide-box', plugin_dir_url( __FILE__ ) . 'multi-slide-box.js', array( 'jquery' ) );
        wp_localize_script( 'multi-slide-box', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'sfi-textdomain' ),
                'button' => __( 'Use this image', 'sfi-textdomain' ),
            )
        );
        
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'multi-slide-box' );
    }
}
add_action( 'admin_enqueue_scripts', 'sfiEnqueue' );



add_action( 'init', 'addThumbSize' );
function addThumbSize() {
    add_image_size( 'admin-thumb', 120, 120, true ); //admin thumb
}

/**
 * returns an array of the images stored in the sfi-images metabox
 * args: post_id
 */
function sfiGetSlides( $post_id ){
    $sfi_slides = get_post_meta( $post_id, 'sfi-images');
    return explode(',', $sfi_slides[0]);
}

?>