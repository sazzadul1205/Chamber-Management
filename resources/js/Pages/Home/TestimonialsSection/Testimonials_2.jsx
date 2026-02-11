import React from "react";
import Slider from "react-slick";
import {
  FaStar,
  FaQuoteLeft,
  FaPlay,
  FaCheckCircle,
  FaChevronRight,
  FaChevronLeft,
  FaGoogle,
  FaYelp,
  FaFacebookF
} from "react-icons/fa";

import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const Testimonials_2 = () => {
  const testimonials = [
    {
      id: 1,
      name: "Jessica Parker",
      location: "Los Angeles, CA",
      rating: 5,
      text: "I was always self-conscious about my crooked teeth. After completing my Invisalign treatment, I can't stop smiling!",
      hasVideo: true,
      treatment: "Invisalign",
      duration: "12 months"
    },
    {
      id: 2,
      name: "Thomas Anderson",
      location: "San Francisco, CA",
      rating: 5,
      text: "Porcelain veneers completely transformed my smile. Best decision I ever made.",
      hasVideo: false,
      treatment: "Porcelain Veneers",
      duration: "2 visits"
    }
  ];

  const NextArrow = ({ onClick }) => (
    <div
      onClick={onClick}
      className="absolute right-6 top-1/2 -translate-y-1/2 z-20 cursor-pointer"
    >
      <div className="w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:shadow-xl transition">
        <FaChevronRight className="text-gray-700" />
      </div>
    </div>
  );

  const PrevArrow = ({ onClick }) => (
    <div
      onClick={onClick}
      className="absolute left-6 top-1/2 -translate-y-1/2 z-20 cursor-pointer"
    >
      <div className="w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:shadow-xl transition">
        <FaChevronLeft className="text-gray-700" />
      </div>
    </div>
  );

  const settings = {
    dots: true,
    infinite: true,
    speed: 700,
    slidesToShow: 1,
    autoplay: true,
    autoplaySpeed: 5500,
    centerMode: true,
    centerPadding: "250px",
    responsive: [
      {
        breakpoint: 1024,
        settings: { centerPadding: "100px" }
      },
      {
        breakpoint: 768,
        settings: {
          centerMode: false,
          arrows: false
        }
      }
    ],
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />
  };

  const renderStars = (rating) =>
    [...Array(5)].map((_, i) => (
      <FaStar
        key={i}
        className={`${i < rating ? "text-yellow-400" : "text-gray-300"
          } text-lg`}
      />
    ));

  return (
    <section className="py-24 bg-gradient-to-b from-gray-50 to-white">
      <div className="max-w-7xl mx-auto px-6">

        {/* Header */}
        <div className="text-center mb-16">
          <span className="text-blue-600 font-semibold text-sm tracking-wide">
            REAL RESULTS
          </span>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mt-4 mb-6">
            Real Smiles, Real Stories
          </h2>
          <p className="text-gray-600 max-w-2xl mx-auto">
            Authentic transformations from patients who trusted us.
          </p>
        </div>

        {/* Slider */}
        <div className="relative">
          <Slider {...settings}>
            {testimonials.map((item) => (
              <div key={item.id} className="px-6">
                <div className="bg-white rounded-3xl shadow-xl overflow-hidden">
                  <div className="grid lg:grid-cols-2">

                    {/* Left Content */}
                    <div className="p-10">
                      <div className="flex items-center gap-3 mb-6">
                        {renderStars(item.rating)}
                        <span className="flex items-center gap-1 text-green-600 text-sm font-medium">
                          <FaCheckCircle />
                          Verified
                        </span>
                      </div>

                      <FaQuoteLeft className="text-3xl text-blue-100 mb-4" />

                      <p className="text-gray-700 text-lg leading-relaxed mb-8">
                        {item.text}
                      </p>

                      <div>
                        <h4 className="text-xl font-bold text-gray-800">
                          {item.name}
                        </h4>
                        <p className="text-gray-500 text-sm">
                          {item.location}
                        </p>

                        <div className="flex gap-3 mt-4">
                          <span className="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-semibold">
                            {item.treatment}
                          </span>
                          <span className="text-xs bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full">
                            {item.duration}
                          </span>
                        </div>
                      </div>
                    </div>

                    {/* Right Before/After */}
                    <div className="bg-gray-50 p-10 flex items-center justify-center">
                      <div className="grid grid-cols-2 gap-6 w-full max-w-sm">

                        <div className="text-center">
                          <div className="h-32 bg-gray-200 rounded-2xl shadow-inner" />
                          <p className="mt-3 text-sm font-semibold text-gray-600">
                            Before
                          </p>
                        </div>

                        <div className="text-center">
                          <div className="h-32 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl shadow-inner" />
                          <p className="mt-3 text-sm font-semibold text-gray-600">
                            After
                          </p>
                        </div>

                      </div>

                      {item.hasVideo && (
                        <div className="absolute bottom-8 bg-red-500 text-white px-6 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm font-semibold">
                          <FaPlay />
                          Watch Story
                        </div>
                      )}
                    </div>

                  </div>
                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* Platform Rating */}
        <div className="mt-20 text-center">
          <div className="inline-flex items-center gap-10 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-10 py-6 rounded-2xl shadow-xl">
            <div>
              <div className="flex items-center gap-2 text-2xl font-bold">
                <FaStar className="text-yellow-300" />
                4.9
              </div>
              <p className="text-sm text-blue-100">
                Average Rating
              </p>
            </div>

            <div className="flex gap-8 text-center text-sm">
              <div>
                <FaGoogle className="text-2xl mb-1" />
                Google
              </div>
              <div>
                <FaYelp className="text-2xl mb-1" />
                Yelp
              </div>
              <div>
                <FaFacebookF className="text-2xl mb-1" />
                Facebook
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  );
};

export default Testimonials_2;
