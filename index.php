<?php require_once "./Classes/Path.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Manager</title>

  <link rel="stylesheet" href="style.css">
  <script src="js/main.js"></script>
  <script src="js/index.js" defer></script>
  <script src="js/appearance.js" defer></script>
</head>
<body>
  <img src="http://localhost/project-manager/EndPoints/theme-image-GET.php" class="bgImage" alt="bgimage">

  <pre class="PHP-Exception con"></pre>

  <div class="projects-manipulation con">
    <input type="text" placeholder="Project name">
    <div class="controls">
      <button class="cancel">Cancel</button>
      <button class="submit">Submit</button>
      <button class="delete">Delete</button>
    </div>
  </div>

  <header class="con">
    <h5>Project Manager</h5>
    <button id="options" class="icon">
      <img src="./svg/options.svg" alt="opt">
    </button>
  </header>

  <div class="main center project-select">
    <div class="con">
      <div class="projects"></div>
    </div>
  </div>
</body>
</html>