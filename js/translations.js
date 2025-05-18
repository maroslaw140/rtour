let currentLanguage = 'pl';
let translations = {};

async function loadTranslations(lang) {
    try {
        const response = await fetch(`/lang/${lang}.json`);
        translations[lang] = await response.json();
        return translations[lang];
    } catch (error) {
        console.error(`Error loading ${lang} translations:`, error);
        return {};
    }
}

async function changeLanguage(lang) {
    if (!translations[lang]) {
        await loadTranslations(lang);
    }
    currentLanguage = lang;
    localStorage.setItem('preferredLanguage', lang);
    applyTranslations();
    updateLanguageSwitcher();
}

function applyTranslations() {
    document.querySelectorAll('[data-i18n]').forEach(element => {
        const keys = element.getAttribute('data-i18n').split('.');
        let value = translations[currentLanguage];

        keys.forEach(key => {
            value = value?.[key];
        });

        if (value !== undefined) {
            element.textContent = value;
        }
    });
    document.documentElement.lang = currentLanguage;
}

function updateLanguageSwitcher() {
    document.querySelectorAll('.language-switcher').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-lang') === currentLanguage) {
            btn.classList.add('active');
        }
    });
}

document.querySelectorAll('.language-switcher').forEach(btn => {
    btn.addEventListener('click', () => {
        const lang = btn.getAttribute('data-lang');
        changeLanguage(lang);
    });
});

// init
document.addEventListener('DOMContentLoaded', async () => {
    const savedLang = localStorage.getItem('preferredLanguage') || 'pl';
    await changeLanguage(savedLang);
});