// Authentication functions
const AUTH_KEY = 'expenseTrackerAuth';
const USER_KEY = 'expenseTrackerUser';

// Check if user is logged in
function isLoggedIn() {
  return localStorage.getItem(AUTH_KEY) === 'true';
}

// Get current user
function getCurrentUser() {
  return localStorage.getItem(USER_KEY);
}

// Login function
function login(username, password) {
  // Simple validation (replace with real authentication)
  if (username && password) {
    localStorage.setItem(AUTH_KEY, 'true');
    localStorage.setItem(USER_KEY, username);
    localStorage.setItem('lastLoginTime', new Date().toISOString());
    return true;
  }
  return false;
}

// Logout function
function logout() {
  localStorage.removeItem(AUTH_KEY);
  localStorage.removeItem(USER_KEY);
  localStorage.removeItem('lastLoginTime');
  window.location.href = 'login.html';
}

// Redirect to login if not authenticated
function requireAuth() {
  if (!isLoggedIn()) {
    window.location.href = 'login.html';
    return false;
  }
  return true;
}

// Update navbar based on auth status
function updateNavbar() {
  const navbar = document.querySelector('.navbar ul');
  if (!navbar) return;

  if (isLoggedIn()) {
    const user = getCurrentUser();
    navbar.innerHTML = `
      <li><a href="dashboard.html">Dashboard</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="view_expenses.php?username=${encodeURIComponent(user)}">My Expenses</a></li>
      <li><a href="#" onclick="logout()" style="color: #f56565;">Logout (${user})</a></li>
    `;
  } else {
    navbar.innerHTML = `
      <li><a href="login.html">Login</a></li>
      <li><a href="register.html">Register</a></li>
    `;
  }
}

// Initialize auth check on page load
document.addEventListener('DOMContentLoaded', function() {
  updateNavbar();
  
  // Update last visited time
  const lastVisitedSpan = document.getElementById('last-visited');
  if (lastVisitedSpan) {
    const lastVisited = localStorage.getItem('lastVisited');
    const now = new Date();
    
    if (lastVisited) {
      lastVisitedSpan.textContent = new Date(lastVisited).toLocaleString();
    } else {
      lastVisitedSpan.textContent = "This is your first visit.";
    }
    localStorage.setItem('lastVisited', now.toISOString());
  }
});
