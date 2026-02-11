import React from "react";
import {
  FaUserMd,
  FaClipboardList,
  FaSmile,
  FaSyncAlt,
  FaCrown,
  FaBed,
  FaLaptop,
  FaBolt,
  FaPhone,
  FaRocket
} from "react-icons/fa";

const OurServices_3 = () => {
  const processSteps = [
    { step: "01", title: "Consultation & Examination", description: "Comprehensive assessment including digital X-rays and oral cancer screening", icon: <FaUserMd className="text-3xl" />, color: "from-blue-500 to-cyan-400" },
    { step: "02", title: "Personalized Treatment Plan", description: "Customized plan with transparent pricing and timeline", icon: <FaClipboardList className="text-3xl" />, color: "from-purple-500 to-pink-400" },
    { step: "03", title: "Comfortable Treatment", description: "Pain-free procedures with sedation options available", icon: <FaSmile className="text-3xl" />, color: "from-emerald-500 to-teal-400" },
    { step: "04", title: "Follow-up & Maintenance", description: "Regular checkups and preventive care guidance", icon: <FaSyncAlt className="text-3xl" />, color: "from-amber-500 to-orange-400" }
  ];

  const featuredServices = [
    { title: "Same-Day Crowns", description: "Get your custom crown designed, milled, and placed in a single visit", icon: <FaCrown />, highlight: "CAD/CAM Technology" },
    { title: "Sleep Dentistry", description: "Anxiety-free treatments with IV sedation and nitrous oxide options", icon: <FaBed />, highlight: "Anxiety Relief" },
    { title: "Digital Smile Design", description: "Preview your new smile with advanced 3D imaging technology", icon: <FaLaptop />, highlight: "Visual Results" },
    { title: "Laser Dentistry", description: "Minimally invasive procedures with faster healing and less discomfort", icon: <FaBolt />, highlight: "Minimal Pain" }
  ];

  const serviceTable = [
    ["Teeth Cleaning", "Professional scaling and polishing", "45 min", "$120"],
    ["Dental Fillings", "Composite resin restoration", "30 min", "$150"],
    ["Root Canal", "Endodontic treatment", "90 min", "$750"],
    ["Dental Crown", "Porcelain crown placement", "2 visits", "$800"],
    ["Teeth Whitening", "In-office professional whitening", "60 min", "$299"],
    ["Dental Implant", "Titanium implant placement", "3-6 months", "$2,500"]
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-2 rounded-full mb-6">
            <FaBolt className="text-yellow-400" />
            <span className="text-sm font-semibold tracking-wider">ADVANCED DENTAL CARE</span>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Experience <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Next-Level Dentistry</span>
          </h2>
          <p className="text-gray-300 text-lg max-w-3xl mx-auto">
            Combining cutting-edge technology with compassionate care to deliver exceptional results
            and comfortable experiences.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-20">
          {/* Left: Patient Journey */}
          <div className="space-y-8">
            <h3 className="text-2xl font-bold flex items-center gap-3">
              <FaClipboardList className="text-cyan-400" />
              Our Patient Journey
            </h3>
            <div className="space-y-8">
              {processSteps.map((step, idx) => (
                <div key={idx} className="relative flex gap-6">
                  {idx < processSteps.length - 1 && <div className="absolute left-6 top-16 bottom-0 w-0.5 bg-gradient-to-b from-gray-700 to-transparent"></div>}
                  <div className="relative z-10">
                    <div className={`w-12 h-12 rounded-full bg-gradient-to-r ${step.color} flex items-center justify-center`}>
                      {step.icon}
                    </div>
                  </div>
                  <div className="flex-1 pt-1">
                    <div className="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300">
                      <h4 className="text-xl font-bold mb-2">{step.title}</h4>
                      <p className="text-gray-300">{step.description}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Right: Featured Services */}
          <div className="space-y-8">
            <h3 className="text-2xl font-bold flex items-center gap-3">
              <FaRocket className="text-cyan-400" />
              Advanced Features
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {featuredServices.map((service, idx) => (
                <div key={idx} className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700/50 hover:border-cyan-500/30 transition-all duration-300 group">
                  <div className="flex items-start gap-4">
                    <div className="w-14 h-14 rounded-xl bg-gradient-to-r from-gray-800 to-gray-900 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-3xl">
                      {service.icon}
                    </div>
                    <div className="flex-1">
                      <h4 className="font-bold text-lg mb-2">{service.title}</h4>
                      <p className="text-gray-400 text-sm mb-3">{service.description}</p>
                      <span className="inline-flex items-center px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-xs font-semibold text-cyan-300">
                        {service.highlight}
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {/* Technology Showcase */}
            <div className="bg-gradient-to-r from-cyan-500/10 via-blue-500/10 to-purple-500/10 rounded-2xl p-6 border border-cyan-500/20">
              <h4 className="font-bold text-lg mb-4 flex items-center gap-2">
                <FaLaptop className="text-cyan-400" />
                Our Technology
              </h4>
              <div className="flex flex-wrap gap-3">
                {["3D Imaging", "Digital X-rays", "Intraoral Cameras", "Laser Dentistry", "CAD/CAM", "Sedation Options"].map((tech, i) => (
                  <span key={i} className="inline-flex items-center px-3 py-1 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 text-sm">
                    <span className="w-2 h-2 rounded-full bg-cyan-500 mr-2"></span>
                    {tech}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Services Table */}
        <div className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
          <div className="p-8 border-b border-gray-700">
            <h3 className="text-2xl font-bold flex items-center gap-3">
              <FaClipboardList className="text-cyan-400" />
              Complete Services List
            </h3>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-800/50">
                <tr>
                  <th className="py-4 px-6 text-left">Service</th>
                  <th className="py-4 px-6 text-left">Description</th>
                  <th className="py-4 px-6 text-left">Average Time</th>
                  <th className="py-4 px-6 text-left">Starting From</th>
                </tr>
              </thead>
              <tbody>
                {serviceTable.map((row, i) => (
                  <tr key={i} className="border-b border-gray-700/30 hover:bg-gray-800/30 transition-colors">
                    <td className="py-4 px-6 font-medium">{row[0]}</td>
                    <td className="py-4 px-6 text-gray-400">{row[1]}</td>
                    <td className="py-4 px-6">{row[2]}</td>
                    <td className="py-4 px-6 text-cyan-400 font-semibold">{row[3]}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Footer CTA */}
          <div className="p-8 bg-gradient-to-r from-gray-800 to-gray-900 flex flex-col lg:flex-row items-center justify-between gap-6">
            <div>
              <h4 className="font-bold text-xl mb-2">Schedule Your Visit</h4>
              <p className="text-gray-400">We're open Monday to Saturday, with emergency services available</p>
            </div>
            <div className="flex flex-col sm:flex-row gap-4">
              <a href="#contact" className="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                Book Appointment
              </a>
              <a href="tel:+1234567890" className="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 text-center">
                Call: (123) 456-7890
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  );
};

export default OurServices_3;
