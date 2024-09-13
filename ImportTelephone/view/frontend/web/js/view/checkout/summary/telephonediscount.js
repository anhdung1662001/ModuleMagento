define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'mage/translate'
    ],
    function ($,Component,quote,totals,priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magenest/ImportTelephone/checkout/summary/telephonediscount'
            },
            totals: quote.getTotals(),

            /**
             *
             * @returns {*|Boolean}
             */
            isEthical: function () {
                var ethical = this.get_param('ethical');
                return ethical !== null;
            },

            get_param: function(param)
            {
                var search = window.location.search.substring(1);
                var compareKeyValuePair = function(pair) {
                    var key_value = pair.split('=');
                    var decodedKey = decodeURIComponent(key_value[0]);
                    var decodedValue = decodeURIComponent(key_value[1]);
                    if(decodedKey === param) return decodedValue;
                    return null;
                };

                var comparisonResult = null;

                if(search.indexOf('&') > -1) {
                    var params = search.split('&');
                    for(var i = 0; i < params.length; i++) {
                        comparisonResult = compareKeyValuePair(params[i]);
                        if(comparisonResult !== null) {
                            break;
                        }
                    }
                } else {
                    comparisonResult = compareKeyValuePair(search);
                }

                return comparisonResult;
            },

            isDisplayedCustomdiscountTotal : function () {
                return this.getPureValue() !== 0;
            },
            getTitle: function () {
                return $.mage.__('Discount');
            },
            getPureValue: function () {
                var price = 0;
                var totals = this.totals();
                if (totals && totals['extension_attributes']) {
                    var telephoneNumberDiscount = totals['extension_attributes']['telephone_number_discount'];
                    if (telephoneNumberDiscount) {
                        price = parseFloat(telephoneNumberDiscount);
                    }
                }
                return price;
            },

            getCustomdiscountTotal : function () {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
