<?php

// Throwable (interface)
// ├─ Error → fatal (like dividing by text, missing function)
// └─ Exception → expected problems in your code (like bad input, invalid file).

// try / catch / finally

// Use try for risky code, catch to react, and finally to always clean up.

declare(strict_types=1);

function riskyDivide(int $a, int $b): float {
  if ($b === 0) {
    throw new InvalidArgumentException('Division par zéro interdite.');
  }
  return $a / $b;
}

try {
  echo riskyDivide(10, 0);
} catch (InvalidArgumentException $e) {
  echo "[WARN] " . $e->getMessage() . PHP_EOL;
} finally {
  echo "Toujours exécuté (libération de ressources, etc.)." . PHP_EOL;
}

// try {
//   $result = 10 / 0; // risky!
// } catch (DivisionByZeroError $e) {
//   echo " Can't divide by zero!\n";
// } finally {
//   echo "✔ Done (cleanup).\n";
// }




// Multi-catch & order ofcatch
try {
    // ...
  } catch (JsonException|InvalidArgumentException $e) {
    // traitement commun (ex. message + journalisation)
  } catch (Exception $e) {
    // filet de sécurité pour autres exceptions
  }


//   Specific first, general second

//   try {
//     $data = json_decode("invalid", true, 512, JSON_THROW_ON_ERROR);
//   } catch (JsonException $e) {
//     echo " Bad JSON!\n";
//   } catch (Exception $e) {
//     echo " Some other problem.\n";
//   }







// Rethrow (adding context)
function loadConfig(string $path): array {
    try {
      $json = file_get_contents($path);
      if ($json === false) {
        throw new RuntimeException("Lecture impossible: $path");
      }
      return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
      throw new RuntimeException("JSON invalide dans $path", previous: $e);
    }
  }

// Add context when rethrowing
// Wrap exceptions with more useful messages.

// function loadConfig(string $path): array {
//     try {
//       return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
//     } catch (JsonException $e) {
//       throw new RuntimeException("Config file is broken: $path", 0, $e);
//     }
//   }
