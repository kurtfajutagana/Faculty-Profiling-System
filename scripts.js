function resetLink() {
  const email = document.getElementById("email").value;
  if (email.trim() === "") {
    alert("Please enter a valid email address.");
  } else {
    alert("Password reset link has been sent to " + email);
  }
}

// ====== MENU TOGGLE WITH BACKDROP & ACCESSIBILITY ==========
function toggleMenu() {
  const menu = document.getElementById('menu');
  const body = document.body;
  const bar1 = document.getElementById('bar1');
  const bar2 = document.getElementById('bar2');
  const bar3 = document.getElementById('bar3');

  if (!menu || !body || !bar1 || !bar2 || !bar3) return;

  // Ensure backdrop exists
  let backdrop = document.querySelector('.menu-backdrop');
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.className = 'menu-backdrop';
    document.body.appendChild(backdrop);
    backdrop.addEventListener('click', () => {
      if (menu.classList.contains('active')) {
        toggleMenu();
      }
    });
  }

  if (menu.classList.contains('active')) {
    menu.classList.remove('active');
    body.classList.remove('menu-open');
    backdrop.classList.remove('active');
    bar1.style.transform = 'rotate(0) translate(0)';
    bar2.style.opacity = '1';
    bar3.style.transform = 'rotate(0) translate(0)';
  } else {
    menu.classList.add('active');
    body.classList.add('menu-open');
    backdrop.classList.add('active');
    bar1.style.transform = 'rotate(45deg) translate(5px, 5px)';
    bar2.style.opacity = '0';
    bar3.style.transform = 'rotate(-45deg) translate(7px, -6px)';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const menu = document.getElementById('menu');
  const bar1 = document.getElementById('bar1');
  const bar2 = document.getElementById('bar2');
  const bar3 = document.getElementById('bar3');
  
  if (menu && bar1 && bar2 && bar3) {
    menu.classList.remove('active');
    bar1.style.transform = 'rotate(0) translate(0)';
    bar2.style.opacity = '1';
    bar3.style.transform = 'rotate(0) translate(0)';
  }

  // Create backdrop if menu exists
  if (menu && !document.querySelector('.menu-backdrop')) {
    const backdrop = document.createElement('div');
    backdrop.className = 'menu-backdrop';
    document.body.appendChild(backdrop);
    backdrop.addEventListener('click', () => {
      if (menu.classList.contains('active')) {
        toggleMenu();
      }
    });
  }

  // Close menu on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const activeMenu = document.getElementById('menu');
      if (activeMenu && activeMenu.classList.contains('active')) {
        toggleMenu();
      }
    }
  });

  // Auto-close menu on link click on small screens
  if (menu) {
    const navLinks = menu.querySelectorAll('nav a:not([id="reportsDropdown"])');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 768 && menu.classList.contains('active')) {
          toggleMenu();
        }
      });
    });
  }
});