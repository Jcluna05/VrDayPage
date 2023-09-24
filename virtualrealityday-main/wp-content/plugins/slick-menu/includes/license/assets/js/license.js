;( function( $ ) {

    'use strict';

    var XT_LICENSE = {};

    // LICENSE FUNCTIONS ----------------------------------------------------------

    XT_LICENSE = {

        init: function() {

            var self = this;
            var initLicenseFormEvent = function() {

                self.initLicenseForm($(this));
            };

            $('.xt-license-activation').each(initLicenseFormEvent);
        },

        initLicenseForm: function(wrapper) {

            var product_id = $(wrapper).data('id');
            var homeurl = $(wrapper).data('homeurl');
            var domain = $(wrapper).data('domain');
            var ajaxurl = $(wrapper).data('ajaxurl');
            var forms = $(wrapper).find('.xt-license-form');
            var activationForm = $(wrapper).find('#xt-license-activation-form');
            var revokeForm = $(wrapper).find('#xt-license-revoke-form');
            var localRevokeForm = $(wrapper).find('#xt-license-local-revoke-form');
            var licenseFound = $(wrapper).find('#xt-license-activated');
            var licenseActivated = $(licenseFound).find('.xt-license-status-active');
            var licenseExpired = $(licenseFound).find('.xt-license-status-expired');
            var licenseInvalid = $(wrapper).find('#xt-license-invalid');
            var licenseInfo = $(wrapper).find('.xt-license-info');
            var revokeInfo = $(wrapper).find('.xt-license-revoke-info');
            var invalidTimer = $(wrapper).find('.xt-license-timer');

            forms.each(function() {

                var form = $(this);

                form.find('input[name="purchase_code"]').on('blur', function() {
                    $(this).val($(this).val().trim());
                });

                form.find('input[type="submit"]').on('click', function(e) {

                    e.preventDefault();

                    var button = $(this);
                    var defaultButtonValue = button.val();
                    button.val('Processing...');
                    button.attr('disabled', true);

                    var action = form.find('input[name="action"]').val();

                    if(action === prefix('activation')) {

                        var host = domain !== '' ? domain : location.host.replace('www.', '');
                        var installation_url = homeurl;
                        form.find('input[name="domain"]').val(host);
                        form.find('input[name="installation_url"]').val(installation_url);
                    }

                    var postData = form.find(':input').serialize();

                    $.ajax({
                        cache		: false,
                        url         : ajaxurl,
                        type        : 'POST',
                        data        : postData,
                        dataType    : 'json',
                    })
                    // using the done promise callback
                        .done(function(data) {

                            licenseResponse(data);
                            button.val(defaultButtonValue);
                            button.removeAttr('disabled');
                        })
                        .fail(function(data) {

                            alert('Something went wrong! Please try again later!');
                            button.val(defaultButtonValue);
                            button.removeAttr('disabled');
                        });
                });
            });

            revokeForm.find('.xt-license-revoke-cancel').on('click', function(e) {

                e.preventDefault();

                showActivationForm();
            });

            function prefix(key) {

                return 'xt_license_'+product_id+'_'+key;
            }

            function licenseResponse(data) {

                if(data) {

                    if (data.code === "valid" || data.code === "expired") {

                        onActivation(data);

                    } else if (data.code === "exists") {

                        showRevokeForm(data);

                    } else if (data.code === "revoked") {

                        onRevoked(data);

                    } else {

                        onInvalid(data);
                    }

                }else{

                    onInvalid(data);
                }
            }

            function showRevokeForm(data) {

                activationForm.hide();
                licenseFound.hide();
                revokeInfo.hide();
                licenseInvalid.hide();
                localRevokeForm.hide();
                revokeForm.fadeIn();

                revokeForm.find('input[name="purchase_code"]').val(data.license.purchase_code);
                revokeForm.find('input[name="domain"]').val(data.license.domain);

                licenseInfo.html(data.license_summary).fadeIn();
            }

            function onRevoked(data) {

                showActivationForm();
                licenseInfo.empty().hide();
                revokeInfo.html(data.msg);
                revokeInfo.fadeIn();
                localRevokeForm.hide();
            }

            function showActivationForm() {

                licenseInfo.empty().hide();
                revokeForm.hide();
                localRevokeForm.hide();
                licenseFound.hide();
                revokeInfo.hide();
                licenseInvalid.hide();
                activationForm.fadeIn();
            }

            function onActivation(data) {

                revokeForm.hide();
                activationForm.hide();
                revokeInfo.hide();
                licenseInvalid.hide();
                licenseFound.fadeIn();

                if(!data.license.expired) {
                    licenseExpired.hide();
                    licenseActivated.fadeIn();
                }else{
                    licenseActivated.hide();
                    licenseExpired.fadeIn();
                }

                localRevokeForm.fadeIn();

                licenseInfo.html(data.license_summary).fadeIn();

                localRevokeForm.find('[name="purchase_code"]').val(data.license.purchase_code);
                localRevokeForm.find('[name="domain"]').val(data.license.domain);

                $( document ).trigger( "xt-license-activated", [ data.license, data.license_summary, licenseInfo ] );
            }

            function onInvalid() {

                revokeForm.hide();
                activationForm.hide();
                revokeInfo.hide();
                localRevokeForm.hide();
                licenseFound.hide();
                licenseInvalid.fadeIn();

                var count = 3;
                invalidTimer.html(count);
                count--;
                var timer = setInterval(function() {

                    if(count === 0) {

                        invalidTimer.empty();
                        clearInterval(timer);
                        showActivationForm();

                    }else{

                        invalidTimer.html(count);
                        count--;
                    }

                }, 1000);
            }
        }
    };


    $(document).ready(function() {

        XT_LICENSE.init();
    });

})( jQuery );