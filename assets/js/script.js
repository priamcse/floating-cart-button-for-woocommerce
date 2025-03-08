// jQuery(document).ready(function ($) {
//   $(document.body).on("added_to_cart", function () {
//     $.ajax({
//       url: wc_cart_fragments_params.wc_ajax_url
//         .toString()
//         .replace("%%endpoint%%", "get_refreshed_fragments"),
//       type: "POST",
//       success: function (response) {
//         if (response && response.fragments) {
//           $.each(response.fragments, function (key, value) {
//             $(key).replaceWith(value);
//           });
//         }
//       },
//     });
//   });
// });
// jQuery(document).ready(function ($) {
//   console.log("jQuery is ready.");

//   $(document).on("click", ".add_to_cart_button", function () {
//     console.log("Add to cart button clicked.");

//     setTimeout(function () {
//       $(document.body).trigger("added_to_cart");
//     }, 1000);
//   });

//   $(document.body).on("added_to_cart", function () {
//     console.log("Product added to cart - triggering AJAX update.");

//     $.ajax({
//       url: wc_cart_fragments_params.wc_ajax_url
//         .toString()
//         .replace("%%endpoint%%", "get_refreshed_fragments"),
//       type: "POST",
//       success: function (response) {
//         if (response && response.fragments) {
//           $.each(response.fragments, function (key, value) {
//             $(key).replaceWith(value);
//           });
//         }
//       },
//     });
//   });
// });

jQuery(document).ready(function ($) {
  console.log("jQuery is ready.");

  $(document).on("click", ".add_to_cart_button", function () {
    console.log("Add to cart button clicked.");

    setTimeout(function () {
      $(document.body).trigger("added_to_cart");
    }, 1000);
  });

  $(document.body).on("added_to_cart", function () {
    console.log("Product added to cart - triggering AJAX update.");

    $.ajax({
      url: wc_cart_fragments_params.wc_ajax_url
        .toString()
        .replace("%%endpoint%%", "get_refreshed_fragments"),
      type: "POST",
      success: function (response) {
        if (response && response.fragments) {
          $.each(response.fragments, function (key, value) {
            $(key).replaceWith(value);
          });
        }
        console.log("Cart updated via AJAX.");

        // **Reinitialize WooCommerce scripts after AJAX update**
        $(document.body).trigger("wc_fragments_refreshed");
      },
    });
  });

  // **Rebind quantity selector events**
  $(document).on(
    "click",
    ".wc-block-components-quantity-selector__button",
    function (e) {
      e.preventDefault();

      let $input = $(this).siblings(
        ".wc-block-components-quantity-selector__input"
      );
      let currentValue = parseInt($input.val(), 10);
      let min = parseInt($input.attr("min"), 10) || 1;
      let max = parseInt($input.attr("max"), 10) || 9999;

      if (
        $(this).hasClass("wc-block-components-quantity-selector__button--plus")
      ) {
        if (currentValue < max) {
          $input.val(currentValue + 1).trigger("change");
        }
      } else if (
        $(this).hasClass("wc-block-components-quantity-selector__button--minus")
      ) {
        if (currentValue > min) {
          $input.val(currentValue - 1).trigger("change");
        }
      }
    }
  );
});
