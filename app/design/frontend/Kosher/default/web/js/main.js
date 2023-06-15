define([
    'jquery',
    'matchMedia',
    'domReady!'
], function ($, mediaCheck) {

    'use strict';

    // console.log('Global scripts');

    const breakPoint = '(max-width: 980px)';

    // Close menu

    const cartBtn = document.querySelector('.minicart-wrapper .showcart');
    const search = $('.header-search-container');
    const login = $('.header-login-block');
    const menuBtn = $('#catalog_button');
    const mainMenu = $('#kosher_main_menu');

    // Close account menu

    let closeAccount = () => {
        if (login.hasClass('active')) {
            login.removeClass('active');
            login.find('[data-popup-content]').removeAttr('style');
        }
    }

    let closeMenu = () => {
        if(mainMenu.css('display') === 'block') mainMenu.fadeOut(150, 'swing');
    }

    // Close minicart

    let closeMinicart = () => {
        if (cartBtn.classList.contains('active')) {
            cartBtn.classList.remove('active');
            cartBtn.closest('div').classList.remove('active');
            document.querySelector('.minicart-wrapper .mage-dropdown-dialog').style.display = 'none'
        }
    }

    // Close account menu on minicart call if opened

    cartBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (!cartBtn.classList.contains('active')) {
            cartBtn.classList.add('active');
            cartBtn.closest('div').classList.add('active');
            document.querySelector('.minicart-wrapper .mage-dropdown-dialog').removeAttribute('style');
            closeAccount();
            closeMenu();
            closeMobileSearch();
            return;
        }
        closeMinicart();
    });

    setTimeout(function () {
        document.querySelector('#btn-minicart-close').addEventListener('click', () => {
            closeMinicart();
        });
    }, 2000)


    // Close account on cart click

    document.querySelector('.header-login-block').addEventListener('click', () => {
        closeMinicart();
        closeMenu();
        closeMobileSearch();
    });

    // Close mobile search

    let closeMobileSearch = () => {
        if($('body').hasClass('mobile-view')) search.fadeOut(200);
    }

    //Main menu

    menuBtn.on('click', () => {
        mainMenu.slideToggle(300, 'swing');
        closeMinicart();
        closeAccount();
        closeMobileSearch();
    });


    // Show/hide mobile search

    $('.header-right-container .search-button').on('click', () => {
        search.fadeToggle(200);
        closeMenu();
        closeAccount();
        closeMinicart();
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
