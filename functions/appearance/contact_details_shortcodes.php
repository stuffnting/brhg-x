<?php
/**
 * Contact details shortcode.
 * 
 * Used to display the contact details in a graphic.
 * brhg2016_encode_email() is in functions/utility_functions.php
 */

add_shortcode( 'contact-details', 'brhg2016_make_contact_details' );

function brhg2016_make_contact_details( $atts ){

  extract( shortcode_atts( array(
     'frame' => false,
  ), $atts, 'contact-details' ) );

  $address_html = '';

  if ( have_rows( 'brhg_details_address', 'options' ) ) :

    $no_address_lines = count( get_field( 'brhg_details_address', 'options' ) );

    while ( have_rows( 'brhg_details_address', 'options' ) ) : the_row();

      $address_html .= get_sub_field( 'brhg_details_address_line' );
      $address_html .= ( $no_address_lines > 	get_row_index() ) ? "<br>\n" : "" ;

    endwhile;

  else :

    return;

  endif;

  $contact_email = get_field( 'brhg_contact_email_address', 'options' );

  if ( !empty( $contact_email ) ) {
    $address_html .= "<br>\n" . brhg2016_encode_email( $contact_email );
  }

  $classes = $frame ? 'contact-details contact-details-frame' : 'contact-details' ;
  
  $html_out = "<div class='$classes'><address>$address_html</a></div>";

  return $html_out;

}

/**
 * A shortcode that returns an email link containing the shop help email from the BRHG Details options page.
 */
add_shortcode( 'shop-help-email', 'brhg2024_shop_help_email' );

function brhg2024_shop_help_email( $atts, $content = '' ) {

    $email = brhg2024_get_shop_help_email( true );

    return "<a href='mailto:$email'>$content</a>";

}