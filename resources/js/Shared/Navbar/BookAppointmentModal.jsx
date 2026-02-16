import React, { useEffect, useState } from "react";
import axios from "axios";
import { HiX } from "react-icons/hi";

const BookAppointmentModal = ({ isOpen, onClose }) => {
  const [data, setData] = useState({
    full_name: "",
    email: "",
    phone: "",
    preferred_date: "",
    preferred_time: "",
    service: "",
    message: ""
  });

  const [services, setServices] = useState([]);
  const [timeSlots, setTimeSlots] = useState([]);
  const [errors, setErrors] = useState({});
  const [processing, setProcessing] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");
  const [requestError, setRequestError] = useState("");

  const todayDate = new Date().toISOString().split("T")[0];

  useEffect(() => {
    if (!isOpen) return;

    document.body.style.overflow = "hidden";

    const fetchMetadata = async () => {
      try {
        const { data: payload } = await axios.get("/api/online-bookings/metadata");
        setServices(payload.services || []);
        setTimeSlots(payload.time_slots || []);
      } catch (err) {
        console.error("Metadata fetch failed");
      }
    };

    fetchMetadata();

    return () => {
      document.body.style.overflow = "auto";
    };
  }, [isOpen]);

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

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    setErrors({});
    setSuccessMessage("");
    setRequestError("");

    try {
      const response = await axios.post("/api/online-bookings", data);
      setSuccessMessage(response.data?.message || "Appointment sent successfully!");
      resetForm();
    } catch (error) {
      if (error.response?.status === 422) {
        setErrors(error.response.data?.errors || {});
      } else {
        setRequestError(
          error.response?.data?.message ||
          "Something went wrong. Try again."
        );
      }
    } finally {
      setProcessing(false);
    }
  };

  const fieldError = (name) => {
    const errorValue = errors[name];
    return Array.isArray(errorValue) ? errorValue[0] : errorValue;
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[999] bg-black/50 text-black flex items-center justify-center px-4">
      <div className="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

        <button
          onClick={onClose}
          className="absolute top-4 right-4 text-gray-500 hover:text-red-500"
        >
          <HiX size={24} />
        </button>

        <h2 className="text-2xl font-bold mb-6">Book Appointment</h2>

        <form onSubmit={handleSubmit} className="space-y-5">

          {successMessage && (
            <div className="bg-green-50 text-green-700 p-3 rounded-lg">
              {successMessage}
            </div>
          )}

          {requestError && (
            <div className="bg-red-50 text-red-700 p-3 rounded-lg">
              {requestError}
            </div>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input
                type="text"
                name="full_name"
                placeholder="Full Name"
                value={data.full_name}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              />
              {fieldError("full_name") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("full_name")}</p>
              )}
            </div>

            <div>
              <input
                type="email"
                name="email"
                placeholder="Email"
                value={data.email}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              />
              {fieldError("email") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("email")}</p>
              )}
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input
                type="tel"
                name="phone"
                placeholder="Phone"
                value={data.phone}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              />
              {fieldError("phone") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("phone")}</p>
              )}
            </div>

            <div>
              <input
                type="date"
                name="preferred_date"
                min={todayDate}
                value={data.preferred_date}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              />
              {fieldError("preferred_date") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("preferred_date")}</p>
              )}
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <select
                name="service"
                value={data.service}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              >
                <option value="">Select Service</option>
                {services.map((s, i) => (
                  <option key={i} value={s}>{s}</option>
                ))}
              </select>
              {fieldError("service") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("service")}</p>
              )}
            </div>

            <div>
              <select
                name="preferred_time"
                value={data.preferred_time}
                onChange={handleInputChange}
                className="border w-full p-3 rounded-lg"
              >
                <option value="">Select Time</option>
                {timeSlots.map((t, i) => (
                  <option key={i} value={t}>{t}</option>
                ))}
              </select>
              {fieldError("preferred_time") && (
                <p className="text-red-500 text-sm mt-1">{fieldError("preferred_time")}</p>
              )}
            </div>
          </div>

          <textarea
            name="message"
            rows="3"
            placeholder="Additional Notes"
            value={data.message}
            onChange={handleInputChange}
            className="border w-full p-3 rounded-lg"
          />

          <button
            type="submit"
            disabled={processing}
            className="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition disabled:opacity-60"
          >
            {processing ? "Sending..." : "Submit Appointment"}
          </button>
        </form>
      </div>
    </div>
  );
};

export default BookAppointmentModal;
