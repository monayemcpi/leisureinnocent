// source --> https://innocent.aprosoftbd.com/wp-includes/js/zxcvbn-async.min.js?ver=1.0 
/*! This file is auto-generated */
!function(){function t(){var t,e=document.createElement("script");return e.src=_zxcvbnSettings.src,e.type="text/javascript",e.async=!0,(t=document.getElementsByTagName("script")[0]).parentNode.insertBefore(e,t)}null!=window.attachEvent?window.attachEvent("onload",t):window.addEventListener("load",t,!1)}.call(this);
// source --> https://innocent.aprosoftbd.com/wp-content/plugins/translatepress-multilingual/assets/js/trp-frontend-compatibility.js?ver=2.1.4 
document.addEventListener("DOMContentLoaded", function(event) {
    function trpClearWooCartFragments(){

        // clear WooCommerce cart fragments when switching language
        var trp_language_switcher_urls = document.querySelectorAll(".trp-language-switcher-container a:not(.trp-ls-disabled-language)");

        for (i = 0; i < trp_language_switcher_urls.length; i++) {
            trp_language_switcher_urls[i].addEventListener("click", function(){
                if ( typeof wc_cart_fragments_params !== 'undefined' && typeof wc_cart_fragments_params.fragment_name !== 'undefined' ) {
                    window.sessionStorage.removeItem(wc_cart_fragments_params.fragment_name);
                }
            });
        }
    }

    trpClearWooCartFragments();
});