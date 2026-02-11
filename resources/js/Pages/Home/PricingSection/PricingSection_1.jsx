import React from "react";

const PricingSection_1 = () => {
  const plans = [
    {
      name: "Basic Care",
      price: "89",
      period: "per month",
      color: "from-blue-500 to-cyan-400",
      bgColor: "from-blue-50 to-cyan-50",
      popular: false,
      features: [
        "2 Dental Cleanings per year",
        "Comprehensive Exam",
        "Digital X-rays (2 per year)",
        "Emergency Exam",
        "15% Discount on Treatments",
        "Priority Scheduling"
      ],
      excluded: ["Cosmetic Procedures", "Major Restorations", "Orthodontics"]
    },
    {
      name: "Complete Care",
      price: "149",
      period: "per month",
      color: "from-emerald-500 to-teal-400",
      bgColor: "from-emerald-50 to-teal-50",
      popular: true,
      features: [
        "4 Dental Cleanings per year",
        "Unlimited Exams",
        "Digital X-rays (4 per year)",
        "Emergency Care Included",
        "25% Discount on Treatments",
        "Teeth Whitening (1 per year)",
        "Fluoride Treatments",
        "Same-Day Appointments"
      ],
      excluded: ["Dental Implants", "Orthodontics"]
    },
    {
      name: "Family Plan",
      price: "299",
      period: "per family/month",
      color: "from-purple-500 to-pink-400",
      bgColor: "from-purple-50 to-pink-50",
      popular: false,
      features: [
        "Covers 2 Adults + 2 Children",
        "8 Cleanings total per year",
        "Unlimited Exams for all",
        "Digital X-rays for all",
        "Emergency Care Included",
        "35% Discount on Treatments",
        "Children's Sealants",
        "Orthodontic Consultation",
        "24/7 Dental Advice Line"
      ],
      excluded: ["Cosmetic Procedures"]
    }
  ];

  const addOns = [
    { name: "Teeth Whitening", price: "$299", original: "$450" },
    { name: "Dental Implant", price: "$2,500", original: "$3,200" },
    { name: "Invisalign Treatment", price: "$3,999", original: "$4,800" },
    { name: "Porcelain Veneer", price: "$950", original: "$1,200" }
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 mb-4 justify-center">
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
            <span className="text-blue-600 font-semibold tracking-wider">TRANSPARENT PRICING</span>
            <div className="w-12 h-1 bg-blue-500 rounded-full"></div>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
            Affordable <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Dental Plans</span>
          </h2>
          <p className="text-gray-600 text-lg max-w-3xl mx-auto">
            No hidden fees, no surprises. Choose the plan that fits your needs and budget.
            All plans include preventive care and significant discounts on treatments.
          </p>
        </div>

        {/* Pricing Cards */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto mb-16">
          {plans.map((plan, index) => (
            <div
              key={index}
              className={`relative rounded-2xl overflow-hidden border-2 ${plan.popular ? 'border-emerald-200 shadow-2xl' : 'border-gray-200'} bg-white`}
            >
              {/* Popular Badge */}
              {plan.popular && (
                <div className="absolute top-0 right-0">
                  <div className="bg-gradient-to-r from-emerald-500 to-teal-400 text-white px-6 py-2 rounded-bl-lg">
                    <span className="font-semibold text-sm">MOST POPULAR</span>
                  </div>
                </div>
              )}

              <div className={`p-8 ${plan.popular ? 'bg-gradient-to-br' : ''} ${plan.bgColor}`}>
                {/* Plan Name */}
                <h3 className="text-2xl font-bold text-gray-800 mb-4">{plan.name}</h3>

                {/* Price */}
                <div className="mb-6">
                  <div className="flex items-baseline">
                    <span className="text-5xl font-bold text-gray-800">${plan.price}</span>
                    <span className="text-gray-600 ml-2">{plan.period}</span>
                  </div>
                  <p className="text-gray-500 text-sm mt-1">Billed annually or monthly</p>
                </div>

                {/* CTA Button */}
                <button className={`w-full bg-gradient-to-r ${plan.color} text-white font-semibold py-4 rounded-xl mb-8 hover:opacity-90 transition-opacity duration-300`}>
                  Get Started
                </button>

                {/* Features */}
                <div className="space-y-4">
                  <h4 className="font-bold text-gray-700">What's Included:</h4>
                  {plan.features.map((feature, idx) => (
                    <div key={idx} className="flex items-start gap-3">
                      <div className="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg className="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                      </div>
                      <span className="text-gray-700">{feature}</span>
                    </div>
                  ))}

                  {/* Excluded */}
                  {plan.excluded.length > 0 && (
                    <>
                      <h4 className="font-bold text-gray-700 mt-6">Not Included:</h4>
                      {plan.excluded.map((exclude, idx) => (
                        <div key={idx} className="flex items-start gap-3">
                          <div className="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg className="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                              <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                            </svg>
                          </div>
                          <span className="text-gray-500">{exclude}</span>
                        </div>
                      ))}
                    </>
                  )}
                </div>
              </div>

              {/* Bottom Accent */}
              <div className={`h-2 bg-gradient-to-r ${plan.color}`}></div>
            </div>
          ))}
        </div>

        {/* Add-ons Section */}
        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h3 className="text-2xl font-bold text-gray-800 mb-6 text-center">
              Additional Treatments
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {addOns.map((addOn, index) => (
                <div key={index} className="border border-gray-200 rounded-xl p-6 hover:border-blue-200 transition-colors">
                  <h4 className="font-bold text-gray-800 mb-2">{addOn.name}</h4>
                  <div className="flex items-baseline gap-2 mb-3">
                    <span className="text-2xl font-bold text-blue-600">{addOn.price}</span>
                    <span className="text-gray-400 line-through text-sm">{addOn.original}</span>
                  </div>
                  <p className="text-gray-600 text-sm mb-4">Member price with any plan</p>
                  <button className="w-full text-blue-600 hover:text-blue-700 font-semibold py-2 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                    Add to Plan
                  </button>
                </div>
              ))}
            </div>

            {/* Savings Notice */}
            <div className="mt-8 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
              <div className="flex items-center justify-between">
                <div>
                  <h4 className="font-bold text-gray-800 mb-1">Save Up to 40%</h4>
                  <p className="text-gray-600 text-sm">Compared to paying for services individually</p>
                </div>
                <div className="text-right">
                  <div className="text-3xl font-bold text-emerald-600">40%</div>
                  <div className="text-sm text-gray-600">Average Savings</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* FAQ */}
        <div className="mt-16 max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-800 mb-8 text-center">
            Frequently Asked Questions
          </h3>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {[
              { q: "Can I use my insurance with these plans?", a: "Yes! Our plans work alongside most insurance policies to maximize your benefits." },
              { q: "Is there a waiting period?", a: "No waiting period for preventive care. Some major services have a 6-month waiting period." },
              { q: "Can I cancel anytime?", a: "Yes, you can cancel your membership at any time with 30 days notice." },
              { q: "Do you offer payment plans?", a: "Yes, we offer interest-free financing options for qualified patients." }
            ].map((faq, index) => (
              <div key={index} className="bg-white rounded-xl p-6 border border-gray-200">
                <h4 className="font-bold text-gray-800 mb-3">{faq.q}</h4>
                <p className="text-gray-600">{faq.a}</p>
              </div>
            ))}
          </div>
        </div>

      </div>
    </div>
  );
};

export default PricingSection_1;
