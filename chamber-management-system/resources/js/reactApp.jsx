import React from "react";
import ReactDOM from "react-dom/client";
import PatientSelect from "./components/PatientSelect";

const el = document.getElementById("referred_by_patient_react");
if (el) {
  const root = ReactDOM.createRoot(el);
  root.render(<PatientSelect />);
}
