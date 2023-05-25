define([
    'jquery',
    'slick',
    'domReady!'
], function ($, slick) {

    'use strict';
    
    console.log('home.productSlider');


    $.widget('home.productSlider', {
        _create: function () {
            this._slider();
        },

        _slider: function () {

            $(this.element).not('.slick-initialized').slick({
                arrows: true,
                dots: false,
                infinite: true,
                speed: 500,
                slidesToShow: 4,
                slidesToScroll: 4,
                lazyLoad: 'ondemand',
                pauseOnFocus: true,
                pauseOnHover: true,

                responsive: [
                    {
                        breakpoint: 1450,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                        }
                    },
                    {
                        breakpoint: 1150,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                        }
                    },
                    {
                        breakpoint: 780,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                        }
                    }
                ]
            });

        }

    });

    return $.home.productSlider;
});
