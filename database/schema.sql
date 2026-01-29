-- =====================================================
-- Cyntour Tourism Management System Database Schema
-- Version: 1.0
-- Description: Complete database schema with sample data
-- Note: Tables are automatically created on first connection
-- =====================================================

-- Create database if not exists (run manually if needed)
-- CREATE DATABASE IF NOT EXISTS barqvkxs_cyn 
--     CHARACTER SET utf8mb4 
--     COLLATE utf8mb4_unicode_ci;

-- USE barqvkxs_cyn;

-- =====================================================
-- USERS AND AUTHENTICATION
-- =====================================================

-- Users table for authentication and user management
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'agent') DEFAULT 'user',
    remember_token VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- HOTELS AND PRICING
-- =====================================================

-- Hotels master table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    address TEXT,
    star_rating TINYINT DEFAULT 0,
    description TEXT,
    amenities JSON,
    contact_phone VARCHAR(50),
    contact_email VARCHAR(255),
    website VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_city (city),
    INDEX idx_status (status),
    FULLTEXT idx_search (name, city, district)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hotel pricing data (room types and rates)
CREATE TABLE IF NOT EXISTS pricing_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(255) NOT NULL,
    room_type VARCHAR(100) NOT NULL,
    accommodation VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    meal_plan ENUM('RO', 'BB', 'HB', 'FB', 'AI', 'UAI') DEFAULT 'BB',
    min_stay INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_hotel_name (hotel_name),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_room_type (room_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hotel images
CREATE TABLE IF NOT EXISTS hotel_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_hotel_id (hotel_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TOURS
-- =====================================================

-- Tours master table
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    duration VARCHAR(50),
    price DECIMAL(10, 2),
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    max_participants INT,
    included TEXT,
    not_included TEXT,
    meeting_point VARCHAR(255),
    category ENUM('city_tour', 'day_trip', 'multi_day', 'adventure', 'cultural') DEFAULT 'day_trip',
    image_url VARCHAR(500),
    pdf_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    FULLTEXT idx_search (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour schedules (available dates)
CREATE TABLE IF NOT EXISTS tour_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    tour_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    available_spots INT,
    price_override DECIMAL(10, 2) DEFAULT NULL,
    status ENUM('available', 'full', 'cancelled') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    INDEX idx_tour_date (tour_id, tour_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRANSFERS
-- =====================================================

-- Transfer services
CREATE TABLE IF NOT EXISTS transfer_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    vehicle_type ENUM('sedan', 'vito', 'sprinter', 'bus', 'luxury') NOT NULL,
    max_passengers INT NOT NULL,
    max_luggage INT,
    description TEXT,
    image_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vehicle_type (vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transfer routes and pricing
CREATE TABLE IF NOT EXISTS transfer_routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    distance_km DECIMAL(6, 2),
    estimated_duration_minutes INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_locations (pickup_location, dropoff_location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transfer pricing
CREATE TABLE IF NOT EXISTS transfer_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    service_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES transfer_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES transfer_services(id) ON DELETE CASCADE,
    INDEX idx_route_service (route_id, service_id),
    INDEX idx_validity (valid_from, valid_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VOUCHERS AND BOOKINGS
-- =====================================================

-- Hotel vouchers
CREATE TABLE IF NOT EXISTS h_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_no VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    hotel_name VARCHAR(255) NOT NULL,
    room_type VARCHAR(100),
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    infants INT DEFAULT 0,
    meal_plan VARCHAR(50),
    total_price DECIMAL(10, 2),
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_voucher_no (voucher_no),
    INDEX idx_status (status),
    INDEX idx_dates (check_in, check_out)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- City tour vouchers
CREATE TABLE IF NOT EXISTS city_tour_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_no VARCHAR(60) NOT NULL,
    company_name VARCHAR(120) NOT NULL,
    customer_phone VARCHAR(40),
    hotel_name VARCHAR(120),
    adult INT DEFAULT 0,
    child INT DEFAULT 0,
    infant INT DEFAULT 0,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_voucher_no (voucher_no),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- City tour voucher customers
CREATE TABLE IF NOT EXISTS city_tour_voucher_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_id INT NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    FOREIGN KEY (voucher_id) REFERENCES city_tour_vouchers(id) ON DELETE CASCADE,
    INDEX idx_voucher_id (voucher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- City tour voucher tours
CREATE TABLE IF NOT EXISTS city_tour_voucher_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_id INT NOT NULL,
    tour_name VARCHAR(120) NOT NULL,
    tour_date DATE NOT NULL,
    duration VARCHAR(40),
    FOREIGN KEY (voucher_id) REFERENCES city_tour_vouchers(id) ON DELETE CASCADE,
    INDEX idx_voucher_id (voucher_id),
    INDEX idx_tour_date (tour_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transfer vouchers
CREATE TABLE IF NOT EXISTS transfer_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_no VARCHAR(50) NOT NULL,
    company_name VARCHAR(120),
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    hotel_name VARCHAR(255),
    flight_number VARCHAR(20),
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    pickup_date DATE NOT NULL,
    pickup_time TIME NOT NULL,
    return_date DATE,
    return_time TIME,
    transfer_type ENUM('arrival', 'departure', 'round_trip', 'point_to_point') NOT NULL,
    vehicle_type VARCHAR(50),
    total_pax INT DEFAULT 1,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    infants INT DEFAULT 0,
    total_price DECIMAL(10, 2),
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_voucher_no (voucher_no),
    INDEX idx_status (status),
    INDEX idx_pickup_date (pickup_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RECEIPTS AND PAYMENTS
-- =====================================================

-- Receipts
CREATE TABLE IF NOT EXISTS receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    customer_company VARCHAR(255),
    customer_email VARCHAR(255),
    customer_phone VARCHAR(50),
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    currency ENUM('USD', 'EUR', 'TRY', 'GBP') DEFAULT 'EUR',
    payment_status ENUM('pending', 'partial', 'paid', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_receipt_no (receipt_no),
    INDEX idx_payment_status (payment_status),
    INDEX idx_customer_company (customer_company(100)),
    INDEX idx_created_at (created_at),
    INDEX idx_company_currency (customer_company(100), currency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Receipt items
CREATE TABLE IF NOT EXISTS receipt_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT NOT NULL,
    item_type ENUM('hotel', 'tour', 'transfer', 'other') NOT NULL,
    description VARCHAR(500) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    voucher_reference VARCHAR(50),
    FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE CASCADE,
    INDEX idx_receipt_id (receipt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments
CREATE TABLE IF NOT EXISTS receipt_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'bank_transfer', 'other') NOT NULL,
    payment_date DATE NOT NULL,
    reference_no VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE CASCADE,
    INDEX idx_receipt_id (receipt_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PARTNERS AND SUPPLIERS
-- =====================================================

-- Partners (travel agencies, hotels, etc.)
CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    partner_type ENUM('hotel', 'agency', 'transport', 'guide', 'other') NOT NULL,
    commission_rate DECIMAL(5, 2) DEFAULT 0,
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_partner_type (partner_type),
    INDEX idx_status (status),
    INDEX idx_company_name (company_name(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

-- Insert sample admin user (password: Admin123!)
INSERT INTO users (company_name, first_name, last_name, email, phone_number, password, role, status) VALUES
('CYN TURIZM', 'Admin', 'User', 'admin@cyntour.com', '+905318176770', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('Travel Partner', 'John', 'Doe', 'agent@example.com', '+901234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'active'),
('Tourist', 'Jane', 'Smith', 'user@example.com', '+901234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active');

-- Insert sample hotels
INSERT INTO hotels (name, city, district, address, star_rating, description, contact_phone, status) VALUES
('CVK Park Bosphorus Hotel', 'Istanbul', 'Taksim', 'Cumhuriyet Cad. No:84, Taksim', 5, 'Luxury hotel with Bosphorus views', '+902122560000', 'active'),
('Swissotel The Bosphorus', 'Istanbul', 'Besiktas', 'Bayıldım Cad. No:2, Maçka', 5, 'Five-star luxury hotel overlooking the Bosphorus', '+902123261100', 'active'),
('Delta Hotel Laleli', 'Istanbul', 'Laleli', 'Harikzadeler Sokak No:8, Laleli', 3, 'Comfortable hotel in historic Laleli district', '+902125178585', 'active'),
('Beyaz Saray Hotel', 'Istanbul', 'Fatih', 'Akşemsettin Mah., Fatih', 4, 'Elegant hotel near historical sites', '+902125242525', 'active'),
('Selge Beach Resort', 'Antalya', 'Side', 'Side Mah., Manavgat', 5, 'All-inclusive beach resort with stunning Mediterranean views', '+902427536000', 'active');

-- Insert sample pricing data
INSERT INTO pricing_data (hotel_name, room_type, accommodation, start_date, end_date, price, currency, meal_plan) VALUES
('CVK Park Bosphorus Hotel', 'Standard Room', 'Double', '2024-01-01', '2024-03-31', 150.00, 'EUR', 'BB'),
('CVK Park Bosphorus Hotel', 'Deluxe Room', 'Double', '2024-01-01', '2024-03-31', 220.00, 'EUR', 'BB'),
('CVK Park Bosphorus Hotel', 'Suite', 'Double', '2024-01-01', '2024-03-31', 350.00, 'EUR', 'BB'),
('Swissotel The Bosphorus', 'Classic Room', 'Double', '2024-01-01', '2024-03-31', 280.00, 'EUR', 'BB'),
('Swissotel The Bosphorus', 'Bosphorus View', 'Double', '2024-01-01', '2024-03-31', 380.00, 'EUR', 'BB'),
('Delta Hotel Laleli', 'Standard Room', 'Double', '2024-01-01', '2024-12-31', 45.00, 'EUR', 'BB'),
('Delta Hotel Laleli', 'Triple Room', 'Triple', '2024-01-01', '2024-12-31', 60.00, 'EUR', 'BB'),
('Beyaz Saray Hotel', 'Standard Room', 'Double', '2024-01-01', '2024-12-31', 75.00, 'EUR', 'BB'),
('Beyaz Saray Hotel', 'Family Room', 'Quadruple', '2024-01-01', '2024-12-31', 110.00, 'EUR', 'BB'),
('Selge Beach Resort', 'Standard Room', 'Double', '2024-05-01', '2024-10-31', 180.00, 'EUR', 'AI'),
('Selge Beach Resort', 'Sea View Room', 'Double', '2024-05-01', '2024-10-31', 220.00, 'EUR', 'AI');

-- Insert sample tours
INSERT INTO tours (name, slug, description, short_description, duration, price, currency, category, status) VALUES
('Istanbul City Tour', 'istanbul-city-tour', 'Discover the magic of Istanbul! Visit the Blue Mosque, Hagia Sophia, Topkapi Palace, and the Grand Bazaar. Experience the rich history and culture of this magnificent city.', 'Full day tour of Istanbul historic sites', '8 hours', 75.00, 'EUR', 'city_tour', 'active'),
('Sapanca Maşukiye Tour', 'sapanca-masukiye-tour', 'Escape to the natural beauty of Sapanca Lake and Maşukiye. Enjoy waterfalls, nature walks, and traditional Turkish breakfast.', 'Nature day trip to Sapanca region', '10 hours', 65.00, 'EUR', 'day_trip', 'active'),
('Princes Islands Tour', 'princes-islands-tour', 'Take a ferry to the car-free Princes Islands. Explore Büyükada by horse carriage, swim in crystal clear waters, and enjoy fresh seafood.', 'Ferry trip to Princes Islands', '8 hours', 55.00, 'EUR', 'day_trip', 'active'),
('Cappadocia Red Tour', 'cappadocia-red-tour', 'Explore the Northern part of Cappadocia including Devrent Valley, Monks Valley, Open Air Museum, and more.', 'Northern Cappadocia highlights', '8 hours', 45.00, 'EUR', 'cultural', 'active'),
('Bursa Day Trip', 'bursa-day-trip', 'Visit the first Ottoman capital, Bursa. See the Grand Mosque, silk market, and enjoy a cable car ride up Mount Uludağ.', 'Day trip to historic Bursa', '12 hours', 85.00, 'EUR', 'day_trip', 'active'),
('Bosphorus Dinner Cruise', 'bosphorus-dinner-cruise', 'Enjoy a magical evening on the Bosphorus with dinner, live entertainment, and stunning views of illuminated Istanbul.', 'Evening cruise with dinner', '4 hours', 55.00, 'EUR', 'city_tour', 'active');

-- Insert sample transfer services
INSERT INTO transfer_services (name, vehicle_type, max_passengers, max_luggage, description, status) VALUES
('Mercedes E-Class', 'sedan', 3, 3, 'Luxury sedan for executive transfers', 'active'),
('Mercedes Vito', 'vito', 7, 7, 'Comfortable van for small groups and families', 'active'),
('Mercedes Sprinter', 'sprinter', 14, 14, 'Spacious van for medium groups', 'active'),
('Luxury Bus', 'bus', 45, 45, 'Full-size bus for large groups', 'active'),
('Mercedes S-Class', 'luxury', 3, 3, 'Premium luxury sedan for VIP transfers', 'active');

-- Insert sample transfer routes
INSERT INTO transfer_routes (pickup_location, dropoff_location, distance_km, estimated_duration_minutes) VALUES
('Istanbul International Airport (IST)', 'Taksim', 45.0, 45),
('Istanbul International Airport (IST)', 'Sultanahmet', 52.0, 55),
('Istanbul International Airport (IST)', 'Kadikoy', 65.0, 70),
('Sabiha Gokcen Airport (SAW)', 'Taksim', 55.0, 60),
('Sabiha Gokcen Airport (SAW)', 'Sultanahmet', 50.0, 55),
('Antalya Airport (AYT)', 'Antalya City Center', 15.0, 20),
('Antalya Airport (AYT)', 'Side', 70.0, 60),
('Antalya Airport (AYT)', 'Alanya', 125.0, 120);

-- Insert sample transfer pricing
INSERT INTO transfer_pricing (route_id, service_id, price, currency, valid_from, valid_to) VALUES
(1, 1, 45.00, 'EUR', '2024-01-01', '2024-12-31'),
(1, 2, 55.00, 'EUR', '2024-01-01', '2024-12-31'),
(1, 3, 75.00, 'EUR', '2024-01-01', '2024-12-31'),
(2, 1, 50.00, 'EUR', '2024-01-01', '2024-12-31'),
(2, 2, 60.00, 'EUR', '2024-01-01', '2024-12-31'),
(4, 1, 55.00, 'EUR', '2024-01-01', '2024-12-31'),
(4, 2, 65.00, 'EUR', '2024-01-01', '2024-12-31'),
(6, 1, 25.00, 'EUR', '2024-01-01', '2024-12-31'),
(6, 2, 35.00, 'EUR', '2024-01-01', '2024-12-31'),
(7, 2, 65.00, 'EUR', '2024-01-01', '2024-12-31'),
(8, 2, 85.00, 'EUR', '2024-01-01', '2024-12-31');

-- Insert sample partners
INSERT INTO partners (company_name, contact_person, email, phone, partner_type, commission_rate, status) VALUES
('Delta Hotels', 'Ahmet Yilmaz', 'reservations@deltahotels.com', '+902125178585', 'hotel', 10.00, 'active'),
('Selge Beach Resort', 'Mehmet Kaya', 'booking@selgebeach.com', '+902427536000', 'hotel', 12.00, 'active'),
('Istanbul Guide Service', 'Ayşe Demir', 'info@istanbulguides.com', '+905551234567', 'guide', 15.00, 'active'),
('Cappadocia Tours', 'Mustafa Eren', 'tours@cappadocia.com', '+905559876543', 'agency', 8.00, 'active');

-- Sample city tour voucher
INSERT INTO city_tour_vouchers (voucher_no, company_name, customer_phone, hotel_name, adult, child, infant) VALUES
('SAMPLE001', 'Travel Partner', '+901234567890', 'Delta Hotel Laleli', 2, 1, 0);

SET @voucher_id = LAST_INSERT_ID();

INSERT INTO city_tour_voucher_customers (voucher_id, customer_name) VALUES
(@voucher_id, 'John Doe'),
(@voucher_id, 'Jane Doe'),
(@voucher_id, 'Jimmy Doe');

INSERT INTO city_tour_voucher_tours (voucher_id, tour_name, tour_date, duration) VALUES
(@voucher_id, 'Istanbul City Tour', '2024-06-15', '8 hours'),
(@voucher_id, 'Bosphorus Cruise', '2024-06-16', '3 hours');

-- =====================================================
-- VIEWS FOR REPORTING
-- =====================================================

-- View for hotel booking summary
CREATE OR REPLACE VIEW v_hotel_booking_summary AS
SELECT 
    h.voucher_no,
    h.customer_name,
    h.hotel_name,
    h.room_type,
    h.check_in,
    h.check_out,
    DATEDIFF(h.check_out, h.check_in) AS nights,
    h.adults,
    h.children,
    h.total_price,
    h.currency,
    h.status,
    h.created_at
FROM h_vouchers h
ORDER BY h.created_at DESC;

-- View for transfer booking summary
CREATE OR REPLACE VIEW v_transfer_booking_summary AS
SELECT 
    t.voucher_no,
    t.customer_name,
    t.pickup_location,
    t.dropoff_location,
    t.pickup_date,
    t.pickup_time,
    t.transfer_type,
    t.vehicle_type,
    t.total_pax,
    t.total_price,
    t.currency,
    t.status,
    t.created_at
FROM transfer_vouchers t
ORDER BY t.pickup_date DESC;

-- View for daily revenue
CREATE OR REPLACE VIEW v_daily_revenue AS
SELECT 
    DATE(r.created_at) AS revenue_date,
    COUNT(r.id) AS total_receipts,
    SUM(r.total_amount) AS total_revenue,
    r.currency
FROM receipts r
WHERE r.payment_status IN ('paid', 'partial')
GROUP BY DATE(r.created_at), r.currency
ORDER BY revenue_date DESC;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
