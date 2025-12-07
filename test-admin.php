<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Fake admin login for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['fullname'] = 'Administrator';
$_SESSION['role'] = 2;
$_SESSION['email'] = 'admin@example.com';

define('BASE_PATH', __DIR__);

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Course.php';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Database Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Database Connection & CRUD Test</h1>
    
    <?php
    try {
        echo "<div class='alert alert-success'>✅ PHP Session Started</div>";
        echo "<div class='alert alert-info'>Session User: " . $_SESSION['fullname'] . " (Role: " . $_SESSION['role'] . ")</div>";
        
        // Test Database Connection
        $db = new Database();
        $conn = $db->getConnection();
        echo "<div class='alert alert-success'>✅ Database Connected Successfully!</div>";
        
        // Test User Model
        echo "<h3 class='mt-4'>Test User Model</h3>";
        $totalUsers = User::query()->count();
        echo "<div class='alert alert-info'>Total Users: " . $totalUsers . "</div>";
        
        $users = User::query()->limit(3)->get();
        echo "<table class='table table-sm'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>{$user->id}</td><td>{$user->username}</td><td>{$user->email}</td><td>{$user->role}</td></tr>";
        }
        echo "</table>";
        
        // Test Category Model
        echo "<h3 class='mt-4'>Test Category Model</h3>";
        $totalCategories = Category::query()->count();
        echo "<div class='alert alert-info'>Total Categories: " . $totalCategories . "</div>";
        
        $categories = Category::query()->limit(3)->get();
        echo "<table class='table table-sm'>";
        echo "<tr><th>ID</th><th>Name</th><th>Description</th></tr>";
        foreach ($categories as $cat) {
            echo "<tr><td>{$cat->id}</td><td>{$cat->name}</td><td>" . substr($cat->description ?? '', 0, 50) . "</td></tr>";
        }
        echo "</table>";
        
        // Test Course Model
        echo "<h3 class='mt-4'>Test Course Model</h3>";
        $totalCourses = Course::query()->count();
        echo "<div class='alert alert-info'>Total Courses: " . $totalCourses . "</div>";
        
        $courses = Course::query()->limit(3)->get();
        echo "<table class='table table-sm'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Instructor ID</th></tr>";
        foreach ($courses as $course) {
            echo "<tr><td>{$course->id}</td><td>{$course->title}</td><td>{$course->status}</td><td>{$course->instructor_id}</td></tr>";
        }
        echo "</table>";
        
        // Test CRUD Operations
        echo "<h3 class='mt-4'>Test CRUD Operations</h3>";
        
        // CREATE
        echo "<h5>Test CREATE</h5>";
        $testCategory = new Category();
        $testCategory->name = "Test Category " . time();
        $testCategory->description = "This is a test category created at " . date('Y-m-d H:i:s');
        $testCategory->save();
        echo "<div class='alert alert-success'>✅ Category Created! ID: {$testCategory->id}</div>";
        
        // READ
        echo "<h5>Test READ</h5>";
        $foundCategory = Category::find($testCategory->id);
        if ($foundCategory) {
            echo "<div class='alert alert-success'>✅ Category Found! Name: {$foundCategory->name}</div>";
        }
        
        // UPDATE
        echo "<h5>Test UPDATE</h5>";
        $foundCategory->name = "Updated Test Category " . time();
        $foundCategory->save();
        echo "<div class='alert alert-success'>✅ Category Updated! New Name: {$foundCategory->name}</div>";
        
        // DELETE
        echo "<h5>Test DELETE</h5>";
        $foundCategory->delete();
        echo "<div class='alert alert-success'>✅ Category Deleted!</div>";
        
        $checkDeleted = Category::find($testCategory->id);
        if (!$checkDeleted) {
            echo "<div class='alert alert-success'>✅ Confirmed: Category no longer exists in database</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    ?>
    
    <hr class="my-5">
    
    <h3>Navigation Links</h3>
    <div class="list-group">
        <a href="/admin/dashboard" class="list-group-item list-group-item-action">Go to Admin Dashboard</a>
        <a href="/admin/users" class="list-group-item list-group-item-action">Go to User Management</a>
        <a href="/admin/categories" class="list-group-item list-group-item-action">Go to Category Management</a>
        <a href="/admin/categories/create" class="list-group-item list-group-item-action">Go to Create Category</a>
        <a href="/admin/reports/statistics" class="list-group-item list-group-item-action">Go to Statistics</a>
        <a href="/admin-test.html" class="list-group-item list-group-item-action">Go to Admin Test Page</a>
    </div>
</div>
</body>
</html>
