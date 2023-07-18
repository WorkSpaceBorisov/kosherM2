const config = {
    map: {
        '*': {
            'main': 'js/main',
            'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
            'custom.select': 'js/widgets/custom-select.widget',
            'slider': 'js/widgets/slider'
        }
    },

    paths: {
        'main': 'js/main',
        'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
        'custom.select': 'js/widgets/custom-select.widget',
        'slick': 'js/vendors/slick/slick.min',
    },

    shim: {
        'slick': ['jquery']
    },

    config: {
        mixins: {
            'mage/collapsible': {
                'js/mage/collapsible-mixin': true
            }
        }
    },

    deps: [
        'js/widgets/custom-navigation'
    ]
};
