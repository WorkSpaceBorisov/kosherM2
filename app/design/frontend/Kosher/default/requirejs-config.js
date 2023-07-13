const config = {
    map: {
        '*': {
            'main': 'js/main',
            'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
            'custom.select': 'js/widgets/custom-select.widget',
            'slick': 'js/vendors/slick/slick.min',
            'mousewheel': 'js/vendors/malihu/jquery.mousewheel.min',
            'scrollbar': 'js/vendors/malihu/jquery.mCustomScrollbar.min',
            'home.categories': 'js/widgets/home-categories.widget'
        }
    },

    paths: {
        'main': 'js/main',
        'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
        'custom.select': 'js/widgets/custom-select.widget',
        'slick': 'js/vendors/slick/slick.min',
        'mousewheel': 'js/vendors/malihu/jquery.mousewheel.min',
        'scrollbar': 'js/vendors/malihu/jquery.mCustomScrollbar.min',
        'home.categories': 'js/widgets/home-categories.widget'
    },

    shim: {
        slick: {
            deps: ['jquery']
        },
        scrollbar: {
            deps: ['jquery']
        }
    },

    deps: [
        'js/widgets/custom-navigation'
    ]
};
