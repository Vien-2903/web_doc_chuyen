<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung" class="btn-home">Quay lại danh sách</a>
<h1 class="form-title">Chỉnh sửa người dùng</h1>

<?php if(!empty($errors)): ?>
    <div class="alert-message alert-error">
        <?php foreach($errors as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&action=update" class="user-form">
    <input type="hidden" name="id" value="<?php echo $nguoidung['id']; ?>">
    
    <table>
        <tr>
            <td><label for="ten_dang_nhap">Tên đăng nhập:</label></td>
        </tr>
        <tr>
            <td>
                <input type="text" 
                        id="ten_dang_nhap" 
                        value="<?php echo htmlspecialchars($nguoidung['ten_dang_nhap']); ?>"
                        disabled
                        style="background-color: #f5f5f5; cursor: not-allowed; color: #666;">
                <small style="display: block; margin-top: 5px; color: #999; font-size: 12px; font-style: italic;">
                    Không thể chỉnh sửa tên đăng nhập
                </small>
            </td>
        </tr>

        <tr>
            <td><label for="email">Email: <span style="color: red;">*</span></label></td>
        </tr>
        <tr>
            <td>
                <input type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($nguoidung['email']); ?>"
                        required
                        placeholder="example@email.com">
            </td>
        </tr>

        <tr>
            <td><label for="vai_tro">Vai trò: <span style="color: red;">*</span></label></td>
        </tr>
        <tr>
            <td>
                <select id="vai_tro" name="vai_tro" required>
                    <option value="user" <?php echo ($nguoidung['vai_tro'] == 'user') ? 'selected' : ''; ?>>
                        User
                    </option>
                    <option value="admin" <?php echo ($nguoidung['vai_tro'] == 'admin') ? 'selected' : ''; ?>>
                        Admin
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <div class="form-actions">
        <button type="submit">Cập nhật</button>
    </div>
</form>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>