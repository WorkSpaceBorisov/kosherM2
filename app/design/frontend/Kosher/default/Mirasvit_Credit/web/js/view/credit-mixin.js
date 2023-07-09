define(function () {
    'use strict';

    const mixin = {

        /**
         *
         * @param {Column} elem
         */
        initialize() {
            this._super();

            this.initMobileBlock();
        },

        /**
         * Show block on mobile and add possibility to hide block
         *
         * @returns void
         */
        initMobileBlock() {
            // Store credit for mobile

            const storeCreditBtn = document.querySelector('.link.credit');

            if (storeCreditBtn) {
                const storeCreditActive = () => {
                    setTimeout(() => {
                        storeCreditBtn.classList.add('active')
                    }, 1000)
                };

                storeCreditBtn.addEventListener('click', function (e) {
                    if(e.target === e.currentTarget) {
                        sessionStorage.setItem('hideStoreCreditBtn', true);
                        this.classList.remove('active');
                        e.preventDefault();
                    }
                });

                !sessionStorage.getItem('hideStoreCreditBtn') ? storeCreditActive() : null;
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});