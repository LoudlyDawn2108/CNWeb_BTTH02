<?php
require_once 'config/Database.php';

// Instantiate DB & Connect
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Connection failed\n");
}

echo "Starting data seeding...\n";

// 1. Create Instructor
$instructorUsername = 'instructor_test';
$instructorEmail = 'instructor_test@example.com';
$passwordText = 'password';
$passwordHash = password_hash($passwordText, PASSWORD_BCRYPT);
$instructorName = 'Giảng viên Test';
$roleInstructor = 1; // 1: Giảng viên

// Check if exists
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$instructorUsername]);
$instructor = $stmt->fetch();

if ($instructor) {
    echo "Instructor '$instructorUsername' already exists (ID: " . $instructor['id'] . "). Skipping creation.\n";
    $instructorId = $instructor['id'];
} else {
    $stmt = $db->prepare("INSERT INTO users (username, email, password, fullname, role, status) VALUES (?, ?, ?, ?, ?, 1)");
    if ($stmt->execute([$instructorUsername, $instructorEmail, $passwordHash, $instructorName, $roleInstructor])) {
        $instructorId = $db->lastInsertId();
        echo "Created Instructor: $instructorUsername (ID: $instructorId)\n";
    } else {
        die("Failed to create instructor.\n");
    }
}

// 2. Create Students
$students = [];
for ($i = 1; $i <= 5; $i++) {
    $stuUsername = "student_test_$i";
    $stuEmail = "student_test_$i@example.com";
    $stuName = "Học viên Test $i";
    $roleStudent = 0; // 0: Học viên

    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$stuUsername]);
    $stu = $stmt->fetch();

    if ($stu) {
        echo "Student '$stuUsername' already exists. Skipping.\n";
        $students[] = $stu['id'];
    } else {
        $stmt = $db->prepare("INSERT INTO users (username, email, password, fullname, role, status) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$stuUsername, $stuEmail, $passwordHash, $stuName, $roleStudent]);
        $stuId = $db->lastInsertId();
        $students[] = $stuId;
        echo "Created Student: $stuUsername (ID: $stuId)\n";
    }
}

// 3. Create Courses
$categories = [1, 2, 3]; // Assuming these IDs exist from schema.sql
$coursesData = [
    [
        'title' => 'Lập trình PHP chuyên sâu',
        'desc' => 'Khóa học PHP nâng cao dành cho người đi làm.',
        'cat' => 1,
        'price' => 1000000,
        'level' => 'Advanced'
    ],
    [
        'title' => 'ReactJS Fundamentals',
        'desc' => 'Học ReactJS từ con số 0.',
        'cat' => 1,
        'price' => 500000,
        'level' => 'Beginner'
    ],
    [
        'title' => 'Database Design Masterclass',
        'desc' => 'Tối ưu hóa và thiết kế CSDL chuẩn.',
        'cat' => 3,
        'price' => 800000,
        'level' => 'Intermediate'
    ]
];

$courseIds = [];

foreach ($coursesData as $c) {
    // Check duplicate by title to avoid spamming runs
    $stmt = $db->prepare("SELECT id FROM courses WHERE title = ? AND instructor_id = ?");
    $stmt->execute([$c['title'], $instructorId]);
    $existingCourse = $stmt->fetch();

    if ($existingCourse) {
        echo "Course '{$c['title']}' already exists. Skipping.\n";
        $courseIds[] = $existingCourse['id'];
        continue;
    }

    $stmt = $db->prepare("INSERT INTO courses (title, description, instructor_id, category_id, price, duration_weeks, level, status) VALUES (?, ?, ?, ?, ?, 8, ?, 'approved')");
    if ($stmt->execute([$c['title'], $c['desc'], $instructorId, $c['cat'], $c['price'], $c['level']])) {
        $cid = $db->lastInsertId();
        $courseIds[] = $cid;
        echo "Created Course: {$c['title']} (ID: $cid)\n";

        // Create Lessons for this course
        for ($j = 1; $j <= 5; $j++) {
            $lTitle = "Bài học $j: Giới thiệu về " . $c['title'];
            $lContent = "Nội dung bài học số $j. Đây là nội dung mẫu.";
            $lVideo = "https://www.youtube.com/watch?v=dQw4w9WgXcQ"; // Dummy URL
            
            $stmtL = $db->prepare("INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES (?, ?, ?, ?, ?)");
            $stmtL->execute([$cid, $lTitle, $lContent, $lVideo, $j]);
        }
        echo "  -> Added 5 lessons.\n";
    }
}

// 4. Enroll Students
// Enroll random students to random courses
foreach ($courseIds as $cid) {
    foreach ($students as $sid) {
        // 70% chance to enroll
        if (rand(1, 100) <= 70) {
            // Check existing
            $stmt = $db->prepare("SELECT id FROM enrollments WHERE course_id = ? AND student_id = ?");
            $stmt->execute([$cid, $sid]);
            if ($stmt->fetch()) continue;

            $status = 'active';
            $progress = rand(0, 100);
            if ($progress == 100) $status = 'completed';

            $stmt = $db->prepare("INSERT INTO enrollments (course_id, student_id, status, progress) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cid, $sid, $status, $progress]);
            echo "Enrolled Student $sid to Course $cid (Progress: $progress%)\n";
        }
    }
}

echo "--------------------------------------------------\n";
echo "SEEDING COMPLETED SUCCESSFULLY!\n";
echo "Instructor Login:\n";
echo "  Username: instructor_test\n";
echo "  Password: password\n";
echo "--------------------------------------------------\n";
