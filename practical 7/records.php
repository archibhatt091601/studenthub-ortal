```php
<?php

$csvFile = "registrations.csv";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Stored Registration Records</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #162a2b;
            color: white;
        }

        tr:nth-child(even) {
            background: #f7f7f7;
        }

        .message {
            text-align: center;
            padding: 20px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #162a2b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Stored Registration Records</h1>

    <?php

    // Check if CSV file exists
    if (!file_exists($csvFile)) {

        echo "<div class='message'>";
        echo "<p>No records have been stored yet.</p>";
        echo "<a href='index.html'>Go to Registration Form</a>";
        echo "</div>";

    } else {

        // Open CSV file for reading
        $handle = fopen($csvFile, "r");

        if ($handle === false) {

            echo "<p>Unable to open the CSV file.</p>";

        } else {

            echo "<div class='table-wrapper'>";

            echo "<table>";

            $rowNumber = 0;

            // Read CSV row by row
            while (($row = fgetcsv($handle)) !== false) {

                echo "<tr>";

                foreach ($row as $value) {

                    // First row is table heading
                    if ($rowNumber === 0) {

                        echo "<th>";
                        echo htmlspecialchars($value, ENT_QUOTES, "UTF-8");
                        echo "</th>";

                    } else {

                        echo "<td>";
                        echo htmlspecialchars($value, ENT_QUOTES, "UTF-8");
                        echo "</td>";
                    }
                }

                echo "</tr>";

                $rowNumber++;
            }

            echo "</table>";

            echo "</div>";

            // Close CSV file
            fclose($handle);

            echo "<a href='index.html'>Back to Registration Form</a>";
        }
    }

    ?>

</div>

</body>

</html>
```
