define([
    'jquery',
    'domReady!'
], function ($) {

    'use strict';

    // console.log('Custom select widget by Max');

    $.widget('custom.select', {
        options: {
            action: 'change', // Set to submit to submit form on select option
            autoHide: 1, // Set to 0 to prevent dropdown autohide
        },

        _create: function () {
            $(this.element).find('option:selected').attr('selected', 'selected');
            this._build();
            this._disabled();
            console.log('autoHide', this.options.autoHide);
        },

        _build: function () {
            let self = this;
            let elem = $(this.element);
            let title = elem.find('option:selected').text();
            let options = elem.find('option');
            let wrapper = elem.parent();
            let firstSelectedOption = elem.find('option:selected').index();

            wrapper.addClass('custom-select-wrapper');

            // Build title

            let newTitle = document.createElement('div');
            newTitle.classList.add('custom-select-title');
            newTitle.innerHTML = title.trim();

            if (elem.prop('disabled')) newTitle.classList.add('disabled');

            wrapper.append(newTitle)

            // Build options list

            let list = document.createElement('ul');
            list.classList.add('custom-select-list')

            options.each((i, item) => {
                let option = document.createElement('li');
                option.innerText = item.innerText.trim();
                if (i == firstSelectedOption) {
                    option.classList.add('hidden-option');
                }
                list.append(option);
            });

            wrapper.append(list);

            // Show list on title click

            $(newTitle).on('click', function () {

                // Autohide other
                if (self.options.autoHide === 1 && !$(this).hasClass('active')) {
                    $('.custom-select-wrapper').find('.show-options-list').removeClass('show-options-list');
                    $('.custom-select-wrapper').find('.active').removeClass('active');
                }

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active').next('.custom-select-list').removeClass('show-options-list')
                    return;
                }

                $(this).addClass('active').next('.custom-select-list').addClass('show-options-list');

            });

            let builtOptions = elem.parent().find('.custom-select-list li');

            builtOptions.on('click', function (e) {
                let index = $(this).index()
                newTitle.innerText = $(this).text();

                builtOptions.removeAttr('class');
                $(this).addClass('hidden-option');

                options.removeAttr('selected', 'selected');
                $(options[index]).attr('selected', 'selected');
                $(this).closest('ul').removeClass('show-options-list');
                $(this).closest('.custom-select-wrapper').find('.active').removeClass('active');

                // Change event for select
                elem.change();

                if (self.options.action === 'submit') {
                    console.log('Submit form');
                    $(this).closest('form').submit();
                }

            });

            // Close popup if click outside and not title

            if (self.options.autoHide === 1) {
                console.log('Autoclose = 1');
                $('body').off().on('click', function (e) {
                    let popup = $('.custom-select-list');
                    if (!e.target.closest('.custom-select-wrapper') && popup.hasClass('show-options-list')) {
                        popup.removeClass('show-options-list').prev('.custom-select-title').removeClass('active');
                    }
                });
            }

            // Select stage check for disabled select

            let checkStage = function () {
                const config = {
                    attributes: true,
                };

                const callback = function (mutationsList, observer) {
                    for (let mutation of mutationsList) {
                        if (mutation.type === 'attributes') {
                            console.log(target);
                            if (mutation.target.disabled) {
                                mutation.target.parentElement.querySelector('.custom-select-title').classList.add('disabled');
                                mutation.target.parentElement.querySelector('.custom-select-list').classList.remove('show-options-list');
                                return;
                            }
                            mutation.target.parentElement.querySelector('.custom-select-title').classList.remove('disabled')
                        }
                    }
                };

                const observer = new MutationObserver(callback);

                // Todo solve with no name atrribute case

                var name;

                try {
                    name = elem.attr('name');
                    observer.observe(document.getElementsByName(name)[0], config);
                } catch (e) {
                    name = elem.attr('id');
                    observer.observe(document.getElementById(name)[0], config);
                }

            }

            // Call check if select disabled
            checkStage();
        },


        // Debug disabled

        _disabled: function () {
            let btn = $('button.check-stage');

            btn.off().on('click', function (e) {
                e.preventDefault();
                let getID = $(this).data('target');
                let select = $(`select[name=${getID}]`);

                if (select.prop('disabled')) {
                    select.removeAttr('disabled')
                    return;
                }

                select.prop('disabled', true);
            })
        }

    });

    return $.custom.select;
});
