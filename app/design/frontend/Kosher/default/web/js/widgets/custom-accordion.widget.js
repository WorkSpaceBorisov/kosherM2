define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Custom accordion by Max');

    $.widget('custom.accordion', {
        options: {
            triggerClass: '[data-accord-title]',
            contentClass: '[data-accord-content]',
            speed: 1500,
            autoClose: true, // Use 0 or false for keep opened
            opened: false // Set number to open on load, starting from 1, false to all closed on start
        },

        _create: function () {
            this._build();
        },

        _build: function () {
            let elem = $(this.element);
            let triggerClass = this.options.triggerClass;
            let contentClass = this.options.contentClass;
            let trigger = $(triggerClass);
            let content = $(contentClass);
            let speed = this.options.speed;
            let opened = this.options.opened;
            let autoClose = this.options.autoClose;

            if (!this.options.autoClose) elem.addClass('noautoclose');
            trigger.css('transition', speed + 'ms');

            if (opened) {
                elem.find(triggerClass).eq(opened - 1).addClass('active').next(contentClass).slideDown(speed).addClass('opened');
            }

            trigger.off().on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active').next(contentClass).slideUp(speed).removeClass('opened');
                    return;
                }
                if (!$(this).parent().hasClass('noautoclose')) {
                    $(this).parent().find(triggerClass).removeClass('active');
                    $(this).parent().find(`${triggerClass}.opened`).slideUp(speed).removeClass('opened');
                }
                if (autoClose) {
                    elem.find('.active').removeClass('active').next(contentClass).slideUp(speed).removeClass('opened')
                }

                $(this).addClass('active').next(contentClass).slideDown(speed).addClass('opened');
            });
        }

    });

    return $.custom.accordion;
});
