<?php

/**
 * Shortcode that generates the BRHG publication prices list.
 */

add_shortcode('brhg-publications-price-list', 'brhg2024_make_publications_price_list');

function brhg2024_make_publications_price_list() {

  $range_array = get_transient("brhg_publications_price_list");

  // If the transient does not exist, does not have a value, or has expired, then get_transient will return false.
  if (!$range_array) {
    return;
  }

  $table_headers = array(
    array('header' => '#', 'class_ext' => 'number', 'tooltip' => 'Publication number'),
    array('header' => 'Title', 'class_ext' => 'title', 'tooltip' => 'Publication title'),
    array('header' => 'ISBN', 'class_ext' => 'isbn', 'tooltip' => 'International Standard Book Number'),
    array('header' => 'Edition', 'class_ext' => 'edition', 'tooltip' => 'Details of the current edition'),
    array('header' => 'PP', 'class_ext' => 'pp', 'tooltip' => 'Number of printed pages in the publication'),
    array('header' => 'PI', 'class_ext' => 'pi', 'tooltip' => 'Number of printed images&sol;illustrations in the publication'),
    array('header' => 'Format', 'class_ext' => 'format', 'tooltip' => 'Publication&apos;s physical format'),
    array('header' => 'RRP', 'class_ext' => 'rrp', 'tooltip' => 'Recommended Retail Price'),
    array('header' => 'Notes', 'class_ext' => 'notes', 'tooltip' => 'Further details'),
  );

  $list_html = '';
  $table_headers_html = '';

  foreach ($table_headers as $header) {

    $table_headers_html .= sprintf(
      "<th title='%s' class='%s'>%s</th>\n",
      $header['tooltip'],
      "price-list__head price-list__head--{$header['class_ext']}",
      $header['header']
    );
  }

  foreach ($range_array as $range) {
    $pub_rows = '';

    foreach ($range['publications_in_range'] as $publication) {
      extract($publication, EXTR_OVERWRITE);

      $note_html = !empty($notes) ? "<span class='price-list__pop-note-btn'>[NOTE]</span><span class='price-list__pop-note'>$notes</span>" : '';

      $pub_rows .= sprintf(
        "<tr>
          <td class='price-list__cell price-list__cell--" . $table_headers[0]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[1]['class_ext'] . "'><a href='%s'>%s</a></td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[2]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[3]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[4]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[5]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[6]['class_ext'] . "'>%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[7]['class_ext'] . "'>£%s</td>\n
          <td class='price-list__cell price-list__cell--" . $table_headers[8]['class_ext'] . "'>%s</td>\n
        </tr>\n",
        $publication_number,
        $url,
        brhg2016_trim_things($title, 60),
        $isbn,
        str_replace(array('Edition', 'Revised'), array('Ed', 'Rev'), $edition),
        $pages,
        $images,
        $format,
        $price,
        $note_html
      );
    }

    // Make the table for this pub range
    $list_html .= sprintf(
      "<h2 class='price-list__title'>%s</h2>\n
      <p class='price-list__scroll'>(Drag left/right)</p>\n
      <div class='price-list__table-wrap'>\n
        <table class='price-list__table'>\n
          <thead>\n
            <tr>\n
              %s
            </tr>\n
          </thead>\n
          <tbody>\n
              %s
          </tbody>\n
        </table>\n
      </div>\n",
      $range['publication_range_name'],
      $table_headers_html,
      $pub_rows,
    );
  }

  return !empty($list_html) ? "<div class='price-list'>{$list_html}</div>" : '';
}
