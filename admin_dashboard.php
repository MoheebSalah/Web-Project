<?php
include 'auth.php';
include 'db_connect.php';
if ($_SESSION['user_role'] != 'admin') {
    header("Location: frontpage.php");
    exit();
}

$error = '';
$success = '';

$user_query = "SELECT id, name, email, role FROM user ORDER BY id";
$user_result = mysqli_query($conn, $user_query);

$news_query = "SELECT n.id, n.title, n.dateposted, n.status, c.name AS category_name, u.name AS author_name 
               FROM news n 
               JOIN category c ON n.category_id = c.id 
               JOIN user u ON n.author_id = u.id 
               ORDER BY n.dateposted DESC";
$news_result = mysqli_query($conn, $news_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO user (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if (mysqli_query($conn, $query)) {
        $success = "تم إضافة المستخدم بنجاح.";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "حدث خطأ: " . mysqli_error($conn);
    }    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = ($_POST['user_id']);
    if ($user_id != $_SESSION['user_id']) { 
        $query = "DELETE FROM user WHERE id = $user_id";
        mysqli_query($conn, $query);
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "لا يمكن حذف حسابك الخاص.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontPage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title>لوحة تحكم الإداري - Shasha</title>
</head>
<body class="arabic-font">
    <nav class="row p-4">
        <div class="container col-md-10">
            <div class="row">
                <div class="col-md-6">
                    <img class="col-md-2" src="https://shasha.ps/storage/2024/05/09/logo.png" alt="">
                    <span>مرحبًا، <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
                <div class="col-md-6 text-end">
                    <a class="anchor" href="frontPage.php">الصفحة الرئيسية</a>
                    <a class="anchor" href="add_news.php">إضافة خبر جديد</a>
                    <a class="anchor" href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container col-md-10 mt-4">
        <h2>إدارة المستخدمين</h2>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <h3>إضافة مستخدم جديد</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">الاسم</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">الإيميل</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">الدور</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="author">مؤلف</option>
                    <option value="editor">محرر</option>
                    <option value="admin">إداري</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">إضافة المستخدم</button>
        </form>

        <h3 class="mt-5">قائمة المستخدمين</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الإيميل</th>
                    <th>الدور</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role'] == 'author' ? 'مؤلف' : ($user['role'] == 'editor' ? 'محرر' : 'إداري'); ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3 class="mt-5">إدارة الأخبار</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>التصنيف</th>
                    <th>المؤلف</th>
                    <th>تاريخ النشر</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($news = mysqli_fetch_assoc($news_result)) { ?>
                    <tr>
                        <td><a href="details.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a></td>
                        <td><?php echo htmlspecialchars($news['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                        <td><?php echo date('d F Y', strtotime($news['dateposted'])); ?></td>
                        <td><?php echo $news['status'] == 'approved' ? 'معتمد' : ($news['status'] == 'pending' ? 'قيد المراجعة' : 'مرفوض'); ?></td>
                        <td>
                            <a href="edit_news.php?id=<?php echo $news['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <form method="POST" action="editor_dashboard.php" style="display: inline;">
                                <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>