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
    /* General page styling */
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
        padding: 10px;
        margin: 0;
        line-height: 1.6;
    }

    /* Container for central alignment */
    div {
        max-width: 650px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    /* Styling for buttons */
    button {
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    /* Hover effect for buttons */
    button:hover {
        background-color: #388E3C;
    }

    /* Styling for QR code container */
    #qr_code_container {
        margin-top: 25px;
        text-align: center;
    }

    /* Button container for layout */
    .button-container {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        div {
            padding: 15px;
        }
        button {
            width: 100%;
            margin-top: 10px;
        }
        .button-container {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
<h1>QR Track</h1>
</head>
<body>
    <form action='index.php' method='POST' style="display:inline;">
                <input type='submit' name='logout' value='Log Out' class="logout-button">
            </form>
    <form action='6_StaffPage.php?<?php echo SID;?>' method='POST'>
                <input type='submit' name='back' value='Back' class='btn-back'>
            </form>
    <div>
        <h2>QR Code Generator</h2>
        <table>
            <tr><td>
                <label for="subject_code">Subject Code:</label>        
                </td>
                <td>
                <input type="text" id="subject_code" placeholder="e.g., MATH101">
                </td>
            </tr>
            <tr><td>
                <label for="week">Week:</label>
                </td>
                <td>
                <input type="text" id="week" placeholder="e.g., 1">
                </td>
            </tr>
        </table>
        
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