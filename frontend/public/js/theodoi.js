const followedStoryIds = new Set();
let listenersBound = false;
let refreshTimeout = null;

function ensureToastContainer() {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function showToast(message, type = 'success') {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 260);
    }, 2200);
}

function redirectToLogin() {
    window.location.href = '/web_doc_truyen/frontend/view/log/login.html';
}

async function loadFollowedIds() {
    followedStoryIds.clear();

    try {
        const response = await fetch('/web_doc_truyen/backend/api/theo_doi_truyen/get_by_user_theodoi_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({})
        });

        if (response.status === 401) {
            localStorage.removeItem('followedStoryIds');
            return;
        }

        const data = await response.json();
        if (response.ok && data.success && Array.isArray(data.data)) {
            data.data.forEach((item) => {
                const idValue = Number(item.id || item.id_truyen || 0);
                if (idValue) {
                    followedStoryIds.add(idValue);
                }
            });
            localStorage.setItem('followedStoryIds', JSON.stringify(Array.from(followedStoryIds)));
        }
    } catch (error) {
        console.warn('Không tải được danh sách theo dõi:', error);
    }
}

function updateFollowButton(button, isFollowing) {
    button.classList.toggle('is-following', isFollowing);
    button.classList.toggle('not-following', !isFollowing);
    button.textContent = isFollowing ? 'Đang theo dõi' : 'Theo dõi';
    button.title = isFollowing ? 'Bỏ theo dõi truyện này' : 'Theo dõi truyện này';
    button.setAttribute('aria-label', button.title);
}

function updateAllFollowButtons() {
    document.querySelectorAll('.follow-card-button').forEach((button) => {
        const storyId = Number(button.dataset.storyId || 0);
        if (!storyId) return;
        updateFollowButton(button, followedStoryIds.has(storyId));
    });
}

function setFollowedFromServerList(list) {
    followedStoryIds.clear();
    if (Array.isArray(list)) {
        list.forEach((item) => {
            const id = Number(item.id || item.id_truyen || 0);
            if (id) followedStoryIds.add(id);
        });
    }

    localStorage.setItem('followedStoryIds', JSON.stringify(Array.from(followedStoryIds)));
    localStorage.setItem('followListUpdatedAt', String(Date.now()));
    updateAllFollowButtons();
}

function bindFollowButtons() {
    document.querySelectorAll('.follow-card-button').forEach((button) => {
        if (button.dataset.boundFollowClick === '1') {
            return;
        }

        button.addEventListener('click', async (event) => {
            event.preventDefault();
            event.stopPropagation();

            const storyId = Number(button.dataset.storyId || 0);
            if (!storyId) {
                showToast('Thiếu ID truyện để theo dõi.', 'error');
                return;
            }

            const isFollowingNow = button.classList.contains('is-following');
            const confirmed = window.confirm(
                isFollowingNow
                    ? 'Bạn có đồng ý bỏ theo dõi truyện này không?'
                    : 'Bạn có đồng ý theo dõi truyện này không?'
            );
            if (!confirmed) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch('/web_doc_truyen/backend/api/theo_doi_truyen/toggle_theodoi_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ id_truyen: storyId })
                });

                if (response.status === 401) {
                    window.alert('Bạn cần đăng nhập để theo dõi truyện.');
                    redirectToLogin();
                    return;
                }

                const data = await response.json();
                if (response.ok && data.success) {
                    const nowFollowing = Boolean(data.is_following);

                    setFollowedFromServerList(data.following);
                    updateFollowButton(button, nowFollowing);
                    showToast(nowFollowing ? 'Đã theo dõi truyện.' : 'Đã bỏ theo dõi truyện.', 'success');

                    window.dispatchEvent(new CustomEvent('followingUpdated', {
                        detail: {
                            action: data.action,
                            storyId,
                            following: data.following,
                            message: data.message
                        }
                    }));
                } else {
                    showToast(data.message || 'Không thể thay đổi trạng thái theo dõi.', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi toggle theo dõi:', error);
                showToast('Lỗi mạng khi thay đổi theo dõi.', 'error');
            } finally {
                button.disabled = false;
            }
        });

        button.dataset.boundFollowClick = '1';
    });
}

function addFollowButtons() {
    document.querySelectorAll('.story-card').forEach((card) => {
        const storyId = card.dataset.storyId || 0;
        if (!storyId) return;

        const isFollowing = followedStoryIds.has(Number(storyId));
        let followBtn = card.querySelector('.follow-card-button');

        if (!followBtn) {
            followBtn = document.createElement('button');
            followBtn.type = 'button';
            followBtn.className = 'follow-card-button';
            followBtn.dataset.storyId = storyId;
            card.appendChild(followBtn);
        }

        updateFollowButton(followBtn, isFollowing);
    });

    bindFollowButtons();
}

function scheduleFollowRefresh() {
    if (refreshTimeout) {
        clearTimeout(refreshTimeout);
    }

    refreshTimeout = setTimeout(async () => {
        await loadFollowedIds();
        addFollowButtons();
        updateAllFollowButtons();
        refreshTimeout = null;
    }, 120);
}

function bindAutoRefreshEvents() {
    if (listenersBound) {
        return;
    }

    window.addEventListener('storage', function (event) {
        if (event.key === 'followListUpdatedAt' || event.key === 'followedStoryIds') {
            scheduleFollowRefresh();
        }
    });

    window.addEventListener('pageshow', function () {
        scheduleFollowRefresh();
    });

    window.addEventListener('focus', function () {
        scheduleFollowRefresh();
    });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            scheduleFollowRefresh();
        }
    });

    window.addEventListener('followingUpdated', function () {
        scheduleFollowRefresh();
    });

    listenersBound = true;
}

async function initializeTheoDoiTruyen() {
    await loadFollowedIds();
    addFollowButtons();
    updateAllFollowButtons();
    bindAutoRefreshEvents();
}
