<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Trang - <?php echo htmlspecialchars($chuong['ten_chuong']); ?></title>
    <link rel="stylesheet" href="/web_doc_truyen/frontend/public/css/user.css">
</head>
<body>
    <?php include __DIR__ . '/../layouts/user/header.html'; ?>
    
     <div class="container">
        <!-- Nút quay lại -->
        <div style="margin-bottom: 20px;">
            <a href="index.php?page=chitiet&id=<?php echo $chuong['id_truyen']; ?>" style="display: inline-block; padding: 12px 25px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                ← Quay lại 
            </a>
        </div>
        <!-- Thông tin chương -->
        <div class="story-card">
            <h1>Chương <?php echo $chuong['so_chuong']; ?>: <?php echo htmlspecialchars($chuong['tieu_de']); ?></h1>
            <p class="chapter-date">📅 <?php echo date('d/m/Y', strtotime($chuong['ngay_dang'])); ?></p>
        </div>

        
        <h2 style="text-align: center; margin: 30px 0; color: white;">📄 Chọn Trang Để Đọc</h2>
        
        
        <?php if(!empty($danhSachTrang)): ?>
        <div class="story-grid">
            <?php foreach($danhSachTrang as $trang): ?>
            <div class="story-card">
                <a href="index.php?page=doctrang&id=<?php echo $trang['id']; ?>">
                    <div class="page-number">
                        Trang <?php echo $trang['so_trang']; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="story-card" style="text-align: center; padding: 40px;">
            <p>Chưa có trang nào được đăng</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>