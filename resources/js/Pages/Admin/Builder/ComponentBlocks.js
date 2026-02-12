export const componentBlocks = [
    // ============ SECTIONS ============
    {
        id: "hero-section-1",
        label: "Hero Section",
        category: "Sections",
        content: `<section class="bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 text-white py-24 px-4 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="relative max-w-4xl mx-auto">
                  <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">Create Something Amazing</h1>
                  <p class="text-xl md:text-2xl mb-8 opacity-90">Build beautiful websites with our drag & drop builder</p>
                  <div class="flex gap-4 justify-center">
                    <button class="px-8 py-4 bg-white text-purple-600 font-bold rounded-full hover:shadow-2xl transform hover:scale-105 transition-all duration-300">Get Started</button>
                    <button class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-purple-600 transition-all duration-300">Learn More</button>
                  </div>
                </div>
              </section>`,
    },
    {
        id: "hero-section-2",
        label: "Hero With Image",
        category: "Sections",
        content: `<section class="bg-white py-16 px-4">
                <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                  <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Your Product, <span class="text-purple-600">Reimagined</span></h1>
                    <p class="text-lg text-gray-600 mb-8">Experience the next generation of digital solutions designed to transform your workflow.</p>
                    <div class="flex gap-4">
                      <button class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-lg">Start Free Trial</button>
                      <button class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Watch Demo</button>
                    </div>
                    <p class="text-sm text-gray-500 mt-6">Trusted by 10,000+ companies worldwide</p>
                  </div>
                  <div class="bg-gradient-to-br from-purple-100 to-pink-100 p-8 rounded-2xl">
                    <div class="bg-white shadow-2xl rounded-xl p-6">
                      <div class="w-full h-48 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg mb-4"></div>
                      <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                      <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                  </div>
                </div>
              </section>`,
    },
    {
        id: "hero-section-3",
        label: "Minimal Hero",
        category: "Sections",
        content: `<section class="py-20 px-4 text-center bg-gray-50">
                <span class="text-sm uppercase tracking-wider text-purple-600 font-semibold">Welcome to our platform</span>
                <h2 class="text-4xl md:text-5xl font-light text-gray-900 mt-4 mb-6">Simple, elegant, <span class="font-bold">powerful</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-10">The minimalist approach to building professional websites that convert visitors into customers.</p>
                <button class="px-8 py-3 bg-gray-900 text-white rounded-full hover:bg-gray-800 transition shadow-md">Explore Features →</button>
              </section>`,
    },

    // ============ FEATURE SECTIONS ============
    {
        id: "features-grid-1",
        label: "Features Grid",
        category: "Features",
        content: `<section class="py-20 px-4 bg-white">
                <div class="max-w-7xl mx-auto">
                  <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Everything you need</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Powerful features to help you build better websites faster</p>
                  </div>
                  <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-xl transition">
                      <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                      </div>
                      <h3 class="text-xl font-semibold text-gray-900 mb-2">Lightning Fast</h3>
                      <p class="text-gray-600">Optimized performance with instant page loads and smooth animations.</p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-xl transition">
                      <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                      </div>
                      <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure by Default</h3>
                      <p class="text-gray-600">Enterprise-grade security with automatic backups and SSL encryption.</p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-xl transition">
                      <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                      </div>
                      <h3 class="text-xl font-semibold text-gray-900 mb-2">Modular Design</h3>
                      <p class="text-gray-600">Build complex layouts using simple, reusable components.</p>
                    </div>
                  </div>
                </div>
              </section>`,
    },
    {
        id: "features-list",
        label: "Feature List",
        category: "Features",
        content: `<section class="py-16 px-4 bg-gradient-to-br from-gray-50 to-white">
                <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                  <div>
                    <span class="text-sm uppercase tracking-wider text-purple-600 font-semibold">Why choose us</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-6">The smart choice for modern businesses</h2>
                    <p class="text-gray-600 mb-8">Join thousands of companies that trust our platform for their digital needs.</p>
                    <div class="space-y-4">
                      <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                          <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Unlimited Projects</h3>
                          <p class="text-sm text-gray-600">Work on as many projects as you need</p>
                        </div>
                      </div>
                      <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                          <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Team Collaboration</h3>
                          <p class="text-sm text-gray-600">Invite team members and work together</p>
                        </div>
                      </div>
                      <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                          <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Priority Support</h3>
                          <p class="text-sm text-gray-600">Get help when you need it, 24/7</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="bg-white p-8 rounded-2xl shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Start your free trial</h3>
                    <form>
                      <input type="email" placeholder="Work email" class="w-full px-4 py-3 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-purple-600" />
                      <button class="w-full px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">Get started →</button>
                      <p class="text-xs text-gray-500 text-center mt-4">No credit card required. Cancel anytime.</p>
                    </form>
                  </div>
                </div>
              </section>`,
    },

    // ============ CARDS ============
    {
        id: "pricing-card",
        label: "Pricing Card",
        category: "Cards",
        content: `<div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm mx-auto border border-gray-100 hover:shadow-2xl transition">
                <div class="flex justify-between items-start mb-6">
                  <div>
                    <span class="text-sm uppercase tracking-wider text-purple-600 font-semibold">Pro Plan</span>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">$49<span class="text-lg font-normal text-gray-600">/mo</span></h3>
                  </div>
                  <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs font-semibold rounded-full">Popular</span>
                </div>
                <p class="text-gray-600 mb-6">For growing businesses that need more power and flexibility.</p>
                <hr class="mb-6">
                <ul class="space-y-4 mb-8">
                  <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Up to 50 team members</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>100GB cloud storage</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Advanced analytics</span>
                  </li>
                </ul>
                <button class="w-full px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition font-semibold shadow-lg">Choose Plan</button>
              </div>`,
    },
    {
        id: "team-card",
        label: "Team Member",
        category: "Cards",
        content: `<div class="bg-white rounded-xl p-6 text-center group hover:shadow-xl transition">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full mx-auto mb-4 flex items-center justify-center">
                  <span class="text-white text-3xl font-bold">JD</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">John Doe</h3>
                <p class="text-purple-600 mb-3">CEO & Founder</p>
                <p class="text-gray-600 text-sm mb-4">10+ years of experience in web development</p>
                <div class="flex justify-center gap-3">
                  <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-blue-100 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.99C18.343 21.128 22 16.991 22 12z"/></svg>
                  </a>
                  <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-blue-100 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.937 4.937 0 004.604 3.417 9.868 9.868 0 01-6.102 2.104c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0021.942-12.3A9.885 9.885 0 0024 4.59z"/></svg>
                  </a>
                </div>
              </div>`,
    },

    // ============ FORMS ============
    {
        id: "contact-form",
        label: "Contact Form",
        category: "Forms",
        content: `<div class="bg-white p-8 rounded-2xl shadow-lg max-w-md mx-auto">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Get in touch</h3>
                <p class="text-gray-600 mb-6">We'll get back to you within 24 hours.</p>
                <form>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" placeholder="Your name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent" />
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" placeholder="you@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent" />
                  </div>
                  <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea rows="4" placeholder="How can we help?" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"></textarea>
                  </div>
                  <button class="w-full px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">Send Message</button>
                </form>
              </div>`,
    },
    {
        id: "newsletter",
        label: "Newsletter",
        category: "Forms",
        content: `<div class="bg-gradient-to-r from-purple-600 to-pink-600 p-8 rounded-2xl text-white max-w-md mx-auto">
                <h3 class="text-2xl font-bold mb-2">Stay updated</h3>
                <p class="mb-6 opacity-90">Subscribe to our newsletter for the latest updates.</p>
                <form class="flex flex-col sm:flex-row gap-3">
                  <input type="email" placeholder="Your email address" class="flex-1 px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-300" />
                  <button class="px-6 py-3 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition font-semibold shadow-lg">Subscribe</button>
                </form>
                <p class="text-xs mt-4 opacity-75">No spam. Unsubscribe anytime.</p>
              </div>`,
    },

    // ============ NAVIGATION ============
    {
        id: "navbar-simple",
        label: "Simple Navbar",
        category: "Navigation",
        content: `<nav class="bg-white shadow-sm px-4 py-3">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                  <div class="text-2xl font-bold text-purple-600">Brand</div>
                  <div class="hidden md:flex space-x-8">
                    <a href="#" class="text-gray-700 hover:text-purple-600">Home</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600">Features</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600">Pricing</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600">Contact</a>
                  </div>
                  <button class="md:hidden text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                  </button>
                </div>
              </nav>`,
    },

    // ============ FOOTERS ============
    {
        id: "footer-simple",
        label: "Simple Footer",
        category: "Footers",
        content: `<footer class="bg-gray-900 text-white py-12 px-4">
                <div class="max-w-7xl mx-auto">
                  <div class="grid md:grid-cols-4 gap-8">
                    <div>
                      <h3 class="text-xl font-bold mb-4">Company</h3>
                      <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Press</a></li>
                      </ul>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold mb-4">Product</h3>
                      <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                      </ul>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold mb-4">Legal</h3>
                      <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Terms</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Cookies</a></li>
                      </ul>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold mb-4">Follow us</h3>
                      <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">Twitter</a>
                        <a href="#" class="text-gray-400 hover:text-white">LinkedIn</a>
                        <a href="#" class="text-gray-400 hover:text-white">GitHub</a>
                      </div>
                    </div>
                  </div>
                  <hr class="border-gray-800 my-8">
                  <p class="text-center text-gray-400 text-sm">© 2024 Your Company. All rights reserved.</p>
                </div>
              </footer>`,
    },

    // ============ TESTIMONIALS ============
    {
        id: "testimonial-card",
        label: "Testimonial",
        category: "Testimonials",
        content: `<div class="bg-white p-8 rounded-2xl shadow-lg max-w-md mx-auto">
                <div class="flex items-center mb-6">
                  <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.983 3v7.391C9.983 16.095 6.252 19.961 1 21l-.995-2.151C3.437 17.148 4.966 14.928 4.966 12.79V3H9.983zm12.017 0v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151C16.437 17.148 17.966 14.928 17.966 12.79V3H22z"/>
                  </svg>
                </div>
                <p class="text-gray-700 mb-6 italic">"This platform has completely transformed how we build websites. The drag and drop interface is intuitive and the components are beautiful."</p>
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full"></div>
                  <div>
                    <h4 class="font-semibold text-gray-900">Sarah Johnson</h4>
                    <p class="text-sm text-gray-600">CEO, TechStart</p>
                  </div>
                </div>
              </div>`,
    },

    // ============ BLOG ============
    {
        id: "blog-post",
        label: "Blog Card",
        category: "Blog",
        content: `<article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400"></div>
                <div class="p-6">
                  <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                    <span>Dec 12, 2024</span>
                    <span>•</span>
                    <span>5 min read</span>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-purple-600">
                    <a href="#">Getting Started with Modern Web Design</a>
                  </h3>
                  <p class="text-gray-600 mb-4">Learn the fundamentals of creating beautiful, responsive websites with our component-based approach.</p>
                  <a href="#" class="text-purple-600 font-semibold hover:text-purple-700 flex items-center gap-1">
                    Read more 
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </a>
                </div>
              </article>`,
    },

    // ============ STATS ============
    {
        id: "stats-section",
        label: "Stats Section",
        category: "Statistics",
        content: `<section class="bg-white py-16 px-4">
                <div class="max-w-7xl mx-auto">
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                      <div class="text-4xl font-bold text-purple-600 mb-2">10K+</div>
                      <div class="text-gray-600">Active Users</div>
                    </div>
                    <div>
                      <div class="text-4xl font-bold text-pink-600 mb-2">50K+</div>
                      <div class="text-gray-600">Projects</div>
                    </div>
                    <div>
                      <div class="text-4xl font-bold text-blue-600 mb-2">100+</div>
                      <div class="text-gray-600">Countries</div>
                    </div>
                    <div>
                      <div class="text-4xl font-bold text-green-600 mb-2">24/7</div>
                      <div class="text-gray-600">Support</div>
                    </div>
                  </div>
                </div>
              </section>`,
    },
];
