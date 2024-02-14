/**
 * Toast Configuration and Vue App Setup for Registration Form
 *
 * This script configures Toast (a Swal component) and creates a Vue App instance
 * to manage the registration form.
 *
 * @category Frontend
 * @package Toast and Vue App Configuration
 * @author José Caruajulca
 */

// Configure Toast for notifications during registration
const Toast = Swal.mixin({
    toast: true, // Enable Toast notifications
    position: "top-end", // Position at the top right
    showConfirmButton: false, // Do not show confirmation button
    timer: 3000, // Default duration of 3 seconds
    timerProgressBar: true, // Display progress bar during the duration
    didOpen: (toast) => {
        // Pause timer when hovering over the notification
        toast.onmouseenter = Swal.stopTimer;
        // Resume timer when mouse leaves the notification
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// Create Vue App instance for registration form
const app = Vue.createApp({
    data() {
        return {
            csrfToken: document.getElementById('app').dataset.csrfToken || '',
            formData: {
                username: '',
                name: '',
                lastName: '',
                email: '',
                password: ''
            }
        };
    },
    methods: {
        /**
         * Handle registration form submission
         *
         * This method sends a POST request to the backend with form data
         * and handles responses, showing Toast notifications or clearing the form as needed.
         */
        formRegister() {
            const formData = new FormData();
            formData.append('csrf_token', this.csrfToken);
            formData.append('username', this.formData.username);
            formData.append('name', this.formData.name);
            formData.append('last_name', this.formData.lastName);
            formData.append('email', this.formData.email);
            formData.append('password', this.formData.password);

            fetch('backend', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        Swal.fire({
                            icon: 'success',
                            title: data.success_message,
                            showConfirmButton: true
                        }).then(() => {
                            // Clear the form after successful registration
                            this.clearForm();
                        });
                    } else {
                        // Show error notification
                        Toast.fire({
                            icon: "error",
                            title: data.error_message
                        });
                    }
                })
                .catch(error => {
                    // Show error notification in case of request failure
                    Toast.fire({
                        icon: "error",
                        title: error
                    });
                });
        },
        /**
         * Clear form data after successful registration
         */
        clearForm() {
            this.formData.username = '';
            this.formData.name = '';
            this.formData.lastName = '';
            this.formData.email = '';
            this.formData.password = '';
        }
    }
});

// Mount the Vue App on the element with the id 'app' in the DOM
app.mount('#app');
