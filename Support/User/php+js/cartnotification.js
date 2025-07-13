document.addEventListener('DOMContentLoaded', () => {
    const triggerElement = document.getElementById('cart-notification-trigger');

    if (triggerElement && triggerElement.dataset.showPopup === 'true') {
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
            <span class="icon">🛒</span>
            <span>View Your Current Cart</span>
        `;
        notification.onclick = () => {
            window.location.href = 'cart.php';
        };

        document.body.appendChild(notification);

        // Animate the notification in
        setTimeout(() => {
            notification.classList.add('show');
        }, 100); // Small delay to ensure CSS transition applies

        // Optionally, hide after some time if not clicked
        // setTimeout(() => {
        //     notification.classList.remove('show');
        //     setTimeout(() => notification.remove(), 500); // Remove after transition
        // }, 10000); // Hide after 10 seconds
    }
});