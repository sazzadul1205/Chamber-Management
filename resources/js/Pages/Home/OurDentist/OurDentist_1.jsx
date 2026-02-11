import React from "react";
import {
  FiCalendar,
  FiBookOpen,
  FiClipboard,
  FiUser,
  FiAward,
  FiShield,
} from "react-icons/fi";

const OurDentist_1 = () => {
  const dentists = [
    {
      name: "Dr. Sarah Johnson",
      title: "Chief Dental Surgeon",
      specialty: "Cosmetic & Restorative Dentistry",
      experience: "15+ Years",
      education: "DDS, University of California",
      bio: "Dr. Johnson is a board-certified dentist with advanced training in cosmetic procedures. She's passionate about creating beautiful smiles and has helped thousands of patients regain their confidence.",
      expertise: ["Cosmetic Dentistry", "Dental Implants", "Full Mouth Reconstruction"],
      color: "from-blue-500 to-cyan-400",
    },
    {
      name: "Dr. Michael Chen",
      title: "Orthodontic Specialist",
      specialty: "Braces & Invisalign",
      experience: "12+ Years",
      education: "DDS, MSD in Orthodontics",
      bio: "Dr. Chen specializes in orthodontics for both children and adults. He's a certified Invisalign provider and has successfully treated over 1,000 cases using clear aligner technology.",
      expertise: ["Invisalign", "Traditional Braces", "Early Intervention"],
      color: "from-emerald-500 to-teal-400",
    },
    {
      name: "Dr. Emily Rodriguez",
      title: "Pediatric Dentist",
      specialty: "Children's Dentistry",
      experience: "8+ Years",
      education: "DDS, MS in Pediatric Dentistry",
      bio: "Dr. Rodriguez creates a fun, anxiety-free environment for young patients. Her gentle approach and specialized training make dental visits a positive experience for children of all ages.",
      expertise: ["Pediatric Care", "Sedation Dentistry", "Special Needs"],
      color: "from-purple-500 to-pink-400",
    },
    {
      name: "Dr. James Wilson",
      title: "Periodontist",
      specialty: "Gum Disease & Implants",
      experience: "10+ Years",
      education: "DDS, MS in Periodontology",
      bio: "Dr. Wilson specializes in the prevention, diagnosis, and treatment of gum disease. He's an expert in dental implant placement and laser periodontal therapy.",
      expertise: ["Gum Treatment", "Dental Implants", "Laser Dentistry"],
      color: "from-amber-500 to-orange-400",
    },
    {
      name: "Dr. Lisa Thompson",
      title: "Endodontist",
      specialty: "Root Canal Specialist",
      experience: "9+ Years",
      education: "DDS, MS in Endodontics",
      bio: "Dr. Thompson is known for her pain-free root canal treatments. She uses advanced microscopy and 3D imaging to ensure precise, comfortable procedures with excellent outcomes.",
      expertise: ["Root Canal Therapy", "Microsurgery", "Dental Trauma"],
      color: "from-rose-500 to-red-400",
    },
    {
      name: "Dr. Robert Kim",
      title: "Oral Surgeon",
      specialty: "Surgical Dentistry",
      experience: "14+ Years",
      education: "DDS, MD, Oral Surgery",
      bio: "Dr. Kim performs complex oral surgeries including wisdom tooth removal, dental implants, and bone grafting. His surgical precision and patient care have earned him outstanding reviews.",
      expertise: ["Wisdom Teeth", "Bone Grafting", "Jaw Surgery"],
      color: "from-indigo-500 to-blue-400",
    },
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 mb-4">
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
            <span className="text-blue-600 font-semibold tracking-wider">
              OUR EXPERT TEAM
            </span>
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Meet Our{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">
              Dental Specialists
            </span>
          </h2>

          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            Our experienced team delivers advanced dental care with precision,
            compassion, and modern technology.
          </p>
        </div>

        {/* Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {dentists.map((dentist, index) => (
            <div
              key={index}
              className="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group"
            >
              {/* Top Gradient */}
              <div className={`h-32 bg-gradient-to-r ${dentist.color}`}></div>

              <div className="px-8 pb-8 relative">
                {/* Avatar */}
                <div className="relative -mt-16 mb-6">
                  <div className="w-32 h-32 rounded-2xl bg-gray-900 border-4 border-white shadow-xl flex items-center justify-center">
                    <FiUser className="text-white text-5xl" />
                  </div>
                </div>

                {/* Name */}
                <h3 className="text-2xl font-bold text-gray-800 mb-1">
                  {dentist.name}
                </h3>
                <p className="text-lg font-semibold text-blue-600 mb-1">
                  {dentist.title}
                </p>
                <p className="text-gray-600 mb-4">{dentist.specialty}</p>

                {/* Meta Info */}
                <div className="flex items-center gap-4 mb-4 text-sm text-gray-700">
                  <div className="flex items-center gap-2">
                    <FiCalendar className="text-blue-500" />
                    {dentist.experience}
                  </div>

                  <div className="w-px h-4 bg-gray-300"></div>

                  <div className="flex items-center gap-2">
                    <FiBookOpen className="text-blue-500" />
                    {dentist.education}
                  </div>
                </div>

                {/* Bio */}
                <p className="text-gray-600 text-sm leading-relaxed mb-6">
                  {dentist.bio}
                </p>

                {/* Expertise */}
                <div className="mb-6 flex flex-wrap gap-2">
                  {dentist.expertise.map((item, idx) => (
                    <span
                      key={idx}
                      className={`inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r ${dentist.color} text-white`}
                    >
                      <FiAward size={12} />
                      {item}
                    </span>
                  ))}
                </div>

                {/* Buttons */}
                <div className="flex gap-4">
                  <button className="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2 rounded-lg transition-all duration-300">
                    Book Appointment
                  </button>

                  <button className="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center transition-colors">
                    <FiClipboard className="text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* CTA */}
        <div className="mt-16 text-center">
          <div className="inline-flex flex-col sm:flex-row items-center gap-6 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-2xl p-8 text-white">
            <div className="text-left">
              <h3 className="text-2xl font-bold mb-2 flex items-center gap-2">
                <FiShield />
                Board Certified Professionals
              </h3>
              <p className="text-blue-100">
                Committed to continuous education and clinical excellence.
              </p>
            </div>

            <a
              href="#team"
              className="bg-white text-blue-600 hover:bg-blue-50 font-semibold px-8 py-3 rounded-xl transition-all duration-300 whitespace-nowrap"
            >
              View Full Team
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OurDentist_1;
