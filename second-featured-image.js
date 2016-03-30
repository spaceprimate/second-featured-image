/*
 * Attaches the image uploader to the input field
 * 
 */
jQuery(document).ready(function($){

    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;





    /*
     * returns the contens of #msb-images input as a javascript array
     */
    function get_meta_images(){
        return $("#msb-images").val().split(',');
    }




    /*
     * deletes corresponding slide from .meta-thumbs and from the #msb-images input
     */
    function remove_meta_image(){
        //var thumbArray = get_meta_images();
        $('#sfi-thumbnail').html("");
        $('#sfi-image').val("");
        $('#sfi-remove-image').addClass('hide');
        $('#sfi-set-image').removeClass('hide');
    }
 
    /*
     * Opens the wordpress media manager frame and sets actions for 
     * when the user makes their selections
     * Runs when Add Slide button is clicked
     */
    $('#sfi-set-image').click(function(e){
 
        // Prevents the default action from occuring.
        e.preventDefault();
 
        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }


        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });


        // Runs when an image is selected.
        meta_image_frame.on('select', function(){
            
            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

            // displays a thumbnail of the selected image
            $('#sfi-thumbnail').html("<img src='" + media_attachment.url + "' alt='thumbnail'>");

            // Sends the attachment URL to our custom image input field.
            $('#sfi-image').val(media_attachment.url);

            // hide / show appropriate links
            $('#sfi-remove-image').removeClass('hide');
            $('#sfi-set-image').addClass('hide');

        });
 
        // Opens the media library frame.
        meta_image_frame.open();
    });

    $('#sfi-remove-image').click(function(e){
 
        // Prevents the default action from occuring.
        e.preventDefault();


        remove_meta_image();


      });
    

/* -------------------------------------------------------------------------
--  Display initial state and set listeners
------------------------------------------------------------------------- */

    /*
     * calls remove_meta_image
     */
    $(".meta-thumbs a.del").live('click', function(e){
        e.preventDefault();
        remove_meta_image();
    });


});