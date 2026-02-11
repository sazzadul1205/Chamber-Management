import React from "react";
import { FaSmile, FaCalendarAlt, FaStar, FaAmbulance, FaBullseye, FaEye, FaGem } from "react-icons/fa";

const AboutSection_2 = () => {
  const stats = [
    { number: "5000+", label: "Happy Patients", icon: <FaSmile className="text-blue-500 text-2xl" /> },
    { number: "15+", label: "Years Experience", icon: <FaCalendarAlt className="text-blue-500 text-2xl" /> },
    { number: "98%", label: "Success Rate", icon: <FaStar className="text-blue-500 text-2xl" /> },
    { number: "24/7", label: "Emergency Care", icon: <FaAmbulance className="text-blue-500 text-2xl" /> },
  ];

  const values = [
    {
      icon: <FaBullseye className="text-blue-500 text-2xl" />,
      title: "Our Mission",
      description: "To provide exceptional dental care that enhances lives through healthy, beautiful smiles.",
    },
    {
      icon: <FaEye className="text-blue-500 text-2xl" />,
      title: "Our Vision",
      description: "To be the leading dental clinic recognized for innovation, compassion, and excellence.",
    },
    {
      icon: <FaGem className="text-blue-500 text-2xl" />,
      title: "Our Values",
      description: "Integrity, empathy, excellence, and continuous learning guide everything we do.",
    },
  ];

  const features = [
    "Digital X-rays & 3D Imaging",
    "Pain-Free Anesthesia Options",
    "Same-Day Emergency Appointments",
    "Flexible Payment Plans",
    "Child-Friendly Environment",
    "Advanced Sterilization Protocols",
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-white to-blue-50">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <span className="inline-block mb-4 text-sm text-blue-600 font-semibold tracking-wider">WHY CHOOSE US</span>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Welcome to <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Dental Excellence</span>
          </h2>
          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            Where advanced dental technology meets compassionate care. Your journey to a perfect smile starts here.
          </p>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          {stats.map((stat, index) => (
            <div
              key={index}
              className="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100"
            >
              <div className="flex flex-col items-center text-center">
                <div className="w-16 h-16 rounded-full bg-gradient-to-br from-blue-50 to-cyan-50 flex items-center justify-center mb-4">
                  {stat.icon}
                </div>
                <div className="text-3xl font-bold text-gray-800 mb-2">{stat.number}</div>
                <div className="text-gray-600 font-medium">{stat.label}</div>
              </div>
            </div>
          ))}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

          {/* Left Side - Content */}
          <div className="space-y-8">
            <h3 className="text-3xl font-bold text-gray-800">Your Trusted Dental Partner</h3>
            <div className="space-y-6">
              <p className="text-gray-600 leading-relaxed">
                At Dental Excellence, we combine years of expertise with cutting-edge technology
                to provide comprehensive dental solutions. Our patient-first approach ensures
                you receive personalized care in a comfortable, anxiety-free environment.
              </p>
              <p className="text-gray-600 leading-relaxed">
                From routine checkups to complex cosmetic procedures, our team is dedicated to
                helping you achieve and maintain optimal oral health for life.
              </p>
            </div>

            {/* Features List */}
            <div className="space-y-4">
              {features.map((feature, index) => (
                <div key={index} className="flex items-center gap-3">
                  <div className="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg className="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                    </svg>
                  </div>
                  <span className="text-gray-700">{feature}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Right Side - Values Cards */}
          <div className="space-y-6">
            {values.map((value, index) => (
              <div
                key={index}
                className="bg-gradient-to-br from-white to-blue-50 rounded-2xl p-6 border border-gray-200 hover:border-blue-200 transition-all duration-300"
              >
                <div className="flex items-start gap-4">
                  <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center flex-shrink-0">
                    {value.icon}
                  </div>
                  <div>
                    <h4 className="text-xl font-bold text-gray-800 mb-2">{value.title}</h4>
                    <p className="text-gray-600">{value.description}</p>
                  </div>
                </div>
              </div>
            ))}

            {/* CTA Card */}
            <div className="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-2xl p-6 text-white">
              <h4 className="text-xl font-bold mb-3">Ready for Your Consultation?</h4>
              <p className="text-blue-100 mb-4">Book your appointment online in minutes</p>
              <a
                href="#contact"
                className="inline-flex items-center bg-white text-blue-600 hover:bg-blue-50 font-semibold px-6 py-3 rounded-lg transition-all duration-300"
              >
                Schedule Now
                <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>
  );
};

export default AboutSection_2;
