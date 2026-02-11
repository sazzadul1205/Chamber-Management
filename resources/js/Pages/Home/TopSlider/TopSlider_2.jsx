import React from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const TopSlider_2 = () => {
  const slides = [
    {
      title: "Your Smile, Our Priority",
      subtitle: "Modern dental care with a gentle touch",
      description: "Experience cutting-edge dental technology combined with compassionate care.",
      image: "/assets/Sliders/slider1.jfif",
      cta: "Book Appointment",
      color: "from-blue-900/80 to-teal-700/70",
      accentColor: "text-white"
    },
    {
      title: "Advanced Treatments",
      subtitle: "From orthodontics to cosmetic dentistry",
      description: "Comprehensive dental solutions tailored to your unique needs.",
      image: "/assets/Sliders/slider2.jfif",
      cta: "View Services",
      color: "from-purple-900/80 to-blue-700/70",
      accentColor: "text-white"
    },
    {
      title: "Comfort & Care",
      subtitle: "Safe and relaxing environment for all ages",
      description: "Creating a welcoming atmosphere for patients of all ages.",
      image: "/assets/Sliders/slider3.jfif",
      cta: "Learn More",
      color: "from-emerald-900/80 to-cyan-700/70",
      accentColor: "text-white"
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
    fade: true,
    arrows: false,
    pauseOnHover: true,
    dotsClass: "slick-dots !bottom-8",
    customPaging: (i) => (
      <div className="w-3 h-3 bg-white/50 rounded-full transition-all duration-300 hover:bg-white"></div>
    ),
  };

  return (
    <div className="w-full h-screen max-h-[90vh] relative overflow-hidden">
      <Slider {...settings}>
        {slides.map((slide, i) => (
          <div key={i} className="relative w-full h-[90vh]">
            {/* Background Image with Overlay */}
            <div className="absolute inset-0">
              <div
                className="w-full h-full bg-cover bg-center transform scale-105 transition-transform duration-10000"
                style={{ backgroundImage: `url(${slide.image})` }}
              />
              <div className={`absolute inset-0 bg-gradient-to-r ${slide.color}`}></div>
              <div className="absolute inset-0 bg-black/20"></div>
            </div>

            {/* Content */}
            <div className="relative h-full flex items-center">
              <div className="container mx-auto px-6 lg:px-12">
                <div className="max-w-2xl space-y-6">
                  {/* Subtle Badge */}
                  <div className="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-4">
                    <span className="text-white/90 text-sm font-semibold tracking-wider">
                      EXCELLENCE IN DENTISTRY
                    </span>
                  </div>

                  {/* Title with animation */}
                  <h1 className="text-5xl lg:text-7xl font-bold text-white leading-tight">
                    {slide.title.split(",")[0]}
                    <span className="block text-blue-200 mt-2">
                      {slide.title.split(",")[1]}
                    </span>
                  </h1>

                  {/* Subtitle */}
                  <h2 className="text-2xl lg:text-3xl font-light text-white/90 mb-4">
                    {slide.subtitle}
                  </h2>

                  {/* Description */}
                  <p className="text-lg text-white/80 max-w-xl leading-relaxed">
                    {slide.description}
                  </p>

                  {/* CTA Buttons */}
                  <div className="flex flex-wrap gap-4 pt-6">
                    <a
                      href="#contact"
                      className="inline-flex items-center bg-white text-blue-800 hover:bg-blue-50 font-semibold px-8 py-4 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl shadow-lg"
                    >
                      <span>{slide.cta}</span>
                      <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                      </svg>
                    </a>
                    <a
                      href="#services"
                      className="inline-flex items-center bg-transparent border-2 border-white/30 hover:border-white text-white font-semibold px-8 py-4 rounded-lg transition-all duration-300 backdrop-blur-sm"
                    >
                      <span>Our Services</span>
                      <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>

            {/* Decorative Elements */}
            <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/20 to-transparent"></div>

            {/* Floating Elements */}
            <div className="absolute top-1/4 right-12 w-32 h-32 border-2 border-white/20 rounded-full animate-pulse"></div>
            <div className="absolute bottom-1/4 left-8 w-16 h-16 border border-white/10 rounded-full"></div>
          </div>
        ))}
      </Slider>

      {/* Scroll Indicator */}
      <div className="absolute bottom-6 left-1/2 transform -translate-x-1/2 hidden lg:block">
        <div className="animate-bounce">
          <svg className="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </div>
      </div>
    </div>
  );
};

export default TopSlider_2;