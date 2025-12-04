<?php
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Get date range filter (default: last 30 days)
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '30';
$days = (int)$date_range;

// User Growth Data (Last N days)
$user_growth_query = "SELECT 
    DATE(created_at) as date,
    COUNT(*) as count
FROM users
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
GROUP BY DATE(created_at)
ORDER BY date ASC";
$user_growth_stmt = $conn->prepare($user_growth_query);
$user_growth_stmt->bind_param("i", $days);
$user_growth_stmt->execute();
$user_growth_result = $user_growth_stmt->get_result();

$user_growth_labels = [];
$user_growth_data = [];
while ($row = $user_growth_result->fetch_assoc()) {
    $user_growth_labels[] = date('M d', strtotime($row['date']));
    $user_growth_data[] = (int)$row['count'];
}

// Rental Trends Data
$rental_trends_query = "SELECT 
    DATE(start_date) as date,
    COUNT(*) as count,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
FROM rentals
WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
GROUP BY DATE(start_date)
ORDER BY date ASC";
$rental_trends_stmt = $conn->prepare($rental_trends_query);
$rental_trends_stmt->bind_param("i", $days);
$rental_trends_stmt->execute();
$rental_trends_result = $rental_trends_stmt->get_result();

$rental_trends_labels = [];
$rental_trends_data = [];
$rental_completed_data = [];
while ($row = $rental_trends_result->fetch_assoc()) {
    $rental_trends_labels[] = date('M d', strtotime($row['date']));
    $rental_trends_data[] = (int)$row['count'];
    $rental_completed_data[] = (int)$row['completed'];
}

// Revenue Data (from completed rentals)
$revenue_query = "SELECT 
    DATE(r.start_date) as date,
    SUM(DATEDIFF(r.end_date, r.start_date) + 1) * b.rental_price_per_day as revenue
FROM rentals r
INNER JOIN books b ON r.book_id = b.id
WHERE r.status = 'completed' 
  AND r.start_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
GROUP BY DATE(r.start_date)
ORDER BY date ASC";
$revenue_stmt = $conn->prepare($revenue_query);
$revenue_stmt->bind_param("i", $days);
$revenue_stmt->execute();
$revenue_result = $revenue_stmt->get_result();

$revenue_labels = [];
$revenue_data = [];
while ($row = $revenue_result->fetch_assoc()) {
    $revenue_labels[] = date('M d', strtotime($row['date']));
    $revenue_data[] = (float)$row['revenue'];
}

// Rental Status Distribution
$rental_status_query = "SELECT 
    status,
    COUNT(*) as count
FROM rentals
GROUP BY status";
$rental_status_result = $conn->query($rental_status_query);

$rental_status_labels = [];
$rental_status_data = [];
$rental_status_colors = [
    'pending' => '#f59e0b',
    'approved' => '#10b981',
    'rejected' => '#ef4444',
    'completed' => '#3b82f6',
    'cancelled' => '#6b7280'
];
while ($row = $rental_status_result->fetch_assoc()) {
    $rental_status_labels[] = ucfirst($row['status']);
    $rental_status_data[] = (int)$row['count'];
}

// Book Type Distribution
$book_type_query = "SELECT 
    book_type,
    COUNT(*) as count
FROM books
GROUP BY book_type";
$book_type_result = $conn->query($book_type_query);

$book_type_labels = [];
$book_type_data = [];
$book_type_colors = ['#3b82f6', '#10b981'];
while ($row = $book_type_result->fetch_assoc()) {
    $book_type_labels[] = ucfirst($row['book_type']);
    $book_type_data[] = (int)$row['count'];
}

// Top Books (Most Rented)
$top_books_query = "SELECT 
    b.title,
    b.author,
    COUNT(r.id) as rental_count
FROM books b
LEFT JOIN rentals r ON b.id = r.book_id
GROUP BY b.id, b.title, b.author
ORDER BY rental_count DESC
LIMIT 10";
$top_books_result = $conn->query($top_books_query);

$top_books_labels = [];
$top_books_data = [];
while ($row = $top_books_result->fetch_assoc()) {
    $top_books_labels[] = substr($row['title'], 0, 20) . (strlen($row['title']) > 20 ? '...' : '');
    $top_books_data[] = (int)$row['rental_count'];
}

// User Activity by Hour (last 7 days)
$activity_query = "SELECT 
    HOUR(created_at) as hour,
    COUNT(*) as count
FROM users
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY HOUR(created_at)
ORDER BY hour ASC";
$activity_result = $conn->query($activity_query);

$activity_labels = [];
$activity_data = [];
for ($i = 0; $i < 24; $i++) {
    $activity_labels[] = sprintf('%02d:00', $i);
    $activity_data[$i] = 0;
}
while ($row = $activity_result->fetch_assoc()) {
    $activity_data[(int)$row['hour']] = (int)$row['count'];
}
$activity_data = array_values($activity_data);

// Overall Statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_books = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$total_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT 
    SUM(DATEDIFF(r.end_date, r.start_date) + 1) * b.rental_price_per_day as total
FROM rentals r
INNER JOIN books b ON r.book_id = b.id
WHERE r.status = 'completed'")->fetch_assoc()['total'] ?? 0;
$new_users_today = $conn->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$new_rentals_today = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE DATE(start_date) = CURDATE()")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics — BOOK HUB</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/book-hub/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">

  <!-- CSS Files -->
  <link rel="stylesheet" href="/book-hub/public/static/css/variables.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/base.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/admin.css">
  
  <style>
    /* Chart container styles */
    .card-content canvas {
      max-width: 100% !important;
      height: auto !important;
    }
    
    .card-content {
      min-height: 300px;
    }
    
    #topBooksChart {
      min-height: 400px;
    }
  </style>
</head>
<body>

<div class="admin-page">
  <?php require_once __DIR__ . '/../components/admin-sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <?php require_once __DIR__ . '/../components/admin-topbar.php'; ?>

    <!-- Content Area -->
    <div class="content-area">
      <div class="section-header">
        <h1>Analytics Dashboard</h1>
        <div class="header-actions">
          <form method="GET" style="display: inline-flex; gap: 0.5rem; align-items: center;">
            <select name="date_range" class="filter-select" onchange="this.form.submit()" style="height: 44px; padding: 0 1rem;">
              <option value="7" <?php echo $date_range === '7' ? 'selected' : ''; ?>>Last 7 days</option>
              <option value="30" <?php echo $date_range === '30' ? 'selected' : ''; ?>>Last 30 days</option>
              <option value="90" <?php echo $date_range === '90' ? 'selected' : ''; ?>>Last 90 days</option>
              <option value="365" <?php echo $date_range === '365' ? 'selected' : ''; ?>>Last year</option>
            </select>
          </form>
          <button class="btn btn-secondary" onclick="exportAnalytics()">
            <i class="fas fa-download"></i> Export Report
          </button>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
          <div class="stat-icon primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?php echo $total_users; ?></div>
            <div class="stat-change positive">
              <i class="fas fa-user-plus"></i> <?php echo $new_users_today; ?> today
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon success">
            <i class="fas fa-book"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Total Books</div>
            <div class="stat-value"><?php echo $total_books; ?></div>
            <div class="stat-change positive">
              <i class="fas fa-check-circle"></i> Available
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon accent">
            <i class="fas fa-handshake"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Total Rentals</div>
            <div class="stat-value"><?php echo $total_rentals; ?></div>
            <div class="stat-change positive">
              <i class="fas fa-plus"></i> <?php echo $new_rentals_today; ?> today
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon secondary">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">LKR <?php echo number_format($total_revenue, 0); ?></div>
            <div class="stat-change positive">
              <i class="fas fa-arrow-up"></i> From rentals
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- User Growth Chart -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-line"></i> User Growth</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="userGrowthChart"></canvas>
          </div>
        </div>

        <!-- Rental Trends Chart -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-area"></i> Rental Trends</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="rentalTrendsChart"></canvas>
          </div>
        </div>
      </div>

      <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Revenue Chart -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Revenue Trend</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <!-- Rental Status Distribution -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Rental Status</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="rentalStatusChart"></canvas>
          </div>
        </div>
      </div>

      <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Book Type Distribution -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-doughnut"></i> Book Types</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="bookTypeChart"></canvas>
          </div>
        </div>

        <!-- User Activity by Hour -->
        <div class="dashboard-card">
          <div class="card-header">
            <h3><i class="fas fa-clock"></i> User Activity (24h)</h3>
          </div>
          <div class="card-content" style="padding: 1.5rem; position: relative; height: 300px;">
            <canvas id="activityChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Top Books Chart -->
      <div class="dashboard-card" style="margin-bottom: 2rem;">
        <div class="card-header">
          <h3><i class="fas fa-trophy"></i> Most Rented Books</h3>
        </div>
        <div class="card-content" style="padding: 1.5rem; position: relative; height: 400px;">
          <canvas id="topBooksChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/book-hub/public/static/js/admin.js"></script>
<script>
// Wait for DOM and Chart.js to be ready
window.addEventListener('load', function() {
  // Check if Chart.js is loaded
  if (typeof Chart === 'undefined') {
    console.error('Chart.js library not loaded!');
    alert('Chart.js library failed to load. Please check your internet connection.');
    return;
  }
  
  console.log('Chart.js loaded successfully');

  // Chart.js configuration
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
      },
      tooltip: {
        mode: 'index',
        intersect: false,
      }
    },
    scales: {
      x: {
        grid: {
          display: false
        }
      },
      y: {
        beginAtZero: true,
        grid: {
          color: 'rgba(0, 0, 0, 0.05)'
        }
      }
    }
  };

  // Ensure data arrays are not empty
  const userGrowthLabels = <?php echo json_encode($user_growth_labels); ?> || [];
  const userGrowthData = <?php echo json_encode($user_growth_data); ?> || [];
  const rentalTrendsLabels = <?php echo json_encode($rental_trends_labels); ?> || [];
  const rentalTrendsData = <?php echo json_encode($rental_trends_data); ?> || [];
  const rentalCompletedData = <?php echo json_encode($rental_completed_data); ?> || [];
  const revenueLabels = <?php echo json_encode($revenue_labels); ?> || [];
  const revenueData = <?php echo json_encode($revenue_data); ?> || [];
  const rentalStatusLabels = <?php echo json_encode($rental_status_labels); ?> || [];
  const rentalStatusData = <?php echo json_encode($rental_status_data); ?> || [];
  const bookTypeLabels = <?php echo json_encode($book_type_labels); ?> || [];
  const bookTypeData = <?php echo json_encode($book_type_data); ?> || [];
  const activityLabels = <?php echo json_encode($activity_labels); ?> || [];
  const activityData = <?php echo json_encode($activity_data); ?> || [];
  const topBooksLabels = <?php echo json_encode($top_books_labels); ?> || [];
  const topBooksData = <?php echo json_encode($top_books_data); ?> || [];

  // User Growth Chart
  try {
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
      console.log('Creating user growth chart...');
      new Chart(userGrowthCtx.getContext('2d'), {
        type: 'line',
        data: {
          labels: userGrowthLabels.length > 0 ? userGrowthLabels : ['No Data'],
          datasets: [{
            label: 'New Users',
            data: userGrowthData.length > 0 ? userGrowthData : [0],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: chartOptions
      });
      console.log('User growth chart created successfully');
    } else {
      console.error('User growth chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating user growth chart:', error);
  }

  // Rental Trends Chart
  try {
    const rentalTrendsCtx = document.getElementById('rentalTrendsChart');
    if (rentalTrendsCtx) {
      console.log('Creating rental trends chart...');
      new Chart(rentalTrendsCtx.getContext('2d'), {
        type: 'line',
        data: {
          labels: rentalTrendsLabels.length > 0 ? rentalTrendsLabels : ['No Data'],
          datasets: [{
            label: 'Total Rentals',
            data: rentalTrendsData.length > 0 ? rentalTrendsData : [0],
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
          }, {
            label: 'Completed',
            data: rentalCompletedData.length > 0 ? rentalCompletedData : [0],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
          }]
        },
        options: chartOptions
      });
      console.log('Rental trends chart created successfully');
    } else {
      console.error('Rental trends chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating rental trends chart:', error);
  }

  // Revenue Chart
  try {
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
      console.log('Creating revenue chart...');
      new Chart(revenueCtx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: revenueLabels.length > 0 ? revenueLabels : ['No Data'],
          datasets: [{
            label: 'Revenue (LKR)',
            data: revenueData.length > 0 ? revenueData : [0],
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: chartOptions.plugins,
          scales: {
            x: chartOptions.scales.x,
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                callback: function(value) {
                  return 'LKR ' + value.toLocaleString();
                }
              }
            }
          }
        }
      });
      console.log('Revenue chart created successfully');
    } else {
      console.error('Revenue chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating revenue chart:', error);
  }

  // Rental Status Pie Chart
  try {
    const rentalStatusCtx = document.getElementById('rentalStatusChart');
    if (rentalStatusCtx) {
      console.log('Creating rental status chart...');
      new Chart(rentalStatusCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: rentalStatusLabels.length > 0 ? rentalStatusLabels : ['No Data'],
          datasets: [{
            data: rentalStatusData.length > 0 ? rentalStatusData : [0],
            backgroundColor: [
              'rgba(245, 158, 11, 0.8)',
              'rgba(16, 185, 129, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(59, 130, 246, 0.8)',
              'rgba(107, 114, 128, 0.8)'
            ],
            borderColor: [
              'rgb(245, 158, 11)',
              'rgb(16, 185, 129)',
              'rgb(239, 68, 68)',
              'rgb(59, 130, 246)',
              'rgb(107, 114, 128)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right'
            }
          }
        }
      });
      console.log('Rental status chart created successfully');
    } else {
      console.error('Rental status chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating rental status chart:', error);
  }

  // Book Type Chart
  try {
    const bookTypeCtx = document.getElementById('bookTypeChart');
    if (bookTypeCtx) {
      console.log('Creating book type chart...');
      new Chart(bookTypeCtx.getContext('2d'), {
        type: 'pie',
        data: {
          labels: bookTypeLabels.length > 0 ? bookTypeLabels : ['No Data'],
          datasets: [{
            data: bookTypeData.length > 0 ? bookTypeData : [0],
            backgroundColor: [
              'rgba(59, 130, 246, 0.8)',
              'rgba(16, 185, 129, 0.8)'
            ],
            borderColor: [
              'rgb(59, 130, 246)',
              'rgb(16, 185, 129)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right'
            }
          }
        }
      });
      console.log('Book type chart created successfully');
    } else {
      console.error('Book type chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating book type chart:', error);
  }

  // Activity Chart
  try {
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
      console.log('Creating activity chart...');
      new Chart(activityCtx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: activityLabels.length > 0 ? activityLabels : ['No Data'],
          datasets: [{
            label: 'User Registrations',
            data: activityData.length > 0 ? activityData : [0],
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
            borderRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: chartOptions.plugins,
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                maxRotation: 45,
                minRotation: 45
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            }
          }
        }
      });
      console.log('Activity chart created successfully');
    } else {
      console.error('Activity chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating activity chart:', error);
  }

  // Top Books Chart
  try {
    const topBooksCtx = document.getElementById('topBooksChart');
    if (topBooksCtx) {
      console.log('Creating top books chart...');
      new Chart(topBooksCtx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: topBooksLabels.length > 0 ? topBooksLabels : ['No Data'],
          datasets: [{
            label: 'Rental Count',
            data: topBooksData.length > 0 ? topBooksData : [0],
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: 'rgb(245, 158, 11)',
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: chartOptions.plugins,
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            y: {
              grid: {
                display: false
              }
            }
          }
        }
      });
      console.log('Top books chart created successfully');
    } else {
      console.error('Top books chart canvas not found');
    }
  } catch (error) {
    console.error('Error creating top books chart:', error);
  }
  
  console.log('All charts initialized');
});

  function exportAnalytics() {
    // Simple export functionality
    window.print();
  }

  // Make exportAnalytics available globally
  window.exportAnalytics = exportAnalytics;
</script>
</body>
</html>


