<?php 
/*
 Plugin Name: Nikah Wedding Pro Posttype
 Plugin URI: https://www.themeseye.com/
 Description: Creating new post type for Nikah Wedding Pro Theme
 Author: Themes Eye
 Version: 1.0
 Author URI: https://www.themeseye.com/
*/

define( 'nikah_wedding_pro_posttype_version', '1.0' );
add_action( 'init', 'nikah_wedding_pro_posttype_create_post_type' );

function nikah_wedding_pro_posttype_create_post_type() {

	register_post_type( 'events',
    array(
        'labels' => array(
            'name' => __( 'Events','nikah-wedding-pro-posttype' ),
            'singular_name' => __( 'Events','nikah-wedding-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
      )
    )
	);

  register_post_type( 'family',
    array(
        'labels' => array(
            'name' => __( 'Our Family','nikah-wedding-pro-posttype' ),
            'singular_name' => __( 'Our Family','nikah-wedding-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-welcome-learn-more',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
      )
    )
  ); 
}

// ----------------- Events Meta -------------------

// Serives section
function nikah_wedding_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('nikah-wedding-pro-posttype-pro-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'nikah_wedding_pro_posttype_images_metabox_enqueue');

function nikah_wedding_pro_posttype_bn_custom_meta_events() {

    add_meta_box( 'bn_meta', __( 'Events Meta', 'nikah-wedding-pro-posttype' ), 'nikah_wedding_pro_posttype_bn_meta_callback_events', 'events', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
	add_action('admin_menu', 'nikah_wedding_pro_posttype_bn_custom_meta_events');
}

function nikah_wedding_pro_posttype_bn_meta_callback_events( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $service_image = get_post_meta( $post->ID, 'meta-image', true );
    $eventdate = get_post_meta( $post->ID, 'meta-event-date', true );
    $eventtime = get_post_meta( $post->ID, 'meta-event-time', true );
    $eventlocation = get_post_meta( $post->ID, 'meta-event-location', true );
    ?>
	<div id="property_stuff">
		<table id="list-table">			
			<tbody id="the-list" data-wp-lists="list:meta">
			  <tr id="meta-1">
          <p>
            <label for="meta-image"><?php echo esc_html('Icon Image'); ?></label><br>
            <input type="text" name="meta-image" id="meta-image" class="meta-image regular-text" value="<?php echo esc_attr( $service_image ); ?>">
            <input type="button" class="button image-upload" value="Browse">
          </p>
          <div class="image-preview"><img src="<?php echo $bn_stored_meta['meta-image'][0]; ?>" style="max-width: 250px;"></div>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php esc_html_e( 'Event Date', 'nikah-wedding-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-event-date" id="meta-event-date" value="<?php echo esc_attr( $eventdate ); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php esc_html_e( 'Event Time', 'nikah-wedding-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-event-time" id="meta-event-time" value="<?php echo esc_attr( $eventtime ); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php esc_html_e( 'Event Location', 'nikah-wedding-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-event-location" id="meta-event-location" value="<?php echo esc_attr( $eventlocation ); ?>" />
          </td>
        </tr>
      </tbody>
		</table>
	</div>
	<?php
}

function nikah_wedding_pro_posttype_bn_meta_save_events( $post_id ) {

	if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	
  
  // Save Event Date
  if( isset( $_POST[ 'meta-event-date' ] ) ) {
    update_post_meta( $post_id, 'meta-event-date', sanitize_text_field($_POST[ 'meta-event-date' ]) );
  }
  // Save Event Time
  if( isset( $_POST[ 'meta-event-time' ] ) ) {
    update_post_meta( $post_id, 'meta-event-time', sanitize_text_field($_POST[ 'meta-event-time' ]) );
  }
  // Save Event Location
  if( isset( $_POST[ 'meta-event-location' ] ) ) {
    update_post_meta( $post_id, 'meta-event-location', sanitize_text_field($_POST[ 'meta-event-location' ]) );
  }

  // Save Image
  if( isset( $_POST[ 'meta-image' ] ) ) {
      update_post_meta( $post_id, 'meta-image', esc_url_raw($_POST[ 'meta-image' ]) );
  }
}
add_action( 'save_post', 'nikah_wedding_pro_posttype_bn_meta_save_events' );


/* --------------------- events shortcode  ------------------- */

function nikah_wedding_pro_posttype_events_func( $atts ) {
  $events = '';
  $thumb_url='';
  $events = '<div class="row">';
  $query = new WP_Query( array( 'post_type' => 'events') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=events');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $event_image= get_post_meta(get_the_ID(), 'meta-image', true);
        $eventdate= get_post_meta($post_id,'meta-event-date',true);
        $eventtime= get_post_meta($post_id,'meta-event-time',true);
        $eventlocation= get_post_meta($post_id,'meta-event-location',true);
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        
        if(get_post_meta($post_id,'meta-events-url',true !='')){$custom_url =get_post_meta($post_id,'meta-events-url',true); } else{ $custom_url = get_permalink(); }
        $events .= '<div class="col-lg-4 col-md-6 col-sm-12">
                      <div class="events-box">
                        <div class="row">
                          <div class="col-lg-12">
                           <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                            <div class="events_icon">
                              <img class="" src="'.esc_url($event_image).'">
                            </div>
                            <p class="event-data">
                            <i class="fas fa-calendar-alt"></i>
                              '.$eventdate.'
                            </p>
                            <p class="event-data">
                            <i class="far fa-clock"></i>
                              '.$eventtime.'
                            </p>
                            <p class="event-data">
                            <i class="fas fa-map-marker-alt"></i>
                              '.$eventlocation.'
                            </p>
                          <a class="read-more" href="'.esc_url($custom_url).'">
                          <i class="fas fa-arrow-right"></i>
                          Read More</a>
                          </div>
                        </div>
                      </div>
                    </div>';


    if($k%2 == 0){
      $events.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $events = '<h2 class="center">'.esc_html__('Post Not Found','nikah-wedding-pro-posttype-pro').'</h2>';
  endif;
  $events .= '</div>';
  return $events;
}

add_shortcode( 'list-events', 'nikah_wedding_pro_posttype_events_func' );

/* ----------------- Family ---------------- */

function nikah_wedding_pro_posttype_bn_designation_meta() {
    add_meta_box( 'nikah_wedding_pro_posttype_bn_meta', __( 'Enter Details','nikah-wedding-pro-posttype' ), 'nikah_wedding_pro_posttype_bn_meta_callback', 'family', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'nikah_wedding_pro_posttype_bn_designation_meta');
}
/* Adds a meta box for custom post */
function nikah_wedding_pro_posttype_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'nikah_wedding_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $meta_designation = get_post_meta( $post->ID, 'meta-designation', true );
    $meta_team_email = get_post_meta( $post->ID, 'meta-family-email', true );
    $meta_team_call = get_post_meta( $post->ID, 'meta-family-call', true );
    $meta_team_face = get_post_meta( $post->ID, 'meta-facebookurl', true );
    $meta_team_twit = get_post_meta( $post->ID, 'meta-twitterurl', true );
    $meta_team_gplus = get_post_meta( $post->ID, 'meta-googleplusurl', true );
    $meta_team_pint = get_post_meta( $post->ID, 'meta-pinteresturl', true );
    $meta_team_inst = get_post_meta( $post->ID, 'meta-instagramurl', true );
    ?>
    <div id="family_custom_stuff">
        <table id="list-table">         
          <tbody id="the-list" data-wp-lists="list:meta">
              <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Relation', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($meta_designation); ?>" />
                </td>
              </tr>
              <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Email', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-family-email" id="meta-family-email" value="<?php echo esc_attr($meta_team_email); ?>" />
                </td>
              </tr>
               <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Phone', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-family-call" id="meta-family-call" value="<?php echo esc_attr($meta_team_call); ?>" />
                </td>
              </tr>
              <tr id="meta-3">
                <td class="left">
                  <?php esc_html_e( 'Facebook Url', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_attr($meta_team_face); ?>" />
                </td>
              </tr>
              <tr id="meta-5">
                <td class="left">
                  <?php esc_html_e( 'Twitter Url', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_attr($meta_team_twit); ?>" />
                </td>
              </tr>
              <tr id="meta-6">
                <td class="left">
                  <?php esc_html_e( 'GooglePlus URL', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_attr($meta_team_gplus); ?>" />
                </td>
              </tr>
              <tr id="meta-7">
                <td class="left">
                  <?php esc_html_e( 'Pinterest URL', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-pinteresturl" id="meta-pinteresturl" value="<?php echo esc_attr($meta_team_pint); ?>" />
                </td>
              </tr>
               <tr id="meta-8">
                <td class="left">
                  <?php esc_html_e( 'Instagram URL', 'nikah-wedding-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-instagramurl" id="meta-instagramurl" value="<?php echo esc_attr($meta_team_inst); ?>" />
                </td>
              </tr>
          </tbody>
        </table>
    </div>
    <?php
}

/* Saves the custom fields meta input */
function nikah_wedding_pro_posttype_bn_metadesig_family_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', sanitize_text_field($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', sanitize_text_field($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url_raw($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url_raw($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url_raw($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url_raw($_POST[ 'meta-googleplusurl' ]) );
    }

    // Save Pinterest
    if( isset( $_POST[ 'meta-pinteresturl' ] ) ) {
        update_post_meta( $post_id, 'meta-pinteresturl', esc_url_raw($_POST[ 'meta-pinteresturl' ]) );
    }

     // Save Instagram
    if( isset( $_POST[ 'meta-instagramurl' ] ) ) {
        update_post_meta( $post_id, 'meta-instagramurl', esc_url_raw($_POST[ 'meta-instagramurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', sanitize_text_field($_POST[ 'meta-designation' ]) );
    }

    // Save Email
    if( isset( $_POST[ 'meta-family-email' ] ) ) {
        update_post_meta( $post_id, 'meta-family-email', sanitize_text_field($_POST[ 'meta-family-email' ]) );
    }
    // Save Call
    if( isset( $_POST[ 'meta-family-call' ] ) ) {
        update_post_meta( $post_id, 'meta-family-call', sanitize_text_field($_POST[ 'meta-family-call' ]) );
    }
}
add_action( 'save_post', 'nikah_wedding_pro_posttype_bn_metadesig_family_save' );

/* family shorthcode */
function nikah_wedding_pro_posttype_family_func( $atts ) {
    $family = ''; 
    $custom_url ='';
    $family = '<div class="row">';
    $query = new WP_Query( array( 'post_type' => 'family' ) );
    if ( $query->have_posts() ) :
    $k=1;
    $new = new WP_Query('post_type=family'); 
    while ($new->have_posts()) : $new->the_post();
    	$post_id = get_the_ID();
    	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
      if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
		  $url = $thumb['0'];
      $excerpt = wp_trim_words(get_the_excerpt(),25);
      $designation= get_post_meta($post_id,'meta-designation',true);
      $facebookurl= get_post_meta($post_id,'meta-facebookurl',true);
      $linkedin=get_post_meta($post_id,'meta-linkdenurl',true);
      $twitter=get_post_meta($post_id,'meta-twitterurl',true);
      $googleplus=get_post_meta($post_id,'meta-googleplusurl',true);
      $pinterest=get_post_meta($post_id,'meta-pinteresturl',true);
      $instagram=get_post_meta($post_id,'meta-instagramurl',true);
      $family .= '<div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="family_box">
                    <div class="image-box ">
                      <div class="box image-overlay">
                        <img class="client-img" src="'.esc_url($thumb_url).'" alt="family-thumbnail" />
                        <div class="box-content family-box">
                          <h4 class="family_name"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>
                          <p class="designation">'.esc_html($designation).'</p>
                        </div>
                      </div>
                    </div>
                  <div class="content_box w-100">
                    <div class="family-socialbox">
                      <div class="family_socialbox">';
                        if($facebookurl != ''){
                          $family .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                        } if($twitter != ''){
                          $family .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                        } if($googleplus != ''){
                          $family .= '<a class="" href="'.esc_url($googleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                        } if($linkedin != ''){
                          $family .= '<a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                        }if($pinterest != ''){
                          $family .= '<a class="" href="'.esc_url($pinterest).'" target="_blank"><i class="fab fa-pinterest-p"></i></a>';
                        }if($instagram != ''){
                          $family .= '<a class="" href="'.esc_url($instagram).'" target="_blank"><i class="fab fa-instagram"></i></a>';
                        }
                      $family .= '</div>
                    </div>
                  </div>
                </div>
              </div>
                ';

      if($k%2 == 0){
          $family.= '<div class="clearfix"></div>'; 
      } 
      $k++;         
  endwhile; 
  wp_reset_postdata();
  $family.= '</div>';
  else :
    $family = '<h2 class="center">'.esc_html_e('Not Found','nikah-wedding-pro-posttype').'</h2>';
  endif;
  return $family;
}
add_shortcode( 'family', 'nikah_wedding_pro_posttype_family_func' );







