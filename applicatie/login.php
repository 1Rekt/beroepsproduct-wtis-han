<?php
    include 'setup.php';

    //no role given
    if(!isset($_GET['role'])){
        header('Location: /index.php');
    }

    $role = $_GET['role'];
    $_GET = [];
    
    //define role
    if($role == 'passagier'){
        $_SESSION['user_role'] = 'passagier';
        header('Location: /passagier/index.php');
    }
    if($role == 'medewerker'){
        $_SESSION['user_role'] = 'medewerker';
        header('Location: /medewerker/index.php');
    }

?>