<?php
/*
*
* Loop for thumbs only archive pages
*
*/
?>

<article class="thumb-only-listing">

	<?php
  if (!has_post_thumbnail()) {
    $class = 'thumb-missing';
  } else {
    $class = '';
  }
  ?>

	<?php
  $thumb_attr = array(
    'class'    => 'thumb-only-listing__item-img',
    'alt'      => the_title('', '', false) . __(' Poster', 'brhg2016'),
    'loading' => 'lazy'
  );
  the_post_thumbnail('big_thumb', $thumb_attr);
  ?>
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="thumb-only-listing__item-link">
		<h2 class="thumb-only-listing__item-title"><?php the_title(); ?></h2>
	</a>

</article>