<?php require_once __DIR__ . '/../layouts/cover_image_helper.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truyện Yêu Thích - Web Đọc Truyện</title>
    <link rel="stylesheet" href="/web_doc_truyen/frontend/public/css/user.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/web_doc_truyen/frontend/public/css/yeuthich.css?v=<?= time() ?>">
    <style>
        .favorites-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            padding: 40px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        .favorites-header h2 {
            color: white;
            text-align: center;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .favorites-count {
            text-align: center;
            color: rgba(255,255,255,0.9);
            font-size: 16px;
        }
        
        .empty-favorites {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-icon {
            font-size: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .btn-remove-favorite {
            width: 100%;
            background-color: #fff;
            color: #e74c3c;
            padding: 10px;
            border: 2px solid #e74c3c;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-remove-favorite:hover {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layouts/user/header.html'; ?>
    
    <!-- Header -->
    <div class="favorites-header">
        <div class="container">
            <h2>❤️ Truyện Yêu Thích Của Tôi</h2>
            <p class="favorites-count">
                Bạn đang yêu thích <strong><?= count($truyens) ?></strong> truyện
            </p>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="container" style="margin-bottom: 20px;">
            <?php if($_GET['msg'] == 'removed'): ?>
                <div class="alert-message alert-success">
                    ✓ Đã xóa khỏi danh sách yêu thích!
                </div>
            <?php elseif($_GET['msg'] == 'error'): ?>
                <div class="alert-message alert-error">
                    ⚠️ Có lỗi xảy ra! Vui lòng thử lại.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <section class="stories">
        <div class="container">
            <?php if (empty($truyens)): ?>
                <!-- Empty State -->
                <div class="empty-favorites">
                    <div class="empty-icon">💔</div>
                    <h3 style="color: white; font-size: 28px; margin-bottom: 15px;">
                        Bạn chưa yêu thích truyện nào
                    </h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 30px;">
                        Hãy khám phá và thêm những truyện yêu thích của bạn!
                    </p>
                    <a href="/web_doc_truyen/frontend/public/index.php" 
                       style="display: inline-block; padding: 14px 35px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
                        📖 Khám phá truyện
                    </a>
                </div>
            <?php else: ?>
                <!-- Stories Grid -->
                <div class="story-grid">
                    <?php foreach ($truyens as $truyen): ?>
                        <div class="story-card">
                            <a href="/web_doc_truyen/frontend/public/index.php?page=chitiet&id=<?= $truyen['id'] ?>">
                                <?php 
                                $imagePath = resolve_cover_image_url(
                                    $truyen['anh_bia'] ?? '',
                                    'https://via.placeholder.com/180x240/667eea/ffffff?text=No+Image'
                                );
                                ?>
                                <img src="<?= $imagePath ?>" 
                                     alt="<?= htmlspecialchars($truyen['ten_truyen']) ?>"
                                     onerror="this.src='https://via.placeholder.com/180x240/667eea/ffffff?text=No+Image'">
                                
                                <h3><?= htmlspecialchars($truyen['ten_truyen']) ?></h3>
                                
                                <p>
                                    Tác giả: 
                                    <?php 
                                    if (!empty($truyen['but_danh'])) {
                                        echo htmlspecialchars($truyen['but_danh']);
                                    } elseif (!empty($truyen['ten_tacgia'])) {
                                        echo htmlspecialchars($truyen['ten_tacgia']);
                                    } else {
                                        echo 'Chưa rõ';
                                    }
                                    ?>
                                </p>
                                
                                <p class="story-views">
                                    👁️ <?= number_format($truyen['luot_xem']) ?> lượt xem
                                </p>
                            </a>
                            
                            <!-- BỎ phần hiển thị ngày yêu thích -->
                            
                            <!-- Nút xóa yêu thích -->
                            <form method="POST" 
                                  action="/web_doc_truyen/frontend/public/index.php?page=yeuthich&action=remove"
                                  onsubmit="return confirm('Bạn có chắc muốn bỏ yêu thích truyện này?');">
                                <input type="hidden" name="id_truyen" value="<?= $truyen['id'] ?>">
                                <button type="submit" class="btn-remove-favorite">
                                    💔 Bỏ yêu thích
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© 2024 Đọc Truyện Online - All Rights Reserved | Made with ❤️</p>
        </div>
    </footer>
</body>
</html>