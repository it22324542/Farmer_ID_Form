<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Form Data Test</title></head><body>";
echo "<h2>Testing Form Submission</h2>";
echo "<h3>POST Data:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h3>FILES Data:</h3>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

echo "<h3>Server Info:</h3>";
echo "<pre>";
echo "Request Method: " . $_SERVER["REQUEST_METHOD"] . "\n";
echo "Content Type: " . ($_SERVER["CONTENT_TYPE"] ?? 'Not set') . "\n";
echo "</pre>";

echo "<a href='../Farmer_id_form.php'>Back to Form</a>";
echo "</body></html>";
?>
