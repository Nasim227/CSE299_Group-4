<?php

include("connection.php");

function getDistinctBrands($conn, $table) {
    $brands = [];
    
    $check_column = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE 'Company_name'");
    
    if ($check_column && mysqli_num_rows($check_column) > 0) {
        $result = mysqli_query($conn, "SELECT DISTINCT Company_name FROM $table WHERE Company_name IS NOT NULL AND Company_name != ''");
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (!empty(trim($row['Company_name']))) {
                    $brands[] = trim($row['Company_name']);
                }
            }
        }
    }
    
    return $brands;
}

$bike_brands = getDistinctBrands($conn, 'bike');
$scooter_brands = getDistinctBrands($conn, 'scooter');
$oil_brands = getDistinctBrands($conn, 'engine_oil');
$helmet_brands = getDistinctBrands($conn, 'helmet');

$all_brands = array_unique(array_merge($bike_brands, $scooter_brands, $oil_brands, $helmet_brands));
$all_brands = array_filter($all_brands);
sort($all_brands);

$actionTarget = 'shwprdcts.php'; 
$currentPage = $currentPage ?? 'products';

if (isset($currentPage)) {
    if ($currentPage === 'search') {
        $actionTarget = 'search.php';
    } elseif ($currentPage === 'products') {
        $actionTarget = 'shwprdcts.php';
    }
}

$searchQuery = $_GET['query'] ?? '';

$selectedCategories = [];
if (isset($_GET['category'])) {
    if (is_array($_GET['category'])) {
        $selectedCategories = $_GET['category'];
    } else {
        $selectedCategories = [$_GET['category']];
    }
    
    $selectedCategories = array_map('trim', $selectedCategories);
    $selectedCategories = array_filter($selectedCategories, function($cat) {
        return !empty($cat) && $cat !== '';
    });
}

$selectedBrands = [];
if (isset($_GET['brands'])) {
    if (is_array($_GET['brands'])) {
        $selectedBrands = $_GET['brands'];
    } else {
        $selectedBrands = [$_GET['brands']];
    }
    
    $selectedBrands = array_map('trim', $selectedBrands);
    $selectedBrands = array_filter($selectedBrands, function($brand) {
        return !empty($brand) && $brand !== '';
    });
}

if (isset($_GET['brand']) && !empty($_GET['brand']) && empty($selectedBrands)) {
    $selectedBrands = [$_GET['brand']];
}
?>

<link rel="stylesheet" href="../css/siddebar.css">

<form id="filterForm" action="<?= $actionTarget ?>" method="GET" class="sidebar">
    <?php if (!empty($searchQuery)): ?>
        <input type="hidden" name="query" value="<?= htmlspecialchars($searchQuery) ?>">
    <?php endif; ?>
    
    <h3>Filter Products</h3>

    <div class="filter-section">
        <p><strong>Category:</strong></p>
        <?php
        $categories = [
            'Bikes' => 'Bikes',
            'Scooters' => 'Scooters', 
            'Engine_oil' => 'Engine Oil',
            'Helmet' => 'Helmets'
        ];
        
        $allSelected = empty($selectedCategories) || in_array('All', $selectedCategories);
        
        foreach ($categories as $value => $label) {
            $checked = (!$allSelected && in_array($value, $selectedCategories)) ? 'checked' : '';
            echo "<label style='display: block; margin-bottom: 5px;'>";
            echo "<input type='checkbox' name='category[]' value='$value' onchange='handleCategoryChange(this)' $checked> $label";
            echo "</label>";
        }
        
        $allChecked = $allSelected ? 'checked' : '';
        echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>";
        echo "<input type='checkbox' name='category[]' value='All' onchange='handleCategoryChange(this)' $allChecked> All Categories";
        echo "</label>";
        ?>
    </div>

    <div class="filter-section">
        <p><strong>Brands:</strong></p>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
            <?php if (empty($all_brands)): ?>
                <p style="color: #666; font-style: italic;">No brands available</p>
            <?php else: ?>
                <?php foreach ($all_brands as $brand): ?>
                    <label style="display: block; margin-bottom: 3px;">
                        <input type="checkbox" name="brands[]" value="<?= htmlspecialchars($brand) ?>"
                               onchange="autoSubmit()"
                               <?= in_array($brand, $selectedBrands) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($brand) ?>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="filter-section">
        <p><strong>Price Range:</strong></p>
        <div style="display: flex; gap: 10px; align-items: center;">
            <label>
                Min: ৳
                <input type="number" name="min_price" min="0" step="100" 
                       value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" 
                       onchange="autoSubmit()" 
                       style="width: 80px; padding: 5px;">
            </label>
            <span>-</span>
            <label>
                Max: ৳
                <input type="number" name="max_price" min="0" step="100" 
                       value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" 
                       onchange="autoSubmit()" 
                       style="width: 80px; padding: 5px;">
            </label>
        </div>
    </div>

    <div class="filter-section">
        <button type="button" 
                id="clearFiltersBtn"
                onclick="clearFilters()" 
                class="clear-filters-btn">
            Clear All Filters
        </button>
    </div>
</form>

<script>
    let debugMode = false;

    function log(message, data = null) {
        if (debugMode) {
            console.log('[Filter Debug]', message, data || '');
        }
    }

    function autoSubmit() {
        log('Auto submit triggered');
        setTimeout(() => {
            const form = document.getElementById("filterForm");
            if (form) {
                log('Submitting form');
                form.submit();
            } else {
                log('Form not found for auto submit');
            }
        }, 100);
    }

    function handleCategoryChange(checkbox) {
        log('Category change triggered', checkbox.value);
        
        const form = document.getElementById("filterForm");
        if (!form) {
            log('Form not found in handleCategoryChange');
            return;
        }
        
        const categoryCheckboxes = form.querySelectorAll('input[name="category[]"]');
        const allCheckbox = form.querySelector('input[name="category[]"][value="All"]');
        
        if (checkbox.value === 'All') {
            if (checkbox.checked) {
                log('All category selected, unchecking others');
                categoryCheckboxes.forEach(cb => {
                    if (cb.value !== 'All') {
                        cb.checked = false;
                    }
                });
            }
        } else {
            if (checkbox.checked && allCheckbox) {
                log('Specific category selected, unchecking All');
                allCheckbox.checked = false;
            }
            
            const specificCategoriesChecked = Array.from(categoryCheckboxes).some(cb => 
                cb.value !== 'All' && cb.checked
            );
            if (!specificCategoriesChecked && allCheckbox) {
                log('No specific categories selected, checking All');
                allCheckbox.checked = true;
            }
        }
        
        autoSubmit();
    }

    function clearFilters() {
        log('Clear filters function called');
        
        try {
            const form = document.getElementById("filterForm");
            
            if (!form) {
                log('ERROR: Filter form not found');
                clearFiltersViaURL();
                return;
            }
            
            log('Form found, proceeding with clear');
            
            const searchQuery = form.querySelector('input[name="query"]');
            const searchValue = searchQuery ? searchQuery.value : '';
            log('Search value to preserve:', searchValue);
            
            const clearBtn = document.getElementById('clearFiltersBtn');
            if (clearBtn) {
                clearBtn.disabled = true;
                clearBtn.textContent = 'Clearing...';
            }
            const categoryCheckboxes = form.querySelectorAll('input[name="category[]"]');
            log('Found category checkboxes:', categoryCheckboxes.length);
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            const brandCheckboxes = form.querySelectorAll('input[name="brands[]"]');
            log('Found brand checkboxes:', brandCheckboxes.length);
            brandCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            const minPriceInput = form.querySelector('input[name="min_price"]');
            const maxPriceInput = form.querySelector('input[name="max_price"]');
            
            if (minPriceInput) {
                minPriceInput.value = '';
                log('Cleared min price');
            }
            if (maxPriceInput) {
                maxPriceInput.value = '';
                log('Cleared max price');
            }
            
            if (searchValue && searchQuery) {
                searchQuery.value = searchValue;
                log('Restored search query');
            }
            
            const allCategoryCheckbox = form.querySelector('input[name="category[]"][value="All"]');
            if (allCategoryCheckbox) {
                allCategoryCheckbox.checked = true;
                log('Checked All category');
            }
            
            log('Submitting cleared form');
            form.submit();
            
        } catch (error) {
            log('ERROR in clearFilters:', error);
            console.error("Error clearing filters:", error);
            
            const clearBtn = document.getElementById('clearFiltersBtn');
            if (clearBtn) {
                clearBtn.disabled = false;
                clearBtn.textContent = 'Clear All Filters';
            }
            
            clearFiltersViaURL();
        }
    }

    function clearFiltersViaURL() {
        log('Using URL-based clear as fallback');
        
        try {
            const searchQuery = new URLSearchParams(window.location.search).get('query');
            const baseUrl = window.location.pathname;
            
            if (searchQuery) {
                const newUrl = baseUrl + '?query=' + encodeURIComponent(searchQuery);
                log('Redirecting to:', newUrl);
                window.location.href = newUrl;
            } else {
                log('Redirecting to base URL:', baseUrl);
                window.location.href = baseUrl;
            }
        } catch (error) {
            log('ERROR in URL-based clear:', error);
            console.error("Error in URL-based clear:", error);
            window.location.reload();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        log('DOM Content Loaded - Initializing form');
        
        const form = document.getElementById("filterForm");
        if (!form) {
            log('ERROR: Form not found on DOMContentLoaded');
            return;
        }
        
        const categoryCheckboxes = form.querySelectorAll('input[name="category[]"]');
        const allCheckbox = form.querySelector('input[name="category[]"][value="All"]');
        
        log('Initializing with checkboxes:', categoryCheckboxes.length);
        
        const anyCategorySelected = Array.from(categoryCheckboxes).some(cb => cb.checked);
        if (!anyCategorySelected && allCheckbox) {
            allCheckbox.checked = true;
            log('No categories selected, checked All by default');
        }
        
        log('Form initialization complete');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById("filterForm");
        if (form) {
            form.addEventListener('submit', function(e) {
                log('Form submission detected');
                
                const clearBtn = document.getElementById('clearFiltersBtn');
                if (clearBtn) {
                    clearBtn.disabled = true;
                    clearBtn.textContent = 'Loading...';
                }
            });
        }
    });

    if (window.location.search.includes('debug=1')) {
        debugMode = true;
        console.log('Filter debug mode enabled');
    }
</script>


