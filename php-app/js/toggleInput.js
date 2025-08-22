document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('buchungsarten-dropdown');
    const customInput = document.getElementById('custom-input');

    if (dropdown && customInput) {
        dropdown.addEventListener('change', function () {
            if (this.value === 'custom') {
                customInput.classList.remove('d-none');
            } else {
                customInput.classList.add('d-none');
            }
        });
    }
});
