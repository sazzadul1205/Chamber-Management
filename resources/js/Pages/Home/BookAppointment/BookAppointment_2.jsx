import React, { useState } from "react";
import { useForm } from "@inertiajs/react";
import { FiUser, FiClock, FiCalendar, FiPhone, FiMail, FiActivity } from "react-icons/fi";

const BookAppointment_2 = () => {
  const [step, setStep] = useState(1);

  const { data, setData, post, processing, errors, reset } = useForm({
    patientType: "",
    service: "",
    date: "",
    time: "",
    name: "",
    email: "",
    phone: "",
    notes: ""
  });

  const patientTypes = [
    { id: "new", label: "New Patient", description: "First time visiting our clinic", icon: <FiUser className="w-6 h-6 text-blue-500" /> },
    { id: "existing", label: "Existing Patient", description: "Have visited us before", icon: <FiUser className="w-6 h-6 text-green-500" /> },
    { id: "emergency", label: "Emergency", description: "Need immediate care", icon: <FiActivity className="w-6 h-6 text-red-500" /> }
  ];

  const services = [
    { id: "checkup", name: "General Checkup", duration: "30 min", icon: <FiActivity className="w-6 h-6" /> },
    { id: "cleaning", name: "Teeth Cleaning", duration: "45 min", icon: <FiActivity className="w-6 h-6" /> },
    { id: "whitening", name: "Teeth Whitening", duration: "60 min", icon: <FiActivity className="w-6 h-6" /> },
    { id: "implant", name: "Dental Implant", duration: "2+ visits", icon: <FiActivity className="w-6 h-6" /> },
    { id: "braces", name: "Braces Consultation", duration: "45 min", icon: <FiActivity className="w-6 h-6" /> },
    { id: "emergency", name: "Emergency Dental", duration: "30-60 min", icon: <FiActivity className="w-6 h-6 text-red-500" /> }
  ];

  const dates = [
    { date: "Today", available: true },
    { date: "Tomorrow", available: true },
    { date: "In 2 days", available: true },
    { date: "In 3 days", available: false },
    { date: "Next Week", available: true }
  ];

  const timeSlots = [
    "9:00 AM", "10:00 AM", "11:00 AM",
    "2:00 PM", "3:00 PM", "4:00 PM", "5:00 PM"
  ];

  const handleNext = () => { if (step < 4) setStep(step + 1); };
  const handleBack = () => { if (step > 1) setStep(step - 1); };

  const handleSubmit = (e) => {
    e.preventDefault();
    post("/appointments", {
      onSuccess: () => {
        alert("Appointment booked successfully! We'll contact you shortly.");
        reset();
        setStep(1);
      }
    });
  };

  const handleSelect = (field, value) => setData(field, value);

  return (
    <div className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-12">
          <div className="inline-block mb-4">
            <span className="text-blue-600 font-semibold tracking-wider text-sm">QUICK BOOKING</span>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Book Your <span className="text-blue-600">Appointment</span> in 4 Easy Steps
          </h2>
          <p className="text-gray-600 text-lg max-w-2xl mx-auto">
            Our simple booking process gets you scheduled quickly and conveniently.
          </p>
        </div>

        <div className="max-w-4xl mx-auto">
          {/* Progress Steps */}
          <div className="mb-12">
            <div className="flex justify-between items-center relative">
              <div className="absolute top-5 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
              <div
                className="absolute top-5 left-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-400 -z-10 transition-all duration-500"
                style={{ width: `${(step - 1) * 33.33}%` }}
              ></div>

              {[1, 2, 3, 4].map((stepNumber) => (
                <div key={stepNumber} className="flex flex-col items-center">
                  <div className={`
                    w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                    ${step === stepNumber
                      ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-lg'
                      : step > stepNumber
                        ? 'bg-green-500 text-white'
                        : 'bg-white text-gray-400 border-2 border-gray-300'
                    }
                  `}>
                    {step > stepNumber ? "✓" : stepNumber}
                  </div>
                  <span className="mt-2 text-sm font-medium text-gray-600">
                    {stepNumber === 1 && "Patient Type"}
                    {stepNumber === 2 && "Service"}
                    {stepNumber === 3 && "Date & Time"}
                    {stepNumber === 4 && "Details"}
                  </span>
                </div>
              ))}
            </div>
          </div>

          {/* Form Container */}
          <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div className="p-8 lg:p-10">
              <form onSubmit={handleSubmit}>
                {/* Step 1: Patient Type */}
                {step === 1 && (
                  <div className="space-y-8">
                    <h3 className="text-2xl font-bold text-gray-800 mb-2">
                      Are you a new or existing patient?
                    </h3>
                    <p className="text-gray-600 mb-6">
                      Select the option that best describes your situation.
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      {patientTypes.map((type) => (
                        <button
                          key={type.id}
                          type="button"
                          onClick={() => handleSelect('patientType', type.id)}
                          className={`p-6 rounded-xl border-2 text-left transition-all duration-300 flex items-center gap-3 ${data.patientType === type.id
                              ? 'border-blue-500 bg-blue-50'
                              : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/50'
                            }`}
                        >
                          {type.icon}
                          <div>
                            <div className="font-bold text-gray-800 text-lg mb-1">{type.label}</div>
                            <p className="text-gray-600 text-sm">{type.description}</p>
                          </div>
                        </button>
                      ))}
                    </div>
                    {errors.patientType && <p className="text-red-500 mt-2">{errors.patientType}</p>}
                  </div>
                )}

                {/* Step 2: Service */}
                {step === 2 && (
                  <div className="space-y-8">
                    <h3 className="text-2xl font-bold text-gray-800 mb-2">
                      What service do you need?
                    </h3>
                    <p className="text-gray-600 mb-6">
                      Select the dental service you're interested in.
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      {services.map((service) => (
                        <button
                          key={service.id}
                          type="button"
                          onClick={() => handleSelect('service', service.id)}
                          className={`p-6 rounded-xl border-2 text-left transition-all duration-300 flex items-center gap-3 ${data.service === service.id
                              ? 'border-blue-500 bg-blue-50'
                              : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/50'
                            }`}
                        >
                          {service.icon}
                          <div>
                            <div className="font-bold text-gray-800">{service.name}</div>
                            <div className="text-sm text-gray-500">{service.duration}</div>
                          </div>
                        </button>
                      ))}
                    </div>
                    {errors.service && <p className="text-red-500 mt-2">{errors.service}</p>}
                  </div>
                )}

                {/* Step 3: Date & Time */}
                {step === 3 && (
                  <div className="space-y-8">
                    <h3 className="text-2xl font-bold text-gray-800 mb-2">
                      Choose Date & Time
                    </h3>
                    <p className="text-gray-600 mb-6">
                      Select your preferred appointment slot.
                    </p>

                    <div className="space-y-6">
                      <div>
                        <label className="block text-gray-700 font-medium mb-4">Select Date</label>
                        <div className="flex flex-wrap gap-3">
                          {dates.map((item, index) => (
                            <button
                              key={index}
                              type="button"
                              onClick={() => handleSelect('date', item.date)}
                              className={`px-6 py-3 rounded-lg border transition-all duration-300 ${data.date === item.date
                                  ? 'bg-blue-600 text-white border-blue-600'
                                  : item.available
                                    ? 'border-gray-300 hover:border-blue-500 hover:bg-blue-50'
                                    : 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'
                                } ${!item.available && 'opacity-50'}`}
                              disabled={!item.available}
                            >
                              {item.date}
                            </button>
                          ))}
                        </div>
                        {errors.date && <p className="text-red-500 mt-2">{errors.date}</p>}
                      </div>

                      <div>
                        <label className="block text-gray-700 font-medium mb-4">Select Time</label>
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                          {timeSlots.map((time, index) => (
                            <button
                              key={index}
                              type="button"
                              onClick={() => handleSelect('time', time)}
                              className={`py-3 rounded-lg border transition-all duration-300 ${data.time === time
                                  ? 'bg-blue-600 text-white border-blue-600'
                                  : 'border-gray-300 hover:border-blue-500 hover:bg-blue-50'
                                }`}
                            >
                              {time}
                            </button>
                          ))}
                        </div>
                        {errors.time && <p className="text-red-500 mt-2">{errors.time}</p>}
                      </div>
                    </div>
                  </div>
                )}

                {/* Step 4: Details */}
                {step === 4 && (
                  <div className="space-y-8">
                    <h3 className="text-2xl font-bold text-gray-800 mb-2">Your Contact Information</h3>
                    <p className="text-gray-600 mb-6">We'll use this to confirm your appointment.</p>

                    <div className="space-y-6">
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <label className="block text-gray-700 font-medium mb-2">Full Name *</label>
                          <input
                            type="text"
                            name="name"
                            required
                            value={data.name}
                            onChange={e => setData("name", e.target.value)}
                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="John Doe"
                          />
                          {errors.name && <p className="text-red-500 mt-1">{errors.name}</p>}
                        </div>

                        <div>
                          <label className="block text-gray-700 font-medium mb-2">Email Address *</label>
                          <input
                            type="email"
                            name="email"
                            required
                            value={data.email}
                            onChange={e => setData("email", e.target.value)}
                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="john@example.com"
                          />
                          {errors.email && <p className="text-red-500 mt-1">{errors.email}</p>}
                        </div>
                      </div>

                      <div>
                        <label className="block text-gray-700 font-medium mb-2">Phone Number *</label>
                        <input
                          type="tel"
                          name="phone"
                          required
                          value={data.phone}
                          onChange={e => setData("phone", e.target.value)}
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                          placeholder="(123) 456-7890"
                        />
                        {errors.phone && <p className="text-red-500 mt-1">{errors.phone}</p>}
                      </div>

                      <div>
                        <label className="block text-gray-700 font-medium mb-2">Additional Notes (Optional)</label>
                        <textarea
                          name="notes"
                          rows="3"
                          value={data.notes}
                          onChange={e => setData("notes", e.target.value)}
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                          placeholder="Any specific concerns or questions..."
                        ></textarea>
                        {errors.notes && <p className="text-red-500 mt-1">{errors.notes}</p>}
                      </div>
                    </div>
                  </div>
                )}

                {/* Navigation Buttons */}
                <div className="flex justify-between mt-12 pt-8 border-t border-gray-200">
                  {step > 1 && (
                    <button
                      type="button"
                      onClick={handleBack}
                      className="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
                    >
                      ← Back
                    </button>
                  )}

                  {step < 4 ? (
                    <button
                      type="button"
                      onClick={handleNext}
                      disabled={
                        (step === 1 && !data.patientType) ||
                        (step === 2 && !data.service) ||
                        (step === 3 && (!data.date || !data.time))
                      }
                      className={`ml-auto px-8 py-3 rounded-xl font-semibold transition-all duration-300 ${(step === 1 && !data.patientType) ||
                          (step === 2 && !data.service) ||
                          (step === 3 && (!data.date || !data.time))
                          ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                          : 'bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white hover:shadow-lg'
                        }`}
                    >
                      Continue →
                    </button>
                  ) : (
                    <button
                      type="submit"
                      disabled={processing}
                      className="ml-auto bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl"
                    >
                      {processing ? "Sending..." : "Confirm Booking"}
                    </button>
                  )}
                </div>
              </form>
            </div>

            {/* Summary Sidebar */}
            <div className="bg-gradient-to-b from-blue-50 to-cyan-50 p-8 border-t border-blue-200">
              <h4 className="font-bold text-gray-800 mb-4">Appointment Summary</h4>

              <div className="space-y-4">
                <div className="flex justify-between items-center">
                  <span className="text-gray-600 flex items-center gap-1"><FiUser /> Patient Type:</span>
                  <span className="font-medium text-gray-800">
                    {data.patientType === 'new' && 'New Patient'}
                    {data.patientType === 'existing' && 'Existing Patient'}
                    {data.patientType === 'emergency' && 'Emergency'}
                    {!data.patientType && 'Not selected'}
                  </span>
                </div>

                <div className="flex justify-between items-center">
                  <span className="text-gray-600 flex items-center gap-1"><FiActivity /> Service:</span>
                  <span className="font-medium text-gray-800">
                    {services.find(s => s.id === data.service)?.name || 'Not selected'}
                  </span>
                </div>

                <div className="flex justify-between items-center">
                  <span className="text-gray-600 flex items-center gap-1"><FiCalendar /> Date:</span>
                  <span className="font-medium text-gray-800">{data.date || 'Not selected'}</span>
                </div>

                <div className="flex justify-between items-center">
                  <span className="text-gray-600 flex items-center gap-1"><FiClock /> Time:</span>
                  <span className="font-medium text-gray-800">{data.time || 'Not selected'}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Quick Contact */}
          <div className="mt-8 text-center">
            <p className="text-gray-600">
              Need help? Call us at{" "}
              <a href="tel:+1234567890" className="text-blue-600 font-semibold hover:text-blue-700">
                (123) 456-7890
              </a>{" "}
              or{" "}
              <a href="mailto:info@dentalclinic.com" className="text-blue-600 font-semibold hover:text-blue-700">
                email us
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default BookAppointment_2;
