const config = {
    map: {
        '*': {
            'main': 'js/main',
            'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
            'custom.select': 'js/widgets/custom-select.widget',
            'slick': 'js/vendors/slick/slick.min'
        }
    },

    paths: {
        'main': 'js/main',
        'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
        'custom.select': 'js/widgets/custom-select.widget',
        'slick': 'js/vendors/slick/slick.min',
    },

    shim: {
        slick: {
            deps: ['jquery']
        }
    },

    deps: [
        'js/widgets/custom-navigation'
    ]
};
