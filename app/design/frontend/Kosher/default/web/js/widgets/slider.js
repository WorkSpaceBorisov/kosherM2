define([
    'jquery',
    'slick',
    'matchMedia',
    'domReady!'
], function ($, slick, mediaCheck) {
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
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                        }
                    },
                    {
                        breakpoint: 768,
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
            onlyDesktop: false,
            initial: 1
        },

        /**
         * Default create function of jQuery UI Factory.
         * @private
         */
        _create() {
            var self = this,
                element = self.element;

            if (this.options.useCarousel) {
                if (this.options.onlyDesktop) {
                    mediaCheck({
                        media: '(min-width: 768px)',
                        entry: function () {
                            this.slickObject = self.element.slick(this.options.carouselOptions);
                        }.bind(this),
                        exit: function () {
                            setTimeout(function () {
                                if (this.slickObject) {
                                    this.slickObject.slick('destroy');
                                    this.slickObject = null;
                                }
                            }.bind(this), 500)
                        }.bind(this)
                    })
                } else {
                    this.slickObject = self.element.slick(this.options.carouselOptions);
                }
            } else {
                this.options.sliderOptions.initialSlide = self._getRandomSlide();
                this.slickObject  = self.element.slick(self.options.sliderOptions);
            }

            $(document).on('slider:needUpdate', $.proxy(this._recalcSlider, this));

            if (!this.slickObject) return this;
        },

        _recalcSlider: function () {
            this.slickObject.slick('setPosition');
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
        }

    });

    return $.kosh.slider;
});
