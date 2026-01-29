<?php
/**
 * Image Configuration for CYN Tourism
 * 
 * This file manages image paths throughout the application.
 * To switch from local images to CDN-hosted images:
 * 1. Upload your images to a CDN (e.g., Cloudinary, AWS S3, imgBB)
 * 2. Set USE_CDN to true
 * 3. Update CDN_BASE_URL with your CDN base URL
 * 
 * Benefits:
 * - Reduces repository size for faster downloads
 * - Allows image optimization through CDN
 * - Easy to switch between local and CDN hosting
 */

// Configuration flags
define('USE_CDN', false); // Set to true when images are hosted externally
define('CDN_BASE_URL', 'https://your-cdn.com/cyntour-images'); // Your CDN base URL

// Local paths relative to document root
define('LOCAL_IMAGE_PATH', '');
define('LOCAL_IMG_FOLDER', 'img/');

/**
 * Get the correct image path based on configuration
 * 
 * @param string $imagePath Local path to the image
 * @return string Full URL to the image (local or CDN)
 */
function getImageUrl($imagePath) {
    if (USE_CDN) {
        // Remove leading ./ or / from path
        $cleanPath = ltrim($imagePath, './');
        return CDN_BASE_URL . '/' . $cleanPath;
    }
    return $imagePath;
}

/**
 * Image manifest - maps image names to their descriptions
 * Useful for documentation and CDN migration
 */
$IMAGE_MANIFEST = [
    // Logos and branding
    'logo.png' => 'Main company logo',
    'logo-mini.png' => 'Compact logo for mobile/sidebar',
    'footer-logo.png' => 'Footer logo',
    'stamp.png' => 'Official company stamp for vouchers',
    'singateur.png' => 'Signature image for documents',
    'Toursablogo.png' => 'TURSAB certification logo',
    
    // Tour images
    'tour.webp' => 'General tour placeholder',
    'bosphorus-cruise.jpg' => 'Bosphorus cruise tour',
    'Capadocia-jungle-tour.jpg' => 'Cappadocia jungle tour',
    'Cappadocia-bike-tour.jpg' => 'Cappadocia bike tour',
    'cappadocia-sky-tour.jpg' => 'Cappadocia hot air balloon tour',
    'Kapadokya-horse-tour.jpg' => 'Cappadocia horse riding tour',
    'antalya-tour.jpg' => 'Antalya tour',
    'izmit-tour.jpg' => 'Izmit tour',
    'trabzon-tour.jpg' => 'Trabzon tour',
    'turkish-night.jpg' => 'Turkish night entertainment',
    
    // Destination images
    'istanbul.jpeg' => 'Istanbul cityscape',
    'Baku.png' => 'Baku, Azerbaijan',
    'Bakuhotels.png' => 'Baku hotels overview',
    'Bursa.jpg' => 'Bursa city',
    'Sapanca.png' => 'Sapanca lake',
    'Sile.png' => 'Sile beach',
    'Yalova.png' => 'Yalova thermal springs',
    'Princess-Island.png' => 'Princes Islands',
    'dubai.jpeg' => 'Dubai destination',
    'egypt.jpeg' => 'Egypt destination',
    'malaysia.jpeg' => 'Malaysia destination',
    'maldives.jpeg' => 'Maldives destination',
    'asia.jpeg' => 'Asia destinations',
    'europe.jpeg' => 'Europe destinations',
    
    // Airport images
    'Antalya-airport.jpeg' => 'Antalya Airport',
    'Istanbul-international-airport.jpeg' => 'Istanbul Airport',
    'Sabiha-airport.jpeg' => 'Sabiha Gokcen Airport',
    'Baku-International-Airport.png' => 'Baku Airport',
    'bakuairport.png' => 'Baku Airport alternative',
    
    // Hotel images
    'istanbulhotels.png' => 'Istanbul hotels overview',
    'cvk-park-bosphorus.jpg' => 'CVK Park Bosphorus Hotel',
    'swissotel-bosphorus.jpg' => 'Swissotel Bosphorus',
    
    // Vehicle images
    'mercedes-bus.jpeg' => 'Mercedes bus for group transfers',
    'mercedes-luxury-sedan.jpeg' => 'Mercedes luxury sedan',
    'mercedes-sprinter-van.jpeg' => 'Mercedes Sprinter van',
    'mercedes-vito.jpeg' => 'Mercedes Vito van',
    'tra.webp' => 'Transfer vehicle',
    
    // Slides and hero images
    'slide2.jpg' => 'Homepage slider image 2',
    'slide45.jpg' => 'Homepage slider image',
    'slide452.jpg' => 'Homepage slider image variant',
    
    // Images in /img folder
    'img/logo.png' => 'Logo in img folder',
    'img/icon123.ico' => 'Favicon',
    'img/Tursab.png' => 'TURSAB logo',
    'img/tursab-seeklogo-removebg.png' => 'TURSAB logo transparent',
    'img/istanbul.png' => 'Istanbul image',
    'img/istanbul1.png' => 'Istanbul alternative image',
    'img/undraw_profile.svg' => 'Default profile avatar',
    'img/undraw_profile_1.svg' => 'Profile avatar variant 1',
    'img/undraw_profile_2.svg' => 'Profile avatar variant 2',
    'img/undraw_profile_3.svg' => 'Profile avatar variant 3',
];

/**
 * Get a list of all image files that can be moved to CDN
 */
function getImageFiles() {
    global $IMAGE_MANIFEST;
    return array_keys($IMAGE_MANIFEST);
}

/**
 * Instructions for migrating to CDN:
 * 
 * 1. Create an account on your preferred CDN service:
 *    - Cloudinary (https://cloudinary.com) - Good for image optimization
 *    - imgBB (https://imgbb.com) - Simple and free
 *    - AWS S3 (https://aws.amazon.com/s3) - Enterprise grade
 *    - GitHub raw (create a separate repo for images)
 * 
 * 2. Upload all images from this repository to your CDN
 *    - Maintain the same folder structure (img/ subfolder)
 *    - Keep filenames exactly the same
 * 
 * 3. Update CDN_BASE_URL in this file with your CDN URL
 * 
 * 4. Set USE_CDN to true
 * 
 * 5. Test all pages to ensure images load correctly
 * 
 * 6. Once confirmed working, you can remove images from the repository:
 *    - Add image extensions to .gitignore
 *    - Run: git rm --cached *.jpg *.jpeg *.png *.gif *.webp
 *    - Keep local copies for backup
 */
?>
