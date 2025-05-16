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

// التحقق من وجود معرف الخبر
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: {$_SESSION['user_role']}_dashboard.php");
    exit();
}

$news_id = intval($_GET['id']);

// جلب الخبر
$query = "SELECT n.title, n.body, n.image, n.category_id, n.author_id, n.keywords, n.status 
          FROM news n 
          WHERE n.id = $news_id";
$result = mysqli_query($conn, $query);
$news = mysqli_fetch_assoc($result);

if (!$news) {
    header("Location: {$_SESSION['user_role']}_dashboard.php");
    exit();
}

// التحقق من صلاحية التعديل
if ($_SESSION['user_role'] == 'author' && $news['author_id'] != $user_id) {
    $error = "غير مصرح لك بتعديل هذا الخبر.";
    header("Location: author_dashboard.php");
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
    $image_path = $news['image']; // الإبقاء على الصورة القديمة افتراضيًا
    $status = $_SESSION['user_role'] == 'admin' ? $news['status'] : 'pending';

    // التحقق من الصورة إذا تم رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2 ميجا
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_dir = 'uploads/';
        $image_path = $upload_dir . $file_name;

        // التحقق من نوع الملف وحجمه
        if (!in_array($file_type, $allowed_types)) {
            $error = "نوع الملف غير مدعوم. يرجى رفع صورة بصيغة JPG أو PNG.";
        } elseif ($file_size > $max_size) {
            $error = "حجم الصورة كبير جدًا. الحد الأقصى 2 ميجا.";
        } else {
            // نقل الصورة الجديدة
            if (move_uploaded_file($file_tmp, $image_path)) {
                // حذف الصورة القديمة إذا كانت موجودة
                if (file_exists($news['image']) && $news['image'] != $image_path) {
                    unlink($news['image']);
                }
            } else {
                $error = "حدث خطأ أثناء رفع الصورة.";
            }
        }
    }

    // التحقق من الحقول
    if (empty($title) || empty($body) || $category_id <= 0) {
        $error = "يرجى ملء جميع الحقول المطلوبة.";
    } elseif (!$error) {
        // تحديث الخبر
        $query = "UPDATE news 
                  SET title = '$title', body = '$body', image = '$image_path', 
                      category_id = $category_id, keywords = '$keywords', status = '$status' 
                  WHERE id = $news_id";
        if (mysqli_query($conn, $query)) {
            $success = "تم تعديل الخبر بنجاح.";
        } else {
            $error = "حدث خطأ أثناء تعديل الخبر: " . mysqli_error($conn);
            // حذف الصورة الجديدة إذا فشل التحديث
            if (file_exists($image_path) && $image_path != $news['image']) {
                unlink($image_path);
            }
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
    <title>تعديل الخبر - Shasha</title>
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
        <h2>تعديل الخبر</h2>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form method="POST" action="" enctype="multipart/form-data">
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
                        <option value="<?php echo $cat['id']; ?>" <?php echo $news['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="keywords" class="form-label">الكلمات المفتاحية</label>
                <input type="text" class="form-control" id="keywords" name="keywords" value="<?php echo htmlspecialchars($news['keywords']); ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">صورة الخبر (اتركها فارغة للاحتفاظ بالصورة الحالية)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/jpg">
                <p class="mt-2">الصورة الحالية: <a href="<?php echo htmlspecialchars($news['image']); ?>" target="_blank">عرض الصورة</a></p>
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </form>
    </div>
</body>
</html>