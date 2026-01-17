import React, { useState } from "react";
import Select from "react-select";

export default function PatientSelect() {
  const el = document.getElementById("referred_by_patient_react");
  if (!el) return null;

  const patients = JSON.parse(el.dataset.patients);
  const oldValue = el.dataset.old;

  // Transform patients for React Select
  const options = patients.map(p => ({
    value: p.id,
    label: `${p.full_name} (${p.patient_code})`
  }));

  const [selected, setSelected] = useState(
    oldValue ? options.find(o => o.value == oldValue) : null
  );

  // Update hidden input for Laravel form submission
  const handleChange = (option) => {
    setSelected(option);
    const hiddenInput = document.getElementById("referred_by_patient_hidden");
    if (hiddenInput) hiddenInput.value = option ? option.value : "";
  };

  return (
    <div>
      <label className="form-label">Referred By Patient</label>
      <Select
        options={options}
        value={selected}
        onChange={handleChange}
        isClearable
        placeholder="Select Patient"
      />

      {/* Hidden input for Laravel */}
      <input
        type="hidden"
        name="referred_by_patient_id"
        id="referred_by_patient_hidden"
        value={selected ? selected.value : ""}
      />
    </div>
  );
}
