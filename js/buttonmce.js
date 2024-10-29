tinymce.PluginManager.add('aikct_ask', function(editor, url) {
    // Button aikct_ask
    editor.addButton('aikct_ask', {
        type: 'button',
        text: 'KCT',
        icon: 'aikct_mce-icon',
        onclick: function() {
            var kctaiDiv = document.getElementById('kctai');
            if (kctaiDiv.style.display === 'none' || kctaiDiv.style.display === '') {
                kctaiDiv.style.display = 'block'; 
            } else {
                kctaiDiv.style.display = 'none'; 
            }
            console.log('AI KCT button clicked');
        }
    });

    // Menubutton aikct_ask_pro
    editor.addButton('aikct_ask_pro', {
        type: 'menubutton',
        text: 'KCT',
        id:'btne',
        icon: 'aikct_mce-icon-pro',
        menu: [
            {
                text: 'Option 1',
                onclick: function() {
                    console.log('Option 1 selected');
                    // Add your option-specific logic here
                }
            },
            {
                text: 'Option 2',
                onclick: function() {
                    console.log('Option 2 selected');
                    // Add your option-specific logic here
                }
            }
            // You can add more options here
        ]
    });
});

tinymce.init({
    selector: 'content',  // Your specific selector
    plugins: 'aikct_ask ',  // Register your plugins
    toolbar: 'aikct_ask aikct_ask_pro'  // Add buttons to the toolbar
});
