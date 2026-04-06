<?php
require_once(__DIR__ . '/../model/HoSoModel.php');
require_once(__DIR__ . '/../model/NguoiDungModel.php');

class HoSoController {
    private $hoSoModel;
    private $nguoiDungModel;
    
    public function __construct() {
        $this->hoSoModel = new HoSoModel();
        $this->nguoiDungModel = new NguoiDungModel();
    }

    private function getAvatarUploadDir() {
        $targetDir = realpath(__DIR__ . '/../uploads/avatar');
        if ($targetDir === false) {
            $targetDir = __DIR__ . '/../uploads/avatar';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $targetDir = realpath($targetDir) ?: $targetDir;
        }

        return $targetDir;
    }

    private function deleteAvatarFile($avatarFileName) {
        $avatarFileName = trim((string)$avatarFileName);
        if ($avatarFileName === '') {
            return;
        }

        $fileName = basename(str_replace('\\', '/', $avatarFileName));
        if ($fileName === '' || $fileName === '.' || $fileName === '..') {
            return;
        }

        $candidates = [
            __DIR__ . '/../uploads/avatar/' . $fileName,
            __DIR__ . '/../../frontend/public/uploads/avatar/' . $fileName
        ];

        foreach ($candidates as $candidate) {
            $fullPath = realpath($candidate);
            if ($fullPath && file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
    // Xử lý cập nhật hồ sơ
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_nguoidung = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : (int)($_POST['id_nguoidung'] ?? 0);
            $ho_ten = trim($_POST['ho_ten']);
            $ngay_sinh = $_POST['ngay_sinh'];
            $gioi_tinh = $_POST['gioi_tinh'];
            $so_dien_thoai = trim($_POST['so_dien_thoai']);
            $dia_chi = trim($_POST['dia_chi']);

            if($id_nguoidung <= 0) {
                $_SESSION['error'] = "Phiên đăng nhập không hợp lệ";
                header("Location: /web_doc_truyen/frontend/public/index.php?page=login");
                exit();
            }
            
            // Kiểm tra số điện thoại trùng (loại trừ chính nó)
            if($so_dien_thoai !== '' && $this->hoSoModel->checkSoDienThoaiExists($so_dien_thoai, $id_nguoidung)) {
                $_SESSION['error'] = "Số điện thoại đã tồn tại!";
                header("Location: /web_doc_truyen/frontend/public/index.php?page=profile");
                exit();
            }
            
            // Kiểm tra hồ sơ đã tồn tại chưa
            if($this->hoSoModel->checkHoSoExists($id_nguoidung)) {
                // Update
                $this->hoSoModel->updateHoSo($id_nguoidung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi);
            } else {
                // Create
                $this->hoSoModel->createHoSo($id_nguoidung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi);
            }
            
            // Xử lý upload avatar mới
            if(isset($_FILES['avatar']) && (int)$_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                if((int)$_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = 'Upload ảnh thất bại! Mã lỗi: ' . (int)$_FILES['avatar']['error'];
                    header("Location: /web_doc_truyen/frontend/public/index.php?page=profile");
                    exit();
                }

                // Lấy avatar cũ
                $old_avatar = $this->hoSoModel->getAvatar($id_nguoidung);
                
                // Upload avatar mới
                $avatar = $this->uploadAvatar($_FILES['avatar']);
                
                if($avatar !== false) {
                    $updated = $this->hoSoModel->updateAvatar($id_nguoidung, $avatar);

                    if(!$updated) {
                        $this->deleteAvatarFile($avatar);
                        $_SESSION['error'] = 'Upload ảnh thất bại! Không thể lưu thông tin ảnh.';
                        header("Location: /web_doc_truyen/frontend/public/index.php?page=profile");
                        exit();
                    }

                    // Xóa avatar cũ nếu có
                    if($old_avatar) {
                        $this->deleteAvatarFile($old_avatar);
                    }
                } else {
                    $_SESSION['error'] = 'Upload ảnh thất bại! Kiểm tra định dạng (JPG, JPEG, PNG, GIF, WEBP) và kích thước (max 5MB)';
                    header("Location: /web_doc_truyen/frontend/public/index.php?page=profile");
                    exit();
                }
            }
            
            $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
            header("Location: /web_doc_truyen/frontend/public/index.php?page=profile");
            exit();
        }
    }
    
    // Upload avatar
    private function uploadAvatar($file) {
        $target_dir = $this->getAvatarUploadDir() . DIRECTORY_SEPARATOR;

        if(!is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        // Kiểm tra file
        $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if(!in_array($imageFileType, $allowed_types)) {
            return false;
        }

        if(@getimagesize($file['tmp_name']) === false) {
            return false;
        }
        
        // Kiểm tra kích thước (max 5MB)
        if($file['size'] > 5000000) {
            return false;
        }
        
        // Tạo tên file mới
        try {
            $randomPrefix = bin2hex(random_bytes(8));
        } catch (Throwable $e) {
            $randomPrefix = uniqid();
        }
        $new_filename = $randomPrefix . '_' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Upload file
        if(move_uploaded_file($file['tmp_name'], $target_file)) {
            return $new_filename;
        }
        
        return false;
    }
    public function view() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit();
        }
        
        // Lấy ID người dùng từ session
        $id_nguoidung = $_SESSION['user']['id'];
        
        // Lấy thông tin người dùng
        $nguoiDung = $this->nguoiDungModel->getNguoiDungById($id_nguoidung);
        
        // Lấy thông tin hồ sơ
        $hoSo = $this->hoSoModel->getHoSoByUserId($id_nguoidung);
        
        // Nếu chưa có hồ sơ, tạo mảng rỗng với thông tin cơ bản từ người dùng
        if(!$hoSo) {
            $hoSo = [
                'id_nguoidung' => $id_nguoidung,
                'ten_dang_nhap' => $nguoiDung['ten_dang_nhap'],
                'email' => $nguoiDung['email'],
                'vai_tro' => $nguoiDung['vai_tro'],
                'ho_ten' => '',
                'avatar' => '',
                'so_dien_thoai' => '',
                'gioi_tinh' => '',
                'ngay_sinh' => '',
                'dia_chi' => ''
            ];
        } else {
            // Gộp thông tin từ bảng nguoidung vào
            $hoSo['ten_dang_nhap'] = $nguoiDung['ten_dang_nhap'];
            $hoSo['vai_tro'] = $nguoiDung['vai_tro'];
            $hoSo['email'] = $nguoiDung['email'];
        }
        
        // Hiển thị trang xem hồ sơ
        require_once(__DIR__ . '/../view/hoso_user/hoso_view.php');
    }
    public function edit() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit();
        }
        
        // Lấy ID người dùng từ session
        $id_nguoidung = $_SESSION['user']['id'];
        
        // Lấy thông tin người dùng
        $nguoiDung = $this->nguoiDungModel->getNguoiDungById($id_nguoidung);
        
        // Lấy thông tin hồ sơ
        $hoSo = $this->hoSoModel->getHoSoByUserId($id_nguoidung);
        
        // Nếu chưa có hồ sơ, tạo mảng rỗng với thông tin cơ bản từ người dùng
        if(!$hoSo) {
            $hoSo = [
                'id_nguoidung' => $id_nguoidung,
                'ten_dang_nhap' => $nguoiDung['ten_dang_nhap'],
                'email' => $nguoiDung['email'],
                'vai_tro' => $nguoiDung['vai_tro'],
                'ho_ten' => '',
                'avatar' => '',
                'so_dien_thoai' => '',
                'gioi_tinh' => '',
                'ngay_sinh' => '',
                'dia_chi' => ''
            ];
        } else {
            // Gộp thông tin từ bảng nguoidung vào
            $hoSo['ten_dang_nhap'] = $nguoiDung['ten_dang_nhap'];
            $hoSo['vai_tro'] = $nguoiDung['vai_tro'];
            $hoSo['email'] = $nguoiDung['email'];
        }
        
        // Hiển thị form chỉnh sửa
        require_once(__DIR__ . '/../view/hoso_user/hoso_detail.php');
    }

    // API: Lấy toàn bộ thông tin người dùng đang đăng nhập
    public function getCurrentUserProfileData($sessionUser) {
        if (empty($sessionUser) || empty($sessionUser['id'])) {
            return [
                'status' => 401,
                'body' => [
                    'success' => false,
                    'message' => 'Bạn chưa đăng nhập'
                ]
            ];
        }

        $idNguoiDung = (int) $sessionUser['id'];
        $nguoiDung = $this->nguoiDungModel->getNguoiDungById($idNguoiDung);

        if (!$nguoiDung) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ]
            ];
        }

        $hoSo = $this->hoSoModel->getHoSoByUserId($idNguoiDung);

        $profileData = [
            'ho_ten' => $hoSo['ho_ten'] ?? '',
            'avatar' => $hoSo['avatar'] ?? '',
            'so_dien_thoai' => $hoSo['so_dien_thoai'] ?? '',
            'gioi_tinh' => $hoSo['gioi_tinh'] ?? '',
            'ngay_sinh' => $hoSo['ngay_sinh'] ?? '',
            'dia_chi' => $hoSo['dia_chi'] ?? '',
            'ngay_tao_ho_so' => $hoSo['ngay_tao'] ?? null,
            'ngay_cap_nhat_ho_so' => $hoSo['ngay_cap_nhat'] ?? null
        ];

        $profileCompleted =
            $profileData['ho_ten'] !== '' &&
            $profileData['so_dien_thoai'] !== '' &&
            $profileData['gioi_tinh'] !== '' &&
            $profileData['ngay_sinh'] !== '' &&
            $profileData['dia_chi'] !== '';

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'message' => 'Lấy thông tin người dùng thành công',
                'data' => [
                    'id' => (int) $nguoiDung['id'],
                    'ten_dang_nhap' => $nguoiDung['ten_dang_nhap'] ?? '',
                    'email' => $nguoiDung['email'] ?? '',
                    'vai_tro' => $nguoiDung['vai_tro'] ?? 'user',
                    'profile_completed' => $profileCompleted,
                    'profile' => $profileData
                ]
            ]
        ];
    }

    // API: Cập nhật hồ sơ cho người dùng hiện tại (hỗ trợ user mới chưa có hồ sơ)
    public function updateCurrentUserProfileData($sessionUser, $inputData, $avatarFile = null) {
        if (empty($sessionUser) || empty($sessionUser['id'])) {
            return [
                'status' => 401,
                'body' => [
                    'success' => false,
                    'message' => 'Bạn chưa đăng nhập'
                ]
            ];
        }

        if (!is_array($inputData)) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ'
                ]
            ];
        }

        $idNguoiDung = (int) $sessionUser['id'];
        $hoSoHienTai = $this->hoSoModel->getHoSoByUserId($idNguoiDung);

        $ho_ten = array_key_exists('ho_ten', $inputData)
            ? trim((string) $inputData['ho_ten'])
            : ($hoSoHienTai['ho_ten'] ?? '');

        $ngay_sinh = array_key_exists('ngay_sinh', $inputData)
            ? trim((string) $inputData['ngay_sinh'])
            : ($hoSoHienTai['ngay_sinh'] ?? '');

        $gioi_tinh = array_key_exists('gioi_tinh', $inputData)
            ? strtolower(trim((string) $inputData['gioi_tinh']))
            : ($hoSoHienTai['gioi_tinh'] ?? '');

        $so_dien_thoai = array_key_exists('so_dien_thoai', $inputData)
            ? preg_replace('/\s+/', '', (string) $inputData['so_dien_thoai'])
            : ($hoSoHienTai['so_dien_thoai'] ?? '');

        $dia_chi = array_key_exists('dia_chi', $inputData)
            ? trim((string) $inputData['dia_chi'])
            : ($hoSoHienTai['dia_chi'] ?? '');

        if ($so_dien_thoai !== '' && !preg_match('/^[0-9]{10,11}$/', $so_dien_thoai)) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'Số điện thoại phải có 10-11 chữ số'
                ]
            ];
        }

        if ($gioi_tinh !== '' && !in_array($gioi_tinh, ['nam', 'nu', 'khac'], true)) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'Giới tính không hợp lệ'
                ]
            ];
        }

        if ($ngay_sinh !== '') {
            $ngaySinhDate = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
            $ngaySinhValid = $ngaySinhDate && $ngaySinhDate->format('Y-m-d') === $ngay_sinh;

            if (!$ngaySinhValid || $ngay_sinh > date('Y-m-d')) {
                return [
                    'status' => 400,
                    'body' => [
                        'success' => false,
                        'message' => 'Ngày sinh không hợp lệ'
                    ]
                ];
            }
        }

        if ($so_dien_thoai !== '' && $this->hoSoModel->checkSoDienThoaiExists($so_dien_thoai, $idNguoiDung)) {
            return [
                'status' => 409,
                'body' => [
                    'success' => false,
                    'message' => 'Số điện thoại đã tồn tại'
                ]
            ];
        }

        if ($this->hoSoModel->checkHoSoExists($idNguoiDung)) {
            $saved = $this->hoSoModel->updateHoSo($idNguoiDung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi);
        } else {
            $saved = $this->hoSoModel->createHoSo($idNguoiDung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi);
        }

        if (!$saved) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Không thể cập nhật hồ sơ'
                ]
            ];
        }

        // Nếu có gửi avatar thì cập nhật thêm ảnh đại diện.
        if (is_array($avatarFile) && isset($avatarFile['error']) && (int)$avatarFile['error'] !== UPLOAD_ERR_NO_FILE) {
            if ((int)$avatarFile['error'] !== UPLOAD_ERR_OK) {
                return [
                    'status' => 400,
                    'body' => [
                        'success' => false,
                        'message' => 'Upload ảnh thất bại! Mã lỗi: ' . (int)$avatarFile['error']
                    ]
                ];
            }

            $oldAvatar = $this->hoSoModel->getAvatar($idNguoiDung);
            $newAvatar = $this->uploadAvatar($avatarFile);

            if ($newAvatar === false) {
                return [
                    'status' => 400,
                    'body' => [
                        'success' => false,
                        'message' => 'Upload ảnh thất bại! Kiểm tra định dạng (JPG, JPEG, PNG, GIF, WEBP) và kích thước (max 5MB)'
                    ]
                ];
            }

            $avatarSaved = $this->hoSoModel->updateAvatar($idNguoiDung, $newAvatar);
            if (!$avatarSaved) {
                $this->deleteAvatarFile($newAvatar);

                return [
                    'status' => 500,
                    'body' => [
                        'success' => false,
                        'message' => 'Không thể lưu thông tin avatar'
                    ]
                ];
            }

            if (!empty($oldAvatar)) {
                $this->deleteAvatarFile($oldAvatar);
            }
        }

        $response = $this->getCurrentUserProfileData($sessionUser);
        if (($response['status'] ?? 500) === 200) {
            $response['body']['message'] = 'Cập nhật hồ sơ thành công';
        }
        return $response;
    }
}
?>