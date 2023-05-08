define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Custom accordion by Max');

    $.widget('custom.accordion', {
        options: {
            speed: 300,
            autoClose: true, // Use 0 or false for keep opened
            opened: false, // Set number to open on load, starting from 1, false to all closed on start
        },

        _create: function () {
            this._build();
        },

        _build: function () {
            let self = this;
            let elem = $(this.element);
            let title = $('[data-accord-title]');
            let content = $('[data-accord-content]');
            let speed = this.options.speed;
            let arrow = $('[data-arrow]');
            let opened = this.options.opened;

            if (!this.options.autoClose) elem.addClass('noautoclose');
            if (elem.find('[data-arrow]')) arrow.css('transition', speed + 'ms');

            if (opened) {
                elem.find('[data-accord-title]').eq(opened - 1).addClass('opened').next(content).slideDown(speed).addClass('opened');
            }

            title.off().on('click', function () {
                if ($(this).hasClass('opened')) {
                    $(this).removeClass('opened').next(content).slideUp(speed).removeClass('opened')
                    return;
                }
                if (!$(this).parent().hasClass('noautoclose')) {
                    $(this).parent().find('[data-accord-title]').removeClass('opened');
                    $(this).parent().find('[data-accord-content].opened').slideUp(speed).removeClass('opened');
                }
                $(this).addClass('active opened').next(content).slideDown(speed).addClass('opened');
            });
        }

    });

    return $.custom.accordion;
});
