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
                                <i class="ri-dashboard-2-line"></i>Home
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarLanding" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLanding">
                            <img width="20" height="20" style="margin-right: 5px;" src="https://img.icons8.com/ios-filled/50/bursts.png" alt="bursts"/><span data-key="t-batches">Batches</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarLanding">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-batch.php" class="nav-link" >Add/Edit Batches</a>
                                    </li>
                                </ul>
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="view-batch.php" class="nav-link" >View Batches</a>
                                    </li>
                                </ul>
                            </div>
                            
                        </li>


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                                <i class="ri-account-circle-line"></i> <span data-key="t-supervisor">Supervisors</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAuth">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-supervisor.php" class="nav-link"> Add/Edit Supervisors
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-supervisor.php" class="nav-link"> View Supervisors
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarPages" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPages">
                                <i class="ri-pages-line"></i> <span data-key="t-projectmanager">Evaluators</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarPages">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-mrgevaluator.php" class="nav-link">Add/Edit MRG Evaluators</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-mrgevaluators.php" class="nav-link">View MRG Evaluators</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="add-dpegevaluator.php" class="nav-link">Add/Edit DPEG Evaluators</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-dpegevaluators.php" class="nav-link">View DPEG Evaluators</a>
                                    </li>

                                </ul>
                            </div>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMRG" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMRG">
                            <img src="mrg.svg" alt="" height="20px" width="20px"/ style="margin-right: 5px;"> <span data-key="t-projectmanager">Evaluations Groups</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMRG">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-mrg.php" class="nav-link">Add/Edit Major Research Groups</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-mrg.php" class="nav-link">View Major Research Groups</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="add-dpeg.php" class="nav-link">Add/Edit Department Groups</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-dpeg.php" class="nav-link">View Department Groups</a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMRG" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMRG">
                                <img src="new.svg" alt="" height="20px" width="20px"/ style="margin-right: 5px;"> <span data-key="t-projectmanager">Groups Mapping</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMRG">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-mrgbatches.php" class="nav-link">Add/Edit MRG Batches</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-mrgbatches.php" class="nav-link">View MRG Batches</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="add-dpegbatches.php" class="nav-link">Add/Edit DPEG Sections</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-dpegbatches.php" class="nav-link">View DPEG Sections</a>
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
                                        <a href="add-student.php" class="nav-link" data-key="t-students">Add/Edit Students</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="view-student.php" class="nav-link" data-key="t-students">View Students</a>
                                    </li>

                                </ul>
                            </div>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarmarks" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarmarks">
                            <img width="20" height="20" style="margin-right: 10px;" src="https://img.icons8.com/ios-filled/50/students.png" alt="students"/> <span data-key="t-students">Marks</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarmarks">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="view-marks.php" class="nav-link" data-key="t-students">View Marks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-analytics.php" class="nav-link" data-key="t-students">View Analytics</a>
                                    </li>

                                </ul>
                            </div>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarEvaluations" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEvaluations">
                                <i class="ri-pages-line"></i> <span data-key="t-evaluations">Evaluations</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarEvaluations">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="add-evaluation.php" class="nav-link">Add/Edit Evaluations</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="view-evaluation.php" class="nav-link">View Evaluations</a>
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