<html>
<head>
  <link rel="stylesheet" href="website/css/crop_utilization.css">
  <script>
    function openModal(id, cropId, crop_utilization) {
      document.getElementById('modal').style.display = 'block';
      document.getElementById('editId').value = id;
      document.getElementById('editCropId').value = cropId;
      document.getElementById('editPrice').value = crop_utilization;
    }

    function closeModal() {
      document.getElementById('modal').style.display = 'none';
    }
  </script>
</head>
<body>
  <h1>Crop Utilization</h1>

  <table border="1" width="100%">
    <tr>
      <th>ID</th>
      <th>Crop ID</th>
      <th>Util. GM/day</th>
      <th>Action</th>
    </tr>
    <?php
      // Assuming you already have a database connection in $conn using OOP
      $sql = "SELECT id, crop_id, utilization_gm_per_day FROM crop_utilization";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['crop_id'] . "</td>";
          echo "<td>" . $row['utilization_gm_per_day'] . "</td>";
          echo "<td><button class='edit-btn' onclick=\"openModal('{$row['id']}', '{$row['crop_id']}', '{$row['utilization_gm_per_day']}')\">Edit</button></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='4'>No records found</td></tr>";
      }
    ?>
  </table>

  <!-- Modal Structure -->
  <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:white; margin:10% auto; padding:20px; width:300px;">
      <h2>Edit Crop Price</h2>
      <form action="/update?u=crop_utilization" method="post">
        <input type="hidden" id="editId" name="id">
        <label>Crop ID:</label>
        <input type="text" id="editCropId" name="crop_id" disabled><br>
        <label>Crop Utilization:</label>
        <input type="text" id="editPrice" name="crop_utilization"><br><br>
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