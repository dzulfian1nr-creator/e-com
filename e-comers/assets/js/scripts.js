/**
 * ===================================================
 * CUSTOM JAVASCRIPT
 * ===================================================
 */

// Auto-hide alert messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Alert yang dismissible akan auto hide setelah 5 detik
        if (alert.querySelector('.btn-close')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
});

// Format currency display
function formatCurrency(value) {
    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    });
    return formatter.format(value);
}

// Confirm delete action
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus?') {
    return confirm(message);
}

// Add to cart with loading state
function addToCart(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner spinner-border spinner-border-sm me-2"></span>Loading...';
    
    setTimeout(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    }, 1000);
}

// Update quantity input with min/max validation
document.addEventListener('change', function(e) {
    if (e.target.name === 'quantity') {
        const input = e.target;
        const min = parseInt(input.getAttribute('min')) || 1;
        const max = parseInt(input.getAttribute('max')) || 999;
        let value = parseInt(input.value);
        
        if (value < min) input.value = min;
        if (value > max) input.value = max;
    }
});

// Search product functionality
const searchInput = document.getElementById('search');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
            const productName = product.getAttribute('data-product');
            if (productName && productName.includes(searchTerm)) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    });
}

// Print invoice
function printInvoice() {
    window.print();
}

// Logout confirmation
function confirmLogout() {
    return confirm('Apakah Anda yakin ingin logout?');
}
