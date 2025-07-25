<?php

if (isset($_SESSION['user'])) {
    sleep(5);
    header("Location: index.php");
    exit();
}


$title = "Sign Up";
include('head.php');
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';

?>
<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100" style="background: url('https://i.ytimg.com/vi/5AKFfv-HNHc/maxresdefault.jpg') no-repeat center center; background-size: cover;">
                                        <div class="bg-overlay" style="background: rgb(2,0,36);
background: linear-gradient(90deg, rgba(2,0,36,0.4290966386554622) 0%, rgba(9,9,121,0.4767156862745098) 35%, rgba(109,2,222,0.0845588235294118) 90%, rgba(0,212,255,0.510329131652661) 100%);"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="index.html" class="d-block">
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
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4" >
                                        <div>
                                            <h5 class="text-primary">Register Account</h5>
                                            <p class="text-muted">Get your Project Evaluation System account.</p>
                                        </div>

                                        <div class="mt-4">
                                            <form class="needs-validation" novalidate action="register.php" method="POST">

                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" placeholder="Name" required>
                                                    <div class="invalid-feedback">
                                                        Please Enter Your Name
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="username" placeholder="Enter Preferred Username" required>
                                                    <div class="invalid-feedback">
                                                        Please Enter Username
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="email" placeholder="***@gmail.com" required>
                                                    <div class="invalid-feedback">
                                                        Please enter your Mail
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" name="password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        <div class="invalid-feedback">
                                                            Please enter password
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-13">Password must contain:</h5>
                                                    <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                    <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                    <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Type of account
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <li><a class="dropdown-item" href="#" data-value="Admin">Admin</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="Supervisor">Supervisor</a></li>
                                                        <li><a class="dropdown-item" href="#" data-value="Project Manager">Project Manager</a></li>
                                                    </ul>
                                                </div>
                                                <select id="accountType" name="type" class="form-select d-none" required>
                                                    <option value="Admin">Admin</option>
                                                    <option value="Supervisor">Supervisor</option>
                                                    <option value="Project Manager">MRG Evaluator</option>
                                                </select>
                                        

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Sign Up</button>
                                        </div>
                                        </form>
                                    </div>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0">Already have an account ? <a href="login.php" class="fw-semibold text-primary text-decoration-underline"> Login</a> </p>
                                    </div>

                                    <br>
                                </div>
                            </div>
                        </div>
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

      <!-- footer -->
      <footer class="footer galaxy-border-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> Project Evaluation System. Crafted with <i class="mdi mdi-heart text-danger"></i> by KITSW
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

    <!-- validation init -->
    <script src="assets/js/pages/form-validation.init.js"></script>
    <!-- password create init -->
    <script src="assets/js/pages/passowrd-create.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($error) : ?>
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
                title: 'Registration Successful',
                text: '<?php echo $success;?>',
                timer: 3000, // Show for 3 seconds
                willClose: () => {
                    window.location.href = 'login.php'; // Redirect to login page after success
                }
            });
            </script>
    <?php endif; ?>

</body>

</html>