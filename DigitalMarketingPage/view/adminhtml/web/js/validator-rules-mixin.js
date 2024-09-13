define([
    'jquery',
    'uiRegistry',
    'underscore',
    'Magenest_DigitalMarketingPage/js/question-type'
], function ($, registry, _, questionType) {
    'use strict';

    return function (target) {
        let mixin = _.mapObject({
            'validate-true-answer': [
                function (value, params, additionalParams) {
                    if (additionalParams && additionalParams.source.get('params.invalid') !== undefined) {
                        if (additionalParams.value() != questionType.fillInTheAnswer) {
                            let answersUi = registry.get(additionalParams.parentName + '.answers');
                            if (answersUi && answersUi.getChildItems()) {
                                return  answersUi.getChildItems().some(item => item.true_answer === '1');
                            }
                        }
                    }
                    return true;
                },
                $.mage.__('Please choose at least 1 true answer')
            ]
        }, function (data) {
            return {
                handler: data[0],
                message: data[1]
            };
        });
        return {...target, ...mixin};
    };
});
