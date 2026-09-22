import React from "react";
import { useTranslation } from "react-i18next";
import { router } from "@inertiajs/react";

const languages = [
    { code: 'en', 'label': 'English' },
    { code: 'tr', 'label': 'Turkey' }
];

export default function LanguageSwitcher() {
    const {t, i18n} = useTranslation();

    const changeLanguage = (e) => {
        const selectedLang = e.target.value;
        i18n.changeLanguage(selectedLang);

        localStorage.setItem('lang', selectedLang);

        router.get(window.location.pathname, {lang: selectedLang}, {
            replace: true,
            preserveScroll: true,
            preserveState: true
        });
    }

    return (
        <div className="flex items-center space-x-2">
            <label htmlFor="lang-select" className="text-sm">
                {t('language')}:
            </label>
            <select
                id="lang-select"
                onChange={changeLanguage}
                value={i18n.language}
                className="border px-2 py-1 rounded text-sm"
            >
                {languages.map((lang) => (
                    <option key={lang.code} value={lang.code}>
                        {lang.label}
                    </option>
                ))}
            </select>
        </div>
    )
}