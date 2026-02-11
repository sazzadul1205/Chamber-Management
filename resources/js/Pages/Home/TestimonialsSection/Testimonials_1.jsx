import React from "react";
import Slider from "react-slick";
import {
  FaStar,
  FaQuoteLeft,
  FaCheckCircle,
} from "react-icons/fa";
import { FcGoogle } from "react-icons/fc";
import { FaYelp, FaFacebookF } from "react-icons/fa";
import { MdHealthAndSafety } from "react-icons/md";
import { HiArrowRight } from "react-icons/hi";

import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const Testimonials_1 = () => {
  const testimonials = [
    {
      id: 1,
      name: "Jennifer Martinez",
      role: "Patient since 2020",
      rating: 5,
      text: "Dr. Johnson and her team are absolutely amazing! I've always been anxious about dental visits, but they made me feel completely at ease. The procedure was painless and my smile looks better than ever.",
      treatment: "Teeth Whitening",
      date: "March 2024"
    },
    {
      id: 2,
      name: "Robert Chen",
      role: "Patient since 2022",
      rating: 5,
      text: "I had Invisalign treatment and couldn't be happier. The whole process was smooth and my teeth are perfectly straight now.",
      treatment: "Invisalign",
      date: "February 2024"
    },
    {
      id: 3,
      name: "Sarah Williams",
      role: "Patient since 2019",
      rating: 5,
      text: "Brought my daughter for her first visit. The team was gentle and welcoming. Amazing experience.",
      treatment: "Pediatric Dentistry",
      date: "January 2024"
    }
  ];

  const settings = {
    dots: true,
    infinite: true,
    speed: 700,
    slidesToShow: 3,
    autoplay: true,
    autoplaySpeed: 4500,
    responsive: [
      {
        breakpoint: 1024,
        settings: { slidesToShow: 2 }
      },
      {
        breakpoint: 768,
        settings: { slidesToShow: 1, arrows: false }
      }
    ]
  };

  const renderStars = (rating) =>
    [...Array(5)].map((_, i) => (
      <FaStar
        key={i}
        className={`text-sm ${i < rating ? "text-yellow-400" : "text-gray-300"
          }`}
      />
    ));

  return (
    <section className="py-24 bg-gradient-to-b from-white to-blue-50">
      <div className="max-w-7xl mx-auto px-6">

        {/* Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
            What Our Patients Say
          </h2>
          <p className="text-gray-600 max-w-2xl mx-auto">
            Real experiences from real patients who trust us with their smiles.
          </p>
        </div>

        {/* Rating Summary */}
        <div className="bg-white rounded-3xl shadow-xl p-10 mb-16">
          <div className="flex flex-col lg:flex-row items-center justify-between gap-10">
            <div className="flex items-center gap-8">
              <div className="text-center">
                <div className="text-5xl font-bold text-gray-800">4.9</div>
                <div className="flex justify-center gap-1 mt-2">
                  {[...Array(5)].map((_, i) => (
                    <FaStar key={i} className="text-yellow-400" />
                  ))}
                </div>
                <p className="text-sm text-gray-500 mt-2">
                  Based on 500+ reviews
                </p>
              </div>

              <div className="hidden lg:block w-px h-20 bg-gray-200" />

              <div className="grid grid-cols-2 gap-6 text-sm">
                <div className="flex items-center gap-2">
                  <FcGoogle className="text-2xl" />
                  <span>4.9</span>
                </div>
                <div className="flex items-center gap-2">
                  <FaYelp className="text-red-500" />
                  <span>4.8</span>
                </div>
                <div className="flex items-center gap-2">
                  <FaFacebookF className="text-blue-600" />
                  <span>4.9</span>
                </div>
                <div className="flex items-center gap-2">
                  <MdHealthAndSafety className="text-green-600 text-xl" />
                  <span>4.9</span>
                </div>
              </div>
            </div>

            <button className="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg">
              Write a Review <HiArrowRight />
            </button>
          </div>
        </div>

        {/* Slider */}
        <Slider {...settings}>
          {testimonials.map((item) => (
            <div key={item.id} className="px-4">
              <div className="bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 p-8 h-full flex flex-col">

                <div className="flex items-center justify-between mb-4">
                  <div className="flex gap-1">
                    {renderStars(item.rating)}
                  </div>
                  <span className="text-xs text-gray-400">
                    {item.date}
                  </span>
                </div>

                <FaQuoteLeft className="text-3xl text-blue-100 mb-4" />

                <p className="text-gray-700 leading-relaxed mb-6 flex-1">
                  {item.text}
                </p>

                <div className="border-t pt-4">
                  <h4 className="font-semibold text-gray-800">
                    {item.name}
                  </h4>
                  <p className="text-sm text-gray-500">
                    {item.role}
                  </p>
                  <span className="inline-block mt-2 text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full">
                    {item.treatment}
                  </span>
                </div>
              </div>
            </div>
          ))}
        </Slider>

        {/* Trust Badge */}
        <div className="mt-20 text-center">
          <div className="inline-flex items-center gap-3 bg-white shadow-md px-8 py-4 rounded-full">
            <FaCheckCircle className="text-green-500" />
            <span className="font-semibold text-gray-700">
              Verified Patient Reviews
            </span>
          </div>
        </div>

      </div>
    </section>
  );
};

export default Testimonials_1;
