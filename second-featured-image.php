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
 

    <?php 
    if ( isset ( $sfi_stored_meta['sfi-image'] ) ) : ?>
        
        <div id="sfi-thumbnail">
            <img id="sfi-thumbnail" src="<?php echo $sfi_stored_meta['sfi-image'][0]; ?>" class="attachment-post-thumbnail" ></a>
        </div>

    <?php
    endif;
    ?>


    




	<p>
        
	    <input type="text" name="sfi-image" id="sfi-image" value="<?php if ( isset ( $sfi_stored_meta['sfi-image'] ) ) echo $sfi_stored_meta['sfi-image'][0]; ?>" />

	    <input type="button" id="sfi-image-button" class="button" value="<?php _e( 'Add Slide', 'sfi-textdomain' )?>" />
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
	if( isset( $_POST[ 'sfi-image' ] ) ) {
	    update_post_meta( $post_id, 'sfi-image', $_POST[ 'sfi-image' ] );
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
        wp_register_script( 'second-featured-image', plugin_dir_url( __FILE__ ) . 'second-featured-image.js', array( 'jquery' ) );
        wp_localize_script( 'second-featured-image', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'sfi-textdomain' ),
                'button' => __( 'Use this image', 'sfi-textdomain' ),
            )
        );
        
        wp_enqueue_script( 'second-featured-image' );
    }
}
add_action( 'admin_enqueue_scripts', 'sfiEnqueue' );





/**
 * returns an array of the images stored in the sfi-image metabox
 * args: post_id
 */
function sfiGetImage( $post_id ){
    $sfi_image = get_post_meta( $post_id, 'sfi-image');
    return $sfi_image[0];
}

?>