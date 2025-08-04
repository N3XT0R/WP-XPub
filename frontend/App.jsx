import SettingsPage from './components/SettingsPage';

export default function App() {
  const settings = window.xpubSettings || {};
  return <SettingsPage {...settings} />;
}

