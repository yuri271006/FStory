/**
 * CẤU HÌNH ĐƯỜNG DẪN API
 */
const API_ENDPOINT = 'page/me/func/post/post'; 

// Cấu hình Toast thông báo nhỏ góc phải (Thay cho alert thành công)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. XỬ LÝ TEXTAREA TỰ GIÃN NỞ ---
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('auto-expand')) {
            e.target.style.height = 'auto'; 
            e.target.style.height = (e.target.scrollHeight) + 'px';
        }
    });

    // --- 2. ĐÓNG MENU KHI CLICK RA NGOÀI ---
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.post-options')) {
            document.querySelectorAll('.dropdown-menu').forEach(el => el.classList.remove('show'));
        }
    });

    // --- 3. LOGIC ĐĂNG BÀI ---
    const btnPost = document.getElementById('btnPostSubmit');
    if (btnPost) {
        btnPost.addEventListener('click', function() {
            const contentBox = document.getElementById('postContent');
            const content = contentBox.value.trim();

            if (!content) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa có nội dung',
                    text: 'Bạn hãy viết gì đó trước khi đăng nhé!',
                    confirmButtonColor: 'var(--primary)'
                });
                return;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đăng...';
            this.disabled = true;

            const formData = new FormData();
            formData.append('action', 'post_status');
            formData.append('content', content);

            fetch(API_ENDPOINT, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    const noMsg = document.getElementById('no-activity-msg');
                    if (noMsg) noMsg.remove();

                    const feed = document.getElementById('activity-feed');
                    feed.insertAdjacentHTML('afterbegin', data.html);

                    contentBox.value = '';
                    contentBox.style.height = 'auto';
                    togglePostBox(false);

                    // Thông báo thành công nhẹ nhàng
                    Toast.fire({ icon: 'success', title: 'Đã đăng bài viết mới' });
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ', 'error');
            })
            .finally(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    }
});

// --- CÁC HÀM GLOBAL ---

function togglePostBox(show) {
    const collapsed = document.getElementById('post-collapsed');
    const expanded = document.getElementById('post-expanded');
    if (!collapsed || !expanded) return;

    if (show) {
        collapsed.style.display = 'none';
        expanded.style.display = 'block';
        document.getElementById('postContent').focus();
    } else {
        collapsed.style.display = 'flex';
        expanded.style.display = 'none';
    }
}

function toggleMenu(id) {
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        if(el.id !== 'menu-' + id) el.classList.remove('show');
    });
    const menu = document.getElementById('menu-' + id);
    if(menu) menu.classList.toggle('show');
}

function likePost(id, btn) {
    btn.classList.toggle('liked'); 
    const countSpan = document.getElementById('like-count-' + id);
    
    const formData = new FormData();
    formData.append('action', 'toggle_like');
    formData.append('id', id);

    fetch(API_ENDPOINT, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status) {
            if(data.new_count > 0) {
                countSpan.innerHTML = `<i class="fa-solid fa-thumbs-up" style="color:var(--primary)"></i> ${data.new_count}`;
            } else {
                countSpan.innerHTML = '';
            }
        } else {
            Swal.fire('Lỗi', data.message, 'error');
            btn.classList.toggle('liked'); 
        }
    });
}

// --- XÓA BÀI VIẾT (Thay confirm bằng Swal) ---
function deletePost(id) {
    Swal.fire({
        title: 'Bạn chắc chắn chứ?',
        text: "Bài viết sẽ bị xóa vĩnh viễn và không thể khôi phục!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', // Màu đỏ cảnh báo
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Vâng, xóa nó!',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_post');
            formData.append('id', id);

            fetch(API_ENDPOINT, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status) {
                    const item = document.getElementById('activity-' + id);
                    if(item) {
                        item.style.transition = '0.3s';
                        item.style.opacity = '0';
                        setTimeout(() => item.remove(), 300);
                        Toast.fire({ icon: 'success', title: 'Đã xóa bài viết' });
                    }
                } else {
                    Swal.fire('Không thể xóa', data.message, 'error');
                }
            });
        }
    });
}

function toggleComment(id) {
    const section = document.getElementById('cmt-section-' + id);
    if (section) {
        section.style.display = (section.style.display === 'none' || section.style.display === '') ? 'block' : 'none';
    }
}

function submitComment(e, id) {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        const content = e.target.value.trim();
        if(!content) return;

        const formData = new FormData();
        formData.append('action', 'post_comment');
        formData.append('id', id);
        formData.append('content', content);

        fetch(API_ENDPOINT, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status) {
                const list = document.getElementById('cmt-list-' + id);
                // Vì không xác định được parent wrapper ở đây, ta thêm vào cuối list
                // (Code HTML đã update ở bước trước để handle hiển thị)
                list.insertAdjacentHTML('beforeend', `
                   <div class="comment-wrapper">
                        <div class="comment-item" style="display:flex; gap:10px;">
                            <img src="${data.avatar}" style="width:30px; height:30px; border-radius:50%; object-fit: cover;">
                            <div>
                                <div class="comment-content">
                                    <div style="font-weight:700; font-size:0.9rem;">${data.nickname}</div>
                                    <div class="text-content">${data.content}</div>
                                </div>
                                <div class="comment-actions">
                                    <span class="btn-reply">Vừa xong</span>
                                </div>
                            </div>
                        </div>
                   </div>
                `);
                e.target.value = '';
                e.target.style.height = 'auto';
            }
        });
    }
}

// --- XÓA BÌNH LUẬN (Thay confirm bằng Swal) ---
function deleteComment(cmtId) {
    Swal.fire({
        title: 'Xóa bình luận?',
        text: "Bạn có chắc muốn xóa bình luận này không?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_comment');
            formData.append('id', cmtId);

            fetch(API_ENDPOINT, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status) {
                    let el = document.getElementById('comment-wrapper-' + cmtId); 
                    if (!el) el = document.getElementById('reply-item-' + cmtId);
                    if (el) el.remove();
                    Toast.fire({ icon: 'success', title: 'Đã xóa bình luận' });
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

function enableEditComment(cmtId) {
    const contentBox = document.querySelector(`#cmt-content-${cmtId} .text-content`);
    const originalText = contentBox.innerText;
    
    contentBox.innerHTML = `
        <input type="text" id="edit-input-${cmtId}" value="${originalText}" 
               class="comment-input" style="width:100%; min-width: 200px; padding: 5px; border-radius: 10px; border: 1px solid var(--primary);"
               onkeydown="handleEditKey(event, ${cmtId}, '${originalText}')">
        <div style="font-size:0.7rem; color:gray; margin-top:2px;">Enter để lưu, Esc để hủy</div>
    `;
    
    document.getElementById('menu-cmt-' + cmtId).classList.remove('show');
    const input = document.getElementById('edit-input-' + cmtId);
    input.focus();
    // Di chuyển con trỏ chuột về cuối
    input.setSelectionRange(input.value.length, input.value.length);
}

function handleEditKey(e, cmtId, oldText) {
    if (e.key === 'Escape') {
        document.querySelector(`#cmt-content-${cmtId} .text-content`).innerText = oldText;
    } else if (e.key === 'Enter') {
        const newText = e.target.value.trim();
        if (!newText) return;

        const formData = new FormData();
        formData.append('action', 'edit_comment');
        formData.append('id', cmtId);
        formData.append('content', newText);

        fetch(API_ENDPOINT, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status) {
                document.querySelector(`#cmt-content-${cmtId} .text-content`).innerText = newText;
                Toast.fire({ icon: 'success', title: 'Đã cập nhật bình luận' });
            } else {
                Swal.fire('Lỗi', data.message, 'error');
                document.querySelector(`#cmt-content-${cmtId} .text-content`).innerText = oldText;
            }
        });
    }
}

// Logic Sửa bài viết (Inline, không cần alert)
function editPost(id) {
    const contentDiv = document.getElementById('content-' + id);
    const currentText = contentDiv.innerText;
    contentDiv.setAttribute('data-original', currentText); 
    
    contentDiv.innerHTML = `
        <textarea id="edit-area-${id}" class="auto-expand" style="width:100%; border:1px solid var(--primary); padding:10px; border-radius:5px; background: transparent; color: inherit; font-family: inherit;">${currentText}</textarea>
        <div style="text-align:right; margin-top:5px;">
            <button onclick="cancelEdit(${id})" class="icon-btn-sm" style="margin-right:10px; cursor:pointer;">Hủy</button>
            <button onclick="saveEdit(${id})" class="btn-write" style="background:var(--primary); color:white; padding:5px 15px; border:none; border-radius:4px; cursor:pointer;">Lưu</button>
        </div>
    `;
    
    const ta = document.getElementById('edit-area-' + id);
    ta.style.height = ta.scrollHeight + 'px';
    ta.focus();
}

function cancelEdit(id) {
    const contentDiv = document.getElementById('content-' + id);
    const originalText = contentDiv.getAttribute('data-original');
    contentDiv.innerText = originalText;
}

function saveEdit(id) {
    const newContent = document.getElementById('edit-area-' + id).value.trim();
    if(!newContent) {
        Swal.fire('Lỗi', 'Nội dung không được để trống', 'warning');
        return;
    }

    const contentDiv = document.getElementById('content-' + id);
    contentDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

    const formData = new FormData();
    formData.append('action', 'edit_post');
    formData.append('id', id);
    formData.append('content', newContent);

    fetch(API_ENDPOINT, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status) {
            contentDiv.innerText = newContent;
            Toast.fire({ icon: 'success', title: 'Đã cập nhật bài viết' });
        } else {
            Swal.fire('Lỗi', data.message, 'error');
            cancelEdit(id);
        }
    });
}

// --- SHOW LIKE MODAL ---
function showLikers(actId) {
    const modal = document.getElementById('likesModal');
    const content = document.getElementById('likerListContent');
    modal.style.display = 'flex';
    content.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';

    const formData = new FormData();
    formData.append('action', 'get_likers');
    formData.append('id', actId);

    fetch(API_ENDPOINT, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status && data.likers.length > 0) {
            let html = '';
            data.likers.forEach(user => {
                html += `
                <div class="liker-item">
                    <img src="src/avt/${user.avatar || 'default_avt.png'}" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <div style="font-weight:600;">${user.nickname}</div>
                </div>`;
            });
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div style="text-align:center; padding:20px; color:gray;">Chưa có ai thích bài viết này.</div>';
        }
    });
}

function closeLikeModal() {
    document.getElementById('likesModal').style.display = 'none';
}
document.getElementById('likesModal').addEventListener('click', function(e){
    if(e.target === this) closeLikeModal();
});

// --- REPLY COMMENT ---
function openReplyBox(cmtId) {
    const box = document.getElementById('reply-box-' + cmtId);
    if(box.style.display === 'block') {
        box.style.display = 'none';
    } else {
        document.querySelectorAll('.reply-input-box').forEach(b => b.style.display = 'none');
        box.style.display = 'block';
        box.querySelector('input').focus();
    }
}

function showAllComments(actId, btn) {
    const section = document.getElementById('cmt-list-' + actId);
    const hiddenItems = section.querySelectorAll('.hidden-comment');
    hiddenItems.forEach(item => item.classList.remove('hidden-comment'));
    btn.style.display = 'none';
}

function replyToChild(parentId, childName) {
    const box = document.getElementById('reply-box-' + parentId);
    const input = box.querySelector('input');
    box.style.display = 'block';
    input.value = `@${childName} `;
    input.focus();
}

function submitReply(e, actId, parentId) {
    if(e.key === 'Enter') {
        e.preventDefault();
        const content = e.target.value.trim();
        if(!content) return;

        const formData = new FormData();
        formData.append('action', 'post_comment');
        formData.append('id', actId);
        formData.append('content', content);
        formData.append('parent_id', parentId); 

        fetch(API_ENDPOINT, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status) {
                const parentBox = document.getElementById('reply-box-' + parentId).parentElement;
                let replyList = parentBox.querySelector('.reply-list');
                
                if(!replyList) {
                    replyList = document.createElement('div');
                    replyList.className = 'reply-list';
                    parentBox.insertBefore(replyList, document.getElementById('reply-box-' + parentId));
                }

                replyList.insertAdjacentHTML('beforeend', `
                    <div class="reply-item" id="reply-item-${data.id}" style="display:flex; gap:10px;">
                        <img src="${data.avatar}" style="width:24px; height:24px; border-radius:50%; object-fit:cover;">
                        <div class="comment-content">
                            <div style="font-weight:700; font-size:0.85rem;">${data.nickname}</div>
                            <div class="text-content">${data.content}</div>
                        </div>
                    </div>
                `);
                e.target.value = '';
                document.getElementById('reply-box-' + parentId).style.display = 'none';
            }
        });
    }
}