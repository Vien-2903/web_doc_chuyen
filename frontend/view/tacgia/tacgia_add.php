<?php include __DIR__ . '/../layouts/admin/header.php'; ?>

<a href="index.php?page=admin&controller=tacgia&action=index" class="btn-home">← Quay lại danh sách</a>

<h1 class="form-title">Thêm tác giả mới</h1>
<?php if(!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>⚠️ Có lỗi xảy ra:</strong>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            <?php foreach($errors as $error): ?>
                <li style="margin-bottom: 5px;"><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="POST" action="index.php?page=admin&controller=tacgia&action=add" class="user-form">
    <table>
        <tr>
            <td><label for="ten_tacgia">Tên tác giả: <span style="color: red;">*</span></label></td>
        </tr>
        <tr>
            <td>
                <input type="text" 
                        id="ten_tacgia" 
                        name="ten_tacgia" 
                        value="<?php echo isset($_POST['ten_tacgia']) ? htmlspecialchars($_POST['ten_tacgia']) : ''; ?>"
                        required
                        placeholder="Nhập tên tác giả">
            </td>
        </tr>

        <tr>
            <td><label for="but_danh">Bút danh:</label></td>
        </tr>
        <tr>
            <td>
                <input type="text" 
                        id="but_danh" 
                        name="but_danh" 
                        value="<?php echo isset($_POST['but_danh']) ? htmlspecialchars($_POST['but_danh']) : ''; ?>"
                        placeholder="Nhập bút danh (nếu có)">
            </td>
        </tr>

        <tr>
            <td><label for="gioi_thieu">Giới thiệu:</label></td>
        </tr>
        <tr>
            <td>
                <textarea id="gioi_thieu" 
                          name="gioi_thieu" 
                          rows="5" 
                          placeholder="Giới thiệu về tác giả..."
                          style="width: 100%; padding: 9px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; resize: vertical;"><?php echo isset($_POST['gioi_thieu']) ? htmlspecialchars($_POST['gioi_thieu']) : ''; ?></textarea>
            </td>
        </tr>
    </table>

    <div class="form-actions">
        <button type="submit">Thêm mới</button>
        <a href="index.php?page=admin&controller=tacgia&action=index" class="btn-back">Hủy</a>
    </div>
</form>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>