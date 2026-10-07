```php
<?php

// --------------------------------------------------
// 1. CHECK WHETHER FORM WAS SUBMITTED USING POST
// --------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request. Please submit the form.");
}


// --------------------------------------------------
// 2. RECEIVE AND SANITIZE THE INPUTS
// --------------------------------------------------

$fullName = trim($_POST["fullName"] ?? "");
$email    = trim($_POST["email"] ?? "");
$mobile   = trim($_POST["mobile"] ?? "");
$course   = trim($_POST["course"] ?? "");
$year     = trim($_POST["year"] ?? "");
$gender   = trim($_POST["gender"] ?? "");
$message  = trim($_POST["message"] ?? "");

// Sanitize text inputs
$fullName = htmlspecialchars($fullName, ENT_QUOTES, "UTF-8");
$email    = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
$mobile   = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
$course   = htmlspecialchars($course, ENT_QUOTES, "UTF-8");
$year     = htmlspecialchars($year, ENT_QUOTES, "UTF-8");
$gender   = htmlspecialchars($gender, ENT_QUOTES, "UTF-8");
$message  = htmlspecialchars($message, ENT_QUOTES, "UTF-8");


// --------------------------------------------------
// 3. CREATE ERROR ARRAY
// --------------------------------------------------

$errors = [];


// --------------------------------------------------
// 4. VALIDATE THE INPUTS
// --------------------------------------------------

// Validate Full Name
if (empty($fullName)) {
    $errors[] = "Full name is required.";
} elseif (strlen($fullName) < 3) {
    $errors[] = "Full name must contain at least 3 characters.";
}


// Validate Email
if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}


// Validate Mobile Number
if (empty($mobile)) {
    $errors[] = "Mobile number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}


// Validate Course
$validCourses = ["bca", "bba", "bcom", "ba", "bsc", "other"];

if (empty($course)) {
    $errors[] = "Please select a course.";
} elseif (!in_array($course, $validCourses, true)) {
    $errors[] = "Invalid course selected.";
}


// Validate Year
$validYears = ["1", "2", "3", "4", "other"];

if (empty($year)) {
    $errors[] = "Please select your year.";
} elseif (!in_array($year, $validYears, true)) {
    $errors[] = "Invalid year selected.";
}


// Validate Gender
$validGenders = ["female", "male", "other", "prefer-not-to-say"];

if (empty($gender)) {
    $errors[] = "Please select your gender.";
} elseif (!in_array($gender, $validGenders, true)) {
    $errors[] = "Invalid gender selected.";
}


// Validate Message
if (empty($message)) {
    $errors[] = "Message is required.";
} elseif (strlen($message) < 5) {
    $errors[] = "Message must contain at least 5 characters.";
}


// --------------------------------------------------
// 5. IF THERE ARE ERRORS, DISPLAY ERROR MESSAGE
// --------------------------------------------------

if (!empty($errors)) {

    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    echo "<title>Form Errors</title>";

    echo "<style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 40px;
            }

            .box {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            }

            h2 {
                color: #b42318;
            }

            li {
                margin: 10px 0;
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
          </style>";

    echo "</head>";
    echo "<body>";

    echo "<div class='box'>";

    echo "<h2>Please correct the following errors:</h2>";

    echo "<ul>";

    foreach ($errors as $error) {
        echo "<li>" . $error . "</li>";
    }

    echo "</ul>";

    echo "<a href='index.html'>Go Back to Form</a>";

    echo "</div>";

    echo "</body>";
    echo "</html>";

    exit;
}


// --------------------------------------------------
// 6. CSV FILE HANDLING
// --------------------------------------------------

$csvFile = "registrations.csv";


// Check whether CSV file exists
$fileExists = file_exists($csvFile);


// Open CSV file safely
$handle = fopen($csvFile, "a");


// Check whether file was opened successfully
if ($handle === false) {

    die("Error: Unable to open the CSV file.");
}


// --------------------------------------------------
// 7. WRITE CSV HEADER IF FILE IS NEW
// --------------------------------------------------

if (!$fileExists || filesize($csvFile) === 0) {

    $header = [
        "Full Name",
        "Email",
        "Mobile",
        "Course",
        "Year",
        "Gender",
        "Message"
    ];

    fputcsv($handle, $header);
}


// --------------------------------------------------
// 8. STORE FORM DATA IN CSV
// --------------------------------------------------

$data = [
    $fullName,
    $email,
    $mobile,
    $course,
    $year,
    $gender,
    $message
];


// Write data to CSV
if (!fputcsv($handle, $data)) {

    fclose($handle);

    die("Error: Unable to save your information.");
}


// --------------------------------------------------
// 9. CLOSE THE CSV FILE
// --------------------------------------------------

fclose($handle);


// --------------------------------------------------
// 10. DISPLAY SUCCESS MESSAGE
// --------------------------------------------------

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Registration Successful</title>";

echo "<style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }

        .box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        h2 {
            color: #176b4d;
        }

        a {
            display: inline-block;
            margin: 10px;
            padding: 10px 18px;
            background: #162a2b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
      </style>";

echo "</head>";
echo "<body>";

echo "<div class='box'>";

echo "<h2>✓ Registration Successful!</h2>";

echo "<p>Thank you, <strong>" . $fullName . "</strong>.</p>";

echo "<p>Your information has been successfully saved.</p>";

echo "<a href='index.html'>Back to Form</a>";

echo "<a href='records.php'>View Stored Records</a>";

echo "</div>";

echo "</body>";
echo "</html>";

?>
```
