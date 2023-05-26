define([
    'jquery',
    'slick',
    'domReady!'
], function ($, slick) {

    'use strict';

    console.log('home.mainSlider');

    $.widget('home.mainSlider', {

        options: {
            initial: 3
        },

        _create: function () {
            this.options.initial = this._getRandomSlide();
            this._slider();
        },

        _getRandomSlide() {
            let min = 0;
            let max = +$(this.element).find('li').length;
            let random = (min, max) => Math.floor(Math.random() * (max - min)) + min;
            return random(min, max)
        },

        _slider: function () {

            $(this.element).not('.slick-initialized').slick({
                arrows: false,
                dots: true,
                infinite: true,
                autoplay: true,
                fade: true,
                speed: 2000,
                autoplaySpeed: 3000,
                // autoplaySpeed: 3000000,
                slidesToShow: 1,
                slidesToScroll: 1,
                lazyLoad: 'ondemand',
                easing: 'ease-out',
                pauseOnFocus: true,
                pauseOnHover: true,
                initialSlide: this.options.initial
            });

        }

    });

    return $.home.mainSlider;
});
