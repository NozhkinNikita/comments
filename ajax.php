<?php
require_once("Comment.class.php");
$comment = new Comment();
$action  = $_POST['action'];
if ($action == "delete") {
    $id   = mysql_escape_string($_POST['id']);
    $json = $comment->deleteComment($id);
    $json = json_encode($json);
    print($json);
    
} else {
    $name   = mysql_escape_string($_POST['name']);
    $parent = mysql_escape_string($_POST['parent']);
    $text   = mysql_escape_string($_POST['text']);
    $comment->addComment($parent, $name, $text);
    print($comment->getLastId());
}
?>

