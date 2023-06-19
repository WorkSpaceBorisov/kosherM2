define([
    'jquery'
], function ($) {

    'use strict';

    // Close on click out

    $(window).click(() => {
        let menu = $('#kosher_main_menu');
        //Hide if visible
        $('.mobile-view .header-search-container').fadeOut(500);
        if (menu.css('display') === 'block') menu.slideUp(300, 'swing');
    });
    
});
