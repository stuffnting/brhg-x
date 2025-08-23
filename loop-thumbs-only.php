<?php
/*
*
* Loop for thumbs only archive pages
*
*/
?>

<article class="thumb-only-listing">

<?php 
	if ( !has_post_thumbnail() ) {
		$class = 'thumb-missing';
	} else {
		$class = '';
	} 
?>
								
	<a href ="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="thumb-only-listing-link">
<?php
	$thumb_attr = array(
		'class'		=>'thumb-only-listing-img',
		'alt'			=> the_title('', '', false).__( ' Poster', 'brhg2016' ),
		'loading' => 'lazy'
	);
	the_post_thumbnail( 'big_thumb', $thumb_attr ); 
?>
		<h2 class="thumb-only-listing-title wordbreak <?php echo $class ; ?>"><?php the_title(); ?></h2>
	</a>

</article>