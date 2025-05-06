<?php
include 'auth.php';
include 'db_connect.php';
if ($_SESSION['user_role'] != 'editor') {
    header("Location: frontpage.php");
    exit();
}
// معالجة إجراءات المحرر
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $news_id = intval($_POST['news_id']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        $query = "UPDATE news SET status = 'approved' WHERE id = $news_id";
    } elseif ($action == 'deny') {
        $query = "UPDATE news SET status = 'denied' WHERE id = $news_id";
    } elseif ($action == 'delete') {
        $query = "DELETE FROM news WHERE id = $news_id";
    }

    mysqli_query($conn, $query);
    header("Location: editor_dashboard.php");
    exit();
}

// جلب كل الأخبار
$query = "SELECT n.id, n.title, n.dateposted, n.status, c.name AS category_name, u.name AS author_name 
          FROM news n 
          JOIN category c ON n.category_id = c.id 
          JOIN user u ON n.author_id = u.id 
          ORDER BY n.dateposted DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontPage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title>لوحة تحكم المحرر - Shasha</title>
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
                    <a class="anchor" href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container col-md-10 mt-4">
        <h2>إدارة الأخبار</h2>
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
                <?php while ($news = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><a href="details.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a></td>
                        <td><?php echo htmlspecialchars($news['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                        <td><?php echo date('d F Y', strtotime($news['dateposted'])); ?></td>
                        <td><?php echo $news['status'] == 'approved' ? 'معتمد' : ($news['status'] == 'pending' ? 'قيد المراجعة' : 'مرفوض'); ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">موافقة</button>
                                <button type="submit" name="action" value="deny" class="btn btn-warning btn-sm">رفض</button>
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