<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ROC.ph — Digital Marketing</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📈</text></svg>">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
  <!-- Navbar -->
  <header class="nav-bg py-3 shadow-sm">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between">
        <div class="fs-3 fw-bold text-warning">ROC.ph</div>
        <nav class="d-none d-md-flex gap-4">
          <a href="#services" class="text-white text-decoration-none hover-underline">Services</a>
          <a href="#about" class="text-white text-decoration-none hover-underline">About</a>
          <a href="#testimonials" class="text-white text-decoration-none hover-underline">Testimonials</a>
          <a href="#contact" class="text-white text-decoration-none hover-underline">Contact</a>
        </nav>
        <a href="/applicant/login" class="btn btn-warning fw-bold px-4 rounded-pill">Get Started</a>
      </div>
    </div>
  </header>

  <main>
    <!-- Hero Section -->
    <section class="hero-gradient py-5 py-md-5">
      <div class="container py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-6 text-white">
            <h1 class="display-5 fw-bold mb-4">Elevate Your Brand with Data-Driven Marketing</h1>
            <p class="lead opacity-90 mb-5">ROC.ph delivers cutting-edge digital marketing solutions that drive real results. From SEO to social media, we help businesses grow their online presence and reach their target audience.</p>
            <div class="d-flex gap-3">
              <a href="/applicant/login" class="btn btn-warning btn-lg px-4 rounded-pill fw-bold">Get Started <i class="bi bi-arrow-right ms-2"></i></a>
              <a href="#services" class="btn btn-outline-light btn-lg px-4 rounded-pill">Learn More</a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card overflow-hidden rounded-4 shadow-lg border-0">
              <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=60&auto=format&fit=crop" alt="team" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-white">
      <div class="container py-5">
        <div class="text-center mb-5">
          <h2 class="fw-bold text-dark mb-2">Our Services</h2>
          <p class="text-muted mx-auto" style="max-width: 600px;">Comprehensive marketing solutions tailored to your business needs</p>
        </div>

        <div class="row g-4">
          <!-- Service 1 -->
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-4 shadow-sm border-0 bg-light">
              <div class="bg-success bg-opacity-10 rounded-3 mb-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-graph-up-arrow fs-2 text-success"></i>
              </div>
              <h5 class="fw-bold">SEO Optimization</h5>
              <p class="text-muted small">Boost your search rankings and drive organic traffic with our proven SEO strategies.</p>
            </div>
          </div>
          <!-- Service 2 -->
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-4 shadow-sm border-0 bg-light">
              <div class="bg-primary bg-opacity-10 rounded-3 mb-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-people fs-2 text-primary"></i>
              </div>
              <h5 class="fw-bold">Social Media</h5>
              <p class="text-muted small">Engage your audience across platforms with compelling content and campaigns.</p>
            </div>
          </div>
          <!-- Service 3 -->
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-4 shadow-sm border-0 bg-light">
              <div class="bg-warning bg-opacity-10 rounded-3 mb-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-megaphone fs-2 text-warning"></i>
              </div>
              <h5 class="fw-bold">Content Marketing</h5>
              <p class="text-muted small">Create content that attracts, informs, and converts your target customers.</p>
            </div>
          </div>
          <!-- Service 4 -->
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-4 shadow-sm border-0 bg-light">
              <div class="bg-danger bg-opacity-10 rounded-3 mb-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-bar-chart fs-2 text-danger"></i>
              </div>
              <h5 class="fw-bold">PPC Advertising</h5>
              <p class="text-muted small">Drive immediate traffic with highly-targeted paid campaigns.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 bg-light">
      <div class="container py-5">
        <div class="text-center mb-5">
          <h2 class="fw-bold text-dark mb-2">What Our Clients Say</h2>
          <p class="text-muted">Don't just take our word for it - hear from our satisfied clients</p>
        </div>

        <div class="row g-4">
          @foreach([
            ['name' => 'Maria Santos', 'company' => 'TechStart Philippines', 'text' => 'ROC.ph transformed our digital presence completely. Our website traffic increased by 300% in just 6 months!'],
            ['name' => 'John Cruz', 'company' => 'EcoRetail Solutions', 'text' => 'The team at ROC.ph is incredibly professional and results-driven. Our social media engagement has never been better.'],
            ['name' => 'Anna Rodriguez', 'company' => 'Global Consulting Group', 'text' => 'Outstanding service! Their strategic approach helped us generate quality leads consistently.']
          ] as $testimonial)
          <div class="col-lg-4">
            <div class="card h-100 p-4 border-0 shadow-sm">
              <div class="text-warning mb-3">
                @for($i=0; $i<5; $i++) <i class="bi bi-star-fill"></i> @endfor
              </div>
              <p class="text-dark mb-4 fst-italic">"{{ $testimonial['text'] }}"</p>
              <div class="mt-auto">
                <h6 class="fw-bold mb-0 text-dark">{{ $testimonial['name'] }}</h6>
                <small class="text-muted">{{ $testimonial['company'] }}</small>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 hero-gradient text-white">
      <div class="container py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <h2 class="display-6 fw-bold mb-4">Ready to Grow Your Business?</h2>
            <p class="lead opacity-90 mb-5">Let's discuss how we can help you achieve your marketing goals. Get a free consultation with our experts today.</p>

            <div class="d-flex flex-column gap-4 mb-5">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  <i class="bi bi-envelope fs-4"></i>
                </div>
                <span class="fs-5">hello@roc.ph</span>
              </div>
              <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  <i class="bi bi-telephone fs-4"></i>
                </div>
                <span class="fs-5">+63 (2) 1234-5678</span>
              </div>
            </div>
            <a href="#" class="btn btn-warning btn-lg px-5 rounded-pill fw-bold">Request a Consultation</a>
          </div>

          <div class="col-lg-6">
            <div class="card overflow-hidden rounded-4 shadow-lg border-0">
              <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=60&auto=format&fit=crop" alt="graph" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-dark text-light py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="fs-4 fw-bold text-warning mb-3">ROC.ph</div>
          <p class="text-secondary small">Your trusted partner in digital marketing excellence. We provide data-driven strategies to help your business scale.</p>
        </div>
        <div class="col-sm-6 col-lg-4">
          <h6 class="fw-bold text-white mb-3">Services</h6>
          <ul class="list-unstyled text-secondary small">
            <li class="mb-2">SEO Optimization</li>
            <li class="mb-2">Social Media Marketing</li>
            <li class="mb-2">Content Marketing</li>
            <li class="mb-2">PPC Advertising</li>
          </ul>
        </div>
        <div class="col-sm-6 col-lg-4">
          <h6 class="fw-bold text-white mb-3">Company</h6>
          <ul class="list-unstyled text-secondary small">
            <li class="mb-2">About Us</li>
            <li class="mb-2">Careers</li>
            <li class="mb-2">Blog</li>
            <li class="mb-2">Contact</li>
          </ul>
        </div>
      </div>
      <hr class="my-5 border-secondary">
      <div class="text-center text-secondary small">
        © 2025 ROC.ph. All rights reserved.
      </div>
    </div>
  </footer>
</body>
</html>