document.addEventListener('DOMContentLoaded', () => {
  // Initialize menu state
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

  // Initialize form submission if form exists
  const addFacultyForm = document.getElementById('addFacultyForm');
  if (addFacultyForm) {
    initializeFacultyForm(addFacultyForm);
  }
});

// ====== MENU TOGGLE ==========
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

  if (!bar1.style.transform) {
    bar1.style.transform = 'rotate(0) translate(0)';
    bar2.style.opacity = '1';
    bar3.style.transform = 'rotate(0) translate(0)';
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

function renderStatsCards(container, stats) {
  container.innerHTML = '';
  stats.forEach(stat => {
    const statCard = document.createElement('div');
    statCard.className = 'stat-card';
    statCard.innerHTML = `
      <h3>${stat.title}</h3>
      <div class="stat-value">${stat.value}</div>
    `;
    container.appendChild(statCard);
  });
}

function populateCredentialList(elementId, items) {
  const listElement = document.getElementById(elementId);
  if (!listElement) return;

  listElement.innerHTML = '';

  if (!items || items.length === 0) {
    const noItemsElement = document.createElement('li');
    noItemsElement.className = 'no-pending';
    noItemsElement.textContent = 'No items found';
    listElement.appendChild(noItemsElement);
    return;
  }

  items.forEach(item => {
    const listItem = document.createElement('li');
    listItem.innerHTML = `
      <span class="credential-type">${item.type || 'Document'}</span>
      <span class="credential-name">${item.name || 'Untitled'}</span>
      <span class="credential-date">${item.date || 'No date'}</span>
    `;
    listElement.appendChild(listItem);
  });
}

function updateCredentialCounts(counts) {
  document.querySelectorAll('.credential-card .stat-value').forEach((el, index) => {
    if (index < counts.length) {
      el.textContent = counts[index];
    }
  });
}

// ====== FORM HANDLING ==========
function initializeFacultyForm(form) {
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    const messageBox = document.getElementById('addFacultyMessage');
    
    try {
      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      messageBox.style.display = 'none';
      messageBox.textContent = '';

      // Clear previous error highlights
      form.querySelectorAll('.error-field').forEach(el => el.classList.remove('error-field'));

      // Validate form
      const requiredFields = ['faculty_id', 'full_name', 'email', 'college_id'];
      const missingFields = requiredFields.filter(field => {
        const element = form.elements[field];
        return !element || !element.value.trim();
      });
      
      if (missingFields.length > 0) {
        throw new Error(`Please fill in all required fields: ${missingFields.join(', ')}`);
      }

      // Validate email format
      const email = form.elements['email'].value;
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        throw new Error('Please enter a valid email address');
      }

      // Validate faculty ID format (XX-XXXXX)
      const facultyId = form.elements['faculty_id'].value;
      if (!/^\d{2}-\d{5}$/.test(facultyId)) {
        throw new Error('Faculty ID must be in format XX-XXXXX');
      }

      // Prepare form data
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      // Submit form
      const response = await fetch('add_faculty.php', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
      });

      // Handle response
      const responseText = await response.text();
      let result;
      
      try {
        result = JSON.parse(responseText);
      } catch (e) {
        console.error('Failed to parse JSON:', responseText);
        throw new Error('Server returned an invalid response');
      }

      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Failed to add faculty');
      }

      // Success - show message in box and alert
      messageBox.textContent = result.message || 'Faculty added successfully!';
      messageBox.style.display = 'block';
      messageBox.style.backgroundColor = '#d4edda';
      messageBox.style.color = '#155724';
      
      alert(result.message || 'Faculty added successfully!');
      
      form.reset();
      
      // Redirect after delay
      setTimeout(() => {
        window.location.reload();
      }, 1500);

    } catch (error) {
      console.error('Error:', error);
      
      // Show error in message box
      messageBox.textContent = error.message;
      messageBox.style.display = 'block';
      messageBox.style.backgroundColor = '#f8d7da';
      messageBox.style.color = '#721c24';
      
      // Also show alert
      alert('Error: ' + error.message);

      // Highlight problematic fields
      if (error.message.includes('Faculty ID')) {
        form.elements['faculty_id'].classList.add('error-field');
      }
      if (error.message.includes('Email')) {
        form.elements['email'].classList.add('error-field');
      }
      if (error.message.includes('College')) {
        const collegeInput = form.querySelector('input[name="college_id"]');
        if (collegeInput) collegeInput.classList.add('error-field');
      }
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  });
}