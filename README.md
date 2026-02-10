# ⚙️ BrickPHP Core

[![Latest Stable Version](https://img.shields.io/packagist/v/brickphp/core.svg?style=flat-square)](https://packagist.org/packages/brickphp/core)
[![License](https://img.shields.io/packagist/l/brickphp/core.svg?style=flat-square)](https://packagist.org/packages/brickphp/core)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-777bb4.svg?style=flat-square)](https://php.net)

Questo repository contiene il **motore logico** del framework BrickPHP. È progettato per essere leggero, modulare e privo di dipendenze pesanti, fornendo le fondamenta necessarie per gestire il ciclo di vita di una richiesta HTTP in un pattern MVC.

> **Nota:** Questo pacchetto è un componente interno. Per iniziare a sviluppare un'applicazione, utilizza lo [Skeleton Ufficiale di BrickPHP](https://github.com/donatodelpeschio/BrickPHP).

---

## 🏗️ Architettura del Core

Il Core gestisce il flusso della richiesta attraverso i seguenti componenti fondamentali:

1. **HTTP Layer:** Gestione di `Request` (cattura globali, sessioni, input) e `Response` (header, status code, body).
2. **Routing:** Un sistema di routing basato su Regex con supporto per parametri dinamici `{id}`.
3. **Dispatcher:** Risolve ed esegue i Controller iniettando le dipendenze necessarie.
4. **Database Wrapper:** Un'interfaccia fluida sopra PDO per query veloci e sicure.
5. **View Engine:** Sistema di rendering leggero con supporto per l'estrazione di dati e buffer di output.



---

## 🛠️ Componenti Tecnici

### Sistema di Routing
Il router supporta definizioni pulite e mappatura diretta ai controller:
```php
$router->get('/profile/{id}', [UserController::class, 'show']);
```

### Database & Model
BrickPHP include un Database Manager globale accessibile tramite l'helper `db()`:
```php
$users = db()->table('users')->where('active', 1)->get();
```

### Global Helpers
Per semplificare lo sviluppo, il Core espone funzioni globali caricate via Composer:
- `view($name, $data)` Renderizza una vista.
- `db()` Istanza del query builder.
- `env($key, $default)` Accesso sicuro alle variabili d'ambiente.
- `cache()` Gestione della cache (File/Redis).
___

## 📦 Installazione (Standalone)
Se desideri utilizzare solo il motore di BrickPHP in un progetto custom:
```bash
composer require brickphp/core
```

Nel tuo entry point, definisci la costante `BRICK_PATH` per orientare il framework:
```php
define('BRICK_PATH', __DIR__);
require 'vendor/autoload.php';
```
___

## 🤝 Contribuire
Siamo aperti a contributi! Se hai idee per migliorare le performance o aggiungere funzionalità al motore:

1. Fai il Fork del progetto.

2. Crea un branch per la tua feature (`git checkout -b feature/AmazingFeature`).

3. Fai il Commit delle tue modifiche.

4. Pusha sul branch.

5. Apri una Pull Request.

**Sostieni lo sviluppo**

Se questo motore ti aiuta a costruire software più velocemente, considera una piccola donazione per supportarne il mantenimento:

[☕ Supporta BrickPHP su PayPal](https://paypal.me/mailboxporter)

## 📄 Licenza
Questo progetto è rilasciato sotto licenza MIT. Consulta il file [LICENSE](LICENSE) per maggiori dettagli.

<p align="center">Core Engine ideato da <strong>Donato Del Peschio</strong></p>