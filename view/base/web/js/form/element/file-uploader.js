define([
    'jquery',
    'Magento_Ui/js/form/element/file-uploader'
], function (
    $,
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'EPuzzle_FileUploader/form/element/uploader/uploader',
            uploaderConfig: {
                url: '/epuzzleFileUpload',
                dataType: 'json',
                sequentialUploads: true,
                formData: {
                    'form_key': window.FORM_KEY
                }
            },
            pushToForms: [],
            listens: {
                value: 'updateValueHandle'
            }
        },

        /**
         * Update value handle
         *
         * @param {array} value updated value
         * @return {void}
         */
        updateValueHandle(value) {
            this.pushToForms.forEach((selector) => {
                const $form = $(selector);
                const name = -1 !== this.inputName.indexOf(']')
                    ? this.inputName.substring(0, this.inputName.length - 1) + '_value]'
                    : this.inputName + '_value';
                let $input = $form.find(`input[type="hidden"][name="${name}"]`);
                if (!$input.length) {
                    $input = $(`<input type="hidden" name="${name}">`);
                    $input.prependTo($form);
                }

                $input.val(JSON.stringify(value));
            });
        }
    });
});
