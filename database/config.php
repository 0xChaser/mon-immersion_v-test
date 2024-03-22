<?php

define("HOTE", 'localhost');
define("BDD", 'flois1974934_2efkdi');
define("UTILISATEUR",'root');
define("MDP",'');


function connect()
{
    try
    {
        $connect = new PDO('mysql:host=' . HOTE . ';dbname='.  BDD , UTILISATEUR, MDP,
        array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));

    
    return $connect;
    }
    catch(PDOException $e)
    {  
        echo $e ->getMessage() ;
        return false ;
    }
}

function includeFileBasedOnRole() {
    include_once '../menus/menu_administrateur.php' ;
}

function getNNIFromSession() {
    $userInfo = "A62382";
    return $userInfo;
}

function getnomfromSession() {
    $userInfo = "ISAK";
    return $userInfo;
}

function getPrenomfromSession() {
    $userInfo = "Florian";
    return $userInfo;
}

function GetCodeFsdumOfAgent() {
    $userInfo = 130776151;
    return $userInfo;
}

function getFormattedNameFromSession() {
    $userInfo = "Florian ISAK";
    return $userInfo;
}


function getEmailFromSession() {
    $userInfo = "florian.isak@enedis.fr";
    return $userInfo;
}