import React from "react";
import Slider from "react-slick";
import { FaRobot, FaTooth, FaBaby, FaSmile, FaBed, FaEnvelope, FaArrowRight, FaFacebookF, FaTwitter, FaInstagram, FaLinkedinIn } from "react-icons/fa";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const LatestNews_3 = () => {
  const heroPost = {
    title: "The Future of Dentistry: AI, Robotics, and Personalized Care",
    excerpt: "How AI and robotics are revolutionizing diagnosis, treatment planning, and patient care.",
    icon: <FaRobot className="text-9xl" />,
    category: "Future of Dentistry",
    date: "March 15, 2024",
    author: "Dr. Sarah Johnson",
    readTime: "10 min read",
    gradient: "from-cyan-500 to-blue-600"
  };

  const articles = [
    {
      id: 1,
      title: "Why Your Gum Health Matters More Than You Think",
      excerpt: "New studies link periodontal disease to diabetes, heart disease, and Alzheimer's.",
      icon: <FaTooth className="text-2xl" />,
      category: "Gum Health",
      date: "March 12, 2024",
      author: "Dr. James Wilson",
      readTime: "6 min read",
      gradient: "from-emerald-500 to-teal-500"
    },
    {
      id: 2,
      title: "Invisalign First: Early Orthodontic Treatment for Children",
      excerpt: "Why more parents are choosing clear aligners for their children's orthodontic needs.",
      icon: <FaBaby className="text-2xl" />,
      category: "Pediatric",
      date: "March 10, 2024",
      author: "Dr. Emily Rodriguez",
      readTime: "5 min read",
      gradient: "from-purple-500 to-pink-500"
    },
    {
      id: 3,
      title: "Porcelain Veneers vs. Composite Bonding: Which Lasts Longer?",
      excerpt: "Comparing durability, cost, and aesthetics of two popular smile makeover options.",
      icon: <FaSmile className="text-2xl" />,
      category: "Cosmetic",
      date: "March 8, 2024",
      author: "Dr. Michael Chen",
      readTime: "7 min read",
      gradient: "from-amber-500 to-orange-500"
    },
    {
      id: 4,
      title: "Sleep Apnea and Dentistry: How Oral Appliances Can Help",
      excerpt: "Non-invasive treatment options for obstructive sleep apnea from your dentist.",
      icon: <FaBed className="text-2xl" />,
      category: "Sleep Health",
      date: "March 5, 2024",
      author: "Dr. Robert Kim",
      readTime: "8 min read",
      gradient: "from-indigo-500 to-blue-500"
    }
  ];

  const trending = [
    { title: "Oil Pulling: Fact or Fiction?", views: "4.2k", trend: "+12%" },
    { title: "Cost of Dental Implants in 2024", views: "3.8k", trend: "+8%" },
    { title: "Wisdom Teeth: To Remove or Not?", views: "3.5k", trend: "+15%" },
    { title: "Best Electric Toothbrushes", views: "3.1k", trend: "+10%" },
    { title: "Teeth Whitening Side Effects", views: "2.9k", trend: "+7%" }
  ];

  const settings = {
    dots: true,
    infinite: true,
    speed: 800,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: true,
    arrows: false,
    responsive: [
      { breakpoint: 1024, settings: { slidesToShow: 2 } },
      { breakpoint: 640, settings: { slidesToShow: 1 } }
    ],
    appendDots: dots => (
      <div className="absolute -bottom-8 left-1/2 transform -translate-x-1/2">
        <ul className="flex space-x-2"> {dots} </ul>
      </div>
    ),
    customPaging: i => <button className="w-2 h-2 bg-gray-600 rounded-full hover:bg-cyan-400 transition-colors"></button>
  };

  const socialIcons = [<FaFacebookF />, <FaTwitter />, <FaInstagram />, <FaLinkedinIn />];

  return (
    <div className="py-20 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-2 rounded-full mb-6">
            <span className="text-cyan-400">📰</span>
            <span className="text-sm font-semibold tracking-wider">DENTAL INSIGHTS</span>
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold mb-6">
            Latest from <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">The Journal</span>
          </h2>
          <p className="text-gray-300 text-lg max-w-2xl mx-auto">
            Expert insights, clinical updates, and oral health education from our dental specialists.
          </p>
        </div>

        {/* Hero Post */}
        <div className="max-w-6xl mx-auto mb-16">
          <div className={`bg-gradient-to-r ${heroPost.gradient} rounded-3xl overflow-hidden shadow-2xl`}>
            <div className="grid grid-cols-1 lg:grid-cols-2">
              <div className="p-8 lg:p-12 flex flex-col justify-center">
                <div className="flex items-center gap-3 mb-4">
                  <span className="bg-white/20 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full">
                    FEATURED STORY
                  </span>
                  <span className="text-white/80 text-sm">{heroPost.date}</span>
                </div>
                <h3 className="text-3xl lg:text-4xl font-bold text-white mb-4">{heroPost.title}</h3>
                <p className="text-white/90 text-lg mb-6">{heroPost.excerpt}</p>
                <div className="flex items-center gap-4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold">
                      {heroPost.author.charAt(0)}
                    </div>
                    <div>
                      <p className="font-semibold text-white">{heroPost.author}</p>
                      <p className="text-white/80 text-sm">{heroPost.readTime}</p>
                    </div>
                  </div>
                  <button className="bg-white text-gray-900 hover:bg-gray-100 font-semibold px-6 py-2 rounded-lg transition-colors">
                    Read More
                  </button>
                </div>
              </div>
              <div className="flex items-center justify-center p-12">
                {heroPost.icon}
              </div>
            </div>
          </div>
        </div>

        {/* Articles Slider */}
        <div className="max-w-7xl mx-auto mb-16">
          <div className="flex items-center justify-between mb-8">
            <h3 className="text-2xl font-bold flex items-center gap-3">Recent Articles</h3>
            <a href="#archive" className="text-cyan-400 hover:text-cyan-300 font-semibold text-sm">View Archive →</a>
          </div>
          <Slider {...settings}>
            {articles.map(article => (
              <div key={article.id} className="px-3">
                <div className={`bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700 p-6 hover:border-cyan-500/30 transition-all h-full`}>
                  <div className="flex items-start justify-between mb-4">
                    <div className={`w-12 h-12 rounded-xl ${article.gradient} flex items-center justify-center`}>
                      {article.icon}
                    </div>
                    <span className="text-xs text-gray-400">{article.date}</span>
                  </div>
                  <span className={`inline-block text-xs font-semibold px-2 py-1 rounded-full bg-gradient-to-r ${article.gradient} bg-opacity-20 text-cyan-300 mb-3`}>
                    {article.category}
                  </span>
                  <h4 className="text-xl font-bold text-white mb-3 line-clamp-2 hover:text-cyan-400 transition-colors">{article.title}</h4>
                  <p className="text-gray-400 text-sm mb-4 line-clamp-3">{article.excerpt}</p>
                  <div className="flex items-center justify-between pt-4 border-t border-gray-700">
                    <div className="flex items-center gap-2">
                      <div className="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-xs">{article.author.charAt(0)}</div>
                      <span className="text-xs text-gray-400">{article.author.split(' ')[1]}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-xs text-gray-400">{article.readTime}</span>
                      <FaArrowRight className="text-cyan-400" />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* Trending & Newsletter */}
        <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 bg-gray-800 rounded-2xl border border-gray-700 p-8">
            <h3 className="text-xl font-bold mb-6 flex items-center gap-2">Trending Now</h3>
            <div className="space-y-4">
              {trending.map((item, i) => (
                <div key={i} className="flex items-center justify-between group">
                  <div className="flex items-center gap-4">
                    <span className="text-2xl text-gray-600 group-hover:text-cyan-400 transition-colors">#{i + 1}</span>
                    <div>
                      <h4 className="font-semibold text-white group-hover:text-cyan-400 transition-colors">{item.title}</h4>
                      <p className="text-sm text-gray-400">{item.views} reads</p>
                    </div>
                  </div>
                  <span className="text-green-400 text-sm">{item.trend}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="space-y-6">
            <div className="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-2xl border border-cyan-500/20 p-6">
              <h3 className="text-xl font-bold mb-4 flex items-center gap-2"><FaEnvelope className="text-cyan-400" /> Newsletter</h3>
              <input type="email" placeholder="Your email" className="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm mb-3 focus:ring-2 focus:ring-cyan-500" />
              <button className="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-3 rounded-lg transition-all">
                Subscribe
              </button>
            </div>

            <div className="bg-gray-800 rounded-2xl border border-gray-700 p-6">
              <h3 className="text-lg font-bold mb-4">Follow Us</h3>
              <div className="flex gap-4">
                {socialIcons.map((icon, i) => (
                  <button key={i} className="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-xl hover:bg-cyan-500/20 hover:border-cyan-500 border border-transparent transition-all">
                    {icon}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* View All */}
        <div className="text-center mt-16">
          <a href="#blog" className="inline-flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 hover:border-cyan-500/30 px-8 py-4 rounded-xl transition-all duration-300">
            <span className="font-semibold text-white">Browse All Articles</span>
            <FaArrowRight className="text-cyan-400" />
          </a>
        </div>
      </div>
    </div>
  );
};

export default LatestNews_3;
