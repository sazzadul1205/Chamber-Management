import React, { useState } from "react";
import Select from "react-select";

export default function PatientSelect({
  patients = [],
  name,
  defaultValue = null,
  placeholder = "Select patient"
}) {
  const options = patients.map(p => ({
    value: p.id,
    label: `${p.full_name} (${p.patient_code})`,
  }));

  const [selected, setSelected] = useState(
    defaultValue
      ? options.find(o => String(o.value) === String(defaultValue))
      : null
  );

  return (
    <>
      <Select
        options={options}
        value={selected}
        onChange={setSelected}
        isClearable
        placeholder={placeholder}
      />

      {/* Laravel hidden input */}
      <input
        type="hidden"
        name={name}
        value={selected ? selected.value : ""}
      />
    </>
  );
}
