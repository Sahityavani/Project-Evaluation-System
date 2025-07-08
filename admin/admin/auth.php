<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['type'] !== 'Admin'){
    if($_SESSION['type'] === 'Supervisor'){
        header("Location: ../supervisor/index.php?success=You+not+have+admin+rights");
        exit();
    } elseif($_SESSION['type'] === 'Evaluator'){
        header("Location: ../mrg/index.php?success=You+not+have+admin+rights");
        exit();
    }
    elseif($_SESSION['type'] === 'Student'){
        header("Location: ../student/index.php?success=You+not+have+admin+rights");
    }
    elseif($_SESSION['type'] === 'DPEGEvaluator'){
        header("Location: ../dpeg/index.php?success=You+not+have+Admin+rights");
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