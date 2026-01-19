// src/components/DentalTeethSelect.jsx

import React, { useState } from "react";
import Select from "react-select";

/* =========================
   STATIC DATA (INSIDE)
========================= */

const TEETH = [
  { number: "11", label: "Upper Right Central Incisor" },
  { number: "12", label: "Upper Right Lateral Incisor" },
  { number: "13", label: "Upper Right Canine" },
  { number: "14", label: "Upper Right First Premolar" },
  { number: "15", label: "Upper Right Second Premolar" },
  { number: "16", label: "Upper Right First Molar" },
  { number: "17", label: "Upper Right Second Molar" },
  { number: "18", label: "Upper Right Third Molar" },

  { number: "21", label: "Upper Left Central Incisor" },
  { number: "22", label: "Upper Left Lateral Incisor" },
  { number: "23", label: "Upper Left Canine" },
  { number: "24", label: "Upper Left First Premolar" },
  { number: "25", label: "Upper Left Second Premolar" },
  { number: "26", label: "Upper Left First Molar" },
  { number: "27", label: "Upper Left Second Molar" },
  { number: "28", label: "Upper Left Third Molar" },

  { number: "31", label: "Lower Left Central Incisor" },
  { number: "32", label: "Lower Left Lateral Incisor" },
  { number: "33", label: "Lower Left Canine" },
  { number: "34", label: "Lower Left First Premolar" },
  { number: "35", label: "Lower Left Second Premolar" },
  { number: "36", label: "Lower Left First Molar" },
  { number: "37", label: "Lower Left Second Molar" },
  { number: "38", label: "Lower Left Third Molar" },

  { number: "41", label: "Lower Right Central Incisor" },
  { number: "42", label: "Lower Right Lateral Incisor" },
  { number: "43", label: "Lower Right Canine" },
  { number: "44", label: "Lower Right First Premolar" },
  { number: "45", label: "Lower Right Second Premolar" },
  { number: "46", label: "Lower Right First Molar" },
  { number: "47", label: "Lower Right Second Molar" },
  { number: "48", label: "Lower Right Third Molar" }
];

const CONDITIONS = [
  "Healthy",
  "Cavity",
  "Filled",
  "Crown",
  "Missing",
  "Implant",
  "Root Canal",
  "Decay",
  "Fractured",
  "Discolored",
  "Sensitive",
  "Other"
];

/* =========================
   COMPONENT
========================= */

export default function DentalTeethSelect({
  defaultValues = [],
  name = "charts"
}) {
  // Map old() values
  const initialSelections = defaultValues.map(d => {
    const tooth = TEETH.find(t => t.number === d.tooth_number);
    return {
      tooth: tooth || { number: d.tooth_number, label: d.tooth_number },
      condition: d.tooth_condition
    };
  });

  const [selections, setSelections] = useState(initialSelections);
  const [selectedTooth, setSelectedTooth] = useState(null);
  const [selectedCondition, setSelectedCondition] = useState(null);

  const toothOptions = TEETH
    .filter(t => !selections.find(s => s.tooth.number === t.number))
    .map(t => ({ value: t.number, label: t.label }));

  const conditionOptions = CONDITIONS.map(c => ({ value: c, label: c }));

  const addSelection = () => {
    if (!selectedTooth || !selectedCondition) return;

    setSelections([
      ...selections,
      {
        tooth: { number: selectedTooth.value, label: selectedTooth.label },
        condition: selectedCondition.value
      }
    ]);

    setSelectedTooth(null);
    setSelectedCondition(null);
  };

  const removeSelection = (toothNumber) => {
    setSelections(selections.filter(s => s.tooth.number !== toothNumber));
  };

  return (
    <div>
      {/* Selectors */}
      <div className="d-flex gap-2 mb-3">
        <div style={{ flex: 1 }}>
          <Select
            options={toothOptions}
            value={selectedTooth}
            onChange={setSelectedTooth}
            placeholder="Select Tooth"
          />
        </div>

        <div style={{ flex: 1 }}>
          <Select
            options={conditionOptions}
            value={selectedCondition}
            onChange={setSelectedCondition}
            placeholder="Select Condition"
          />
        </div>

        <button type="button" className="btn btn-success" onClick={addSelection}>
          <i className="bi bi-plus-lg"></i>
        </button>
      </div>

      {/* Selected teeth */}
      {selections.map((sel, index) => (
        <div
          key={sel.tooth.number}
          className="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2"
        >
          <span>
            Tooth {sel.tooth.number} ({sel.tooth.label}) — {sel.condition}
          </span>

          <button
            type="button"
            className="btn btn-sm btn-danger"
            onClick={() => removeSelection(sel.tooth.number)}
          >
            <i className="bi bi-trash"></i>
          </button>

          {/* Hidden inputs */}
          <input type="hidden" name={`${name}[${index}][tooth_number]`} value={sel.tooth.number} />
          <input type="hidden" name={`${name}[${index}][tooth_condition]`} value={sel.condition} />
        </div>
      ))}
    </div>
  );
}
