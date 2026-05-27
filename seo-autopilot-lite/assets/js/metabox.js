/**
 * SEO Autopilot Lite Admin JavaScript
 */
(function($) {
    'use strict';

    // Tab functionality for meta box.
    $(document).on('click', '.sap-tab', function() {
        var tabId = $(this).data('tab');
        
        // Update active tab.
        $('.sap-tabs .sap-tab').removeClass('active');
        $(this).addClass('active');
        
        // Show tab content.
        $('.sap-tab-content').removeClass('active');
        $('#sap-' + tabId + '-tab').addClass('active');
    });

    // Live preview for SEO title and description.
    $(document).on('input', '#seo_autopilot_title, #seo_autopilot_description', function() {
        updateSnippetPreview();
    });

    function updateSnippetPreview() {
        var title = $('#seo_autopilot_title').val() || $('#seo_autopilot_title').attr('placeholder') || document.title;
        var description = $('#seo_autopilot_description').val() || '';
        
        $('.sap-snippet-title').text(title.substring(0, 60));
        $('.sap-snippet-description').text(description.substring(0, 160) || wpTrimWords(document.getElementById('content')?.value || '', 30));
    }

    function wpTrimWords(text, numWords) {
        if (!text) return '';
        var words = text.split(/\s+/).slice(0, numWords);
        return words.join(' ') + (text.split(/\s+/).length > numWords ? '...' : '');
    }

    // AI Generate buttons.
    $(document).on('click', '.sap-ai-generate', function() {
        if (!seoAutopilotLiteMetabox.aiEnabled) {
            alert('AI is not enabled. Please enable AI in settings.');
            return;
        }

        var action = $(this).data('action');
        var postId = $('#post_ID').val();
        
        $('.sap-ai-loading').show();
        $('.sap-ai-result').hide();

        $.ajax({
            url: seoAutopilotLiteMetabox.ajaxUrl,
            type: 'POST',
            data: {
                action: 'seo_autopilot_lite_generate_ai',
                nonce: seoAutopilotLiteMetabox.nonce,
                post_id: postId,
                action_type: action
            },
            success: function(response) {
                $('.sap-ai-loading').hide();
                
                if (response.success) {
                    var data = response.data;
                    
                    if (action === 'title' && data.title) {
                        $('#seo_autopilot_title').val(data.title);
                        $('.sap-ai-result').text('Title generated! Preview updated.').show();
                    } else if (action === 'description' && data.description) {
                        $('#seo_autopilot_description').val(data.description);
                        $('.sap-ai-result').text('Description generated! Preview updated.').show();
                    } else if (action === 'faq' && data.schema) {
                        $('#seo_autopilot_schema_type').val('FAQPage');
                        $('#seo_autopilot_schema_json').val(data.schema);
                        $('.sap-ai-result').text('FAQ schema generated!').show();
                    } else if (action === 'schema' && data.schema) {
                        $('#seo_autopilot_schema_json').val(data.schema);
                        $('.sap-ai-result').text('Schema generated!').show();
                    }
                    
                    updateSnippetPreview();
                } else {
                    $('.sap-ai-result').text(response.data.message || 'Failed to generate content.').show();
                }
            },
            error: function() {
                $('.sap-ai-loading').hide();
                $('.sap-ai-result').text('An error occurred. Please try again.').show();
            }
        });
    });

    // Test connection button on settings page.
    $(document).on('click', '#test-connection-btn', function() {
        var apiKey = $('#ai_api_key').val();
        var provider = $('#ai_provider').val();
        
        if (!apiKey || apiKey === '**********') {
            alert('Please enter your API key first.');
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
