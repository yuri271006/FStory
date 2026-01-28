// 1. Toast Notification
function showToast(message, type = 'success') {
    let container = document.querySelector('.f-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'f-toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `f-toast ${type}`;
    toast.innerHTML = `<span style="font-weight:600; font-size:0.9rem;">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// 2. Custom Modal
function confirmModal(title, message, callback, isDanger = false) {
    if (!document.getElementById('fModalOverlay')) {
        const html = `
        <div class="f-modal-overlay" id="fModalOverlay">
            <div class="f-modal">
                <h3 id="fModalTitle"></h3>
                <p id="fModalMsg"></p>
                <div class="f-modal-actions">
                    <button class="btn-modal btn-cancel" onclick="closeModal()">Hủy</button>
                    <button class="btn-modal btn-confirm" id="fModalConfirm">Đồng ý</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
    }
    
    const overlay = document.getElementById('fModalOverlay');
    const confirmBtn = document.getElementById('fModalConfirm');
    
    document.getElementById('fModalTitle').innerText = title;
    document.getElementById('fModalMsg').innerText = message;
    
    if (isDanger) confirmBtn.classList.add('danger');
    else confirmBtn.classList.remove('danger');
    
    overlay.classList.add('open');
    
    // Xóa event cũ để tránh click đúp
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    document.getElementById('fModalConfirm').onclick = () => {
        callback();
        closeModal();
    };
}

function closeModal() {
    const overlay = document.getElementById('fModalOverlay');
    if(overlay) overlay.classList.remove('open');
}