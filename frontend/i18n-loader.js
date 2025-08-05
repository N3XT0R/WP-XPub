import {setLocaleData} from '@wordpress/i18n';
// Starte sofort die App (synchroner Import)
import './main.jsx';

// Locale & Pfad aus globalem WP-Objekt
const locale = window?.xpubSettings?.locale || 'en_US';
const domain = 'xpub-multi-channel-publisher';
const baseUrl = window?.xpubSettings?.translationsBaseUrl || '';

(async () => {
    try {
        const response = await fetch(`${baseUrl}/${locale}.json`);
        if (!response.ok) throw new Error(`Translation for ${locale} not found`);
        const json = await response.json();
        setLocaleData(json, domain);
    } catch (err) {
        console.warn(`Could not load translation for locale "${locale}".`, err);
    }
})();

