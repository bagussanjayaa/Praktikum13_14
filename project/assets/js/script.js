document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const usernameInput = document.getElementById('username').value.trim();
            const passwordInput = document.getElementById('password').value;
            const messageDiv = document.getElementById('login-message');
            
            let isValid = true;
            let errorMessage = '';

            if (usernameInput === '' || passwordInput.length < 6) {
                isValid = false;
                errorMessage = 'Login gagal: Username harus diisi dan Password minimal 6 karakter.';
            }

            if (!isValid) {
                event.preventDefault(); 
                
                if (messageDiv) {
                    messageDiv.innerHTML = '<p style="color:red; font-weight:bold;">' + errorMessage + '</p>';
                } else {
                    alert(errorMessage);
                }
            }
        });
    }

    console.log("Assets JS loaded. Modular system active.");
});