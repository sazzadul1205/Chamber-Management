import React, { useState, useEffect } from "react";
import { FaTooth } from "react-icons/fa";
import { HiMenu, HiX } from "react-icons/hi";

const DefaultNavItems = [
  { id: "home", label: "Home" },
  { id: "about", label: "About" },
  { id: "services", label: "Services" },
  { id: "pricing", label: "Pricing" },
  { id: "dentists", label: "Our Dentists" },
  { id: "testimonials", label: "Testimonials" },
  { id: "news", label: "Latest News" },
  { id: "contact", label: "Contact" },
];

const Navbar = ({ navItems = [] }) => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [activeSection, setActiveSection] = useState("home");

  const finalNavItems = navItems.length > 0 ? navItems : DefaultNavItems;

  // Detect active section smoothly
  useEffect(() => {
    const handleScroll = () => {
      const sections = document.querySelectorAll("section[id]");
      let current = "home";

      sections.forEach((section) => {
        const sectionTop = section.offsetTop - 150;
        const sectionHeight = section.clientHeight;

        if (
          window.scrollY >= sectionTop &&
          window.scrollY < sectionTop + sectionHeight
        ) {
          current = section.id;
        }
      });

      setActiveSection(current);
    };

    window.addEventListener("scroll", handleScroll);
    handleScroll();

    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const scrollToSection = (sectionId) => {
    setIsMobileMenuOpen(false);

    const element = document.getElementById(sectionId);
    if (!element) return;

    const offset = 100;
    const top =
      element.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
      top,
      behavior: "smooth",
    });

    setActiveSection(sectionId);
  };

  const sectionExists = (sectionId) => {
    return document.getElementById(sectionId) !== null;
  };

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">

          {/* Logo */}
          <button
            onClick={() => scrollToSection("home")}
            className="flex items-center space-x-2"
          >
            <FaTooth className="text-blue-600 w-8 h-8" />
            <span className="font-bold text-xl text-blue-700">
              SmileCare
            </span>
          </button>

          {/* Desktop Menu */}
          <ul className="hidden lg:flex space-x-6 font-medium text-gray-700">
            {finalNavItems.map((item) =>
              sectionExists(item.id) ? (
                <li key={item.id}>
                  <button
                    onClick={() => scrollToSection(item.id)}
                    className={`px-2 py-1 transition-colors ${activeSection === item.id
                      ? "text-blue-600 font-semibold border-b-2 border-blue-600"
                      : "hover:text-blue-600"
                      }`}
                  >
                    {item.label}
                  </button>
                </li>
              ) : null
            )}
          </ul>

          {/* Mobile Toggle */}
          <button
            className="lg:hidden text-gray-700"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            {isMobileMenuOpen ? (
              <HiX className="w-6 h-6" />
            ) : (
              <HiMenu className="w-6 h-6" />
            )}
          </button>

          {/* CTA */}
          <button
            onClick={() => scrollToSection("contact")}
            className="hidden lg:block bg-blue-500 px-6 py-2 rounded shadow-xl hover:shadow-2xl text-white"
          >
            Book Now
          </button>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <ul className="lg:hidden mt-2 bg-white shadow-md rounded-lg py-4 space-y-2">
            {finalNavItems.map((item) =>
              sectionExists(item.id) ? (
                <li key={item.id}>
                  <button
                    onClick={() => scrollToSection(item.id)}
                    className={`block w-full text-left px-4 py-2 rounded-lg ${activeSection === item.id
                      ? "bg-blue-50 text-blue-600"
                      : "hover:bg-blue-50"
                      }`}
                  >
                    {item.label}
                  </button>
                </li>
              ) : null
            )}
          </ul>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
