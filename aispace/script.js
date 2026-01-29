// Language detection and management
let currentLanguage = 'en'; // Default language

// Detect browser language
function detectBrowserLanguage() {
    const browserLang = navigator.language || navigator.userLanguage;
    const langCode = browserLang.split('-')[0].toLowerCase();
    
    // Check if browser language is supported (vi, ru, en)
    if (langCode === 'vi' || langCode === 'ru') {
        return langCode;
    }
    
    // Default to English
    return 'en';
}

// Initialize language on page load
function initLanguage() {
    // Check if language is stored in localStorage
    const savedLanguage = localStorage.getItem('preferredLanguage');
    
    if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'vi' || savedLanguage === 'ru')) {
        currentLanguage = savedLanguage;
    } else {
        // Use browser language detection
        currentLanguage = detectBrowserLanguage();
        localStorage.setItem('preferredLanguage', currentLanguage);
    }
    
    // Set language selector
    document.getElementById('languageSelect').value = currentLanguage;
    
    // Translate page
    translatePage(currentLanguage);
}

// Translate page content
function translatePage(lang) {
    currentLanguage = lang;
    localStorage.setItem('preferredLanguage', lang);
    
    // Update HTML lang attribute
    document.documentElement.lang = lang;
    
    // Get all elements with data-translate attribute
    const elements = document.querySelectorAll('[data-translate]');
    
    elements.forEach(element => {
        const key = element.getAttribute('data-translate');
        const translation = getTranslation(key, lang);
        
        if (translation !== null && translation !== undefined) {
            // Handle nested objects (like contact.address.label)
            if (typeof translation === 'object') {
                // For nested translations with label/value structure
                if (translation.label !== undefined && translation.value !== undefined) {
                    // This is for contact section
                    const parts = key.split('.');
                    const lastPart = parts[parts.length - 1];
                    if (lastPart === 'label') {
                        element.textContent = translation.label;
                    } else if (lastPart === 'value') {
                        element.textContent = translation.value;
                    }
                } else {
                    // For other nested objects, try to get a string representation
                    // This shouldn't happen with our current structure, but handle it gracefully
                    element.textContent = '';
                }
            } else {
                // Simple string translation
                element.textContent = translation;
            }
        }
    });
}

// Get translation by key path
function getTranslation(key, lang) {
    const keys = key.split('.');
    let value = translations[lang];
    
    if (!value) {
        return null;
    }
    
    for (const k of keys) {
        if (value && typeof value === 'object' && k in value) {
            value = value[k];
        } else {
            return null;
        }
    }
    
    // Return the final value (should be a string for most cases)
    return value;
}

// Language selector change handler
document.addEventListener('DOMContentLoaded', function() {
    initLanguage();
    
    // Add event listener for language selector
    const languageSelect = document.getElementById('languageSelect');
    if (languageSelect) {
        languageSelect.addEventListener('change', function(e) {
            translatePage(e.target.value);
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

