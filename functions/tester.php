<?php

$test_array = array('Pants', 'vest', 'long johns');

function my_funk($array) {
  foreach ($array as $item) {
    echo $item;
  }
}

my_funk($test_array);
