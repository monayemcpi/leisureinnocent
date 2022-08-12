'use strict';

class MiniCart {
  miniCartAll() {
    var $win = $(window);
    var $box = $('.tbay-dropdown-cart .dropdown-content,.tbay-bottom-cart .content,.topbar-mobile .btn,#tbay-mobile-menu, .active-mobile button,#tbay-offcanvas-main,.topbar-mobile .btn-toggle-canvas,#tbay-offcanvas-main .btn-toggle-canvas');
    $win.on("click.Bst,click touchstart tap", function (event) {
      if ($box.has(event.target).length == 0 && !$box.is(event.target)) {
        $('#wrapper-container').removeClass('active active-cart');
        $('#wrapper-container').removeClass('offcanvas-right');
        $('#wrapper-container').removeClass('offcanvas-left');
        $('.tbay-dropdown-cart').removeClass('active');
        $('#tbay-offcanvas-main,.tbay-offcanvas').removeClass('active');
        $("#tbay-dropdown-cart").hide(500);
        $('.tbay-bottom-cart').removeClass('active');
      }
    });
    $(".tbay-dropdown-cart.v2 .offcanvas-close").on('click', function () {
      $('#wrapper-container').removeClass('active');
      $('#wrapper-container').removeClass('offcanvas-right');
      $('#wrapper-container').removeClass('offcanvas-left');
      $('.tbay-dropdown-cart').removeClass('active');
    });
  }

}

class AjaxCart {
  constructor() {
    if (typeof greenmart_settings === "undefined") return;
    MiniCart.prototype.miniCartAll();

    this._intAjaxCart();

    this._initEventRemoveProduct();
  }

  _intAjaxCart() {
    if (!jQuery('body').hasClass('tbay-disable-ajax-popup-cart')) {
      var product_info = null;
      jQuery('body').on('adding_to_cart', function (button, data, data2) {
        product_info = data2;
      });
      jQuery('body').on('added_to_cart', function (fragments, cart_hash) {
        if (typeof product_info['page'] === "undefined") {
          jQuery('#tbay-cart-modal').modal();
          var url = greenmart_settings.ajaxurl + '?action=greenmart_add_to_cart_product&product_id=' + product_info.product_id;
          jQuery.get(url, function (data, status) {
            jQuery('#tbay-cart-modal .modal-body .modal-body-content').html(data);
          });
          jQuery('#tbay-cart-modal').on('hidden.bs.modal', function () {
            jQuery(this).find('.modal-body .modal-body-content').empty();
          });
        }
      });
    }
  }

  _initAjaxSingleCart() {
    if (!greenmart_settings.enable_ajax_add_to_cart || !greenmart_settings.ajax_single_add_to_cart) return;

    $(document).on('click', '.single_add_to_cart_button', function (e) {
      if ($(this).closest('form.cart').find('input[name="greenmart_buy_now"]').length > 0 && $(this).closest('form.cart').find('input[name="greenmart_buy_now"]').val() === "1") return;
      let $button = $(this),
          $form = $button.closest('form.cart');

      if ($form.hasClass('grouped_form') || $form.find('input[name=quantity]').length == 0 || $button.parents('#yith-quick-view-content').length > 0) {
        return;
      }

      var id = $button.val(),
          product_qty = $form.find('input[name=quantity]').val() || 1,
          product_id = $form.find('input[name=product_id]').val() || id,
          variation_form = $(this).closest('.variations_form'),
          var_id = 0,
          item = {};
      if (!product_id) return;
      if ($button.is('.disabled')) return;

      if (variation_form.length > 0) {
        var_id = variation_form.find('input[name=variation_id]').val();
        product_id = variation_form.find('input[name=product_id]').val();
        var product_id = variation_form.find('input[name=product_id]').val(),
            quantity = variation_form.find('input[name=quantity]').val(),
            check = true;
        let variations = variation_form.find('select[name^=attribute]');

        if (!variations.length) {
          variations = variation_form.find('[name^=attribute]:checked');
        }

        if (!variations.length) {
          variations = variation_form.find('input[name^=attribute]');
        }

        variations.each(function () {
          var $this = $(this),
              attributeName = $this.attr('name'),
              attributevalue = $this.val(),
              index,
              attributeTaxName;
          $this.removeClass('error');

          if (attributevalue.length === 0) {
            index = attributeName.lastIndexOf('_');
            attributeTaxName = attributeName.substring(index + 1);
            $this.addClass('required error').before('<div class="ajaxerrors"><p>Please select ' + attributeTaxName + '</p></div>');
            check = false;
          } else {
            item[attributeName] = attributevalue;
          }
        });

        if (!check) {
          return false;
        }
      }

      e.preventDefault();
      var data = {
        action: 'woocommerce_ajax_add_to_cart',
        product_id: product_id,
        product_sku: '',
        quantity: product_qty,
        variation_id: var_id,
        variation: item
      };
      $(document.body).trigger('adding_to_cart', [$button, data]);
      $.ajax({
        type: 'post',
        url: wc_add_to_cart_params.ajax_url,
        data: data,
        beforeSend: function (response) {
          $button.removeClass('added').addClass('loading');
        },
        complete: function (response) {
          $button.addClass('added').removeClass('loading');
        },
        success: function (response) {
          $.each(response.fragments, function (key, value) {
            $(key).replaceWith(value);
          });

          if (response.error & response.product_url) {
            window.location = response.product_url;
            return;
          } else {
            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
            $('.woocommerce-notices-wrapper').empty().append(response.notices);
          }

          $('.tbay-dropdown-cart').addClass('active');
        }
      });
      return false;
    });
  }

  _initEventRemoveProduct() {
    if (!greenmart_settings.enable_ajax_add_to_cart) return;
    $(document).on('click', '.mini_cart_content a.remove', event => {
      this._onclickRemoveProduct(event);
    });
  }

  _onclickRemoveProduct(event) {
    event.preventDefault();
    var product_id = $(event.currentTarget).attr("data-product_id"),
        cart_item_key = $(event.currentTarget).attr("data-cart_item_key"),
        product_container = jQuery(event.currentTarget).parents('.mini_cart_item'),
        thisItem = $(event.currentTarget).closest('.widget_shopping_cart_content');
    product_container.block({
      message: null,
      overlayCSS: {
        cursor: 'none'
      }
    });

    this._callRemoveProductAjax(product_id, cart_item_key, thisItem, event);
  }

  _callRemoveProductAjax(product_id, cart_item_key, thisItem, event) {
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: wc_add_to_cart_params.ajax_url,
      data: {
        action: "product_remove",
        product_id: product_id,
        cart_item_key: cart_item_key
      },
      beforeSend: function () {
        thisItem.find('.mini_cart_content').append('<div class="ajax-loader-wapper"><div class="ajax-loader"></div></div>').fadeTo("slow", 0.3);
        event.stopPropagation();
      },
      success: response => {
        this._onRemoveSuccess(response, product_id);
      }
    });
  }

  _onRemoveSuccess(response, product_id) {
    if (!response || response.error) return;
    var fragments = response.fragments;

    if (fragments) {
      $.each(fragments, function (key, value) {
        $(key).replaceWith(value);
      });
    }

    $('.add_to_cart_button.added[data-product_id="' + product_id + '"]').removeClass("added").next('.wc-forward').remove();
  }

}

class WishList {
  constructor() {
    this._onChangeWishListItem();
  }

  _onChangeWishListItem() {
    jQuery(document).on('added_to_wishlist removed_from_wishlist', () => {
      var counter = jQuery('.count_wishlist');
      $.ajax({
        url: yith_wcwl_l10n.ajax_url,
        data: {
          action: 'yith_wcwl_update_wishlist_count'
        },
        dataType: 'json',
        success: function (data) {
          counter.html(data.count);
        },
        beforeSend: function () {
          counter.block();
        },
        complete: function () {
          counter.unblock();
        }
      });
    });
  }

}

class ProductItem {
  initOnChangeQuantity(callback) {
    var _this = this;

    $(document).off('click', '.plus, .minus').on('click', '.plus, .minus', function () {
      var $qty = $(this).closest('.quantity').find('.qty'),
          currentVal = parseFloat($qty.val()),
          max = $qty.attr('max'),
          min = $qty.attr('min'),
          step = $qty.attr('step'),
          number_digits = _this.numberAfterDecimal(step);

      if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
      if (max === '' || max === 'NaN') max = '';
      if (min === '' || min === 'NaN') min = 0;
      if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

      if ($(this).is('.plus')) {
        if (max && (max == currentVal || currentVal > max)) {
          $qty.val(max);
        } else {
          $qty.val((currentVal + parseFloat(step)).toFixed(number_digits));
        }
      } else {
        if (min && (min == currentVal || currentVal < min)) {
          $qty.val(min).trigger('change');
        } else if (currentVal > 0) {
          $qty.val((currentVal - parseFloat(step)).toFixed(number_digits));
        }
      }

      if (callback && typeof callback == "function") {
        $(this).parent().find('input').trigger("change");
        callback();
      }
    });
  }

  numberAfterDecimal(value) {
    let output = 0;

    if (value.toString().split(".").length > 1) {
      output = value.toString().split(".")[1].length;
    } else {
      return output;
    }

    if (output < 0) return output;
    return output;
  }

  _initQuantityMode() {
    if (typeof greenmart_settings === "undefined" || !greenmart_settings.quantity_mode) return;
    $(".woocommerce .products").on("click", ".quantity .qty", function () {
      return false;
    });
    $(document).on('change', ".quantity .qty", function () {
      var add_to_cart_button = $(this).parents(".product-block").find(".add_to_cart_button");
      add_to_cart_button.attr("data-quantity", $(this).val());
    });
    $(document).on("keypress", ".quantity .qty", function (e) {
      if ((e.which || e.keyCode) === 13) {
        $(this).parents(".product-block").find(".add_to_cart_button").trigger("click");
      }
    });
  }

}

class Cart {
  constructor() {
    var _this = this;

    if (typeof greenmart_settings === "undefined") return;

    _this._initEventChangeQuantity();

    jQuery(document.body).on('updated_wc_div', () => {
      _this._initEventChangeQuantity();

      jQuery(document.body).trigger('greenmart_load_more');

      if (typeof wc_add_to_cart_variation_params !== 'undefined') {
        jQuery('.variations_form').each(function () {
          jQuery(this).wc_variation_form();
        });
      }
    });
    jQuery(document.body).on('cart_page_refreshed', () => {
      _this._initEventChangeQuantity();
    });
    jQuery(document.body).on('tbay_display_mode', () => {
      _this._initEventChangeQuantity();
    });
  }

  _initEventChangeQuantity() {
    if ($("body.woocommerce-cart [name='update_cart']").length === 0) {
      new ProductItem().initOnChangeQuantity(() => {});
    } else {
      new ProductItem().initOnChangeQuantity(() => {
        $('.woocommerce-cart-form :input[name="update_cart"]').prop('disabled', false);

        if (typeof greenmart_settings !== "undefined" && greenmart_settings.ajax_update_quantity) {
          $("[name='update_cart']").trigger('click');
        }
      });
    }
  }

}

class SideBar {
  constructor() {
    this._layoutShopCanvasSidebar();

    this._layoutShopFullWidth();

    this._layoutSidebarMobile();
  }

  _layoutShopCanvasSidebar() {
    $(".button-canvas-sidebar, .product-canvas-sidebar .product-canvas-close").on("click", function (e) {
      $('.product-canvas-sidebar').toggleClass('active');
      $("body").toggleClass('product-canvas-active');
    });
    var win_canvas = $(window);
    var box_canvas = $('.product-canvas-sidebar .content,.button-canvas-sidebar');
    win_canvas.on("click.Bst", event => {
      event.target;

      if (box_canvas.has(event.target).length == 0 && !box_canvas.is(event.target)) {
        $('.product-canvas-sidebar').removeClass('active');
        $("body").removeClass('product-canvas-active');
      }
    });
  }

  _layoutSidebarMobile() {
    $(document).on('click', '.tbay-sidebar-mobile-btn', function () {
      $('body').toggleClass('show-sidebar');
    });
    $(document).on('click', '.close-side-widget, .tbay-close-side', function () {
      $('body').removeClass('show-sidebar');
    });
  }

  _layoutShopFullWidth() {
    $(".button-product-top").on("click", function (e) {
      $('.product-top-sidebar').toggleClass('active');
      $('.product-top-sidebar > .container .content').slideToggle(500, function () {});
    });
  }

}

class ModalVideo {
  constructor($el, options = {
    classBtn: '.tbay-modalButton',
    defaultW: 640,
    defaultH: 360
  }) {
    this.$el = $el;
    this.options = options;

    this._initVideoIframe();
  }

  _initVideoIframe() {
    $(`${this.options.classBtn}[data-target='${this.$el}']`).on('click', this._onClickModalBtn);
    $(this.$el).on('hidden.bs.modal', () => {
      $(this.$el).find('iframe').html("").attr("src", "");
    });
  }

  _onClickModalBtn(event) {
    let html = $(event.currentTarget).data('target');
    var allowFullscreen = $(event.currentTarget).attr('data-tbayVideoFullscreen') || false;
    var dataVideo = {
      'src': $(event.currentTarget).attr('data-tbaySrc'),
      'height': $(event.currentTarget).attr('data-tbayHeight') || this.options.defaultH,
      'width': $(event.currentTarget).attr('data-tbayWidth') || this.options.defaultW
    };
    if (allowFullscreen) dataVideo.allowfullscreen = "";
    $(html).find("iframe").attr(dataVideo);
  }

}

class WooCommon {
  constructor() {
    this._greenmartFixRemove();

    this._greenmartVideoModal();
  }

  _greenmartFixRemove() {
    $('.tbay-gallery-varible .woocommerce-product-gallery__trigger').remove();
  }

  _greenmartVideoModal() {
    $('.tbay-video-modal').each((index, element) => {
      new ModalVideo(`#video-modal-${$(element).attr("data-id")}`);
    });
  }

}

class singleProduct {
  constructor() {
    var _this = this;

    _this._initOnClickReview();

    _this._initBuyNow();

    _this._initChangeImageVarible();

    _this._initOpenAttributeMobile();

    _this._initCloseAttributeMobile();

    _this._initCloseAttributeMobileWrapper();

    _this._initAddToCartClickMobile();

    _this._initBuyNowwClickMobile();
  }

  _initOnClickReview() {
    $('body').on('click', 'a.woocommerce-review-link', function () {
      if (!$('#reviews').closest('.panel').find('.tabs-title a').hasClass('collapsed')) return;
      $('#reviews').closest('.panel').find('.tabs-title a.collapsed').on('click');
    });
  }

  _initBuyNow() {
    $('body').on('click', '.tbay-buy-now', function (e) {
      e.preventDefault();
      let productform = $(this).closest('form.cart'),
          submit_btn = productform.find('[type="submit"]'),
          buy_now = productform.find('input[name="greenmart_buy_now"]'),
          is_disabled = submit_btn.is('.disabled');

      if (is_disabled) {
        submit_btn.trigger('click');
      } else {
        buy_now.val('1');
        productform.find('.single_add_to_cart_button').click();
      }
    });
    $(document.body).on('check_variations', function () {
      let btn_submit = $('form.variations_form').find('.single_add_to_cart_button');
      btn_submit.each(function (index) {
        let is_submit_disabled = $(this).is('.disabled');

        if (is_submit_disabled) {
          $(this).parent().find('.tbay-buy-now').addClass('disabled');
        } else {
          $(this).parent().find('.tbay-buy-now').removeClass('disabled');
        }
      });
    });
  }

  _initChangeImageVarible() {
    let form = $("form.variations_form");
    if (form.length === 0) return;
    $("form.variations_form").on('change', function () {
      var _this = $(this);

      var attribute_label = [];
      let src_image = $(".flex-control-thumbs").find('.flex-active').attr('src');
      $('.mobile-infor-wrapper img').attr('src', src_image);

      if (!_this.find('.single_variation_wrap .single_variation .woocommerce-variation-price').is(':empty')) {
        if ($('.woocommerce-variation-add-to-cart').hasClass('woocommerce-variation-add-to-cart-disabled')) {
          _this.find('.mobile-infor-wrapper .infor-body .price').empty().append(_this.parent().find('.price').html());

          _this.find('.mobile-infor-wrapper .infor-body > .stock').show();
        } else {
          if (!_this.find('.single_variation_wrap .single_variation').is(':empty')) {
            _this.find('.mobile-infor-wrapper .infor-body .price').empty().append(_this.find('.single_variation_wrap .single_variation').html());

            if (!_this.find('.single_variation_wrap .single_variation .woocommerce-variation-availability').is(':empty')) {
              _this.find('.mobile-infor-wrapper .infor-body > .stock').hide();
            }
          } else {
            _this.find('.mobile-infor-wrapper .infor-body .price').empty().append(_this.parent().find('.price').html());
          }
        }
      }

      _this.find('.variations tr').each(function () {
        if (typeof $(this).find('select').val() !== "undefined") {
          attribute_label.push($(this).find('select option:selected').text());
        }
      });

      _this.parent().find('.mobile-attribute-list .value').empty().append(attribute_label.join('/ '));
    });
  }

  _initOpenAttributeMobile() {
    let attribute = $("#attribute-open");
    if (attribute.length === 0) return;
    attribute.on('click', function () {
      $(this).parent().parent().find('form.cart').addClass('open open-btn-all');
      $(this).parents('#tbay-main-content').addClass('open-main-content');
    });
  }

  _initAddToCartClickMobile() {
    let addtocart = $("#tbay-click-addtocart");
    if (addtocart.length === 0) return;
    addtocart.on('click', function () {
      $(this).parent().parent().find('form.cart').addClass('open open-btn-addtocart');
      $(this).parents('#tbay-main-content').addClass('open-main-content');
    });
  }

  _initBuyNowwClickMobile() {
    let buy_now = $("#tbay-click-buy-now");
    if (buy_now.length === 0) return;
    buy_now.on('click', function () {
      $(this).parent().parent().find('form.cart').addClass('open open-btn-buynow');
      $(this).parents('#tbay-main-content').addClass('open-main-content');
    });
  }

  _initCloseAttributeMobile() {
    let close = $("#mobile-close-infor");
    if (close.length === 0) return;
    close.on('click', function () {
      $(this).parents('form.cart').removeClass('open');

      if ($(this).parents('form.cart').hasClass('open-btn-all')) {
        $(this).parents('form.cart').removeClass('open-btn-all');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }

      if ($(this).parents('form.cart').hasClass('open-btn-buynow')) {
        $(this).parents('form.cart').removeClass('open-btn-buynow');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }

      if ($(this).parents('form.cart').hasClass('open-btn-addtocart')) {
        $(this).parents('form.cart').removeClass('open-btn-addtocart');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }
    });
  }

  _initCloseAttributeMobileWrapper() {
    let close = $("#mobile-close-infor-wrapper");
    if (close.length === 0) return;
    close.on('click', function () {
      $(this).parent().find('form.cart').removeClass('open');

      if ($(this).parent().find('form.cart').hasClass('open-btn-all')) {
        $(this).parent().find('form.cart').removeClass('open-btn-all');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }

      if ($(this).parent().find('form.cart').hasClass('open-btn-buynow')) {
        $(this).parent().find('form.cart').removeClass('open-btn-buynow');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }

      if ($(this).parent().find('form.cart').hasClass('open-btn-addtocart')) {
        $(this).parent().find('form.cart').removeClass('open-btn-addtocart');
        $(this).parents('#tbay-main-content').removeClass('open-main-content');
      }
    });
  }

}

class DisplayMode {
  constructor() {
    if (typeof greenmart_settings === "undefined") return;

    this._initModeListShopPage();

    this._initModeGridShopPage();

    $(document.body).on('displayMode', () => {
      this._initModeListShopPage();

      this._initModeGridShopPage();
    });
  }

  _initModeListShopPage() {

    $('#display-mode-list').each(function (index) {
      $(this).on('click', function () {
        event.preventDefault();
        $(event.currentTarget).addClass('active').prev().removeClass('active');
        Cookies.set('display_mode', 'list', {
          expires: 0.1,
          path: '/'
        });

        if (!$(event.currentTarget).parents('.tbay-filter').parent().find('div.products').hasClass('products-list')) {
          $(event.currentTarget).parents('.tbay-filter').parent().find('div.products').fadeOut(0, function () {
            $(this).addClass('products-list').removeClass('products-grid').fadeIn(300);
          });
          $(event.currentTarget).parents('.tbay-filter').parent().find('div.products').find('.product-block').removeClass('grid').fadeIn(300).addClass('list');
        }

        return false;
      });
    });
  }

  _initModeGridShopPage() {

    $('#display-mode-grid').each(function (index) {
      $(this).on('click', function () {
        event.preventDefault();
        $(event.currentTarget).addClass('active').next().removeClass('active');
        Cookies.set('display_mode', 'grid', {
          expires: 0.1,
          path: '/'
        });

        if (!$(event.currentTarget).parents('.tbay-filter').parent().find('div.products').hasClass('products-grid')) {
          $(event.currentTarget).parents('.tbay-filter').parent().find('div.products').fadeOut(0, function () {
            $(this).addClass('products-grid').removeClass('products-list').fadeIn(300);
          });
          $(event.currentTarget).parents('.tbay-filter').parent().find('div.products').find('.product-block').removeClass('list').fadeIn(300).addClass('grid');
        }

        return false;
      });
    });
  }

  _getDisplayMode() {
    if (greenmart_settings.display_mode == 'list') {
      Cookies.set('display_mode', 'list', {
        expires: 0.1,
        path: '/'
      });
    } else if (greenmart_settings.display_mode == 'grid') {
      Cookies.set('display_mode', 'grid', {
        expires: 0.1,
        path: '/'
      });
    }

    if (Cookies.get('display_mode') != undefined && Cookies.get('display_mode') !== "") {
      if (Cookies.get('display_mode') == 'grid') {
        let mode = $('.display-mode').find("button.grid");
        mode.parent().children().removeClass('active');
        mode.addClass('active');
        $('.tbay-filter').parent().find('.products').addClass('products-' + Cookies.get('display_mode'));
      }

      if (Cookies.get('display_mode') == 'list') {
        let mode = $('.display-mode').find("button.list");
        mode.parent().children().removeClass('active');
        mode.addClass('active');
        $('.tbay-filter').parent().find('.products').addClass('products-' + Cookies.get('display_mode'));
      }
    }
  }

}

class ProductTabs {
  constructor() {
    if (typeof greenmart_settings === "undefined") return;

    this._initProductTabs();
  }

  _initProductTabs() {
    var process = false;
    $('.tbay-product-tabs-ajax.ajax-active').each(function () {
      var $this = $(this);
      $this.find('.product-tabs-title li a').off('click').on('click', function (e) {
        e.preventDefault();
        var $this = $(this),
            atts = $this.parent().parent().data('atts'),
            value = $this.data('value'),
            id = $this.attr('href');

        if (process || $(id).hasClass('active-content')) {
          return;
        }

        process = true;
        $.ajax({
          url: greenmart_settings.ajaxurl,
          data: {
            atts: atts,
            value: value,
            action: 'greenmart_get_products_tab_shortcode'
          },
          dataType: 'json',
          method: 'POST',
          beforeSend: function (xhr) {
            $(id).parent().addClass('load-ajax');
          },
          success: function (data) {
            $(id).html(data.html);
            $(id).parent().find('.current').removeClass('current');
            $(id).parent().removeClass('load-ajax');
            $(id).addClass('active-content');
            $(id).addClass('current');
            $(document.body).trigger('tbay_carousel_slick');
            $(document.body).trigger('tbay_ajax_tabs_products');
          },
          error: function () {
            console.log('ajax error');
          },
          complete: function () {
            process = false;
          }
        });
      });
    });
  }

}

class ProductCategoriesTabs {
  constructor() {
    if (typeof greenmart_settings === "undefined") return;

    this._initProductCategoriesTabs();
  }

  _initProductCategoriesTabs() {
    var process = false;
    $('.tbay-product-categories-tabs-ajax.ajax-active').each(function () {
      var $this = $(this);
      $this.find('.product-categories-tabs-title li a').off('click').on('click', function (e) {
        e.preventDefault();
        var $this = $(this),
            atts = $this.parent().parent().data('atts'),
            value = $this.data('value'),
            id = $this.attr('href');

        if (process || $(id).hasClass('active-content')) {
          return;
        }

        process = true;
        $.ajax({
          url: greenmart_settings.ajaxurl,
          data: {
            atts: atts,
            value: value,
            action: 'greenmart_get_products_categories_tab_shortcode'
          },
          dataType: 'json',
          method: 'POST',
          beforeSend: function (xhr) {
            $(id).parent().addClass('load-ajax');
          },
          success: function (data) {
            if ($(id).find('.tab-banner').length > 0) {
              $(id).append(data.html);
            } else {
              $(id).prepend(data.html);
            }

            $(id).parent().find('.current').removeClass('current');
            $(id).parent().removeClass('load-ajax');
            $(id).addClass('active-content');
            $(id).addClass('current');
            $(document.body).trigger('tbay_carousel_slick');
            $(document.body).trigger('tbay_ajax_tabs_products');
          },
          error: function () {
            console.log('ajax error');
          },
          complete: function () {
            process = false;
          }
        });
      });
    });
  }

}

jQuery(document).ready(() => {
  jQuery(document.body).trigger('tawcvs_initialized');
  var product_item = new ProductItem();
  product_item.initOnChangeQuantity();

  product_item._initQuantityMode();

  new AjaxCart(), new singleProduct(), new SideBar(), new WishList(), new Cart(), new WooCommon(), new ModalVideo("#productvideo"), new DisplayMode(), new ProductTabs(), new ProductCategoriesTabs();
});

var AjaxProductTabs = function ($scope, $) {
  new ProductTabs(), new ProductCategoriesTabs();
};

jQuery(window).on('elementor/frontend/init', function () {
  if (typeof greenmart_settings !== "undefined" && elementorFrontend.isEditMode() && Array.isArray(greenmart_settings.elements_ready.ajax_tabs)) {
    jQuery.each(greenmart_settings.elements_ready.ajax_tabs, function (index, value) {
      elementorFrontend.hooks.addAction('frontend/element_ready/tbay-' + value + '.default', AjaxProductTabs);
    });
  }
});
