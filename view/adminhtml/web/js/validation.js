require([
        'jquery',
        'mage/translate',
        'jquery/validate',
        'mage/url',
    ],
    function($, translate, validate, urlBuilder) {
        // Will not work on subdomain installtions of Magento?
        var baseUrl = window.location.protocol + '//' + window.location.host + '/';

        $(document).ready(function() {
            var merchantsTimeout = null;
            var $merchantSelect = $('#payment_other_nocks_gateway_merchant');
            $merchantSelect.before('<p id="payment_other_nocks_gateway_merchants_message" style="display: none; color: red"></p>');

            if ($merchantSelect.find('option').length === 0) {
                $merchantSelect.hide();
                $('#payment_other_nocks_gateway_merchants_message')
                    .html($.mage.__('No merchants found'))
                    .show();
            }

            function getMerchants() {
                $merchantSelect.hide();
                $('#payment_other_nocks_gateway_merchants_message')
                    .html($.mage.__('Loading merchants'))
                    .show();

                clearTimeout(merchantsTimeout);
                merchantsTimeout = setTimeout(function() {
                    var url = baseUrl + urlBuilder.build('nocks/ajax/merchants');
                    var testmode = $('#payment_other_nocks_gateway_testmode').val();
                    var accessToken = $('#payment_other_nocks_gateway_access_token').val();

                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {
                            accessToken: accessToken,
                            testMode: testmode,
                        }
                    }).done(function(data) {
                        if (data.merchants.length > 0) {
                            $merchantSelect.find('option').remove().end();

                            for (var i = 0; i < data.merchants.length; i++) {
                                var merchant = data.merchants[i];
                                $merchantSelect.append('<option value="' + merchant.value + '">' + merchant.label + '</option>');
                            }

                            $merchantSelect.show();
                            $('#payment_other_nocks_gateway_merchants_message').hide();
                        } else {
                            $merchantSelect.hide();
                            $('#payment_other_nocks_gateway_merchants_message')
                                .html($.mage.__('No merchants found'))
                                .show();
                        }
                    });
                }, 200);
            }

            $('#payment_other_nocks_gateway_testmode').on('change', function() {
                getMerchants();
            });

            $('#payment_other_nocks_gateway_access_token').on('change keyup', function() {
                getMerchants();
            });
        });

        $.validator.addMethod(
            'validate-accessToken', function (v) {
                var valid = false;
                var testmode = $('#payment_other_nocks_gateway_testmode').val();

                // Will not work on subdomain installtions of Magento?
                var url = baseUrl + urlBuilder.build('nocks/ajax/validateAccessToken');

                $.ajax({
                    method: 'POST',
                    url: url,
                    async: false,
                    success: function (result) {
                        valid = result.valid;
                    },
                    data: {
                        accessToken: v,
                        testMode: testmode,
                    }
                });

                return valid;
            }, $.mage.__('Invalid access token, make sure the access token has the right scopes'));
    }
);