define([
    'jquery',
    'matchMedia',
    'scrollbar',
    'domReady!'
], function ($, mediaCheck, scrollbar) {

    'use strict';

    console.log('kosher.pdpPopup');

    $.widget('kosher.pdpPopup', {

        options: {
            breakPoint: '980px',
            popup: '.k4u-popup',
            close: '.k4u-popup #k4u_popup_close',
            _popup: $('.k4u-popup'),
            _close: $('.k4u-popup #k4u_popup_close'),
            _overlay: $('.kosher-overlay-inner')
        },

        _create: function () {
            this._open();
            this._close();
            this._resizing();
            this._scrollbar();

        },

        _calcHeight: function(){
            let innerHeight = $('.k4u-popup__product-container').height();
            this.options._popup.height(innerHeight + 5)
        },

        _resizing: function(){
            let self = this;
            $(window).resize(function() {
                self._calcHeight();
            });
        },

        _open: function () {
            let self = this;
            let openMe = () => {
                $('body').addClass('k4u-popup-on');
                setTimeout(() => {
                    $('body').addClass('fadeOn-popup');
                }, 50)
            }

            $('.product-items .product-image-wrapper, .product-items .product-item-link').on('click', function (e) {
                openMe();
                self._calcHeight();
                e.preventDefault()
            });
        },

        _close: function () {
            let close = this.options._close;
            let overlay = this.options._overlay;

            let closeMe = () => {
                $('body').removeClass('fadeOn-popup');
                setTimeout(() => {
                    $('body').removeClass('k4u-popup-on');
                }, 1000);
            }

            close.on('click', () => {
                closeMe()
            });
            overlay.on('click', () => {
                closeMe()
            });
        },

        _scrollbar: function () {
            $('.k4u-popup').mCustomScrollbar({
                axis: 'y',
                theme: 'popup',
                mouseWheel: {
                    enable: true,
                    axis: 'y'
                },
                scrollButtons: {
                    enable: true
                }
            });
        }

    });

    return $.kosher.pdpPopup;
});
