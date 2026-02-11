import React from "react";
import {
  FaStar,
  FaEye,
  FaCommentDots,
  FaFolderOpen,
  FaClock,
  FaEnvelope,
  FaArrowRight,
  FaTooth,
  FaUserMd
} from "react-icons/fa";

const LatestNews_2 = () => {

  const featuredPosts = [
    {
      id: 1,
      title: "Revolutionary Laser Dentistry: Pain-Free Treatments Are Here",
      excerpt:
        "Our clinic introduces state-of-the-art dental lasers for minimally invasive procedures.",
      category: "Technology",
      date: "March 12, 2024",
      author: "Dr. Robert Kim",
      readTime: "6 min read",
      views: "2.5k",
      comments: "18",
      gradient: "from-amber-500 to-orange-500"
    }
  ];

  const categories = [
    { id: 1, name: "All", count: 24 },
    { id: 2, name: "Dental Health", count: 8 },
    { id: 3, name: "Technology", count: 6 }
  ];

  const timelineNews = [
    {
      id: 1,
      title: "Clinic Expansion: New Pediatric Wing Opening",
      date: "March 14, 2024",
      category: "Announcement"
    }
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <span className="text-blue-600 font-semibold text-sm tracking-wider">
            OUR BLOG
          </span>
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800 mt-4">
            Latest <span className="text-blue-600">News & Updates</span>
          </h2>
          <p className="text-gray-600 mt-4 max-w-2xl mx-auto">
            Stay up to date with dental innovations and clinic news.
          </p>
        </div>

        <div className="grid lg:grid-cols-3 gap-8 max-w-7xl mx-auto">

          {/* Main Column */}
          <div className="lg:col-span-2 space-y-8">

            {/* Featured */}
            <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
              <div className="p-6 bg-gradient-to-r from-blue-600 to-cyan-500 text-white flex items-center gap-3">
                <FaStar />
                <h3 className="text-xl font-bold">Featured Articles</h3>
              </div>

              <div className="p-8 space-y-6">
                {featuredPosts.map(post => (
                  <div key={post.id} className="flex gap-6 group">

                    <div className={`w-24 h-24 rounded-2xl bg-gradient-to-br ${post.gradient} flex items-center justify-center`}>
                      <FaUserMd className="text-white text-3xl" />
                    </div>

                    <div className="flex-1">
                      <div className="flex items-center gap-3 text-sm text-gray-500 mb-2">
                        <span className="font-semibold text-blue-600">
                          {post.category}
                        </span>
                        <span>{post.date}</span>
                      </div>

                      <h4 className="text-xl font-bold text-gray-800 group-hover:text-blue-600 transition">
                        {post.title}
                      </h4>

                      <p className="text-gray-600 mt-2 line-clamp-2">
                        {post.excerpt}
                      </p>

                      <div className="flex items-center gap-4 text-sm text-gray-500 mt-4">
                        <span>{post.readTime}</span>
                        <span className="flex items-center gap-1">
                          <FaEye /> {post.views}
                        </span>
                        <span className="flex items-center gap-1">
                          <FaCommentDots /> {post.comments}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              <div className="p-6 border-t">
                <button className="text-blue-600 font-semibold inline-flex items-center hover:text-blue-700">
                  View All Featured
                  <FaArrowRight className="ml-2" />
                </button>
              </div>
            </div>

            {/* Example Regular Card */}
            <div className="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                  <FaTooth className="text-blue-600" />
                </div>
                <div>
                  <span className="text-xs font-semibold text-blue-600">
                    Dental Tips
                  </span>
                  <h4 className="font-bold text-gray-800 mt-2">
                    10 Foods That Strengthen Your Teeth Naturally
                  </h4>
                  <p className="text-gray-500 text-sm mt-1">
                    March 18, 2024 • 4 min read
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Sidebar */}
          <div className="space-y-8">

            {/* Categories */}
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-xl font-bold mb-6 flex items-center gap-2">
                <FaFolderOpen className="text-blue-600" />
                Categories
              </h3>

              {categories.map(cat => (
                <button
                  key={cat.id}
                  className="w-full flex justify-between p-3 rounded-lg hover:bg-gray-50 transition"
                >
                  <span>{cat.name}</span>
                  <span className="text-xs bg-gray-100 px-2 py-1 rounded-full">
                    {cat.count}
                  </span>
                </button>
              ))}
            </div>

            {/* Timeline */}
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-xl font-bold mb-6 flex items-center gap-2">
                <FaClock className="text-blue-600" />
                Recent Updates
              </h3>

              {timelineNews.map(item => (
                <div key={item.id} className="mb-4">
                  <span className="text-xs text-blue-600 font-semibold">
                    {item.category}
                  </span>
                  <h4 className="text-sm font-bold mt-1">
                    {item.title}
                  </h4>
                  <p className="text-xs text-gray-500 mt-1">
                    {item.date}
                  </p>
                </div>
              ))}
            </div>

            {/* Newsletter */}
            <div className="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-2xl p-6 text-white">
              <h3 className="text-xl font-bold mb-3 flex items-center gap-2">
                <FaEnvelope />
                Newsletter
              </h3>

              <input
                type="email"
                placeholder="Your email"
                className="w-full px-4 py-3 rounded-xl text-gray-800 text-sm mb-3 focus:outline-none"
              />

              <button className="w-full bg-white text-blue-600 font-semibold py-3 rounded-xl hover:bg-blue-50 transition">
                Subscribe
              </button>
            </div>

          </div>
        </div>
      </div>
    </section>
  );
};

export default LatestNews_2;
  