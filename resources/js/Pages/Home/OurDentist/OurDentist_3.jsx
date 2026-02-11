import React, { useState } from "react";
import {
  FiUser,
  FiChevronLeft,
  FiChevronRight,
  FiStar,
  FiAward,
  FiBookOpen,
  FiCheckCircle,
  FiUsers,
  FiCalendar,
  FiSmile,
  FiShield,
} from "react-icons/fi";

const OurDentist_3 = () => {
  const [activeIndex, setActiveIndex] = useState(0);

  const dentists = [
    {
      name: "Dr. Sarah Johnson",
      title: "Founder & Chief Dental Officer",
      specialty: "Cosmetic & Restorative Dentistry",
      experience: "15 years",
      education: [
        "DDS - University of California, San Francisco",
        "Advanced Cosmetic Dentistry - NYU",
        "Fellow - International College of Dentists",
      ],
      achievements: [
        "Top Dentist Award 2022, 2023",
        "Published 12 research papers",
        "Inventor of Pain-Free Injection Technique",
      ],
      stats: "5000+",
      statLabel: "Successful Cases",
      rating: "4.98",
      reviews: "320+",
      color: "from-cyan-500 to-blue-600",
    },
    {
      name: "Dr. Michael Chen",
      title: "Orthodontic Director",
      specialty: "Invisalign & Braces Specialist",
      experience: "12 years",
      education: [
        "DDS - Columbia University",
        "MS in Orthodontics - University of Pennsylvania",
        "Invisalign Diamond Plus Provider",
      ],
      achievements: [
        "Invisalign Top 1% Provider",
        "Developed Accelerated Aligner Protocol",
        "Guest Lecturer at 8 universities",
      ],
      stats: "1500+",
      statLabel: "Invisalign Cases",
      rating: "4.99",
      reviews: "280+",
      color: "from-emerald-500 to-teal-600",
    },
    {
      name: "Dr. Emily Rodriguez",
      title: "Pediatric Dentistry Director",
      specialty: "Children's & Special Needs Dentistry",
      experience: "8 years",
      education: [
        "DDS - Harvard School of Dental Medicine",
        "MS in Pediatric Dentistry - Boston University",
        "Certified in Pediatric Sedation",
      ],
      achievements: [
        "Founder of 'Smile Without Fear' Program",
        "Child Life Council Certified",
        "Best Pediatric Dentist 2023",
      ],
      stats: "2000+",
      statLabel: "Happy Kids",
      rating: "5.00",
      reviews: "410+",
      color: "from-purple-500 to-pink-600",
    },
  ];

  const nextSlide = () =>
    setActiveIndex((prev) => (prev + 1) % dentists.length);

  const prevSlide = () =>
    setActiveIndex((prev) => (prev - 1 + dentists.length) % dentists.length);

  const activeDentist = dentists[activeIndex];

  return (
    <div className="py-20 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 px-6 py-2 rounded-full mb-6">
            <FiUsers className="text-cyan-400" />
            <span className="text-sm font-semibold tracking-wider">
              WORLD-CLASS DENTISTS
            </span>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Your Smile is in{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">
              Expert Hands
            </span>
          </h2>

          <p className="text-gray-300 text-lg max-w-3xl mx-auto">
            Ivy League education. Years of clinical excellence. Results you can trust.
          </p>
        </div>

        {/* Main Card */}
        <div className="max-w-6xl mx-auto relative">

          {/* Arrows */}
          <button
            onClick={prevSlide}
            className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition"
          >
            <FiChevronLeft size={22} />
          </button>

          <button
            onClick={nextSlide}
            className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition"
          >
            <FiChevronRight size={22} />
          </button>

          <div className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl border border-gray-700 overflow-hidden">
            <div className="grid grid-cols-1 lg:grid-cols-2">

              {/* Left */}
              <div className="relative p-10 flex flex-col items-center justify-center">
                <div className={`absolute inset-0 bg-gradient-to-br ${activeDentist.color} opacity-10`} />

                <div className="relative z-10 mb-8">
                  <div className="w-44 h-44 rounded-3xl bg-gradient-to-br from-gray-700 to-gray-800 border-4 border-cyan-500/30 flex items-center justify-center shadow-2xl">
                    <FiUser className="text-6xl text-gray-300" />
                  </div>

                  <div className="absolute -top-4 -right-4 bg-yellow-500 rounded-full w-16 h-16 flex flex-col items-center justify-center shadow-xl">
                    <span className="text-lg font-bold">{activeDentist.rating}</span>
                    <FiStar size={12} />
                  </div>
                </div>

                <h3 className="text-3xl font-bold mb-2 text-center">
                  {activeDentist.name}
                </h3>
                <p className="text-cyan-400 font-semibold text-lg mb-2 text-center">
                  {activeDentist.title}
                </p>
                <p className="text-gray-300 mb-6 text-center">
                  {activeDentist.specialty}
                </p>

                <div className="flex gap-10">
                  <div className="text-center">
                    <div className="text-2xl font-bold text-cyan-400">
                      {activeDentist.experience}
                    </div>
                    <div className="text-gray-400 text-sm">Experience</div>
                  </div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-cyan-400">
                      {activeDentist.stats}
                    </div>
                    <div className="text-gray-400 text-sm">
                      {activeDentist.statLabel}
                    </div>
                  </div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-cyan-400">
                      {activeDentist.reviews}
                    </div>
                    <div className="text-gray-400 text-sm">Reviews</div>
                  </div>
                </div>
              </div>

              {/* Right */}
              <div className="p-10 bg-gray-800/60 space-y-8">

                <div>
                  <h4 className="text-lg font-semibold text-cyan-400 mb-4 flex items-center gap-2">
                    <FiBookOpen /> Education & Training
                  </h4>
                  <ul className="space-y-3 text-gray-300">
                    {activeDentist.education.map((item, idx) => (
                      <li key={idx} className="flex gap-2">
                        <FiCheckCircle className="text-cyan-400 mt-1" />
                        {item}
                      </li>
                    ))}
                  </ul>
                </div>

                <div>
                  <h4 className="text-lg font-semibold text-cyan-400 mb-4 flex items-center gap-2">
                    <FiAward /> Achievements
                  </h4>
                  <ul className="space-y-3 text-gray-300">
                    {activeDentist.achievements.map((item, idx) => (
                      <li key={idx} className="flex gap-2">
                        <FiStar className="text-yellow-400 mt-1" />
                        {item}
                      </li>
                    ))}
                  </ul>
                </div>

                <div className="flex gap-4 pt-6">
                  <button
                    className={`flex-1 bg-gradient-to-r ${activeDentist.color} hover:opacity-90 py-4 rounded-xl font-semibold transition`}
                  >
                    Book Appointment
                  </button>
                  <button className="px-6 py-4 rounded-xl border border-gray-600 hover:border-cyan-500 transition">
                    View Bio
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* Dots */}
          <div className="flex justify-center gap-3 mt-8">
            {dentists.map((_, index) => (
              <button
                key={index}
                onClick={() => setActiveIndex(index)}
                className={`h-3 rounded-full transition-all ${index === activeIndex
                    ? "w-12 bg-gradient-to-r from-cyan-500 to-blue-500"
                    : "w-3 bg-gray-600"
                  }`}
              />
            ))}
          </div>
        </div>

        {/* Stats Section */}
        <div className="mt-20 grid grid-cols-2 md:grid-cols-4 gap-10 text-center max-w-5xl mx-auto">
          {[
            { value: "15+", label: "Specialists", icon: <FiUsers /> },
            { value: "50+", label: "Years Combined", icon: <FiCalendar /> },
            { value: "25k+", label: "Patients Treated", icon: <FiSmile /> },
            { value: "4.9", label: "Average Rating", icon: <FiShield /> },
          ].map((stat, index) => (
            <div key={index}>
              <div className="text-cyan-400 text-3xl mb-2 flex justify-center">
                {stat.icon}
              </div>
              <div className="text-2xl font-bold text-cyan-400">
                {stat.value}
              </div>
              <div className="text-gray-400">{stat.label}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default OurDentist_3;
