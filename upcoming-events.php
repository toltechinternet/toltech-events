<?php
/**
 * Plugin Name: Toltech Events
 * Plugin URI: http://www.toltech.co.uk
 * Description: A plugin to show a list of upcoming events.
 * Version: 1.0
 * Author: Toltech Internet Solutions
 * Author URI: http://www.toltech.co.uk
 */


// Define Directory
define( 'ROOT', plugins_url( '', __FILE__ ) );
define( 'IMAGES', ROOT . '/img/' );
define( 'STYLES', ROOT . '/css/' );
define( 'SCRIPTS', ROOT . '/js/' );


// Define jQuery & Date Picker
function uep_admin_script_style( $hook ) {
    global $post_type;
 
    if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && ( 'event' == $post_type ) ) {
        wp_enqueue_script(
            'upcoming-events',
            SCRIPTS . 'script.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            '1.0',
            true
        );
 
        wp_enqueue_style(
            'jquery-ui-calendar',
            STYLES . 'jquery-ui-1.10.4.min.css',
            false,
            '1.10.4',
            'all'
        );

        wp_enqueue_script(
            'Time Picker JS',
            SCRIPTS . 'jquery.ui.timepicker.js',
            '1.0',
            true
        );
 
        wp_enqueue_style(
            'Time Picker CSS',
            STYLES . 'jquery.ui.timepicker.css',
            false,
            '1.10.4',
            'all'
        );
    }
}
add_action( 'admin_enqueue_scripts', 'uep_admin_script_style' );


// Define Custom Post Type
function uep_custom_post_type() {
    $labels = array(
        'name'                  =>   __( 'Events', 'uep' ),
        'singular_name'         =>   __( 'Event', 'uep' ),
        'add_new_item'          =>   __( 'Add New Event', 'uep' ),
        'all_items'             =>   __( 'All Events', 'uep' ),
        'edit_item'             =>   __( 'Edit Event', 'uep' ),
        'new_item'              =>   __( 'New Event', 'uep' ),
        'view_item'             =>   __( 'View Event', 'uep' ),
        'not_found'             =>   __( 'No Events Found', 'uep' ),
        'not_found_in_trash'    =>   __( 'No Events Found in Trash', 'uep' )
    );
 
    $supports = array(
        'title',
        'editor',
        'excerpt',
        'thumbnail'
    );
 
    $args = array(
        'label'         =>   __( 'Events', 'uep' ),
        'labels'        =>   $labels,
        'description'   =>   __( 'A list of upcoming events', 'uep' ),
        'public'        =>   true,
        'show_in_menu'  =>   true,
        'menu_icon'     =>   'dashicons-calendar',
        'has_archive'   =>   true,
        'rewrite'       =>   true,
        'supports'      =>   $supports
    );
 
    register_post_type( 'event', $args );
}
add_action( 'init', 'uep_custom_post_type' );



// Define Metaboxes
function uep_add_event_info_metabox() {
    add_meta_box(
        'uep-event-info-metabox',
        __( 'Event Information', 'uep' ),
        'uep_render_event_info_metabox',
        'event',
        'side',
        'core'
    );

}
add_action( 'add_meta_boxes', 'uep_add_event_info_metabox' );

function uep_render_event_info_metabox( $post ) {
 
    // generate a nonce field
    wp_nonce_field( basename( __FILE__ ), 'uep-event-info-nonce' );
 
    // get previously saved meta values (if any)
    $event_start_date = get_post_meta( $post->ID, 'event-start-date', true );
    $event_end_date = get_post_meta( $post->ID, 'event-end-date', true );
    $event_venue = get_post_meta( $post->ID, 'event-venue', true );
    $event_time = get_post_meta( $post->ID, 'event-time', true );
   
    $event_address = get_post_meta( $post->ID, 'event-address', true );
    $event_town = get_post_meta( $post->ID, 'event-town', true );
    $event_postcode = get_post_meta( $post->ID, 'event-postcode', true );
    $event_country = get_post_meta( $post->ID, 'event-country', true );
 
    // if there is previously saved value then retrieve it, else set it to the current time
    $event_start_date = ! empty( $event_start_date ) ? $event_start_date : time();
 
    //we assume that if the end date is not present, event ends on the same day
    $event_end_date = ! empty( $event_end_date ) ? $event_end_date : $event_start_date;
 
    ?>
 
	<label for="uep-event-start-date"><?php _e( 'Event Start Date:', 'uep' ); ?></label>
	        <input style="margin-bottom: 10px;" class="widefat uep-event-date-input" id="uep-event-start-date" type="text" name="uep-event-start-date" placeholder="Format: February 18, 2014" value="<?php echo date( 'F d, Y', $event_start_date ); ?>" />
 
	<label or="uep-event-end-date"><?php _e( 'Event End Date:', 'uep' ); ?></label>
	        <input style="margin-bottom: 10px;" class="widefat uep-event-date-input" id="uep-event-end-date" type="text" name="uep-event-end-date" placeholder="Format: February 18, 2014" value="<?php echo date( 'F d, Y', $event_end_date ); ?>" />
 
	<label for="uep-event-venue"><?php _e( 'Event Venue:', 'uep' ); ?></label>
	        <input style="margin-bottom: 10px;" class="widefat" id="uep-event-venue" type="text" name="uep-event-venue" placeholder="eg. Times Square" value="<?php echo $event_venue; ?>" />
    
   <label for="uep-event-time"><?php _e( 'Event Time:', 'uep' ); ?></label>
            <input style="margin-bottom: 10px;" class="widefat" id="uep-event-time" type="text" name="uep-event-time" placeholder="eg. 1:00pm" value="<?php echo $event_time; ?>" /><br /><br />
    
    <label for="uep-event-address"><?php _e( 'Address:', 'uep' ); ?></label>
            <input style="margin-bottom: 10px;" class="widefat" id="uep-event-address" type="text" name="uep-event-address" placeholder="eg. 22 Pottery Street" value="<?php echo $event_address; ?>" />
   
    <label for="uep-event-town"><?php _e( 'Town:', 'uep' ); ?></label>
            <input style="margin-bottom: 10px;" class="widefat" id="uep-event-town" type="text" name="uep-event-town" placeholder="eg. Greenock" value="<?php echo $event_town; ?>" />
   
    <label for="uep-event-postcode"><?php _e( 'Postcode:', 'uep' ); ?></label>
            <input style="margin-bottom: 10px;"class="widefat" id="uep-event-postcode" type="text" name="uep-event-postcode" placeholder="eg. PA15 2UZ" value="<?php echo $event_postcode; ?>" />
   
    <label for="uep-event-country"><?php _e( 'Country:', 'uep' ); ?></label>
            <input style="margin-bottom: 10px;" class="widefat" id="uep-event-country" type="text" name="uep-event-country" placeholder="eg. Scotland" value="<?php echo $event_country; ?>" />

    
<?php } ?>



<?php 

// Define Custom Post Type Save
function uep_save_event_info( $post_id ) {
 
    // checking if the post being saved is an 'event',
    // if not, then return
    if ( 'event' != $_POST['post_type'] ) {
        return;
    }
 
    // checking for the 'save' status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST['uep-event-info-nonce'] ) && ( wp_verify_nonce( $_POST['uep-event-info-nonce'], basename( __FILE__ ) ) ) ) ? true : false;
 
    // exit depending on the save status or if the nonce is not valid
    if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
        return;
    }
 
    // checking for the values and performing necessary actions
    if ( isset( $_POST['uep-event-start-date'] ) ) {
        update_post_meta( $post_id, 'event-start-date', strtotime( $_POST['uep-event-start-date'] ) );
    }
 
    if ( isset( $_POST['uep-event-end-date'] ) ) {
        update_post_meta( $post_id, 'event-end-date', strtotime( $_POST['uep-event-end-date'] ) );
    }
 
    if ( isset( $_POST['uep-event-venue'] ) ) {
        update_post_meta( $post_id, 'event-venue', sanitize_text_field( $_POST['uep-event-venue'] ) );
    }

    if ( isset( $_POST['uep-event-time'] ) ) {
        update_post_meta( $post_id, 'event-time', sanitize_text_field( $_POST['uep-event-time'] ) );
    }

    if ( isset( $_POST['uep-event-address'] ) ) {
        update_post_meta( $post_id, 'event-address', sanitize_text_field( $_POST['uep-event-address'] ) );
    }
    
    if ( isset( $_POST['uep-event-town'] ) ) {
        update_post_meta( $post_id, 'event-town', sanitize_text_field( $_POST['uep-event-town'] ) );
    }
    
    if ( isset( $_POST['uep-event-postcode'] ) ) {
        update_post_meta( $post_id, 'event-postcode', sanitize_text_field( $_POST['uep-event-postcode'] ) );
    }
    
    if ( isset( $_POST['uep-event-country'] ) ) {
        update_post_meta( $post_id, 'event-country', sanitize_text_field( $_POST['uep-event-country'] ) );
    }
}
add_action( 'save_post', 'uep_save_event_info' );



// Define Custome Post Type Backend Column Headings (Start Date, End Date, Venue)
function uep_custom_columns_head( $defaults ) {
    unset( $defaults['date'] );
 
    $defaults['event_start_date'] = __( 'Start Date', 'uep' );
    $defaults['event_end_date'] = __( 'End Date', 'uep' );
    $defaults['event_venue'] = __( 'Venue', 'uep' );
 
    return $defaults;
}
add_filter( 'manage_edit-event_columns', 'uep_custom_columns_head', 10 );


function uep_custom_columns_content( $column_name, $post_id ) {
 
    if ( 'event_start_date' == $column_name ) {
        $start_date = get_post_meta( $post_id, 'event-start-date', true );
        echo date( 'F d, Y', $start_date );
    }
 
    if ( 'event_end_date' == $column_name ) {
        $end_date = get_post_meta( $post_id, 'event-end-date', true );
        echo date( 'F d, Y', $end_date );
    }
 
    if ( 'event_venue' == $column_name ) {
        $venue = get_post_meta( $post_id, 'event-venue', true );
        echo $venue;
    }
}
add_action( 'manage_event_posts_custom_column', 'uep_custom_columns_content', 10, 2 );