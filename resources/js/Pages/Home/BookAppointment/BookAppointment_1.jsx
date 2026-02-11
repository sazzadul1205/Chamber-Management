import React from "react";
import { useForm } from "@inertiajs/react";
import { FiPhone, FiMail, FiMapPin } from "react-icons/fi";

const BookAppointment_1 = () => {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: "",
    email: "",
    phone: "",
    date: "",
    time: "",
    service: "",
    message: ""
  });

  const services = [
    "General Checkup",
    "Teeth Cleaning",
    "Teeth Whitening",
    "Dental Implants",
    "Root Canal",
    "Braces/Invisalign",
    "Emergency Dental",
    "Other"
  ];

  const timeSlots = [
    "9:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
    "2:00 PM", "3:00 PM", "4:00 PM", "5:00 PM"
  ];

  const handleSubmit = (e) => {
    e.preventDefault();
    post("/appointments", {
      onSuccess: () => {
        alert("Appointment request sent successfully!");
        reset();
      }
    });
  };

  return (
    <div className="py-20 bg-gradient-to-b from-white to-blue-50">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
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

            {/* Left Side - Form */}
            <div className="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
              <h3 className="text-2xl font-bold text-gray-800 mb-8">
                Book Appointment
              </h3>

              <form onSubmit={handleSubmit} className="space-y-6">
                {/* Name & Email */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Full Name *
                    </label>
                    <input
                      type="text"
                      name="name"
                      value={data.name}
                      onChange={e => setData("name", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="John Doe"
                    />
                    {errors.name && <div className="text-red-500 text-sm mt-1">{errors.name}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Email Address *
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={data.email}
                      onChange={e => setData("email", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="john@example.com"
                    />
                    {errors.email && <div className="text-red-500 text-sm mt-1">{errors.email}</div>}
                  </div>
                </div>

                {/* Phone & Date */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Phone Number *
                    </label>
                    <input
                      type="tel"
                      name="phone"
                      value={data.phone}
                      onChange={e => setData("phone", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="(123) 456-7890"
                    />
                    {errors.phone && <div className="text-red-500 text-sm mt-1">{errors.phone}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Preferred Date *
                    </label>
                    <input
                      type="date"
                      name="date"
                      value={data.date}
                      onChange={e => setData("date", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    />
                    {errors.date && <div className="text-red-500 text-sm mt-1">{errors.date}</div>}
                  </div>
                </div>

                {/* Service & Time */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Service Needed *
                    </label>
                    <select
                      name="service"
                      value={data.service}
                      onChange={e => setData("service", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    >
                      <option value="">Select a service</option>
                      {services.map((service, index) => (
                        <option key={index} value={service}>{service}</option>
                      ))}
                    </select>
                    {errors.service && <div className="text-red-500 text-sm mt-1">{errors.service}</div>}
                  </div>

                  <div>
                    <label className="block text-gray-700 font-medium mb-2">
                      Preferred Time *
                    </label>
                    <select
                      name="time"
                      value={data.time}
                      onChange={e => setData("time", e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    >
                      <option value="">Select time</option>
                      {timeSlots.map((time, index) => (
                        <option key={index} value={time}>{time}</option>
                      ))}
                    </select>
                    {errors.time && <div className="text-red-500 text-sm mt-1">{errors.time}</div>}
                  </div>
                </div>

                {/* Message */}
                <div>
                  <label className="block text-gray-700 font-medium mb-2">
                    Additional Notes
                  </label>
                  <textarea
                    name="message"
                    rows="4"
                    value={data.message}
                    onChange={e => setData("message", e.target.value)}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Any specific concerns or questions..."
                  ></textarea>
                  {errors.message && <div className="text-red-500 text-sm mt-1">{errors.message}</div>}
                </div>

                {/* Submit Button */}
                <button
                  type="submit"
                  disabled={processing}
                  className="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-4 rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl"
                >
                  {processing ? "Sending..." : "Book Appointment Now"}
                </button>

                <p className="text-gray-500 text-sm text-center">
                  By submitting, you agree to our Terms and confirm you've read our Privacy Policy.
                </p>
              </form>
            </div>

            {/* Right Side - Info */}
            <div className="space-y-8">
              {/* Contact Info */}
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

              {/* Office Hours */}
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
                    <span className="text-yellow-600">⚠️</span>
                    <p className="text-sm text-yellow-700">
                      Emergency appointments available 24/7. Call for immediate assistance.
                    </p>
                  </div>
                </div>
              </div>

              {/* Why Choose */}
              <div className="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-8 border border-emerald-200">
                <h3 className="text-xl font-bold text-gray-800 mb-4">
                  Why Book With Us?
                </h3>

                <div className="space-y-3">
                  {[
                    "✅ Same-day appointments available",
                    "✅ No hidden fees",
                    "✅ Insurance accepted",
                    "✅ Modern equipment",
                    "✅ Pain-free treatments",
                    "✅ Friendly staff"
                  ].map((feature, index) => (
                    <div key={index} className="flex items-center gap-3">
                      <span className="text-emerald-600">{feature}</span>
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
