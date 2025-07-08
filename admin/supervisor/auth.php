<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['type'] !== 'Supervisor'){
    if($_SESSION['type'] === 'Admin'){
        header("Location: ../admin/index.php?success=You+are+a+admin");
        exit();
    } elseif($_SESSION['type'] === 'Evaluator'){
        header("Location: ../mrg/index.php?success=You+not+have+supervisor+rights");
        exit();
    }
    elseif($_SESSION['type'] === 'Student'){
        header("Location: ../student/index.php?success=You+not+have+Supervisor+rights");
    }
    elseif($_SESSION['type'] === 'DPEGEvaluator'){
        header("Location: ../dpeg/index.php?success=You+are+a+DPEG+Evaluator");
        exit();
    }
    else{
        unset($_SESSION['user']);
        unset($_SESSION['type']);
        header("Location: ../login.php");
        exit();
    }
}
?>