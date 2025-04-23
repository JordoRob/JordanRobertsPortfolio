<?php
/*
    Generates the navigation bar for the top of the page.
    $selected_id: The id of the page that is currently selected. (refer to arrays in function)
    $version: The version of the nav bar to generate (0 for not logged in, 1 for logged in, 2 for admin)
*/

function generate_nav_bar($selected_id, $version) {
    set_background_css();
    $nav = array();
    if ($version == 0) {
        $nav = array(
            "About" => "about.php", 
            "Login" => "login.php",
		"Home" => "/index.html"

        );
    } else if ($version == 1) {
        $nav = array(
            "Employees" => "/labour/src/Employee Page/employees.php", 
            "Jobs"      => "/labour/src/job-view/job-view.php",
            "History" => "/labour/src/History Page/history.php", 
            "Account"   => "/labour/src/Account Page/account.php",
		"Home" => "/index.html"
        );
        
        if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
            $nav["Admin&nbspView&nbsp→"] = "/labour/src/admin-view/admin-functions/admin_functions.php";
        }
        
        $nav["Logout"] = "/labour/src/processLogout.php";
    } else if ($version == 2) {
        $nav = array(
            "Documentation"         => "/labour/src/admin-view/documentation.php", 
            "Functions"     => "/labour/src/admin-view/admin-functions/admin_functions.php", 
            "User&nbspView&nbsp→"  => "/labour/src/job-view/job-view.php", 
            "Logout"        => "/labour/src/processLogout.php"
        );
    }

    $i = 0;
    foreach ($nav as $title => $link) {
        $nav[$title] = generate_nav_element($i == $selected_id, $title, $link);
        $i++;
    }

    return "
        <div class='main-header'>
            <p id='logo' style='font-size: 2em; text-wrap: nowrap; font-weight:bold; margin-left:5px;max-width:40%;align-self:center;'> Logo Here </p>
            <nav class='main-nav'>                
                " . implode("", $nav) . "
            </nav>
        </div>
    ";
}

/*
    Generates a single navigation element.
    $is_selected: Whether or not the element is selected.
    $title: The title of the element.
    $href: The href of the element.
*/

function generate_nav_element($is_selected, $title, $href) {
    $selected = "";
    if ($is_selected) {
        $selected = "class='selected-nav'";
        $href = "#";
    }
    return "<a href='$href' $selected>$title</a>";
}

function set_background_css() {
    echo "<style id='background_image_styles'>";
    if (isset($_SESSION['use_background']) && $_SESSION['use_background'] == 1) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/labour/src/img/backgrounds/" . $_SESSION['user'] . ".jpg")) {
            echo "
                body {
                    background-image: url('/labour/src/img/backgrounds/" . $_SESSION['user'] . ".jpg');
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    background-position: center;
                }
            ";
        } else {
            $_SESSION['use_background'] = 0;
        }
    }
    echo "</style>";
}
?>