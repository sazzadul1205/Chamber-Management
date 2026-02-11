import React from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const TopSlider_3 = () => {
  const slides = [
    {
      title: "Smile with Confidence",
      subtitle: "Expert dental care for a lifetime",
      description: "Transform your smile with our comprehensive dental solutions. Trust our experienced team for exceptional results.",
      image: "/assets/Sliders/slider1.jfif",
      features: ["Teeth Whitening", "Dental Implants", "Regular Checkups"],
      icon: "😊",
      color: "bg-gradient-to-br from-blue-50 to-cyan-50",
      accentColor: "text-blue-600",
      accentBgColor: "bg-blue-600"
    },
    {
      title: "Cutting-edge Dentistry",
      subtitle: "Technology-driven, patient-focused",
      description: "Experience the latest in dental technology with pain-free treatments and faster recovery times.",
      image: "/assets/Sliders/slider2.jfif",
      features: ["Digital Scanners", "Laser Dentistry", "3D Imaging"],
      icon: "⚡",
      color: "bg-gradient-to-br from-purple-50 to-pink-50",
      accentColor: "text-purple-600",
      accentBgColor: "bg-purple-600"
    },
    {
      title: "Gentle & Caring Professionals",
      subtitle: "Relaxing treatments for children and adults",
      description: "Our compassionate team creates a welcoming environment where every patient feels comfortable and cared for.",
      image: "/assets/Sliders/slider3.jfif",
      features: ["Child-Friendly", "Sedation Options", "Comfort Care"],
      icon: "🤗",
      color: "bg-gradient-to-br from-emerald-50 to-teal-50",
      accentColor: "text-emerald-600",
      accentBgColor: "bg-emerald-600"
    },
  ];

  const settings = {
    dots: true,
    infinite: true,
    speed: 800,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 4500,
    arrows: true,
    pauseOnHover: true,
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />,
    appendDots: dots => (
      <div className="absolute bottom-6 left-1/2 transform -translate-x-1/2">
        <ul className="flex space-x-2"> {dots} </ul>
      </div>
    ),
    customPaging: (i) => (
      <button className="w-2 h-2 bg-gray-400 rounded-full transition-all duration-300 hover:bg-gray-600"></button>
    ),
  };

  return (
    <div className="w-full h-[85vh] relative overflow-hidden">
      <Slider {...settings}>
        {slides.map((slide, i) => (
          <div key={i} className="relative w-full h-[85vh]">
            {/* Background Pattern */}
            <div className={`absolute inset-0 ${slide.color} opacity-90`}>
              <div className="absolute inset-0 opacity-10" style={{
                backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
                backgroundSize: '60px 60px'
              }}></div>
            </div>

            {/* Main Content Container */}
            <div className="relative h-full flex items-center justify-center">
              <div className="container mx-auto px-6 lg:px-12">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

                  {/* Left Content */}
                  <div className="space-y-8 relative z-10">
                    {/* Icon Badge */}
                    <div className="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/80 backdrop-blur-sm shadow-lg mb-4">
                      <span className="text-3xl">{slide.icon}</span>
                    </div>

                    {/* Title */}
                    <h1 className="text-5xl lg:text-6xl font-bold leading-tight">
                      <span className={`block ${slide.accentColor}`}>{slide.title.split(" ")[0]}</span>
                      <span className="text-gray-800">
                        {slide.title.split(" ").slice(1).join(" ")}
                      </span>
                    </h1>

                    {/* Subtitle */}
                    <h2 className="text-2xl font-semibold text-gray-600">
                      {slide.subtitle}
                    </h2>

                    {/* Description */}
                    <p className="text-gray-600 leading-relaxed text-lg max-w-xl">
                      {slide.description}
                    </p>

                    {/* Features */}
                    <div className="flex flex-wrap gap-3">
                      {slide.features.map((feature, idx) => (
                        <span
                          key={idx}
                          className="inline-flex items-center px-4 py-2 rounded-full bg-white/60 backdrop-blur-sm border border-gray-200 text-gray-700 font-medium"
                        >
                          <svg className="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                          </svg>
                          {feature}
                        </span>
                      ))}
                    </div>

                    {/* CTA Buttons */}
                    <div className="flex flex-wrap gap-4 pt-4">
                      <a
                        href="#contact"
                        className={`inline-flex items-center ${slide.accentBgColor} hover:opacity-90 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl`}
                      >
                        <span className="text-lg">Book Appointment</span>
                        <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                      </a>
                      <a
                        href="#about"
                        className="inline-flex items-center bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-4 rounded-xl transition-all duration-300 border-2 border-gray-200"
                      >
                        <span className="text-lg">Meet Our Team</span>
                        <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.67 3.943a8 8 0 00-12.65-4.386" />
                        </svg>
                      </a>
                    </div>
                  </div>

                  {/* Right Image */}
                  <div className="relative">
                    <div className="relative rounded-3xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-transform duration-700">
                      <div
                        className="h-[500px] bg-cover bg-center relative"
                        style={{ backgroundImage: `url(${slide.image})` }}
                      >
                        {/* Image Overlay */}
                        <div className={`absolute inset-0 ${slide.accentBgColor.replace('bg-', 'bg-gradient-to-t from-')}/10 to-transparent`}></div>
                      </div>

                      {/* Floating Stats */}
                      <div className="absolute -bottom-6 left-1/2 transform -translate-x-1/2">
                        <div className="flex gap-6">
                          <div className="bg-white rounded-xl p-4 shadow-lg text-center min-w-[120px]">
                            <div className="text-2xl font-bold text-gray-800">99%</div>
                            <div className="text-sm text-gray-600">Satisfaction</div>
                          </div>
                          <div className="bg-white rounded-xl p-4 shadow-lg text-center min-w-[120px]">
                            <div className="text-2xl font-bold text-gray-800">500+</div>
                            <div className="text-sm text-gray-600">Happy Patients</div>
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Decorative Elements */}
                    <div className="absolute -top-6 -right-6 w-32 h-32 rounded-full border-4 border-white/30 hidden lg:block"></div>
                    <div className="absolute -bottom-8 -left-8 w-24 h-24 rounded-full border-2 border-white/20 hidden lg:block"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        ))}
      </Slider>
    </div>
  );
};

// Custom Arrow Components
const NextArrow = (props) => {
  const { className, style, onClick } = props;
  return (
    <div
      className={`${className} !right-8 z-10`}
      style={{ ...style, display: "block" }}
      onClick={onClick}
    >
      <div className="w-12 h-12 rounded-full bg-white/80 backdrop-blur-sm shadow-lg flex items-center justify-center hover:bg-white transition-all">
        <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </div>
  );
};

const PrevArrow = (props) => {
  const { className, style, onClick } = props;
  return (
    <div
      className={`${className} !left-8 z-10`}
      style={{ ...style, display: "block" }}
      onClick={onClick}
    >
      <div className="w-12 h-12 rounded-full bg-white/80 backdrop-blur-sm shadow-lg flex items-center justify-center hover:bg-white transition-all">
        <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
        </svg>
      </div>
    </div>
  );
};

export default TopSlider_3;