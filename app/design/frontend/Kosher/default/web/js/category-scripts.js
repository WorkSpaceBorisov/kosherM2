define([
    'jquery',
    'matchMedia'
], function ($, mediaCheck) {

    'use strict';

    // console.log('app/design/frontend/Kosher/default/web/js/category-scripts.js');

    const html = document.querySelector('html')
    const body = document.body;

    // Filters on/off

    let removeFilters = () => {
        document.body.classList.remove('filters-on');
        html.classList.remove('not100');
    }

    document.querySelector('#show_filters_button').addEventListener('click', () => {
        document.body.classList.add('filters-on');
        html.classList.add('not100');
        document.querySelector('.center-cell').classList.remove('active');
    });

    document.querySelector('#hide_filters_button').addEventListener('click', removeFilters);
    document.querySelector('.catalog-category-view #kosher_overlay').addEventListener('click', removeFilters);

    if (document.querySelector('.products.wrapper').classList.contains('products-list')) {
        document.body.classList.add('products-list');
        // document.body.classList.add('slide-filters');
    }

    if (document.querySelector('.products.wrapper').classList.contains('products-grid')) document.body.classList.add('products-grid');

    document.querySelector('#show_toolbar_button').addEventListener('click', (e) => {
        e.target.closest('.center-cell').classList.toggle('active');
    });


    const productListSlideFiltersOn = '(max-width: 1200px)';
    const productGridSlideFiltersOn = '(max-width: 1000px)';

    // Mediachecks

    mediaCheck({
        media: productListSlideFiltersOn,
        entry: () => {
            // Mobile mode
            if (body.classList.contains('products-list')) body.classList.add('slide-filters');
        },
        exit: () => {
            // Desktop mode
            if (body.classList.contains('products-list')) body.classList.remove('slide-filters');
        }
    });

    mediaCheck({
        media: productGridSlideFiltersOn,
        entry: () => {
            // Mobile mode
            if (body.classList.contains('products-grid')) body.classList.add('slide-filters');
        },
        exit: () => {
            // Desktop mode
            if (body.classList.contains('products-grid')) body.classList.remove('slide-filters');
        }
    });

});
