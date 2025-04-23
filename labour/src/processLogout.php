<?php
session_start();
function logout(): bool
{
    
    session_destroy();
    header("Location: login.php?status=2");
    return true;
}

logout();
