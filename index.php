<?php
$doc = new DOMDocument();
$doc->loadHTML("
<!DOCTYPE html>
<html>

<head>
<link rel='stylesheet' href='style.css'>
<script async src='https://www.googletagmanager.com/gtag/js?id=G-71VN63R68V'></script>
<script>
window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-71VN63R68V');
</script>
</head>

<body>
<form class='form' id='form' action='' method='post' name='guestbook'>
    <input placeholder='Naam' type='text' required id='author' name='author'>
    <input placeholder='Bericht' type='text' required id='message' name='message'>
    <input id='submit' name='submit' type='submit'>
</form>
<div class='gastenboek' id='gastenboek'>
</div>
</body>

</html>");


if(session_id() == '') {session_start();}

// Toon alle foutmeldingen:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logingegevens voor de database
$ini_arr = parse_ini_file("./.env");
$servername = "localhost";
$username = "klas4s22_579571";
$database_name = "klas4s22_579571";
$password = array_pop($ini_arr);


// Meer informatie over DSN: http://php.net/manual/en/pdo.construct.php
$database_connection = new PDO("mysql:host=$servername;dbname=$database_name", $username, $password);
//        echo "<script type='text/javascript'>alert('Je hebt al een bericht geplaatst!');</script>";
if(isset($_POST["submit"])) {
    foreach (explode(" ", htmlspecialchars($_POST["message"])) as $w) {
        if(strlen($w) > 20) {
            $telang = true;
        }
        else {
            $telang = false;
        }

    }
    if (isset($_SESSION['al_gestuurd'])) {
        echo "<script type='text/javascript'>alert('Je hebt al een bericht achtergelaten!');</script>";
    }elseif ($telang) {
        echo "<script type='text/javascript'>alert('Een woord in je bericht is te lang, probeer het nog eens.');</script>";
    } else {

        $author = htmlspecialchars($_POST["author"]);
        $message = htmlspecialchars($_POST["message"]);
        $_SESSION['al_gestuurd'] = true;
        $datetime = strval(date("Y-m-d H:i:s"));
        $sth = $database_connection->prepare("INSERT INTO berichten (name, text, datetime) values (?, ?, ?)");
        $sth->execute([$author, $message, $datetime]);
    }

//        $query = $database_connection->quer:w
//("INSERT INTO berichten (name, text, datetime) values ('" . $author . "', '" . $message . "', '" . $datetime . "')");
}


$query = $database_connection->query("SELECT * FROM berichten group by datetime desc");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $node = $doc->getElementById('gastenboek');
    $fragment= $doc->createDocumentFragment();
    $fragment->appendXML("<div class='container'><div class='info'><h1>".$row['name']."</h1><div class='datetime'><h5>".$row['datetime']."</h5></div></div><div class='message'><p>".$row['text']."</p></div></div>");
    $node->appendChild($fragment);

}

echo $doc->saveHTML();
//echo "</table>";
?>

