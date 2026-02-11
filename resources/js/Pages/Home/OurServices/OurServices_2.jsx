import React, { useState } from "react";
import { FaShieldAlt, FaSmile, FaSyncAlt, FaBullseye, FaClock, FaPhone, FaCreditCard } from "react-icons/fa";

const OurServices_2 = () => {
  const [activeTab, setActiveTab] = useState(0);

  const serviceCategories = [
    {
      category: "Preventive Care",
      icon: <FaShieldAlt className="text-3xl text-blue-600" />,
      services: [
        { title: "Dental Checkups", description: "Comprehensive exams including oral cancer screening and digital X-rays", duration: "30-60 min", price: "From $89" },
        { title: "Teeth Cleaning", description: "Professional scaling and polishing to remove plaque and stains", duration: "45-60 min", price: "From $120" },
        { title: "Fluoride Treatment", description: "Strengthens tooth enamel and prevents cavities", duration: "15 min", price: "From $35" }
      ]
    },
    {
      category: "Cosmetic Dentistry",
      icon: <FaSmile className="text-3xl text-purple-600" />,
      services: [
        { title: "Teeth Whitening", description: "Professional-grade whitening for dramatic results in one visit", duration: "60-90 min", price: "From $299" },
        { title: "Porcelain Veneers", description: "Custom-made shells to transform your smile's appearance", duration: "2 visits", price: "From $950" },
        { title: "Dental Bonding", description: "Quick fix for chipped, cracked, or discolored teeth", duration: "30-60 min", price: "From $250" }
      ]
    },
    {
      category: "Restorative",
      icon: <FaSyncAlt className="text-3xl text-green-600" />,
      services: [
        { title: "Dental Crowns", description: "Custom-made caps to restore damaged or decayed teeth", duration: "2 visits", price: "From $800" },
        { title: "Root Canal Therapy", description: "Saves infected teeth and relieves pain", duration: "60-90 min", price: "From $750" },
        { title: "Dentures", description: "Custom removable replacements for missing teeth", duration: "4-6 weeks", price: "From $1,500" }
      ]
    },
    {
      category: "Specialized",
      icon: <FaBullseye className="text-3xl text-red-600" />,
      services: [
        { title: "Dental Implants", description: "Permanent solution for missing teeth with natural look and feel", duration: "3-6 months", price: "From $2,500" },
        { title: "Invisalign", description: "Clear aligners for discreet teeth straightening", duration: "12-18 months", price: "From $3,500" },
        { title: "Wisdom Teeth Removal", description: "Safe extraction of impacted or problematic wisdom teeth", duration: "45-90 min", price: "From $250" }
      ]
    }
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-12">
          <span className="text-blue-600 font-semibold tracking-wider text-sm inline-block mb-2">WHAT WE OFFER</span>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
            Our <span className="text-blue-600">Specialized</span> Services
          </h2>
          <p className="text-gray-600 text-lg max-w-2xl mx-auto">
            From routine care to complex procedures, we provide comprehensive dental solutions
            using the latest technology and techniques.
          </p>
        </div>

        {/* Category Tabs */}
        <div className="flex flex-wrap justify-center gap-4 mb-12">
          {serviceCategories.map((category, index) => (
            <button
              key={index}
              onClick={() => setActiveTab(index)}
              className={`flex items-center gap-3 px-6 py-4 rounded-xl transition-all duration-300 ${activeTab === index
                ? "bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-lg"
                : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-200"
                }`}
            >
              {category.icon}
              <span className="font-semibold">{category.category}</span>
            </button>
          ))}
        </div>

        {/* Services Content */}
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
          <div className="p-8 lg:p-12">

            {/* Category Header */}
            <div className="flex items-center gap-4 mb-8">
              <div className="w-16 h-16 rounded-2xl bg-gradient-to-r from-blue-100 to-cyan-100 flex items-center justify-center">
                {serviceCategories[activeTab].icon}
              </div>
              <div>
                <h3 className="text-2xl font-bold text-gray-800">{serviceCategories[activeTab].category}</h3>
                <p className="text-gray-600">Comprehensive treatments for optimal oral health</p>
              </div>
            </div>

            {/* Services Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              {serviceCategories[activeTab].services.map((service, index) => (
                <div
                  key={index}
                  className="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-200 hover:border-blue-200 transition-all duration-300 hover:shadow-lg"
                >
                  <h4 className="text-xl font-bold text-gray-800 mb-3">{service.title}</h4>
                  <p className="text-gray-600 mb-6">{service.description}</p>

                  <div className="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div className="flex items-center gap-2 text-gray-600">
                      <FaClock className="w-5 h-5 text-gray-400" />
                      <span className="text-sm">{service.duration}</span>
                    </div>
                    <div className="text-lg font-bold text-blue-600">{service.price}</div>
                  </div>

                  <button className="w-full mt-6 bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold py-3 rounded-lg transition-colors duration-300">
                    Learn More
                  </button>
                </div>
              ))}
            </div>
          </div>

          {/* Bottom Banner */}
          <div className="bg-gradient-to-r from-blue-50 to-cyan-50 p-8">
            <div className="flex flex-col lg:flex-row items-center justify-between gap-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                  <FaPhone className="text-xl text-blue-600" />
                </div>
                <div>
                  <h4 className="font-bold text-gray-800">Need Help Choosing?</h4>
                  <p className="text-gray-600 text-sm">Our team can recommend the best treatment for you</p>
                </div>
              </div>
              <a
                href="#contact"
                className="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 whitespace-nowrap"
              >
                Free Consultation
              </a>
            </div>
          </div>
        </div>

        {/* Insurance Notice */}
        <div className="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200 flex items-center gap-4">
          <div className="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <FaCreditCard className="text-xl text-emerald-600" />
          </div>
          <div>
            <h4 className="font-bold text-gray-800 mb-1">Insurance & Payment Options</h4>
            <p className="text-gray-600 text-sm">
              We accept most major insurance plans and offer flexible payment options including
              interest-free financing to make dental care accessible for everyone.
            </p>
          </div>
        </div>

      </div>
    </section>
  );
};

export default OurServices_2;
