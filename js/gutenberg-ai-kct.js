function aikctconvertentity(input) {
    const textarea = document.createElement('textarea');

    textarea.innerHTML = input;

    const decodedHtml = textarea.value;

    return decodedHtml;
}


var closeBtn = document.getElementById('close-btn');
    if (closeBtn) {
        closeBtn.onclick = function(event) {
            event.preventDefault();
            var kctaiDivnew = document.getElementById('kctai');
            if (kctaiDivnew) {
                kctaiDivnew.style.display = 'none';
            } else {
                console.error('Element with ID "kctai" not found.');
            }
        };
    } else {
        console.error('Element with ID "close-btn" not found.');
    }

    
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

function positionKctaiDivForGutenberg() {
       
        var editorWrapper = document.getElementById('editor');;
         var kctaiDivnew = document.getElementById('kctai');
        var kctaiDiv = kctaiDivnew;
        var postStatusInfo =  document.getElementById('editor');
        postStatusInfo.parentNode.insertBefore(kctaiDiv, postStatusInfo);
    
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


var editorType = detectEditor();

if(editorType=='gutenberg'){
    addAskAIButtonToGutenberg();
    positionKctaiDivForGutenberg();
}




