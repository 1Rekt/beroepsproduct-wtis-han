<?php
function loadMenu(){
    $menu = '<header><nav><div class="container"><ul>';
    
    //home button
    $menu .= '<li><a href="/index.php">Gelre Airport!</a></li>';

    if(isset($_SESSION['user_role'])){
        if($_SESSION['user_role'] == 'passagier'){
            $menu .= '<li><a href="/passagier/index.php">Passagier</a></li>';
        }

        if($_SESSION['user_role'] == 'medewerker'){
            $menu .= '<li><a href="/medewerker/index.php">Medewerker</a></li>';
            $menu .= '<li><a href="/medewerker/nieuwe-vlucht.php">Nieuwe vlucht</a></li>';
            $menu .= '<li><a href="/medewerker/nieuwe-passagier.php">Nieuwe passagier</a></li>';
        }
    }

    $menu .= '</ul></div></nav></header>';

    return $menu;
}

function loadHead($pageTitle = ''){
    return '<head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="/css/style.css" rel="stylesheet">
                <link href="/css/custom.css" rel="stylesheet">
                <title>Gelre Airport - '.$pageTitle.'</title>
            </head>';
}

function loadFooter(){
    return '<footer>
                <div class="container">
                    <ul>
                        <li>Jurre de Klijn @ HAN</li>
                    </ul>
                </div>
            </footer>';
}

function dd($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    exit;
}

function setError($error){
    $_SESSION['errors'][] = $error;
}
function clearErrors(){
    unset($_SESSION['errors']);
}
function showErrors(){

    $html = '';
    if(isset($_SESSION['errors'])){
        $html .= '<section class="errorsDiv"><div class="container">';
        foreach($_SESSION['errors'] as $error){
            $html .= '<div class="error">'.$error.'</div>';
        }
        $html .= '</div></section>';
    }
    
    clearErrors();
    return $html;
}
function checkErrors(){
    if(isset($_SESSION['errors'])){
        return true;
    }
    return false;
}

function setMessage($type,$message){
    $_SESSION[$type][] = $message;
}
function clearMessages(){
    unset($_SESSION['warning']);
    unset($_SESSION['success']);
}
function showMessages(){

    $html = '';
    if(isset($_SESSION['warning'])){
        $html .= '<section class="warningsDiv"><div class="container">';
        foreach($_SESSION['warning'] as $warning){
            $html .= '<div class="warning">'.$warning.'</div>';
        }
        $html .= '</div></section>';
    }
    if(isset($_SESSION['success'])){
        $html .= '<section class="successDiv"><div class="container">';
        foreach($_SESSION['success'] as $message){
            $html .= '<div class="success">'.$message.'</div>';
        }
        $html .= '</div></section>';
    }
    
    clearMessages();
    return $html;
}
function checkMessages(){
    if(isset($_SESSION['warning'])){
        return true;
    }
    if(isset($_SESSION['success'])){
        return true;
    }
    return false;
}

function roleCheck($role){
    if($role == 'medewerker'){
        if($_SESSION['user_role'] == 'medewerker'){
            return true;
        }else{
            echo 'Geen toegang';
            exit;
        }
    }

    if($role == 'passagier'){
        if($_SESSION['user_role'] == 'passagier'){
            return true;
        }else{
            echo 'Geen toegang';
            exit;
        }
    }
}