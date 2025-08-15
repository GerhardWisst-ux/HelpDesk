<head>
  <title>HelpDesk Hinzufügen Kunde</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php

  require 'db.php';
  session_start();

  // Regeln
  $minLength = 3;
  $maxLength = 100;
  $alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:.]+$/';

  $firma = trim($_POST['firma']);
  $zusatz = trim($_POST['zusatz']);
    $street = trim($_POST['street']);
  $location = trim($_POST['location']);
  $mail = trim($_POST['mail']);
  $internet = trim($_POST['internet']);
  $telefon = trim($_POST['telefon']);
  $fax = trim($_POST['fax']);
  $priceperhour = trim($_POST['priceperhour']);

  // Validierung   
  if (strlen($firma) < $minLength) {
    die("Fehler: Die Firma muss mindestens $minLength Zeichen lang sein.");
  }
  if (strlen($firma) > $maxLength) {
    die("Fehler: Die Firma darf maximal $maxLength Zeichen lang sein.");
  }
  if (!preg_match($alphanumericRegex, $firma)) {
    die("Fehler: Die Firma darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
  }
  if (!preg_match($alphanumericRegex, $zusatz)) {
    die("Fehler: Der Zusatz darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
  }
  if (strlen($street) < $minLength) {
    die("Fehler: Die Straße muss mindestens $minLength Zeichen lang sein.");
  }
  if (strlen($street) > $maxLength) {
    die("Fehler: Die Straße darf darf maximal $maxLength Zeichen lang sein.");
  }
  if (!preg_match($alphanumericRegex, $street)) {
    die("Fehler: Die Straße darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
  }
  if (strlen($location) < $minLength) {
    die("Fehler: Der Ort muss mindestens $minLength Zeichen lang sein.");
  }
  if (strlen($location) > $maxLength) {
    die("Fehler: Der Ort darf darf maximal $maxLength Zeichen lang sein.");
  }
  if (!preg_match($alphanumericRegex, $location)) {
    die("Fehler: Der Ort darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
  }  
  
  if (strlen($internet) > $maxLength) {
    die("Fehler: Die Internet-Adresse muss mindestens $minLength Zeichen lang sein.");
  }
  
  // Regulärer Ausdruck für die E-Mail-Validierung
  // $regex = '^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$';
  // if (!preg_match($regex, $mail)) {
  //   die("E-Mail-Adresse ist ungültig..");
  // }

//   $i = 0;
//   foreach ($_POST as $post) {    
//     echo "<p>" . htmlspecialchars($post) . "</p>";
//     $i++;
// }


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zusatz = $_POST['zusatz'];
    $firma = $_POST['firma'];
    $street = $_POST['street'];
    $location = $_POST['location'];
    $telefon = $_POST['telefon'];
    $mail = $_POST['mail'];
    $fax = $_POST['fax'];
    $internet = $_POST['internet'];
    $zip = $_POST['zip'];
    $zip = $_POST['zip'];
    $countryID = 1;

    $sql = "INSERT INTO customer (zusatz, firma, street, zip, telefon, fax, internet,location, mail, countryid,priceperhour) VALUES (:zusatz, :firma, :street, :zip, :telefon, :fax , :internet, :location, :mail, :countryid, :priceperhour)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['zusatz' => $zusatz, 'firma' => $firma, 'street' => $street, 'telefon' => $telefon, 'fax' => $fax, 'internet' => $internet, 'zip' => $zip, 'location' => $location, 'mail' => $mail, 'countryid' => $countryID, 'priceperhour' => $priceperhour]);

    echo "Kunde hinzugefügt!";
    sleep(3);
    header('Location: Customer.php'); // Zurück zur Übersicht
  
  }
  ?>
</body>