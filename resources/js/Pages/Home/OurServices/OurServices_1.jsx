import React from "react";
import { FaTooth, FaSmile, FaBone, FaBaby, FaTeeth, FaAmbulance, FaArrowRight } from "react-icons/fa";

const OurServices_1 = () => {
  const services = [
    {
      icon: <FaTooth className="text-4xl text-blue-600" />,
      title: "Teeth Cleaning",
      description: "Professional cleaning to remove plaque and tartar, preventing cavities and gum disease.",
      features: ["Ultrasonic Scaling", "Polishing", "Fluoride Treatment"],
      color: "bg-gradient-to-br from-blue-50 to-cyan-50"
    },
    {
      icon: <FaSmile className="text-4xl text-purple-600" />,
      title: "Cosmetic Dentistry",
      description: "Transform your smile with our aesthetic dental solutions including veneers and teeth whitening.",
      features: ["Teeth Whitening", "Veneers", "Dental Bonding"],
      color: "bg-gradient-to-br from-purple-50 to-pink-50"
    },
    {
      icon: <FaBone className="text-4xl text-green-600" />,
      title: "Dental Implants",
      description: "Permanent tooth replacement solutions that look, feel, and function like natural teeth.",
      features: ["Single Implants", "All-on-4", "Implant Dentures"],
      color: "bg-gradient-to-br from-emerald-50 to-teal-50"
    },
    {
      icon: <FaBaby className="text-4xl text-yellow-600" />,
      title: "Pediatric Dentistry",
      description: "Gentle dental care for children in a fun, comforting environment designed for young patients.",
      features: ["Child Exams", "Preventive Care", "Emergency Pediatric"],
      color: "bg-gradient-to-br from-amber-50 to-orange-50"
    },
    {
      icon: <FaTeeth className="text-4xl text-cyan-600" />,
      title: "Orthodontics",
      description: "Straighten your teeth with modern braces and clear aligner options for all ages.",
      features: ["Traditional Braces", "Invisalign", "Retainers"],
      color: "bg-gradient-to-br from-cyan-50 to-blue-50"
    },
    {
      icon: <FaAmbulance className="text-4xl text-red-600" />,
      title: "Emergency Care",
      description: "Immediate dental care for urgent situations including toothaches, breaks, and injuries.",
      features: ["24/7 Availability", "Pain Relief", "Same-Day Appointments"],
      color: "bg-gradient-to-br from-rose-50 to-red-50"
    }
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 mb-4">
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
            <span className="text-blue-600 font-semibold tracking-wider">OUR SERVICES</span>
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Comprehensive <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Dental Services</span>
          </h2>

          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            We offer a complete range of dental treatments using state-of-the-art technology
            and personalized care to meet all your oral health needs.
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {services.map((service, index) => (
            <div
              key={index}
              className={`${service.color} rounded-2xl overflow-hidden border border-gray-200 hover:border-blue-200 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group`}
            >
              <div className="p-8">
                {/* Service Icon */}
                <div className="w-20 h-20 rounded-2xl bg-white shadow-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                  {service.icon}
                </div>

                {/* Title & Description */}
                <h3 className="text-2xl font-bold text-gray-800 mb-4">{service.title}</h3>
                <p className="text-gray-600 mb-6 leading-relaxed">{service.description}</p>

                {/* Features List */}
                <div className="space-y-3">
                  {service.features.map((feature, idx) => (
                    <div key={idx} className="flex items-center gap-3">
                      <div className="w-6 h-6 rounded-full bg-white flex items-center justify-center flex-shrink-0">
                        <svg className="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                      </div>
                      <span className="text-gray-700">{feature}</span>
                    </div>
                  ))}
                </div>

                {/* Learn More Button */}
                <div className="mt-8 pt-6 border-t border-gray-200">
                  <button className="flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors group/btn">
                    <span>Learn More</span>
                    <FaArrowRight className="ml-2 transition-transform group-hover/btn:translate-x-2" />
                  </button>
                </div>
              </div>

              {/* Bottom Accent */}
              <div className="h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
          ))}
        </div>

        {/* CTA Section */}
        <div className="mt-16 text-center">
          <div className="inline-flex flex-col sm:flex-row items-center gap-6 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-2xl p-8 text-white">
            <div className="text-left">
              <h3 className="text-2xl font-bold mb-2">Ready to Schedule?</h3>
              <p className="text-blue-100">Book your appointment online or call us today</p>
            </div>
            <div className="flex flex-col sm:flex-row gap-4">
              <a
                href="#contact"
                className="bg-white text-blue-600 hover:bg-blue-50 font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1"
              >
                Book Appointment
              </a>
              <a
                href="tel:+1234567890"
                className="bg-transparent border-2 border-white/30 hover:border-white text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300"
              >
                Call Now
              </a>
            </div>
          </div>
        </div>

      </div>
    </section>
  );
};

export default OurServices_1;
