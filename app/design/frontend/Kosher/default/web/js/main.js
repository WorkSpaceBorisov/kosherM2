define([
    'jquery',
    'matchMedia',
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    const breakPoint = '(max-width: 980px)';
    const cartBtn = $('.minicart-wrapper .showcart');
    const search = $('.js_header_search_container');
    const login = $('[data-trigger="customer-trigger"]');
    const menuBtn = $('[data-action="toggle-nav"]');
    const $body = $('body');

    // =========== minicart =============== 

    // Close minicart
    let closeMinicart = () => {
        if (cartBtn.hasClass('active')) {
            cartBtn.removeClass('active');
            cartBtn.closest('div').removeClass('active');
            $('.minicart-wrapper .mage-dropdown-dialog').css('display', 'none');
        }
    }

    // Cart fade close
    let cartFade = () => {
        const cart = $('.block.block-minicart');
        const delay = 0;

        cart.fadeOut(500)

        setTimeout(() => {
            cartBtn.removeClass('active');
            cartBtn.closest('div').removeClass('active');
            $('.minicart-wrapper .mage-dropdown-dialog').css('display', 'none');
        }, delay + 1000);

        setTimeout(() => {
            cart.css('display', 'block');
        }, delay + 1800);
    }

    // Detect cart close on scroll
    $(window).scroll(function() {
        if(cartBtn.hasClass('active')) {
            let scroll = $(window).scrollTop();
            if (scroll > 150) cartFade();
        }
    });

    $('#kosher_overlay').on('click', () => {
        closeMinicart();
    });

    // Close nav/search/account on minicart call if opened
    cartBtn.on('click', function(e) {
        e.preventDefault();
        if (!cartBtn.hasClass('active')) {
            cartBtn.addClass('active');
            cartBtn.closest('div').addClass('active');
            $('.minicart-wrapper .mage-dropdown-dialog').removeAttr('style');
            closeAccount();
            closeMenu();
            closeMobileSearch();
            return;
        }
        closeMinicart();
    });

    // =========== search =============== 

    $(window).click(() => {
        $('.mobile-view .js_header_search_container').fadeOut(500);
    });

    // Close mobile search
    let closeMobileSearch = () => {
        if ($body.hasClass('mobile-view')) search.fadeOut(200);
    }

    // Show/hide mobile search
    $('.js_open_search').on('click', () => {
        search.fadeToggle(200);
        closeMenu();
        closeAccount();
        closeMinicart();
    });

    let desktopCleaup = () => {
        search.removeAttr('style');
    }

    // =========== navigation =============== 

    // Close navigation
    let closeMenu = () => {
        const $html = $('html');
        const $nav = $('.page-header [data-action="navigation"]');
        
        if($html.hasClass('nav-opened')) {
            $html.removeClass('nav-opened');
            $nav.slideToggle(300, 'swing');
            $nav.find('.expanded').removeClass('expanded').find('.submenu').slideUp(300);
        }
    }

    // Close minicart/search/account on nav opened
    menuBtn.on('click', () => {
        closeMinicart();
        closeAccount();
        closeMobileSearch();
    });

    // =========== login =============== 

    // Close account menu
    let closeAccount = () => {
        $('[data-block="customer-menu"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
    }

    // Close menu/search/minicart on login click
    login.on('click', () => {
        closeMinicart();
        closeMenu();
        closeMobileSearch();
    });
    
    // Mediacheck
    mediaCheck({
        media: breakPoint,
        entry: () => {
            // Mobile mode
            $body.addClass('mobile-view');
        },
        exit: () => {
            // Desktop mode
            desktopCleaup();
            $body.removeClass('mobile-view');
        }
    });

    // No close
    $('.page-header').on('click', (e) => {
        e.stopPropagation()
    });

    $('#show_toolbar_button').on('click', (e) => {
        $(e.target).parents('.center-cell').toggleClass('active');
    });

    let preventScroll = function (e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Prevent scroll on open
    const eBody = document.querySelector('body')
    const options = {
        attributes: true
    }

    function callback(mutationList, observer) {
        mutationList.forEach(function (mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                // handle class change
                if (eBody.classList.contains('scroll-lock')) {
                    eBody.addEventListener('wheel', preventScroll, {passive: false});
                } else {
                    eBody.removeEventListener('wheel', preventScroll, {passive: false});
                }
            }
        })
    }

    const observer = new MutationObserver(callback)
    observer.observe(eBody, options)

});
