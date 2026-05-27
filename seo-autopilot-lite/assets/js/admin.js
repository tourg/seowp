/**
 * SEO Autopilot Lite Admin JavaScript (Settings Page)
 */
(function($) {
    'use strict';

    // Test connection button on settings page.
    $(document).on('click', '#test-connection-btn', function() {
        var apiKey = $('#ai_api_key').val();
        var provider = $('#ai_provider').val();
        
        if (!apiKey || apiKey === '**********') {
            alert(seoAutopilotLiteAdmin.strings.apiKeyRequired || 'Please enter your API key first.');
            return;
        }

        $('#test-connection-result').text(seoAutopilotLiteAdmin.strings.testConnection);

        $.ajax({
            url: seoAutopilotLiteAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'seo_autopilot_lite_test_connection',
                nonce: seoAutopilotLiteAdmin.nonce,
                api_key: apiKey,
                provider: provider
            },
            success: function(response) {
                if (response.success) {
                    $('#test-connection-result')
                        .removeClass('error')
                        .addClass('success')
                        .text(response.data.message);
                } else {
                    $('#test-connection-result')
                        .removeClass('success')
                        .addClass('error')
                        .text(response.data.message);
                }
            },
            error: function() {
                $('#test-connection-result')
                    .removeClass('success')
                    .addClass('error')
                    .text(seoAutopilotLiteAdmin.strings.connectionFailed);
            }
        });
    });

})(jQuery);
