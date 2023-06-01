<?php

if (isset($_POST['fullname'])) {
    from_submit();
}

/**
 * Displays site name.
 */
function site_name()
{
    echo config('name');
}

/**
 * Displays site url provided in config.
 */
function site_url()
{
    echo config('site_url');
}

/**
 * Displays site version.
 */
function site_version()
{
    echo config('version');
}

/**
 * Website navigation.
 */
function nav_menu($sep = ' | ')
{
    $nav_menu = '';
    $nav_items = config('nav_menu');

    foreach ($nav_items as $uri => $name) {
        $query_string = str_replace('page=', '', $_SERVER['QUERY_STRING'] ?? '');
        $class = $query_string == $uri ? ' active' : '';
        $url = config('site_url') . '/' . (config('pretty_uri') || $uri == '' ? '' : '?page=') . $uri;

        // Add nav item to list. See the dot in front of equal sign (.=)
        $nav_menu .= '<li class="nav-item" ><a class="nav-link" href="' . $url . '" title="' . $name . '" class="' . $class . '">' . $name . '</a></li>';
    }

    echo trim($nav_menu, $sep);
}

/**
 * Displays page title. It takes the data from
 * URL, it replaces the hyphens with spaces and
 * it capitalizes the words.
 */
function page_title()
{
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'Home';

    echo ucwords(str_replace('-', ' ', $page));
}

/**
 * Displays page content. It takes the data from
 * the static pages inside the pages/ directory.
 * When not found, display the 404 error page.
 */
function page_content()
{
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $path = getcwd() . '/' . config('content_path') . '/' . $page . '.phtml';

    if (!file_exists($path)) {
        $path = getcwd() . '/' . config('content_path') . '/404.phtml';
    }

    echo file_get_contents($path);
}


function from_submit()
{
    $conn = OpenCon();
    //i used this to crypt https://www.javatpoint.com/how-to-encrypt-or-decrypt-a-string-in-php#:~:text=In%20the%20PHP%20programming%20language,both%20methods%20of%20PHP%20language.
    $ciphering_value = "AES-128-CTR";
    $encryption_key = "Oumaima";
    $message = openssl_encrypt($_POST['message'], $ciphering_value, $encryption_key);
    // i used this to hash https://www.php.net/manual/en/function.md5.php
    $fullname = md5($_POST['fullname']);
    $sql = "INSERT INTO contacts(fullname, email, phone, message) VALUES ('" . $fullname . "','" . $_POST['email'] . "','" . $_POST['phone'] . "','" . $message . "')";
    if ($conn->query($sql) === TRUE) {
        header('Location: /home');
    } else {
        header('Location: /about-us');
    }

    CloseCon($conn);
}

/**
 * Starts everything and displays the template.
 */
function init()
{
    require config('template_path') . '/template.php';
}

function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "webapp";
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n" . $conn->error);

    return $conn;
}

function CloseCon($conn)
{
    $conn->close();
}
