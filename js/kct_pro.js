tinymce.PluginManager.add('aikct_ask_pro', function(editor, url) {
    var summarizeButton;
    var improve_content_btn,Simplify_content_btn,expand_content_btn,trim_content_btn;
    editor.addButton('aikct_ask_pro', {
        type: 'menubutton',
        text: 'KCT Pro',
        id:'btne',
        icon: 'aikct_mce-icon_shortcode',
         menu: [ // Thêm menu sổ xuống
            {
                text: '📖 Summarize content',
                id: 'summarize_content_btn',
                disabled: true,
                onclick: function() {
                    var selectedText = editor.selection.getContent({format: 'text'});
                    if (selectedText) {

                         var kctaiDiv = document.getElementById('kctai');
                         kctaiDiv.style.display = 'block'; 
                       

                        var prompt = 'Question: Provide the key points and concepts in this content in a succinct summary. Context: '+ selectedText + '. Output reponse the same langues in Context';
                        var askAiInput = document.getElementById('ask_ai');
                        askAiInput.value = prompt;
                        var sendBtn = document.getElementById('send-btn');
                        sendBtn.disabled = false;
                        sendBtn.click();
                        askAiInput.value = '';
                    }
                },
                onPostRender: function() {
                     summarizeButton = this;
                    editor.on('NodeChange', function(e) {
                        // Disable the button if no text is selected
                        summarizeButton.disabled(editor.selection.isCollapsed());
                    });
                }
            },
            {
                text: '⚡️Improve writing',
                id: 'improve_content_btn',
                disabled: true,
                onclick: function() {
                    var selectedText = editor.selection.getContent({format: 'text'});
                    if (selectedText) {

                         var kctaiDiv = document.getElementById('kctai');
                         kctaiDiv.style.display = 'block'; 
                       

                        var prompt = 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning. Context: '+ selectedText + '. Output reponse the same langues in Context';
                        var askAiInput = document.getElementById('ask_ai');
                        askAiInput.value = prompt;
                        var sendBtn = document.getElementById('send-btn');
                        sendBtn.disabled = false;
                        sendBtn.click();
                        askAiInput.value = '';
                    }
                },
                onPostRender: function() {
                     improve_content_btn = this;
                    editor.on('NodeChange', function(e) {
                        // Disable the button if no text is selected
                        improve_content_btn.disabled(editor.selection.isCollapsed());
                    });
                }
            },
            {
                text: '⚡️ Simplify language',
                id: 'Simplify_content_btn',
                disabled: true,
                onclick: function() {
                    var selectedText = editor.selection.getContent({format: 'text'});
                    if (selectedText) {

                         var kctaiDiv = document.getElementById('kctai');
                         kctaiDiv.style.display = 'block'; 
                       

                        var prompt = 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning. Rewrite this content with simplified language and reduce the complexity of the writing, so that the content is easier to understand. Context: '+ selectedText + '. Output reponse the same langues in Context';
                        var askAiInput = document.getElementById('ask_ai');
                        askAiInput.value = prompt;
                        var sendBtn = document.getElementById('send-btn');
                        sendBtn.disabled = false;
                        sendBtn.click();
                        askAiInput.value = '';
                    }
                },
                onPostRender: function() {
                     Simplify_content_btn = this;
                    editor.on('NodeChange', function(e) {
                        // Disable the button if no text is selected
                        Simplify_content_btn.disabled(editor.selection.isCollapsed());
                    });
                }
            },
            {
                text: '⚡️ Expand upon',
                id: 'expand_content_btn',
                disabled: true,
                onclick: function() {
                    var selectedText = editor.selection.getContent({format: 'text'});
                    if (selectedText) {

                         var kctaiDiv = document.getElementById('kctai');
                         kctaiDiv.style.display = 'block'; 
                       

                        var prompt = 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning. Expand upon this content with descriptive language and more detailed explanations, to make the writing easier to understand and increase the length of the content.  Context: '+ selectedText + '. Output reponse the same langues in Context';
                        var askAiInput = document.getElementById('ask_ai');
                        askAiInput.value = prompt;
                        var sendBtn = document.getElementById('send-btn');
                        sendBtn.disabled = false;
                        sendBtn.click();
                        askAiInput.value = '';
                    }
                },
                onPostRender: function() {
                     expand_content_btn = this;
                    editor.on('NodeChange', function(e) {
                        // Disable the button if no text is selected
                        expand_content_btn.disabled(editor.selection.isCollapsed());
                    });
                }
            },
            {
                text: '⚡️ Trim Content',
                id: 'trim_content_btn',
                disabled: true,
                onclick: function() {
                    var selectedText = editor.selection.getContent({format: 'text'});
                    if (selectedText) {

                         var kctaiDiv = document.getElementById('kctai');
                         kctaiDiv.style.display = 'block'; 
                       

                        var prompt = 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning. Remove any repetitive, redundant, or non-essential writing in this content without changing the meaning or losing any key information.  Context: '+ selectedText + '. Output reponse the same langues in Context';
                        var askAiInput = document.getElementById('ask_ai');
                        askAiInput.value = prompt;
                        var sendBtn = document.getElementById('send-btn');
                        sendBtn.disabled = false;
                        sendBtn.click();
                        askAiInput.value = '';
                    }
                },
                onPostRender: function() {
                     trim_content_btn = this;
                    editor.on('NodeChange', function(e) {
                        // Disable the button if no text is selected
                        trim_content_btn.disabled(editor.selection.isCollapsed());
                    });
                }
            },
            {
                type: 'menubutton',
                text: '🔁 Translate',
                menu: (function() {
                    const languages = [
                        { text: 'Translate to Vietnamese', id: 'Translate_vi', language: 'Vietnamese' },
                        { text: 'Translate to English', id: 'Translate_en', language: 'English' },
                        { text: 'Translate to Thai', id: 'Translate_th', language: 'Thai' },
                        { text: 'Translate to Japanese', id: 'Translate_ja', language: 'Japanese' },
                        { text: 'Translate to French', id: 'Translate_fr', language: 'French' },
                        { text: 'Translate to Portuguese', id: 'Translate_pt', language: 'Portuguese' },
                        { text: 'Translate to Afrikaans', id: 'Translate_af', language: 'Afrikaans' },
                        { text: 'Translate to Albanian', id: 'Translate_sq', language: 'Albanian' },
                        { text: 'Translate to Amharic', id: 'Translate_am', language: 'Amharic' },
                        { text: 'Translate to Arabic', id: 'Translate_ar', language: 'Arabic' },
                        { text: 'Translate to Armenian', id: 'Translate_hy', language: 'Armenian' },
                        { text: 'Translate to Azerbaijani', id: 'Translate_az', language: 'Azerbaijani' },
                        { text: 'Translate to Basque', id: 'Translate_eu', language: 'Basque' },
                        { text: 'Translate to Belarusian', id: 'Translate_be', language: 'Belarusian' },
                        { text: 'Translate to Bengali', id: 'Translate_bn', language: 'Bengali' },
                        { text: 'Translate to Bosnian', id: 'Translate_bs', language: 'Bosnian' },
                        { text: 'Translate to Bulgarian', id: 'Translate_bg', language: 'Bulgarian' },
                        { text: 'Translate to Catalan', id: 'Translate_ca', language: 'Catalan' },
                        { text: 'Translate to Cebuano', id: 'Translate_ceb', language: 'Cebuano' },
                        { text: 'Translate to Chichewa', id: 'Translate_ny', language: 'Chichewa' },
                        { text: 'Translate to Chinese', id: 'Translate_zh', language: 'Chinese' },
                        { text: 'Translate to Corsican', id: 'Translate_co', language: 'Corsican' },
                        { text: 'Translate to Croatian', id: 'Translate_hr', language: 'Croatian' },
                        { text: 'Translate to Czech', id: 'Translate_cs', language: 'Czech' },
                        { text: 'Translate to Danish', id: 'Translate_da', language: 'Danish' },
                        { text: 'Translate to Dutch', id: 'Translate_nl', language: 'Dutch' },
                        { text: 'Translate to Esperanto', id: 'Translate_eo', language: 'Esperanto' },
                        { text: 'Translate to Estonian', id: 'Translate_et', language: 'Estonian' },
                        { text: 'Translate to Filipino', id: 'Translate_tl', language: 'Filipino' },
                        { text: 'Translate to Finnish', id: 'Translate_fi', language: 'Finnish' },
                        { text: 'Translate to Frisian', id: 'Translate_fy', language: 'Frisian' },
                        { text: 'Translate to Galician', id: 'Translate_gl', language: 'Galician' },
                        { text: 'Translate to Georgian', id: 'Translate_ka', language: 'Georgian' },
                        { text: 'Translate to German', id: 'Translate_de', language: 'German' },
                        { text: 'Translate to Greek', id: 'Translate_el', language: 'Greek' },
                        { text: 'Translate to Gujarati', id: 'Translate_gu', language: 'Gujarati' },
                        { text: 'Translate to Haitian Creole', id: 'Translate_ht', language: 'Haitian Creole' },
                        { text: 'Translate to Hausa', id: 'Translate_ha', language: 'Hausa' },
                        { text: 'Translate to Hawaiian', id: 'Translate_haw', language: 'Hawaiian' },
                        { text: 'Translate to Hebrew', id: 'Translate_he', language: 'Hebrew' },
                        { text: 'Translate to Hindi', id: 'Translate_hi', language: 'Hindi' },
                        { text: 'Translate to Hmong', id: 'Translate_hmn', language: 'Hmong' },
                        { text: 'Translate to Hungarian', id: 'Translate_hu', language: 'Hungarian' },
                        { text: 'Translate to Icelandic', id: 'Translate_is', language: 'Icelandic' },
                        { text: 'Translate to Igbo', id: 'Translate_ig', language: 'Igbo' },
                        { text: 'Translate to Indonesian', id: 'Translate_id', language: 'Indonesian' },
                        { text: 'Translate to Irish', id: 'Translate_ga', language: 'Irish' },
                        { text: 'Translate to Italian', id: 'Translate_it', language: 'Italian' },
                        { text: 'Translate to Javanese', id: 'Translate_jv', language: 'Javanese' },
                        { text: 'Translate to Kannada', id: 'Translate_kn', language: 'Kannada' },
                        { text: 'Translate to Kazakh', id: 'Translate_kk', language: 'Kazakh' },
                        { text: 'Translate to Khmer', id: 'Translate_km', language: 'Khmer' },
                        { text: 'Translate to Korean', id: 'Translate_ko', language: 'Korean' },
                        { text: 'Translate to Kurdish', id: 'Translate_ku', language: 'Kurdish' },
                        { text: 'Translate to Kyrgyz', id: 'Translate_ky', language: 'Kyrgyz' },
                        { text: 'Translate to Lao', id: 'Translate_lo', language: 'Lao' },
                        { text: 'Translate to Latin', id: 'Translate_la', language: 'Latin' },
                        { text: 'Translate to Latvian', id: 'Translate_lv', language: 'Latvian' },
                        { text: 'Translate to Lithuanian', id: 'Translate_lt', language: 'Lithuanian' },
                        { text: 'Translate to Luxembourgish', id: 'Translate_lb', language: 'Luxembourgish' },
                        { text: 'Translate to Macedonian', id: 'Translate_mk', language: 'Macedonian' },
                        { text: 'Translate to Malagasy', id: 'Translate_mg', language: 'Malagasy' },
                        { text: 'Translate to Malay', id: 'Translate_ms', language: 'Malay' },
                        { text: 'Translate to Malayalam', id: 'Translate_ml', language: 'Malayalam' },
                        { text: 'Translate to Maltese', id: 'Translate_mt', language: 'Maltese' },
                        { text: 'Translate to Maori', id: 'Translate_mi', language: 'Maori' },
                        { text: 'Translate to Marathi', id: 'Translate_mr', language: 'Marathi' },
                        { text: 'Translate to Mongolian', id: 'Translate_mn', language: 'Mongolian' },
                        { text: 'Translate to Myanmar', id: 'Translate_my', language: 'Myanmar' },
                        { text: 'Translate to Nepali', id: 'Translate_ne', language: 'Nepali' },
                        { text: 'Translate to Norwegian', id: 'Translate_no', language: 'Norwegian' },
                        { text: 'Translate to Pashto', id: 'Translate_ps', language: 'Pashto' },
                        { text: 'Translate to Persian', id: 'Translate_fa', language: 'Persian' },
                        { text: 'Translate to Polish', id: 'Translate_pl', language: 'Polish' },
                        { text: 'Translate to Punjabi', id: 'Translate_pa', language: 'Punjabi' },
                        { text: 'Translate to Romanian', id: 'Translate_ro', language: 'Romanian' },
                        { text: 'Translate to Russian', id: 'Translate_ru', language: 'Russian' },
                        { text: 'Translate to Samoan', id: 'Translate_sm', language: 'Samoan' },
                        { text: 'Translate to Scots Gaelic', id: 'Translate_gd', language: 'Scots Gaelic' },
                        { text: 'Translate to Serbian', id: 'Translate_sr', language: 'Serbian' },
                        { text: 'Translate to Sesotho', id: 'Translate_st', language: 'Sesotho' },
                        { text: 'Translate to Shona', id: 'Translate_sn', language: 'Shona' },
                        { text: 'Translate to Sindhi', id: 'Translate_sd', language: 'Sindhi' },
                        { text: 'Translate to Sinhala', id: 'Translate_si', language: 'Sinhala' },
                        { text: 'Translate to Slovak', id: 'Translate_sk', language: 'Slovak' },
                        { text: 'Translate to Slovenian', id: 'Translate_sl', language: 'Slovenian' },
                        { text: 'Translate to Somali', id: 'Translate_so', language: 'Somali' },
                        { text: 'Translate to Spanish', id: 'Translate_es', language: 'Spanish' },
                        { text: 'Translate to Sundanese', id: 'Translate_su', language: 'Sundanese' },
                        { text: 'Translate to Swahili', id: 'Translate_sw', language: 'Swahili' },
                        { text: 'Translate to Swedish', id: 'Translate_sv', language: 'Swedish' },
                        { text: 'Translate to Tajik', id: 'Translate_tg', language: 'Tajik' },
                        { text: 'Translate to Tamil', id: 'Translate_ta', language: 'Tamil' },
                        { text: 'Translate to Telugu', id: 'Translate_te', language: 'Telugu' },
                        { text: 'Translate to Turkish', id: 'Translate_tr', language: 'Turkish' },
                        { text: 'Translate to Ukrainian', id: 'Translate_uk', language: 'Ukrainian' },
                        { text: 'Translate to Urdu', id: 'Translate_ur', language: 'Urdu' },
                        { text: 'Translate to Uzbek', id: 'Translate_uz', language: 'Uzbek' },
                        { text: 'Translate to Vietnamese', id: 'Translate_vi', language: 'Vietnamese' },
                        { text: 'Translate to Welsh', id: 'Translate_cy', language: 'Welsh' },
                        { text: 'Translate to Xhosa', id: 'Translate_xh', language: 'Xhosa' },
                        { text: 'Translate to Yiddish', id: 'Translate_yi', language: 'Yiddish' },
                        { text: 'Translate to Yoruba', id: 'Translate_yo', language: 'Yoruba' },
                        { text: 'Translate to Zulu', id: 'Translate_zu', language: 'Zulu' }
                    ];

                    return languages.map(function(lang) {
                        return {
                            text: lang.text,
                            id: lang.id,
                            onclick: function() {
                                var selectedText = editor.selection.getContent({format: 'text'});
                                if (selectedText) {
                                    var kctaiDiv = document.getElementById('kctai');
                                    kctaiDiv.style.display = 'block';

                                    var prompt = 'Translate this content to ' + lang.language + ' language. Context: ' + selectedText;
                                    var askAiInput = document.getElementById('ask_ai');
                                    askAiInput.value = prompt;
                                    var sendBtn = document.getElementById('send-btn');
                                    sendBtn.disabled = false;
                                    sendBtn.click();
                                    askAiInput.value = '';
                                }
                            }
                        };
                    });
                })()
            }


        ],
        
    });

editor.on('NodeChange', function(e) {
        if (summarizeButton) {
            if (editor.selection.isCollapsed()) {
                summarizeButton.disabled(true); // Disable if no text is selected

            } else {
                summarizeButton.disabled(false); // Enable if text is selected
            }
        }

         if (improve_content_btn) {
            if (editor.selection.isCollapsed()) {
                improve_content_btn.disabled(true); // Disable if no text is selected

            } else {
                improve_content_btn.disabled(false); // Enable if text is selected
            }
        }

        if (Simplify_content_btn) {
            if (editor.selection.isCollapsed()) {
                Simplify_content_btn.disabled(true); // Disable if no text is selected

            } else {
                Simplify_content_btn.disabled(false); // Enable if text is selected
            }
        }

        if (expand_content_btn) {
            if (editor.selection.isCollapsed()) {
                expand_content_btn.disabled(true); // Disable if no text is selected

            } else {
                expand_content_btn.disabled(false); // Enable if text is selected
            }
        }

        if (trim_content_btn) {
            if (editor.selection.isCollapsed()) {
                trim_content_btn.disabled(true); // Disable if no text is selected

            } else {
                trim_content_btn.disabled(false); // Enable if text is selected
            }
        }

        

        
        

    });
});

tinymce.init({
    selector: 'content',  // Your specific selector
    plugins: 'aikct_ask_pro ',  // Register your plugins
    toolbar: ' aikct_ask_pro'  // Add buttons to the toolbar
});





