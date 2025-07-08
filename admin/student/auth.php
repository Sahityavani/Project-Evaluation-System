<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['type'] !== 'Student'){
    if($_SESSION['type'] === 'Admin'){
        header("Location: ../admin/index.php?success=You+are+a+admin");
        exit();
    } elseif($_SESSION['type'] === 'Supervisor'){
        header("Location: ../mrg/index.php?success=You+are+a+supervisor");
        exit();
    }
    elseif($_SESSION['type'] === 'Evaluator'){
        header("Location: ../student/index.php?success=You+are+a+evaluator");
    }
    elseif($_SESSION['type'] === 'DPEGEvaluator'){
        header("Location: ../dpeg/index.php?success=You+a+DPEG+Evaluator");
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