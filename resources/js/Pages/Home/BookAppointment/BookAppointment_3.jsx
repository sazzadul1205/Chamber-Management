import React, { useState } from "react";
import { useForm } from "@inertiajs/react";
import {
  FiCalendar,
  FiPhone,
  FiClock,
  FiTarget,
  FiZap,
  FiShield,
  FiCreditCard,
  FiRefreshCw,
  FiCheckCircle,
  FiAlertTriangle
} from "react-icons/fi";

const BookAppointment_3 = () => {
  const [selectedService, setSelectedService] = useState("");

  const { data, setData, post, processing, errors, reset } = useForm({
    name: "",
    phone: "",
    date: "",
    time: "",
    service: ""
  });

  const services = [
    { id: "emergency", name: "Emergency Dental", icon: <FiAlertTriangle size={26} />, },
    { id: "checkup", name: "General Checkup", icon: <FiCheckCircle size={26} />, },
    { id: "cleaning", name: "Teeth Cleaning", icon: <FiZap size={26} />, },
    { id: "whitening", name: "Teeth Whitening", icon: <FiTarget size={26} />, }
  ];

  const today = new Date();
  const dates = [];
  for (let i = 0; i < 7; i++) {
    const date = new Date(today);
    date.setDate(today.getDate() + i);
    dates.push({
      date: date.toISOString().split("T")[0],
      display:
        i === 0
          ? "Today"
          : i === 1
            ? "Tomorrow"
            : date.toLocaleDateString("en-US", {
              weekday: "short",
              month: "short",
              day: "numeric"
            })
    });
  }

  const timeSlots = ["Morning", "Afternoon", "Evening"];

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!selectedService) return;

    post("/appointments", {
      onSuccess: () => {
        reset();
        setSelectedService("");
        alert("Appointment request received! We’ll call you shortly.");
      }
    });
  };

  const handleServiceSelect = (id) => {
    setSelectedService(id);
    setData("service", id);
  };

  return (
    <div className="py-20 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 px-6 py-2 rounded-full mb-6">
            <FiCalendar className="text-cyan-400" />
            <span className="text-sm font-semibold tracking-wider">QUICK BOOKING</span>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Book Your{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">
              Appointment
            </span>
          </h2>

          <p className="text-gray-300 text-lg max-w-2xl mx-auto">
            Get the care you need quickly. Book online in under 2 minutes.
          </p>
        </div>

        <div className="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

          {/* Service + Form */}
          <div className="lg:col-span-2 bg-gray-800 rounded-2xl border border-gray-700 p-8">

            <h3 className="text-2xl font-bold mb-6 flex items-center gap-3">
              <FiTarget className="text-cyan-400" />
              What do you need?
            </h3>

            <div className="grid grid-cols-2 gap-4 mb-8">
              {services.map((service) => (
                <button
                  key={service.id}
                  type="button"
                  onClick={() => handleServiceSelect(service.id)}
                  className={`p-6 rounded-xl border-2 text-left transition-all ${selectedService === service.id
                      ? "border-cyan-500 bg-cyan-500/10"
                      : "border-gray-700 hover:border-cyan-500/50 hover:bg-cyan-500/5"
                    }`}
                >
                  <div className="flex items-center gap-3">
                    {service.icon}
                    <span className="font-bold">{service.name}</span>
                  </div>
                </button>
              ))}
            </div>
            {errors.service && <p className="text-red-400 mb-4">{errors.service}</p>}

            <form onSubmit={handleSubmit} className="space-y-6">

              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <label className="block mb-2 text-gray-300">Your Name *</label>
                  <input
                    type="text"
                    value={data.name}
                    onChange={(e) => setData("name", e.target.value)}
                    className="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-cyan-500"
                  />
                  {errors.name && <p className="text-red-400 text-sm mt-1">{errors.name}</p>}
                </div>

                <div>
                  <label className="block mb-2 text-gray-300">Phone Number *</label>
                  <input
                    type="tel"
                    value={data.phone}
                    onChange={(e) => setData("phone", e.target.value)}
                    className="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-cyan-500"
                  />
                  {errors.phone && <p className="text-red-400 text-sm mt-1">{errors.phone}</p>}
                </div>
              </div>

              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <label className="block mb-2 text-gray-300">Preferred Date *</label>
                  <select
                    value={data.date}
                    onChange={(e) => setData("date", e.target.value)}
                    className="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-cyan-500"
                  >
                    <option value="">Select date</option>
                    {dates.map((d, i) => (
                      <option key={i} value={d.date}>{d.display}</option>
                    ))}
                  </select>
                  {errors.date && <p className="text-red-400 text-sm mt-1">{errors.date}</p>}
                </div>

                <div>
                  <label className="block mb-2 text-gray-300">Time Preference *</label>
                  <select
                    value={data.time}
                    onChange={(e) => setData("time", e.target.value)}
                    className="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-cyan-500"
                  >
                    <option value="">Select time</option>
                    {timeSlots.map((t, i) => (
                      <option key={i} value={t}>{t}</option>
                    ))}
                  </select>
                  {errors.time && <p className="text-red-400 text-sm mt-1">{errors.time}</p>}
                </div>
              </div>

              <button
                type="submit"
                disabled={!selectedService || processing}
                className={`w-full py-4 rounded-xl font-semibold transition ${!selectedService || processing
                    ? "bg-gray-700 text-gray-400 cursor-not-allowed"
                    : "bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600"
                  }`}
              >
                {processing ? "Sending..." : "Request Appointment"}
              </button>
            </form>
          </div>

          {/* Side Panel */}
          <div className="space-y-6">

            <div className="bg-gray-800 rounded-2xl border border-gray-700 p-6">
              <h4 className="font-bold text-lg mb-4 flex items-center gap-2">
                <FiPhone className="text-cyan-400" />
                Call Us
              </h4>
              <a href="tel:+1234567890" className="text-xl font-bold hover:text-cyan-300">
                (123) 456-7890
              </a>
              <p className="text-gray-400 text-sm mt-2">Available 24/7</p>
            </div>

            <div className="bg-gray-800 rounded-2xl border border-gray-700 p-6">
              <h4 className="font-bold text-lg mb-4 flex items-center gap-2">
                <FiClock className="text-cyan-400" />
                Hours
              </h4>
              <div className="space-y-2 text-gray-300 text-sm">
                <div className="flex justify-between"><span>Mon - Fri</span><span>8AM - 8PM</span></div>
                <div className="flex justify-between"><span>Saturday</span><span>9AM - 5PM</span></div>
                <div className="flex justify-between"><span>Sunday</span><span>Emergency Only</span></div>
              </div>
            </div>

          </div>
        </div>

        {/* Trust Section */}
        <div className="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
          <div>
            <FiShield className="mx-auto mb-2 text-cyan-400" size={28} />
            <div className="font-bold">Secure Booking</div>
          </div>
          <div>
            <FiCreditCard className="mx-auto mb-2 text-cyan-400" size={28} />
            <div className="font-bold">No Card Required</div>
          </div>
          <div>
            <FiRefreshCw className="mx-auto mb-2 text-cyan-400" size={28} />
            <div className="font-bold">Easy Reschedule</div>
          </div>
          <div>
            <FiCheckCircle className="mx-auto mb-2 text-cyan-400" size={28} />
            <div className="font-bold">Instant Confirmation</div>
          </div>
        </div>

      </div>
    </div>
  );
};

export default BookAppointment_3;
