<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
    // Include connection file
}
require_once "conn.php";


// Define variables and initialize with empty values
$Email = $password = "";
$Email_err = $pass_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["Email"]))) {
        $Email_err = "Please enter your Email.";
    } else {
        $Email = trim($_POST["Email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["Password"]))) {
        $pass_err = "Please enter your password.";
    } else {
        $password = trim($_POST["Password"]);
    }

    // Validate credentials
    if (empty($Email_err) && empty($pass_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, email, password FROM users WHERE email = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = $Email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if Email exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $Email, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["Email"] = $Email;
                            // Redirect user to welcome page
                            header("location: index.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Login</title>
</head>

<body>
    <section>

        <h1>Log In</h1>
        <?php
        if (!empty($login_err)) {
            echo '<div  style="color: red;">' . $login_err . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="infos">
                <div class="email   log-in-email">
                    <input type="email" placeholder="E-Mail Address" name="Email" class="form-control <?php echo (!empty($Email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Email; ?>">
                    <span class="invalid-feedback"><?php echo '<div  style="color: red;">' .  $Email_err . '</div>'; ?></span>

                    <input type="password" placeholder="Password" name="Password" class="form-control <?php echo (!empty($pass_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo '<div  style="color: red;">' . $pass_err . '</div>'; ?></span>
                </div>
                <div class="sign-up-btn search-btn">
                    <input style="background: none; border: none;" name="SignIn" type="submit" value="Sign In">
                </div>
            </div>
        </form>
        <p>Don't have an account? <a href="signup.php"><b class="bold">Sign Up</b></a></p>
    </section>
</body>

</html>