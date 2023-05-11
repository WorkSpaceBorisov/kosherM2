/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    let btn = $('#catalog_button');
    let menu = $('#kosher_main_menu');

    btn.on('click', () => menu.slideToggle(300, 'swing'));


});
