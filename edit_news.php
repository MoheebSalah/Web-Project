<?php
include 'auth.php';
include 'db_connect.php';

if ($_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$news_id = intval($_GET['id']);
$error = '';
$success = '';

// جلب الخبر
$query = "SELECT n.title, n.body, n.category_id, n.keywords 
          FROM news n 
          WHERE n.id = $news_id";
$result = mysqli_query($conn, $query);
$news = mysqli_fetch_assoc($result);

if (!$news) {
    header("Location: admin_dashboard.php");
    exit();
}

// جلب التصنيفات
$cat_query = "SELECT id, name FROM category";
$cat_result = mysqli_query($conn, $cat_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);
    $category_id = intval($_POST['category_id']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);

    if (empty($title) || empty($body) || $category_id <= 0) {
        $error = "يرجى ملء جميع الحقول المطلوبة.";
    } else {
        $query = "UPDATE news SET title = '$title', body = '$body', category_id = $category_id, keywords = '$keywords' WHERE id = $news_id";
        if (mysqli_query($conn, $query)) {
            $success = "تم تعديل الخبر بنجاح.";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "حدث خطأ أثناء تعديل الخبر: " . mysqli_error($conn);
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
    <title>تعديل خبر - Shasha</title>
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
                    <a class="anchor" href="admin_dashboard.php">العودة إلى لوحة التحكم</a>
                    <a class="anchor" href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container col-md-8 mt-4">
        <h2>تعديل خبر</h2>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="title" class="form-label">عنوان الخبر</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="body" class="form-label">محتوى الخبر</label>
                <textarea class="form-control" id="body" name="body" rows="10" required><?php echo htmlspecialchars($news['body']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">التصنيف</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">اختر تصنيفًا</option>
                    <?php while ($cat = mysqli_fetch_assoc($cat_result)) { ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $news['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="keywords" class="form-label">الكلمات المفتاحية</label>
                <input type="text" class="form-control" id="keywords" name="keywords" value="<?php echo htmlspecialchars($news['keywords']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </form>
    </div>
</body>
</html>