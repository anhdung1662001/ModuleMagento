define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/element/single-checkbox',
    'Magenest_DigitalMarketingPage/js/question-type'
], function (registry, _, checkbox, questionType) {
    'use strict';

    return checkbox.extend({
        validate: function () {
            this.validationParams = this.getTypeQuestionUi();
            return this._super();
        },
        onCheckedChanged: function (newChecked) {
            this._super();
            if (this.focused()) {
                let typeQuestionUi = this.getTypeQuestionUi();
                if (typeQuestionUi && typeQuestionUi.value() == questionType.chooseOneAnswer) {
                    let answersUi = registry.get(this.containerQuestionString + '.answers');
                    if (answersUi) {
                        let countChild = answersUi.getRecordCount();
                        if (countChild) {
                            let parts = this.parentName.split('.'),
                                index = Number(parts[parts.length - 1]);
                            if (!isNaN(index)) {
                                for (let i = 0; i <= countChild - 1; i++) {
                                    if (index != i) {
                                        let trueAnswersUi = registry.get(answersUi.name + '.' + i + '.field_true_answer');
                                        if (trueAnswersUi) {
                                            trueAnswersUi.checked(false);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        getTypeQuestionUi: function () {
            let parentName = this.parentName;
            if (parentName) {
                let startIndex = parentName.indexOf("container_question");
                this.containerQuestionString = parentName.substring(0, startIndex + "container_question".length);
                return registry.get(this.containerQuestionString + '.question_type');
            }
        }
    });
});
