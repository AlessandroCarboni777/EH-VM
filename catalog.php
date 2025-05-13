<?php
// Database credentials
$host = 'localhost'; // Change if your DB is on another host
$db   = 'ModernShop2';
$user = 'modernadmin';      // Change to your DB user
$pass = 'NuN728Xx2nCH';          // Change to your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Vulnerable search logic
    $search = '';
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        // Vulnerable to SQL injection!
        $sql = "SELECT id, name, description, price FROM products WHERE name LIKE '%$search%' ORDER BY created_at DESC";
    } else {
        $sql = 'SELECT id, name, description, price FROM products ORDER BY created_at DESC';
    }
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog - Modern Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3a86ff;
            --secondary: #8338ec;
            --accent: #ff006e;
            --light: #ffffff;
            --dark: #1a1a1a;
            --gray: #f8f9fa;
            --border: #e9ecef;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--gray); color: var(--dark); line-height: 1.6; }
        header { background-color: var(--light); box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); position: sticky; top: 0; z-index: 100; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 24px; font-weight: bold; color: var(--primary); }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { text-decoration: none; color: var(--dark); font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); font-weight: 600; }
        .catalog-header { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 40px 0; text-align: center; }
        .catalog-header h1 { font-size: 36px; margin-bottom: 15px; }
        .catalog-header p { font-size: 18px; max-width: 600px; margin: 0 auto; }
        .catalog-content { padding: 50px 0; }
        .search-bar { margin-bottom: 30px; text-align: center; }
        .search-bar input[type="text"] {
            padding: 10px 15px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 16px;
            width: 250px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .search-bar button:hover {
            background: var(--secondary);
        }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; }
        .product-card { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); transition: transform 0.3s, box-shadow 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        .product-image { width: 100%; max-width: 300px; height: 200px; object-fit: cover; display: block; margin: 0 auto 1rem; background: #f0f0f0; }
        .product-info { padding: 15px; }
        .product-info h3 { font-size: 18px; margin-bottom: 10px; }
        .product-info p.description { color: #666; font-size: 14px; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; }
        .product-info .price { font-weight: bold; font-size: 18px; color: var(--primary); margin-bottom: 15px; }
        .product-info .actions { display: flex; justify-content: space-between; }
        .btn { padding: 8px 15px; font-size: 14px; border-radius: 5px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; border: 2px solid var(--primary); background: transparent; color: var(--primary); }
        .btn:hover { background-color: var(--primary); color: white; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: transparent; color: var(--primary); }
        .empty-state { text-align: center; padding: 50px 0; }
        .empty-state i { font-size: 60px; color: #ccc; margin-bottom: 20px; }
        .empty-state h3 { font-size: 24px; margin-bottom: 15px; }
        .empty-state p { max-width: 500px; margin: 0 auto 20px; color: #666; }
        footer { background-color: var(--dark); color: white; padding: 50px 0 20px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .footer-column h3 { font-size: 18px; margin-bottom: 20px; font-weight: 600; }
        .footer-column ul { list-style: none; }
        .footer-column ul li { margin-bottom: 10px; }
        .footer-column a { color: #b3b3b3; text-decoration: none; transition: color 0.3s; }
        .footer-column a:hover { color: white; }
        .copyright { text-align: center; padding-top: 20px; border-top: 1px solid #333; color: #b3b3b3; }
        .social-links { display: flex; gap: 15px; margin-top: 15px; }
        .social-links a { color: #b3b3b3; font-size: 18px; transition: color 0.3s; }
        .social-links a:hover { color: white; }
        @media (max-width: 768px) {
            nav { flex-direction: column; gap: 20px; }
            .nav-links { flex-direction: column; align-items: center; gap: 15px; }
            .catalog-header h1 { font-size: 28px; }
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
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
                    <a href="catalog.php" class="active">Catalog</a>
                    <a href="about.html">About</a>
                    <a href="contact.html">Contact</a>
                </div>
            </nav>
        </div>
    </header>
    <section class="catalog-header">
        <div class="container">
            <h1>Product Catalog</h1>
            <p>Browse our wide selection of high-quality products</p>
        </div>
    </section>
    <section class="catalog-content">
        <div class="container">
            <form class="search-bar" method="get" action="catalog.php">
                <input type="text" name="search" placeholder="Search product name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
            <?php if (isset($error)): ?>
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Database Error</h3>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php elseif (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Products Found</h3>
                    <p>There are currently no products in the catalog.</p>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-info">
                                <img src="img/placeholder.png" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                                <div class="actions">
                                    <a href="#" class="btn">View Details</a>
                                    <button class="btn btn-primary" disabled>Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                <p>&copy; 2023 ModernShop. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>