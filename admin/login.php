<?php 

session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['type'];
    if($user == 'Admin'){
        header("Location: ../admin/index.php");
        exit();
    }
    else if($user == 'Supervisor'){
        header("Location: ../supervisor/index.php");
        exit();
    }
    else if($user == 'Evaluator'){
        header("Location: ../mrg/index.php");
        exit();
    }
    else if($user == 'Student'){
        header("Location: ../student/index.php");
        exit();
    }
    else if($user == 'DPEGEvaluator'){
        header("Location: ../dpeg/index.php");
        exit();
    }
}

$title="Login";
include('head.php');

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>
<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>

<?php if ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo $error; ?>'
        });
        </script>
    <?php endif; ?>
    <?php if ($success) : ?>
        <script>
        Swal.fire({
                icon: 'success',
                title: 'Succesfully Updated',
                text: '<?php echo $success;?>'
            });
            </script>
    <?php endif; ?>

 

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay" ></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden card-bg-fill galaxy-border-none" >
                            <div class="row g-0">
                                <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100" style="background: url('https://i.ytimg.com/vi/5AKFfv-HNHc/maxresdefault.jpg') no-repeat center center; background-size: cover;">
                                        <div class="bg-overlay" style="background: rgb(2,0,36);
background: linear-gradient(90deg, rgba(2,0,36,0.4290966386554622) 0%, rgba(9,9,121,0.4767156862745098) 35%, rgba(109,2,222,0.0845588235294118) 90%, rgba(0,212,255,0.510329131652661) 100%);"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="index.php" class="d-block">
                                                    <img src="assets/images/logo-light.png" alt="" height="100">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic" style="color: white;">" The best way to predict the future is to create it. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic" style="color: white;">" Quality is not an act, it is a habit."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic" style="color: white;">" In the middle of difficulty lies opportunity. "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p class="text-muted">Sign in to continue to Project Evalution System.</p>
                                        </div>

                                        <div class="mt-4">
                                            <form action="authentication.php" method="post">
                                                <div class="mb-3">
                                                 <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Type of account
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#" data-value="Student">Student</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="Admin">Admin</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="Supervisor">Supervisor</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="Evaluator">MRG Evaluator</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="DPEGEvaluator">DPEG Evaluator</a></li>
                                                    </ul>
                                                </div>
        </div>
                                                <select id="accountType" name="type" class="form-select d-none" required>
                                                <option value="Student">Student</option>
                                                    <option value="Admin">Admin</option>
                                                    <option value="Supervisor">Supervisor</option>
                                                    <option value="Evaluator">MRG Evaluator</option>
                                                    <option value="DPEGEvaluator">DPEG Evaluator</option>
                                                </select>

                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="username" placeholder="Enter username">
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="auth-pass-reset-cover.html" class="text-muted">Forgot password?</a>
                                                    </div>
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input" name="password">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" name="remember" type="checkbox" value="" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Sign In</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
<!-- footer -->
<script>
document.addEventListener('DOMContentLoaded', () => {
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            const dropdownButton = document.getElementById('dropdownMenuButton');
            const selectField = document.getElementById('accountType');
            const form = document.getElementById('typeForm');

            dropdownItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent default action of the link
                    const value = item.getAttribute('data-value');
                    const text = item.textContent;

                    // Update the button text
                    dropdownButton.textContent = text;

                    // Update the select field's value
                    selectField.value = value;

                    // Optionally, submit the form here
                    // form.submit(); // Uncomment this line if you want automatic form submission
                });
            });
        });
    </script>
<footer class="footer galaxy-border-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> Project Evalution System. Crafted with <i class="mdi mdi-heart text-danger"></i> by KITSW
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

   <!-- JAVASCRIPT -->
   <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script disable-devtool-auto src='assets/js/imp.js'></script>

    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>