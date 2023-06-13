define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Custom plus minus widget by Max');

    $.widget('custom.plusMinus', {
        options: {
            minimal: 0,
            limit: 100,
            buttons: true, // Use false to skip _build function and apply buttons manually
            plus: 'btn-plus',
            minus: 'btn-minus'
        },

        _create: function () {
            this.options.buttons === true ? this._build() : this._calc();
        },

        _build: function () {
            let btnMinus = $(`<div class="custom-qty-btn ${self.options.minus}"><span>-</span></div>`);
            let btnPlus = $(`<div class="custom-qty-btn ${self.options.plus}"><span>+</span></div>`);

            this.element.before(btnMinus)
            this.element.after(btnPlus)

            this._calc();
        },

        _buttonDisabler: function (val) {
            let self = this;
            let qty = $(this.element);
            let minus = qty.prev(`.${self.options.minus}`);
            let plus = qty.next(`.${self.options.plus}`);
            +val === self.options.minimal ? minus.addClass('disabled') : minus.removeClass('disabled');
            +val === self.options.limit ? plus.addClass('disabled') : plus.removeClass('disabled');


            if(+val === self.options.minimal){
                let container = qty.closest(".calc-cell-container");
                setTimeout(() => {
                    qty.closest('.show-calc').removeClass('show-calc').find('.disabled').removeClass('disabled');
                    container.addClass('no-hover');
                }, 700)
                setTimeout(() => {
                    qty.val(1);
                    container.removeClass('no-hover');
                }, 2000);
            }

        },

        _calc: function () {

            let self = this;
            let qty = $(this.element);
            let minus = qty.prev(`.${self.options.minus}`);
            let plus = qty.next(`.${self.options.plus}`);

            this._buttonDisabler(qty.val());

            minus.on('click', function () {
                let val = +qty.val();
                if (val === self.options.minimal) return;
                qty.val(--val);
                self._buttonDisabler(val);
                console.log(val);
            });

            plus.on('click', function () {
                let val = +qty.val();
                if (val === self.options.limit) return;
                qty.val(++val);
                self._buttonDisabler(val);
                console.log(val);
            });
        },
    });

    return $.custom.plusMinus;
});
