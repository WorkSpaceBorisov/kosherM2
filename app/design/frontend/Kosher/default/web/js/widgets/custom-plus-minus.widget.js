define(['jquery', 'Magento_Customer/js/customer-data', 'domReady!'], function (
    $,
    customerData,
) {
    'use strict';

    $.widget('custom.plusMinus', {
        options: {
            minimal: 0,
            limit: 100,
            buttons: true, // Use false to skip _build function and apply buttons manually
            plus: 'btn-plus',
            minus: 'btn-minus',
            keyup: true,
            productId: '',
            updateQtyUrl: '',
            removeItemUrl: '',
        },

        /**
         * @private
         */
        _create() {
            this.options.buttons === true ? this._build() : this._calc();
            this.options.keyup === true ? this._initKeyup() : null;

            // Add to cart category button slide
            const $body = $('body');
            if ($body.hasClass('page-products') || $body.hasClass('cms-index-index')) {
                $('.add-to-calc__button').on('click', (e) => $(e.target).closest('.calc-cell-container').addClass('show-calc'));
            }
        },

        /**
         * @return {*}
         */
        _build() {
            let btnMinus = $(
                `<div class="custom-qty-btn ${self.options.minus}"><span>-</span></div>`,
            );
            let btnPlus = $(
                `<div class="custom-qty-btn ${self.options.plus}"><span>+</span></div>`,
            );

            this.element.before(btnMinus);
            this.element.after(btnPlus);

            this._calc();
        },

        /**
         * @param {string} val
         * @return {*}
         */
        _buttonDisabler(val) {
            let self = this;
            let qty = $(this.element);
            let minus = qty.prev(`.${self.options.minus}`);
            let plus = qty.next(`.${self.options.plus}`);
            +val === self.options.minimal
                ? minus.addClass('disabled')
                : minus.removeClass('disabled');
            +val === self.options.limit
                ? plus.addClass('disabled')
                : plus.removeClass('disabled');

            if (+val === self.options.minimal) {
                let container = qty.closest('.calc-cell-container');
                setTimeout(() => {
                    qty.closest('.show-calc')
                        .removeClass('show-calc')
                        .find('.disabled')
                        .removeClass('disabled');
                    container.addClass('no-hover');
                }, 700);
                setTimeout(() => {
                    qty.val(1);
                    container.removeClass('no-hover');
                }, 2000);
            }
        },

        /**
         * Calculate logic
         * @return {*}
         */
        _calc() {
            let self = this;
            let qty = $(this.element);
            let minus = qty.prev(`.${self.options.minus}`);
            let plus = qty.next(`.${self.options.plus}`);

            this._buttonDisabler(qty.val());

            minus.on('click', function () {
                self._addLoader();
                let val = +qty.val();
                if (val === self.options.minimal) return;
                qty.val(--val);
                self._buttonDisabler(val);
                self._changeQty('decrease');
            });

            plus.on('click', function () {
                self._addLoader();
                let val = +qty.val();
                if (val === self.options.limit) return;
                qty.val(++val);
                self._buttonDisabler(val);
                self._changeQty('increase');
            });
        },

        /**
         * Add keyup logic for updating qty
         * @return {*}
         */
        _initKeyup() {
            const self = this;
            const qty = $(this.element);

            qty.on('keyup', () => {
                self._addLoader();
                self._changeQty(qty.val());
                qty.trigger('blur');
            });
        },

        /**
         * Change qty for current product
         * @param {Number} sign increase or decrease the product qty
         * @private
         */
        _changeQty(sign) {
            const self = this;
            const cartItems = customerData.get('cart')().items;
            const product = cartItems.find(
                (item) => item.product_id === this.options.productId,
            );

            // check if product still in the cart
            if (product) {
                const productQty = product.qty;
                const productItemId = product.item_id;
                const formKey = $(this.element)
                    .parents('[data-role="tocart-form"]')
                    .find('[name="form_key"]')
                    .val();
                let newQuantity;
                let data;
                let actionUrl;

                if (sign === 'increase') {
                    newQuantity = productQty + 1;
                } else if (sign >= 1) {
                    newQuantity = Number(sign);
                } else if (sign == 0) {
                    newQuantity = 1;
                    $(self.element).val(1);
                } else {
                    newQuantity = productQty - 1;
                }

                newQuantity === 0
                    ? this._removeItem(productItemId, formKey)
                    : this._updateItemQty(productItemId, formKey, newQuantity);

            }

            // submit ajax form only if qty !== '0'
            if (!product && $(this.element).val() !== '0') {
                this._submitAjaxForm();
            }

            setTimeout(() => {
                this._removeLoader();
            }, 1000);
        },

        /**
         * Update item qty in cart
         * @param {String} productId
         * @param {String} formKey
         * @param {String} qty
         * @returns {void}
         */
        _updateItemQty(productId, formKey, qty) {
            const self = this;

            $.ajax({
                url: this.options.updateQtyUrl,
                data: {
                    item_id: productId,
                    item_qty: qty,
                    form_key: formKey,
                },
                type: 'post',
                dataType: 'json',
            });
        },

        /**
         * Remove item from cart
         * @param {String} productId
         * @param {String} formKey
         * @returns {void}
         */
        _removeItem(productId, formKey) {
            $.ajax({
                url: this.options.removeItemUrl,
                data: {
                    item_id: productId,
                    form_key: formKey,
                },
                type: 'post',
                dataType: 'json',
            });
        },

        /**
         * Add item to cart
         * For the situation when you add product, change qty and then remove the product from minicart
         * then next click on +/- will add needed amount of products
         * @returns {void}
         */
        _submitAjaxForm() {
            $(this.element).parents('[data-role="tocart-form"]').submit();
            this._removeLoader();
        },

        /**
         * Add loader
         * @returns {void}
         */
        _addLoader() {
            $(this.element).closest('.calc-cell').addClass('loading');
        },

        /**
         * Remove loader
         * @returns {void}
         */
        _removeLoader() {
            $(this.element).closest('.calc-cell').removeClass('loading');
        },
    });

    return $.custom.plusMinus;
});
