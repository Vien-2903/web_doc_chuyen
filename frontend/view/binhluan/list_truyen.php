<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/cover_image_helper.php'; ?>

<h1>Quản lý Bình luận</h1>

<div style="margin-bottom: 20px;">
    <a href="index.php?page=user&controller=home" class="btn-action">← Quay lại</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh bìa</th>
            <th>Tên truyện</th>
            <th>Tác giả</th>
            <th>Trạng thái</th>
            <th>Số bình luận</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($truyens)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    <?php if ($_SESSION['user']['vai_tro'] === 'admin'): ?>
                        Chưa có truyện nào
                    <?php else: ?>
                        Bạn chưa bình luận truyện nào
                    <?php endif; ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($truyens as $truyen): ?>
                <tr>
                    <td><?php echo $truyen['id']; ?></td>
                    <td>
                        <?php if ($truyen['anh_bia']): ?>
                            <img src="<?php echo htmlspecialchars(resolve_cover_image_url($truyen['anh_bia'])); ?>"
                                 alt="Ảnh bìa" 
                                 style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px;"
                                 onerror="this.src='https://via.placeholder.com/60x80/2c3e50/ffffff?text=No+Image'">
                        <?php else: ?>
                            <span style="color: #999;">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($truyen['ten_truyen']); ?></td>
                    <td>
                        <?php 
                        echo $truyen['but_danh'] 
                            ? htmlspecialchars($truyen['but_danh']) 
                            : htmlspecialchars($truyen['ten_tacgia']); 
                        ?>
                    </td>
                    <td>
                        <?php if ($truyen['trang_thai'] == 'dang_ra'): ?>
                            <span class="status-badge status-active">Đang ra</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">Hoàn thành</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="font-weight: bold; color: #4285f4;">
                            💬 <?php 
                                // Hiển thị số bình luận tùy vai trò
                                if ($_SESSION['user']['vai_tro'] === 'admin') {
                                    echo $truyen['total_comments'];
                                } else {
                                    echo $truyen['my_comments'];
                                }
                            ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?page=binhluan&action=viewComments&id_truyen=<?php echo $truyen['id']; ?>" 
                           class="btn-action">
                            👁️ Xem
                        </a>
                        <a href="index.php?page=binhluan&action=addForm&id_truyen=<?php echo $truyen['id']; ?>" 
                           class="btn-action">
                            ✍️ Viết
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #cce5ff;
    color: #004085;
}

.btn-action {
    background-color: white !important;
    color: #5b9bd5 !important;
    padding: 8px 16px !important;
    border: 2px solid #5b9bd5 !important;
    border-radius: 4px !important;
    text-decoration: none !important;
    font-size: 14px !important;
    display: inline-block !important;
    cursor: pointer !important;
    transition: all 0.3s !important;
    margin: 0 2px !important;
}

.btn-action:hover {
    background-color: #5b9bd5 !important;
    color: white !important;
}
</style>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>