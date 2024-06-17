document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.menu-item');

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            const tableNumber = item.getAttribute('data-table');
            window.location.href = `placeorder.html?table=${tableNumber}`;
        });
    });
});
