import {useEffect, useState} from 'react';
import SettingsPage from './components/SettingsPage';

export default function App() {
    const {restUrl, restNonce} = window.xpubSettings || {};
    const [settings, setSettings] = useState(null);

    useEffect(() => {
        if (!restUrl || !restNonce) {
            return;
        }
        const base = restUrl.replace(/\/$/, '');
        fetch(`${base}/xpub/v1/settings`, {
            headers: {
                'X-WP-Nonce': restNonce,
            },
        })
            .then(res => res.json())
            .then(data => setSettings({...data, restUrl}));
    }, [restUrl, restNonce]);

    if (!settings) {
        return null;
    }
    return <SettingsPage {...settings} />;
}

