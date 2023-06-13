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

    // Close account on cart click

    document.querySelector('.header-login-block').addEventListener('click', () => {
        document.querySelector('.minicart-wrapper').classList.remove('active');
        document.querySelector('.action.showcart').classList.remove('active');
        document.querySelector('.minicart-wrapper [role="dialog"]').style.display = 'none';

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
            // Mobile mode
            $('body').addClass('mobile-view');
        },
        exit: () => {
            // Desktop mode
            desktopCleaup();
            $('body').removeClass('mobile-view');
        }
    });

    // No close

    $('.page-header, #kosher_main_menu').on('click', (e) => {
        e.stopPropagation()
    });

    // Add to cart category button slide

    if ($('body').hasClass('page-products') || $('body').hasClass('cms-index-index')) {
        $('.add-to-calc__button').on('click', (e) => $(e.target).closest('.calc-cell-container').addClass('show-calc'));
    }

});
