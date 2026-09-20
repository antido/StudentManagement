import { React, useState } from "react";
import Sidebar from "@/Components/Sidebar";
import { useTranslation } from "react-i18next";
import LanguageSwitcher from "@/Components/LanguageSwitcher";

export default function DashboardLayout({ children }) {
    const [mountedAt] = useState(new Date().toLocaleTimeString());
    const {t, i18n} = useTranslation();

    return (
        <div className="flex">
            <Sidebar />
            <main className="flex-1">
                <header className="bg-white shadow p-4">
                    {t(`Topbar (mounted at ${mountedAt})`)}
                    <LanguageSwitcher />
                </header>
                <section className="p4">{children}</section>
            </main>
        </div>
    )
}