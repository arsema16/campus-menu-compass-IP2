document.addEventListener('DOMContentLoaded', () => {
  // Toggle between sign up and sign in panels
  document.getElementById('register').addEventListener('click', () => {
    document.getElementById('container').classList.add('active');
  });

  document.getElementById('login').addEventListener('click', () => {
    document.getElementById('container').classList.remove('active');
  });

  // Login form submit handler
  document.getElementById('login-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('loginPassword').value;
const role = document.getElementById('login-role').value.trim().toLowerCase();

    if (!email || !password || !role) {
      alert("Please fill in all fields!");
      return;
    }

    try {
      const response = await fetch('login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ email, password, role })
      });

      const result = await response.json();

      if (result.status === 'success') {
        alert(result.message);
        // Redirect based on role
        if (result.data.role === 'regular_admin' || result.data.role === 'super_admin') {
          window.location.href = 'admin.php';
        } else {
          window.location.href = 'option.php';
        }
      } else {
        alert(result.message);
      }
    } catch (error) {
      console.error('Login Error:', error);
      alert("Login failed due to a technical error.");
    }
  });

   // Signup form submit handler with password validation
  document.getElementById('signup-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const username = document.getElementById('signup-name').value.trim();
    const email = document.getElementById('signup-email').value.trim();
    const password = document.getElementById('registerPassword').value;
    const role = document.querySelector('input[name="role"]').value; // should be "user"

    if (!username || !email || !password || !role) {
      alert("Please fill in all fields.");
      return;
    }

    // Password validation
    const uppercaseRegex = /[A-Z]/;
    if (password.length < 6 || !uppercaseRegex.test(password)) {
      alert("Password must be at least 6 characters long and include at least one uppercase letter.");
      return;
    }

    try {
      const response = await fetch('register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ username, email, password, role })
      });

      const result = await response.json();

      if (result.status === 'success') {
        alert(result.message);
        document.getElementById('container').classList.remove('active');
      } else {
        alert(result.message || 'Registration failed.');
      }
    } catch (err) {
      console.error('Registration Error:', err);
      alert('Something went wrong. Please try again.');
    }
  });

  // Password toggle icon handler
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const targetId = icon.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (input) {
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bx-hide');
          icon.classList.add('bx-show');
        } else {
          input.type = 'password';
          icon.classList.remove('bx-show');
          icon.classList.add('bx-hide');
        }
      }
    });
  });
});
