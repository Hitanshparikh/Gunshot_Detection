<?php
include 'config.php';

// Fetch data from the database
$sql = "SELECT timestamp, direction, angle FROM gunshot_directions ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$latestData = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gunshot Detection System</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #000000, #660c0c);
            font-family: 'Roboto', sans-serif;
            color: white;
            padding: 10px;
        }

        h1 {
            font-size: 36px;
            color: #f2f2f2;
            margin-bottom: 20px;
            text-shadow: 4px 4px 10px rgba(0, 0, 0, 0.8);
            letter-spacing: 2px;
            text-align: center;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.7);
            padding: 20px;
            width: 100%;
            max-width: 1000px;
            margin: 0 10px;
        }

        #radar-box {
            width: 100%;
            max-width: 400px;
            height: 400px; /* Make sure the radar box is square */
            border-radius: 50%;
            background: radial-gradient(circle at center, #00c40a, #000000);
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.9), 0 0 15px rgba(0, 200, 255, 0.8);
            border: 2px solid rgba(0, 255, 255, 0.4);
            margin-bottom: 20px;
        }

        .radar-line {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 3px;
            background-color: rgba(0, 255, 0, 1);
            transform-origin: left center;
            animation: radar-sweep 3s infinite linear;
        }

        @keyframes radar-sweep {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .degree-label {
            position: absolute;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            transform: translate(-50%, -50%);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .degree-mark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 60%;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.4);
            transform-origin: left center;
        }

        .blinking-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
        }

        table {
            width: 100%;
            max-width: 350px;
            border-collapse: collapse;
            background-color: rgba(0, 0, 0, 0.5);
            text-align: center;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        th, td {
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: rgba(255, 255, 255, 0.2);
            color: #00d9ff;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 14px;
        }

        tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        #info-section {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.7);
            padding: 20px;
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
        }

        #info-section h2 {
            margin: 0;
            color: #f2f2f2;
        }

        #info-section p {
            font-size: 18px;
            color: #ffffff;
            margin: 10px 0;
        }
        </style>
</head>
<body>
    <h1>Gunshot Detection System</h1>

    <div class="container">
        <!-- Radar Box -->
        <div id="radar-box">
            <div class="radar-line"></div>
            <div id="blinking-indicator" class="blinking-indicator"></div>
        </div>

        <!-- Table for Timestamp, Direction, and Angle -->
        <table>
            <thead>
                <tr>
                    <th>Timestamp (IST)</th>
                    <th>Direction of Arrival</th>
                    <th>Angle</th>
                </tr>
            </thead>
            <tbody id="data-table">
                <!-- Data from backend will be appended here -->
                <?php if ($latestData): ?>
                <tr>
                    <td><?= $latestData['timestamp'] ?></td>
                    <td><?= $latestData['direction'] ?></td>
                    <td><?= $latestData['angle'] ?></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="3">No data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- New Info Section -->
        <div id="info-section">
            <h2>Latest Gunshot Information</h2>
            <p id="latest-timestamp">Timestamp: <?= $latestData['timestamp'] ?? 'N/A' ?></p>
            <p id="latest-direction">Direction: <?= $latestData['direction'] ?? 'N/A' ?></p>
            <p id="latest-angle">Angle: <?= $latestData['angle'] ?? 'N/A' ?></p>
        </div>
    </div>

    <script>
        function addDegreeMarks() {
            const radar = document.getElementById('radar-box');
            const radius = 45;

            for (let i = 0; i < 360; i += 30) {
                const mark = document.createElement('div');
                mark.className = 'degree-mark';
                mark.style.transform = `rotate(${i}deg)`;
                radar.appendChild(mark);

                const label = document.createElement('div');
                label.className = 'degree-label';
                label.textContent = i + '°';
                const labelAngle = i * Math.PI / 180;
                label.style.left = `${50 + radius * Math.sin(labelAngle)}%`;
                label.style.top = `${50 - radius * Math.cos(labelAngle)}%`;
                radar.appendChild(label);
            }
        }

        function addDirectionLabels() {
            const radar = document.getElementById('radar-box');
            const directions = { 0: 'N', 90: 'E', 180: 'S', 270: 'W' };
            const radius = 40;

            Object.keys(directions).forEach(degree => {
                const label = document.createElement('div');
                label.className = 'degree-label';
                label.textContent = directions[degree];
                const angle = degree * Math.PI / 180;
                label.style.left = `${50 + radius * Math.sin(angle)}%`;
                label.style.top = `${50 - radius * Math.cos(angle)}%`;
                radar.appendChild(label);
            });
        }

        window.onload = function () {
            addDegreeMarks();
            addDirectionLabels();
            fetchDataAndUpdateRadar();
        };

        function updateRadar(degree) {
            const radarLine = document.querySelector('.radar-line');
            radarLine.style.transform = `rotate(${degree}deg)`;

            const indicator = document.getElementById('blinking-indicator');
            const radar = document.getElementById('radar-box');
            const radius = radar.offsetWidth / 2;

            // Calculate the position based on angle
            const angleInRadians = degree * Math.PI / 180;
            const x = radius + (radius - 10) * Math.sin(angleInRadians); // Adjust position
            const y = radius - (radius - 10) * Math.cos(angleInRadians); // Adjust position

            indicator.style.left = `${x}px`;
            indicator.style.top = `${y}px`;
        }

        function appendToTable(timestamp, direction, angle) {
            const tableBody = document.getElementById('data-table');
            const directionMap = { 0: 'N', 90: 'E', 180: 'S', 270: 'W' };
            const directionLabel = directionMap[direction] || `${direction}°`;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `<td>${timestamp}</td><td>${directionLabel}</td><td>${angle}</td>`;
            tableBody.insertBefore(newRow, tableBody.firstChild);
        }

        async function fetchDataAndUpdateRadar() {
            try {
                const response = await fetch('fetch_data.php');
                const data = await response.json();
                const { timestamp, direction, angle } = data;

                if (data.error) {
                    console.error(data.error);
                    return;
                }

                updateRadar(angle);  // Use angle to adjust radar indicator
                appendToTable(timestamp, direction, angle);
                updateInfoSection(timestamp, direction, angle); // Update the new info section
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function updateInfoSection(timestamp, direction, angle) {
            document.getElementById('latest-timestamp').textContent = `Timestamp: ${timestamp}`;
            document.getElementById('latest-direction').textContent = `Direction: ${direction}`;
            document.getElementById('latest-angle').textContent = `Angle: ${angle}`;
        }

        // Fetch data every 5 seconds
        setInterval(fetchDataAndUpdateRadar, 50000);
    </script>

</body>
</html>

