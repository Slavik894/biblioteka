<?php
session_start(); 

if(isset($_SESSION['user_id'])){
  header("Location: main.php");
}

$host = 'localhost';
$dbname = 'biblioteka';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, haslo, imie, nazwisko FROM uzytkownicy WHERE adres_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['haslo'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $user['imie'];
                $_SESSION['surname'] = $user['nazwisko'];

                header('Location: main.php');
            } else {
                $error = "Nieprawidłowe hasło.";
            }
        } else {
            $error = "Nie ma takiego maila.";
        }

        $stmt->close();
    } else {
        $error = "Wszystkie pola są wymagane.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logowanie</title>
    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/4798a03daf.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/index/style.css" />
  </head>
  <body>
    <div class="form-wrapper">
      <div class="form">
        <h2 class="form-title">Logowanie</h2>
        <form method="POST">
          <div class="form-row">
            <label for="email">Email</label>
            <div class="input">
              <div class="icon-wrapper">
                <i class="fa-solid fa-user"></i>
              </div>
              <input
                type="email"
                name="email"
                id="email"
                placeholder="Podaj Email"
              />
            </div>
          </div>
          <div class="form-row">
            <label for="password">Hasło</label>
            <div class="input">
              <div class="icon-wrapper">
                <i class="fa-solid fa-lock"></i>
              </div>
                <input
                  type="password"
                  name="password"
                  id="password"
                  placeholder="Podaj Hasło"
                  class="passInput"
                />
                <i class="fa-solid fa-eye faeye showpass"></i>
                <i class="fa-solid fa-eye-slash faeye hidepass"></i>
            </div>
          </div>
          <div class="form-row remember_me">
            <label for="remember_me">
              <input type="checkbox" name="remember_me" id="remember_me" class="remember_me"/> Zapamiętaj mnie
            </label>
          </div>
          <div class="form-row">
            <button type="submit">Zaloguj</button>
          </div>
        </form>
        <?php
          if(isset($error)){
            echo ' 
              <p style="color:red; font-size:10px;">'.$error.'</p>
            ';
          }
        ?>
        <p class="link_to">
          Nie masz jeszcze konta?
          <a href="register.php" class="link">Zarejestruj się!</a>
        </p>
      </div>
      <div class="form-image">
        <img src="assets/images/a-book-2306181_1920.webp" alt="book" />
      </div>
    </div>
    <script src="assets/js/script.js"></script>
  </body>
</html>