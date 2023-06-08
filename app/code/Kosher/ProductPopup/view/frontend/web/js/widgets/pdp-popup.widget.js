define([
    'jquery',
    'matchMedia',
    'mousewheel',
    'scrollbar',
    'domReady!'
], function ($, mediaCheck, scrollbar) {

    'use strict';

    // console.log('kosher.pdpPopup');

    $.widget('kosher.pdpPopup', {
        options: {
            breakPoint: '980px',
            popup: '.k4u-popup',
            close: '.k4u-popup #k4u_popup_close',
            overlay: '.kosher-overlay-inner',
            _popup: $('.k4u-popup'),
            _close: $('.k4u-popup #k4u_popup_close'),
            _overlay: $('.kosher-overlay-inner'),
            apiURL: null,
            testSku: '043427181129'
        },

        _create: function () {
            this._open();
            this._close();
            this._resizing();
            this._scrollbar();
        },

        _calcHeight: function () {
            let innerHeight = $('.k4u-popup__product-container').height();
            this.options._popup.height(innerHeight + 5)
        },

        _resizing: function () {
            let self = this;
            $(window).resize(function () {
                self._calcHeight();
            });
        },

        _build: function (responce) {
            let data = $.parseJSON(responce).productData;
            
            let euro = new Intl.NumberFormat('en-DE', {
                style: 'currency',
                currency: 'EUR',
            });

            document.querySelector('.k4u-popup__title').innerHTML = data.name;
            document.querySelector('.popup-image').setAttribute('title', data.name);

            if (data.barcode) {
                document.querySelector('.k4u-popup__attributies .barcode').classList.add('exists');
                document.querySelector('.k4u-popup__attributies .barcode .data').textContent = data.barcode;
            }

            // Netto

            if (data.singleweight) {
                document.querySelector('.k4u-popup__attributies .weight').classList.add('exists');
                document.querySelector('.k4u-popup__attributies .weight .data').textContent = parseFloat(data.singleweight).toFixed(2) * 1000;
                document.querySelector('.k4u-popup__details .netto').classList.add('exists')
                document.querySelector('.k4u-popup__details .netto span').textContent = parseFloat(data.singleweight) + 'kg';
            }

            // Brutto

            if (data.weight) {
                document.querySelector('.k4u-popup__details .brutto').classList.add('exists')
                document.querySelector('.k4u-popup__details .brutto span').textContent = parseFloat(data.weight) + 'kg';
            }

            // Manufacturer

            if (data.manufacturer) {
                let mf = data.manufacturer.toString();
                document.querySelector('.k4u-popup__details .manufacturer').classList.add('exists')
                document.querySelector('.k4u-popup__details .manufacturer span').textContent = mf;
            }

            // Type

            if (data.halavi) {
                let type = data.halavi.toString();
                document.querySelector('.k4u-popup__details .type').classList.add('exists')
                document.querySelector('.k4u-popup__details .type span').textContent = type;
            }

            // Supervision

            if (data.supervision) {
                let sp = data.supervision.toString();
                document.querySelector('.k4u-popup__details .supervision').classList.add('exists')
                document.querySelector('.k4u-popup__details .supervision span').textContent = sp;
            }

            if (!data.quantity_and_stock_status.is_in_stock) {
                document.querySelector('.k4u-popup').classList.add('out-of-stock');
            }

            if (data.special_price) {
                let discount = Math.ceil(100 - (data.special_price * 100) / data.price);
                document.querySelector('.k4u-popup__final-price').textContent = euro.format(data.special_price);
                document.querySelector('.k4u-popup__old-price-data').textContent = euro.format(data.price);
                document.querySelector('.k4u-popup__discount').textContent = discount + '%';
                document.querySelector('.k4u-popup__price-block').classList.add('special');
            }

            if (data.price && !data.special_price) {
                document.querySelector('.k4u-popup__final-price').textContent = euro.format(data.price);
            }

            if (data.description || data.short_description) {
                document.querySelector('.k4u-popup__description').classList.add('exists');
                if (data.description) {
                    document.querySelector('.k4u-popup__description p').textContent = data.description;
                } else {
                    document.querySelector('.k4u-popup__description p').textContent = data.short_description;
                }
            }

            // Image

            if (data.image) {
                let path = '/media/catalog/product/' + data.image;
                let image = document.querySelector('.popup-image').setAttribute('src', path);
            }

            // Image right attributies

            if (data.bio_attribute || data.sugar_free || data.gluten_free) {

                let labelsContainer = document.createElement('div');
                labelsContainer.classList.add('product-image-right-labels');
                document.querySelector('.k4u-popup__product-image-block').appendChild(labelsContainer);

                if(data.sugar_free) {
                    let sf_image = document.createElement('img');
                    sf_image.setAttribute('title', 'Sugar free');
                    sf_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/sf-label-big-01.png');
                    sf_image.classList.add('bio');
                    labelsContainer.appendChild(sf_image);
                }


                if(data.bio_attribute) {
                    let bio_image = document.createElement('img');
                    bio_image.setAttribute('title', 'Bio');
                    bio_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/bio-label-big-01.png');
                    bio_image.classList.add('bio');
                    labelsContainer.appendChild(bio_image);
                }

                if(data.gluten_free) {
                    let gluten_free_image = document.createElement('img');
                    gluten_free_image.setAttribute('title', 'Gluten free');
                    gluten_free_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/gf-label-big-01.png');
                    gluten_free_image.classList.add('gf');
                    labelsContainer.appendChild(gluten_free_image);
                }
            }

            this._openMe();
        },

        _askAPI: function (sku) {
            let self = this;
            $.ajax({
                url: this.options.apiURL,
                type: 'GET',
                dataType: 'json',
                data: {
                    sku: sku
                },
            }).done(function (response) {
                self._build(response);
            });
        },

        _openMe: function () {
            $('body').addClass('k4u-popup-on');
            setTimeout(() => {
                $('body').addClass('fadeOn-popup');
            }, 50)
            this._calcHeight();
        },

        _open: function () {
            let self = this;
            $('.product-items .product-image-wrapper, .product-items .product-item-link').on('click', function (e) {
                let sku = $(this).closest('.product-item-info').find('.hidden-sku').data('sku');
                self._askAPI(sku);
                e.preventDefault()
            });
        },

        _close: function () {
            let overlay = this.options.overlay;
            let close = this.options.close;

            let closeMe = (e) => {
                $('body').removeClass('fadeOn-popup');
                setTimeout(() => {
                    $('body').removeClass('k4u-popup-on');
                }, 500);
            }

            $(overlay + ', ' + close).on('click', (e) => {
                closeMe();
                setTimeout(() => {
                    $('.calc-cell-container').removeClass('show-calc');
                    $('.k4u-popup *').removeClass('exists');
                    $('.k4u-popup').removeClass('out-of-stock');
                    $('.k4u-popup__price-block').removeClass('special');
                    $('.product-image-right-labels').remove();
                }, 500)
                e.preventDefault();
            });
        },

        _scrollbar: function () {
            $('.k4u-popup').mCustomScrollbar({
                axis: 'y',
                theme: 'popup',
                scrollInertia: 160,
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
