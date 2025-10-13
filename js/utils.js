// Utility Functions
class Utils {
    async showConfirmationModal(title, message, confirmText, cancelText) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'modal active';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>${title}</h3>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" id="confirmCancel">${cancelText}</button>
                        <button class="btn btn-danger" id="confirmOk">${confirmText}</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            modal.querySelector('#confirmOk').addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });
            
            modal.querySelector('#confirmCancel').addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });
        });
    }
}