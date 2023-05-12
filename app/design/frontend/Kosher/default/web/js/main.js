define([
    'jquery',
    'matchMedia',
    'domReady!'
], function ($, mediaCheck) {

    'use strict';

    // console.log('Global scripts');

    const breakPoint = '(max-width: 980px)';

    // Close account menu on minicart call if opened

    const cartBtn = document.querySelector('.minicart-wrapper .showcart');
    cartBtn.addEventListener('click', () => {
        const block = document.querySelector('.header-login-block');
        if (block.classList.contains('active')) {
            block.classList.remove('active');
            block.querySelector('[data-popup-content]').removeAttribute('style');
        }
    });

    // Show/hide mobile search

    const search = $('.header-search-container');
    const login = $('.header-login-block');

    $('.header-right-container .search-button').on('click', () => {
        search.fadeToggle(200);
        if (login.hasClass('active') && search.css('display') === 'block') {
            login.removeClass('active');
            $('.ko-customer-menu').removeAttr('style');
        }
    });

    let desktopCleaup = () => {
        search.removeAttr('style');
    }

    // Mediacheck

    mediaCheck({
        media: breakPoint,
        entry: () => {
            // Mobile mode\
            $('body').addClass('mobile-view');
        },
        exit: () => {
            // Desktop mode
            desktopCleaup();
            $('body').removeClass('mobile-view');
        }
    });

    // Close on click out

    $(window).click(() => {
        let menu = $('#kosher_main_menu');
        //Hide if visible
        $('.mobile-view .header-search-container').fadeOut(500);
        if (menu.css('display') === 'block') menu.slideUp(300, 'swing');
    });


    // No close

    $('.page-header, #kosher_main_menu').on('click', (e) => {
        e.stopPropagation()
    });

    // Filters on/off

    document.querySelector('#hide_filters').addEventListener('click', () => {
        document.body.classList.remove('filters-on');
    });
    document.querySelector('#show_filters').addEventListener('click', () => {
        document.body.classList.add('filters-on');
    });



});
