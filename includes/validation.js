// Form validation for edit karyawan
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Validation functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
    
    function validatePhone(phone) {
        const re = /^(\+62|62|0)8[1-9][0-9]{6,10}$/;
        return re.test(phone);
    }
    
    function validateNIK(nik) {
        const re = /^[0-9]{16}$/;
        return re.test(nik);
    }
    
    function validateNumeric(value) {
        return !isNaN(value) && value >= 0;
    }
    
    // Real-time validation
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], input[type="date"]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearError(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        let isValid = true;
        let errorMessage = '';
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Field ini wajib diisi';
        }
        
        // Email validation
        if (name === 'email' && value && !validateEmail(value)) {
            isValid = false;
            errorMessage = 'Format email tidak valid';
        }
        
        // Phone validation
        if (name === 'no_telp' && value && !validatePhone(value)) {
            isValid = false;
            errorMessage = 'Format nomor telepon tidak valid';
        }
        
        // NIK validation
        if (name === 'nik' && value && !validateNIK(value)) {
            isValid = false;
            errorMessage = 'NIK harus 16 digit angka';
        }
        
        // Numeric validation
        if ((name === 'gaji_pokok' || name === 'tunjangan' || name === 'bonus') && value && !validateNumeric(value)) {
            isValid = false;
            errorMessage = 'Harus berupa angka positif';
        }
        
        // Date validation
        if (name === 'tgl_lahir' && value) {
            const birthDate = new Date(value);
            const today = new Date();
            if (birthDate > today) {
                isValid = false;
                errorMessage = 'Tanggal lahir tidak boleh masa depan';
            }
        }
        
        if (!isValid) {
            showError(field, errorMessage);
        }
        
        return isValid;
    }
    
    function showError(field, message) {
        const formGroup = field.closest('.form-group');
        let errorDiv = formGroup.querySelector('.error-message');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger small mt-1';
            formGroup.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        field.classList.add('is-invalid');
    }
    
    function clearError(field) {
        const formGroup = field.closest('.form-group');
        const errorDiv = formGroup.querySelector('.error-message');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        
        field.classList.remove('is-invalid');
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isFormValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isFormValid = false;
            }
        });
        
        if (isFormValid) {
            // Show confirmation
            if (confirm('Apakah Anda yakin ingin menyimpan perubahan?')) {
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                
                form.submit();
            }
        }
    });
});
