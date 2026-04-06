<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<a href="/web_doc_truyen/frontend/public/index.php?page=admin" class="btn-home">← Trang chủ Admin</a>
<h1>Quản lý người dùng</h1>

<?php
// Hiển thị thông báo
if(isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $message = '';
    $type = 'success'; // success hoặc error
    
    switch($msg) {
        case 'add_success':
            $message = 'Thêm người dùng thành công!';
            break;
        case 'update_success':
            $message = 'Cập nhật thông tin thành công!';
            break;
        case 'delete_success':
            $message = 'Xóa người dùng thành công!';
            break;
        case 'delete_error':
            $message = 'Có lỗi xảy ra khi xóa người dùng!';
            $type = 'error';
            break;
        case 'cannot_delete_self':
            $message = 'Bạn không thể xóa tài khoản của chính mình!';
            $type = 'error';
            break;
    }
    
    if($message) {
        $bgColor = $type == 'success' ? '#d4edda' : '#f8d7da';
        $borderColor = $type == 'success' ? '#c3e6cb' : '#f5c6cb';
        $textColor = $type == 'success' ? '#155724' : '#721c24';
        
        echo "<div style='background-color: {$bgColor}; border: 1px solid {$borderColor}; color: {$textColor}; padding: 12px 20px; margin: 15px 0; border-radius: 5px;'>";
        echo htmlspecialchars($message);
        echo "</div>";
    }
}
?>

<a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&action=create" class="btn-add">Thêm người dùng mới</a>

<!-- Form tìm kiếm -->
<form method="GET" action="/web_doc_truyen/frontend/public/index.php" style="margin: 20px 0; display: flex; gap: 10px; align-items: center;">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="controller" value="nguoidung">
    <input type="text"
           name="keyword"
           placeholder="🔍 Tìm theo tên đăng nhập hoặc email..." 
           value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
           style="flex: 1; padding: 10px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
    <button type="submit" 
            style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
        Tìm kiếm
    </button>
    <?php if(isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
        <a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung" 
           style="padding: 10px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">
            Xóa
        </a>
    <?php endif; ?>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên đăng nhập</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($nguoidung)): ?>
            <tr>
                <td colspan="5" class="empty-data">
                    Chưa có người dùng nào
                </td>
            </tr>
        <?php else: ?>
            <?php foreach($nguoidung as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($user['ten_dang_nhap']); ?></strong>
                        <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $user['id']): ?>
                            <span style="color: #3498db; font-size: 12px; font-weight: normal;"> (Bạn)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php
                            if($user['vai_tro'] == 'admin') {
                                echo '<span style="color: #e74c3c; font-weight: bold;">Admin</span>';
                            } else {
                                echo '<span style="color: #27ae60;">User</span>';
                            }
                        ?>
                    </td>
                    <td>
                        <a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&action=edit&id=<?php echo $user['id']; ?>" 
                           style="color: #3498db; text-decoration: none; margin-right: 10px;">
                            Sửa
                        </a>
                        
                        <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] != $user['id']): ?>
                            <a href="/web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&action=delete&id=<?php echo $user['id']; ?>" 
                               style="color: #e74c3c; text-decoration: none;"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng <?php echo htmlspecialchars($user['ten_dang_nhap']); ?>?')">
                                Xóa
                            </a>
                        <?php else: ?>
                            <span style="color: #95a5a6; font-style: italic; cursor: not-allowed;" 
                                  title="Bạn không thể xóa tài khoản của chính mình">
                                Xóa
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>