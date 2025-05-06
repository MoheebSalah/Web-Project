<?php
include 'auth.php';
include 'db_connect.php';

if ($_SESSION['user_role'] != 'author') {
    header("Location: frontpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب أخبار المؤلف
$query = "SELECT n.id, n.title, n.dateposted, n.status, c.name AS category_name 
          FROM news n 
          JOIN category c ON n.category_id = c.id 
          WHERE n.author_id = $user_id 
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
    <title>لوحة تحكم المؤلف - Shasha</title>
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
        <h2>أخبارك</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>التصنيف</th>
                    <th>تاريخ النشر</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($news = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><a href="details.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a></td>
                        <td><?php echo htmlspecialchars($news['category_name']); ?></td>
                        <td><?php echo date('d F Y', strtotime($news['dateposted'])); ?></td>
                        <td><?php echo $news['status'] == 'approved' ? 'معتمد' : ($news['status'] == 'pending' ? 'قيد المراجعة' : 'مرفوض'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>