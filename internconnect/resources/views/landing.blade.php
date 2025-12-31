<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ROC.ph — Digital Marketing</title>
    @vite(['resources/css/landing.css','resources/js/app.js'])
</head>
<body class="antialiased text-gray-700">
  <header class="nav-bg text-white shadow-md">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="text-2xl font-semibold text-yellow-400">ROC.ph</div>
      <nav class="space-x-6">
        <a href="#services" class="hover:underline">Services</a>
        <a href="#about" class="hover:underline">About</a>
        <a href="#testimonials" class="hover:underline">Testimonials</a>
        <a href="#contact" class="hover:underline">Contact</a>
      </nav>
      <a href="/applicant/login" class="btn-yellow">Get Started</a>
    </div>
  </header>

  <main>
    <section class="hero-gradient py-20">
      <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div>
          <h1 class="text-white text-3xl md:text-4xl font-semibold mb-6">Elevate Your Brand with Data-Driven Marketing</h1>
          <p class="text-white/90 mb-6">ROC.ph delivers cutting-edge digital marketing solutions that drive real results. From SEO to social media, we help businesses grow their online presence and reach their target audience.</p>
          <div class="flex gap-4">
            <a href="/applicant/login" class="btn-yellow inline-flex items-center">Get Started <span class="ml-3"><i class="bi bi-arrow-right"></i></span></a>
            <a href="#services" class="border border-white/60 text-white rounded-xl px-6 py-3">Learn More</a>
          </div>
        </div>
        <div class="flex justify-center md:justify-end">
          <div class="w-80 h-52 bg-white rounded-2xl overflow-hidden card-shadow">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=60&auto=format&fit=crop" alt="team" class="w-full h-full object-cover">
          </div>
        </div>
      </div>
    </section>

    <section id="services" class="py-16 bg-white">
      <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-xl text-slate-700 mb-2">Our Services</h2>
        <p class="text-slate-400 mb-8">Comprehensive marketing solutions tailored to your business needs</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white border rounded-xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-green-50 rounded-md mb-4 flex items-center justify-center"><i class="bi bi-graph-up-arrow text-2xl text-green-600"></i></div>
            <h3 class="font-medium mb-2">SEO Optimization</h3>
            <p class="text-slate-400 text-sm">Boost your search rankings and drive organic traffic with our proven SEO strategies.</p>
          </div>
          <div class="bg-white border rounded-xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-blue-50 rounded-md mb-4 flex items-center justify-center"><i class="bi bi-people text-2xl text-blue-600"></i></div>
            <h3 class="font-medium mb-2">Social Media Marketing</h3>
            <p class="text-slate-400 text-sm">Engage your audience across platforms with compelling content and campaigns.</p>
          </div>
          <div class="bg-white border rounded-xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-yellow-50 rounded-md mb-4 flex items-center justify-center"><i class="bi bi-megaphone text-2xl text-yellow-600"></i></div>
            <h3 class="font-medium mb-2">Content Marketing</h3>
            <p class="text-slate-400 text-sm">Create content that attracts, informs, and converts your target customers.</p>
          </div>
          <div class="bg-white border rounded-xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-red-50 rounded-md mb-4 flex items-center justify-center"><i class="bi bi-bar-chart text-2xl text-red-600"></i></div>
            <h3 class="font-medium mb-2">PPC Advertising</h3>
            <p class="text-slate-400 text-sm">Drive immediate traffic with highly-targeted paid campaigns.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="testimonials" class="py-16 bg-slate-50">
      <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-lg text-slate-700 mb-2">What Our Clients Say</h2>
        <p class="text-slate-400 mb-8">Don't just take our word for it - hear from our satisfied clients</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-xl p-6 shadow-sm text-left">
            <div class="text-yellow-400 mb-4">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p class="text-slate-600 mb-4">ROC.ph transformed our digital presence completely. Our website traffic increased by 300% in just 6 months!</p>
            <div class="text-sm text-slate-500 font-medium">Maria Santos<br><span class="text-slate-400">TechStart Philippines</span></div>
          </div>
          <div class="bg-white rounded-xl p-6 shadow-sm text-left">
            <div class="text-yellow-400 mb-4">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p class="text-slate-600 mb-4">The team at ROC.ph is incredibly professional and results-driven. Our social media engagement has never been better.</p>
            <div class="text-sm text-slate-500 font-medium">John Cruz<br><span class="text-slate-400">EcoRetail Solutions</span></div>
          </div>
          <div class="bg-white rounded-xl p-6 shadow-sm text-left">
            <div class="text-yellow-400 mb-4">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p class="text-slate-600 mb-4">Outstanding service! Their strategic approach helped us generate quality leads consistently.</p>
            <div class="text-sm text-slate-500 font-medium">Anna Rodriguez<br><span class="text-slate-400">Global Consulting Group</span></div>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="py-16 hero-gradient">
      <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div class="text-white">
          <h3 class="text-2xl font-semibold mb-4">Ready to Grow Your Business?</h3>
          <p class="mb-6 text-white/90">Let's discuss how we can help you achieve your marketing goals. Get a free consultation with our experts today.</p>

          <div class="space-y-4">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-yellow-400 rounded-md flex items-center justify-center"><i class="bi bi-envelope text-2xl text-gray-900"></i></div>
              <div>hello@roc.ph</div>
            </div>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-yellow-400 rounded-md flex items-center justify-center"><i class="bi bi-telephone text-2xl text-gray-900"></i></div>
              <div>+63 (2) 1234-5678</div>
            </div>
            <a href="#" class="mt-4 inline-block btn-yellow">Request a Consultation</a>
          </div>
        </div>

        <div class="flex justify-center md:justify-end">
          <div class="w-80 h-52 bg-white rounded-2xl overflow-hidden card-shadow">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=60&auto=format&fit=crop" alt="graph" class="w-full h-full object-cover">
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="bg-slate-800 text-slate-300 py-10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <div class="text-yellow-400 font-semibold mb-2">ROC.ph</div>
        <p class="text-sm">Your trusted partner in digital marketing excellence.</p>
      </div>
      <div>
        <h4 class="font-medium mb-2 text-white">Services</h4>
        <ul class="text-sm text-slate-400 space-y-1">
          <li>SEO Optimization</li>
          <li>Social Media</li>
          <li>Content Marketing</li>
          <li>PPC Advertising</li>
        </ul>
      </div>
      <div>
        <h4 class="font-medium mb-2 text-white">Company</h4>
        <ul class="text-sm text-slate-400 space-y-1">
          <li>About Us</li>
          <li>Careers</li>
          <li>Blog</li>
          <li>Contact</li>
        </ul>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 mt-8 border-t border-slate-700 pt-6 text-center text-slate-500 text-sm">© 2025 ROC.ph. All rights reserved.</div>
  </footer>
</body>
</html>
