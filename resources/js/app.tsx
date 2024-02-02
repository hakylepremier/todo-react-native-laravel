import "./bootstrap";
import "../css/app.css";
import "@fontsource/roboto/300.css";
import "@fontsource/roboto/400.css";
import "@fontsource/roboto/500.css";
import "@fontsource/roboto/700.css";

import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob(["./Pages/**/*.tsx", "../images/**"])
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            // <ThemeProvider>
            <App {...props} />
            // </ThemeProvider>
        );
    },
    progress: {
        color: "#4B5563",
    },
});
