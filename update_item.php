<?php
include "db.php";

$id=$_POST["id"];
if ($_FILES["image"]["name"]) {
  $img="uploads/".time().$_FILES["image"]["name"];
  move_uploaded_file($_FILES["image"]["tmp_name"],$img);
  $conn->query("UPDATE items SET image='$img' WHERE id=$id");
}

$conn->query("
UPDATE items SET
founder='{$_POST["founder"]}',
type='{$_POST["type"]}',
description='{$_POST["description"]}',
created_at=NOW()
WHERE id=$id
");

header("Location: index.php");
