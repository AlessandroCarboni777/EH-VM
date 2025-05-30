<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$username = $_SESSION['username'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? 0;
$role     = $_SESSION['role'] ?? null;
$token    = $_SESSION['token'] ?? null;

// Se l'utente non è loggato, redirect al login/logout
if (!$username) {
    header("Location: ./log/g4t3.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modern Shop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
        :root {
            --primary: #3a86ff;
            --secondary: #8338ec;
            --accent: #ff006e;
            --light: #ffffff;
            --dark: #1a1a1a;
            --gray: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--gray);
            color: var(--dark);
            line-height: 1.6;
        }
        
        header {
            background-color: var(--light);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .auth-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-outline {
            border: 2px solid var(--primary);
            background: transparent;
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: 2px solid var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: transparent;
            color: var(--primary);
        }
        
        /* Cart Styles */
        .cart-icon-wrapper {
            position: relative;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .cart-icon {
            font-size: 24px;
            color: var(--dark);
            transition: color 0.3s;
        }
        
        .cart-icon:hover {
            color: var(--primary);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Cart Popup */
        .cart-popup {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background-color: white;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: right 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }
        
        .cart-popup.active {
            right: 0;
        }
        
        .cart-popup-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-popup-header h2 {
            margin: 0;
            font-size: 20px;
        }
        
        .cart-popup-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
        
        .cart-popup-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .cart-popup-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .cart-popup-footer .total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .cart-popup-footer .btn {
            display: block;
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .cart-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            position: relative;
        }
        
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: var(--primary);
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
        }
        
        .cart-item-quantity button {
            width: 25px;
            height: 25px;
            background-color: var(--gray);
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .cart-item-quantity span {
            padding: 0 10px;
        }
        
        .cart-item-remove {
            position: absolute;
            top: 10px;
            right: 0;
            background: none;
            border: none;
            color: var(--accent);
            cursor: pointer;
        }
        
        .cart-empty {
            text-align: center;
            padding: 30px 0;
        }
        
        .cart-empty i {
            font-size: 40px;
            color: #ccc;
            margin-bottom: 15px;
            display: block;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .overlay.active {
            display: block;
        }
        
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 40px;
        }
        
        .features {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 32px;
            color: var(--dark);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: var(--gray);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .cta {
            background-color: var(--dark);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        .cta p {
            max-width: 600px;
            margin: 0 auto 30px;
            font-size: 18px;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column a {
            color: #b3b3b3;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #333;
            color: #b3b3b3;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            color: #b3b3b3;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: white;
        }
        
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 20px;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .cart-popup {
                width: 85%;
            }
        }
        .profile-menu {
            position: relative;
            display: inline-block;
            margin-left: 20px;
        }
        
        .profile-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 42px;
            background-color: white;
            min-width: 120px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 6px;
            overflow: hidden;
            z-index: 1000;
        }
        
        .dropdown-menu a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }
        
        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }
        
        </style>
</head>
<body>
 <header>
    <div class="container">
      <nav>
        <div class="logo">ModernShop</div>
        <div class="nav-links">
          <a href="home.php">Home</a>
          <a href="catalog.php">Catalog</a>
          <a href="about.html">About</a>
          <a href="contact.html">Contact</a>
          <?php if ($is_admin == 1): ?>
            <a href="modify_catalog.php">Modify Catalog</a>
          <?php endif; ?>
        </div>
        <div class="auth-buttons">
          <div class="cart-icon-wrapper" id="cart-icon">
            <i class="fas fa-shopping-cart cart-icon"></i>
            <span class="cart-count" id="cart-count">0</span>
          </div>
        </div>
        <div class="profile-menu">
            <img src="/img/profile.png" alt="Profile" class="profile-icon" onclick="toggleDropdown()" />
            <div class="dropdown-menu" id="profileDropdown">
                <?php if ($username): ?>
                <span style="padding: 8px; display: block;">Ciao, <?= htmlspecialchars($username) ?></span>
                <a href="./log/gi0b3rt.php">Settings</a>
                <a href="./log/g4t3.html">Logout</a>
                <?php else: ?>
                <a href="t1r3gi.html">Registrati</a>
                <a href="login.html">Accedi</a>
                <?php endif; ?>
            </div>
        </div>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="container">
      <h1>Discover Our Amazing Products</h1>
      <p>Browse through our extensive catalog of high-quality products at competitive prices. Your satisfaction is our priority.</p>
      <a href="catalog.php" class="btn btn-primary">Browse Catalog</a>
    </div>
  </section>

  <section class="features">
    <div class="container">
      <h2 class="section-title">Why Choose Us</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-truck"></i>
          </div>
          <h3>Fast Delivery</h3>
          <p>We provide quick and reliable shipping options to ensure your products reach you on time.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <h3>Secure Payments</h3>
          <p>Your payment information is always safe with our secure payment processing system.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-headset"></i>
          </div>
          <h3>24/7 Support</h3>
          <p>Our customer support team is always available to help you with any questions or concerns.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="cta">
    <div class="container">
      <h2>Join Our Community Today</h2>
      <p>Sign up now to receive exclusive deals, updates on new arrivals, and special promotions directly to your inbox.</p>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-column">
          <h3>ModernShop</h3>
          <p>Your one-stop destination for all your shopping needs with the best prices and quality.</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="footer-column">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="catalog.php">Catalog</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Customer Service</h3>
          <ul>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Shipping Policy</a></li>
            <li><a href="#">Return Policy</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Contact Us</h3>
          <ul>
            <li><i class="fas fa-map-marker-alt"></i> 123 Main Street, City</li>
            <li><i class="fas fa-phone"></i> (123) 456-7890</li>
            <li><i class="fas fa-envelope"></i> support@modernshop.com</li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2025-05-07 ModernShop. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Cart Popup -->
  <div class="overlay" id="overlay"></div>
  <div class="cart-popup" id="cart-popup">
    <div class="cart-popup-header">
      <h2>Your Cart</h2>
      <button class="cart-popup-close" id="cart-popup-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="cart-popup-body" id="cart-popup-body"></div>
    <div class="cart-popup-footer">
      <div class="total">
        <span>Total:</span>
        <span id="cart-total">$0.00</span>
      </div>
      <a href="cart.html" class="btn btn-primary">View Cart</a>
      <button class="btn btn-outline" id="checkout-btn">Checkout</button>
    </div>
  </div>

  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.getElementById('cart-icon');
            const cartPopup = document.getElementById('cart-popup');
            const overlay = document.getElementById('overlay');
            const closeCartBtn = document.getElementById('cart-popup-close');
            const cartPopupBody = document.getElementById('cart-popup-body');
            const cartCountEl = document.getElementById('cart-count');
            const cartTotalEl = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            function updateCartCount() {
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                cartCountEl.textContent = totalItems;
            }

            function calculateCartTotal() {
                const total = cart.reduce((total, item) => total + (parseFloat(item.price) * item.quantity), 0);
                cartTotalEl.textContent = $${total.toFixed(2)};
            }

            function renderCartItems() {
                if (cart.length === 0) {
                    cartPopupBody.innerHTML = 
                        <div class="cart-empty">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Your cart is empty</p>
                        </div>;
                    return;
                }

                let cartHTML = '';
                cart.forEach((item, index) => {
                    cartHTML += 
                        <div class="cart-item">
                            <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                            <div class="cart-item-info">
                                <div class="cart-item-title">${item.name}</div>
                                <div class="cart-item-price">$${parseFloat(item.price).toFixed(2)}</div>
                                <div class="cart-item-quantity">
                                    <button class="decrease-quantity" data-index="${index}">-</button>
                                    <span>${item.quantity}</span>
                                    <button class="increase-quantity" data-index="${index}">+</button>
                                </div>
                            </div>
                            <button class="cart-item-remove" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>;
                });
                cartPopupBody.innerHTML = cartHTML;

                document.querySelectorAll('.increase-quantity').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        cart[index].quantity++;
                        saveCart();
                        renderCartItems();
                        updateCartCount();
                        calculateCartTotal();
                    });
                });

                document.querySelectorAll('.decrease-quantity').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        if (cart[index].quantity > 1) {
                            cart[index].quantity--;
                        } else {
                            cart.splice(index, 1);
                        }
                        saveCart();
                        renderCartItems();
                        updateCartCount();
                        calculateCartTotal();
                    });
                });

                document.querySelectorAll('.cart-item-remove').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        cart.splice(index, 1);
                        saveCart();
                        renderCartItems();
                        updateCartCount();
                        calculateCartTotal();
                    });
                });
            }

            function saveCart() {
                localStorage.setItem('cart', JSON.stringify(cart));
            }

            cartIcon.addEventListener('click', () => {
                cartPopup.classList.add('active');
                overlay.classList.add('active');
            });

            closeCartBtn.addEventListener('click', () => {
                cartPopup.classList.remove('active');
                overlay.classList.remove('active');
            });

            overlay.addEventListener('click', () => {
                cartPopup.classList.remove('active');
                overlay.classList.remove('active');
            });

            checkoutBtn.addEventListener('click', () => {
                if (cart.length === 0) {
                    alert('Your cart is empty!');
                } else {
                    alert('Proceeding to checkout...');
                }
            });

            updateCartCount();
            calculateCartTotal();
            renderCartItems();
        });
    </script>

    <script>
    function toggleDropdown() {
      const menu = document.getElementById('profileDropdown');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    window.addEventListener('click', function(e) {
      const profileMenu = document.querySelector('.profile-menu');
      if (!profileMenu.contains(e.target)) {
        document.getElementById('profileDropdown').style.display = 'none';
      }
    });
  </script>
</body>
</html>