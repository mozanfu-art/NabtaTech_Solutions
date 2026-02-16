document.querySelectorAll('[data-now]').forEach((el) => {
    const now = new Date();
    el.textContent = now.toLocaleString();
});
