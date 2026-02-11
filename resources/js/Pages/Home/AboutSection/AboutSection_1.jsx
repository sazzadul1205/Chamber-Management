import React from "react";
import { FaTooth, FaUserMd, FaHeart, FaStar } from "react-icons/fa";

const AboutSection_1 = () => {
  const keyPoints = [
    { icon: <FaTooth className="text-blue-500" />, text: "Advanced Technology" },
    { icon: <FaUserMd className="text-blue-500" />, text: "Expert Dentists" },
    { icon: <FaHeart className="text-blue-500" />, text: "Patient-Centered Care" },
    { icon: <FaStar className="text-blue-500" />, text: "5-Star Service" },
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

          {/* Left Side - Image with Badge */}
          <div className="relative">
            <div className="relative rounded-3xl overflow-hidden shadow-2xl">
              <div className="h-[500px] bg-gradient-to-br from-blue-400 to-cyan-300 flex items-center justify-center">
                <div className="text-center">
                  <FaTooth className="text-white text-8xl" />
                  <p className="text-white/80 mt-4 text-lg">Modern Dental Clinic</p>
                </div>
              </div>

              {/* Floating Experience Badge */}
              <div className="absolute -bottom-6 left-8 bg-white rounded-2xl p-6 shadow-xl">
                <div className="text-center">
                  <div className="text-4xl font-bold text-blue-600">15+</div>
                  <div className="text-gray-600 text-sm font-semibold">Years Experience</div>
                </div>
              </div>
            </div>

            {/* Decorative Circle */}
            <div className="absolute -top-6 -right-6 w-32 h-32 rounded-full border-4 border-blue-100 hidden lg:block"></div>
          </div>

          {/* Right Side - Content */}
          <div className="space-y-8">
            {/* Section Label */}
            <div className="inline-flex items-center gap-2">
              <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
              <span className="text-blue-600 font-semibold tracking-wider">ABOUT OUR CLINIC</span>
            </div>

            {/* Main Title */}
            <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 leading-tight">
              Caring for Your Smile
              <span className="block text-blue-600">Since 2008</span>
            </h2>

            {/* Description */}
            <div className="space-y-4 text-gray-600 leading-relaxed">
              <p>
                At SmileCare Dental, we believe everyone deserves a healthy, beautiful smile.
                Our certified dentists combine expertise with compassion to provide
                exceptional dental care in a warm, welcoming environment.
              </p>
              <p>
                Using state-of-the-art technology and evidence-based practices, we deliver
                personalized treatment plans that prioritize your comfort and long-term oral health.
              </p>
            </div>

            {/* Key Points */}
            <div className="grid grid-cols-2 gap-4 pt-4">
              {keyPoints.map((item, index) => (
                <div key={index} className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-xl">
                    {item.icon}
                  </div>
                  <span className="font-medium text-gray-700">{item.text}</span>
                </div>
              ))}
            </div>

            {/* CTA Button */}
            <div className="pt-6">
              <a
                href="#team"
                className="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1"
              >
                Meet Our Team
                <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection_1;
