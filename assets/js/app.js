/**
 * FORSAKDA 26 - Global Frontend JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // 2. User Profile Dropdown
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }

    // 3. Auto dismiss alert messages after 5 seconds
    const alerts = document.querySelectorAll('.auto-dismiss-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // 4. Preview Image Upload
    const imageInputs = document.querySelectorAll('.image-preview-input');
    imageInputs.forEach(input => {
        const previewTargetId = input.dataset.previewTarget;
        if (previewTargetId) {
            const previewEl = document.getElementById(previewTargetId);
            input.addEventListener('change', function () {
                if (this.files && this.files[0] && previewEl) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewEl.src = e.target.result;
                        previewEl.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });

    // 5. Table / List Live Search
    const searchInputs = document.querySelectorAll('.table-search-input');
    searchInputs.forEach(input => {
        const targetTableId = input.dataset.tableTarget;
        if (targetTableId) {
            const table = document.getElementById(targetTableId);
            if (table) {
                input.addEventListener('keyup', function () {
                    const query = this.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }
        }
    });
});

/**
 * Helper to show dynamic confirmation modal
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
