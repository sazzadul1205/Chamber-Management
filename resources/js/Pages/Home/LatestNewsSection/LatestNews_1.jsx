import React from "react";
import {
  FaUserMd,
  FaArrowRight,
  FaRegCalendarAlt
} from "react-icons/fa";

const LatestNews_1 = () => {

  const news = [
    {
      id: 1,
      title: "The Link Between Oral Health and Heart Disease",
      excerpt:
        "New research reveals strong connections between gum disease and cardiovascular health.",
      category: "Dental Health",
      date: "March 15, 2024",
      author: "Dr. Sarah Johnson",
      readTime: "5 min read",
      featured: true,
      color: "from-blue-500 to-cyan-400"
    },
    {
      id: 2,
      title: "Invisalign vs. Traditional Braces",
      excerpt:
        "Compare the pros and cons of clear aligners and metal braces.",
      category: "Orthodontics",
      date: "March 10, 2024",
      author: "Dr. Michael Chen",
      readTime: "7 min read",
      featured: false,
      color: "from-emerald-500 to-teal-400"
    }
  ];

  // Separate featured + regular once
  const featuredArticle = news.find(n => n.featured);
  const regularArticles = news.filter(n => !n.featured);

  return (
    <section className="py-20 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl lg:text-5xl font-bold text-gray-800">
            Dental{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">
              News & Articles
            </span>
          </h2>
          <p className="text-gray-600 text-lg mt-4 max-w-2xl mx-auto">
            Stay updated with the latest dental insights and clinic news.
          </p>
        </div>

        {/* Featured */}
        {featuredArticle && (
          <div className="max-w-6xl mx-auto mb-16 bg-white rounded-3xl shadow-xl overflow-hidden">
            <div className="grid lg:grid-cols-2">

              <div className={`bg-gradient-to-br ${featuredArticle.color} flex items-center justify-center p-12`}>
                <FaUserMd className="text-white text-8xl opacity-80" />
              </div>

              <div className="p-10">
                <span className="text-sm font-semibold text-blue-600">
                  {featuredArticle.category}
                </span>

                <h3 className="text-3xl font-bold text-gray-800 mt-3">
                  {featuredArticle.title}
                </h3>

                <p className="text-gray-600 mt-4">
                  {featuredArticle.excerpt}
                </p>

                <div className="flex items-center gap-6 mt-6 text-sm text-gray-500">
                  <div className="flex items-center gap-2">
                    <FaRegCalendarAlt />
                    {featuredArticle.date}
                  </div>
                  <span>{featuredArticle.readTime}</span>
                </div>

                <button className="mt-8 inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition">
                  Read Full Article
                  <FaArrowRight className="ml-2" />
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {regularArticles.map(article => (
            <div
              key={article.id}
              className="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-6"
            >
              <span className="text-xs font-semibold text-blue-600">
                {article.category}
              </span>

              <h3 className="text-xl font-bold text-gray-800 mt-3">
                {article.title}
              </h3>

              <p className="text-gray-600 mt-3 text-sm">
                {article.excerpt}
              </p>

              <div className="flex items-center justify-between mt-6 text-sm text-gray-500">
                <div className="flex items-center gap-2">
                  <FaUserMd />
                  {article.author}
                </div>
                <span>{article.readTime}</span>
              </div>

              <button className="mt-4 inline-flex items-center text-blue-600 font-semibold text-sm hover:text-blue-700">
                Read More
                <FaArrowRight className="ml-1" />
              </button>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};

export default LatestNews_1;
