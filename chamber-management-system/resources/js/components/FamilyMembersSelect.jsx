import React, { useState } from "react";
import Select from "react-select";

export default function FamilyMembersSelect({ patients, defaultValues }) {
  const options = patients.map(p => ({
    value: p.id,
    label: `${p.full_name} (${p.patient_code})`,
  }));

  const [selected, setSelected] = useState(null);
  const [members, setMembers] = useState(
    options.filter(o => defaultValues.includes(o.value))
  );

  const addMember = () => {
    if (!selected) return;
    if (members.find(m => m.value === selected.value)) return;

    setMembers([...members, selected]);
    setSelected(null);
  };

  const removeMember = (id) => {
    setMembers(members.filter(m => m.value !== id));
  };

  // Filter out already selected members from dropdown
  const availableOptions = options.filter(
    o => !members.find(m => m.value === o.value)
  );

  return (
    <div>
      {/* Selector + Add button */}
      <div className="d-flex gap-2 mb-3">
        <div style={{ flex: 1 }}>
          <Select
            options={availableOptions} // use filtered options
            value={selected}
            onChange={setSelected}
            placeholder="Select member"
          />
        </div>
        <button
          type="button"
          className="btn btn-success"
          onClick={addMember}
        >
          <i className="bi bi-plus-lg"></i>
        </button>
      </div>

      {/* Selected Members List */}
      {members.map(member => (
        <div
          key={member.value}
          className="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2"
        >
          <span>{member.label}</span>

          <button
            type="button"
            className="btn btn-sm btn-danger"
            onClick={() => removeMember(member.value)}
          >
            <i className="bi bi-trash"></i>
          </button>

          {/* Hidden input for Laravel */}
          <input
            type="hidden"
            name="member_patient_ids[]"
            value={member.value}
          />
        </div>
      ))}
    </div>
  );
}
