import React from 'react';
import ReactDOM from 'react-dom';
import App from './App.jsx';
import './index.css';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('xpub-settings-root');
    if (root) {
        ReactDOM.render(
            <React.StrictMode>
                <App/>
            </React.StrictMode>,
            root
        );
    } else {
        console.error('React root element not found');
    }
});