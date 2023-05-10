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
        search.slideToggle(0);
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
            // Mobile mode
        },
        exit: () => {
            // Desktop mode
            desktopCleaup();
        }
    });

});
