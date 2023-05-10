define([
    'jquery',
    'domReady!'
], function($) {

    'use strict';

    // console.log('Custom dropdowns by Max');

    $.widget('custom.dropdown', {
        options: {
            speed: 150,
            container: '[data-popup-container]',
            trigger: '[data-popup-trigger]',
            content: '[data-popup-content]'
        },

        _create: function() {
            this._build();
        },

        _build: function() {
            let self = this;
            let elem = $(this.element);
            let container = this.options.container;
            let trigger = this.options.trigger;
            let content = this.options.content;
            let speed = this.options.speed;

            $(trigger).on('click', function() {
                if ($(this).closest(container).hasClass('active')) {
                    $(this).closest(container).removeClass('active').find(content).hide();
                    return;
                }
                $(this).closest(elem).find(container).not('[data-no-autoclose]').removeClass('active');
                $(this).closest(elem).find(container).not('[data-no-autoclose]').find(content).hide();
                $(this).closest(container).addClass('active').find(content).slideDown(speed);
            });
        }
    });

    return $.custom.dropdown;
});