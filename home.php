<?php
/**
 * CynTour - Unified Home Page
 * 
 * This is the main landing page for the CynTour travel agency.
 * It provides an overview of services and links to other sections.
 */

require_once 'includes/components.php';
require_once 'config.php';

// Get some stats for the hero section
$conn = getMysqliConnection();
$hotelCount = 0;
$tourCount = 0;

$result = $conn->query("SELECT COUNT(DISTINCT hotel_name) as count FROM pricing_data");
if ($result) {
    $hotelCount = $result->fetch_assoc()['count'] ?? 0;
}

$result = $conn->query("SELECT COUNT(*) as count FROM city_tour_vouchers");
if ($result) {
    $tourCount = $result->fetch_assoc()['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php cyn_render_head('CynTour - Premium Travel Services in Turkey'); ?>
    <style>
        /* Hero Section Styles */
        .home-hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, rgba(26, 51, 72, 0.9) 0%, rgba(42, 77, 105, 0.85) 100%),
                        url('istanbul.jpeg') center/cover no-repeat;
            color: var(--white);
            text-align: center;
            padding: var(--spacing-3xl) var(--spacing-lg);
        }
        
        .home-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: linear-gradient(to top, var(--light), transparent);
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            background: rgba(202, 140, 5, 0.2);
            border: 1px solid rgba(202, 140, 5, 0.4);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: var(--spacing-lg);
            animation: fadeInDown 0.8s ease forwards;
        }
        
        .hero-badge i {
            color: var(--primary-light);
        }
        
        .home-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            color: var(--white);
            line-height: 1.1;
            margin-bottom: var(--spacing-lg);
            animation: fadeInUp 0.8s ease 0.2s forwards;
            opacity: 0;
        }
        
        .home-hero h1 span {
            color: var(--primary-light);
            font-style: italic;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto var(--spacing-xl);
            opacity: 0;
            animation: fadeInUp 0.8s ease 0.4s forwards;
            color: rgba(255,255,255,0.9);
        }
        
        .hero-buttons {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeInUp 0.8s ease 0.6s forwards;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: var(--spacing-3xl);
            margin-top: var(--spacing-3xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid rgba(255,255,255,0.2);
            opacity: 0;
            animation: fadeInUp 0.8s ease 0.8s forwards;
        }
        
        .hero-stat {
            text-align: center;
        }
        
        .hero-stat-number {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-light);
            line-height: 1;
        }
        
        .hero-stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-top: var(--spacing-xs);
        }
        
        /* Services Section */
        .services-section {
            background: var(--light);
        }
        
        .service-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: var(--spacing-xl);
            text-align: center;
            transition: var(--transition-normal);
            border: 2px solid transparent;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(202, 140, 5, 0.3);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            border-radius: var(--radius-full);
            box-shadow: 0 10px 30px rgba(202, 140, 5, 0.25);
        }
        
        .service-icon i {
            font-size: 2rem;
            color: var(--white);
        }
        
        .service-card h3 {
            color: var(--secondary);
            font-size: 1.35rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .service-card p {
            color: var(--gray-600);
            font-size: 0.95rem;
            margin-bottom: var(--spacing-md);
        }
        
        /* Featured Section */
        .featured-section {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: var(--white);
        }
        
        .featured-section .cyn-section-title {
            color: var(--white);
        }
        
        .featured-section .cyn-section-desc {
            color: rgba(255,255,255,0.8);
        }
        
        .featured-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-xl);
            overflow: hidden;
            transition: var(--transition-normal);
        }
        
        .featured-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        
        .featured-card-img {
            height: 200px;
            overflow: hidden;
        }
        
        .featured-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-slow);
        }
        
        .featured-card:hover .featured-card-img img {
            transform: scale(1.1);
        }
        
        .featured-card-body {
            padding: var(--spacing-lg);
        }
        
        .featured-card h3 {
            color: var(--primary-light);
            font-size: 1.3rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .featured-card p {
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            margin-bottom: var(--spacing-md);
        }
        
        /* About Section */
        .about-section {
            background: var(--white);
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-3xl);
            align-items: center;
        }
        
        .about-image {
            position: relative;
        }
        
        .about-image img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
        }
        
        .about-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid var(--primary);
            border-radius: var(--radius-xl);
            z-index: -1;
        }
        
        .experience-badge {
            position: absolute;
            bottom: -25px;
            right: -25px;
            background: var(--secondary);
            color: var(--white);
            padding: var(--spacing-lg) var(--spacing-xl);
            border-radius: var(--radius-xl);
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        
        .experience-number {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-light);
            line-height: 1;
        }
        
        .about-content h2 {
            font-size: 2.2rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-lg);
        }
        
        .about-content h2 span {
            color: var(--primary);
            font-style: italic;
        }
        
        .about-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-xl);
        }
        
        .about-feature {
            display: flex;
            gap: var(--spacing-md);
        }
        
        .about-feature-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(202, 140, 5, 0.1);
            border-radius: var(--radius-lg);
            flex-shrink: 0;
        }
        
        .about-feature-icon i {
            color: var(--primary);
            font-size: 1.25rem;
        }
        
        .about-feature h4 {
            font-family: var(--font-primary);
            font-weight: 600;
            font-size: 1rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-xs);
        }
        
        .about-feature p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        /* Certification Section */
        .cert-section {
            background: var(--light);
        }
        
        .cert-card {
            display: flex;
            align-items: center;
            gap: var(--spacing-xl);
            background: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cert-badge img {
            height: 100px;
        }
        
        .cert-info h3 {
            color: var(--secondary);
            font-size: 1.5rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .cert-info p {
            margin-bottom: var(--spacing-md);
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--primary-gradient);
            text-align: center;
            padding: var(--spacing-3xl) var(--spacing-lg);
        }
        
        .cta-section h2 {
            color: var(--white);
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }
        
        .cta-section p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto var(--spacing-xl);
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .hero-stats {
                gap: var(--spacing-xl);
            }
            
            .about-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);
            }
            
            .about-image {
                max-width: 500px;
                margin: 0 auto;
            }
            
            .cert-card {
                flex-direction: column;
                text-align: center;
            }
        }
        
        @media (max-width: 767px) {
            .hero-stats {
                flex-direction: column;
                gap: var(--spacing-lg);
            }
            
            .about-features {
                grid-template-columns: 1fr;
            }
            
            .experience-badge {
                bottom: -15px;
                right: 10px;
                padding: var(--spacing-md);
            }
            
            .experience-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php cyn_render_navbar(true); ?>
    
    <!-- Hero Section -->
    <section class="home-hero">
        <div class="cyn-container">
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                <span>Premium Travel Since 2006</span>
            </div>
            
            <h1>Discover the <span>Magic</span> of Turkey</h1>
            
            <p class="hero-subtitle">
                Experience world-class travel services with CynTour. From luxury hotels to exclusive tours, 
                we craft unforgettable journeys across Turkey's most breathtaking destinations.
            </p>
            
            <div class="hero-buttons">
                <a href="index.php" class="cyn-btn cyn-btn-primary cyn-btn-lg">
                    <i class="fas fa-hotel"></i> Explore Hotels
                </a>
                <a href="tours.php" class="cyn-btn cyn-btn-outline cyn-btn-lg" style="color: var(--white); border-color: rgba(255,255,255,0.5);">
                    <i class="fas fa-map-marked-alt"></i> View Tours
                </a>
            </div>
            
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo $hotelCount; ?>+</div>
                    <div class="hero-stat-label">Hotels</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">18+</div>
                    <div class="hero-stat-label">Years Experience</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo $tourCount > 1000 ? number_format($tourCount/1000, 1) . 'K' : $tourCount; ?>+</div>
                    <div class="hero-stat-label">Tours Completed</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">50+</div>
                    <div class="hero-stat-label">Destinations</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section class="cyn-section services-section">
        <div class="cyn-container">
            <div class="cyn-section-header">
                <span class="cyn-section-tag">Our Services</span>
                <h2 class="cyn-section-title">Premium <span>Travel</span> Services</h2>
                <p class="cyn-section-desc">We offer comprehensive travel solutions to make your Turkish experience seamless and memorable.</p>
            </div>
            
            <div class="cyn-grid cyn-grid-4" style="gap: var(--spacing-lg);">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h3>Hotel Reservations</h3>
                    <p>Access to Turkey's finest hotels and resorts, from boutique properties to luxury chains.</p>
                    <a href="index.php" class="cyn-btn cyn-btn-outline cyn-btn-sm">Browse Hotels</a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3>Guided Tours</h3>
                    <p>Expert-led tours to Istanbul, Cappadocia, Ephesus, and more hidden gems of Turkey.</p>
                    <a href="tours.php" class="cyn-btn cyn-btn-outline cyn-btn-sm">View Tours</a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>VIP Transfers</h3>
                    <p>Comfortable airport transfers and private transportation throughout your journey.</p>
                    <a href="transfer.php" class="cyn-btn cyn-btn-outline cyn-btn-sm">Book Transfer</a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3>Concierge Service</h3>
                    <p>24/7 dedicated support to assist with any request during your travels.</p>
                    <a href="contact.php" class="cyn-btn cyn-btn-outline cyn-btn-sm">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Destinations -->
    <section class="cyn-section featured-section">
        <div class="cyn-container">
            <div class="cyn-section-header">
                <span class="cyn-section-tag" style="color: var(--primary-light);">Featured</span>
                <h2 class="cyn-section-title">Popular <span>Experiences</span></h2>
                <p class="cyn-section-desc">Discover our most sought-after services that travelers love.</p>
            </div>
            
            <div class="cyn-grid cyn-grid-3">
                <div class="featured-card">
                    <div class="featured-card-img">
                        <img src="tra.webp" alt="Transfer Services">
                    </div>
                    <div class="featured-card-body">
                        <h3>Luxury Transfer Services</h3>
                        <p>Premium transportation from all major airports in Turkey with our fleet of luxury vehicles.</p>
                        <a href="transfer.php" class="cyn-btn cyn-btn-primary cyn-btn-sm">Learn More</a>
                    </div>
                </div>
                
                <div class="featured-card">
                    <div class="featured-card-img">
                        <img src="tour.webp" alt="Exclusive Tours">
                    </div>
                    <div class="featured-card-body">
                        <h3>Exclusive Tours</h3>
                        <p>Discover Turkey's hidden gems with our carefully curated tours to magical destinations.</p>
                        <a href="tours.php" class="cyn-btn cyn-btn-primary cyn-btn-sm">Explore Tours</a>
                    </div>
                </div>
                
                <div class="featured-card">
                    <div class="featured-card-img">
                        <img src="istanbul.jpeg" alt="Hotel Accommodations">
                    </div>
                    <div class="featured-card-body">
                        <h3>Premium Hotels</h3>
                        <p>From beachfront villas to historic boutique hotels, find your perfect accommodation.</p>
                        <a href="index.php" class="cyn-btn cyn-btn-primary cyn-btn-sm">View Hotels</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="cyn-section about-section">
        <div class="cyn-container">
            <div class="about-grid">
                <div class="about-image">
                    <img src="istanbul.jpeg" alt="About CynTour">
                    <div class="experience-badge">
                        <div class="experience-number">18+</div>
                        <div>Years of<br>Excellence</div>
                    </div>
                </div>
                
                <div class="about-content">
                    <span class="cyn-section-tag">About Us</span>
                    <h2>Your Trusted <span>Partner</span> in Turkish Tourism</h2>
                    <p>Since 2006, CYN TURIZM has been providing exceptional travel services across Turkey. As a TURSAB certified agency (License No: 11738), we uphold the highest standards in the tourism industry.</p>
                    <p>Our team of experienced professionals is dedicated to creating unforgettable experiences for travelers from around the world. From the historic streets of Istanbul to the fairy chimneys of Cappadocia, we bring Turkey's magic to life.</p>
                    
                    <div class="about-features">
                        <div class="about-feature">
                            <div class="about-feature-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div>
                                <h4>TURSAB Certified</h4>
                                <p>Official tourism license holder</p>
                            </div>
                        </div>
                        
                        <div class="about-feature">
                            <div class="about-feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h4>24/7 Support</h4>
                                <p>Always here to help you</p>
                            </div>
                        </div>
                        
                        <div class="about-feature">
                            <div class="about-feature-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <h4>Global Network</h4>
                                <p>Partners worldwide</p>
                            </div>
                        </div>
                        
                        <div class="about-feature">
                            <div class="about-feature-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <h4>Trusted Service</h4>
                                <p>Thousands of happy clients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Certification Section -->
    <section class="cyn-section cert-section">
        <div class="cyn-container">
            <div class="cert-card">
                <div class="cert-badge">
                    <img src="img/tursab-seeklogo-removebg.png" alt="TURSAB Certification">
                </div>
                <div class="cert-info">
                    <h3>Official TURSAB Certification</h3>
                    <p><strong>CYN TURIZM</strong> is proudly certified by TURSAB (Association of Turkish Travel Agencies) under license number <strong style="color: var(--primary);">11738</strong>. This certification guarantees that we meet all legal requirements and quality standards in Turkey's tourism industry.</p>
                    <a href="https://www.tursab.org.tr/acenta-arama" target="_blank" class="cyn-btn cyn-btn-primary">
                        <i class="fas fa-external-link-alt"></i> Verify Certificate
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cyn-container">
            <h2>Ready to Explore Turkey?</h2>
            <p>Let us help you plan your perfect Turkish adventure. Contact our team today for personalized travel solutions.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="contact.php" class="cyn-btn cyn-btn-light cyn-btn-lg">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
                <a href="https://wa.me/905318176770" target="_blank" class="cyn-btn cyn-btn-lg" style="background: #25D366; color: white;">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </section>
    
    <?php cyn_render_footer(); ?>
    <?php cyn_render_scripts(); ?>
    
    <script>
    // Add transparent navbar scroll behavior
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('mainNav');
        
        function updateNavbar() {
            if (window.scrollY > 100) {
                navbar.style.background = 'var(--white)';
                navbar.style.boxShadow = 'var(--shadow-md)';
                navbar.querySelectorAll('.cyn-nav-link').forEach(link => {
                    link.style.color = 'var(--gray-700)';
                });
            } else {
                navbar.style.background = 'transparent';
                navbar.style.boxShadow = 'none';
                navbar.querySelectorAll('.cyn-nav-link').forEach(link => {
                    if (!link.classList.contains('cyn-btn')) {
                        link.style.color = 'var(--white)';
                    }
                });
            }
        }
        
        updateNavbar();
        window.addEventListener('scroll', updateNavbar);
    });
    </script>
</body>
</html>
