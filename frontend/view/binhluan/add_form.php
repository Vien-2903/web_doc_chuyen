<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/cover_image_helper.php'; ?>

<h1>Viết bình luận: <?php echo htmlspecialchars($truyen['ten_truyen']); ?></h1>

<div style="margin-bottom: 20px;">
    <a href="index.php?page=admin&controller=binhluan" class="btn-action">← Quay lại danh sách</a>
    <a href="index.php?page=admin&controller=binhluan&action=viewComments&id_truyen=<?php echo $truyen['id']; ?>" 
       class="btn-action"> Xem bình luận</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert-message alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
    <div class="alert-message alert-success">
        Thêm bình luận thành công! 
        <a href="index.php?page=admin&controller=binhluan&action=viewComments&id_truyen=<?php echo $truyen['id']; ?>" 
           style="color: #155724; font-weight: bold; text-decoration: underline;">
            Xem bình luận
        </a>
    </div>
<?php endif; ?>

<!-- THÔNG TIN TRUYỆN -->
<div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="display: flex; gap: 20px;">
        <?php if ($truyen['anh_bia']): ?>
              <img src="<?php echo htmlspecialchars(resolve_cover_image_url($truyen['anh_bia'])); ?>"
                 alt="Ảnh bìa" 
                  style="width: 150px; height: 200px; object-fit: cover; border-radius: 8px;"
                  onerror="this.src='https://via.placeholder.com/150x200/2c3e50/ffffff?text=No+Image'">
        <?php endif; ?>
        
        <div>
            <h2 style="margin: 0 0 10px 0;"><?php echo htmlspecialchars($truyen['ten_truyen']); ?></h2>
            <p style="margin: 5px 0; color: #666;">
                <strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($truyen['mo_ta'])); ?>
            </p>
            <p style="margin: 5px 0; color: #666;">
                <strong>Trạng thái:</strong> 
                <?php if ($truyen['trang_thai'] == 'dang_ra'): ?>
                    <span class="status-badge status-active">Đang ra</span>
                <?php else: ?>
                    <span class="status-badge status-inactive">Hoàn thành</span>
                <?php endif; ?>
            </p>
            <p style="margin: 5px 0; color: #666;">
                <strong>Tổng số bình luận:</strong> 
                <span style="color: #4285f4; font-weight: bold;">
                    💬 <?php echo $total_comments; ?>
                </span>
            </p>
        </div>
    </div>
</div>

<!-- FORM VIẾT BÌNH LUẬN -->
<div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0;"> Viết bình luận mới</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Chọn chương: <span style="color: red;">*</span></label>
            <select name="id_chuong" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">-- Chọn chương --</option>
                <?php foreach ($chuongs as $chuong): ?>
                    <option value="<?php echo $chuong['id']; ?>">
                        Chương <?php echo $chuong['so_chuong']; ?>
                        <?php if ($chuong['tieu_de']): ?>
                            - <?php echo htmlspecialchars($chuong['tieu_de']); ?>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color: #666; display: block; margin-top: 5px;">
                 Chọn chương mà bạn muốn bình luận
            </small>
        </div>

        <div class="form-group">
            <label>Nội dung bình luận: <span style="color: red;">*</span></label>
            <textarea name="noi_dung" 
                      rows="10" 
                      required 
                      placeholder="Nhập bình luận của bạn về truyện này..."
                      style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
            <small style="color: #666; display: block; margin-top: 5px;">
                 Tối thiểu 10 ký tự
            </small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn-action">
                 Gửi bình luận
            </button>
            <a href="index.php?page=admin&controller=binhluan" class="btn-action">
                ← Hủy
            </a>
        </div>
    </form>
</div>

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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

/* ============= ĐÃ SỬA: TẤT CẢ CÁC NÚT ============= */
.btn-action {
    background-color: white !important;
    color: #5b9bd5 !important;
    padding: 12px 30px !important;
    border: 2px solid #5b9bd5 !important;
    border-radius: 4px !important;
    text-decoration: none !important;
    font-size: 16px !important;
    display: inline-block !important;
    cursor: pointer !important;
    transition: all 0.3s !important;
}

.btn-action:hover {
    background-color: #5b9bd5 !important;
    color: white !important;
}
/* ============= KẾT THÚC ============= */
</style>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>