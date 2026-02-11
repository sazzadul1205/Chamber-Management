import React from "react";
import {
  FiUser,
  FiStar,
  FiClipboard,
  FiTarget,
  FiActivity,
  FiAward,
  FiShield,
  FiTrendingUp,
} from "react-icons/fi";

const OurDentist_2 = () => {
  const leadDentists = [
    {
      name: "Dr. Sarah Johnson",
      title: "Founder & Chief Dentist",
      specialty: "Cosmetic Dentistry",
      experience: "15 years",
      stats: [
        { value: "5000+", label: "Smiles Created" },
        { value: "15", label: "Years Experience" },
        { value: "100%", label: "Patient Satisfaction" },
      ],
      quote: "Every patient deserves a smile they're proud to share.",
      color: "from-blue-600 to-cyan-500",
      bgColor: "bg-blue-50",
    },
    {
      name: "Dr. Michael Chen",
      title: "Orthodontic Director",
      specialty: "Invisalign Specialist",
      experience: "12 years",
      stats: [
        { value: "1000+", label: "Invisalign Cases" },
        { value: "98%", label: "Success Rate" },
        { value: "5.0", label: "Rating" },
      ],
      quote: "Straight teeth are the foundation of a healthy smile.",
      color: "from-emerald-600 to-teal-500",
      bgColor: "bg-emerald-50",
    },
    {
      name: "Dr. Emily Rodriguez",
      title: "Pediatric Director",
      specialty: "Children's Dentistry",
      experience: "8 years",
      stats: [
        { value: "2000+", label: "Happy Kids" },
        { value: "0", label: "Fearful Patients" },
        { value: "99%", label: "Parents Recommend" },
      ],
      quote: "Making dental visits fun and fear-free for children.",
      color: "from-purple-600 to-pink-500",
      bgColor: "bg-purple-50",
    },
  ];

  const associateDentists = [
    {
      name: "Dr. James Wilson",
      title: "Periodontist",
      specialty: "Gum Disease Specialist",
      icon: <FiClipboard />,
    },
    {
      name: "Dr. Lisa Thompson",
      title: "Endodontist",
      specialty: "Root Canal Specialist",
      icon: <FiTarget />,
    },
    {
      name: "Dr. Robert Kim",
      title: "Oral Surgeon",
      specialty: "Surgical Dentistry",
      icon: <FiActivity />,
    },
    {
      name: "Dr. Amanda Lee",
      title: "General Dentist",
      specialty: "Preventive Care",
      icon: <FiShield />,
    },
    {
      name: "Dr. David Park",
      title: "Prosthodontist",
      specialty: "Crowns & Bridges",
      icon: <FiAward />,
    },
    {
      name: "Dr. Maria Santos",
      title: "Periodontist",
      specialty: "Dental Implants",
      icon: <FiTrendingUp />,
    },
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <span className="text-blue-600 font-semibold tracking-wider text-sm">
            OUR TEAM
          </span>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mt-4 mb-6">
            Exceptional <span className="text-blue-600">Dentists</span>, Exceptional Care
          </h2>
          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            A team of specialists delivering comprehensive dental care with
            decades of combined experience.
          </p>
        </div>

        {/* Lead Dentists */}
        <div className="max-w-6xl mx-auto mb-20">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {leadDentists.map((dentist, index) => (
              <div key={index} className="relative">
                {index === 1 && (
                  <div className="absolute -top-3 left-1/2 -translate-x-1/2 z-10">
                    <div className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg flex items-center gap-1">
                      <FiStar size={14} />
                      MOST REQUESTED
                    </div>
                  </div>
                )}

                <div
                  className={`bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 ${index === 1 ? "scale-105" : ""
                    }`}
                >
                  <div className={`h-32 bg-gradient-to-r ${dentist.color}`}></div>

                  <div className="px-6 pb-8 -mt-12">
                    {/* Avatar */}
                    <div className="flex justify-center mb-4">
                      <div className="w-24 h-24 rounded-2xl bg-white shadow-lg border-4 border-white flex items-center justify-center">
                        <FiUser className="text-gray-700 text-4xl" />
                      </div>
                    </div>

                    {/* Info */}
                    <div className="text-center mb-6">
                      <h3 className="text-2xl font-bold text-gray-800 mb-1">
                        {dentist.name}
                      </h3>
                      <p className="text-lg font-semibold text-blue-600 mb-1">
                        {dentist.title}
                      </p>
                      <p className="text-gray-600 mb-2">
                        {dentist.specialty} • {dentist.experience}
                      </p>

                      {/* Stats */}
                      <div className="flex justify-center gap-6 mt-4">
                        {dentist.stats.map((stat, idx) => (
                          <div key={idx} className="text-center">
                            <div className="text-xl font-bold text-gray-800">
                              {stat.value}
                            </div>
                            <div className="text-xs text-gray-500">
                              {stat.label}
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* Quote */}
                    <div className={`${dentist.bgColor} rounded-xl p-4 mb-6`}>
                      <p className="text-gray-700 text-sm italic">
                        "{dentist.quote}"
                      </p>
                    </div>

                    <button
                      className={`w-full bg-gradient-to-r ${dentist.color} hover:opacity-90 text-white font-semibold py-3 rounded-xl transition-all duration-300`}
                    >
                      Book Appointment
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Associate Dentists */}
        <div className="max-w-6xl mx-auto">
          <div className="flex items-center justify-between mb-8">
            <h3 className="text-2xl font-bold text-gray-800">
              Associate Dentists & Specialists
            </h3>
            <span className="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
              6+ Specialists Available
            </span>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            {associateDentists.map((dentist, index) => (
              <div
                key={index}
                className="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-6 text-center group"
              >
                <div className="relative">
                  <div className="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 text-blue-600 text-2xl">
                    {dentist.icon}
                  </div>
                </div>

                <h4 className="font-bold text-gray-800 text-sm mb-1">
                  {dentist.name}
                </h4>
                <p className="text-blue-600 text-xs font-semibold mb-1">
                  {dentist.title}
                </p>
                <p className="text-gray-500 text-xs">
                  {dentist.specialty}
                </p>
              </div>
            ))}
          </div>
        </div>

        {/* Certifications */}
        <div className="mt-16 max-w-4xl mx-auto">
          <div className="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-8 border border-blue-200">
            <div className="flex flex-col lg:flex-row items-center justify-between gap-8">
              <div className="flex items-center gap-6">
                <div className="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                  <FiAward className="text-blue-600 text-3xl" />
                </div>
                <div>
                  <h4 className="font-bold text-gray-800 text-lg mb-1 flex items-center gap-2">
                    <FiShield /> Board Certified Excellence
                  </h4>
                  <p className="text-gray-600">
                    All our dentists maintain advanced certifications and
                    complete 50+ hours of continuing education annually.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <span className="bg-white px-4 py-2 rounded-lg shadow-sm text-gray-700 font-semibold">
                  ADA
                </span>
                <span className="bg-white px-4 py-2 rounded-lg shadow-sm text-gray-700 font-semibold">
                  ABDS
                </span>
                <span className="bg-white px-4 py-2 rounded-lg shadow-sm text-gray-700 font-semibold">
                  ICOI
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OurDentist_2;
