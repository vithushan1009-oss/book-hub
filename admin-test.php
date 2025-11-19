<?php
// Simple admin test page - bypass session checks for testing
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Test - BOOK HUB</title>
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0;
      padding: 20px;
      min-height: 100vh;
    }
    
    .test-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    h1 {
      color: #333;
      text-align: center;
      margin-bottom: 30px;
    }
    
    .icon-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .icon-card {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      border: 1px solid #e9ecef;
    }
    
    .icon-card h3 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #495057;
      font-size: 16px;
    }
    
    .icon-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 4px;
      transition: background-color 0.2s;
    }
    
    .icon-item:hover {
      background: #e9ecef;
    }
    
    .icon-item i {
      font-size: 18px;
      width: 24px;
      margin-right: 12px;
      color: #007bff;
    }
    
    .status {
      text-align: center;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 600;
    }
    
    .status.loading {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }
    
    .status.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .status.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="test-container">
    <h1><i class="fas fa-book-open"></i> BOOK HUB Admin Dashboard Test</h1>
    
    <div class="status loading" id="status">
      <i class="fas fa-spinner fa-spin"></i> Testing Font Awesome Icon Loading...
    </div>
    
    <div class="icon-grid">
      <!-- Dashboard Icons -->
      <div class="icon-card">
        <h3>📊 Dashboard Icons</h3>
        <div class="icon-item">
          <i class="fas fa-chart-pie"></i>
          <span>Dashboard</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-chart-line"></i>
          <span>Analytics</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-chart-bar"></i>
          <span>Reports</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-tachometer-alt"></i>
          <span>Overview</span>
        </div>
      </div>
      
      <!-- User Management Icons -->
      <div class="icon-card">
        <h3>👥 User Management</h3>
        <div class="icon-item">
          <i class="fas fa-users"></i>
          <span>All Users</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-user-plus"></i>
          <span>Add User</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-user-check"></i>
          <span>Verified</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-user-shield"></i>
          <span>Administrators</span>
        </div>
      </div>
      
      <!-- Content Icons -->
      <div class="icon-card">
        <h3>📚 Content Management</h3>
        <div class="icon-item">
          <i class="fas fa-book"></i>
          <span>Books</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-book-open"></i>
          <span>Library</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-tags"></i>
          <span>Categories</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-handshake"></i>
          <span>Rentals</span>
        </div>
      </div>
      
      <!-- System Icons -->
      <div class="icon-card">
        <h3>⚙️ System & Settings</h3>
        <div class="icon-item">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-bell"></i>
          <span>Notifications</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-download"></i>
          <span>Export</span>
        </div>
        <div class="icon-item">
          <i class="fas fa-power-off"></i>
          <span>Logout</span>
        </div>
      </div>
    </div>
    
    <div style="text-align: center;">
      <a href="/BOOKHUB/book-hub-central/public/admin.php" style="display: inline-block; background: #007bff; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
        <i class="fas fa-arrow-right"></i> Go to Real Admin Dashboard
      </a>
    </div>
  </div>

  <script>
    // Test Font Awesome loading
    setTimeout(() => {
      const testIcon = document.querySelector('.fas.fa-chart-pie');
      const computedStyle = window.getComputedStyle(testIcon, '::before');
      const content = computedStyle.getPropertyValue('content');
      const statusEl = document.getElementById('status');
      
      if (content && content !== 'none' && content !== '""' && content !== '"\\f200"') {
        statusEl.className = 'status success';
        statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ✅ Font Awesome Icons Loading Successfully! All icons should display correctly.';
        console.log('✅ Font Awesome working:', content);
      } else {
        statusEl.className = 'status error';
        statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ❌ Font Awesome Failed to Load. Icons may not display properly.';
        console.log('❌ Font Awesome failed:', content);
      }
    }, 1500);
  </script>
</body>
</html>