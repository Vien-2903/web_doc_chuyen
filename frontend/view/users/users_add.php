<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung" class="btn-home">Quay lại danh sách</a>

<h1 class="form-title">Thêm người dùng mới</h1>

<form method="POST" action="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&action=store" class="user-form">
    <table>
        <tr>
            <td><label for="ten_dang_nhap">Tên đăng nhập: <span style="color: red;">*</span></label></td>
        </tr>
        <tr>
            <td>
                <input type="text" 
                        id="ten_dang_nhap" 
                        name="ten_dang_nhap" 
                        value="<?php echo isset($_POST['ten_dang_nhap']) ? htmlspecialchars($_POST['ten_dang_nhap']) : ''; ?>"
                        required
                        placeholder="Nhập tên đăng nhập">
            </td>
        </tr>

        <tr>
            <td><label for="mat_khau">Mật khẩu: <span style="color: red;">*</span></label></td>
        </tr>
        <tr>
            <td>
                <input type="password" 
                        id="mat_khau" 
                        name="mat_khau" 
                        required
                        minlength="6"
                        placeholder="Tối thiểu 6 ký tự">
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
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
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
                    <option value="user" <?php echo (isset($_POST['vai_tro']) && $_POST['vai_tro'] == 'user') ? 'selected' : ''; ?>>
                        User
                    </option>
                    <option value="admin" <?php echo (isset($_POST['vai_tro']) && $_POST['vai_tro'] == 'admin') ? 'selected' : ''; ?>>
                        Admin
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <div class="form-actions">
        <button type="submit">Thêm mới</button>
    </div>
</form>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>