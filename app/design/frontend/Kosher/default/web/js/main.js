define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Global scripts');

    const cartBtn = document.querySelector('.minicart-wrapper .showcart');
    cartBtn.addEventListener('click', () => {
        const block = document.querySelector('.header-login-block');
        if (block.classList.contains('active')) {
            block.classList.remove('active');
            block.querySelector('[data-popup-content]').removeAttribute('style');
        }
    });

});
