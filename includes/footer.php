<?php
/**
 * Shared Footer Component
 * Include this file in your pages to get consistent footer
 * Usage: include 'includes/footer.php';
 */
?>

<!-- Footer -->
<footer class="footer bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-3">
                    <img src="img/logo.png" alt="CYN Tourism" style="height: 50px; filter: brightness(0) invert(1);">
                </div>
                <p class="text-muted mb-3">Your trusted partner for premium travel services in Turkey since 2006.</p>
                <div class="certification">
                    <img src="img/tursab-seeklogo-removebg.png" alt="TURSAB Certified" style="height: 50px;">
                    <p class="small text-muted mt-2">TURSAB License No: 11738</p>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="index.php"><i class="fas fa-angle-right me-2"></i>Home</a></li>
                    <li><a href="tours.php"><i class="fas fa-angle-right me-2"></i>Tours</a></li>
                    <li><a href="transfer.php"><i class="fas fa-angle-right me-2"></i>Transfer</a></li>
                    <li><a href="contact.php"><i class="fas fa-angle-right me-2"></i>Contact</a></li>
                </ul>
            </div>
            
            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-3">Our Services</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="tours.php"><i class="fas fa-angle-right me-2"></i>City Tours</a></li>
                    <li><a href="tours.php"><i class="fas fa-angle-right me-2"></i>Day Trips</a></li>
                    <li><a href="transfer.php"><i class="fas fa-angle-right me-2"></i>Airport Transfer</a></li>
                    <li><a href="index.php"><i class="fas fa-angle-right me-2"></i>Hotel Booking</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-3">Contact Us</h5>
                <ul class="list-unstyled contact-info">
                    <li class="mb-2">
                        <i class="fas fa-phone-alt me-2 text-primary"></i>
                        <a href="tel:+905318176770">+90 531 817 67 70</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:info@cyntour.com">info@cyntour.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:sales@cyntourim.com">sales@cyntourim.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <span>Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</span>
                    </li>
                </ul>
                <div class="social-links mt-3">
                    <a href="https://www.instagram.com/cyn__turizm/" target="_blank" class="me-3">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="me-3">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="me-3">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="#">
                        <i class="fab fa-whatsapp fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <hr class="my-4 border-secondary">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted small">
                    &copy; <?php echo date('Y'); ?> CYN TURIZM. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-muted small">
                    Established 2006 | Istanbul, Turkey
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</a>

<style>
/* Footer Styles */
.footer {
    background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%);
}

.footer h5 {
    font-weight: 600;
    position: relative;
    padding-bottom: 10px;
}

.footer h5::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background-color: #CA8C05;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a,
.contact-info a,
.contact-info span {
    color: #aaa;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.footer-links a:hover,
.contact-info a:hover {
    color: #CA8C05;
    padding-left: 5px;
}

.social-links a {
    color: #aaa;
    transition: all 0.3s ease;
}

.social-links a:hover {
    color: #CA8C05;
    transform: translateY(-3px);
}

.text-primary {
    color: #CA8C05 !important;
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 45px;
    height: 45px;
    background-color: #CA8C05;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
    box-shadow: 0 4px 15px rgba(202, 140, 5, 0.3);
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background-color: #a06000;
    color: white;
    transform: translateY(-5px);
}
</style>

<script>
// Back to Top Button Functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTop = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    
    backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>
