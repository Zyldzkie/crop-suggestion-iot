<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crop Suggestion</title>
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
      background-color: #f8p9fa; /* Subtle off-white */
      padding: 40px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="/">Dashboard</a>
    <a href="/crop">Crop</a>
    <a href="/1sthalf">Average Price 1st</a>
    <a href="/2ndhalf">Average Price 2nd</a>
    <a href="/crop_utilization">Crop Utilization</a>
    <a href="/ph_requirements">pH Requirements</a>
    <a href="/time_of_planting">Planting Time</a>
    <a href="/not_in_season">Not Season</a>
  </div>

  <div class="content">
    <?php include("pages/"."$page" . ".php");?> 
  </div>
</body>
</html>
