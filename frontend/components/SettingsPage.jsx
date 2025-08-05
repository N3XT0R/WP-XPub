import {useEffect, useState} from 'react';
import {__} from '@wordpress/i18n';

export function SettingsPage({
                                 publishers = [],
                                 activePublisherSlugs = [],
                                 nonce = '',
                                 actionUrl = '',
                                 restUrl = '',
                                 restNonce = ''
                             }) {
    const [active, setActive] = useState(new Set(activePublisherSlugs));
    const [config, setConfig] = useState(() => {
        const initial = {};
        publishers.forEach(pub => {
            initial[pub.slug] = {};
            Object.values(pub.config || {}).forEach(group => {
                Object.entries(group).forEach(([key, value]) => {
                    initial[pub.slug][key] = value;
                });
            });
        });
        return initial;
    });

    const [oauthStatus, setOauthStatus] = useState({});

    useEffect(() => {
        const base = restUrl.replace(/\/$/, '');

        publishers.forEach(publisher => {
            fetch(`${base}/xpub/v1/oauth/${encodeURIComponent(publisher.slug)}/status`, {
                headers: {
                    'X-WP-Nonce': restNonce,
                },
            })
                .then(res => res.json())
                .then(data => {
                    setOauthStatus(prev => ({
                        ...prev,
                        [publisher.slug]: data.connected
                    }));
                });
        });
    }, [publishers, restUrl]);

    const togglePublisher = slug => {
        setActive(prev => {
            const next = new Set(prev);
            if (next.has(slug)) {
                next.delete(slug);
            } else {
                next.add(slug);
            }
            return next;
        });
    };

    const handleConfigChange = (slug, key, value) => {
        setConfig(prev => ({
            ...prev,
            [slug]: {...prev[slug], [key]: value}
        }));
    };

    const startOAuth = async (slug) => {
        const base = restUrl.replace(/\/$/, '');
        const popup = window.open('', '_blank', 'width=600,height=700');

        if (!popup) {
            alert(__('Popup blocked. Please allow popups and try again.', 'xpub-multi-channel-publisher'));
            return;
        }

        popup.document.write(`<p>${__('Saving configuration and starting OAuth…', 'xpub-multi-channel-publisher')}</p>`);

        // 1. Konfig speichern
        const formData = new FormData();
        formData.append('action', 'xpub_save_settings');
        formData.append('_wpnonce', nonce);
        active.forEach(slug => formData.append('active_publishers[]', slug));
        Object.entries(config).forEach(([slug, values]) => {
            Object.entries(values).forEach(([key, value]) => {
                formData.append(`config[${slug}][${key}]`, value);
            });
        });

        try {
            const saveRes = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
            });

            if (!saveRes.ok) {
                throw new Error(`Settings save failed: ${saveRes.status}`);
            }

            // 2. OAuth starten
            const res = await fetch(`${base}/xpub/v1/oauth/${encodeURIComponent(slug)}/start`, {
                headers: {'X-WP-Nonce': restNonce},
            });

            if (!res.ok) throw new Error(`OAuth start failed: ${res.status}`);
            const data = await res.json();

            if (data.url) {
                popup.location.href = data.url;
            } else {
                popup.document.write(`<p>${__('No redirect URL received.', 'xpub-multi-channel-publisher')}</p>`);
            }

            // 3. Polling starten
            const pollInterval = setInterval(() => {
                fetch(`${base}/xpub/v1/oauth/${encodeURIComponent(slug)}/status`, {
                    headers: {'X-WP-Nonce': restNonce},
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.connected) {
                            clearInterval(pollInterval);
                            setOauthStatus(prev => ({
                                ...prev,
                                [slug]: true
                            }));
                            alert(__('OAuth successful!', 'xpub-multi-channel-publisher'));
                            popup.close();
                        }
                    });
            }, 1000);

            setTimeout(() => clearInterval(pollInterval), 5 * 60 * 1000);
        } catch (err) {
            console.error('OAuth error:', err);
            popup.document.write(`<p>${__('OAuth process failed. See console for details.', 'xpub-multi-channel-publisher')}</p>`);
        }
    };


    const renderGroup = (publisher, purposeType, group) => {
        const heading =
            purposeType === 'oauth'
                ? `${__('OAuth', 'xpub-multi-channel-publisher')} ${__('Settings', 'xpub-multi-channel-publisher')}`
                : `${purposeType.charAt(0).toUpperCase() + purposeType.slice(1)} ${__('Settings', 'xpub-multi-channel-publisher')}`;

        return (
            <div key={purposeType} className="mt-6">
                <h4 className="font-semibold mb-4">{heading}</h4>
                {Object.entries(group).map(([key, value]) => {
                    const inputId = `config_${publisher.slug}_${purposeType}_${key}`;
                    let type = 'text';
                    if (purposeType === 'oauth') {
                        if (key === 'clientSecret') type = 'password';
                        if (['redirectUri', 'urlAccessToken', 'urlAuthorize', 'urlResourceOwnerDetails'].includes(key)) {
                            type = 'url';
                        }
                    }
                    const labels = {
                        api_key: __('API Key', 'xpub-multi-channel-publisher'),
                        clientId: __('Client ID', 'xpub-multi-channel-publisher'),
                        clientSecret: __('Client Secret', 'xpub-multi-channel-publisher'),
                        redirectUri: __('Redirect URI', 'xpub-multi-channel-publisher'),
                        urlAccessToken: __('Access Token URL', 'xpub-multi-channel-publisher'),
                        urlAuthorize: __('Authorize URL', 'xpub-multi-channel-publisher'),
                        urlResourceOwnerDetails: __('Resource Owner URL', 'xpub-multi-channel-publisher'),
                        scopes: __('Scopes', 'xpub-multi-channel-publisher'),
                        grant_type: __('Grant Type', 'xpub-multi-channel-publisher'),
                        // usw.
                    };


                    return (
                        <div key={key} className="mb-4">
                            <label htmlFor={inputId} className="block font-bold mb-1">
                                {labels[key] || key}
                            </label>
                            <input
                                type={type}
                                id={inputId}
                                name={`config[${publisher.slug}][${key}]`}
                                value={config[publisher.slug]?.[key] ?? ''}
                                onChange={e => handleConfigChange(publisher.slug, key, e.target.value)}
                                className="w-full max-w-md border rounded p-1"
                                autoComplete={key === 'clientSecret' ? 'off' : undefined}
                            />
                        </div>
                    );
                })}
                {purposeType === 'oauth' && group.clientId && (
                    <div className="mt-4 flex items-center gap-3">
                        <button
                            type="button"
                            className="button button-secondary"
                            onClick={() => startOAuth(publisher.slug)}
                            disabled={oauthStatus[publisher.slug]}
                        >
                            {__('Authenticate with', 'xpub-multi-channel-publisher')} {publisher.name}
                        </button>
                        {oauthStatus[publisher.slug] && (
                            <span className="text-green-600 font-semibold">
                                ✓ {__('Connected', 'xpub-multi-channel-publisher')}
                            </span>
                        )}
                    </div>
                )}
            </div>
        );
    };

    return (
        <form method="post" action={actionUrl} className="p-4">
            <input type="hidden" name="action" value="xpub_save_settings"/>
            <input type="hidden" name="_wpnonce" value={nonce}/>

            <h2 className="text-xl font-bold mb-4">{__('Activate Publisher', 'xpub-multi-channel-publisher')}</h2>
            <fieldset className="mb-8">
                <legend className="mb-2">{__('Select active publishers:', 'xpub-multi-channel-publisher')}</legend>
                {publishers.map(publisher => {
                    const id = `publisher_${publisher.slug}`;
                    return (
                        <label key={publisher.slug} htmlFor={id} className="block mb-2">
                            <input
                                type="checkbox"
                                id={id}
                                name="active_publishers[]"
                                value={publisher.slug}
                                checked={active.has(publisher.slug)}
                                onChange={() => togglePublisher(publisher.slug)}
                                className="mr-2"
                            />
                            {publisher.name}
                        </label>
                    );
                })}
            </fieldset>

            <h2 className="text-xl font-bold mb-4">{__('Configuration', 'xpub-multi-channel-publisher')}</h2>
            {publishers
                .filter(publisher => active.has(publisher.slug))
                .map(publisher => (
                    <fieldset key={publisher.slug} className="mt-8 p-4 border border-gray-300">
                        <legend className="font-bold">
                            {publisher.name} {__('Configuration', 'xpub-multi-channel-publisher')}
                        </legend>
                        {Object.entries(publisher.config || {}).map(([purposeType, group]) =>
                            renderGroup(publisher, purposeType, group)
                        )}
                    </fieldset>
                ))}

            <p className="mt-8">
                <button type="submit" className="button button-primary">
                    {__('Save settings', 'xpub-multi-channel-publisher')}
                </button>
            </p>
        </form>
    );
}

export default SettingsPage;
