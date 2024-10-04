<?php
include 'ConnectionCheck.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Track LogIn/Register</title>
    <link rel="stylesheet" href="LoginPageCss1.css">
</head>
<body>

    <div class="container">
        <h1>QR Track</h1>
        <h2>Sign Up / Sign In</h2>
        <hr />
<div class="form-container active" id="signup">
        <h3 style="display:block; align : center;">New User Registration (Sign Up)</h3>
        <form method="post" action="5.1_Registration.php">
            <div class="form-group">
                <label>Name:</label>
				<div style="display :flex">
                <input type="text" name="first" placeholder="First Name" />
                <input type="text" name="last" placeholder="Last Name" />
				</div>
            </div>

    <div class="form-group">
        <label>Phone Number <i><small>(10 Digits)</small></i>:</label>
        <input type="text" name="phone" placeholder="Phone Number" required />
    </div>

    <div class="form-group">
        <label>Email:</label>
        <input type="text" name="email" placeholder="Email Address" required />

    </div>
            <div class="form-group">
                <div class="form-row">
                <label>Authorization:</label>
                <input type="radio" name="type" value="Student" /> Student
                <input type="radio" name="type" value="Staff" checked /> Staff
            </div>
            </div>

            <div class="form-group">
                <label>Staff / Student ID <i><small>(6 Digits)</small></i>:</label>
                <input type="text" name="id" placeholder="ID" required />
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password1" placeholder="Password" required />
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="password2" placeholder="Confirm Password" required />
            <p><em>(Passwords are case-sensitive and must be at least 6 characters long)</em></p>
			</div>

            
			<div class="buttons">
            <input type="submit" name="register" value="Register" />
			<input type="reset" name="reset" value="Reset" style="margin-left:10px"/>
            
			</div>
        </form>
		<p><a href="#" id="toggleToLogin">Already have an account? Sign In</a></p>
		
		</div>
        
<div class="form-container" id="login">
        <h3>Staff (Sign In)</h3>
        <form method="post" action="5.2_VerifyLogin.php">
            <div class="form-group">
                <label>Staff ID:</label>
                <input type="text" name="Student_Staff_ID" placeholder="ID" required />
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="passwordLogin" placeholder="Password" required />
            

            <p><em>(Passwords are case-sensitive and must be at least 6 characters long)</em></p></div>
			<div class="buttons">
            <input type="submit" name="login" value="Log In" />
			<input type="reset" name="reset" value="Reset" style="margin-left:10px"/>
            
			</div>
        </form>
		
		<p><a href="#" id="toggleToSignup">Don't have an account? Sign Up</a></p>
		</div>
    </div>
	<script>
        document.getElementById("toggleToLogin").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("signup").classList.remove("active");
            document.getElementById("login").classList.add("active");
        });

        document.getElementById("toggleToSignup").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("login").classList.remove("active");
            document.getElementById("signup").classList.add("active");
        });
    </script>

</body>
</html>
