define([
    'jquery',
    'scrollbar',
    'matchMedia',
    'domReady!'
], function ($, scrollbar) {

    'use strict';

    console.log('home.categoriesScroller');


    $.widget('home.categoriesScroller', {
        options: {
            breakPoint: '1600px'

        },

        _create: function () {
            mediaCheck({
                media: `(max-width: ${this.options.breakPoint})`,
                entry: () => {
                    // Mobile mode
                    this._scrollbar();
                },
                exit: () => {
                    // Desktop mode
                    $(this.element).mCustomScrollbar('destroy')
                }
            });
        },

        _scrollbar: function () {
            $(this.element).mCustomScrollbar({
                axis: 'x',
                theme: 'kosher-1',
                mouseWheel: {
                    enable: true,
                    axis: 'x'
                },
                scrollButtons: {
                    enable: true
                }
            });
        }

    });

    return $.home.categoriesScroller;
});
