<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['type'] !== 'DPEGEvaluator'){
    if($_SESSION['type'] === 'Admin'){
        header("Location: ../admin/index.php?success=You+are+a+admin");
        exit();
    } elseif($_SESSION['type'] === 'Supervisor'){
        header("Location: ../supervisor/index.php?success=You+are+a+supervisor");
        exit();
    }
    elseif($_SESSION['type'] === 'Evaluator'){
        header("Location: ../mrg/index.php?success=You+are+a+supervisor");
        exit();
    }
    elseif($_SESSION['type'] === 'Student'){
        header("Location: ../student/index.php?success=You+not+have+Evaluator+rights");
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