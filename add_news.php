<?php
include 'auth.php';
include 'db_connect.php';

if ($_SESSION['user_role'] != 'author' && $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// جلب التصنيفات
$cat_query = "SELECT id, name FROM category";
$cat_result = mysqli_query($conn, $cat_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);
    $category_id = intval($_POST['category_id']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
    $image = 'https://picsum.photos/300/200'; // صورة افتراضية
    $dateposted = date('Y-m-d H:i:s');
    $status = $_SESSION['user_role'] == 'admin' ? 'approved' : 'pending';

    // التحقق من الحقول
    if (empty($title) || empty($body) || $category_id <= 0) {
        $error = "يرجى ملء جميع الحقول المطلوبة.";
    } else {
        $query = "INSERT INTO news (title, body, image, dateposted, category_id, author_id, status, keywords) 
                  VALUES ('$title', '$body', '$image', '$dateposted', $category_id, $user_id, '$status', '$keywords')";
        if (mysqli_query($conn, $query)) {
            $success = "تم إضافة الخبر بنجاح.";
        } else {
            $error = "حدث خطأ أثناء إضافة الخبر: " . mysqli_error($conn);
        }
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
    <title>إضافة خبر جديد - Shasha</title>
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
                    <a class="anchor" href="<?php echo $_SESSION['user_role']; ?>_dashboard.php">العودة إلى لوحة التحكم</a>
                    <a class="anchor" href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container col-md-8 mt-4">
        <h2>إضافة خبر جديد</h2>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="title" class="form-label">عنوان الخبر</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="body" class="form-label">محتوى الخبر</label>
                <textarea class="form-control" id="body" name="body" rows="10" required></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">التصنيف</label>
                <select class="form-control" id "category_id" name="category_id" required>
                    <option value="">اختر تصنيفًا</option>
                    <?php while ($cat = mysqli_fetch_assoc($cat_result)) { ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="keywords" class="form-label">الكلمات المفتاحية</label>
                <input type="text" class="form-control" id="keywords" name="keywords">
            </div>
            <button type="submit" class="btn btn-primary">إضافة الخبر</button>
        </form>
    </div>
</body>
</html>