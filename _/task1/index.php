<?php

function shuffle_word($word) {
    if (mb_strlen($word, 'UTF-8') <= 3) {
        return $word;
    }

    $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
    $first = $chars[0];
    $last = $chars[count($chars) - 1];
    $middle = array_slice($chars, 1, -1);

    do {
        $shuffled = $middle;
        shuffle($shuffled);
    } while ($shuffled === $middle);

    return implode('', array_merge([$first], $shuffled, [$last]));
}

function process_line($line) {
    return preg_replace_callback('/\p{L}{2,}/u', function ($matches) {
        return shuffle_word($matches[0]);
    }, $line);
}

function contains_dangerous_code($content) {
    $dangerous_patterns = [
        '/<script.*?>/i',
        '/<\/script>/i',
        '/<\?php/i',
        '/eval\s*\(/i',
        '/exec\s*\(/i',
        '/shell_exec\s*\(/i',
        '/system\s*\(/i',
        '/passthru\s*\(/i',
        '/base64_decode\s*\(/i'
    ];

    foreach ($dangerous_patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            return true;
        }
    }

    return false;
}

// Ścieżki plików
$inputFile = 'input.txt';
$outputFile = 'output.txt';

if (!file_exists($inputFile)) {
    die("Plik '$inputFile' nie istnieje.\n");
}

$inputContent = file_get_contents($inputFile);

// 🔍 Sprawdzenie zawartości na potencjalnie niebezpieczny kod
if (contains_dangerous_code($inputContent)) {
    die("Zawartość pliku zawiera potencjalnie niebezpieczny kod. Operacja przerwana.\n");
}

// Odczytaj plik jako linie
$lines = explode(PHP_EOL, $inputContent);

// Przetwarzanie i zapis
$outputHandle = fopen($outputFile, 'w');

foreach ($lines as $line) {
    $processedLine = process_line($line);
    fwrite($outputHandle, $processedLine . PHP_EOL);
}

fclose($outputHandle);

echo "Plik został przetworzony i zapisany jako '$outputFile'.\n";

?>