<?php
include 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: frontPage.php");
    exit();
}

$news_id = intval($_GET['id']);

$query = "SELECT n.id, n.title, n.body, n.dateposted, n.views, n.keywords, n.category_id, c.name AS category_name, u.name AS author_name 
          FROM news n 
          JOIN category c ON n.category_id = c.id 
          JOIN user u ON n.author_id = u.id 
          WHERE n.id = $news_id AND n.status = 'approved'";
$result = mysqli_query($conn, $query);
$news = mysqli_fetch_assoc($result);

if (!$news) {
    header("Location: frontPage.php");
    exit();
}

$update_views = "UPDATE news SET views = views + 1 WHERE id = $news_id";
mysqli_query($conn, $update_views);

$category_id = isset($news['category_id']) ? intval($news['category_id']) : 1;
$more_news_query = "SELECT n.id, n.title 
                    FROM news n 
                    WHERE n.status = 'approved' AND n.category_id = $category_id AND n.id != $news_id 
                    ORDER BY n.dateposted DESC LIMIT 3";
$more_news_result = mysqli_query($conn, $more_news_query);

$related_news_query = "SELECT n.id, n.title, c.name AS category_name 
                       FROM news n 
                       JOIN category c ON n.category_id = c.id 
                       WHERE n.status = 'approved' AND n.id != $news_id 
                       ORDER BY n.dateposted DESC LIMIT 3";
$related_news_result = mysqli_query($conn, $related_news_query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontPage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title><?php echo htmlspecialchars($news['title']); ?> - Shasha</title>
</head>

<body class="arabic-font">
    <nav class="row p-4">
        <div class="container col-md-10">
            <div class="row">
                <div class="col-md-4">
                    <img class="col-md-4" src="https://shasha.ps/storage/2024/05/09/logo.png" alt="">
                    <a class="anchor" href="frontPage.php">الرئيسية</a>
                    <a class="anchor" href="category.php?id=1">سياسة</a>
                    <a class="anchor" href="category.php?id=2">اقتصاد</a>
                    <a class="anchor" href="category.php?id=3">رياضة</a>
                    <a class="anchor" href="category.php?id=4">صحة</a>
                </div>
                <div class="col-md-5">
                </div>
                <div class="row col-md-3">
                    <div class="col-md-8 p-2">
                        <input class="col-md-12" type="search" name="" id="" placeholder="ادخل كلمة البحث" style="border-radius: 5px; border: 0px;">
                    </div>
                    <div class="col-md-2">
                        <span class="col-md-8">الخليل</span>
                        <img class="col-md-12" src="Assets/image.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="row mt-4">
        <div class="container col-md-10">
            <div class="row">
                <h6 class="news-cat"><?php echo htmlspecialchars($news['category_name']); ?> - فلسطين</h6>
                <h4 style="font-weight: bold;"><?php echo htmlspecialchars($news['title']); ?></h4>
                <img class="col-md-2" src="Assets/solar--calendar-linear.png" style="width: 48px;" alt="">
                <span class="col-md-8 news-cat"><?php echo date('d F Y', strtotime($news['dateposted'])); ?></span>
                <h5 class="mt-3 col-md-4" style="font-weight: bold; color:#1d3557">شارك القصة</h5>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <img class="col-md-12" src="https://picsum.photos/300/200" alt="">
                    <div class="col-md-12">
                        <p class="news-cat p-2" style="background-color: rgb(1, 1, 1,0.2); font-size: 0.8em;"><?php echo htmlspecialchars(substr($news['body'], 0, 100)); ?>...</p>
                        <button style="background: none; border: none; cursor: pointer"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAbdJREFUWEftl4FNAzEMRd1NYBJgEsokwCTQSaCTwCZwD86VE5zkB5BOSEQ6tVIvzvP3d5LubOOx23h9+3MAZ2a2N7MLM+M7z+uqIp9HM3teH0lcVQEWelgCX0pRP6FuFBAF4M7MbkOWdabAAcYnykTI+2Ue85tjBPAUAiLtlaCAlylCn7fm9QB8cTImk0dh8fhKVK4J3wKg3piNAb0bbZLhoywv6yQSwBfFyACoIdkzkBz6nwwSIaE0Xgbg0is1f1sDj7zkin6JmU1Ug7K2+m4sRaFqDeBypfVK6qACMNWVLVqzBnCphv27wswAeFcUZagBcCxy4Val7WYA3Nx01GlfqAE8YNZ6/pvaEXVs98FmAKlpWyVQ+3+mBG7wrgKpUzuazwC4CYsOqxVInfpLAN5hhcF7RqEMozNgRoHU4L2tWNmMVIDm9p4BxG1T3Q96rdk93FqHiHuBEiilaAHEZNLdtQUQ74BAHJYVulerhCAew9MXEuIBwbXKLyZ4gixGxqwvsN1jfXSOZ/c7AOLF1GGpdX0pHRp5BODKfudaLnlHBYggZHrd+WNC1qMynSwzC6CehPJ7/wDvfHF9IUv4k44AAAAASUVORK5CYII=" alt=""></button>
                        <span style="font-weight: bold;">الخط</span>
                        <button style="background: none; border: none; cursor: pointer"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAbRJREFUWEftl4FNAzEMRd1NYBJgEmASYBJgEmAS2ATuwbk4qRM7baQTEpGqIvXOef7+ccxONl67jfeXPwdwJiI3InIhIvzN52NVke83EXldPylxswqw0eMS+DIV9QfqNgOSAbgXkTuTZZ0pcIDxjTIW8mF5j/ebKwJ4MQGR9iqhgJbJQp+33usB6OZkTCZPic3tI1a5JnwLgHpjNhb0arRBhu+yvK8vkQC+KJYHQA3JnoXk0J+ySISE3HgegEqfrXkGThU9iOkBfK4RI4NmNtZnbCkKVetNVC63XiM7Os+qssXRrAFUqvD8HgGjp6IoQw2AY5ELt44eu4hJzc2J2veFGkDr7x09/S3aSH+vY6sPNgMA7MDgrRLMOP+1UmrwrgKuU7OaB8+pCYsTVivgOnUSgJ6wwuA9o1CGY+8Aj9k1eK8Vz2xGzfbuAdi2OaMfdC+3Vr9XL1CCU0phk3G7awvAzoBAPC9F7Y5WTtHtNTw8kBAPCMYqHUzwBFlExqwH2O61Hl253nwHgB1MFZZa10NpaOQIwN7no2N5yjtZAAtCptedf0zIOirT3jKjAJOa4m+Yf4Av0wJzIf0mRAcAAAAASUVORK5CYII=" alt=""></button>
                        <p class="mt-5" style="font-weight: bold;"><?php echo nl2br(htmlspecialchars($news['body'])); ?></p>
                        <h3 style="font-weight: bold;">خروق الاحتلال لوقف النار في غزة</h3>
                        <p class="mt-5" style="font-weight: bold;"><?php echo nl2br(htmlspecialchars($news['body'])); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <h3 style="font-weight: 700; border-bottom: solid #1d3557; width: fit-content;">المزيد من فلسطين</h3>
                    </div>
                    <div class="col-md-12" style="border: solid rgb(1, 1, 1,0.3) 2px;"></div>
                    <ul class="mb-5">
                        <?php while ($more_news = mysqli_fetch_assoc($more_news_result)) { ?>
                            <li class="mt-3 p-2" style="list-style:square; border-bottom: solid rgb(1, 1, 1, 0.2) 1px;">
                                <a href="details.php?id=<?php echo $more_news['id']; ?>" style="text-decoration: none; color: inherit;"><?php echo htmlspecialchars($more_news['title']); ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <img class="col-md-12 mt-5" src="https://picsum.photos/300/200" alt="">
                    <p>AD</p>
                    <div class="container mt-5">
                        <div class="mb-3">
                            <h3 class="fw-bold border-bottom border-3 border-primary pb-2" style="width: fit-content;">موضوعات ذات صلة</h3>
                        </div>
                        
                        <div class="border-bottom border-2 mb-4" style="border-color: rgba(1, 1, 1, 0.3) !important;"></div>
                        
                        <?php while ($related_news = mysqli_fetch_assoc($related_news_result)) { ?>
                            <div class="row mb-4 align-items-center">
                                <div class="col-md-4 col-sm-12 mb-3 mb-md-0">
                                    <a href="details.php?id=<?php echo $related_news['id']; ?>">
                                        <img src="https://picsum.photos/300/200" alt="صورة الخبر" class="img-fluid rounded w-100">
                                    </a>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <h6 class="news-cat text-muted">
                                        <?php echo htmlspecialchars($related_news['category_name']); ?>
                                    </h6>
                                    <p class="fw-bold">
                                        <a href="details.php?id=<?php echo $related_news['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($related_news['title']); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="row mt-4">
        <div class="container col-md-10">
            <div class="row p-4">
                <div class="col-md-3 row">
                    <img class="col-md-5" src="https://shasha.ps/storage/2024/05/09/logo.png" alt="">
                    <p class="col-md-12 mt-3">
                        تغطية اخبارية شاملة ومتعددة الوسائط للأحداث العربية والعالمية. ويتيح الوصول إلى شبكة منوعة من البرامج السياسة والاجتماعية.
                    </p>
                </div>
                <div class="col-md-3">
                    <ul>
                        <li style="font-size: 1.1em; font-weight: bold;">روابط</li>
                        <li><a class="fanchor" href="category.php?id=1">سياسة</a></li>
                        <li><a class="fanchor" href="category.php?id=2">اقتصاد</a></li>
                        <li><a class="fanchor" href="#">فن وثقافة</a></li>
                        <li><a class="fanchor" href="category.php?id=3">رياضة</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul>
                        <li style="font-size: 1.1em; font-weight: bold;">عن الموقع</li>
                        <li><a class="fanchor" href="#">من نحن</a></li>
                        <li><a class="fanchor" href="#">اعلن معنا</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 style="font-size: 1.1em; font-weight: bold;">اتصل بنا</h6>
                    <div class="button-container">
                        <button class="button flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="22px" class="button-svg" viewBox="0 0 24 24">
                                <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill="#fff" d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                    <path fill="#fff" d="M18 5C17.4477 5 17 5.44772 17 6C17 6.55228 17.4477 7 18 7C18.5523 7 19 6.55228 19 6C19 5.44772 18.5523 5 18 5Z"></path>
                                    <path fill="#fff" d="M1.65396 4.27606C1 5.55953 1 7.23969 1 10.6V13.4C1 16.7603 1 18.4405 1.65396 19.7239C2.2292 20.8529 3.14708 21.7708 4.27606 22.346C5.55953 23 7.23969 23 10.6 23H13.4C16.7603 23 18.4405 23 19.7239 22.346C20.8529 21.7708 21.7708 20.8529 22.346 19.7239C23 18.4405 23 16.7603 23 13.4V10.6C23 7.23969 23 5.55953 22.346 4.27606C21.7708 3.14708 20.8529 2.2292 19.7239 1.65396C18.4405 1 16.7603 1 13.4 1H10.6C7.23969 1 5.55953 1 4.27606 1.65396C3.14708 2.2292 2.2292 3.14708 1.65396 4.27606ZM13.4 3H10.6C8.88684 3 7.72225 3.00156 6.82208 3.0751C5.94524 3.14674 5.49684 3.27659 5.18404 3.43597C4.43139 3.81947 3.81947 4.43139 3.43597 5.18404C3.27659 5.49684 3.14674 5.94524 3.0751 6.82208C3.00156 7.72225 3 8.88684 3 10.6V13.4C3 15.1132 3.00156 16.2777 3.0751 17.1779C3.14674 18.0548 3.27659 18.5032 3.43597 18.816C3.81947 19.5686 4.43139 20.1805 5.18404 20.564C5.49684 20.7234 5.94524 20.8533 6.82208 20.9249C7.72225 20.9984 8.88684 21 10.6 21H13.4C15.1132 21 16.2777 20.9984 17.1779 20.9249C18.0548 20.8533 18.5032 20.7234 18.816 20.564C19.5686 20.1805 20.1805 19.5686 20.564 18.816C20.7234 18.5032 20.8533 18.0548 20.9249 17.1779C20.9984 16.2777 21 15.1132 21 13.4V10.6C21 8.88684 20.9984 7.72225 20.9249 6.82208C20.8533 5.94524 20.7234 5.49684 20.564 5.18404C20.1805 4.43139 19.5686 3.81947 18.816 3.43597C18.5032 3.27659 18.0548 3.14674 17.1779 3.0751C16.2777 3.00156 15.1132 3 13.4 3Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                </g>
                            </svg>
                        </button>
                        <button class="button flex-center">
                            <svg viewBox="0 -2 20 20" width="22px" class="button-svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#fff">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <title>twitter [#154]</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs></defs>
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="Dribbble-Light-Preview" transform="translate(-60.000000, -7521.000000)" fill="#fff">
                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                <path d="M10.29,7377 C17.837,7377 21.965,7370.84365 21.965,7365.50546 C21.965,736 Uit5.33021 21.965,7365.15595 21.953,7364.98267 C22.756,7364.41163 23.449,7363.70276 24,7362.8915 C23.252,7363.21837 22.457,7363.433 21.644,7363.52751 C22.5,7363.02244 23.141,7362.2289 23.448,7361.2926 C22.642,7361.76321 21.761,7362.095 20.842,7362.27321 C19.288,7360.64674 16.689,7360.56798 15.036,7362.09796 C13.971,7363.08447 13.518,7364.55538 13.849,7365.95835 C10.55,7365.79492 7.476,7364.261 5.392,7361.73762 C4.303,7363.58363 4.86,7365.94457 6.663,7367.12996 C6.01,7367.11125 5.371,7366.93797 4.8,7366.62489 L4.8,7366.67608 C4.801,7368.5989 6.178,7370.2549 8.092,7370.63591 C7.488,7370.79836 6.854,7370.82199 6.24,7370.70483 C6.777,7372.35099 8.318,7373.47829 10.073,7373.51078 C8.62,7374.63513 6.825,7375.24554 4.977,7375.24358 C4.651,7375.24259 4.325,7375.22388 4,7375.18549 C5.877,7376.37088 8.06,7377 10.29,7376.99705" id="twitter-[#154]"></path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                        <button class="button flex-center">
                            <svg stroke="#fff" fill="#fff" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" version="1.1" class="button-svg" width="22px" viewBox="0 0 20 20">
                                <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <title>github [#fff142]</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs></defs>
                                    <g fill-rule="evenodd" fill="none" stroke-width="1" stroke="none" id="Page-1">
                                        <g fill="#fff" transform="translate(-140.000000, -7559.000000)" id="Dribbble-Light-Preview">
                                            <g transform="translate(56.000000, 160.000000)" id="icons">
                                                <path id="github-[#fff142]" d="M94,7399 C99.523,7399 104,7403.59 104,7409.253 C104,7413.782 101.138,7417.624 97.167,7418.981 C96.66,7419.082 96.48,7418.762 96.48,7418.489 C96.48,7418.151 96.492,7417.047 96.492,7415.675 C96.492,7414.719 96.172,7414.095 95.813,7413.777 C98.04,7413.523 100.38,7412.656 100.38,7408.718 C100.38,7407.598 99.992,7406.684 99.35,7405.966 C99.454,7405.707 99.797,7404.664 99.252,7403.252 C99.252,7403.252 98.414,7402.977 96.505,7404.303 C95.706,7404.076 94.85,7403.962 94,7403.958 C93.15,7403.962 92.295,7404.076 91.497,7404.303 C89.586,7402.977 88.746,7403.252 88.746,7403.252 C88.203,7404.664 88.546,7405.707 88.649,7405.966 C88.01,7406.684 87.619,7407.598 87.619,7408.718 C87.619,7412.646 89.954,7413.526 92.175,7413.785 C91.889,7414.041 91.63,7414.493 91.54,7415.156 C90.97,7415.418 89.522,7415.871 88.63,7414.304 C88.63,7414.304 88.101,7413.319 87.097,7413.247 C87.097,7413.247 86.122,7413.234 87.029,7413.87 C87.029,7413.87 87.684,7414.185 88.139,7415.37 C88.139,7415.37 88.726,7417.2 91.508,7416.58 C91.513,7417.437 91.522,7418.245 91.522,7418.489 C91.522,7418.76 91.338,7419.077 90.839,7418.982 C86.865,7417.627 84,7413.783 84,7409.253 C84,7403.59 88.478,7399 94,7399"></path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                        <button class="button flex-center">
                            <svg viewBox="0 -3 20 20" width="22px" class="button-svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#fff" stroke="#fff">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <title>youtube [#fff168]</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs></defs>
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="Dribbble-Light-Preview" transform="translate(-300.000000, -7442.000000)" fill="#fff">
                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                <path d="M251.988432,7291.58588 L251.988432,7285.97425 C253.980638,7286.91168 255.523602,7287.8172 257.348463,7288.79353 C255.843351,7289.62824 253.980638,7290.56468 251.988432,7291.58588 M263.090998,7283.18289 C262.747343,7282.73013 262.161634,7282.37809 261.538073,7282.26141 C259.705243,7281.91336 248.270974,7281.91237 246.439141,7282.26141 C245.939097,7282.35515 245.493839,7282.58153 245.111335,7282.93357 C243.49964,7284.42947 244.004664,7292.45151 244.393145,7293.75096 C244.556505,7294.31342 244.767679,7294.71931 245.033639,7294.98558 C245.376298,7295.33761 245.845463,7295.57995 246.384355,7295.68865 C247.893451,7296.0008 255.668037,7296.17532 261.506198,7295.73552 C262.044094,7295.64178 262.520231,7295.39147 262.895762,7295.02447 C264.385932,7293.53455 264.28433,7285.06174 263.090998,7283.18289" id="youtube-[#fff168]"></path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>