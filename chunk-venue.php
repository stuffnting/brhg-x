<?php

/**
 * Prepends the venue meta details to s single Venue post.
 *
 * brhg2016_get_item_meta_singles() is in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */


// Display the OpenStreetMap for ACF map
the_field('openstreetmap-venue');

$venue_details = array(
    'Address'   =>  array(
        'venue_postcode',
        'venue_address1',
        'venue_address2',
        'venue_address3',
        'venue_postcode',
        'venue_city'
    ),
    'email'   =>  array(
        'venue_email'
    ),
    'Phone Number'   =>  array(
        'venue_phone'
    ),
    'Website'   =>  array(
        'venue_website'
    ),
);

foreach ($venue_details as $title => $detail) {
    if (brhg2016_get_item_meta_singles($detail[0], false)) { ?>
        <h3 class="venue-details-title"><?php echo $title; ?></h3>
<?php
        if ($title === 'Address') {
            array_shift($detail);
        }

        foreach ($detail as $key => $value) {
            if (brhg2016_get_item_meta_singles($value, false)) {
                $meta = brhg2016_get_item_meta_singles($value, false);
                printf(
                    "<span class='venue-details venue-details-%s'>%s%s%s</span>%s\n",
                    $value,
                    ($title === 'Website') ? "<a href='" . $meta . "' target='blank'>" : '',
                    ($title === 'email') ? brhg2016_encode_email($meta) : $meta,
                    ($title === 'Website') ? "</a>" : '',
                    (count($detail) > $key + 1) ?  '<br>' : ''
                );
            }
        }
    }
}
