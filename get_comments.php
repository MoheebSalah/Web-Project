<?php
include 'db_connect.php';

if (isset($_GET['news_id']) && is_numeric($_GET['news_id'])) {
    $news_id = (int) $_GET['news_id'];

    $query = "SELECT commenter_name, comment_text, created_at 
              FROM comments 
              WHERE news_id = $news_id 
              ORDER BY created_at DESC";

    $result = mysqli_query($conn, $query);

    $comments_html = '';

    while ($comment = mysqli_fetch_assoc($result)) {
        $name = htmlspecialchars($comment['commenter_name']);
        $text = nl2br(htmlspecialchars($comment['comment_text']));
        $date = date('d F Y, H:i', strtotime($comment['created_at']));

        $comments_html .= "
        <div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>$name</h5>
                <p class='card-text'>$text</p>
                <p class='card-text'><small class='text-muted'>$date</small></p>
            </div>
        </div>";
    }

    echo $comments_html;
}
?>
