<?php
// Path to the CSV file
$csvFile = 'lib/log/logfile';

// Read the CSV file into an array
$data = array_map('str_getcsv', file($csvFile));

// Remove the header row
$header = array_shift($data);

// Initialize arrays to store the statistics
$pageVisits = array();
$dateVisits = array();
$userVisits = array();
$sessionVisits = array();

// Process the data
foreach ($data as $row) {
    list($datestamp, $user, $page, $session) = $row;

    // Count page visits
    if (!isset($pageVisits[$page])) {
        $pageVisits[$page] = 0;
    }
    $pageVisits[$page]++;

    // Count visits by date
    $date = substr($datestamp, 0, 10);
    if (!isset($dateVisits[$date])) {
        $dateVisits[$date] = 0;
    }
    $dateVisits[$date]++;

    // Count visits by user
    if (!isset($userVisits[$user])) {
        $userVisits[$user] = 0;
    }
    $userVisits[$user]++;

    // Count visits by session
    if (!empty($session)) {
        if (!isset($sessionVisits[$session])) {
            $sessionVisits[$session] = 0;
        }
        $sessionVisits[$session]++;
    }
}

// Sort the arrays for display
arsort($pageVisits);
arsort($dateVisits);
arsort($userVisits);
arsort($sessionVisits);
?>

<!-- ... (rest of the code remains the same) ... -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        #options-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        p {
            margin: 0;
        }

        input[type="text"] {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }
    </style>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => {
                    console.log('Service Worker registered with scope:', registration.scope);
                })
                .catch(error => {
                    console.error('Service Worker registration failed:', error);
                });
        }
    </script>
    <title>Log Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php 
	include('lib/nav.php'); 
    include('lib/sus_re_dir.php');

?>
    <div id="options-container">

    <h1>Log Statistics</h1>

    <h2>Page Visits</h2>
    <canvas id="pageVisitsChart"></canvas>

    <h2>Visits by Date</h2>
    <canvas id="dateVisitsChart"></canvas>

    <h2>Visits by User</h2>
    <canvas id="userVisitsChart"></canvas>

    <h2>Visits by Session</h2>
    <canvas id="sessionVisitsChart"></canvas>

    <script>
        // Page Visits Chart
        var pageVisitsCtx = document.getElementById('pageVisitsChart').getContext('2d');
        var pageVisitsChart = new Chart(pageVisitsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($pageVisits)); ?>,
                datasets: [{
                    label: 'Page Visits',
                    data: <?php echo json_encode(array_values($pageVisits)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            display: false // This line removes the tick labels
                        }
                    }
                }
            }
        });

        // Visits by Date Chart
        var dateVisitsCtx = document.getElementById('dateVisitsChart').getContext('2d');
        var dateVisitsChart = new Chart(dateVisitsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($dateVisits)); ?>,
                datasets: [{
                    label: 'Visits by Date',
                    data: <?php echo json_encode(array_values($dateVisits)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Visits by User Chart
        var userVisitsCtx = document.getElementById('userVisitsChart').getContext('2d');
        var userVisitsChart = new Chart(userVisitsCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($userVisits)); ?>,
                datasets: [{
                    label: 'Visits by User',
                    data: <?php echo json_encode(array_values($userVisits)); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Visits by Session Chart
        var sessionVisitsCtx = document.getElementById('sessionVisitsChart').getContext('2d');
        var sessionVisitsChart = new Chart(sessionVisitsCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($sessionVisits)); ?>,
                datasets: [{
                    label: 'Visits by Session',
                    data: <?php echo json_encode(array_values($sessionVisits)); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
   </div>
   </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const filePreview = document.getElementById('filePreview');
        const textarea = filePreview.querySelector('textarea');
        const originalContent = textarea.value;

        searchInput.addEventListener('input', filterFileContent);

        function filterFileContent() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const fileContent = textarea.value.toLowerCase();
            const lines = fileContent.split('\n');
            let filteredContent = '';

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (line.includes(searchTerm)) {
                    filteredContent += `${line}\n`;
                }
            }

            textarea.value = filteredContent;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tickCheckbox = document.getElementById('tickCheckbox');

            // Check the stored checkbox state on page load
            const storedCheckboxState = localStorage.getItem('tickCheckboxState');
            if (storedCheckboxState === 'checked') {
                tickCheckbox.checked = true;
            }

            // Function to reload the page after 10 seconds
            function reloadPage() {
                location.reload(); // Reload the current page
            }

            // Check if the checkbox is ticked every second
            setInterval(function() {
                if (tickCheckbox.checked) {
                    // Store the checkbox state in localStorage
                    localStorage.setItem('tickCheckboxState', 'checked');
                    setTimeout(reloadPage, 5000); // 10000 milliseconds = 10 seconds
                } else {
                    // Remove the stored checkbox state if not checked
                    localStorage.removeItem('tickCheckboxState');
                }
            }, 1000); // Check every 1 second (1000 milliseconds)
        });
    </script>
</body>
</html>
