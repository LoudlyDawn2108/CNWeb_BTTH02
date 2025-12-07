# Admin Module - Testing Guide

## ✅ Các chức năng đã implement

### 1. USER MANAGEMENT (`/admin/users`)

#### View Users

- **URL**: `GET /admin/users`
- **Chức năng**: Xem danh sách users, search, filter, pagination
- **Test**: Truy cập http://localhost/CNWeb_BTTH02/admin/users

#### Toggle User Status

- **URL**: `POST /admin/users/{id}/toggle-status`
- **Body**: `{ "status": 0 hoặc 1 }`
- **Test**: Click nút toggle trên trang users list

### 2. CATEGORY MANAGEMENT (`/admin/categories`)

#### List Categories

- **URL**: `GET /admin/categories`
- **Test**: http://localhost/CNWeb_BTTH02/admin/categories

#### Create Category

- **URL GET**: `/admin/categories/create`
- **URL POST**: `/admin/categories/store`
- **Test**:
  1. Click "Thêm danh mục" trên categories list
  2. Điền form và submit

#### Edit Category

- **URL GET**: `/admin/categories/{id}/edit`
- **URL POST**: `/admin/categories/{id}/update`
- **Test**:
  1. Click nút edit trên categories list
  2. Sửa thông tin và submit

#### Delete Category

- **URL**: `POST /admin/categories/{id}/delete`
- **Test**: Click nút delete trên categories list

### 3. COURSE APPROVAL

#### Approve Course

- **URL**: `POST /admin/courses/{id}/approve`
- **Body**: `{ "action": "approve" }`
- **Test**: Click nút approve trên dashboard

#### Reject Course

- **URL**: `POST /admin/courses/{id}/reject`
- **Body**: `{ "reason": "lý do từ chối" }`
- **Test**: Click nút reject trên dashboard

### 4. REPORTS & STATISTICS

#### View Statistics

- **URL**: `GET /admin/reports/statistics`
- **Test**: http://localhost/CNWeb_BTTH02/admin/reports/statistics

## 🔧 Cách test nhanh

### Option 1: Sử dụng Admin Test Page

1. Mở: http://localhost/CNWeb_BTTH02/admin-test.html
2. Click các nút test để kiểm tra từng chức năng
3. Xem kết quả JSON response

### Option 2: Test thủ công

1. Login với tài khoản admin:

   - Email: admin@example.com
   - Password: admin123

2. Truy cập dashboard: /admin/dashboard

3. Test từng chức năng:
   - ✅ Xem users → Click "Quản lý người dùng" trên sidebar
   - ✅ Toggle user status → Click nút toggle trên users list
   - ✅ Xem categories → Click "Quản lý danh mục"
   - ✅ Tạo category → Click "Thêm danh mục"
   - ✅ Sửa category → Click icon edit
   - ✅ Xóa category → Click icon delete
   - ✅ Approve/Reject course → Click nút trên dashboard
   - ✅ Xem statistics → Click "Thống kê & Báo cáo"

## 🐛 Troubleshooting

### Nếu không hoạt động:

1. **Check database connection**

   ```php
   // Mở file: test-db.php
   <?php
   require_once 'config/Database.php';
   $db = new Database();
   $conn = $db->getConnection();
   echo "Database connected!";
   ?>
   ```

2. **Check error logs**

   - Mở Console (F12) trong browser
   - Tab "Network" để xem request/response
   - Tab "Console" để xem lỗi JavaScript

3. **Check PHP errors**

   - Mở file error.log trong Laragon
   - Hoặc thêm vào index.php:
     ```php
     error_reporting(E_ALL);
     ini_set('display_errors', 1);
     ```

4. **Verify routes**
   - Routes đã được define trong index.php
   - Controller methods tồn tại
   - ViewModels đã được tạo

## 📝 Expected Behavior

### Create Category

1. Click "Thêm danh mục"
2. Điền tên (required, min 3 chars)
3. Điền mô tả (optional, max 500 chars)
4. Submit → Redirect về /admin/categories với message "Thêm danh mục thành công"

### Edit Category

1. Click icon edit
2. Form hiện với data hiện tại
3. Sửa thông tin
4. Submit → Redirect về /admin/categories với message "Cập nhật danh mục thành công"

### Delete Category

1. Click icon delete
2. Confirm dialog xuất hiện
3. Nếu category có courses → Hiện thông báo lỗi không thể xóa
4. Nếu không có courses → Xóa thành công, reload trang

### Toggle User Status

1. Click nút toggle (màu xanh = active, màu xám = inactive)
2. Confirm dialog
3. Status thay đổi, trang reload

### Approve/Reject Course

1. Từ dashboard, thấy "Khóa học chờ duyệt"
2. Click nút approve (màu xanh) → Course được duyệt
3. Click nút reject (màu đỏ) → Popup nhập lý do → Course bị từ chối

## 🔐 Security Notes

- Tất cả các actions đều require admin role
- CSRF protection cần được enable (hiện tại chưa có)
- Input validation đã có
- SQL injection protection (sử dụng prepared statements)

## 📊 Database Schema

Tham khảo: `database/schema.sql`

- users (id, username, email, password, fullname, role, status)
- categories (id, name, description)
- courses (id, title, instructor_id, category_id, status)
- enrollments (id, course_id, student_id, status, progress)
