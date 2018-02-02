/**
 * @module package/quiqqer/controls/bin/site/Window
 */
define('package/quiqqer/controls/bin/site/Window', [

    'qui/QUI',
    'qui/controls/windows/Popup',
    'Ajax'

], function (QUI, QUIPopup, QUIAjax) {
    "use strict";

    return new Class({

        Extends: QUIPopup,
        Type   : 'package/quiqqer/controls/bin/site/Window',

        options: {
            project  : false,
            lang     : false,
            id       : false,
            showTitle: true
        },

        Binds: [
            '$onOpen'
        ],

        initialize: function (options) {
            this.parent(options);

            this.addEvents({
                onOpen: this.$onOpen
            });

            if (this.getAttribute('showTitle')) {
                this.setAttribute('title', '&nbsp;');
            }
        },

        /**
         * event : on open
         */
        $onOpen: function () {
            var self = this;

            this.Loader.show();

            QUIAjax.get('package_quiqqer_controls_ajax_site_get', function (result) {
                if (self.getAttribute('showTitle')) {
                    self.setAttribute('title', result.title);
                }

                self.getContent().set('html', result.content);
                self.refresh();
                self.Loader.hide();
            }, {
                'package': 'quiqqer/controls',
                project  : JSON.encode({
                    name: this.getAttribute('project'),
                    lang: this.getAttribute('lang')
                }),
                id       : this.getAttribute('id')
            });
        }
    });
});