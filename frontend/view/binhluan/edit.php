<?php include __DIR__ . '/../layouts/admin/header.php'; ?>

<h1>Sửa bình luận</h1>

<?php if(isset($error)): ?>
    <div class="alert-message alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" style="max-width: 800px; margin: 0 auto;">
    <div class="form-group">
        <label>Người đăng:</label>
        <input type="text" 
               value="<?php echo htmlspecialchars($comment['ten_dang_nhap']); ?>" 
               readonly 
               style="background-color: #f0f0f0;">
    </div>

    <div class="form-group">
        <label>Ngày tạo:</label>
        <input type="text" 
               value="<?php echo date('d/m/Y H:i', strtotime($comment['ngay_tao'])); ?>" 
               readonly 
               style="background-color: #f0f0f0;">
    </div>

    <div class="form-group">
        <label>Nội dung bình luận: <span style="color: red;">*</span></label>
        <textarea name="noi_dung" 
                  rows="8" 
                  required 
                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"><?php echo htmlspecialchars($comment['noi_dung']); ?></textarea>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button type="submit" class="btn-action">
            💾 Lưu thay đổi
        </button>
        <a href="javascript:history.back()" class="btn-action">
            ← Hủy
        </a>
    </div>
</form>

<!-- ============= ĐÃ SỬA: STYLE NÚT ============= -->
<style>
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
</style>
<!-- ============= KẾT THÚC ============= -->

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>