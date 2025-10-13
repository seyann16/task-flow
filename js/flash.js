// Flash Message System
class FlashManager {
    showFlashMessage(message, type = 'success', duration = 5000) {
        // Get the container with the id of flashMessages
        const container = document.getElementById('flashMessages');
        // Return if there is no container
        if (!container) return;

        // Create a message element
        const messageEl = document.createElement('div');
        // Create a class name for the messag element
        messageEl.className = `flash-message flash-${type}`;
        // Create the inner html
        messageEl.innerHTML = `
            <div class="flash-content">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                <span>${message}</span>
            </div>
            <button class="flash-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Append the message element to the parent element
        container.appendChild(messageEl);

        // Add enter animation
        setTimeout(() => {
            messageEl.style.transform = 'translateX(0)';
            messageEl.style.opacity = '1';
        }, 10);

        // Set timeout if duration is above 0
        if (duration > 0) {
            setTimeout(() => {
                // Remove the child element if the message element belongs to a parent element
                if (messageEl.parentElement) {
                    messageEl.remove();
                }
            }, duration);
        }
    }
}