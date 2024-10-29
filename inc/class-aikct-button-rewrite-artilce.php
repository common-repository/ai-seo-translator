<?php 
defined('ABSPATH') or die();
class aikct_button_rewrite_url {

   

    // Render the button and modal
    public function render() {
        ?>


        <button class="aikct_rewrite_url" title="AI Rewrite content from a given URL">
            <i class="fa-solid fa-pen"></i> Rewrite
        </button>

        <!-- Popup Modal -->
        <div id="rewritePopup" class="rewrite-popup-overlay" style="display:none;">
            <div class="rewrite-popup-content">
                <span class="close-popup">&times;</span>
                <h3>Enter URL to Rewrite</h3>
                <input type="text" class="aikct_input" id="rewriteUrl" placeholder="Enter URL to Rewrite" />
                
                <button id="scanAndRewrite"><i class="fa-regular fa-keyboard"></i> Rewrite Content</button>
            </div>
        </div>

        <style>
            /* Popup Overlay Styles */
            .rewrite-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 999999999999999;
            }

            /* Popup Content Styles */
            .rewrite-popup-content {
                background: white;
                padding: 20px;
                border-radius: 5px;
                width: 400px;
                position: relative;
            }

            /* Close button */
            .close-popup {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 24px;
                cursor: pointer;
            }

            /* Styling buttons and inputs */
            #rewriteUrl {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            #scanAndRewrite {
                background-color: #28a745;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                margin-top: 10px;
            }

            #scanAndRewrite:hover {
                background-color: #218838;
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function($) {

                // Open popup on button click
                $('.aikct_rewrite_url').on('click', function(e) {
                    e.preventDefault();
                    $('#rewritePopup').fadeIn();
                });

                // Close popup on close button click
                $('.close-popup').on('click', function(e) {
                    e.preventDefault();
                    $('#rewritePopup').fadeOut();
                });

                // Scan and rewrite URL content
                $('#scanAndRewrite').on('click', function(e) {
                    e.preventDefault();
                    var rewriteUrl = $('#rewriteUrl').val();
                    if (rewriteUrl === '') {
                        aikct_mess_error('Please enter a valid URL','');
                        return;
                    }

                    $('#loader').show();

                    $.ajax({
                        url: ajaxurl, // URL to WordPress admin-ajax.php
                        type: 'POST',
                        data: {
                            action: 'scan_and_rewrite_url',
                            rewrite_url: rewriteUrl
                        },
                        success: function(response) {
                            if (response.success) {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(response.data.rewritten_content, "text/html");

                                 const reader = new Readability(doc);
                                const article = reader.parse();
                                const tempDiv = document.createElement("div");
                                tempDiv.innerHTML = article.content;
                                const plainText = tempDiv.textContent || tempDiv.innerText || "";

                                console.log(plainText);
                                var promptstring = 'The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\"). Assume the role of an audience-focused content writer. Your task is to adapt the provided [====== '+plainText+' ======] to resonate with the specified [ordinary reader]. Understand the preferences, language style, cultural nuances, and interests of this audience to make the content more relatable for them. Adjust the tone, examples, references, and vocabulary to match the audience\'s level of understanding and expectations. Incorporate relevant anecdotes, metaphors, or analogies that the audience can connect with. Ensure the core message and factual accuracy remain intact while making the content more appealing and accessible to the targeted group.Rewrite the following content by completely altering the sentence structure and replacing any mention of the website with <?php echo get_home_url(); ?> . The provided context is: { }. Ensure that the rewritten text maintains the original meaning while offering a fresh perspective. The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\").';
                                $('#rewritePopup').fadeOut();
                                $('#my_prompt_result').append('<div class="message-box right">Content rewritten successfully</div>');
                                $('#my_prompt_result').addClass('has-content');
                                $('#ask_ai').val(promptstring);
                                $('#send-btn').click();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    });
                });
            });
        </script>
        <?php
    }

   
}

add_action('wp_ajax_scan_and_rewrite_url', 'scan_and_rewrite_url_callback');
function scan_and_rewrite_url_callback() {
    
    $rewrite_url = sanitize_text_field($_POST['rewrite_url']);

    if (empty($rewrite_url) || !filter_var($rewrite_url, FILTER_VALIDATE_URL)) {
        wp_send_json_error(['message' => 'Invalid URL']);
    }
    $response = wp_remote_get($rewrite_url);
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Failed to fetch the URL content']);
    }

    $body = wp_remote_retrieve_body($response);

    $rewritten_content = your_ai_rewrite_function($body); 

    if ($rewritten_content) {
        wp_send_json_success(['rewritten_content' => $rewritten_content]);
    } else {
        wp_send_json_error(['message' => 'Failed to rewrite content']);
    }
}


function your_ai_rewrite_function($content) {
   
    return '' . $content; 
}
