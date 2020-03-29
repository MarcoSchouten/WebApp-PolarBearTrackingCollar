<?php

// check session, if fails redirect because you can't access protected resource
require_once('./utilities.php');
session_start();
session_check();

$id = $_SESSION['id'];
$email = $_SESSION['email'];
$password = $_SESSION['password'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];

// get all the info that are not in the session
// -------------------------------------------------1  connect to DBMS----------------------------------------------------------
require_once('../db/mysql_credentials.php');
$con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
// -------------------------------------------------2  build query-----------------------------------------------------------

$stmt = $con->prepare('SELECT website, note FROM users WHERE password = ? AND email = ?');
$stmt->bind_param('ss', $password, $email);

// -------------------------------------------------3  execute query----------------------------------------------------------
$stmt->execute();
// -------------------------------------------------4  get results ----------------------------------------------------------
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $note = $row['note'];
    $website = $row['website'];
} else {
    $stmt->close();
    $note = 'error';
    $website = 'error';
}
$con->close();
// ------------------------------------------------- Show page ----------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Save Polar Bears | YOUR actions make a difference</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Libraries by CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <!-- Custom -->
    <link rel="stylesheet" href="../css/custom.css" />
    <script src="../js/custom.js"></script>
</head>

<body>
    <!--------------------------------------------- Navigation--------------------------------------------------->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top nav-bg nav-font shadow-sm">
        <a class="navbar-brand ml-1 my-0    " href="../html/index.html">
            <img src="../img/mylogo.jpg" width="30" height="30" class="d-inline-block align-top" alt="" />
            <span class="nav-brand-font">Save Polar Bears</span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav my-0 ml-auto mr-1">
                <li class="nav-item active">
                    <a class="nav-link" href="../html/about.html">About us</a>
                </li>
                <li class="nav-item px-2">
                    <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo "Welcome " . $firstname ?>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="nav-link" href="../php/logout.php">Logout</a>

                        </div>
                    </div>

                </li>
                <li>
                    <a href="../html/donate.html" class="btn btn-primary px-2">Donate</a>
                </li>
            </ul>
        </div>
    </nav>


    <!--------------------------------------------- content--------------------------------------------------->
    <div class="container py-5 my-5">
        <div class="row">
            <div class="col-md-3 ">
                <div class="list-group ">
                    <a href="#" class="list-group-item list-group-item-action active">Dashboard</a>

                    <a href="show_profile_password.php" class="list-group-item list-group-item-action">Change Password</a>


                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Your Profile</h4>
                                <hr />
                            </div>

                        </div>

                        <div class="row">
                            <label for="email" class="col-4 ">Email</label>
                            <div class="col-8"> <?php echo $email ?> </div>
                            <hr />


                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <form action="update_profile.php" method="post">
                                    <div class="form-group row">
                                        <label for="name" class="col-4 col-form-label">First Name</label>
                                        <div class="col-8">
                                            <input name="firstname" placeholder="First Name" value="<?php echo $firstname ?>" class="form-control here" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="lastname" class="col-4 col-form-label">Last Name</label>
                                        <div class="col-8">
                                            <input name="lastname" value="<?php echo $lastname ?>" placeholder="Last Name" class="form-control here" type="text" />
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="website" class="col-4 col-form-label">Website</label>
                                        <div class="col-8">
                                            <input id="website" name="website" value="<?php echo $website ?>" placeholder="website" class="form-control here" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="publicinfo" class="col-4 col-form-label">Additional Info</label>
                                        <div class="col-8">
                                            <textarea id="note" name="note" cols="40" rows="4" class="form-control"><?php echo $note ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="offset-4 col-8">
                                            <button name="submit" type="submit" class="btn btn-primary">
                                                Update My Profile
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- close row-->
    </div>
    <!-- close container-->

    <!--------------------------------------------- footer--------------------------------------------------->
    <div class="jumbotron text-center footer-style  my-0">
        <p>Copyright &copy; Marco Schouten 2019</p>
        <p>
            This website is a project for the Web App Development module at
            University of Genoa
        </p>
    </div>
    <!-- Close Footer -->
</body>

</html>