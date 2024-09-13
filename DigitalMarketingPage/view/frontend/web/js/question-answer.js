define([
    'uiComponent',
    'jquery',
    'ko',
    'escaper',
    'Magenest_DigitalMarketingPage/js/question-type',
    'mage/translate'
], function (
    Component,
    $,
    ko,
    escaper,
    questionType,
    $t
) {
    'use strict';

    return Component.extend({
        messageError: $t('Please choose at least 1 true answer.'),
        defaults: {
            template: 'Magenest_DigitalMarketingPage/question-answer',
            allowedTags: [],
        },
        initialize: function () {
            this._super();
            this.currentAnswers = [];
            this.showError = ko.observable(false);
        },
        getBody: function () {
            return this.template;
        },
        getQuestionEscaper: function () {
            return escaper.escapeHtml(this.data.question, this.allowedTags);
        },
        getAnswerEscaper: function (answer) {
            return escaper.escapeHtml(answer, this.allowedTags);
        },
        getIndex: function () {
            return this.data.index;
        },
        getAnswers: function () {
            return this.data.answers;
        },
        isMultiple: function () {
            return this.data.question_type == questionType.chooseMultipleAnswers
        },
        choice: function (data, element) {
            if (this.isMultiple()) {
                if ($(element.target)[0].checked) {
                    $(element.target).closest('.multiple-choice').addClass('checked');
                    this.currentAnswers.push($(element.target).val())
                } else {
                    $(element.target).closest('.multiple-choice').removeClass('checked');
                    this.currentAnswers = this.currentAnswers.filter(item => item != $(element.target).val());
                }
            } else {
                $(element.target).closest('.answers').find('.single-choice').removeClass('checked')
                $(element.target).closest('.single-choice').addClass('checked');
                this.currentAnswers = [$(element.target).val()];
            }
            return true;
        },
        validate: function () {
            if (Array.isArray(this.currentAnswers) && this.currentAnswers.length === 0) {
                this.showError(true);
                return false;
            }
            this.showError(false);
            return true;
        },
        getPostData: function () {
            const trueAnswers = this.getAnswers()
                .filter(answer => answer.true_answer === '1')
                .map(answer => answer.answer);
            const currentAnswers = this.getAnswers()
                .filter(answer => this.currentAnswers.includes(answer.answer_id))
                .map(answer => answer.answer);
            return {
                'question': this.data.question,
                'answers': {
                    'true_answers': trueAnswers,
                    'current_answers': currentAnswers
                },
                'curren_answer_is_true_answer': this.checkCurrenAnswerIsTrueAnswer(trueAnswers, currentAnswers)
            };
        },
        checkCurrenAnswerIsTrueAnswer: function (trueAnswers, currentAnswers) {
            if (trueAnswers.length !== currentAnswers.length) {
                return false;
            }
            const trueAnswersSorted = trueAnswers.slice().sort();
            const currentAnswersSorted = currentAnswers.slice().sort();

            for (let i = 0; i < trueAnswersSorted.length; i++) {
                if (trueAnswersSorted[i] !== currentAnswersSorted[i]) {
                    return false;
                }
            }
            return true;
        },
        toggleComplete: function () {
            const trueAnswerIds = this.getAnswers()
                .filter(answer => answer.true_answer === '1')
                .map(answer => answer.answer_id);
            const wrongAnswerIds = this.currentAnswers.filter(value => !trueAnswerIds.includes(value));
            for (let id of trueAnswerIds) {
                $("[data-answer='" + id + "']").addClass('true');
            }
            for (let id of wrongAnswerIds) {
                $("[data-answer='" + id + "']").addClass('wrong');
            }

        }
    });
});
