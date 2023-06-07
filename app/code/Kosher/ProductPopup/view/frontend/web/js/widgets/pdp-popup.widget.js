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
            _popup: $('.k4u-popup'),
            _close: $('.k4u-popup #k4u_popup_close'),
            _overlay: $('.kosher-overlay-inner'),
            data: {
                'productData': {
                    'entity_id': '37',
                    'attribute_set_id': '4',
                    'type_id': 'simple',
                    'sku': '043427181129',
                    'has_options': '0',
                    'required_options': '0',
                    'created_at': '2010-03-02 20: 58: 20',
                    'updated_at': '2023-02-27 15: 18: 55',
                    'name': 'Corn Chips Original',
                    'meta_title': 'Lieber&#39s Corn Chips',
                    'meta_description': 'lieber\'s corn chips',
                    'image': '/b/0/b0a77963fdc091dd10caef7b891ee97ed5a47118_product_orig_23_3_6_2.jpg',
                    'small_image': '/b/0/b0a77963fdc091dd10caef7b891ee97ed5a47118_product_orig_23_3_6_2.jpg',
                    'thumbnail': '/b/0/b0a77963fdc091dd10caef7b891ee97ed5a47118_product_orig_23_3_6_2.jpg',
                    'url_key': 'lieber-s-corn-chips',
                    'custom_design': null,
                    'page_layout': null,
                    'options_container': 'container2',
                    'image_label': null,
                    'small_image_label': null,
                    'thumbnail_label': null,
                    'country_of_manufacture': null,
                    'msrp_display_actual_price_type': '4',
                    'gift_message_available': null,
                    'barcode': '043427181129',
                    'singleweight': '0.028',
                    'supervision': '244',
                    'estimated_weight': null,
                    'subtitle': null,
                    'mailchimp_sync_error': null,
                    'sorting_name': 'Corn Chips Original',
                    'manufacturer': '110',
                    'status': '1',
                    'visibility': '4',
                    'tax_class_id': '2',
                    'halavi': '4',
                    'kosherforpesach': '0',
                    'cube_category_featured': '0',
                    'ebizmarts_mark_visited': '0',
                    'mailchimp_sync_modified': '1',
                    'country': null,
                    'suppliers': '495',
                    'price': '0.750000',
                    // 'special_price': null,
                    'special_price': '0.55000',
                    'weight': '0.120000',
                    'minimal_price': '0.500000',
                    'msrp': null,
                    'special_from_date': null,
                    'special_to_date': null,
                    'news_from_date': null,
                    'news_to_date': null,
                    'custom_design_from': null,
                    'custom_design_to': null,
                    'mailchimp_sync_delta': null,
                    'dv_deal_from': null,
                    'dv_deal_to': null,
                    // 'description': null,
                    'description': 'Kosher Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    // 'description': 'Kosher long Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Kosher Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Kosher long Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Kosher Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'short_description': 'Potatoes,vegetable oil,corn,cottonseed,sunflower,soybean or canola oil,wheat starch,tomato powder',
                    'meta_keyword': 'lieber\'s, corn, chips',
                    'custom_layout_update': null,
                    'options': [],
                    'media_gallery': {
                        'images': {
                            '25049': {
                                'value_id': '25049',
                                'file': '/b/0/b0a77963fdc091dd10caef7b891ee97ed5a47118_product_orig_23_3_6_2.jpg',
                                'media_type': 'image',
                                'entity_id': '37',
                                'label': null,
                                'position': '0',
                                'disabled': '0',
                                'label_default': null,
                                'position_default': '0',
                                'disabled_default': '0',
                                'video_provider': null,
                                'video_url': null,
                                'video_title': null,
                                'video_description': null,
                                'video_metadata': null,
                                'video_provider_default': null,
                                'video_url_default': null,
                                'video_title_default': null,
                                'video_description_default': null,
                                'video_metadata_default': null
                            }
                        },
                        'values': []
                    },
                    'extension_attributes': {},
                    'tier_price': [],
                    'tier_price_changed': 0,
                    'quantity_and_stock_status': {
                        'is_in_stock': true,
                        // 'is_in_stock': false,
                        'qty': -1080
                    },
                    'category_ids': [
                        '2',
                        '11',
                        '292',
                        '1122'
                    ],
                    'is_salable': 1
                },
                'status': true
            }
        },

        _create: function () {
            this._build();
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

        _build: function () {
            let data = this.options.data.productData;

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

            if (data.weight) {
                document.querySelector('.k4u-popup__attributies .weight').classList.add('exists');
                document.querySelector('.k4u-popup__attributies .weight .data').textContent = parseFloat(data.weight).toFixed(2) * 1000;
                document.querySelector('.k4u-popup__details .netto span').textContent = parseFloat(data.weight) + 'kg';
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
                document.querySelector('.k4u-popup__description').classList.add('exists')
                if (data.description) {
                    document.querySelector('.k4u-popup__description p').textContent = data.description;
                } else {
                    document.querySelector('.k4u-popup__description p').textContent = data.short_description;
                }
            }

            // Image

            if (data.image) {
                let path = 'http://' + location.hostname + '/media/catalog/product/' + data.image;
                let image = document.querySelector('.popup-image').setAttribute('src', path);
            }

            this._open();
        },

        _open: function () {
            let self = this;
            let openMe = () => {
                $('body').addClass('k4u-popup-on');
                setTimeout(() => {
                    $('body').addClass('fadeOn-popup');
                }, 50)
            }

            $('.product-items .product-image-wrapper, .product-items .product-item-link').on('click', function (e) {
                openMe();
                self._calcHeight();
                e.preventDefault()
            });

        },

        _close: function () {
            let close = this.options._close;
            let overlay = this.options._overlay;

            let closeMe = () => {
                $('body').removeClass('fadeOn-popup');
                setTimeout(() => {
                    $('body').removeClass('k4u-popup-on');
                }, 1000);
            }

            close.on('click', () => {
                closeMe()
            });
            overlay.on('click', () => {
                closeMe()
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
