<?php include __DIR__ . '/../layouts/admin/header.php'; ?>
<a href="index.php?page=admin&controller=home" class="btn-home">← Trang chủ</a>

<h1>Quản lý tác giả</h1>
<?php if (isset($_GET['success'])): ?>
        <div class="alert-message alert-success">
            <?php
                if ($_GET['success'] == 'add') echo "Thêm tác giả thành công!";
                if ($_GET['success'] == 'edit') echo "Cập nhật tác giả thành công!";
                if ($_GET['success'] == 'delete') echo "Xóa tác giả thành công!";
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'delete_error'): ?>
        <div class="alert-message alert-error">
            ⚠️ Không thể xóa tác giả này!<br>
            Vui lòng xóa hết những gì liên quan đến tác giả trước khi xóa.
        </div>
    <?php endif; ?>
<a href="index.php?page=admin&controller=tacgia&action=add" class="btn-add">Thêm tác giả mới</a>
<!-- Form tìm kiếm -->
<form method="GET" action="index.php" style="margin: 20px 0; display: flex; gap: 10px; align-items: center;">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="controller" value="tacgia">
    <input type="text" 
           name="keyword" 
           placeholder="🔍 Tìm theo tên tác giả hoặc bút danh..." 
           value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
           style="flex: 1; padding: 10px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
    <button type="submit" 
            style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
        Tìm kiếm
    </button>
    <?php if(isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
        <a href="index.php?controller=tacgia" 
           style="padding: 10px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">
            Xóa
        </a>
    <?php endif; ?>
</form>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên tác giả</th>
            <th>Bút danh</th>
            <th>Giới thiệu</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $has_data = false;
        while ($tg = mysqli_fetch_assoc($tacgias)): 
            $has_data = true;
        ?>
        <tr>
            <td><?php echo $tg['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($tg['ten_tacgia']); ?></strong></td>
            <td><?php echo htmlspecialchars($tg['but_danh']); ?></td>
            <td><?php echo htmlspecialchars($tg['gioi_thieu']); ?></td>
            <td>
                <a href="index.php?page=admin&controller=tacgia&action=edit&id=<?php echo $tg['id']; ?>">Sửa</a>
                <a href="index.php?page=admin&controller=tacgia&action=delete&id=<?php echo $tg['id']; ?>"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa tác giả <?php echo htmlspecialchars($tg['ten_tacgia']); ?>?')">
                    Xóa
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
        
        <?php if(!$has_data): ?>
        <tr>
            <td colspan="5" class="empty-data">
                Chưa có tác giả nào
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../layouts/admin/footer.php'; ?>