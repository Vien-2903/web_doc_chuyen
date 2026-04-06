<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/cover_image_helper.php'; ?>

<h1>Bình luận: <?php echo htmlspecialchars($truyen['ten_truyen']); ?></h1>

<div style="margin-bottom: 20px;">
    <a href="index.php?page=binhluan&action=index" class="btn-action">← Quay lại danh sách</a>
    <a href="index.php?page=binhluan&action=addForm&id_truyen=<?php echo $truyen['id']; ?>" 
       class="btn-action">✍️ Viết bình luận</a>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert-message alert-success">
        <?php
            if($_GET['success'] == 'edit') echo '✓ Cập nhật bình luận thành công!';
            elseif($_GET['success'] == 'delete') echo '✓ Xóa bình luận thành công!';
            elseif($_GET['success'] == 'added') echo '✓ Thêm bình luận thành công!';
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert-message alert-error">
        ⚠️ Có lỗi xảy ra! Vui lòng thử lại.
    </div>
<?php endif; ?>

<!-- THÔNG TIN TRUYỆN -->
<div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="display: flex; gap: 20px;">
        <?php if ($truyen['anh_bia']): ?>
              <img src="<?php echo htmlspecialchars(resolve_cover_image_url($truyen['anh_bia'])); ?>"
                 alt="Ảnh bìa" 
                  style="width: 120px; height: 160px; object-fit: cover; border-radius: 8px;"
                  onerror="this.src='https://via.placeholder.com/120x160/2c3e50/ffffff?text=No+Image'">
        <?php endif; ?>
        
        <div>
            <h2 style="margin: 0 0 10px 0;"><?php echo htmlspecialchars($truyen['ten_truyen']); ?></h2>
            <p style="margin: 5px 0; color: #666;">
                <strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($truyen['mo_ta'])); ?>
            </p>
            <p style="margin: 5px 0; color: #666;">
                <strong>Tổng số bình luận:</strong> 
                <span style="color: #4285f4; font-weight: bold;">
                    💬 <?php echo count($comments); ?>
                </span>
            </p>
        </div>
    </div>
</div>

<!-- DANH SÁCH BÌNH LUẬN -->
<div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0; margin-bottom: 25px; color: #2c3e50;">📝 Danh Sách Bình Luận</h2>
    
    <?php if (empty($comments)): ?>
        <div style="text-align: center; padding: 60px 20px; color: #999;">
            <div style="font-size: 80px; margin-bottom: 15px; opacity: 0.5;">💬</div>
            <h3 style="color: #666; font-size: 20px; margin-bottom: 10px;">
                Chưa có bình luận nào
            </h3>
            <p style="color: #999; font-size: 14px;">
                Hãy là người đầu tiên bình luận về truyện này!
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <?php
            $commentHighlightStyle = '';
            if ($comment['id_nguoidung'] == $_SESSION['user']['id']) {
                $commentHighlightStyle = ' border-left: 4px solid #3498db; background-color: #eef065;';
            }
            ?>
            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;<?= $commentHighlightStyle ?>">
                
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0;">
                    <div>
                        <span style="font-weight: bold; color: #2c3e50; font-size: 15px;">
                            👤 <?php echo htmlspecialchars($comment['ten_dang_nhap']); ?>
                            <?php if ($comment['id_nguoidung'] == $_SESSION['user']['id']): ?>
                                <span style="color: #3498db; font-size: 13px; font-weight: normal;">(Bạn)</span>
                            <?php endif; ?>
                        </span>
                        <span style="display: inline-block; margin-left: 15px; padding: 4px 10px; background: #ecf0f1; 
                                     color: #7f8c8d; border-radius: 12px; font-size: 12px;">
                            📍 Chương <?php echo $comment['so_chuong']; ?>
                            <?php if ($comment['tieu_de_chuong']): ?>
                                - <?php echo htmlspecialchars($comment['tieu_de_chuong']); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <span style="color: #95a5a6; font-size: 13px;">
                        🕒 <?php echo date('d/m/Y H:i', strtotime($comment['ngay_tao'])); ?>
                    </span>
                </div>
                
                <!-- Content -->
                <div style="margin: 15px 0; color: #34495e; line-height: 1.6; font-size: 14px;">
                    <?php echo nl2br(htmlspecialchars($comment['noi_dung'])); ?>
                </div>
                
                <!-- Actions -->
                <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                    <?php
                    // Xác định URL theo vai trò
                    $isAdmin = $_SESSION['user']['vai_tro'] === 'admin';
                    $isOwner = $comment['id_nguoidung'] == $_SESSION['user']['id'];
                    
                    // URL cho nút Sửa và Xóa
                    if ($isAdmin) {
                        $editUrl = "index.php?page=admin&controller=binhluan&action=edit&id=" . $comment['id'];
                        $deleteUrl = "index.php?page=admin&controller=binhluan&action=delete&id=" . $comment['id'];
                    } else {
                        $editUrl = "index.php?page=binhluan&action=edit&id=" . $comment['id'];
                        $deleteUrl = "index.php?page=binhluan&action=delete&id=" . $comment['id'];
                    }
                    ?>
                    
                    <!-- NÚT SỬA: Chỉ hiện cho người đăng bình luận -->
                    <?php if ($isOwner): ?>
                        <a href="<?php echo $editUrl; ?>" 
                           class="btn-action" 
                           style="font-size: 13px; padding: 6px 14px;">
                            ✏️ Sửa
                        </a>
                    <?php endif; ?>
                    
                    <!-- NÚT XÓA: 
                         - ADMIN: Hiện trên TẤT CẢ bình luận
                         - USER: Chỉ hiện trên bình luận của mình
                    -->
                    <?php if ($isAdmin || $isOwner): ?>
                        <a href="<?php echo $deleteUrl; ?>" 
                           class="btn-action btn-delete" 
                           style="font-size: 13px; padding: 6px 14px;"
                           onclick="return confirm('Bạn có chắc muốn xóa bình luận này?\n\nNgười đăng: <?php echo htmlspecialchars($comment['ten_dang_nhap']); ?>\nNgày tạo: <?php echo date('d/m/Y H:i', strtotime($comment['ngay_tao'])); ?>')">
                            🗑️ Xóa
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.alert-message {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-size: 14px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.btn-action {
    background-color: white !important;
    color: #5b9bd5 !important;
    padding: 10px 20px !important;
    border: 2px solid #5b9bd5 !important;
    border-radius: 4px !important;
    text-decoration: none !important;
    font-size: 14px !important;
    display: inline-block !important;
    cursor: pointer !important;
    transition: all 0.3s !important;
}

.btn-action:hover {
    background-color: #5b9bd5 !important;
    color: white !important;
}

/* Nút Xóa màu đỏ */
.btn-delete {
    color: #e74c3c !important;
    border-color: #e74c3c !important;
}

.btn-delete:hover {
    background-color: #e74c3c !important;
    color: white !important;
}
</style>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>