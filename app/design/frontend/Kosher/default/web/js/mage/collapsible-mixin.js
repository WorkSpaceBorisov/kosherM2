define([
    'jquery',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/core',
    'jquery/jquery-storageapi',
    "mage/mage"
], function($){
    "use strict";

    // need to prevent page jumping on collapsible opening
    const collapsibleWidgetMixin = {

        /**
         * @private
         */
        _create: function () {
            this.storage = $.localStorage;
            this.icons = false;

            if (typeof this.options.icons === 'string') {
                this.options.icons = JSON.parse(this.options.icons);
            }

            this._processPanels();
            this._processState();
            this._refresh();

            this._bind('click');
            this._trigger('created');
        },
    }

    return function (collapsible) {
        $.widget("mage.collapsible", collapsible, collapsibleWidgetMixin);

        return $.mage.collapsible
    }
});