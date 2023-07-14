define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'jquery-ui-modules/widget',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies',
    'jquery-ui-modules/effect-fade'
], function ($, authenticationPopup, customerData) {
    'use strict';

    return function (sidebar) {
        $.widget('mage.sidebar', sidebar, {
            options: {	
                minicart: {	
                    maxItemsVisible: 20	
                }	
            },

            /**	
             * Update sidebar block.	
             */	
            update: function () {	
                $(this.options.targetElement).trigger('contentUpdated');	
            },

            /**	
             * @private	
             */	
            _initContent: function () {	
                var self = this,	
                    events = {};	
                this.element.decorate('list', this.options.isRecursive);	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['click ' + this.options.button.close] = function (event) {	
                    event.stopPropagation();	
                    $(self.options.targetElement).dropdownDialog('close');	
                };	
                events['click ' + this.options.button.checkout] = $.proxy(function () {	
                    var cart = customerData.get('cart'),	
                        customer = customerData.get('customer'),	
                        element = $(this.options.button.checkout);	
                    if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {	
                        // set URL for redirect on successful login/registration. It's postprocessed on backend.	
                        $.cookie('login_redirect', this.options.url.checkout);	
                        if (this.options.url.isRedirectRequired) {	
                            element.prop('disabled', true);	
                            location.href = this.options.url.loginUrl;	
                        } else {	
                            authenticationPopup.showModal();	
                        }	
                        return false;	
                    }	
                    element.prop('disabled', true);	
                    location.href = this.options.url.checkout;	
                }, this);	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['click ' + this.options.button.remove] = function (event) {	
                    event.stopPropagation();	
                    self._removeItem($(event.currentTarget));	
                };	
                // Increase qty value	
                events['click ' + this.options.item.plus] = function (event) {	
                    event.stopPropagation();	
                    let input = $(event.currentTarget).closest('.details-qty').find('.cart-item-qty');	
                    let val = input.val();	
                    input.val(++val).change()	
                };	
                // Decrease qty value	
                events['click ' + this.options.item.minus] = function (event) {	
                    event.stopPropagation();	
                    let input = $(event.currentTarget).closest('.details-qty').find('.cart-item-qty');	
                    let val = input.val();	
                    input.val(--val).change()	
                };	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['keyup ' + this.options.item.qty] = function (event) {	
                    self._showItemButton($(event.target));	
                };	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['change ' + this.options.item.qty] = function (event) {	
                    self._showItemButton($(event.target));	
                };	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['click ' + this.options.item.button] = function (event) {	
                    event.stopPropagation();	
                    self._updateItemQty($(event.currentTarget));	
                };	
                /**	
                 * @param {jQuery.Event} event deleteAll	
                 */	
                events['click ' + this.options.item.deleteAll] = function (event) {	
                    event.preventDefault();	
                    self._emptyCart();	
                };	
                /**	
                 * @param {jQuery.Event} event	
                 */	
                events['focusout ' + this.options.item.qty] = function (event) {	
                    self._validateQty($(event.currentTarget));	
                };	
                this._on(this.element, events);	
            },

            /**	
             * @param {HTMLElement} elem	
             * @private	
             */	
            _showItemButton: function (elem) {	
                var itemId = elem.data('cart-item'),	
                    itemQty = elem.data('item-qty'),	
                    button = 'button#update-cart-item-' + itemId,	
                    self = this;	
                if (this._isValidQty(itemQty, elem.val())) {	
                    self._updateItemQty($(button));	
                }	
            },

            // Empty cart	
            _emptyCart: function () {	
                const deleteBtn = $('.minicart-items .action.delete');	
                const cartBtn = $('.minicart-wrapper .showcart');	
                const dialog = $('.minicart-wrapper .mage-dropdown-dialog');	
                const cart = $('.block.block-minicart');	
                deleteBtn.each(function (i, item) {	
                    item.click();	
                });	
                setTimeout(() => {	
                    cart.fadeOut(800)	
                }, 1500);	
                setTimeout(() => {	
                    cartBtn.removeClass('active');	
                    cartBtn.closest('div').removeClass('active');	
                    dialog.css('display', 'none');	
                }, 2300);	
                setTimeout(() => {	
                    cart.css('display', 'block');	
                }, 3000);	
            },
        });

        return $.mage.sidebar;
    };
});