// Kosher4U js config

var config = {
    map: {
        "*": {
            'main': 'js/main',
            'custon.dropdown': 'js/widgets/custom-dropdown.widget',
            'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
            'custom.accordion': 'js/widgets/custom-accordion.widget',
            'custom.select': 'js/widgets/custom-select.widget',
            'slick': "js/vendors/slick/slick.min",
            'scrollbar': "js/vendors/malihu/jquery.mCustomScrollbar.min",
            'hor.categories': "js/widgets/hor-categories.widget"
        }
    },

    paths: {
        'main': 'js/main',
        'custon.dropdown': 'js/widgets/custom-dropdown.widget',
        'custom.plusMinus': 'js/widgets/custom-plus-minus.widget',
        'custom.accordion': 'js/widgets/custom-accordion.widget',
        'custom.select': 'js/widgets/custom-select.widget',
        'slick': "js/vendors/slick/slick.min",
        'scrollbar': "js/vendors/malihu/jquery.mCustomScrollbar.min",
        'hor.categories': "js/widgets/hor-categories.widget"
    },

    shim: {
        slick: {
            deps: ['jquery']
        },
        scrollbar: {
            deps: ['jquery']
        }
    }

    // deps: [
    //     "js/main"
    // ]

};
