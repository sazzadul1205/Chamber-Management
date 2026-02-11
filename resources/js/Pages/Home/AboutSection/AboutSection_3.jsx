import React from "react";
import { FaStar, FaBookOpen, FaUserMd } from "react-icons/fa";

const AboutSection_3 = () => {
  const timeline = [
    { year: "2005", title: "Humble Beginnings", description: "Started as a single-chair practice with a vision for patient-centered care" },
    { year: "2010", title: "Technology Upgrade", description: "Invested in digital dentistry equipment and 3D imaging technology" },
    { year: "2015", title: "Expansion", description: "Moved to our current state-of-the-art facility with 5 treatment rooms" },
    { year: "2020", title: "Excellence Award", description: "Received 'Best Dental Clinic' award for outstanding patient care" },
    { year: "2024", title: "Today", description: "Serving 5000+ patients with comprehensive dental solutions" }
  ];

  const team = [
    { name: "Dr. Sarah Johnson", role: "Chief Dentist", specialty: "Cosmetic Dentistry", exp: "12 Years" },
    { name: "Dr. Michael Chen", role: "Orthodontist", specialty: "Braces & Aligners", exp: "8 Years" },
    { name: "Dr. Emily Rodriguez", role: "Pediatric Dentist", specialty: "Children's Dentistry", exp: "6 Years" }
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-2 rounded-full mb-6">
            <FaStar className="text-yellow-400" />
            <span className="text-sm font-semibold tracking-wider">OUR JOURNEY</span>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Two Decades of <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Smile Excellence</span>
          </h2>
          <p className="text-gray-300 text-lg max-w-3xl mx-auto">
            From a small practice to a leading dental center, our commitment to exceptional care has never wavered.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">

          {/* Left Side - Timeline */}
          <div className="relative">
            <div className="absolute left-0 lg:left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-cyan-500 via-blue-500 to-purple-500 hidden lg:block"></div>

            <div className="space-y-12">
              {timeline.map((item, index) => (
                <div key={index} className="relative pl-8 lg:pl-16">
                  <div className="absolute left-0 lg:left-4 w-8 h-8 rounded-full border-4 border-gray-800 bg-gradient-to-r from-cyan-500 to-blue-500 flex items-center justify-center z-10">
                    <div className="w-3 h-3 rounded-full bg-white"></div>
                  </div>

                  <div className="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300">
                    <div className="flex items-center gap-4 mb-3">
                      <div className="text-cyan-400 font-bold text-2xl">{item.year}</div>
                      <div className="h-px flex-1 bg-gradient-to-r from-cyan-500/50 to-transparent"></div>
                    </div>
                    <h3 className="text-xl font-bold text-white mb-2">{item.title}</h3>
                    <p className="text-gray-300">{item.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Right Side - Story & Team */}
          <div className="space-y-12">

            {/* Story Section */}
            <div className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700/50">
              <h3 className="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <FaBookOpen className="text-cyan-400" />
                Our Story
              </h3>
              <div className="space-y-4 text-gray-300">
                <p>
                  Founded by Dr. Sarah Johnson in 2005, what began as a small neighborhood dental practice
                  has grown into a comprehensive dental center serving thousands of families.
                </p>
                <p>
                  Driven by a passion for dental excellence and patient comfort, we've continuously evolved,
                  embracing new technologies while maintaining our core values of compassion and integrity.
                </p>
                <p>
                  Today, our team of specialists works together to provide complete dental solutions under one roof,
                  making quality dental care accessible and comfortable for everyone.
                </p>
              </div>
            </div>

            {/* Team Highlights */}
            <div className="space-y-6">
              <h3 className="text-2xl font-bold text-white flex items-center gap-3">
                <FaUserMd className="text-cyan-400" />
                Meet Our Experts
              </h3>

              <div className="space-y-4">
                {team.map((member, index) => (
                  <div key={index} className="bg-white/5 backdrop-blur-sm rounded-xl p-4 hover:bg-white/10 transition-all duration-300">
                    <div className="flex items-center gap-4">
                      <div className="w-16 h-16 rounded-full bg-gradient-to-r from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
                        <FaUserMd className="text-2xl text-white" />
                      </div>
                      <div className="flex-1">
                        <h4 className="font-bold text-white text-lg">{member.name}</h4>
                        <div className="flex items-center gap-4 text-sm text-gray-300">
                          <span>{member.role}</span>
                          <span className="w-1 h-1 rounded-full bg-gray-500"></span>
                          <span>{member.specialty}</span>
                          <span className="w-1 h-1 rounded-full bg-gray-500"></span>
                          <span>{member.exp}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {/* CTA */}
              <div className="pt-6">
                <a
                  href="#team"
                  className="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1"
                >
                  <span>View Full Team</span>
                  <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Philosophy Banner */}
        <div className="mt-20 bg-gradient-to-r from-cyan-500/10 via-blue-500/10 to-purple-500/10 rounded-2xl p-8 border border-cyan-500/20">
          <div className="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div>
              <h3 className="text-2xl font-bold text-white mb-3">
                Our Philosophy: Care Beyond Treatment
              </h3>
              <p className="text-gray-300 max-w-2xl">
                We believe dental care should be more than just fixing problems—it's about building
                relationships, educating patients, and creating smiles that last a lifetime.
              </p>
            </div>
            <a
              href="#contact"
              className="inline-flex items-center bg-white text-gray-900 hover:bg-gray-100 font-semibold px-8 py-3 rounded-xl transition-all duration-300 whitespace-nowrap"
            >
              <span>Start Your Journey</span>
              <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection_3;
