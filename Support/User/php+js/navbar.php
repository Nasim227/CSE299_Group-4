<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    include("connection.php");

    $bike_brands = [];
    $scooter_brands = [];

    $bike_result = mysqli_query($conn, "SELECT DISTINCT Company_name FROM bike");
    if ($bike_result) {
        while ($row = mysqli_fetch_assoc($bike_result)) {
            $bike_brands[] = $row['Company_name'];
        }
    }

    $scooter_result = mysqli_query($conn, "SELECT DISTINCT Company_name FROM scooter");
    if ($scooter_result) {
        while ($row = mysqli_fetch_assoc($scooter_result)) {
            $scooter_brands[] = $row['Company_name'];
        }
    }
?>


<link rel="stylesheet" href="../css/navbar.css">
<script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>

<nav class="navbar">
    <ul>
        <li><a href="index.php"><img src="Images/logo2.png"></a></li>
        <li><a href="index.php" class="tt">Home</a></li>

        <li class="dropdown">
            <a href="#" class="tt">Bikes <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="shwbrnds.php?category=Bikes">All Brands</a>
                <a href="shwprdcts.php?category=Bikes">All Bikes</a>
                <?php foreach ($bike_brands as $brand): ?>
                    <a href="shwprdcts.php?category=Bikes&brand=<?= urlencode($brand) ?>">
                        <?= htmlspecialchars($brand) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </li>

        <li class="dropdown">
            <a href="#" class="tt">Scooters <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="shwbrnds.php?category=Scooters">All Brands</a>
                <a href="shwprdcts.php?category=Scooters">All Scooters</a>
                <?php foreach ($scooter_brands as $brand): ?>
                    <a href="shwprdcts.php?category=Scooters&brand=<?= urlencode($brand) ?>">
                        <?= htmlspecialchars($brand) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </li>

        <li><a href="shwprdcts.php?category=Accessories" class="tt">Accessories</a></li>

        <li class="search" >
            <a href="#" class="tt">Search <i class="fa fa-search"></i></a>
                <form action="search.php" method="get">
                    <input type="text" name="query" placeholder="Search..." required >
                    <button type="submit">Go</button>
                </form>
        </li>

        <li><a href="#" class="tt">Compare</a></li>
        <li><a href="aboutus.php" class="tt">About us</a></li>

        <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
            <li><a href="more.php" class="tt">Management</a></li>
        <?php endif; ?>

        <li class="spacer"></li>

        <?php if (isset($_SESSION["username"])): ?>
            <li><a href="#" class="sl"><?= $_SESSION["username"] ?>' Profile</a></li>
            <li><a href="logout.php" class="sl">Logout</a></li>
        <?php else: ?>
            <li><a href="signup.php" class="sl">Sign UP</a></li>
            <li><a href="login.php" class="sl">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>


<script>
// Navbar JavaScript for Click-based Dropdowns and Search
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle dropdown clicks for mobile and desktop
    const dropdowns = document.querySelectorAll('.dropdown');
    const searchElement = document.querySelector('.search');
    
    // Dropdown functionality
    dropdowns.forEach(dropdown => {
        const dropdownLink = dropdown.querySelector('a.tt');
        
        dropdownLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close other dropdowns
            dropdowns.forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    otherDropdown.classList.remove('active');
                }
            });
            
            // Close search if open
            searchElement.classList.remove('active');
            
            // Toggle current dropdown
            dropdown.classList.toggle('active');
        });
    });
    
    // Search functionality
    if (searchElement) {
        const searchLink = searchElement.querySelector('a.tt');
        const searchForm = searchElement.querySelector('form');
        const searchInput = searchElement.querySelector('input[type="text"]');
        
        searchLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close all dropdowns
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            
            // Toggle search
            searchElement.classList.toggle('active');
            
            // Focus on input when opened
            if (searchElement.classList.contains('active')) {
                setTimeout(() => {
                    searchInput.focus();
                }, 100);
            }
        });
        
        // Keep search open while typing
        searchInput.addEventListener('focus', function() {
            searchElement.classList.add('active');
        });
        
        // Don't close search while user is typing
        searchInput.addEventListener('input', function() {
            searchElement.classList.add('active');
        });
    }
    
    // Close dropdowns and search when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && !e.target.closest('.search')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            if (searchElement) {
                searchElement.classList.remove('active');
            }
        }
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            if (searchElement) {
                searchElement.classList.remove('active');
            }
        }
    });
    
    // Improve hover behavior for desktop
    if (window.innerWidth > 768) {
        dropdowns.forEach(dropdown => {
            let hoverTimeout;
            
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                dropdown.classList.add('active');
            });
            
            dropdown.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    dropdown.classList.remove('active');
                }, 500); // 500ms delay before closing
            });
        });
    }
});
</script>