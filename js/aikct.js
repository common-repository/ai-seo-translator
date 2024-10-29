
var getTagifyValues ;
function inittagfy(inputElement, whitelistString, maxTags = null) {
            var whitelist = whitelistString.split(',').map(item => item.trim());
            
            var tagifyOptions = { 
                whitelist: whitelist,
                dropdown: {
                    enabled: 0, 
                    closeOnSelect: false, 
                    maxItems: 20 
                },
                mixMode: false 
            };
            if (maxTags === 1) {
                tagifyOptions.maxTags = 1;
            }
            var tagify = new Tagify(inputElement, tagifyOptions);
             inputElement.addEventListener('focus', function() {
                tagify.dropdown.show.call(tagify); 
                console.log('cos');
            });

            
            tagify.on('add', function(e) {
                console.log('Tag added:', e.detail.tag);
                updateSelectedTags();
            });

            tagify.on('remove', function(e) {
                console.log('Tag removed:', e.detail.tag);
                updateSelectedTags();
            });

            
            tagify.getValue = function() {
                return tagify.value.map(tag => tag.value);
            };

            
            function updateSelectedTags() {
                
                
                
                
                
                
                
            }

            return tagify;
        }


function aikctconvertentity(input) {
    const textarea = document.createElement('textarea');

    textarea.innerHTML = input;

    let decodedHtml = textarea.value;
    if (decodedHtml.startsWith('"') && decodedHtml.endsWith('"')) {
        decodedHtml = decodedHtml.slice(1, -1);
    }
    return decodedHtml;
}

function aikct_Chat(text, outputId, speed = 50) {
    let outputDiv = document.getElementById(outputId);
    let index = 0;
    let tagStack = [];
    let outputHtml = '';

    let messageDiv = document.createElement('div');
    messageDiv.className = 'message-box left';
    outputDiv.appendChild(messageDiv);

    let audio = new Audio(aikct_js.audio_key);
    audio.loop = true;
    audio.play();

    function displayNextChar() {
        if (index < text.length) {
            if (text[index] === '<') {
                let closingTagIndex = text.indexOf('>', index);
                if (closingTagIndex !== -1) {
                    let tag = text.slice(index, closingTagIndex + 1);
                    let tagNameMatch;

                    if (tag.startsWith('</')) {
                        // Handle closing tags
                        tagNameMatch = tag.match(/<\/(\w+)/);
                        if (tagNameMatch) {
                            let tagName = tagNameMatch[1];
                            if (tagStack.length > 0 && tagStack[tagStack.length - 1] === tagName) {
                                outputHtml += tag;
                                tagStack.pop();
                            }
                        }
                    } else {
                        // Handle opening tags
                        tagNameMatch = tag.match(/<(\w+)/);
                        if (tagNameMatch) {
                            let tagName = tagNameMatch[1];
                            outputHtml += tag;
                            tagStack.push(tagName);
                        }
                    }
                    index = closingTagIndex + 1;
                }
            } else {
                // Handle regular text
                let nextChar = text[index];
                outputHtml += nextChar;
                index++;
            }

            messageDiv.innerHTML = outputHtml;
            outputDiv.scrollTop = outputDiv.scrollHeight;


            setTimeout(displayNextChar, speed);
        } else {
            
            while (tagStack.length > 0) {
                let tagName = tagStack.pop();
                outputHtml += `</${tagName}>`;
            }

            messageDiv.innerHTML = outputHtml;


            let divcontrol = document.createElement('div');
            divcontrol.className = "controls";
                let addButton = document.createElement('button');
                let icon = document.createElement('i');
                icon.className = 'fa-solid fa-plus';
                addButton.appendChild(icon);

            
            addButton.appendChild(document.createTextNode(' Add to Content'));
            addButton.className = 'add-to-content-button';
            addButton.onclick = function(e) {
                e.preventDefault();
                detectEditorAndInsertContent(outputHtml);
            };
            divcontrol.appendChild(addButton);
            outputDiv.appendChild(divcontrol);

            audio.pause();
            audio.currentTime = 0;
        }
    }

    setTimeout(displayNextChar, speed);
}
function detectEditorAndInsertContent(html) {
                        
                        if (typeof wpseEditor !== 'undefined') {
                            const editorType = wpseEditor.editorType;

                            if (editorType === 'gutenberg' && typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
                                var blockEditor = wp.data.select('core/block-editor');
                                if (blockEditor) {
                                    var content = blockEditor.getBlocks();
                                    var newBlock = wp.blocks.createBlock('core/paragraph', {
                                        content: html
                                    });
                                    wp.data.dispatch('core/block-editor').insertBlocks(newBlock, content.length);
                                    clearAndHidePrompt();
                                    return 'gutenberg';
                                }
                            } else if (editorType === 'classic') {
                                if (typeof tinymce !== 'undefined') {
                                    tinymce.get('content').execCommand('mceInsertContent', false, html);
                                    clearAndHidePrompt();
                                    return 'tinymce';
                                }

                                var editor = jQuery('#content');
                                if (editor.length) {
                                    editor.val(editor.val() + html);
                                    clearAndHidePrompt();
                                    return 'classic';
                                }
                            }
                        }

                        console.log('No recognized editor is active');
                        return 'none';
                    }

                    function clearAndHidePrompt() {
                        jQuery('#my_prompt_result').empty().removeClass('has-content');
                        jQuery('#modelai').empty();
                        jQuery('#add_to_post_button').hide();
                        jQuery('#close-btn').click();
                    }

// function aikct_Chat(text, outputId, speed = 50) {
//     let outputDiv = document.getElementById(outputId);
//     let index = 0;
//     let tagStack = [];
//     let outputHtml = '';

//     let messageDiv = document.createElement('div');
//     messageDiv.className = 'message-box left';
//     outputDiv.appendChild(messageDiv);

//     let audio = new Audio(aikct_js.audio_key);
//     audio.loop = true;
//     audio.play();

//     function displayNextChar() {
//         if (index < text.length) {
//             if (text[index] === '<') {
//                 let closingTagIndex = text.indexOf('>', index);
//                 if (closingTagIndex !== -1) {
//                     let tag = text.slice(index, closingTagIndex + 1);
//                     let tagNameMatch;

//                     if (tag.startsWith('</')) {
//                         // Handle closing tags
//                         tagNameMatch = tag.match(/<\/(\w+)/);
//                         if (tagNameMatch) {
//                             let tagName = tagNameMatch[1];
//                             if (tagStack.length > 0 && tagStack[tagStack.length - 1] === tagName) {
//                                 outputHtml += tag;
//                                 tagStack.pop();
//                             }
//                         }
//                     } else {
//                         // Handle opening tags
//                         tagNameMatch = tag.match(/<(\w+)/);
//                         if (tagNameMatch) {
//                             let tagName = tagNameMatch[1];
//                             outputHtml += tag;
//                             tagStack.push(tagName);
//                         }
//                     }
//                     index = closingTagIndex + 1;
//                 }
//             } else {
//                 // Handle regular text
//                 let nextChar = text[index];
//                 outputHtml += nextChar;
//                 index++;
//             }

//             messageDiv.innerHTML = outputHtml;
//             outputDiv.scrollTop = outputDiv.scrollHeight;

//             // Đếm số từ hiện tại và cập nhật vào `div` hiển thị số từ
//             let wordCount = wp.wordcount.count(outputHtml, 'words');
//             document.getElementById('wordCountDisplay').innerText = `Word Count: ${wordCount}`;

//             setTimeout(displayNextChar, speed);
//         } else {
//             // Handle remaining tags in the stack
//             while (tagStack.length > 0) {
//                 let tagName = tagStack.pop();
//                 outputHtml += `</${tagName}>`;
//             }

//             messageDiv.innerHTML = outputHtml;
//             audio.pause();
//             audio.currentTime = 0;
//         }
//     }

//     setTimeout(displayNextChar, speed);
// }


// function aikct_Chat(text, outputId, speed = 50) {
//     let outputDiv = document.getElementById(outputId);
//     let index = 0;
//     let tagStack = [];
//     let outputHtml = '';

//     let messageDiv = document.createElement('div');
//     messageDiv.className = 'message-box left';
//     outputDiv.appendChild(messageDiv);

//     let audio = new Audio(aikct_js.audio_key);
//     audio.loop = true;
//     audio.play();

//     function displayNextChar() {
//         if (index < text.length) {
//             if (text[index] === '<') {
//                 let closingTagIndex = text.indexOf('>', index);
//                 if (closingTagIndex !== -1) {
//                     let tag = text.slice(index, closingTagIndex + 1);
//                     if (tag.startsWith('</')) {
//                         let tagName = tag.match(/<\/(\w+)/)[1];
//                         if (tagStack.length > 0 && tagStack[tagStack.length - 1] === tagName) {
//                             outputHtml += tag;
//                             tagStack.pop();
//                         }
//                     } else {
//                         let tagName = tag.match(/<(\w+)/)[1];
//                         outputHtml += tag;
//                         tagStack.push(tagName);
//                     }
//                     index = closingTagIndex + 1;
//                 }
//             } else {
//                 let nextChar = text[index];
//                 outputHtml += nextChar;
//                 index++;
//             }

//             messageDiv.innerHTML = outputHtml;
//             outputDiv.scrollTop = outputDiv.scrollHeight;

//             // Đếm số từ hiện tại và cập nhật vào `div` hiển thị số từ
//             let wordCount = wp.wordcount.count(outputHtml, 'words');
//             document.getElementById('wordCountDisplay').innerText = `Word Count: ${wordCount}`;

//             setTimeout(displayNextChar, speed);
//         } else {
//             while (tagStack.length > 0) {
//                 let tagName = tagStack.pop();
//                 outputHtml += `</${tagName}>`;
//             }

//             messageDiv.innerHTML = outputHtml;
//             audio.pause();
//             audio.currentTime = 0;
//         }
//     }

//     setTimeout(displayNextChar, speed);
// }

function aikct_get_title() {
    var postTitle = jQuery('#title').val();

    if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        if (editorType === 'gutenberg') {
            postTitle = wp.data.select('core/editor').getEditedPostAttribute('title');
        } else if (editorType === 'classic') {
            postTitle = jQuery('#title').val();
        }
    }

    return postTitle;
}

function aikct_get_content() {
    var postContent = '';

    if (typeof wpseEditor !== 'undefined') {
        const editorType = wpseEditor.editorType;

        if (editorType === 'gutenberg') {
            postContent = wp.data.select('core/editor').getEditedPostAttribute('content');
        } else if (editorType === 'classic') {
            postContent = tinyMCE.activeEditor.getContent();
        }
    }

    return postContent;
}





var closeBtn = document.getElementById('close-btn');
    if (closeBtn) {
        closeBtn.onclick = function(event) {
            event.preventDefault();
            var kctaiDivnew = document.getElementById('kctai');
            if (kctaiDivnew) {
                kctaiDivnew.style.display = 'none';
            } 
        };
    } 




function aikct_mess_success(title, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title || 'Success!',
            text: message || 'Success',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } else {
        alert((title || 'Success!') + '\n' + (message || 'Success Ok.'));
    }
}


function aikct_mess_error(title, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title || 'Error!',
            text: message || 'An error has occurred. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else {
        alert((title || 'Error!') + '\n' + (message || 'An error has occurred. Please try again..'));
    }
}
function autoScrollToBottom(divId) {
    const targetDiv = document.getElementById(divId);

    if (!targetDiv) {
        console.error(`No element found with id ${divId}`);
        return;
    }

    function scrollToBottom() {
        targetDiv.scrollTop = targetDiv.scrollHeight;
    }

    const observer = new MutationObserver(scrollToBottom);
    const config = { childList: true, subtree: true };

    observer.observe(targetDiv, config);
    window.addEventListener('load', scrollToBottom);
}

autoScrollToBottom('my_prompt_result');

document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.key === 'k') {
        event.preventDefault(); // Ngăn chặn hành vi mặc định (nếu có)
        alert('Bạn đã nhấn Ctrl + K');
    }
});

// wp.domReady(function() {
//     document.addEventListener('keydown', function(event) {
//         const activeElement = document.activeElement;
        
//         // Kiểm tra trong Gutenberg Block Editor
//         if (activeElement && activeElement.closest('.block-editor-rich-text__editable')) {
//             if (event.ctrlKey && event.key === 'k') {
//                 event.preventDefault(); 
//                 alert('Bạn đã nhấn Ctrl + K trong Gutenberg Block Editor');
//             }
//         }
//     });
// });

// // Lắng nghe sự kiện trên TinyMCE Editor
// tinymce.init({
//     selector: 'textarea',  
//     setup: function(editor) {
//         editor.on('keydown', function(event) {
//             if (event.ctrlKey && event.key === 'k') {
//                 event.preventDefault();
//                 alert('Bạn đã nhấn Ctrl + K trong TinyMCE Editor');
//             }
//         });
//     }
// });
