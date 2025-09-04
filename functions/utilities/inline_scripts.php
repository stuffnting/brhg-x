<?php

// Needs to run late, after jQuery and Tiny Slider have loaded.
add_action('wp_footer', 'brhg_x_footer_inline_scripts', 50);

function brhg_x_footer_inline_scripts() {
?>
  <!--Main menu with search modal-->
  <script>
    const searchModalOpenBtn = document.getElementById('searchTriggerBtn');
    const searchModalCloseBtn = document.getElementById('searchModalCloseBtn');
    const modal = document.querySelector(searchModalOpenBtn.dataset.modalTarget);

    searchModalOpenBtn.addEventListener('click', (e) => {
      modal.hidden = !modal.hidden;
      const content = modal.querySelector('.menu-search-input');
      content.focus();
    });

    modal.addEventListener('click', function(e) {
      if (e.target.tagName !== 'INPUT') {
        modal.hidden = true;
      }
    })

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.search-modal:not([hidden])').forEach(modal => {
          modal.hidden = true;
        });
      }
    });

    const mobileMenuTrigger = document.getElementById('mobileMenuTrigger');
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.hidden = true;

    mobileMenuTrigger.addEventListener('click', (e) => {
      mobileMenu.hidden = !mobileMenu.hidden;
      if (mobileMenu.hidden === true) {
        mobileMenuTrigger.blur();
      }
    });
  </script>
  <!--END Main menu with search modal-->
  <?php if (is_front_page()) { ?>
    <!-- Front Page Event Series Slider -->
    <script>
      (function($) {
        var slider = tns({
          container: '.fp-slider__inner',
          items: 2.3333,
          gutter: 20,
          lazyload: true,
          edgePadding: 0,
          loop: false,
          mouseDrag: true,
          slideBy: "page",
          swipeAngle: false,
          speed: 400,
          autoplay: false,
          controls: false,
          nav: false,
          navPosition: "bottom",
          responsive: {
            1200: {
              items: 6,
            },
            1000: {
              items: 5
            },
            800: {
              items: 4,
              nav: true
            },
            600: {
              items: 3
            }
          }
        });
      })(jQuery);
    </script>
    <!--END Front Page Event Series Slider -->
  <?php } ?>

  <?php
  if (is_singular('project')) { ?>
    <!--Project more stuff button-->
    <script>
      (function($) {
        var more = 'Show More ';
        var less = 'Show Less ';

        $('div.project-show-counter').css('display', 'none');
        $('.project-show-button').each(function() {
          var section = $(this).siblings('.project-linked-header').text();
          $(this).text(more + section);
        });
        $('.project-show-button').on('click', function() {
          $(this).data('clicked', !$(this).data('clicked'));
          if ($(this).data('clicked')) {
            var section = $(this).siblings('.project-linked-header').text();
            $(this).text(less + section);
            $(this).siblings('.project-show-counter').slideToggle(800);
          } else {
            var section = $(this).siblings('.project-linked-header').text();
            $(this).text(more + section);
            $(this).siblings('.project-show-counter').slideToggle(800);
          }
        });
      })(jQuery);
    </script>
    <!--END Project more stuff button-->
  <?php } ?>

  <?php

  if (has_term('material-for-schools', 'article_type')) {
  ?>
    <!--Material For Schools file list toggle-->
    <script>
      (function($) {
        /**
         * The first <li> in each toggle-list must contain the 'Show' text e.g. 'Show files'
         * List will toggle 'Show *' / 'Hide *' 
         */
        const toggleList = $('.toggle-list');

        function toggleControl(e) {
          let parent = $(this).parent();
          let text = $(this).text();
          parent.find('li:not(:first-child)').slideToggle(800);
          parent.find('li:first-child').text(
            text.indexOf('Show') >= 0 ?
            text.replace('Show', 'Hide') :
            text.replace('Hide', 'Show')
          );
          let target = text.indexOf('Show') >= 0 ?
            parent :
            parent.prevAll('h2').first()

          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000);
        }

        toggleList.each(function() {
          // For each .toggle-list copy the first <li> and change text to 'Hide'
          let lastListItem = '<li>' +
            $(this).find('li:first-child').text().replace('Show', 'Hide') +
            '</li>';
          $(this).append(lastListItem);
          // Initially hide all but first list items
          $(this).find('li:not(:first-child)').css('display', 'none');
          // Event listeners
          $(this).find('li:first-child').on('click', toggleControl);
          $(this).find('li:last-child').on('click', toggleControl);
        });

      })(jQuery);
    </script>
    <!--END Schools course material file list toggle-->
  <?php } ?>

  <?php
  if (is_search()) { ?>
    <!--Search filter button-->
    <script>
      (function($) {

        var $form = $('#search-filter-form');
        var $button = $('#search-filter-form-btn');

        $form.css('display', 'none');

        $button.on('click', function() {
          $(this).data('clicked', !$(this).data('clicked'));

          if ($(this).data('clicked')) {
            $form.slideToggle();
            $(this).text('Hide Search Form');
          } else {
            $form.slideToggle();
            $(this).text('Refine Search');
          }
        });
      })(jQuery);
    </script>
    <!--END Search filter button-->
  <?php } ?>

  <?php
  if (is_singular()) { ?>
    <!--Single page Hide Details button-->
    <script>
      (function($) {
        var $block = $('#single-item-block-details');
        var $button = $('#single-item-details-btn');

        $button.on('click', function() {
          $(this).data('clicked', !$(this).data('clicked'));
          if ($(this).data('clicked')) {
            $(this).siblings().slideToggle();
            $(this).text('Show Details');
          } else {
            $(this).siblings().slideToggle();
            $(this).text('Hide Details');
          }
        });
      })(jQuery);
    </script>
    <!--END Single page Hide Details button-->
  <?php } ?>
<?php
}
