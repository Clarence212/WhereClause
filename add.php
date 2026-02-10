<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Lost Item</title>
<link rel="stylesheet" href="css/form.css">
</head>
<body>

<form class="form-box" action="save.php" method="POST" enctype="multipart/form-data">
  <a href="index.php" class="close-btn">✖</a>
  <h2>Add Lost Item</h2>

  <!-- Change founder -> user to match DB column -->
 

  <select name="type">
    <option value="electronic">Electronic</option>
    <option value="clothing">Clothing</option>
    <option value="money">Money</option>
    <option value="jewelry">Jewelry</option>
    <option value="other">Other</option>
  </select>

  <textarea name="description">Description</textarea>

  <input type="file" name="image">

  <button type="submit">Save</button>
</form>

</body>
</html>
