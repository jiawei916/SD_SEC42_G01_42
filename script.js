// Basic Login Form Script
class BasicLoginForm {
    constructor() {
        this.form = document.getElementById('loginForm');
        this.emailInput = document.getElementById('email');
        this.passwordInput = document.getElementById('password');
        this.passwordToggle = document.getElementById('passwordToggle');
        this.successMessage = document.getElementById('successMessage');
        
        this.init();
    }
    
    init() {
        // Initialize shared utilities
        FormUtils.addSharedAnimations();
        FormUtils.setupFloatingLabels(this.form);
        FormUtils.setupPasswordToggle(this.passwordInput, this.passwordToggle);
        
        // Add event listeners
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
        this.emailInput.addEventListener('input', () => this.validateField('email'));
        this.passwordInput.addEventListener('input', () => this.validateField('password'));
        
        // Add entrance animation
        FormUtils.addEntranceAnimation(this.form.closest('.login-card'), 100);
    }
    
    validateField(fieldName) {
        const input = document.getElementById(fieldName);
        const value = input.value.trim();
        let validation;
        
        // Clear previous errors
        FormUtils.clearError(fieldName);
        
        // Validate based on field type
        if (fieldName === 'email') {
            validation = FormUtils.validateEmail(value);
        } else if (fieldName === 'password') {
            validation = FormUtils.validatePassword(value);
        }
        
        if (!validation.isValid && value !== '') {
            FormUtils.showError(fieldName, validation.message);
            return false;
        } else if (validation.isValid) {
            FormUtils.showSuccess(fieldName);
            return true;
        }
        
        return true;
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        const email = this.emailInput.value.trim();
        const password = this.passwordInput.value.trim();
        
        // Validate all fields
        const emailValid = this.validateField('email');
        const passwordValid = this.validateField('password');
        
        if (!emailValid || !passwordValid) {
            FormUtils.showNotification('Please fix the errors below', 'error', this.form);
            return;
        }
        
        // Show loading state
        const submitBtn = this.form.querySelector('.login-btn');
        submitBtn.classList.add('loading');
        
        try {
            // Simulate login process
            await FormUtils.simulateLogin(email, password);
            
            // Show success state
            this.showSuccess();
            
        } catch (error) {
            // Show error notification
            FormUtils.showNotification(error.message, 'error', this.form);
        } finally {
            // Remove loading state
            submitBtn.classList.remove('loading');
        }
    }
    
    showSuccess() {
        // Hide the form
        this.form.style.display = 'none';
        
        // Show success message
        this.successMessage.classList.add('show');
        
        // Simulate redirect after 2 seconds
        setTimeout(() => {
            FormUtils.showNotification('Redirecting to dashboard...', 'success', this.successMessage);
        }, 2000);
    }
}

// Initialize the form when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BasicLoginForm();
});

// JavaScript to handle show/hide password
document.addEventListener("DOMContentLoaded", function () {
  const passwordField = document.getElementById("password");
  const toggleButton = document.getElementById("passwordToggle");
  const eyeIcon = document.getElementById("eyeIcon");

  toggleButton.addEventListener("click", function () {
    if (passwordField.type === "password") {
      passwordField.type = "text";
      eyeIcon.classList.add("visible");
      eyeIcon.classList.remove("hidden");
    } else {
      passwordField.type = "password";
      eyeIcon.classList.remove("visible");
      eyeIcon.classList.add("hidden");
    }
  });
});


// Remember Me: add checkbox and persist using localStorage
window.onload = function() {
  const rememberCheckbox = document.getElementById("rememberMe");
  const emailField = document.getElementById("email");

  // Load saved email if exists
  if (localStorage.getItem("rememberedEmail")) {
    emailField.value = localStorage.getItem("rememberedEmail");
    rememberCheckbox.checked = true;
  }

  document.getElementById("loginForm").addEventListener("submit", function() {
    if (rememberCheckbox.checked) {
      localStorage.setItem("rememberedEmail", emailField.value);
    } else {
      localStorage.removeItem("rememberedEmail");
    }
  });
};
