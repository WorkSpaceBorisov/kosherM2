define([
    'jquery',
    'slick',
    'domReady!'
], function ($, slick) {
    'use strict';

    $.widget('kosh.slider', {
        options: {
            sliderOptions: {
                arrows: false,
                dots: true,
                infinite: true,
                autoplay: true,
                fade: true,
                speed: 2000,
                autoplaySpeed: 3000,
                slidesToShow: 1,
                slidesToScroll: 1,
                lazyLoad: 'ondemand',
                easing: 'ease-out',
                pauseOnFocus: true,
                pauseOnHover: true,
            },
            carouselOptions: {
                arrows: true,
                dots: false,
                infinite: true,
                draggable: false,
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
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            draggable: true,
                            arrows: false
                        }
                    }
                ]
            },
            
            useCarousel: false,
            initial: 1
        },

        /**
         * Default create function of jQuery UI Factory.
         * @private
         */
        _create() {
            this.slickObject = this._initSlick();
        },

        /**
         * Get random slide number
         * @returns {Number}
         */
        _getRandomSlide() {
            let min = 0;
            let max = +$(this.element).find('li').length;
            let random = (min, max) => Math.floor(Math.random() * (max - min)) + min;
            return random(min, max)
        },

        /**
         * Call all methods of initialization.
         * @private
         */
        _initSlick() {
            let slickObject = null;

            if (this.options.useCarousel) {
                slickObject = this.element.slick(this.options.carouselOptions);
            } else {
                this.options.sliderOptions.initialSlide = this._getRandomSlide();
                slickObject = this.element.slick(this.options.sliderOptions);
            }

            return slickObject;
        }

    });

    return $.kosh.slider;
});
