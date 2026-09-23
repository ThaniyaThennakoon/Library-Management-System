<?php
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Search Logic
$search_results = [];
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $search_results = mysqli_query($conn, "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%' ORDER BY title ASC LIMIT 5");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="homessss.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header class="main-header">
    <div class="custom-container header-container">
        <div class="logo">
            <div class="logo-icon"><i class="fa-solid fa-book-bookmark"></i></div>
            <span>Library<span class="brand-accent">MS</span></span>
        </div>
        <nav class="navigation">
            <a href="#" class="nav-link active">Home</a>
            <a href="#search-section" class="nav-link">Find Books</a>
            <a href="#features" class="nav-link">System Modules</a>
            <a href="#about" class="nav-link">About Framework</a>
            <a href="#contact" class="nav-link">Contact </a>
        </nav>
        <div style="width: 160px;"></div>
       
    </div>
</header>

<!-- HERO BANNER SECTION -->
<section class="hero-section">
    <div class="custom-container hero-container">
        <div class="hero-left">
            <div class="sub-tag"><span class="pulse-dot"></span> Smart Library Management Framework</div>
            <h1 class="hero-title">Explore Institutional Knowledge & Digital Resources <span class="highlight">Without Limits</span></h1>
            <p class="hero-desc">
                Seamlessly access thousands of educational books, track pending journals, reserve items online instantly, and monitor your entire institutional learning path using our fully integrated live database management dashboard infrastructure.
            </p>
            
            <div class="hero-action-buttons-group">
                <a href="#search-section" class="live-btn-browse">
                    <i class="fa-solid fa-magnifying-glass"></i> Browse Books Catalogue
                </a>
                <a href="login.html" class="live-btn-hero-login">
                    <i class="fa-solid fa-key"></i> Get Started
                </a>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-image-wrapper">
                <img src="library4.jpg" alt="Library Workspace Layout" class="main-hero-img">
            </div>
        </div>
    </div>
</section>

<!-- DASHBOARD BACKGROUND SECTION -->
<div class="dashboard-lower-container">


    <section id="search-section" class="search-section">
        <div class="custom-container">
            <div class="search-box-panel-compact">
                <div class="section-title-left-clean">
                    <span class="section-mini-tag">Search Engine</span>
                    <h2>Online Book Catalogue Discovery</h2>
                    <p>Query book inventories across  our digital collection</p>
                </div>

                <div class="search-container-clean">
                    <form action="#search-section" method="GET">
                        <div class="search-input-group">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="search" placeholder="Type Book Title, Author Name or Catalog Index..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                            <button type="submit" class="search-submit-btn">Search</button>
                        </div>
                        <?php if(!empty($search_query)): ?>
                            <a href="home.php#search-section" class="clear-search"><i class="fa-solid fa-xmark"></i> Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['search'])): ?>
                <div class="results-wrapper">
                    <h3 class="results-heading">Search Records for: "<span><?php echo htmlspecialchars($search_query); ?></span>"</h3>
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

    <!-- SYSTEM CORE MODULES -->
    <section class="features-section" id="features">
        <div class="custom-container">
            <div class="section-title">
                <span class="section-mini-tag">Architecture</span>
                <h2>System Core Control Modules</h2>
                <p>Operational core components optimized for institutional digital orchestration</p>
            </div>

            <div class="features-grid">
                <a href="login.html" class="feature-card">
                    <div class="f-icon-box"><i class="fa-solid fa-box-archive"></i></div>
                    <h3>Book Management</h3>
                    <p>Structure catalog indices, index categorization, and modify library stocks instantly.</p>
                    <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="login.html" class="feature-card">
                    <div class="f-icon-box"><i class="fa-solid fa-circle-check"></i></div>
                    <h3>Reservation Module</h3>
                    <p>Hold book queues through real-time asynchronous request handling setups.</p>
                    <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="login.html" class="feature-card">
                    <div class="f-icon-box"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <h3>Borrow & Return</h3>
                    <p>Audit system checkouts, return timelines, and maintain automated transaction logs.</p>
                    <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="login.html" class="feature-card">
                    <div class="f-icon-box"><i class="fa-solid fa-calculator"></i></div>
                    <h3>Fine Assessment</h3>
                    <p>Automated calculation matrices to evaluate overdue return fine configurations.</p>
                    <span class="feature-link-text">Initialize Module <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about-section" id="about">
        <div class="custom-container">
            <div class="about-card-box">
                <span class="section-mini-tag">Deployment Overview</span>
                <h2>Institutional Application Architecture</h2>
                <p class="about-description-text">
                    This modern library framework handles <strong>data integrity</strong> over scalable database layers. It provides administrative matrices for <strong>book allocation workflows</strong>, instantaneous pattern exploration engines, student request queue coordination, and automated secure logging frameworks. All data clusters operate under secure transaction paradigms to prevent latency.
                </p>
            </div>
        </div>
    </section>

</div>

<!-- FOOTER SECTION -->
<footer id="contact">
    <div class="custom-container">
        <div class="footer-grid">
            <div class="footer-info">
                <div class="logo footer-logo-style">
                    <div class="logo-icon"><i class="fa-solid fa-book-bookmark"></i></div>
                    <span>Library<span class="brand-accent">MS</span></span>
                </div>
                <p class="footer-desc-text">Engineered to maximize institutional workflow capabilities via modern responsive data management interface structures.</p>
                <div class="footer-hours-box">
                    <h5><i class="fa-regular fa-clock"></i> Operational Hours</h5>
                    <p>24/7 Service</p>
                    
                </div>
            </div>
            
            <div class="footer-contacts">
                <h4>Central System Contact Desk</h4>
                <div class="contact-links-grid">
                    <p><i class="fa-solid fa-envelope"></i> <span>Primary Support:</span> library.@gmail.com</p>
                    <p><i class="fa-solid fa-phone"></i> <span>Hotline Desk:</span> +94 70 123 2662</p>
                    <p><i class="fa-solid fa-fax"></i> <span>Office Fax Node:</span> +94 11 245 7890</p>
                    <p><i class="fa-solid fa-location-dot"></i> <span>Campus Tower Block:</span> No 45, Dehiwala, Colombo 03, Sri Lanka</p>
                </div>
            </div>
        </div>
        <div class="footer-bar">
            <p>&copy; 2026 LibraryMS Framework Dashboard. All System Operational Nodes and Database Clusters Verified.</p>
        </div>
    </div>
</footer>

</body>
</html>