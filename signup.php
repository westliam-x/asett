<?php
// Include config file
require_once "conn.php";

// Define variables and initialize with empty values
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["Email"]))) {
        $email_err = "Please enter an Email";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE Email = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = trim($_POST["Email"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $email_err = "This Email is already taken.";
                } else {
                    $email = trim($_POST["Email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["Password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["Password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["Password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        //The ret of the inputed data
        $userame = $_POST['userame'];

        // Prepare an insert statement
        $sql = "INSERT INTO users (username	, email, password) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $userame, $param_email, $param_password);

            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
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
    <link rel="stylesheet" href="assets/style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up </title>
</head>

<body>
    <section>
        <div class="d-v-section  text-box">
            <h1>Sign Up</h1>
            <div class="infos">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="names">
                        <input type="text" name="userame" placeholder="Username">
                    </div>
                    <div class="email">
                        <?php echo $email_err; ?>
                        <input type="email" placeholder="E-Mail Address" name="Email" id="">
                        <?php echo $password_err; ?>
                        <input type="password" placeholder="Enter Password" name="Password" id="">
                        <?php echo $confirm_password_err; ?>
                        <input type="password" placeholder="confirm your password" name="confirm_password" id="">
                    </div>
                    <div class="sign-up-btn search-btn">
                        <input style="background: none; border: none;" name="SignIn" type="submit" value="Sign Up">
                    </div>
                </form>

                <p>Already have an account? <a href="login.php">
                        <b class="bold">Log In</b>
                    </a></p>
            </div>

        </div>
    </section>


</body>

</html>