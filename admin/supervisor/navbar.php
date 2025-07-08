<div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-light.png" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="ri-dashboard-2-line"></i> Home
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                                <i class="ri-account-circle-line"></i> <span data-key="t-supervisor">My Batch</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAuth">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="edit-batch.php" class="nav-link"> Edit My Batch
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-batch.php" class="nav-link"> View My Batch
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                       
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarstudents" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarstudents">
                            <img width="20" height="20" style="margin-right: 10px;" src="https://img.icons8.com/ios-filled/50/students.png" alt="students"/> <span data-key="t-students">Students</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarstudents">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="view-student.php" class="nav-link" data-key="t-students">View Students</a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarnew" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarnew">
                                <i class="ri-rocket-line"></i> <span data-key="t-evalutionreports">Marks</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarnew">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-marks.php" class="nav-link" data-key="t-evalutionreports">Award Marks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="edit-marks.php" class="nav-link" data-key="t-evalutionreports">Edit/View Marks</a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>