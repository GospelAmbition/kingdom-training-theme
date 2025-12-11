/**
 * Language Selector JavaScript
 * 
 * Handles language selector dropdown functionality
 */

(function() {
    'use strict';

    const languageDropdowns = document.querySelectorAll('.kt-language-dropdown');

    languageDropdowns.forEach(function(dropdown) {
        // Update display text when selection changes
        dropdown.addEventListener('change', function(e) {
            const url = e.target.value;
            if (url) {
                window.location.href = url;
            }
        });
        
        // Update the display text based on selected option
        const wrapper = dropdown.closest('.kt-language-dropdown-wrapper');
        if (wrapper) {
            const updateDisplay = function() {
                const selectedOption = dropdown.options[dropdown.selectedIndex];
                if (selectedOption) {
                    // Extract language code from option text or URL
                    const optionText = selectedOption.text.trim();
                    // Try to get 2-letter code from common language names
                    let langCode = 'EN';
                    const langMap = {
                        'english': 'EN',
                        'español': 'ES',
                        'spanish': 'ES',
                        'português': 'PT',
                        'portuguese': 'PT',
                        'العربية': 'AR',
                        'arabic': 'AR',
                        'français': 'FR',
                        'french': 'FR',
                    };
                    const lowerText = optionText.toLowerCase();
                    for (const [key, code] of Object.entries(langMap)) {
                        if (lowerText.includes(key)) {
                            langCode = code;
                            break;
                        }
                    }
                    wrapper.setAttribute('data-display', langCode);
                }
            };
            
            updateDisplay();
            dropdown.addEventListener('focus', updateDisplay);
        }
    });
})();
