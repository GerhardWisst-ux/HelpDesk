<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HelpDesk Login</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* === Grundlayout === */
    html,
    body {
      height: 100%;
      margin: 0;
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    /* Wrapper für Flex */
    .wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* === Navbar & Header === */
    .custom-header {
      background: linear-gradient(90deg, #1e3c72, #2a5298);
      color: #fff;
      height: 40px;
      border-bottom: 2px solid #1b3a6d;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      border-radius: 1px 1px 1px 1px;
    }

    .custom-header h2 {
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* === Buttons === */
    .btn-custom {
      background-color: #1e3c72 !important;
      border-radius: 30px;
      font-size: 0.85rem;
      padding: 0.45rem 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary-custom {
      color: linear-gradient(90deg, #1e3c72, #2a5298) !important;
      background-color: #1e3c72 !important;
      border-color: #1e3c72 !important;
    }

    .btn-primary-custom:hover {
      background-color: #1e3c72 !important;
    }


    /* === Karten & Tabellen === */
    .custom-container {
      background-color: #fff;
      border-radius: 12px;
      /* padding: 20px; */
      margin-top: 0px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }


    /* === Navbar Design === */
    .navbar-custom {
      background: linear-gradient(to right, #cce5f6, #e6f2fb);
      border-bottom: 1px solid #b3d7f2;
    }

    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: #0c2c4a;
      font-weight: 500;
    }

    .navbar-custom .nav-link:hover,
    .navbar-custom .nav-link:focus {
      color: #04588c;
      text-decoration: underline;
    }

    /* === Modal === */
    .modal-content {
      border-radius: 12px;
    }

    .modal-header {
      background-color: #0946c9ff;
      color: #fff;
      border-radius: 12px 12px 0 0;
    }
  </style>
</head>

<body>
  <?php
  require 'db.php';
  session_start(); // Session starten
  
  $_SESSION['userid'] = "";

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $passwort = trim($_POST['passwort']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errorMessage = "Ungültige E-Mail-Adresse.";
    } else {
      try {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user !== false && password_verify($passwort, $user['passwort'])) {
          $_SESSION['userid'] = $user['id'];
          $_SESSION['email'] = $user['email'];
          // Redirect nur, wenn Bedingung erreicht
          header("Location: TicketUebersicht.php");
          exit();
        } else {
          $errorMessage = "E-Mail oder Passwort war ungültig.";
        }
      } catch (PDOException $e) {
        $errorMessage = "Datenbankfehler: " . $e->getMessage();
      }
    }
  }
  ?>

  <!-- Inhalt mit Login-Form -->

  <body>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center">
              <h4 class="mb-0">Helpdesk Login</h4>
            </div>
            <div class="card-body">
              <form method="post" action="" class="needs-validation" novalidate>

                <div class="mb-3">
                  <label for="email" class="form-label">Benutzer:</label>
                  <input type="text" name="email" id="email" required class="form-control"
                    placeholder="Benutzer eingeben">
                </div>

                <div class="mb-3">
                  <label for="passwort" class="form-label">Passwort:</label>
                  <input type="password" name="passwort" id="passwort" required class="form-control"
                    placeholder="Passwort eingeben">
                </div>

                <?php if (!empty($errorMessage)): ?>
                  <div class="alert alert-danger">
                    <?= htmlspecialchars($errorMessage) ?>
                  </div>
                <?php endif; ?>

                <div class="d-grid">
                  <button type="submit" class="btn-custom text-white rounded-pill">Anmelden</button>
                </div>

                <div class="text-center mt-3">
                  <a href="register.php" class="text-decoration-none">Noch kein Konto? Jetzt
                    registrieren</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Optional: Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
  </body>

</html>
<?php ob_end_flush(); ?>