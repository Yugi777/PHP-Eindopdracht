<html lang="en">
<head>
    <title>F i l e b r o w s e r</title>
    <link href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
function human_filesize($bytes, $dec = 2)
{
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

if (isset($_GET['dir']) && isset($_GET['file'])) {
    $file = $_GET['dir'] . '/' . $_GET['file'];
}

if (isset($_POST['textadd'])) {
    $textadd = $_POST['textadd'];
    file_put_contents($file, $textadd);
}

$cwd = getcwd();

if (isset($_GET['dir'])) {
    $cwd = $_GET['dir'];
    $cwd = realpath($cwd);
}

if(!str_contains($cwd, getcwd())){
    $cwd = getcwd();
}

$all = scandir($cwd);
$all = array_slice($all, 1);

echo '<div id="breadcrumb">';
$breadcrumbs = explode('\\', str_replace(getcwd(), '', $cwd));
$breadcrumbbuilder = "";
echo '<a href="' . "index.php?dir=" . getcwd() . '">' . "root" . '</a>';
foreach ($breadcrumbs as $crumb) {
    $breadcrumbbuilder .= "/" . $crumb;
    echo '<a href="' . "index.php?dir=" . getcwd() . $breadcrumbbuilder . '">' . $crumb . '</a> ➜ ';
}
if (isset($_GET['file'])) {
    echo $_GET['file'];
}
echo '</div>';

echo '<div id="dirfiles">';

if ($cwd == getcwd()) {
    $all = array_slice($all, 1);
}
foreach ($all as $item) {
    if (is_dir($cwd . '/' . $item)) {
        echo '[D] <a href="index.php?dir=' . $cwd . '/' . $item . '">' . $item . "</a><br>";
    } else {
        echo '[F] <a href="index.php?dir=' . $cwd . '&file=' . $item . '">' . $item . "</a><br>";
    }
}
echo '</div>';

echo '<div id="contents"><b>Inhoud:</b><br>';

if (isset($_GET['file'])) {
    echo 'Bestandsnaam: ' . $_GET['file'] . '<br>';
    $bytes = filesize($file);
    echo "Bestandsgrootte: " . human_filesize($bytes) . "<br>";
    if (is_writable($file)) {
        echo 'Schrijfbaar: Ja<br>';
    } else {
        echo 'Schrijfbaar: Nee<br>';
    }
    echo 'Laatst aangepast op: ' . date("j-m-y", filemtime($file)) . '<br>';
    echo 'Bestandstype: ' . mime_content_type($file) . '<br><br>';
}

if (isset($_GET['file'])) {
    $mime = explode('/', mime_content_type($file))[0];
    $phpcheck = explode('/', mime_content_type($file))[1];

    if ($mime == "image") {
        $imgpath = str_replace(getcwd(), '', $file);
        $imgpath = ltrim($imgpath, '\\');

        echo '<img src="' . $imgpath . '" height="70%">';
    }

    if ($mime == "text") {
        $inhoud = file_get_contents($file);

        echo '<form method="post">
        <textarea name="textadd" rows="20" cols="20">' . htmlentities($inhoud) . '</textarea><br>';
        if($phpcheck != "html"){
            echo '<input type="submit" value="Aanpassen">';
        }
        echo '</form>';
    }
}
echo '</div>';
?>
</body>
</html>
