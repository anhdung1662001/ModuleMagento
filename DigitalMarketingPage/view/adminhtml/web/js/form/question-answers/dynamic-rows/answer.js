define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'Magenest_DigitalMarketingPage/js/question-type',
    'uiRegistry',
], function (_, dynamicRows, questionType, registry) {
    'use strict';

    return dynamicRows.extend({
        initialize: function () {
            this._super();
            let typeQuestionUi = registry.get(this.parentName + '.question_type');
            if (typeQuestionUi && typeQuestionUi.value() == questionType.fillInTheAnswer) {
                this.disabled(true);
            }
        }
    });
});
