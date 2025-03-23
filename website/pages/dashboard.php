<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="website/css/dashboard.css">
</head>
<body>
  <div class="dashboard-container">
    <h1>Soil Monitoring Dashboard</h1>
    
    <div class="dashboard-grid">
      <!-- Soil Parameters Table -->
      <div class="dashboard-card">
        <h2>Soil Parameters</h2>
        <table id="soil-params-table" class="data-table">
          <thead>
            <tr>
              <th>Parameter</th>
              <th>Value</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Nitrogen (N)</td>
              <td id="nitrogen-value">Loading...</td>
              <td id="nitrogen-status">Loading...</td>
            </tr>
            <tr>
              <td>Phosphorus (P)</td>
              <td id="phosphorus-value">Loading...</td>
              <td id="phosphorus-status">Loading...</td>
            </tr>
            <tr>
              <td>Potassium (K)</td>
              <td id="potassium-value">Loading...</td>
              <td id="potassium-status">Loading...</td>
            </tr>
            <tr>
              <td>pH Level</td>
              <td id="ph-value">Loading...</td>
              <td id="ph-status">Loading...</td>
            </tr>
          </tbody>
        </table>
        <div id="last-updated"></div>
      </div>
      
      <!-- GM/day Based Crops Table -->
      <div class="dashboard-card">
        <h2>GM/day Based Crops</h2>
        <table id="gm-crops-table" class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Crop Name</th>
            </tr>
          </thead>
          <tbody id="gm-crops-body">
            <tr><td colspan="2">Loading...</td></tr>
          </tbody>
        </table>
      </div>
      
      <!-- Price Based Crops Table -->
      <div class="dashboard-card">
        <h2>Price Based Crops</h2>
        <table id="price-crops-table" class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Crop Name</th>
            </tr>
          </thead>
          <tbody id="price-crops-body">
            <tr><td colspan="2">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <style>
  .dashboard-container {
    padding: 20px;
    font-family: Arial, sans-serif;
  }

  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }

  .dashboard-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }

  .data-table th, .data-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }

  .data-table th {
    background-color: #f2f2f2;
  }

  #last-updated {
    margin-top: 10px;
    font-size: 12px;
    color: #666;
  }

  .status-low {
    color: red;
    font-weight: bold;
  }

  .status-normal {
    color: green;
  }
  </style>

  <script>
  // Function to interpret nitrogen value
  function interpretNitrogen(value) {
    if (value < 10) return "Low";
    if (value < 20) return "Medium";
    if (value < 30) return "High";
    return "Excessive";
  }

  // Function to interpret phosphorus value
  function interpretPhosphorus(value) {
    if (value < 20) return "Low";
    if (value < 40) return "Medium";
    if (value < 100) return "High";
    return "Excessive";
  }

  // Function to interpret potassium value
  function interpretPotassium(value) {
    if (value < 75) return "Very Low";
    if (value < 150) return "Low";
    if (value < 250) return "Medium";
    if (value < 800) return "High";
    return "Very High";
  }

  // Function to format timestamp
  function formatTimestamp(timestamp) {
    const date = new Date(timestamp * 1000);
    return date.toLocaleString();
  }

  // Function to update the dashboard with sensor data
  function updateDashboard() {
    fetch('/get_sensor_data')
      .then(response => response.json())
      .then(data => {
        console.log('Received data from API:', data);
        
        if (data.sensor_data) {
          const sensorData = data.sensor_data;
          console.log('Sensor data:', sensorData);
          
          // Update soil parameters
          document.getElementById('nitrogen-value').textContent = sensorData.nitrogen + ' mg/kg';
          document.getElementById('phosphorus-value').textContent = sensorData.phosphorus + ' mg/kg';
          document.getElementById('potassium-value').textContent = sensorData.potassium + ' mg/kg';
          document.getElementById('ph-value').textContent = sensorData.pH_level;
          
          // Update status
          const nitrogenStatus = interpretNitrogen(sensorData.nitrogen);
          const phosphorusStatus = interpretPhosphorus(sensorData.phosphorus);
          const potassiumStatus = interpretPotassium(sensorData.potassium);
          
          document.getElementById('nitrogen-status').textContent = nitrogenStatus;
          document.getElementById('phosphorus-status').textContent = phosphorusStatus;
          document.getElementById('potassium-status').textContent = potassiumStatus;
          document.getElementById('ph-status').textContent = "Normal";
          
          // Add status classes
          if (nitrogenStatus === "Low") {
            document.getElementById('nitrogen-status').className = 'status-low';
          } else {
            document.getElementById('nitrogen-status').className = 'status-normal';
          }
          
          if (phosphorusStatus === "Low") {
            document.getElementById('phosphorus-status').className = 'status-low';
          } else {
            document.getElementById('phosphorus-status').className = 'status-normal';
          }
          
          if (potassiumStatus === "Very Low" || potassiumStatus === "Low") {
            document.getElementById('potassium-status').className = 'status-low';
          } else {
            document.getElementById('potassium-status').className = 'status-normal';
          }
          
          // Update last updated time
          document.getElementById('last-updated').textContent = 'Last updated: ' + formatTimestamp(sensorData.timestamp);
        } else {
          console.warn('No sensor data received');
        }
        
        console.log('suggestedCrops1:', data.suggestedCrops1);
        console.log('suggestedCrops2:', data.suggestedCrops2);
        
        // Update GM/day based crops
        const gmCropsBody = document.getElementById('gm-crops-body');
        gmCropsBody.innerHTML = '';
        
        if (data.suggestedCrops1 && data.suggestedCrops1.length > 0) {
          data.suggestedCrops1.forEach((crop, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${index + 1}</td><td>${crop.name}</td>`;
            gmCropsBody.appendChild(row);
          });
        } else {
          gmCropsBody.innerHTML = '<tr><td colspan="2">No crops available</td></tr>';
        }
        
        // Update price based crops
        const priceCropsBody = document.getElementById('price-crops-body');
        priceCropsBody.innerHTML = '';
        
        if (data.suggestedCrops2 && data.suggestedCrops2.length > 0) {
          data.suggestedCrops2.forEach((crop, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${index + 1}</td><td>${crop.name}</td>`;
            priceCropsBody.appendChild(row);
          });
        } else {
          priceCropsBody.innerHTML = '<tr><td colspan="2">No crops available</td></tr>';
        }
      })
      .catch(error => {
        console.error('Error fetching sensor data:', error);
      });
  }

  // Update the dashboard immediately and then every 5 seconds
  updateDashboard();
  setInterval(updateDashboard, 5000);
  </script>
</body>
</html>
