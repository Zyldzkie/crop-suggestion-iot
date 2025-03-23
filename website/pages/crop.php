<html>
<head>
  <link rel="stylesheet" href="website/css/crop.css">
  <script>
    function openModal(id, name) {
      document.getElementById('modal').style.display = 'block';
      document.getElementById('editId').value = id;
      document.getElementById('editName').value = name;
    }

    function closeModal() {
      document.getElementById('modal').style.display = 'none';
    }
  </script>
</head>
<body>
  <h1>Crop List</h1>

  <table border="1" width="100%">
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Action</th>
    </tr>
    <?php
      // Assuming you already have a database connection in $conn using OOP
      $sql = "SELECT id, name FROM crop";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['name'] . "</td>";
          echo "<td><button class='edit-btn' onclick=\"openModal('{$row['id']}', '{$row['name']}')\">Edit</button></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='3'>No records found</td></tr>";
      }
    ?>
  </table>

  <!-- Modal Structure -->
  <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:white; margin:10% auto; padding:20px; width:300px;">
      <h2>Edit Crop Name</h2>
      <form action="/update?u=crop" method="post">
        <input type="hidden" id="editId" name="id">
        <label>Name:</label>
        <input type="text" id="editName" name="name"><br><br>
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
