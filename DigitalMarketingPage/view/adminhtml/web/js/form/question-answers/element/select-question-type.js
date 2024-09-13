define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/element/select',
    'Magenest_DigitalMarketingPage/js/question-type'
], function (registry, _, select, questionType) {
    'use strict';

    return select.extend({
        setDifferedFromDefault: function () {
            this._super();
            let answersUi = registry.get(this.parentName + '.answers')
            if (answersUi) {
                answersUi.disabled(this.value() == questionType.fillInTheAnswer);
                let countChild = answersUi.getRecordCount();
                if (countChild) {
                    for (let i = 0; i <= countChild - 1; i++) {
                        let trueAnswersUi = registry.get(answersUi.name + '.' + i + '.field_true_answer');
                        if (trueAnswersUi) {
                            trueAnswersUi.checked(false);
                        }
                    }
                }
            }
        }
    });
});
