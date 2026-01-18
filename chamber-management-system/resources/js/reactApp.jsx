import React from "react";
import ReactDOM from "react-dom/client";

import PatientSelect from "./components/PatientSelect";
import FamilyMembersSelect from "./components/FamilyMembersSelect";

/**
 * Mount single patient selector
 */
function mountSingle(id, inputName) {
  const el = document.getElementById(id);
  if (!el) return;

  const patients = JSON.parse(el.dataset.patients || "[]");
  const oldValue = el.dataset.old || null;

  ReactDOM.createRoot(el).render(
    <PatientSelect
      patients={patients}
      name={inputName}
      defaultValue={oldValue}
    />
  );
}

/**
 * Mount family members multi selector
 */
function mountMembers(id) {
  const el = document.getElementById(id);
  if (!el) return;

  const patients = JSON.parse(el.dataset.patients || "[]");
  const oldValues = JSON.parse(el.dataset.old || "[]");

  ReactDOM.createRoot(el).render(
    <FamilyMembersSelect
      patients={patients}
      defaultValues={oldValues}
    />
  );
}

/* =========================
   Mount points
========================= */

// Family Head
mountSingle("family_head_react", "head_patient_id");

// Referred By Patient
mountSingle("referred_by_patient_react", "referred_by_patient_id");

// Family Members (MULTI)
mountMembers("family_members_react");
