

document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeCart();
});

function initializeEventListeners() {
    const categorySelect = document.getElementById('categorySelect');
    const sortSelect = document.getElementById('sortSelect');
    const filterForm = document.getElementById('filterForm');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            if (filterForm) filterForm.submit();
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            if (filterForm) filterForm.submit();
        });
    }
    
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            addToCart(productId, quantity);
        });
    });

    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
    });
}

function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('/E-comm/pages/add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('<i class="fas fa-check-circle"></i> Add to Cart Done', 'success');
            updateCartCount(data.cart_count);
        } else {
            showNotification('<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Failed to add product to cart'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.', 'danger');
    });
}

function initializeCart() {
    const cartIcon = document.querySelector('.navbar-brand .fas-shopping-cart');
    if (cartIcon) {
    }
}

function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    } else {
        alert(message);
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

function updateCartTotal() {
    const quantityInputs = document.querySelectorAll('input[name^="quantity"]');
    let total = 0;

    quantityInputs.forEach(input => {
        const row = input.closest('tr');
        if (row) {
            const priceCell = row.querySelector('td:nth-child(2)');
            const price = parseFloat(priceCell.textContent.replace('$', ''));
            const quantity = parseInt(input.value);
            total += price * quantity;
        }
    });

    const totalElement = document.querySelector('.cart-total');
    if (totalElement) {
        totalElement.textContent = formatPrice(total);
    }
}

function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

function toggleVisibility(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
}

function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
    }
}

function getUrlParameter(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const searchProducts = debounce(function(query) {
    if (query.length > 2) {
        console.log('Searching for:', query);
    }
}, 300);

function toCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
}

function debugLog(message, data) {
    if (console && console.log) {
        console.log(message, data);
    }
}

function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

window.addToCart = addToCart;
window.showNotification = showNotification;
window.confirmDelete = confirmDelete;
window.updateCartTotal = updateCartTotal;
window.validateForm = validateForm;
window.formatPrice = formatPrice;
window.formatDate = formatDate;
window.scrollToElement = scrollToElement;
window.toggleVisibility = toggleVisibility;


