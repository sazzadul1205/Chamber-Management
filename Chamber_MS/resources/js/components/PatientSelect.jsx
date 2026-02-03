import React, { useState } from 'react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';

export default function PatientSelect({ oldValue, onChange }) {
  const [selected, setSelected] = useState(null);

  const loadOptions = (inputValue, callback) => {
    axios.get('/api/patients', { params: { search: inputValue } })
      .then(res => {
        const options = res.data.map(p => ({
          value: p.id,
          label: `${p.patient_code} - ${p.full_name} - (${p.phone})`,
        }));
        callback(options);
      });
  };

  const handleChange = (option) => {
    setSelected(option);
    onChange(option ? option.value : '');
  };

  // Tailwind style overrides for react-select
  const customStyles = {
    control: (provided) => ({
      ...provided,
      width: '100%',
      minHeight: '2.5rem',
      borderColor: '#D1D5DB', // border-gray-300
      borderRadius: '0.375rem', // rounded-md
      boxShadow: '0 1px 2px 0 rgba(0,0,0,0.05)', // shadow-sm
      '&:hover': {
        borderColor: '#3B82F6', // focus:ring-blue-500 on hover
      },
    }),
    menu: (provided) => ({
      ...provided,
      zIndex: 50, // ensure dropdown appears over other elements
    }),
    input: (provided) => ({
      ...provided,
      margin: 0,
      padding: 0,
    }),
    placeholder: (provided) => ({
      ...provided,
      color: '#6B7280', // text-gray-700 placeholder
    }),
  };

  return (


    <AsyncSelect
      cacheOptions
      defaultOptions
      loadOptions={loadOptions}
      value={selected}
      onChange={handleChange}
      isClearable
      placeholder="Search and select patient..."
      styles={customStyles}
    />

  );
}
