import React, { useState } from "react";
import { FiShield, FiTool, FiStar, FiTarget, FiCreditCard, FiDollarSign, FiBriefcase, FiInfo } from "react-icons/fi";

const PricingSection_2 = () => {
  const [activeCategory, setActiveCategory] = useState("preventive");

  const categories = [
    { id: "preventive", name: "Preventive Care", icon: <FiShield size={24} /> },
    { id: "restorative", name: "Restorative", icon: <FiTool size={24} /> },
    { id: "cosmetic", name: "Cosmetic", icon: <FiStar size={24} /> },
    { id: "specialized", name: "Specialized", icon: <FiTarget size={24} /> }
  ];

  const services = {
    preventive: [
      { name: "Dental Exam", price: "$75", description: "Comprehensive oral examination" },
      { name: "Teeth Cleaning", price: "$120", description: "Professional scaling and polishing" },
      { name: "Digital X-rays", price: "$45", description: "Full set of diagnostic images" },
      { name: "Fluoride Treatment", price: "$35", description: "Professional fluoride application" },
      { name: "Dental Sealants", price: "$55", description: "Per tooth, for cavity prevention" }
    ],
    restorative: [
      { name: "Tooth-colored Filling", price: "$150", description: "Composite resin restoration" },
      { name: "Dental Crown", price: "$800", description: "Porcelain fused to metal" },
      { name: "Root Canal Therapy", price: "$750", description: "Anterior tooth" },
      { name: "Tooth Extraction", price: "$180", description: "Simple extraction" },
      { name: "Dentures", price: "$1,500", description: "Complete set, starting from" }
    ],
    cosmetic: [
      { name: "Teeth Whitening", price: "$299", description: "In-office professional whitening" },
      { name: "Porcelain Veneers", price: "$950", description: "Per tooth, custom-made" },
      { name: "Dental Bonding", price: "$250", description: "Per tooth, for minor repairs" },
      { name: "Gum Contouring", price: "$600", description: "Per area, for smile enhancement" },
      { name: "Smile Makeover", price: "$5,000+", description: "Complete transformation" }
    ],
    specialized: [
      { name: "Dental Implant", price: "$2,500", description: "Including crown" },
      { name: "Invisalign", price: "$3,999", description: "Complete treatment" },
      { name: "Wisdom Teeth Removal", price: "$250", description: "Per tooth, simple" },
      { name: "Sleep Apnea Device", price: "$1,800", description: "Custom oral appliance" },
      { name: "TMJ Therapy", price: "$1,200", description: "Complete treatment program" }
    ]
  };

  const insurancePlans = [
    { name: "Delta Dental", coverage: "In-network", icon: <FiShield size={28} /> },
    { name: "Cigna", coverage: "Preferred Provider", icon: <FiStar size={28} /> },
    { name: "Aetna", coverage: "In-network", icon: <FiTarget size={28} /> },
    { name: "MetLife", coverage: "Preferred Provider", icon: <FiBriefcase size={28} /> },
    { name: "UnitedHealthcare", coverage: "In-network", icon: <FiDollarSign size={28} /> },
    { name: "Blue Cross", coverage: "Preferred Provider", icon: <FiCreditCard size={28} /> }
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-12">
          <div className="inline-block mb-4">
            <span className="text-blue-600 font-semibold tracking-wider text-sm">CLEAR PRICING</span>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Transparent <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Service Pricing</span>
          </h2>
          <p className="text-gray-600 text-lg max-w-2xl mx-auto">
            No surprises. See our comprehensive pricing for all dental services.
            All prices are estimates - actual costs may vary based on individual needs.
          </p>
        </div>

        {/* Category Tabs */}
        <div className="flex flex-wrap justify-center gap-4 mb-12">
          {categories.map((category) => (
            <button
              key={category.id}
              onClick={() => setActiveCategory(category.id)}
              className={`flex items-center gap-3 px-6 py-3 rounded-xl transition-all duration-300 ${activeCategory === category.id
                ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-xl scale-105'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'
                }`}
            >
              {category.icon}
              <span className="font-semibold">{category.name}</span>
            </button>
          ))}
        </div>

        {/* Services Table */}
        <div className="bg-white rounded-2xl shadow-2xl overflow-hidden mb-12 border border-gray-200">
          <div className="p-8">
            <h3 className="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
              <span className="text-blue-600">{categories.find(c => c.id === activeCategory)?.icon}</span>
              {categories.find(c => c.id === activeCategory)?.name} Services
            </h3>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="py-4 px-6 text-left text-gray-700 font-semibold">Service</th>
                    <th className="py-4 px-6 text-left text-gray-700 font-semibold">Price</th>
                    <th className="py-4 px-6 text-left text-gray-700 font-semibold">Description</th>
                    <th className="py-4 px-6 text-left text-gray-700 font-semibold">Avg. Time</th>
                  </tr>
                </thead>
                <tbody>
                  {services[activeCategory].map((service, index) => (
                    <tr key={index} className="border-b border-gray-100 hover:bg-blue-50 transition-colors">
                      <td className="py-4 px-6 font-medium text-gray-800">{service.name}</td>
                      <td className="py-4 px-6">
                        <div className="text-2xl font-bold text-blue-600">{service.price}</div>
                      </td>
                      <td className="py-4 px-6 text-gray-600">{service.description}</td>
                      <td className="py-4 px-6">
                        {index % 3 === 0 ? "30-45 min" : index % 3 === 1 ? "60-90 min" : "2+ visits"}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Custom Quote Notice */}
            <div className="mt-8 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <FiInfo size={24} className="text-blue-600" />
              </div>
              <div>
                <h4 className="font-bold text-gray-800 mb-1">Need a Custom Quote?</h4>
                <p className="text-gray-600">
                  These are general prices. For an exact quote tailored to your needs,
                  schedule a consultation with our team.
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Insurance Section */}
        <div className="mb-12">
          <h3 className="text-2xl font-bold text-gray-800 mb-8 text-center">
            Accepted Insurance Plans
          </h3>

          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            {insurancePlans.map((plan, index) => (
              <div key={index} className="bg-white rounded-xl p-4 border border-gray-200 text-center hover:border-blue-300 shadow-sm transition-all">
                <div className="text-3xl mb-2">{plan.icon}</div>
                <h4 className="font-bold text-gray-800 mb-1">{plan.name}</h4>
                <span className="text-sm text-green-600 font-medium">{plan.coverage}</span>
              </div>
            ))}
          </div>
        </div>

      </div>
    </div>
  );
};

export default PricingSection_2;
