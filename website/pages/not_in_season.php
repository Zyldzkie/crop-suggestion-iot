<html>
<head>
  <link rel="stylesheet" href="website/css/not_in_season.css">
  <script>
    function openModal(id, cropId, startMonth, endMonth) {
      document.getElementById('modal').style.display = 'block';
      document.getElementById('editId').value = id;
      document.getElementById('editCropId').value = cropId;
      document.getElementById('editStartMonth').value = startMonth;
      document.getElementById('editEndMonth').value = endMonth;
    }

    function closeModal() {
      document.getElementById('modal').style.display = 'none';
    }
  </script>
</head>
<body>
  <h1>Not In Season</h1>

  <table border="1" width="100%">
    <tr>
      <th>ID</th>
      <th>Crop ID</th>
      <th>Start Month</th>
      <th>End Month</th>
      <th>Action</th>
    </tr>
    <?php
      // Assuming you already have a database connection in $conn using OOP
      $sql = "SELECT id, crop_id, start_month, end_month FROM time_of_planting_not_in_season";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['crop_id'] . "</td>";
          echo "<td>" . $row['start_month'] . "</td>";
          echo "<td>" . $row['end_month'] . "</td>";
          echo "<td><button class='edit-btn' onclick=\"openModal('{$row['id']}', '{$row['crop_id']}', '{$row['start_month']}', '{$row['end_month']}')\">Edit</button></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No records found</td></tr>";
      }
    ?>
  </table>

  <!-- Modal Structure -->
  <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:white; margin:10% auto; padding:20px; width:300px;">
      <h2>Edit Time of Planting</h2>
      <form action="/update?u=not_in_season" method="post">
        <input type="hidden" id="editId" name="id">
        <label>Crop ID:</label>
        <input type="text" id="editCropId" name="crop_id" disabled><br>
        <label>Start Month:</label>
        <select id="editStartMonth" name="start_month">
          <option value="January">January</option>
          <option value="February">February</option>
          <option value="March">March</option>
          <option value="April">April</option>
          <option value="May">May</option>
          <option value="June">June</option>
          <option value="July">July</option>
          <option value="August">August</option>
          <option value="September">September</option>
          <option value="October">October</option>
          <option value="November">November</option>
          <option value="December">December</option>
          <option value="All Season">All Season</option>
        </select><br>
        <label>End Month:</label>
        <select id="editEndMonth" name="end_month">
          <option value="January">January</option>
          <option value="February">February</option>
          <option value="March">March</option>
          <option value="April">April</option>
          <option value="May">May</option>
          <option value="June">June</option>
          <option value="July">July</option>
          <option value="August">August</option>
          <option value="September">September</option>
          <option value="October">October</option>
          <option value="November">November</option>
          <option value="December">December</option>
          <option value="All Season">All Season</option>
        </select><br><br>
        <button type="submit">Save</button>
        <button type="button" onclick="closeModal()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'changed') {
        setTimeout(() => {
        alert('Record updated successfully!');
        }, 100);
    }
  </script>
</body>
</html>
