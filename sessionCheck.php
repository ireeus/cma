<?php
include('config.php');
if (isset($_COOKIE[$usernameCookieName])) {
    $username = $_COOKIE[$usernameCookieName]; 
    $decryptedUsername= openssl_decrypt($username, 'aes-256-cbc', $secretkey, 0, $iv);
        
    //checking if the user in the cookie is registered
    $filename = $dir.'users/'. $decryptedUsername . '.php'; 
    if (!file_exists($filename)) {
        header('Location: login.php');
        }else{
            //creating session if cookie exit and the user is registred
            $_SESSION['username'] = $decryptedUsername;

			}  
		}
    
// Check if the user is logged in, redirect to chat.php
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$recoveryFilePath = $dir.'recovery/'. $_SESSION['username'] . '.php'; 
//redirect in case if the user not provided the security answers
if (!file_exists($recoveryFilePath)) {
	header("Location: questionnaire.php");
	exit();
	}

