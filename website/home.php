<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Side Navbar with HTML</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #2c3e50;
      color: white;
      padding-top: 20px;
    }

    .sidebar a {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
      font-size: 18px;
    }

    .sidebar a:hover {
      background-color: #34495e;
    }

    .content {
      flex-grow: 1;
      background-color: #f8f9fa; /* Subtle off-white */
      padding: 40px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Services</a>
    <a href="#">Contact</a>
    <a href="#">Profile</a>
  </div>

  <div class="content">
    <?php //?> 
  </div>
</body>
</html>
