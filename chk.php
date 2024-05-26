<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieve Data</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
            margin-top: 5px;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 150px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .remove-btn {
            padding: 8px 12px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .remove-btn:hover {
            background-color: #bd2130;
        }
        #totalMarks, #chkid {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .form-control {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .form-control label, .form-control input, .form-control button {
            margin: 0 10px;
        }
    </style>
</head>
<body>

<h2>Retrieve Data</h2>

<form method="post" action="">
    <label for="startRegno">Starting Reg No:</label>
    <input type="text" id="startRegno" name="startRegno">
    <label for="endRegno">Ending Reg No:</label>
    <input type="text" id="endRegno" name="endRegno">
    <button type="submit">Retrieve</button>
</form>

<?php
// Include your database connection file
include('connect.php');

$totalMarks = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['startRegno']) && isset($_POST['endRegno'])) {
    $startRegno = $_POST['startRegno'];
    $endRegno = $_POST['endRegno'];

    // Prepare and execute the query
    $sql = "SELECT * FROM student WHERE regno BETWEEN ? AND ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$startRegno, $endRegno]);

    // Check if there are any results
    if ($stmt->rowCount() > 0) {
        echo "<form method='post' id='updateForm'>";
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Reg No</th>";
        echo "<th>Name</th>";
        echo "<th>Mark</th>";
        echo "<th>Chkid</th>";
        echo "<th>Finaltot</th>";
        echo "<th>Action</th>";
        echo "</tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><input type='hidden' name='id[]' value='" . $row['id'] . "'>" . $row['id'] . "</td>";
            echo "<td>" . $row['regno'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td><input type='text' class='mark-input' value='" . $row['mark'] . "' readonly></td>";
            echo "<td><input type='text' name='chkid[]' class='chkid-input' value='" . $row['chkid'] . "'></td>";
            echo "<td><input type='text' name='finaltot[]' class='finaltot-input' value='" . $row['finaltot'] . "' readonly></td>";
            echo "<td><button type='button' class='remove-btn'>Remove</button></td>";
            echo "</tr>";
            
            // Increment total marks
            $totalMarks += $row['mark'];
        }
        echo "</table>";
        echo "<div id='totalMarks'>";
        echo "<label for='totalMarksInput'>Total Marks:</label>";
        echo "<input type='text' id='totalMarksInput' name='totalMarks' value='" . $totalMarks . "'>";
        echo "</div>";
        echo "<div id='chkid'>";
        echo "<label for='chkidInput'>Chkid:</label>";
        echo "<input type='text' id='chkidInput' name='chkid'>";
        echo "</div>";
        echo "<div class='form-control'>";
        echo "<button type='submit' form='updateForm' name='update'>Update</button>";
        echo "</div>";
        echo "</form>";
    } else {
        echo "<p style='text-align: center; color: red;'>No data found for the provided range of registration numbers.</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $ids = $_POST['id'];
    $chkid = $_POST['chkid'];
    $totalMarks = $_POST['totalMarks'];

    for ($i = 0; $i < count($ids); $i++) {
        $sql = "UPDATE student SET chkid = ?, finaltot = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$chkid, $totalMarks, $ids[$i]]);
    }
    echo "<script type='text/javascript'>";
    echo "alert('Updated successfully');";
    echo "window.location.href = window.location.href;";
    echo "</script>";
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Calculate total marks on page load
    calculateTotalMarks();
    
    // Calculate total marks when mark input changes
    $('body').on('input', '.mark-input', function() {
        calculateTotalMarks();
    });

    $('body').on('click', '.remove-btn', function() {
        $(this).closest('tr').remove();
        calculateTotalMarks();
    });

    function calculateTotalMarks() {
        var total = 0;
        $('.mark-input').each(function() {
            var mark = parseInt($(this).val()) || 0;
            total += mark;
        });
        $('#totalMarksInput').val(total);
    }
});
</script>

</body>
</html>
