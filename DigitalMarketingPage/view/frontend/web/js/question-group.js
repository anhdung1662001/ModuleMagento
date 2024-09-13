define([
    'uiComponent',
    'jquery',
    'ko',
    'escaper',
    'mage/storage',
    'Magenest_DigitalMarketingPage/js/question-answer',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function (
    Component,
    $,
    ko,
    escaper,
    storage,
    questionAnswer,
    $t,
    modal
) {
    'use strict';

    return Component.extend({
        contentModal: ko.observable(''),
        isCompleted: ko.observable(false),
        progressBarResult: ko.observable([]),
        textResult: ko.observable(''),
        telephone: ko.observable(''),
        messageLimitNumberUsesTelephone: ko.observable(''),
        messageError: $t('Sorry, something went wrong. Please try again later.'),
        showErrorComplete: ko.observable(false),
        showResult: ko.observable(false),
        showErrorLimitNumberUsesTelephone: ko.observable(false),
        showButtonStart: ko.observable(true),
        showRegisterForm: ko.observable(false),
        showQuestionAnswersForm: ko.observable(false),
        isRegisterFormLoading: ko.observable(false),
        isQuestionAnswersFormLoading: ko.observable(false),
        defaults: {
            template: 'Magenest_DigitalMarketingPage/question-group',
            allowedTags: [],
            questionAnswerElement: []
        },
        initialize: function () {
            this._super();
            this.initElement();
        },

        initElement: function () {
            function createElement(ElementType, properties) {
                return new ElementType(properties);
            }

            for (let value of this.getQuestions()) {
                this.questionAnswerElement.push(createElement(questionAnswer, {data: value}));
            }
        },
        openModal: function () {
            let options = {
                type: 'popup',
                responsive: true,
                buttons: [],
                innerScroll: false,
                modalClass: 'popup-model-flow-button'
            };
            let popup = $('<div class="popup-model" id="popup-model-flow-button"></div>').html(this.contentModal()).appendTo('body');
            modal(options, popup);
            popup.modal('openModal');
        },
        getTitle: function () {
            return escaper.escapeHtml(this.data.title || this.data.question_group, this.allowedTags);
        },
        getQuestions: function () {
            return this.getRandomQuestions(this.data.questions, this.data.limit)
        },
        getRandomQuestions: function (arr, limit) {
            let result = [];
            let usedIndices = new Set();

            limit = Math.min(limit, arr.length);

            let index = 1;
            while (result.length < limit) {
                let randomIndex = Math.floor(Math.random() * arr.length);
                if (!usedIndices.has(randomIndex)) {
                    result.push(arr[randomIndex]);
                    usedIndices.add(randomIndex);
                    arr[randomIndex]['index'] = index;
                    index ++;
                }
            }
            return result;
        },
        start: function () {
            this.showRegisterForm(true);
            this.showButtonStart(false);
        },
        register: function (form) {
            if ($(form).validation() &&
                $(form).validation('isValid') &&
                this.telephone()
            ) {
                let self = this;
                self.resetErrorLimitNumberUsesTelephone();
                self.isRegisterFormLoading(true);
                storage.get(`rest/V1/mgn-dmp/question-group/check-limit-number-uses-telephone/${this.telephone()}`)
                    .done(function (response) {
                        let res = JSON.parse(response);
                        if (res.status) {
                            self.showRegisterForm(false);
                            self.showQuestionAnswersForm(true);
                        } else {
                            self.showErrorLimitNumberUsesTelephone(true);
                            self.messageLimitNumberUsesTelephone(res.message);
                        }
                    }).fail(function (jqXHR, textStatus, err) {
                        self.showErrorLimitNumberUsesTelephone(true);
                        self.messageLimitNumberUsesTelephone(self.messageError);
                    }).always(function () {
                        self.isRegisterFormLoading(false);
                    });
            }
        },
        resetErrorLimitNumberUsesTelephone: function () {
            this.showErrorLimitNumberUsesTelephone(false);
            this.messageLimitNumberUsesTelephone('');
        },

        complete: function () {
            if (this.isCompleted()) {
                this.openModal();
                return;
            }
            let flat = true;
            for (let child of this.questionAnswerElement) {
                let result = child.validate();
                if (flat && !result) {
                    flat = false;
                }
            }
            if (flat) {
                this.isQuestionAnswersFormLoading(true);
                let questionAnswers = [];
                let totalTrueAnswers = 0;
                for (let child of this.questionAnswerElement) {
                    let postData = child.getPostData();
                    if (postData.curren_answer_is_true_answer) {
                        totalTrueAnswers++;
                    }
                    questionAnswers.push(postData);
                }
                let payload = {
                    'telephone': this.telephone(),
                    'question_group_id': this.data.question_group_id,
                    'question_group': this.data.question_group,
                    'question_answers': questionAnswers
                };
                let self = this;
                storage.post(`rest/V1/mgn-dmp/question-group/complete`, JSON.stringify({data: JSON.stringify(payload)}))
                    .done(function (response) {
                        let res = JSON.parse(response);
                        if (res.status) {
                            self.showErrorComplete(false);
                            for (let child of self.questionAnswerElement) {
                                child.toggleComplete();
                            }
                            self.showResult(true);
                            self.textResult('<span class="current">' + totalTrueAnswers + '</span>' + '/' + '<span class="total">' + self.data.limit + '</span>');
                            let progressBarResult = [];
                            for (let i = 1; i <= self.data.limit; i++) {
                                progressBarResult.push({'current': i <= totalTrueAnswers});
                            }
                            self.progressBarResult(progressBarResult);
                            self.isCompleted(true);
                            $('.question-answers-form fieldset').css('pointer-events', 'none');

                            if (totalTrueAnswers >= self.data.number_true_answers_to_receive_reward) {
                                self.contentModal(self.data.content_popup_success)
                            } else {
                                self.contentModal(self.data.content_popup_failure)
                            }
                            self.openModal();
                        } else {
                            self.showErrorComplete(true);
                        }
                    }).fail(function (jqXHR, textStatus, err) {
                        self.showErrorComplete(true);
                    }).always(function () {
                        self.isQuestionAnswersFormLoading(false);
                    });
            }
        }
    });
});
