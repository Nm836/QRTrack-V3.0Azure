<?php
session_start();
include '7_StaffClass.php';
if (isset($_POST['QRCodeGenerator'])) {
            
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance</title>
    <style>
        /* Basic styling for form */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        div {
            max-width: 600px;
            margin: auto;
        }
        button {
            margin-top: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #qr_code_container {
            margin-top: 20px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <h1>QR Code Attendance</h1>
    <form action='6_StaffPage.php?<?php echo SID;?>' method='POST'>
                <input type='submit' name='back' value='Back' class='btn-back'>
            </form>
    <div>
        <h2>Teacher</h2>
        <label for="subject_code">Subject Code:</label>
        <input type="text" id="subject_code" placeholder="e.g., MATH101">
        <br>
        <label for="week">Week:</label>
        <input type="text" id="week" placeholder="e.g., 1">
        <br>
        <div class="button-container">
            <button id="generate_qr">Generate QR Code</button>
            <button id="view_dashboard" onclick="location.href='/teacher_dashboard'">Record</button>
        </div>
        <div id="qr_code_container"></div>
    </div>

    <script>
        document.getElementById("generate_qr").addEventListener("click", () => {
            const subjectCode = document.getElementById('subject_code').value;
            const week = document.getElementById('week').value;

            if (!subjectCode || !week) {
                alert("Please enter both subject code and week.");
                return;
            }

            const formData = new FormData();
            formData.append('subject_code', subjectCode);
            formData.append('week', week);

            const qrContainer = document.getElementById('qr_code_container');
            qrContainer.innerHTML = '<div class="spinner"></div>'; // Show spinner while fetching

            fetch('/generate_qr', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                qrContainer.innerHTML = `
                    <img src="data:image/png;base64,${data.qr_code}" alt="QR Code" style="display: block; margin-bottom: 20px;">
                    <a href="/download_qr/${data.session_id}" download>
                        <button>Download QR Code</button>
                    </a>
                `;
            })
            .catch(error => {
                qrContainer.innerHTML = 'Error generating QR code. Please try again.';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
<?php
}
?>