<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Walidacja numeru PESEL</title>
</head>
<body>
    <h2>Sprawdź numer PESEL</h2>
    <form method="post" action="">
        <label for="pesel">Numer PESEL:</label><br>
        <input type="text" id="pesel" name="pesel" maxlength="11" required><br><br>
        <input type="submit" value="Sprawdź">
    </form>
</body>
</html>

<?php
 function isValidPesel(string $pesel): bool {
        $pesel = trim($pesel);

        if (!preg_match('/^\d{11}$/', $pesel)) {
            return false;
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$pesel[$i] * $weights[$i];
        }

        $controlDigit = (10 - ($sum % 10)) % 10;

        return $controlDigit === (int)$pesel[10];
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $pesel = isset($_POST['pesel']) ? trim($_POST['pesel']) : '';

        if (!preg_match('/^\d+$/', $pesel)) {
            echo "<p style='color:red;'>Błąd: PESEL może zawierać tylko cyfry.</p>";
        } elseif (strlen($pesel) !== 11) {
            echo "<p style='color:red;'>Błąd: Numer PESEL musi mieć dokładnie 11 cyfr.</p>";
        } elseif (isValidPesel($pesel)) {
            echo "<p style='color:green;'>PESEL jest poprawny </p>";
        } else {
            echo "<p style='color:red;'>PESEL jest niepoprawny </p>";
        }
    }

// --------------------------
// TESTY JEDNOSTKOWE
// --------------------------
function runPeselTests(): void {
    $tests = [
        // Poprawne
        '44051401359' => true,   // poprawny PESEL
        '02070803628' => true,   // poprawny PESEL

        // Błędna cyfra kontrolna
        '44051401358' => false,
        '02070803621' => false,

        // Za krótki
        '1234567890' => false,

        // Za długi
        '123456789012' => false,

        // Zawiera litery
        '44051A01359' => false,

        // Zawiera znaki specjalne
        '44051401#59' => false,
    ];

    foreach ($tests as $pesel => $expected) {
        $result = isValidPesel($pesel);
        echo "PESEL: $pesel - ";
        echo ($result && $result === $expected) ? "OK\n" : "BŁĄD\n";
	echo "<br>";
    }
}

// Uruchom testy
echo "<p>Testy: </p>";
runPeselTests();

?>
