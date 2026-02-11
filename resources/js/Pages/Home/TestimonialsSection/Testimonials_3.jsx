import React from "react";
import Slider from "react-slick";
import {
  FaStar,
  FaQuoteLeft,
  FaMapMarkerAlt,
  FaCheckCircle,
  FaChevronRight,
  FaChevronLeft,
  FaArrowRight,
  FaLock
} from "react-icons/fa";

import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const Testimonials_3 = () => {
  const testimonials = [
    {
      id: 1,
      name: "Sophia Bennett",
      title: "Marketing Director",
      company: "Tech Solutions Inc.",
      quote:
        "This dental clinic completely changed my perception of dentistry. I now look forward to my appointments.",
      rating: 5,
      location: "New York",
      patientSince: "2019"
    },
    {
      id: 2,
      name: "Marcus Williams",
      title: "Software Engineer",
      company: "Innovate Labs",
      quote:
        "After years of anxiety, I finally found a clinic that truly understands patient comfort.",
      rating: 5,
      location: "Chicago",
      patientSince: "2021"
    }
  ];

  const NextArrow = ({ onClick }) => (
    <div
      onClick={onClick}
      className="absolute right-0 top-1/2 -translate-y-1/2 z-20 cursor-pointer"
    >
      <div className="w-12 h-12 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center hover:bg-gray-700 transition">
        <FaChevronRight className="text-white" />
      </div>
    </div>
  );

  const PrevArrow = ({ onClick }) => (
    <div
      onClick={onClick}
      className="absolute left-0 top-1/2 -translate-y-1/2 z-20 cursor-pointer"
    >
      <div className="w-12 h-12 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center hover:bg-gray-700 transition">
        <FaChevronLeft className="text-white" />
      </div>
    </div>
  );

  const settings = {
    dots: true,
    infinite: true,
    speed: 900,
    slidesToShow: 2,
    autoplay: true,
    autoplaySpeed: 5000,
    responsive: [
      {
        breakpoint: 1024,
        settings: { slidesToShow: 1 }
      }
    ],
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />
  };

  const renderStars = (rating) =>
    [...Array(5)].map((_, i) => (
      <FaStar
        key={i}
        className={`${i < rating ? "text-cyan-400" : "text-gray-600"
          } text-lg`}
      />
    ));

  return (
    <section className="py-24 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white">
      <div className="max-w-6xl mx-auto px-6">

        {/* Header */}
        <div className="text-center mb-16">
          <span className="text-sm text-cyan-400 font-semibold tracking-wider">
            PATIENT EXPERIENCES
          </span>
          <h2 className="text-4xl lg:text-5xl font-bold mt-4 mb-6">
            Trusted by <span className="text-cyan-400">Thousands</span>
          </h2>
          <p className="text-gray-400 max-w-2xl mx-auto">
            Real feedback from patients who chose us for their care.
          </p>
        </div>

        {/* Slider */}
        <div className="relative">
          <Slider {...settings}>
            {testimonials.map((item) => (
              <div key={item.id} className="px-4">
                <div className="bg-gray-800 rounded-2xl border border-gray-700 p-8 h-full hover:border-cyan-500/40 transition">

                  <FaQuoteLeft className="text-4xl text-cyan-500/20 mb-6" />

                  <p className="text-gray-300 leading-relaxed mb-8">
                    {item.quote}
                  </p>

                  <div className="flex gap-1 mb-6">
                    {renderStars(item.rating)}
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center font-bold text-lg border border-cyan-500/30">
                      {item.name.charAt(0)}
                    </div>

                    <div>
                      <h4 className="font-bold text-lg">{item.name}</h4>
                      <p className="text-gray-400 text-sm">
                        {item.title}, {item.company}
                      </p>

                      <div className="flex items-center gap-3 mt-2 text-xs">
                        <span className="flex items-center gap-1 bg-cyan-500/10 text-cyan-300 px-2 py-1 rounded-full">
                          <FaMapMarkerAlt />
                          {item.location}
                        </span>
                        <span className="bg-gray-700 text-gray-300 px-2 py-1 rounded-full">
                          Since {item.patientSince}
                        </span>
                      </div>
                    </div>
                  </div>

                  {/* Footer */}
                  <div className="mt-8 pt-6 border-t border-gray-700 flex items-center justify-between">
                    <span className="flex items-center gap-2 text-sm text-gray-400">
                      <FaCheckCircle className="text-green-400" />
                      Verified Patient
                    </span>

                    <button className="flex items-center gap-2 text-cyan-400 text-sm font-medium hover:underline">
                      Read More
                      <FaArrowRight className="text-xs" />
                    </button>
                  </div>

                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* Trust Footer */}
        <div className="mt-20 text-center">
          <div className="inline-flex items-center gap-3 bg-white/5 px-6 py-3 rounded-full border border-white/10">
            <FaLock className="text-green-400" />
            <span className="text-gray-300 text-sm">
              All reviews are verified. Zero fake testimonials.
            </span>
          </div>
        </div>

      </div>
    </section>
  );
};

export default Testimonials_3;
