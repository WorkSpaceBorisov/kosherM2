define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Custom plus minus widget by Max');

    $.widget('custom.plusMinus', {
        options: {
            minimal: 1,
            limit: 10000,
            buttons: true // Use false to skip _build function and apply buttons manually
        },

        _create: function () {
            this.options.buttons === true ? this._build() : this._calc();
        },

        _build: function () {
            let btnMinus = $('<div class="custom-qty-btn btn-minus"><span>-</span></div>');
            let btnPlus = $('<div class="custom-qty-btn btn-plus"><span>+</span></div>');

            this.element.before(btnMinus)
            this.element.after(btnPlus)

            this._calc();
        },

        _calc: function () {

            let self = this;
            let qty = $(this.element);

            qty.prev('.btn-minus').on('click', function () {
                let val = +qty.val();
                if (val === self.options.minimal) return;
                qty.val(--val);
            });

            qty.next('.btn-plus').on('click', function () {
                let val = +qty.val();
                if (val === self.options.limit) return;
                qty.val(++val);
            });

        },
    });

    return $.custom.plusMinus;
});


// .box-tocart.custom{
//     margin-bottom: 15px;
//
// .field.qty{
//         display: flex;
//         justify-content: flex-start;
//         width: unset;
//     }
// }
// .input-text.qty.custom{
//     width: 100px;
//     margin-left: 5px;
//     margin-right: 5px;
// }
// .custom-qty-btn{
//     background: #eee;
//     display: flex;
//     align-items: center;
//     justify-content: center;
//     width: 40px;
//     height: 40px;
//     border-radius: 6px;
//     border: 1px solid #bbb;
//     flex-grow: 0;
//     flex-shrink: 0;
// & > span{
//     .lib-font-size(20);
//         display: block;
//         font-weight: 700;
//         line-height: 1;
//         text-align: center;
//     }
//
// &:hover{
//         cursor: pointer;
//     }
//
// }
///////////////////////////////////////////////////////////////////////////////////////////////////
// <div class="box-tocart custom">
//     <div class="field qty">
//     <input type="text" id="custom_qty" value="1" class="input-text qty custom">
//     </div>
//     </div>
//     <div class="box-tocart custom">
//     <div class="field qty">
//     <div class="custom-qty-btn btn-minus"><span>-</span></div>
// <input type="text" id="custom_qty2" value="1" class="input-text qty custom">
//     <div class="custom-qty-btn btn-plus"><span>+</span></div>
// </div>
// </div>
// <div class="box-tocart custom">
//     <div class="field qty">
//     <div class="custom-qty-btn btn-minus"><span>-</span></div>
// <input type="text" id="custom_qty3" value="1" class="input-text qty custom"
// data-mage-init='{"Magento_Theme/js/widgets/plus-minus.widget":{"buttons": false, "limit": 3}}'>
//     <div class="custom-qty-btn btn-plus"><span>+</span></div>
// </div>
// </div>
///////////////////////////////////////////////////////////////////////////////////////////////////
// <script type="text/x-magento-init">
//     {
//         "#custom_qty": {
//             "Magento_Theme/js/widgets/plus-minus.widget": {
//                 "limit": 5
//             }
//         },
//         "#custom_qty2": {
//             "Magento_Theme/js/widgets/plus-minus.widget": {
//                 "limit": 5,
//                 "buttons": false
//             }
//         }
//     }
// </script>
