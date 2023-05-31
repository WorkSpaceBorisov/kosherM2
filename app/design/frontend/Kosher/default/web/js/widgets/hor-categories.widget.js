define([
    'jquery',
    'slick',
    'matchMedia',
    'domReady!'
], function ($, slick, mediaCheck) {

    'use strict';
    
    // console.log('hor.categories');

    $.widget('hor.categories', {
        options: {
            breakPoint: '1600px'
        },

        _create: function () {
            let self = this;
            mediaCheck({
                media: `(max-width: ${this.options.breakPoint})`,
                entry: () => {
                    // Mobile mode
                    this._slider();
                },
                exit: () => {
                    // Desktop mode
                }
            });
        },

        _slider: function () {

            $(this.element).not('.slick-initialized').slick({
                arrows: true,
                dots: false,
                infinite: false,
                speed: 500,
                slidesToShow: 6,
                slidesToScroll: 6,
                prevArrow: 'menu-prev-arrow',
                nextArrow: 'menu-next-arrow',

                responsive: [
                    {
                        breakpoint: 10000,
                        settings: 'unslick'
                    },
                    {
                        breakpoint: 1600,
                        settings: {
                            slidesToShow: 6,
                            slidesToScroll: 2,
                        }
                    },
                    {
                        breakpoint: 1300,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 2,
                        }
                    },
                    {
                        breakpoint: 1000,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 2,
                        }
                    }
                ]
            });
        }
    });

    return $.hor.categories;
});
