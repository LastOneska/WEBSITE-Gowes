document.addEventListener('DOMContentLoaded', () => {
    const durationInput = document.querySelector('[data-rental-duration]');
    const totalOutput = document.querySelector('[data-rental-total]');
    const durationOutput = document.querySelector('[data-duration-output]');

    if (durationInput && totalOutput) {
        const hourlyRate = Number(durationInput.dataset.hourlyRate || 0);
        const formatCurrency = (amount) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(amount);

        const updateTotal = () => {
            const duration = Math.max(1, Number(durationInput.value) || 1);
            totalOutput.textContent = formatCurrency(hourlyRate * duration);
            if (durationOutput) {
                durationOutput.textContent = `${duration} jam`;
            }
        };

        durationInput.addEventListener('input', updateTotal);
        updateTotal();
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || 'Lanjutkan tindakan ini?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
