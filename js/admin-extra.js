/**
 * Post Bulk Edit Script
 *
 * @see { @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/ }
 */

jQuery(document).ready(function ($) {
  if (typeof inlineEditPost === "undefined") {
    return;
  }

  //Prepopulating our quick-edit post info
  var $inline_editor = inlineEditPost.edit;
  inlineEditPost.edit = function (id) {
    //call old copy
    $inline_editor.apply(this, arguments);

    //our custom functionality below
    var post_id = 0;
    if (typeof id == "object") {
      post_id = parseInt(this.getId(id));
    }

    //if we have our post
    if (post_id != 0) {
      //find quick edit row for the post
      var $post_row = $("#post-" + post_id);
      var $edit_row = $("#edit-" + post_id);

      // Projects
      var $project = $(".column-project", $post_row).children();
      // data-project value is the same for column values and quick edit
      $project.each(function () {
        var $id_att = $(this).attr("data-project");
        $("#" + $id_att).attr("checked", true);
      });

      // Publication Range
      var $publication_type = $(".column-taxonomy-pub_range", $post_row).text();

      $("#pub_range_drop option", $edit_row).each(function () {
        if ($(this).text() == $publication_type) {
          $(this).prop("selected", true);
        }
      });

      // Slider Checkbox
      if ($(".in_slider", $post_row).text() === "Slider") {
        $("input.in-slider", $edit_row).prop("checked", true);
      }
    }
  };
});

/**
 * Disable editing of input fields
 */
jQuery(document).ready(function ($) {
  // Front page Featured Item Position
  // Publication number on Pamphlet Price options page
  // Publication name on Publications Collections options page
  // Publication number on Publications Collections options page
  $(".acf-row:not(.acf-clone) input", ".acf-field-65db8d67d7819")
    .add(
      "[data-name='range_number'] .acf-input input",
      ".toplevel_page_publications-prices"
    )
    .add(
      ".acf-field-65e2496e3110b input",
      ".pamphlets_page_publication-collections"
    )
    .add(
      ".acf-field-65e1c0f134d6a input",
      ".pamphlets_page_publication-collections"
    )
    .each(function () {
      $(this).prop("readonly", true);
    });

  /*   $(
    "[data-taxonomy='pub_range'] select",
    ".toplevel_page_publications-prices"
  ).each(function () {
    $(this).prop("disabled", true);
  });

  $(
    "[data-name='publication'] select",
    ".toplevel_page_publications-prices"
  ).each(function () {
    $(this).prop("disabled", true);
  }); */
});

/**
 * Close specified meta boxes
 */

jQuery(document).ready(function ($) {
  $("#normal-sortables .postbox", ".brhg-collapse-meta").addClass("closed");
});

/**
 * Add extra update buttons the ACF options page for publications prices
 *
 */
document.addEventListener('DOMContentLoaded', function() {
    // Only do this on the one options page
    var page = document.querySelector('.toplevel_page_publications-prices');

    if (page) {
        // Create a new save button
        var updateButton = document.createElement('button');
        updateButton.type = 'submit';
        updateButton.className = 'button button-primary';
        updateButton.textContent = 'Update';
        
        var buttonDiv = document.createElement('div');
        buttonDiv.className = 'extra-update-button';
        buttonDiv.appendChild(updateButton);
        

        // Insert the save button at the desired location
        var targetLocation = page.querySelectorAll('.acf-repeater .-table'); // Adjust the selector as needed

        if (targetLocation) {
            targetLocation.forEach((el) => el.parentNode.appendChild(buttonDiv.cloneNode(true)));
        }
    }
});
