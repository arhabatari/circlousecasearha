(() => {
    const form = document.querySelector('.request-form');
    if (!form) return;
    const start = form.querySelector('[name="starts_on"]');
    const end = form.querySelector('[name="ends_on"]');
    const total = form.querySelector('[data-total]');
    const duration = form.querySelector('[data-duration]');
    const price = Number(form.dataset.price);
    const minimum = end.min;
    const update = () => {
        end.min = start.value && start.value > minimum ? start.value : minimum;
        const startDate = Date.parse(`${start.value}T00:00:00Z`);
        const endDate = Date.parse(`${end.value}T00:00:00Z`);
        const days = Math.round((endDate - startDate) / 86400000) + 1;
        if (!Number.isFinite(days) || days < 1) {
            total.textContent = '—';
            duration.textContent = 'Choose a valid period';
            return;
        }
        duration.textContent = `${days} ${days === 1 ? 'day' : 'days'} · both dates included`;
        total.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(days * price)}`;
    };
    start.addEventListener('change', update);
    end.addEventListener('change', update);
    update();
})();
