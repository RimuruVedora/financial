// Initialize lucide icons (render only once)
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
});

// Check if mobile view
function isMobileView() {
  return window.innerWidth < 768; // Tailwind's md breakpoint
}

// Toggle sidebar function
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarLogo = document.getElementById('sidebar-logo');
  const sonlyLogo = document.getElementById('sonly');
  const overlay = document.querySelector('.sidebar-overlay');

  if (isMobileView()) {
    // Mobile behavior
    sidebar.classList.toggle('translate-x-0');
    sidebar.classList.toggle('-translate-x-full');

    // Overlay toggle
    overlay.style.display = sidebar.classList.contains('translate-x-0') ? 'block' : 'none';
  } else {
    // Desktop behavior
    const isCollapsed = sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-20', !isCollapsed);

    localStorage.setItem('sidebarCollapsed', !isCollapsed);

    document.querySelectorAll('.sidebar-text').forEach(text => {
      text.classList.toggle('hidden', !isCollapsed);
    });

    if (sidebar.classList.contains('w-20')) {
      sidebarLogo.classList.add('hidden');
      sonlyLogo.classList.remove('hidden');
    } else {
      sidebarLogo.classList.remove('hidden');
      sonlyLogo.classList.add('hidden');
    }
  }
}

// Handle window resize
function handleResize() {
  const sidebar = document.getElementById('sidebar');
  const sidebarLogo = document.getElementById('sidebar-logo');
  const sonlyLogo = document.getElementById('sonly');

  if (isMobileView()) {
    if (!sidebar.classList.contains('translate-x-0')) {
      sidebar.classList.add('-translate-x-full');
      sidebar.classList.remove('translate-x-0');
    }
    sidebarLogo.classList.remove('hidden');
    sonlyLogo.classList.add('hidden');
  } else {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    sidebar.classList.remove('-translate-x-full', 'translate-x-0');
    sidebar.classList.toggle('w-64', !isCollapsed);
    sidebar.classList.toggle('w-20', isCollapsed);

    document.querySelectorAll('.sidebar-text').forEach(text => {
      text.classList.toggle('hidden', isCollapsed);
    });

    if (isCollapsed) {
      sidebarLogo.classList.add('hidden');
      sonlyLogo.classList.remove('hidden');
    } else {
      sidebarLogo.classList.remove('hidden');
      sonlyLogo.classList.add('hidden');
    }
  }
}

// Initialize sidebar
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarLogo = document.getElementById('sidebar-logo');
  const sonlyLogo = document.getElementById('sonly');

  if (isMobileView()) {
    sidebar.classList.add('-translate-x-full');
    sidebarLogo.classList.remove('hidden');
    sonlyLogo.classList.add('hidden');
  } else {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    sidebar.classList.add(isCollapsed ? 'w-20' : 'w-64');

    document.querySelectorAll('.sidebar-text').forEach(text => {
      text.classList.toggle('hidden', isCollapsed);
    });

    if (isCollapsed) {
      sidebarLogo.classList.add('hidden');
      sonlyLogo.classList.remove('hidden');
    } else {
      sidebarLogo.classList.remove('hidden');
      sonlyLogo.classList.add('hidden');
    }
  }

  setTimeout(() => {
    sidebar.classList.add('loaded');
  }, 50);

  window.addEventListener('resize', handleResize);
}

// Philippine Time
document.addEventListener('DOMContentLoaded', () => {
  function displayPhilippineTime() {
    const options = {
      timeZone: 'Asia/Manila',
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true
    };
    const philippineDateTime = new Date().toLocaleString('en-PH', options);
    const timeElement = document.getElementById('philippineTime');
    if (timeElement) timeElement.textContent = philippineDateTime;
  }

  displayPhilippineTime();
  setInterval(displayPhilippineTime, 1000);

  initSidebar();
});
