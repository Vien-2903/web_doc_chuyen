<?php
$pageTitle = 'Lịch Sử Đọc - Web Đọc Truyện';
require_once __DIR__ . '/../layouts/cover_image_helper.php';
?>

<?php include(__DIR__ . '/../layouts/user/header.html'); ?>

<link rel="stylesheet" href="/web_doc_truyen/frontend/public/css/luutrangdoc.css?v=<?= time() ?>">

<main class="container">
    <div class="lich-su-doc-wrapper">
        <!-- Tiêu đề -->
        <div class="page-header">
            <h2>📖 Lịch Sử Đọc Của Bạn</h2>
            <p class="subtitle">Danh sách các truyện bạn đã và đang đọc</p>
        </div>

        <!-- Thống kê -->
        <div class="lich-su-stats">
            <div class="stat-item">
                <span class="stat-number"><?= count($lichSuDoc ?? []) ?></span>
                <span class="stat-label">Truyện đang đọc</span>
            </div>
        </div>

        <!-- Nút xóa tất cả -->
        <?php if (!empty($lichSuDoc)): ?>
        <div class="action-bar">
            <form method="POST" action="/web_doc_truyen/frontend/public/index.php?page=lichsudoc&action=xoa_tat_ca" 
                  onsubmit="return confirm('Bạn có chắc muốn xóa TẤT CẢ lịch sử đọc?')">
                <button type="submit" class="btn-xoa-tat-ca">
                    🗑️ Xóa Tất Cả Lịch Sử
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Danh sách truyện -->
        <div class="lich-su-list">
            <?php if (!empty($lichSuDoc)): ?>
                <?php foreach ($lichSuDoc as $item): ?>
                <div class="lich-su-item">
                    <!-- Ảnh bìa -->
                    <div class="item-image">
                        <a href="/web_doc_truyen/frontend/public/index.php?page=chitiet&id=<?= $item['id_truyen'] ?>">
                            <img src="<?= htmlspecialchars(resolve_cover_image_url($item['anh_bia'] ?? '', 'https://via.placeholder.com/180x240/2c3e50/ffffff?text=No+Image')) ?>"
                                alt="<?= htmlspecialchars($item['ten_truyen']) ?>"
                                onerror="this.src='https://via.placeholder.com/180x240/2c3e50/ffffff?text=No+Image'">
                        </a>
                    </div>



                    <!-- Thông tin -->
                    <div class="item-info">
                        <h3 class="item-title">
                            <a href="/web_doc_truyen/frontend/public/index.php?page=chitiettruyen&id=<?= $item['id_truyen'] ?>">
                                <?= htmlspecialchars($item['ten_truyen']) ?>
                            </a>
                        </h3>
                        
                        <div class="item-reading-info">
                            <p class="reading-position">
                                📍 Đang đọc: 
                                <strong>Chương <?= $item['so_chuong'] ?></strong>
                                <?php if (!empty($item['ten_chuong'])): ?>
                                    - <?= htmlspecialchars($item['ten_chuong']) ?>
                                <?php endif; ?>
                                <strong class="page-number">- Trang <?= $item['so_trang'] ?></strong>
                            </p>
                            
                            <p class="reading-time">
                                🕒 Đọc lần cuối: <?= date('d/m/Y H:i', strtotime($item['ngay_cap_nhat'])) ?>
                            </p>

                            <?php if (isset($item['phan_tram']) && $item['phan_tram'] > 0): ?>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $item['phan_tram'] ?>%"></div>
                                <span class="progress-text"><?= $item['phan_tram'] ?>%</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Nút hành động -->
                        <div class="item-actions">
                            <a href="/web_doc_truyen/frontend/public/index.php?page=danhsachtrang&id=<?= $item['id_chuong'] ?>" 
                               class="btn-continue">
                                ▶️ Đọc Tiếp
                            </a>
                            <form method="POST" 
                                  action="/web_doc_truyen/frontend/public/index.php?page=lichsudoc&action=xoa" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa lịch sử đọc truyện <?= htmlspecialchars($item['ten_truyen']) ?>?')">
                                <input type="hidden" name="id_truyen" value="<?= $item['id_truyen'] ?>">
                                <button type="submit" class="btn-delete">
                                    🗑️ Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Trống -->
                <div class="empty-state">
                    <div class="empty-icon">📚</div>
                    <h3>Chưa có lịch sử đọc</h3>
                    <p>Bạn chưa đọc truyện nào. Hãy bắt đầu khám phá thư viện của chúng tôi!</p>
                    <a href="/web_doc_truyen/frontend/public/index.php" class="btn-primary">
                        🏠 Về Trang Chủ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>