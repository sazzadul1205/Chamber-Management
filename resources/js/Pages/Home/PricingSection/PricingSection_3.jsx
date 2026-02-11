import React from "react";
import { FiDollarSign, FiCheck, FiX, FiPhone, FiMail, FiArrowRight, FiActivity } from "react-icons/fi";

const PricingSection_3 = () => {
  const plans = [
    {
      name: "Pay Per Service",
      description: "Pay only for what you need",
      price: "Varies",
      popular: false,
      features: [
        { included: true, text: "No membership fee" },
        { included: true, text: "Pay as you go" },
        { included: true, text: "Flexible scheduling" },
        { included: false, text: "Preventive care discounts" },
        { included: false, text: "Emergency care coverage" },
        { included: false, text: "Treatment discounts" },
        { included: false, text: "Whitening included" }
      ],
      bestFor: ["Infrequent visitors", "Budget flexibility"]
    },
    {
      name: "Basic Membership",
      description: "Essential preventive care",
      price: "$29",
      period: "/month",
      popular: false,
      features: [
        { included: true, text: "2 cleanings per year" },
        { included: true, text: "Annual exam & X-rays" },
        { included: true, text: "15% treatment discount" },
        { included: true, text: "Emergency exam included" },
        { included: false, text: "Priority scheduling" },
        { included: false, text: "Whitening treatment" },
        { included: false, text: "Family coverage" }
      ],
      bestFor: ["Individuals", "Basic preventive care"]
    },
    {
      name: "Premium Membership",
      description: "Complete dental coverage",
      price: "$79",
      period: "/month",
      popular: true,
      features: [
        { included: true, text: "4 cleanings per year" },
        { included: true, text: "Unlimited exams" },
        { included: true, text: "25% treatment discount" },
        { included: true, text: "Emergency care coverage" },
        { included: true, text: "Priority scheduling" },
        { included: true, text: "Annual whitening" },
        { included: true, text: "Family discounts" }
      ],
      bestFor: ["Frequent visitors", "Families", "Comprehensive care"]
    },
    {
      name: "Family Plan",
      description: "Coverage for the whole family",
      price: "$149",
      period: "/month",
      popular: false,
      features: [
        { included: true, text: "Covers 4 family members" },
        { included: true, text: "16 cleanings total/year" },
        { included: true, text: "35% treatment discount" },
        { included: true, text: "Emergency care for all" },
        { included: true, text: "24/7 dental advice" },
        { included: true, text: "Whitening for adults" },
        { included: true, text: "Children's sealants" }
      ],
      bestFor: ["Families of 4", "Maximum savings"]
    }
  ];

  const savings = [
    { service: "Teeth Cleaning", regular: "$240", member: "$0", savings: "$240" },
    { service: "Dental Exam", regular: "$300", member: "$0", savings: "$300" },
    { service: "X-rays", regular: "$180", member: "$0", savings: "$180" },
    { service: "Filling (25% off)", regular: "$150", member: "$112", savings: "$38" },
    { total: true, regular: "$870", member: "$112", savings: "$758" }
  ];

  return (
    <div className="py-20 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-2 rounded-full mb-6">
            <FiDollarSign className="text-yellow-400 w-5 h-5" />
            <span className="text-sm font-semibold tracking-wider">SAVE WITH MEMBERSHIP</span>
          </div>

          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Compare & <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Save</span>
          </h2>

          <p className="text-gray-300 text-lg max-w-3xl mx-auto">
            Choose the perfect plan for your dental care needs. See how much you can save
            with our membership options compared to paying for services individually.
          </p>
        </div>

        {/* Comparison Table */}
        <div className="max-w-6xl mx-auto mb-16">
          <div className="overflow-x-auto rounded-2xl border border-gray-700">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-800">
                  <th className="py-6 px-8 text-left">
                    <div className="text-xl font-bold">Plan Features</div>
                  </th>
                  {plans.map((plan, index) => (
                    <th key={index} className="py-6 px-8 text-center">
                      <div className={`${plan.popular ? 'bg-gradient-to-r from-cyan-500 to-blue-500' : 'bg-gray-700'} rounded-xl p-4`}>
                        <div className="font-bold text-lg mb-2">{plan.name}</div>
                        <div className="text-sm text-gray-300 mb-3">{plan.description}</div>
                        <div className="flex items-baseline justify-center">
                          <span className="text-3xl font-bold">{plan.price}</span>
                          {plan.period && <span className="text-gray-300 ml-1">{plan.period}</span>}
                        </div>
                        {plan.popular && (
                          <div className="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-yellow-500/20 border border-yellow-500/30">
                            <span className="text-xs font-semibold text-yellow-300">BEST VALUE</span>
                          </div>
                        )}
                      </div>
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {plans[0].features.map((_, featureIndex) => (
                  <tr key={featureIndex} className="border-b border-gray-700">
                    <td className="py-4 px-8 text-gray-300">
                      {plans[0].features[featureIndex].text}
                    </td>
                    {plans.map((plan, planIndex) => (
                      <td key={planIndex} className="py-4 px-8 text-center">
                        {plan.features[featureIndex].included ? (
                          <div className="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500/20">
                            <FiCheck className="w-4 h-4 text-green-400" />
                          </div>
                        ) : (
                          <div className="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-700">
                            <FiX className="w-4 h-4 text-gray-400" />
                          </div>
                        )}
                      </td>
                    ))}
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr className="bg-gray-800">
                  <td className="py-6 px-8">
                    <div className="font-bold">Best For</div>
                  </td>
                  {plans.map((plan, index) => (
                    <td key={index} className="py-6 px-8 text-center">
                      <div className="space-y-1">
                        {plan.bestFor.map((item, idx) => (
                          <div key={idx} className="text-sm text-gray-300">{item}</div>
                        ))}
                      </div>
                    </td>
                  ))}
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {/* Savings Calculator */}
        <div className="max-w-4xl mx-auto mb-16">
          <div className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700 p-8">
            <h3 className="text-2xl font-bold mb-6 flex items-center gap-3">
              <FiActivity className="text-cyan-400 w-6 h-6" />
              Annual Savings Calculator
            </h3>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-gray-700">
                    <th className="py-4 px-6 text-left text-gray-300">Service</th>
                    <th className="py-4 px-6 text-left text-gray-300">Regular Price</th>
                    <th className="py-4 px-6 text-left text-gray-300">Member Price</th>
                    <th className="py-4 px-6 text-left text-gray-300">Your Savings</th>
                  </tr>
                </thead>
                <tbody>
                  {savings.map((item, index) => (
                    <tr key={index} className={`border-b border-gray-700/50 ${item.total ? 'bg-gray-800/50 font-bold' : ''}`}>
                      <td className="py-4 px-6">
                        {item.total ? "Total Annual Savings" : item.service}
                      </td>
                      <td className="py-4 px-6">
                        <span className={item.total ? "text-white" : "text-gray-300"}>{item.regular}</span>
                      </td>
                      <td className="py-4 px-6">
                        <span className={item.total ? "text-white" : "text-gray-300"}>{item.member}</span>
                      </td>
                      <td className="py-4 px-6">
                        <span className="text-green-400 font-semibold">{item.savings}</span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-6 text-center">
              <p className="text-gray-400">
                *Based on Premium Membership. Actual savings may vary based on individual needs.
              </p>
            </div>
          </div>
        </div>

        {/* CTA Section */}
        <div className="max-w-4xl mx-auto">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div className="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-2xl border border-cyan-500/20 p-8">
              <h3 className="text-2xl font-bold mb-4">Ready to Join?</h3>
              <p className="text-gray-300 mb-6">
                Start saving on your dental care today. No long-term contracts, cancel anytime.
              </p>
              <a
                href="#contact"
                className="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300"
              >
                <span>Sign Up Now</span>
                <FiArrowRight className="w-5 h-5 ml-2" />
              </a>
            </div>

            <div className="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl border border-gray-700 p-8">
              <h3 className="text-2xl font-bold mb-4">Have Questions?</h3>
              <p className="text-gray-300 mb-6">
                Our team is here to help you choose the right plan for your needs.
              </p>
              <div className="space-y-4">
                <a
                  href="tel:+1234567890"
                  className="flex items-center gap-3 text-gray-300 hover:text-white transition-colors"
                >
                  <span className="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                    <FiPhone className="w-5 h-5" />
                  </span>
                  <span>(123) 456-7890</span>
                </a>
                <a
                  href="mailto:info@dentalclinic.com"
                  className="flex items-center gap-3 text-gray-300 hover:text-white transition-colors"
                >
                  <span className="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                    <FiMail className="w-5 h-5" />
                  </span>
                  <span>info@dentalclinic.com</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PricingSection_3;
