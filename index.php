<?php
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Stats Queries
$book_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM books"));
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
$res_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reservations"));

// Search Logic
$search_results = [];
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $search_results = mysqli_query($conn, "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%' LIMIT 6");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- හිස් ගතිය නැති කර Image එක ලොකු කිරීමට එක් කල CSS -->
    <style>
        /* මුළු පිටුවේම පළල වැඩි කර දෙපැත්තේ ඉඩ අඩු කිරීම */
        .custom-container {
            max-width: 95% !important;
            width: 100%;
            margin: 0 auto;
            padding: 0 30px;
        }

        /* Hero Section එක ඇතුලත Layout එක සකස් කිරීම */
        .hero-container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 50px !important; /* කොටස් දෙක මැද අනවශ්‍ය ඉඩ නැති කිරීම */
            padding: 60px 0 !important;
        }

        /* වම් පැත්තේ Text කොටස */
        .hero-left {
            flex: 1.2 !important; /* අකුරු තියෙන කොටසට ප්‍රමාණවත් ඉඩක් දීම */
            max-width: 650px !important;
        }

        /* දකුණු පැත්තේ Image කොටස */
        .hero-right {
            flex: 1 !important; 
            display: flex !important;
            justify-content: flex-end !important;
            width: 100% !important;
        }

        /* Image Wrapper එක සහ රූපය විශාල කිරීම */
        .hero-image-wrapper {
            width: 100% !important;
            max-width: 600px !important; /* Image එක කලින්ට වඩා ගොඩක් ලොකු කර ඇත */
            height: auto !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
        }

        .main-hero-img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }

        /* Responsive - Phone වලදී පෙනෙන ආකාරය හැඩගැස්වීම */
        @media (max-width: 992px) {
            .hero-container {
                flex-direction: column !important;
                text-align: center !important;
                gap: 40px !important;
            }
            .hero-right {
                justify-content: center !important;
            }
            .hero-left {
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

<header class="main-header animate-fade-down">
    <div class="custom-container header-container">
        <div class="logo">
            <div class="logo-icon"><i class="fa-solid fa-book-bookmark"></i></div>
            <span>Library<span class="brand-accent">MS</span></span>
        </div>
        <nav class="navigation">
            <a href="#" class="nav-link active">Home</a>
            <a href="#search-section" class="nav-link">Find Books</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="#about" class="nav-link">About</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>
        <div class="nav-action">
            <a href="login.html" class="portal-btn">Login <i class="fa-solid fa-arrow-right-to-bracket"></i></a>
        </div>
    </div>
</header>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="custom-container hero-container">
        <div class="hero-left animate-fade-in">
            <div class="sub-tag"><span class="pulse-dot"></span> Smart Library Management</div>
            <h1>Explore Knowledge <br><span class="highlight">Without Limits</span></h1>
            <p class="hero-desc">
                Access thousands of books, reserve online instantly, borrow seamlessly, and monitor your entire academic learning path using our integrated management matrix.
            </p>
            <div class="hero-action">
                <a href="#search-section" class="cta-btn">Search Catalogue <i class="fa-solid fa-magnifying-glass"></i></a>
                <a href="login.html" class="secondary-btn">Member Portal</a>
            </div>
        </div>
        <div class="hero-right animate-fade-in delay-1">
            <div class="hero-image-wrapper">
                <img src="library4.jpg" alt="Library Learning Space" class="main-hero-img">
            </div>
        </div>
    </div>
</section>

<!-- CATALOGUE SECTION -->
<section id="search-section" class="search-section animate-slide-up">
    <div class="custom-container">
        <div class="section-title">
            <span class="section-mini-tag">Discovery Engine</span>
            <h2>Online Book Catalogue</h2>
            <p>Query live book inventories across corporate database clusters</p>
        </div>

        <div class="search-container">
            <form action="#search-section" method="GET">
                <div class="search-input-group">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" placeholder="Type Book Title, Author or Catalog Index..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="search-submit-btn">Execute Search</button>
                </div>
                <?php if(!empty($search_query)): ?>
                    <a href="home.php#search-section" class="clear-search"><i class="fa-solid fa-xmark"></i> Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (isset($_GET['search'])): ?>
            <div class="results-wrapper animate-fade-in">
                <h3 class="results-heading">Search Logs for: "<span><?php echo htmlspecialchars($search_query); ?></span>"</h3>
                <?php if (mysqli_num_rows($search_results) > 0): ?>
                    <div class="books-grid">
                        <?php while($book = mysqli_fetch_assoc($search_results)): ?>
                            <div class="book-card">
                                <div class="book-status <?php echo ($book['status'] ?? 'Available') == 'Available' ? 'status-avail' : 'status-borrowed'; ?>">
                                    <?php echo $book['status'] ?? 'Available'; ?>
                                </div>
                                <div class="book-cover-placeholder"><i class="fa-solid fa-book-open"></i></div>
                                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                                <p class="book-author">By <?php echo htmlspecialchars($book['author']); ?></p>
                                <a href="login.html" class="reserve-sm-btn">Process Request</a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results-box">
                        <i class="fa-regular fa-folder-open info-msg-icon"></i>
                        <p>No books found matching your search query. Please try another keyword.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- COUNTER STATS BAR -->
<section class="stats-section">
    <div class="custom-container stats-wrapper">
        <div class="stat-box">
            <h2><?php echo $book_count['total'] ?? '0'; ?>+</h2>
            <p>Total Books Volume</p>
        </div>
        <div class="stat-box">
            <h2><?php echo $user_count['total'] ?? '0'; ?>+</h2>
            <p>Active Digital Users</p>
        </div>
        <div class="stat-box">
            <h2><?php echo $res_count['total'] ?? '0'; ?>+</h2>
            <p>Reservations Automated</p>
        </div>
        <div class="stat-box">
            <h2>24/7/365</h2>
            <p>System Availability</p>
        </div>
    </div>
</section>

<!-- CORE FUNCTIONALITIES GRID -->
<section class="features-section" id="features">
    <div class="custom-container">
        <div class="section-title">
            <span class="section-mini-tag">Architecture</span>
            <h2>System Control Modules</h2>
            <p>Operational components optimized for digital library coordination</p>
        </div>

        <div class="features-grid">
            <a href="login.html" class="feature-card animate-card-wave delay-card-1">
                <div class="f-icon-box"><i class="fa-solid fa-box-archive"></i></div>
                <h3>Book Management</h3>
                <p>Structure catalog indices, index categorization, and modify stocks instantly.</p>
                <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
            </a>
            <a href="login.html" class="feature-card animate-card-wave delay-card-2">
                <div class="f-icon-box"><i class="fa-solid fa-circle-check"></i></div>
                <h3>Reservation Module</h3>
                <p>Hold book queues through real-time asynchronous request handling setups.</p>
                <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
            </a>
            <a href="login.html" class="feature-card animate-card-wave delay-card-3">
                <div class="f-icon-box"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h3>Borrow & Return</h3>
                <p>Audit system checkouts, return timelines, and maintain library logs.</p>
                <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
            </a>
            <a href="login.html" class="feature-card animate-card-wave delay-card-4">
                <div class="f-icon-box"><i class="fa-solid fa-calculator"></i></div>
                <h3>Fine Assessment</h3>
                <p>Automated calculation matrices to evaluate overdue return fine configurations.</p>
                <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

<!-- DEPLOYMENT SPECIFICATION -->
<section class="about-section" id="about">
    <div class="custom-container">
        <div class="about-card">
            <span class="section-mini-tag">Overview</span>
            <h2>About The Application Framework</h2>
            <p>
                This modern web application framework serves to manage institutional catalogs, student transaction pipelines, live search matrices, and automated ledgers under safe encryption perimeters.
            </p>
        </div>
    </div>
</section>

<!-- INTEGRATED CONTACT FOOTER -->
<footer id="contact">
    <div class="custom-container">
        <div class="footer-grid">
            <div class="footer-info">
                <div class="logo footer-logo-style">
                    <div class="logo-icon"><i class="fa-solid fa-book-bookmark"></i></div>
                    <span>Library<span class="brand-accent">MS</span></span>
                </div>
                <p>Engineered to maximize institutional workflow capabilities via modern UI/UX design structures.</p>
            </div>
            <div class="footer-contacts">
                <h4>System Contact Desk</h4>
                <p><i class="fa-solid fa-envelope"></i> library@gmail.com</p>
                <p><i class="fa-solid fa-phone"></i> +94 70 123 2662</p>
                <p><i class="fa-solid fa-location-dot"></i> Colombo, Sri Lanka</p>
            </div>
        </div>
        <div class="footer-bar">
            <p>&copy; 2026 LibraryMS Framework Dashboard. All Operational Nodes Verified.</p>
        </div>
    </div>
</footer>

</body>
</html>