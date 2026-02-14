import React, { useEffect, useState } from "react";
import axios from "axios";
import { FiPhone, FiMail, FiMapPin } from "react-icons/fi";

const BookAppointment_1 = () => {
  const [data, setData] = useState({
    full_name: "",
    email: "",
    phone: "",
    preferred_date: "",
    preferred_time: "",
    service: "",
    message: ""
  });
  const [errors, setErrors] = useState({});
  const [processing, setProcessing] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");
  const [requestError, setRequestError] = useState("");

  const [services, setServices] = useState([
    "General Checkup",
    "Teeth Cleaning",
    "Teeth Whitening",
    "Dental Implants",
    "Root Canal",
    "Braces/Invisalign",
    "Emergency Dental",
    "Other"
  ]);

  const [timeSlots, setTimeSlots] = useState([
    "9:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
    "2:00 PM", "3:00 PM", "4:00 PM", "5:00 PM"
  ]);

  useEffect(() => {
    const fetchMetadata = async () => {
      try {
        const { data: payload } = await axios.get("/api/online-bookings/metadata");
        if (Array.isArray(payload.services) && payload.services.length > 0) {
          setServices(payload.services);
        }
        if (Array.isArray(payload.time_slots) && payload.time_slots.length > 0) {
          setTimeSlots(payload.time_slots);
        }
      } catch (_) {
      }
    };

    fetchMetadata();
  }, []);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setData((prev) => ({ ...prev, [name]: value }));
  };

  const resetForm = () => {
    setData({
      full_name: "",
      email: "",
      phone: "",
      preferred_date: "",
      preferred_time: "",
      service: "",
      message: ""
    });
  };

  const todayDate = new Date().toISOString().split("T")[0];

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    setErrors({});
    setSuccessMessage("");
    setRequestError("");

    try {
      const response = await axios.post("/api/online-bookings", data);
      const payload = response.data || {};
      setSuccessMessage(payload.message || "Appointment request sent successfully!");
      resetForm();
    } catch (error) {
      if (error.response?.status === 422) {
        setErrors(error.response.data?.errors || {});
      } else {
        setRequestError(error.response?.data?.message || error.message || "Something went wrong. Please try again.");
      }
    } finally {
      setProcessing(false);
    }
  };

  const fieldError = (name) => {
    const errorValue = errors[name];
    if (Array.isArray(errorValue)) {
      return errorValue[0];
    }
    return errorValue;
  };

  return (
    <div className="py-20 bg-gradient-to-b from-white to-blue-50">
      <div className="container mx-auto px-4 lg:px-8">

        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 mb-4">
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
            <span className="text-blue-600 font-semibold tracking-wider">BOOK NOW</span>
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Schedule Your <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Appointment</span>
          </h2>

          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            Book your dental appointment online in minutes. Our team will confirm your booking
            and reach out to you shortly.
          </p>
        </div>

        <div className="max-w-6xl mx-auto">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <div className="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
              <h3 className="text-2xl font-bold text-gray-800 mb-8">
                Book Appointment
              </h3>

              <form onSubmit={handleSubmit} className="space-y-6">
                {successMessage && (
                  <div className="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {successMessage}
                  </div>
                )}
                {requestError && (
                  <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {requestError}
                  </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Full Name *
                    </label>
                    <input
                      type="text"
                      name="full_name"
                      value={data.full_name}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="John Doe"
                    />
                    {fieldError("full_name") && <div className="text-red-500 text-sm mt-1">{fieldError("full_name")}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Email Address *
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={data.email}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="john@example.com"
                    />
                    {fieldError("email") && <div className="text-red-500 text-sm mt-1">{fieldError("email")}</div>}
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Phone Number *
                    </label>
                    <input
                      type="tel"
                      name="phone"
                      value={data.phone}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="(123) 456-7890"
                    />
                    {fieldError("phone") && <div className="text-red-500 text-sm mt-1">{fieldError("phone")}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Preferred Date *
                    </label>
                    <input
                      type="date"
                      name="preferred_date"
                      min={todayDate}
                      value={data.preferred_date}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    />
                    {fieldError("preferred_date") && <div className="text-red-500 text-sm mt-1">{fieldError("preferred_date")}</div>}
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Service Needed *
                    </label>
                    <select
                      name="service"
                      value={data.service}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    >
                      <option value="">Select a service</option>
                      {services.map((service, index) => (
                        <option key={index} value={service}>{service}</option>
                      ))}
                    </select>
                    {fieldError("service") && <div className="text-red-500 text-sm mt-1">{fieldError("service")}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Preferred Time *
                    </label>
                    <select
                      name="preferred_time"
                      value={data.preferred_time}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    >
                      <option value="">Select time</option>
                      {timeSlots.map((time, index) => (
                        <option key={index} value={time}>{time}</option>
                      ))}
                    </select>
                    {fieldError("preferred_time") && <div className="text-red-500 text-sm mt-1">{fieldError("preferred_time")}</div>}
                  </div>
                </div>

                <div>
                  <label className="block text-gray-700 font-medium mb-2">
                    Additional Notes
                  </label>
                  <textarea
                    name="message"
                    rows="4"
                    value={data.message}
                    onChange={handleInputChange}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Any specific concerns or questions..."
                  ></textarea>
                  {fieldError("message") && <div className="text-red-500 text-sm mt-1">{fieldError("message")}</div>}
                </div>

                <button
                  type="submit"
                  disabled={processing}
                  className="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-4 rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70"
                >
                  {processing ? "Sending..." : "Book Appointment Now"}
                </button>

                <p className="text-gray-500 text-sm text-center">
                  By submitting, you agree to our Terms and confirm you've read our Privacy Policy.
                </p>
              </form>
            </div>

            <div className="space-y-8">
              <div className="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 border border-blue-200">
                <h3 className="text-2xl font-bold text-gray-800 mb-6">
                  Contact Information
                </h3>

                <div className="space-y-6">
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                      <FiPhone className="text-blue-600 w-6 h-6" />
                    </div>
                    <div>
                      <h4 className="font-bold text-gray-800">Call Us</h4>
                      <a href="tel:+1234567890" className="text-blue-600 hover:text-blue-700">
                        (123) 456-7890
                      </a>
                    </div>
                  </div>

                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                      <FiMail className="text-blue-600 w-6 h-6" />
                    </div>
                    <div>
                      <h4 className="font-bold text-gray-800">Email Us</h4>
                      <a href="mailto:info@dentalclinic.com" className="text-blue-600 hover:text-blue-700">
                        info@dentalclinic.com
                      </a>
                    </div>
                  </div>

                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                      <FiMapPin className="text-green-600 w-6 h-6" />
                    </div>
                    <div>
                      <h4 className="font-bold text-gray-800">Visit Us</h4>
                      <p className="text-gray-600">
                        123 Dental Street, City, State 12345
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-2xl shadow-lg p-8">
                <h3 className="text-2xl font-bold text-gray-800 mb-6">
                  Office Hours
                </h3>

                <div className="space-y-4">
                  {[
                    { day: "Monday - Friday", time: "8:00 AM - 8:00 PM" },
                    { day: "Saturday", time: "9:00 AM - 5:00 PM" },
                    { day: "Sunday", time: "Emergency Only" }
                  ].map((schedule, index) => (
                    <div key={index} className="flex justify-between items-center py-3 border-b border-gray-100">
                      <span className="font-medium text-gray-700">{schedule.day}</span>
                      <span className="text-gray-600">{schedule.time}</span>
                    </div>
                  ))}
                </div>

                <div className="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                  <div className="flex items-center gap-3">
                    <span className="text-yellow-600">!</span>
                    <p className="text-sm text-yellow-700">
                      Emergency appointments available 24/7. Call for immediate assistance.
                    </p>
                  </div>
                </div>
              </div>

              <div className="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-8 border border-emerald-200">
                <h3 className="text-xl font-bold text-gray-800 mb-4">
                  Why Book With Us?
                </h3>

                <div className="space-y-3">
                  {[
                    "Same-day appointments available",
                    "No hidden fees",
                    "Insurance accepted",
                    "Modern equipment",
                    "Pain-free treatments",
                    "Friendly staff"
                  ].map((feature, index) => (
                    <div key={index} className="flex items-center gap-3">
                      <span className="text-emerald-600">- {feature}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  );
};

export default BookAppointment_1;
