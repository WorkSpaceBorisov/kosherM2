define([
    'jquery',
    'matchMedia',
    'custom.plusMinus',
    'slick',
    'domReady!'
], function ($, mediaCheck, plusMinus, slick) {

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
            searchUrl: '',
            testSku: '043427181129'
        },

        _create: function () {
            this._open();
            this._close();
            this._resizing();
        },

        _calcHeight: function () {
            let innerHeight = $('.k4u-popup__product-container').height();
            this.options._popup.height(innerHeight + 5)
        },

        _resizing: function () {
            let self = this;
            $(window).resize(function () {
                // self._calcHeight();
            });
        },

        _build: function (responce) {
            let container = document.querySelector('.k4u-popup .k4u-popup__inner');
            let data = responce[0];
            const urlForSearch = this.options.searchUrl;

            let euro = new Intl.NumberFormat('en-DE', {
                style: 'currency',
                currency: 'EUR',
            });

            let newTag = (tag, name, content) => {
                let elem = document.createElement(tag);
                name ? elem.className = name : null;
                content ? elem.innerHTML = content : null;
                return elem;
            }

            // Image

            let imageContainer = newTag('div', 'k4u-popup__product-image-block')
            if (data.image) {
                let img = newTag('img', 'popup-image');
                let path = data.image;
                img.setAttribute('title', data.name);
                img.setAttribute('src', path);
                imageContainer.appendChild(img);
            }
            container.appendChild(imageContainer);

            // InfoBlock

            let infoBlock = newTag('div', 'k4u-popup__product-info-block');

            infoBlock.appendChild(newTag('h3', 'k4u-popup__title', data.name));

            // Main attributies

            let mainAttrsBlock = newTag('ul', 'k4u-popup__attributies');

            if (data.barcode) {
                let content = '<span class="label">Barcode</span> <span class="data">' + data.barcode + '</span></li>';
                mainAttrsBlock.appendChild(newTag('li', 'barcode', content))
            }

            var kgWeight;

            if (data.singleweight) {
                let val = parseFloat(data.singleweight).toFixed(2) * 1000;
                let content = '<span class="label">Weight,gr</span> <span class="data">' + val + '</span></li>';
                kgWeight = data.singleweight + 'kg';
                let weight = newTag('li', 'weight', content);
                mainAttrsBlock.appendChild(weight);
            }

            infoBlock.appendChild(mainAttrsBlock);

            if (!data.quantity_and_stock_status.is_in_stock) {
                document.querySelector('.k4u-popup').classList.add('out-of-stock');
                let content = '<div class="stock">\n' +
                    '                        <span class="out-of-stock">Out of Stock</span>\n' +
                    '                    </div>';
                infoBlock.appendChild(newTag('div', 'k4u-popup__out-of-stock', content));
            }

            // Finblock start

            let finBlock = newTag('div', 'k4u-popup-fin-block');

            // Build price

            let priceBlock = newTag('div', 'k4u-popup__price-block');

            if (data.special_price) {

                let oldPriceBlock = newTag('span', 'k4u-popup__old-price');
                let discount = Math.ceil(100 - (data.special_price * 100) / data.price);
                let oldPriceData = newTag('span', 'k4u-popup__old-price-data', euro.format(data.price));
                let discountData = newTag('span', 'k4u-popup__discount', discount + '%');
                oldPriceBlock.append(oldPriceData, discountData);

                let finalPriceBlock = newTag('span', 'k4u-popup__final-price', euro.format(data.special_price));

                priceBlock.append(oldPriceBlock, finalPriceBlock)

            }

            if (data.price && !data.special_price) {
                priceBlock.appendChild(newTag('div', 'k4u-popup__final-price', euro.format(data.price)));
            }

            finBlock.appendChild(priceBlock);

            if (!data.quantity_and_stock_status.is_in_stock) {
                let content = '<div class="k4u-popup__notify-user__button">\n' +
                    '                            <span>Notify Availability</span>\n' +
                    '                        </div>';
                finBlock.appendChild(newTag('div', 'k4u-popup__notify-user', content));
            } else {
                let calcBlock = newTag('div', 'k4u-popup__add-to-cart');
                let calcContainer = newTag('div', 'calc-cell-container');

                let calcCell = newTag('div', 'calc-cell calculator');
                let minus = newTag('div', 'custom-qty-btn btn-minus', '<span>-</span>');
                let plus = newTag('div', 'custom-qty-btn btn-plus', '<span>+</span>')
                let input = newTag('input', 'input-text qty custom in-popup');
                input.setAttribute('value', 1);
                input.setAttribute('type', 'number');
                calcCell.append(minus, input, plus);

                let addToCartCell = newTag('div', 'add-to-calc calc-cell');
                let btn = newTag('button', 'add-to-calc__button', '<span>Add to cart</span>')
                addToCartCell.appendChild(btn);

                calcContainer.append(calcCell, addToCartCell);
                calcBlock.append(calcContainer)

                btn.addEventListener('click', function () {
                    calcContainer.classList.add('show-calc');
                })

                finBlock.appendChild(calcBlock);
            }

            infoBlock.appendChild(finBlock);

            // Finblock end

            // Additional info

            let addInfoBlock = newTag('div', 'k4u-popup__additional-info');

            if (data.description || data.short_description) {
                let title = newTag('h4', null, 'Ingredients:');
                let content = data.description || data.short_description;
                addInfoBlock.append(newTag('h4', null, 'Ingredients:'), newTag('p', null, content));
                addInfoBlock.addEventListener('copy', function (e){
                    e.preventDefault();
                    e.clipboardData.setData("text/plain", "Do not copy this block content!");
                })
            }

            infoBlock.appendChild(addInfoBlock);

            // Attributies

            let popupDetails = newTag('div', 'k4u-popup__details')
            let attrList = newTag('ul', 'k4u-popup__details-list');

            let attributies = {
                'manufacturer': data.manufacturer,
                'supervision': data.supervision,
                'weight': data.weight,
                'singleweight': data.singleweight,
                'halavi': data.halavi // Type
            }

            let simpleAttr = (name, content, attr) => {
                let liClass = 'product-attribute-class-' + name;
                let dataAttr = 'data-product-attribute-' + name;

                let li = newTag('li', liClass);
                let span = newTag('span', null, content);
                span.setAttribute(dataAttr, attr || content)
                li.appendChild(span)
                attrList.appendChild(li)
            }

            let objAttr = (name, attrs) => {
                let val = '';
                if (name == 'halavi') name = 'type';

                for (let item in attrs) {
                    let innerElement;
                    if(name === 'manufacturer' || name === 'supervision') {
                        innerElement = `<a href="${urlForSearch}${attrs[item]}" target="_blank"><span>${attrs[item]}</span></a>`;
                    } else {
                        innerElement = `<span data-attrib-id="${item}">${attrs[item]}</span>`;
                    }
                    
                    val += innerElement;
                }

                if (Object.keys(attrs).length > 1) {
                    val = `<span  class="attrs-list">${val}</span>`;
                    name += ' attrs-list-item';
                }
                ;

                attrList.appendChild(newTag('li', 'product-attribute-class-' + name, val));
            }

            for (let item in attributies) {
                let val = attributies[item];

                switch (item) {

                    case 'weight':
                        let weight = parseFloat(data.weight);
                        simpleAttr(item, weight + 'kg', weight);
                        break;
                    case 'singleweight':
                        let singleweight = parseFloat(data.singleweight);
                        simpleAttr(item, singleweight + 'kg', singleweight);
                        break;
                    case 'manufacturer':
                    case 'halavi':
                        if (data[item]) objAttr(item, data[item])
                        break;
                    case 'supervision':
                        if (data[item] && typeof data[item] === 'object') objAttr(item, data[item])
                        break;
                    default:
                        simpleAttr(item, val)

                }
            }

            popupDetails.appendChild(attrList);
            infoBlock.appendChild(popupDetails)

            // Image right attributies

            if (data.bio_attribute || data.sugar_free || data.gluten_free) {

                let labelsContainer = document.createElement('div');
                labelsContainer.classList.add('product-image-right-labels');
                document.querySelector('.k4u-popup__product-image-block').appendChild(labelsContainer);

                if (data.sugar_free) {
                    let sf_image = document.createElement('img');
                    sf_image.setAttribute('title', 'Sugar free');
                    sf_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/sf-label-big-01.svg');
                    sf_image.classList.add('bio');
                    labelsContainer.appendChild(sf_image);
                }


                if (data.bio_attribute) {
                    let bio_image = document.createElement('img');
                    bio_image.setAttribute('title', 'Bio');
                    bio_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/bio-label-big-01.svg');
                    bio_image.classList.add('bio');
                    labelsContainer.appendChild(bio_image);
                }

                if (data.gluten_free) {
                    let gluten_free_image = document.createElement('img');
                    gluten_free_image.setAttribute('title', 'Gluten free');
                    gluten_free_image.setAttribute('src', '/static/frontend/Kosher/default/en_US/images/labels/gf-label-big-01.svg');
                    gluten_free_image.classList.add('gf');
                    labelsContainer.appendChild(gluten_free_image);
                }
            }

            container.appendChild(infoBlock);

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
            $('body').addClass('k4u-popup-on scroll-lock');
            setTimeout(() => {
                $('body').addClass('fadeOn-popup');
            }, 50)
            // this._calcHeight();
            $('.k4u-popup__product-container .input-text.in-popup').plusMinus({'buttons': false, 'limit': 10000});
        },

        _open: function () {
            let self = this;
            $('.product-items .product-image-wrapper, .product-items .product-item-link').on('click', function (e) {
                let sku = $(this).closest('.product-item-info').find('.hidden-sku').data('sku');
                self._askAPI(sku);
                self._slider();
            });
        },

        _close: function () {
            let overlay = this.options.overlay;
            let close = this.options.close;

            let closeMe = (e) => {
                $('body').removeClass('fadeOn-popup scroll-lock');
                setTimeout(() => {
                    $('body').removeClass('k4u-popup-on');
                }, 500);
            }

            $(overlay + ', ' + close).on('click', (e) => {
                closeMe();

                document.querySelector('.k4u-popup .k4u-popup__inner').innerHTML = '';
                document.querySelector('.k4u-popup').classList.remove('out-of-stock');
                e.preventDefault();
            });
        },

        _slider: function () {

            let isSlider = $('.k4u-popup__slider-container .widget-product-grid');

            isSlider.on('init', function (event, slick) {
                let width = $('.k4u-popup').width();
                console.log('initialized ', width);
                isSlider.find('.slick-list').width(width - 48);
            });

            isSlider.not('.slick-initialized').slick({
                arrows: true,
                dots: false,
                infinite: true,
                speed: 500,
                slidesToShow: 3,
                slidesToScroll: 3,
                lazyLoad: 'ondemand',
                pauseOnFocus: true,
                pauseOnHover: true
            });

        }


    });

    return $.kosher.pdpPopup;
});
