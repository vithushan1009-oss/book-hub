<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us — BOOK HUB</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
  <link rel="stylesheet" href="static/css/home.css">
  <link rel="stylesheet" href="static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.css">
  <style>
    .contact-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      text-align: center;
      padding: 2rem 1.5rem;
      transition: var(--transition-smooth);
    }
    .contact-card:hover {
      box-shadow: var(--shadow-xl);
      transform: translateY(-4px);
    }
    .contact-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 3.5rem;
      height: 3.5rem;
      border-radius: 50%;
      margin-bottom: 1rem;
    }
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-group label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../src/components/navbar.php'; ?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <div style="max-width: 48rem; margin: 0 auto;">
        <h1>Get in Touch</h1>
        <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
      </div>
    </div>
  </section>

  <!-- Contact Info Cards -->
  <section style="padding: 3rem 0;">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        
        <div class="contact-card">
          <div class="contact-icon" style="background: hsla(6, 78%, 57%, 0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--secondary);">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
          </div>
          <h3 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--foreground);">Phone</h3>
          <p style="color: var(--muted-foreground);">+94 21 222 3456</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon" style="background: hsla(38, 90%, 51%, 0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent);">
              <rect width="20" height="16" x="2" y="4" rx="2"/>
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
          </div>
          <h3 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--foreground);">Email</h3>
          <p style="color: var(--muted-foreground);">info@bookhub.com</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon" style="background: hsla(210, 29%, 24%, 0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
          </div>
          <h3 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--foreground);">Address</h3>
          <p style="color: var(--muted-foreground);">45 Stanley Road, Jaffna</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon" style="background: hsla(145, 63%, 42%, 0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: hsl(145, 63%, 42%);">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <h3 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--foreground);">Hours</h3>
          <p style="color: var(--muted-foreground);">Mon-Sat: 9AM-8PM</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Contact Form & Map -->
  <section style="padding: 3rem 0 5rem;">
    <div class="container">
      <style>
        @media (min-width: 768px) {
          .contact-grid {
            grid-template-columns: 1fr 1fr !important;
          }
          .name-grid {
            grid-template-columns: 1fr 1fr !important;
          }
        }
      </style>
      <div class="contact-grid" style="display: grid; grid-template-columns: 1fr; gap: 3rem;">
        
        <!-- Contact Form -->
        <div>
          <h2 style="font-size: 1.875rem; font-weight: 700; color: var(--primary); margin-bottom: 1.5rem;">Send Us a Message</h2>
          <form id="contact-form" novalidate style="background: var(--card); padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
            <div class="name-grid" style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
              <div class="form-group">
                <label for="firstName">First Name <span style="color: var(--secondary);">*</span></label>
                <input type="text" id="firstName" name="firstName" placeholder="Kamal">
              </div>
              <div class="form-group">
                <label for="lastName">Last Name <span style="color: var(--secondary);">*</span></label>
                <input type="text" id="lastName" name="lastName" placeholder="Kumar">
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address <span style="color: var(--secondary);">*</span></label>
              <input type="email" id="email" name="email" placeholder="kamal@example.com">
            </div>

            <div class="form-group">
              <label for="subject">Subject <span style="color: var(--secondary);">*</span></label>
              <input type="text" id="subject" name="subject" placeholder="How can we help you?">
            </div>

            <div class="form-group">
              <label for="message">Message <span style="color: var(--secondary);">*</span></label>
              <textarea id="message" name="message" rows="6" placeholder="Tell us more about your inquiry..."></textarea>
            </div>

            <button type="submit" class="btn btn-secondary btn-lg" style="width: 100%;">
              <i class="fas fa-paper-plane"></i> Send Message
            </button>
          </form>
        </div>

        <!-- Map & Hours -->
        <div>
          <h2 style="font-size: 1.875rem; font-weight: 700; color: var(--primary); margin-bottom: 1.5rem;">Visit Our Location</h2>
          <div style="border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg); height: 500px; background: var(--muted); margin-bottom: 1.5rem;">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2sNew%20York%20Public%20Library!5e0!3m2!1sen!2sus!4v1234567890123"
              width="100%"
              height="100%"
              style="border: 0;"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="BOOK HUB Location">
            </iframe>
          </div>

          <div class="contact-card" style="text-align: left;">
            <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">Opening Hours</h3>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; color: var(--muted-foreground);">
              <div style="display: flex; justify-content: space-between;">
                <span>Monday - Friday</span>
                <span style="font-weight: 600; color: var(--foreground);">9:00 AM - 8:00 PM</span>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <span>Saturday</span>
                <span style="font-weight: 600; color: var(--foreground);">10:00 AM - 6:00 PM</span>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <span>Sunday</span>
                <span style="font-weight: 600; color: var(--foreground);">Closed</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/contact.js"></script>
</body>
</html>
