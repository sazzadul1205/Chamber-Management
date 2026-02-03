import "./bootstrap";
import Alpine from "alpinejs";
import React from "react";
import { createRoot } from "react-dom/client";
import PatientSelect from "./components/PatientSelect";

window.Alpine = Alpine;
Alpine.start();

// Mount the Referred By React component
document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("referred-by-select");
    if (!el) return;

    const oldValue = el.dataset.old ? parseInt(el.dataset.old) : null;
    const root = createRoot(el);

    root.render(
        <PatientSelect
            oldValue={oldValue}
            onChange={(val) =>
                (document.getElementById("referred_by_hidden").value = val)
            }
        />,
    );
});
