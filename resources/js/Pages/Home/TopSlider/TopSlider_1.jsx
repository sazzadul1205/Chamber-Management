import React, { useState } from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const TopSlider_1 = () => {
  const [activeSlide, setActiveSlide] = useState(0);

  const slides = [
    {
      title: "Brighten Your Smile",
      subtitle: "Professional dental care for you and your family",
      image: "/assets/Sliders/slider1.jfif",
      color: "from-blue-600/90 to-cyan-500/80",
      overlay: "bg-gradient-to-r from-blue-900/40 via-blue-800/30 to-transparent"
    },
    {
      title: "Healthy Teeth, Happy Life",
      subtitle: "Advanced orthodontics and cosmetic dentistry",
      image: "/assets/Sliders/slider2.jfif",
      color: "from-emerald-600/90 to-teal-500/80",
      overlay: "bg-gradient-to-r from-emerald-900/40 via-emerald-800/30 to-transparent"
    },
    {
      title: "Gentle & Caring Dentists",
      subtitle: "Comfortable treatments for children and adults",
      image: "/assets/Sliders/slider3.jfif",
      color: "from-violet-600/90 to-purple-500/80",
      overlay: "bg-gradient-to-r from-violet-900/40 via-purple-800/30 to-transparent"
    },
  ];

  const settings = {
    dots: true,
    infinite: true,
    speed: 1000,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    fade: true,
    pauseOnHover: true,
    beforeChange: (current, next) => setActiveSlide(next),
    appendDots: dots => (
      <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2">
        <ul className="flex space-x-3"> {dots} </ul>
      </div>
    ),
    customPaging: (i) => (
      <button className={`w-3 h-3 rounded-full transition-all duration-300 ${i === activeSlide ? 'bg-white scale-125 ring-2 ring-white/50' : 'bg-white/40 hover:bg-white/60'}`}></button>
    ),
  };

  return (
    <div className="w-full h-[90vh] relative overflow-hidden">
      <Slider {...settings}>
        {slides.map((slide, i) => (
          <div key={i} className="relative w-full h-[90vh]">
            {/* Main Background Image */}
            <div
              className="absolute inset-0 bg-cover bg-center transform transition-transform duration-10000"
              style={{
                backgroundImage: `url(${slide.image})`,
                transform: i === activeSlide ? 'scale(1.05)' : 'scale(1)'
              }}
            />

            {/* Gradient Overlays */}
            <div className={`absolute inset-0 bg-gradient-to-r ${slide.color}`}></div>
            <div className={`absolute inset-0 ${slide.overlay}`}></div>
            <div className="absolute inset-0 bg-black/20"></div>

            {/* Floating Particles */}
            <div className="absolute inset-0 overflow-hidden">
              {[...Array(15)].map((_, idx) => (
                <div
                  key={idx}
                  className="absolute w-1 h-1 bg-white/30 rounded-full animate-float"
                  style={{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${idx * 0.2}s`,
                    animationDuration: `${3 + Math.random() * 4}s`
                  }}
                ></div>
              ))}
            </div>

            {/* Content */}
            <div className="relative h-full flex items-center justify-center px-4 lg:px-8">
              <div className="max-w-6xl mx-auto w-full">
                <div className="flex flex-col lg:flex-row items-center justify-between gap-12">

                  {/* Left Content - Text */}
                  <div className="lg:w-1/2 space-y-8">
                    {/* Animated Badge */}
                    <div className="inline-block animate-fadeInUp" style={{ animationDelay: '0.2s' }}>
                      <div className="bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-6 py-3 inline-flex items-center gap-2">
                        <span className="text-white text-sm font-semibold tracking-wider">
                          🌟 PREMIUM DENTAL CARE
                        </span>
                      </div>
                    </div>

                    {/* Title with Staggered Animation */}
                    <div className="space-y-4">
                      <h1
                        className="text-5xl lg:text-7xl font-bold text-white leading-tight animate-fadeInUp"
                        style={{ animationDelay: '0.4s' }}
                      >
                        {slide.title}
                      </h1>

                      <div className="h-1 w-24 bg-white/50 rounded-full animate-fadeInUp" style={{ animationDelay: '0.6s' }}></div>

                      <p
                        className="text-xl lg:text-2xl text-white/90 font-light animate-fadeInUp"
                        style={{ animationDelay: '0.8s' }}
                      >
                        {slide.subtitle}
                      </p>
                    </div>

                    {/* CTA Button with Hover Effect */}
                    <div className="animate-fadeInUp" style={{ animationDelay: '1s' }}>
                      <a
                        href="#contact"
                        className="group inline-flex items-center bg-white text-gray-900 hover:bg-gray-50 font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl shadow-lg"
                      >
                        <span className="text-lg">Book Appointment Now</span>
                        <svg className="w-5 h-5 ml-3 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                      </a>
                    </div>
                  </div>

                  {/* Right Content - Stats Card */}
                  <div className="lg:w-2/5 animate-fadeInUp" style={{ animationDelay: '1.2s' }}>
                    <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-8 shadow-2xl">
                      <div className="space-y-6">
                        <div className="flex items-center justify-between pb-4 border-b border-white/20">
                          <h3 className="text-white text-xl font-semibold">Why Choose Us</h3>
                          <div className="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <span className="text-white">✨</span>
                          </div>
                        </div>

                        <div className="space-y-4">
                          <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                              <span className="text-2xl">⭐</span>
                            </div>
                            <div>
                              <h4 className="text-white font-semibold">Expert Dentists</h4>
                              <p className="text-white/70 text-sm">20+ years experience</p>
                            </div>
                          </div>

                          <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                              <span className="text-2xl">⚡</span>
                            </div>
                            <div>
                              <h4 className="text-white font-semibold">Modern Technology</h4>
                              <p className="text-white/70 text-sm">Latest equipment</p>
                            </div>
                          </div>

                          <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                              <span className="text-2xl">❤️</span>
                            </div>
                            <div>
                              <h4 className="text-white font-semibold">Pain-Free Care</h4>
                              <p className="text-white/70 text-sm">Comfort guaranteed</p>
                            </div>
                          </div>
                        </div>

                        <div className="pt-4 border-t border-white/20">
                          <div className="flex items-center justify-between">
                            <span className="text-white/80 text-sm">Open Today</span>
                            <span className="text-white font-semibold">8:00 AM - 8:00 PM</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Bottom Gradient */}
            <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/30 to-transparent"></div>
          </div>
        ))}
      </Slider>

      {/* Custom CSS for Animations */}
      <style jsx global>{`
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        @keyframes float {
          0%, 100% {
            transform: translateY(0) translateX(0);
          }
          50% {
            transform: translateY(-20px) translateX(10px);
          }
        }
        
        .animate-fadeInUp {
          animation: fadeInUp 0.8s ease-out forwards;
          opacity: 0;
        }
        
        .animate-float {
          animation: float 3s ease-in-out infinite;
        }
      `}</style>
    </div>
  );
};

export default TopSlider_1;