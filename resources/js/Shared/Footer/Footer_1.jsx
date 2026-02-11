import React from "react";
import { FaFacebookF, FaYoutube, FaLinkedinIn } from "react-icons/fa";
import { GiTooth } from "react-icons/gi";

const Footer_1 = () => {
  return (
    <footer className="footer p-10 bg-blue-50 text-blue-900 sm:footer-horizontal shadow-lg">
      {/* Branding / About */}
      <div>
        <div className="flex items-center gap-2 mb-2">
          <GiTooth size={40} className="text-blue-600" />
          <span className="font-bold text-xl">SmileCare Dental</span>
        </div>
        <p className="text-sm text-gray-700">
          Providing expert dental care with a gentle touch.<br />
          Your smile, our priority since 2005.
        </p>
      </div>

      {/* Services / Links */}
      <div>
        <span className="footer-title">Services</span>
        <a className="link link-hover">Teeth Cleaning</a>
        <a className="link link-hover">Orthodontics</a>
        <a className="link link-hover">Cosmetic Dentistry</a>
        <a className="link link-hover">Pediatric Dentistry</a>
      </div>

      {/* Contact / Social */}
      <div>
        <span className="footer-title">Follow Us</span>
        <div className="flex gap-3 mt-2">
          <a className="btn btn-circle btn-outline btn-sm hover:bg-blue-600 hover:text-white transition-colors">
            <FaFacebookF size={16} />
          </a>
          <a className="btn btn-circle btn-outline btn-sm hover:bg-red-600 hover:text-white transition-colors">
            <FaYoutube size={16} />
          </a>
          <a className="btn btn-circle btn-outline btn-sm hover:bg-blue-700 hover:text-white transition-colors">
            <FaLinkedinIn size={16} />
          </a>
        </div>
        <p className="mt-4 text-gray-600 text-sm">
          123 Dental Street, Tooth City, SmileState<br />
          info@smilecare.com | +1 (555) 123-4567
        </p>
      </div>

      {/* Footer bottom */}
      <div className="sm:mt-4 text-center sm:text-left col-span-full text-gray-500 text-xs">
        &copy; {new Date().getFullYear()} SmileCare Dental. All rights reserved.
      </div>
    </footer>
  );
};

export default Footer_1;
