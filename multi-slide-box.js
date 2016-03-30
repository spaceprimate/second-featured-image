/*
 * Attaches the image uploader to the input field
 * 
 */
jQuery(document).ready(function($){

    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;




    /*
     * recursive function - we need to ensure that thumbnails don't load out of order while we wait
     * on a server request
     * get the image urls from the .msb-images input and display them
     */
    function display_meta_images(thumbArray, i){

            if (thumbArray != "" && i != thumbArray.length){
   
                  $('.meta-thumbs').append("<li><img src='' height='100' class='" + i + "' /><br><a href='#' class='del' rel='" + i + "'>delete</a></li>");

                  wp.media.model.Attachment.get( thumbArray[i] ).fetch({success:function(att){ // where 7 is the id of a single attachment
                  //tempUrl = att.attributes.sizes.thumbnail.url; // { id: 7 }
                  
                    $('.meta-thumbs img.' + i ).attr("src", att.attributes.sizes.thumbnail.url );
                    $('.meta-thumbs img.' + i ).attr("title", att.attributes.id );

                  }});

                e = i + 1;
                
                display_meta_images(thumbArray, e);
            }
    }

    /*
     * clears .meta-thumb content to make way for updated slides thumbnails
     */
    function clear_meta_images(){
        $('.meta-thumbs').html("");
    } 

    /*
     * returns the contens of #msb-images input as a javascript array
     */
    function get_meta_images(){
        return $("#msb-images").val().split(',');
    }


    /*
     * updates the order in the #msb-images input when an image has been dragged/sorted
     * make sure to remove the -120x120 from the url
     */
    function update_image_order(){
        var i = 0;
        var thumbArray = new Array();
        $(".meta-thumbs li img").each(function() {
                
                thumbArray[i] =  $(this).attr("title");
                
                i++;
            });
        $('#msb-images').val(thumbArray.toString());
        clear_meta_images();
        display_meta_images(get_meta_images(), 0);

    }

    /*
     * deletes corresponding slide from .meta-thumbs and from the #msb-images input
     */
    function remove_meta_image(tn_index){
        var thumbArray = get_meta_images();
        thumbArray.splice(tn_index, 1);
        $('#msb-images').val(thumbArray.toString());
    }
 
    /*
     * Opens the wordpress media manager frame and sets actions for 
     * when the user makes their selections
     * Runs when Add Slide button is clicked
     */
    $('#msb-images-button').click(function(e){
 
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
            library: { type: 'image' },
            multiple: true
        });


        // Runs when an image is selected.
        meta_image_frame.on('select', function(){
            
            //get the current images from #msb-images input
            var imgArray = get_meta_images();

            //set i to +1 imgArray length, or to 0 if array is empty
            if (imgArray != "") var i = imgArray.length;
            else var i = 0;

            //get the objects of the images that have been selected
            var imgSelection = meta_image_frame.state().get('selection');

            // iterate through selected elements
            imgSelection.each(function(attachment) {
                imgArray[i] = attachment.attributes.id;
                i++;
            });

            //add updated array back to input field
            $('#msb-images').val(imgArray.toString());
            clear_meta_images();
            display_meta_images(get_meta_images(), 0);
        });
 
        // Opens the media library frame.
        meta_image_frame.open();
    });
    

/* -------------------------------------------------------------------------
--  Display initial state and set listeners
------------------------------------------------------------------------- */
  //This initiates the javascript for for the popup media manager
  //ONLY RUN IT IF THERE'S A .meta-thumbs field (otherwise everything breaks!)
  if ( $( ".meta-thumbs" ).length ){
    display_meta_images(get_meta_images(), 0);
  }
    /*
     * calls remove_meta_image
     */
    $(".meta-thumbs a.del").live('click', function(e){
        e.preventDefault();
        //alert("clicked");
        var tn_index = $(this).attr('rel');
        $(this).parent().remove();
        remove_meta_image(tn_index);
        clear_meta_images();
        display_meta_images(get_meta_images(), 0);

    });

    //$( ".meta-thumbs" ).sortable();
    $( ".meta-thumbs" ).sortable({
      stop: function( event, ui ) {update_image_order();}
    });
});