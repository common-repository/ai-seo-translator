 
jQuery(document).ready(function($) {

if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        if (editorType === 'gutenberg') {
            console.log('Gutenberg editor is active');
            // Add your Gutenberg specific code here
        } else if (editorType === 'classic') {
            console.log('Classic editor is active');
            // Add your Classic editor specific code here
        } else {
            console.log('Editor type is unknown or not an editor page');
        }
    }


function detectWPEditorVersion() {

    if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        if (editorType === 'gutenberg') {
             console.log('Gutenberg editor is active');
            return 'gutenberg';
           
            // Add your Gutenberg specific code here
        } else if (editorType === 'classic') {
            return 'tinymce';
            console.log('Classic editor tinymce is active');
            // Add your Classic editor specific code here
        } else {

            console.log('Editor type is unknown or not an editor page');
             return 'Unknown';
        }
    }
}

console.log(detectWPEditorVersion());


function detectEditor() {
    if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        if (editorType === 'gutenberg') {
             console.log('Gutenberg editor is active');
            return 'gutenberg';
           
            // Add your Gutenberg specific code here
        } else if (editorType === 'classic') {
            return 'tinymce';
            console.log('Classic editor tinymce is active');
            // Add your Classic editor specific code here
        } else {

            console.log('Editor type is unknown or not an editor page');
             return 'Unknown';
        }
    }
}
var editorType = detectEditor();


function addAskAIButtonToGutenberg() {
        wp.data.subscribe(function () {
            const editorElement = document.querySelector('.edit-post-header-toolbar');
            if (editorElement && !document.querySelector('#ask-kctai-button')) {
                
                var askKCTAIButton = document.createElement('button');

                    askKCTAIButton.innerHTML = `
                      <svg width="24" height="24" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H5Zm6.8 11.5.5 1.2a68.3 68.3 0 0 0 .7 1.1l.4.1c.3 0 .5 0 .7-.3.2-.1.3-.3.3-.6l-.3-1-2.6-6.2a20.4 20.4 0 0 0-.5-1.3l-.5-.4-.7-.2c-.2 0-.5 0-.6.2-.2 0-.4.2-.5.4l-.3.6-.3.7L5.7 15l-.2.6-.1.4c0 .3 0 .5.3.7l.6.2c.3 0 .5 0 .7-.2l.4-1 .5-1.2h3.9ZM9.8 9l1.5 4h-3l1.5-4Zm5.6-.9v7.6c0 .4 0 .7.2 1l.7.2c.3 0 .6 0 .8-.3l.2-.9V8.1c0-.4 0-.7-.2-.9a1 1 0 0 0-.8-.3c-.2 0-.5.1-.7.3l-.2 1Z"></path></svg>
              KCT `;
                    askKCTAIButton.classList.add('icon-text');
                    askKCTAIButton.title = 'Ask AI KCT';
                    askKCTAIButton.id = 'ask-kctai-button';

                editorElement.appendChild(askKCTAIButton);
                var kctaiDiv = document.getElementById('kctai');
        
                if (kctaiDiv) {

                    kctaiDiv.style.display = 'none';
                    document.getElementById('my_prompt_meta').style.display = 'none';

                    askKCTAIButton.addEventListener('click', function(event) {
                        event.preventDefault();

                        if (kctaiDiv.style.display === 'none' || kctaiDiv.style.display === '') {
                            kctaiDiv.style.display = 'block'; 
                        } else {
                            kctaiDiv.style.display = 'none'; 
                        }
                    });
                }
            }
        });
    }
function positionKctaiDivForGutenberg() {
       
        var editorWrapper = document.getElementById('editor');;
         var kctaiDivnew = document.getElementById('kctai');
        var kctaiDiv = kctaiDivnew;
        var postStatusInfo =  document.getElementById('editor');
        if (postStatusInfo) {
                postStatusInfo.parentNode.insertBefore(kctaiDiv, postStatusInfo);
            } 
        
    
        if (kctaiDiv && editorWrapper) {
            function positionKctaiDiv() {
                var kctaiDivWidth = 600; 
                var windowWidth = window.innerWidth;

                var leftPosition = (windowWidth - kctaiDivWidth) / 2;

                kctaiDiv.style.position = 'fixed';
                kctaiDiv.style.bottom = '0px';
                kctaiDiv.style.left = leftPosition + 'px';
                kctaiDiv.style.width = kctaiDivWidth + 'px';
                kctaiDiv.style.zIndex = '999999999999999';
            }


            positionKctaiDiv();

            window.addEventListener('scroll', positionKctaiDiv);
            window.addEventListener('resize', positionKctaiDiv);

            var observer = new MutationObserver(positionKctaiDiv);
            observer.observe(editorWrapper, { childList: true, subtree: true });
        }
    }
if(editorType=='gutenberg'){
    // addAskAIButtonToGutenberg();
    // positionKctaiDivForGutenberg();
    

    
}else if(editorType=='tinymce'){
    console.log('atinymce');
    


    var kctaiDivnew = document.getElementById('kctai');
    if(kctaiDivnew){
        var kctaiDiv = kctaiDivnew;
        var postStatusInfo = document.getElementById('wp-content-wrap');
        if (postStatusInfo) {
            postStatusInfo.parentNode.insertBefore(kctaiDiv, postStatusInfo);
        }
        

        kctaiDiv.style.display = 'none';
        if (kctaiDiv && postStatusInfo) {
            function positionKctaiDiv() {

                var postStatusInfoRect = postStatusInfo.getBoundingClientRect();

                kctaiDiv.style.position = postStatusInfo.style.position;
                kctaiDiv.style.bottom = '5px'; 
                
                kctaiDiv.style.left = postStatusInfoRect.left + 'px';
                kctaiDiv.style.width = postStatusInfoRect.width + 'px';
                
            }

            positionKctaiDiv();

            window.addEventListener('resize', positionKctaiDiv);

            var observer = new MutationObserver(positionKctaiDiv);
            observer.observe(postStatusInfo, { childList: true, subtree: true });
        }

        var kctaiDiv = document.getElementById('kctai');
        var postStatusInfo = document.getElementById('post-status-info');

        if (postStatusInfo) {
            function positionKctaiDiv() {

                var postStatusInfoRect = postStatusInfo.getBoundingClientRect();

                kctaiDiv.style.position = 'fixed'; 
                if (postStatusInfo.style.bottom.trim() === '') {
                    kctaiDiv.style.bottom = '0px'; 
                } else {
                    kctaiDiv.style.bottom = postStatusInfo.style.bottom; 
                }
               
                kctaiDiv.style.left = postStatusInfoRect.left + 'px';
                kctaiDiv.style.width = postStatusInfoRect.width + 'px';
                kctaiDiv.style.zIndex = '999999999'; 
                
            }
            positionKctaiDiv();
            window.addEventListener('scroll', positionKctaiDiv);

            window.addEventListener('resize', positionKctaiDiv);
        }

    }
    

}






    


});

jQuery(document).ready(function($) {
    // Create the toolbar container
    var toolbar = document.createElement('div');
    toolbar.id = 'tinyeditor-toolbar';
    toolbar.style.position = 'absolute';
    toolbar.style.display = 'none';
    toolbar.style.backgroundColor = '#fff';
    toolbar.style.border = '1px solid #ccc';
    toolbar.style.padding = '5px';
    toolbar.style.borderRadius = '5px';
    toolbar.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
    toolbar.style.zIndex = '1000';

    // Create the "AI KCT" button
    var aiKctBtn = document.createElement('button');
    aiKctBtn.id = 'ai-kct-btn';
    aiKctBtn.innerHTML = '<i class="mce-ico mce-i-aikct_mce-icon_shortcode"></i> <span class="mce-txt">KCT Pro</span>';
   
    
    aiKctBtn.style.border = 'none';
    aiKctBtn.style.padding = '2px 3px';
    aiKctBtn.style.fontSize = '14px';
    aiKctBtn.style.cursor = 'pointer';
    aiKctBtn.style.borderRadius = '5px';
    aiKctBtn.style.position = 'relative';

   var dropdownMenu = document.createElement('div');
    dropdownMenu.id = 'dropdown-menu';
    dropdownMenu.style.display = 'none';
    dropdownMenu.style.position = 'absolute';
    dropdownMenu.style.top = '100%'; // Position it below the "AI KCT" button
    dropdownMenu.style.left = '0'; // Align left edge with the "AI KCT" button
    dropdownMenu.style.backgroundColor = '#fff';
    dropdownMenu.style.border = '1px solid #ccc';
    dropdownMenu.style.borderRadius = '5px';
    dropdownMenu.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
    dropdownMenu.style.zIndex = '1001';
    dropdownMenu.style.minWidth = '150px'; // Optional: set a minimum width for dropdown
    dropdownMenu.style.whiteSpace = 'nowrap'; // Prevent text wrapping
    dropdownMenu.style.flexDirection = 'column';

    // Define the standard TinyMCE buttons
    var standardButtons = [
        { id: 'bold-btn', label: '<b>B</b>', command: 'bold' },
        { id: 'italic-btn', label: '<i>I</i>', command: 'italic' },
        { id: 'underline-btn', label: '<u>U</u>', command: 'underline' },
        { id: 'link-btn', label: '🔗', command: 'createLink' },
        { id: 'h2-btn', label: 'H2', command: 'formatBlock', value: 'h2' },
        { id: 'h3-btn', label: 'H3', command: 'formatBlock', value: 'h3' },
        { id: 'quote-btn', label: '“ ”', command: 'formatBlock', value: 'blockquote' }
    ];

    // Define the AI KCT buttons
    var aiButtons = [
        { id: 'summarize-btn', label: '📖 Summarize content', command: 'summarizeContent' },
        { id: 'improve-btn', label: '⚡️ Improve writing', command: 'improveContent' },
        { id: 'simplify-btn', label: '⚡️ Simplify language', command: 'simplifyContent' },
        { id: 'expand-btn', label: '⚡️ Expand upon', command: 'expandContent' },
        { id: 'trim-btn', label: '⚡️ Trim Content', command: 'trimContent' },
        // { id: 'translate-btn', label: 'Translate', command: 'translateContent' }
    ];

    // Function to create a button
    function createButton(btn) {
        if (!btn || typeof btn !== 'object') {
            console.error('Invalid button data');
            return null;
        }
        var editorType = wpseEditor.editorType;
        if(editorType=='classic'){
            if (typeof tinyMCE !== 'undefined') {
                var editor = tinyMCE.activeEditor;
                var button = document.createElement('button');
                button.id = btn.id;
                button.innerHTML = btn.label;
                button.style.backgroundColor = '#fff';
                button.style.border = 'none';
                button.style.cursor = 'pointer';
                button.style.padding = '10px';
                // button.style.width = '100%';
                button.style.textAlign = 'left';
                button.style.fontSize = '14px';

                button.addEventListener('click', function () {
                    switch (btn.command) {
                        case 'createLink':
                            var url = prompt('Nhập URL:');
                            editor.execCommand(btn.command, false, url);
                            break;
                        case 'summarizeContent':
                        case 'improveContent':
                        case 'simplifyContent':
                        case 'expandContent':
                        case 'trimContent':
                        case 'translateContent':
                            var selectedText = editor.selection.getContent({ format: 'text' });

                            // const selectedText = document.getSelection().toString();


                            if (selectedText) {
                                var kctaiDiv = document.getElementById('kctai');
                                kctaiDiv.style.display = 'block';
                                var prompt = '';
                                if (btn.command === 'summarizeContent') {
                                    prompt = 'Question: Provide the key points and concepts in this content in a succinct summary. Context: ' + selectedText + '. Output response the same languages in Context';
                                } else if (btn.command === 'improveContent') {
                                    prompt = 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning. Context: ' + selectedText + '. Output response the same languages in Context';
                                } else if (btn.command === 'simplifyContent') {
                                    prompt = 'Rewrite this content with simplified language and reduce the complexity of the writing, so that the content is easier to understand. Context: ' + selectedText + '. Output response the same languages in Context';
                                } else if (btn.command === 'expandContent') {
                                    prompt = 'Expand upon this content with descriptive language and more detailed explanations, to make the writing easier to understand and increase the length of the content. Context: ' + selectedText + '. Output response the same languages in Context';
                                } else if (btn.command === 'trimContent') {
                                    prompt = 'Remove any repetitive, redundant, or non-essential writing in this content without changing the meaning or losing any key information. Context: ' + selectedText + '. Output response the same languages in Context';
                                } else if (btn.command === 'translateContent') {
                                    var language = prompt('Enter target language (e.g., en, es, pt, de, fr, no, uk, ja, ko, zh_cn, he, hi):');
                                    prompt = 'Translate this content to ' + language + ' language. Context: ' + selectedText;
                                }
                                var askAiInput = document.getElementById('ask_ai');
                                askAiInput.value = prompt;
                                var sendBtn = document.getElementById('send-btn');
                                sendBtn.disabled = false;
                                sendBtn.click();
                                askAiInput.value = '';
                            }
                            break;
                        default:
                            editor.execCommand(btn.command, false, btn.value || null);
                            break;
                    }
                    dropdownMenu.style.display = 'none'; // Hide dropdown after button click
                });

                button.addEventListener('mouseover', function () {
                    button.style.backgroundColor = '#eee';
                });

                button.addEventListener('mouseout', function () {
                    button.style.backgroundColor = '#fff';
                });

                return button;
            }
            
        }
        
    }

    // Add standard TinyMCE buttons to the toolbar
   

    
    toolbar.appendChild(aiKctBtn);

    // Add AI KCT buttons to the dropdown menu
    aiButtons.forEach(function (btn) {
        var button = createButton(btn);
        if (button) {
            dropdownMenu.appendChild(button);
        }
        
    });

    // Show dropdown menu when hovering over "AI KCT" button
    aiKctBtn.addEventListener('mouseover', function () {
        dropdownMenu.style.display = 'flex';
    });

    
    function hideDropdown() {
        setTimeout(function () {
            if (!aiKctBtn.matches(':hover') && !dropdownMenu.matches(':hover')) {
                dropdownMenu.style.display = 'none';
            }
        }, 100); 
    }

    aiKctBtn.addEventListener('mouseout', hideDropdown);
    dropdownMenu.addEventListener('mouseout', hideDropdown);

    
    toolbar.appendChild(dropdownMenu);
     standardButtons.forEach(function (btn) {
        var button = createButton(btn);
        if (button) {
            toolbar.appendChild(button);
        }
    });
    document.body.appendChild(toolbar);
});

jQuery(document).ready(function($) {
    // Ensure that the TinyMCE editor is fully loaded
    if (typeof tinyMCE !== 'undefined') {
        // Wait until the editor is ready
        tinyMCE.on('AddEditor', function(event) {
            var editor = tinyMCE.activeEditor;

            // Check if the editor is properly initialized
            if (editor) {
                editor.on('mouseup keyup', function() {
                    console.log('Editor ID:', editor.id);
                    // Your logic here, e.g., handling the mouseup or keyup event
                });
            } else {
                console.log('Editor is not yet initialized.');
            }
        });
    } else {
        console.log('TinyMCE is not available.');
    }
});


jQuery(document).ready(function($) {
    
    if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        // Create and style the toolbar if not already present
        let toolbar = document.getElementById('tinyeditor-toolbar');
        if (!toolbar) {
            toolbar = document.createElement('div');
            toolbar.id = 'tinyeditor-toolbar';
            toolbar.style.position = 'absolute';
            toolbar.style.display = 'none';
            toolbar.style.backgroundColor = '#fff';
            toolbar.style.border = '1px solid #ccc';
            toolbar.style.padding = '5px';
            toolbar.style.borderRadius = '5px';
            toolbar.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
            toolbar.style.zIndex = '1000';
            document.body.appendChild(toolbar);
        }

        const showToolbar = (left, top) => {
            toolbar.style.display = 'block';
            toolbar.style.top = (top - 10) + 'px';
            toolbar.style.left = left + 'px';
        };

        const hideToolbar = () => {
            toolbar.style.display = 'none';
        };

        if (editorType === 'gutenberg') {
            const { select, subscribe } = wp.data;

            // Function to update toolbar visibility and position
            const updateToolbar = () => {
                const { getSelectedBlock, getSelectionStart } = select('core/block-editor');

                // Get the currently selected block and its selection start position
                const selectedBlock = getSelectedBlock();
                const selectionStart = getSelectionStart();
                
                if (selectedBlock && selectionStart !== undefined) {
                    const selectionRect = document.getSelection().getRangeAt(0).getBoundingClientRect();
                    const editorRect = document.querySelector('.editor-styles-wrapper').getBoundingClientRect();
                    
                    const toolbarLeft = editorRect.left + selectionRect.left;
                    const toolbarTop = editorRect.top + selectionRect.bottom + window.pageYOffset;
                    
                    showToolbar(toolbarLeft, toolbarTop);
                } else {
                    hideToolbar();
                }
            };

            // Subscribe to editor state changes
            subscribe(() => {
                updateToolbar();
            });

            // Initial update
            updateToolbar();

        } else if (editorType === 'classic') {
            if (typeof tinyMCE !== 'undefined') {
                var editor = tinyMCE.activeEditor;

                if (editor) {
                    editor.on('mouseup keyup', function() {
                        if (editor.selection) {
                            var selectedText = editor.selection.getContent({ format: 'text' });

                            if (selectedText !== '') {
                                var selectionRange = editor.selection.getRng();
                                var selectionRect = selectionRange.getBoundingClientRect();
                                var editorRect = editor.getContainer().getBoundingClientRect();
                                var tabbarLeft = editorRect.left + selectionRect.left;
                                var tabbarTop = editorRect.top + selectionRect.bottom + window.pageYOffset;

                                showToolbar(tabbarLeft, tabbarTop);
                            } else {
                                hideToolbar();
                            }
                        } else {
                            hideToolbar();
                        }
                    });
                    console.log('Classic editor is active');
                } else {
                    console.log('Classic editor not found');
                }
            }
            
        } else {
            console.log('Editor type is unknown or not an editor page');
        }
    }

});


function kctaipostmarkdown(src){
        src=src.replace(/(#+)(\w+)/g,'\n$1 $2');
        var rx_lt=/</g;
        var rx_gt=/>/g;
        var rx_space=/\t|\r|\uf8ff/g;
        var rx_escape=/\\([\\\|`*_{}\[\]()#+\-~])/g;
        var rx_hr=/^([*\-=_] *){3,}$/gm;
        var rx_blockquote=/\n *&gt; *([^]*?)(?=(\n|$){2})/g;
        var rx_list=/\n( *)(?:[*\-+]|((\d+)|([a-z])|[A-Z])[.)]) +([^]*?)(?=(\n|$){2})/g;
        var rx_listjoin=/<\/(ol|ul)>\n\n<\1>/g;
        var rx_highlight=/(^|[^A-Za-z\d\\])(([*_])|(~)|(\^)|(--)|(\+\+)|`)(\2?)([^<]*?)\2\8(?!\2)(?=\W|_|$)/g;
        var rx_code=/\n((```|~~~).*\n?([^]*?)\n?\2|((    .*?\n)+))/g;
        var rx_link=/((!?)\[(.*?)\]\((.*?)( ".*")?\)|\\([\\`*_{}\[\]()#+\-.!~]))/g;
        var rx_table=/\n(( *\|.*?\| *\n)+)/g;
        var rx_thead=/^.*\n( *\|( *\:?-+\:?-+\:? *\|)* *\n|)/;
        var rx_row=/.*\n/g;var rx_cell=/\||(.*?[^\\])\|/g;
        var rx_heading=/(?=^|>|\n)([>\s]*?)(#{1,6}) (.*?)( #*)? *(?=\n|$)/g;
        var rx_para=/(?=^|>|\n)\s*\n+([^<]+?)\n+\s*(?=\n|<|$)/g;
        var rx_stash=/-\d+\uf8ff/g;
        function replace(rex,fn){src=src.replace(rex,fn);}

    function element(tag,content){return '<'+tag+'>'+content+'</'+tag+'>';}
    function blockquote(src){return src.replace(rx_blockquote,function(all,content){return element('blockquote',blockquote(highlight(content.replace(/^ *&gt; */gm,''))));});}
    function list(src){return src.replace(rx_list,function(all,ind,ol,num,low,content){var entry=element('li',highlight(content.split(RegExp('\n ?'+ind+'(?:(?:\\d+|[a-zA-Z])[.)]|[*\\-+]) +','g')).map(list).join('</li><li>')));return '\n'+(ol?'<ol start="'+(num?ol+'">':parseInt(ol,36)-9+'" style="list-style-type:'+(low?'low':'upp')+'er-alpha">')+entry+'</ol>':element('ul',entry));});}
    function highlight(src){return src.replace(rx_highlight,function(all,_,p1,emp,sub,sup,small,big,p2,content){return _+element(emp?(p2?'strong':'em'):sub?(p2?'s':'sub'):sup?'sup':small?'small':big?'big':'code',highlight(content));});}
    function unesc(str){return str.replace(rx_escape,'$1');}
    var stash=[];
    var si=0;
    src='\n'+src+'\n';
    replace(rx_lt,'&lt;');
    replace(rx_gt,'&gt;');
    replace(rx_space,'  ');
    src=blockquote(src);
    replace(rx_hr,'<hr/>');
    src=list(src);
    replace(rx_listjoin,'');
    replace(rx_code,function(all,p1,p2,p3,p4){stash[--si]=element('pre',element('code',p3||p4.replace(/^    /gm,'')));return si+'\uf8ff';});
    replace(rx_link,function(all,p1,p2,p3,p4,p5,p6){stash[--si]=p4?p2?(showImage==true?(p4.indexOf("http")>-1?'<img src="'+p4+'" alt="'+p3+'" onerror="this.style.display=\'none\'"/>':''):''):'<a href="'+p4+'" alt="'+p3+'">'+unesc(highlight(p3))+'</a>':p6;return si+'\uf8ff';});
    replace(rx_table,function(all,table){var sep=table.match(rx_thead)[1];return '\n'+element('table',table.replace(rx_row,function(row,ri){return row==sep?'':element('tr',row.replace(rx_cell,function(all,cell,ci){return ci?element(sep&&!ri?'th':'td',unesc(highlight(cell||''))):''}))}))});
    replace(rx_heading,function(all,_,p1,p2){return _+element('h'+p1.length,unesc(highlight(p2)))});
    replace(rx_para,function(all,content){return element('p',unesc(highlight(content)))});
    replace(rx_stash,function(all){return stash[parseInt(all)]});return src.trim();
    };
