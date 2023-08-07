define(['jquery', 'domReady!'], function ($) {
    'use strict';

    const Navigation = function (selector) {
        return {
            element: $(selector),

            init: function () {
                if (!this.element.length) {
                    return false;
                }

                this._bindClick();
                this._initNavigation();
                this._addViewAll();
            },

            _bindClick() {
                const self = this;

                $('[data-action="toggle-nav"]').on('click', function () {
                    $('html').toggleClass('nav-opened');
                    $('.page-header [data-action="navigation"]').slideToggle(300, 'swing');
                    self.element
                        .find('.expanded')
                        .removeClass('expanded')
                        .find('.submenu')
                        .slideUp(300);
                });

                $(document).mouseup(function (e) {
                    const container = $('.page-header [data-action="navigation"]');
                    if (!container.is(e.target) &&
                        container.has(e.target).length === 0 && $('html').hasClass('nav-opened') && $(e.target).attr("data-action") !== 'toggle-nav') {
                        $('[data-action="toggle-nav"]').trigger('click')
                    }
                });
            },

            _initNavigation() {
                const self = this;
                $('.level-top:has(a)', self.element).off('mouseenter mouseleave click');
                $('.level-top li:has(a)', self.element).off('click');
                $('.level1.subitem:has(a)', self.element).off('click');
                $(document).off('click.navigation');

                self.element.find('.submenu').slideUp(300);

                $('li.parent > a', self.element).on('click', function (evt) {
                    const target = $(evt.target),
                        parent = target.closest('.parent');

                    if (!parent.hasClass('expanded')) {
                        evt.preventDefault();
                        $(parent)
                            .siblings('.parent')
                            .removeClass('expanded')
                            .find('.submenu')
                            .slideUp(300).find('.parent').removeClass('expanded');
                        $(parent).addClass('expanded');
                        $(parent).children('.submenu').slideToggle(300);
                    }
                });
            },

            _addViewAll() {
                const subMenus = this.element.find('li.level1.parent');

                $.each(subMenus, $.proxy(function (index, item) {
                    let categoryParent;

                    const category = $(item).find('> a span').not('.ui-menu-icon').text(),
                        categoryUrl = $(item).find('> a').attr('href'),
                        menu = $(item).find('.submenu');

                    this.categoryLink = $('<a>')
                        .attr('href', categoryUrl)
                        .text(`All ${category}`);

                    categoryParent = $('<li>')
                        .addClass('ui-menu-item all-category')
                        .html(this.categoryLink);

                    if (menu.find('.all-category').length === 0) {
                        menu.prepend(categoryParent);
                    }

                }, this));
            }
        };
    };

    const navigation = new Navigation('.page-header [data-init="navigation"]');

    navigation.init();

    return navigation;
});
