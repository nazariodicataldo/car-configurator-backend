# Car Configurator Backend

## Configurazione del progetto

Clona il repository e crea il file `.env` a partire da `.env.example`:

```bash
cp .env.example .env
```

Configura le variabili d'ambiente del database e genera la chiave dell'applicazione:

```bash
php artisan key:generate
```

Installa le dipendenze:

```bash
composer install
```

Esegui le migrazioni:

```bash
php artisan migrate
```

Avvia il server di sviluppo:

```bash
php artisan serve
```

---

## 1. Architettura del progetto

Il backend è sviluppato in **PHP 8.3+** con **Laravel 13**, seguendo un'architettura **MVC orientata alle API REST** in formato JSON.

L'applicazione gestisce un sistema di configurazione automobilistica che consente agli utenti di esplorare il catalogo dei veicoli, personalizzare le configurazioni selezionando motorizzazioni, allestimenti, colori e optional e salvare le proprie configurazioni. Gli utenti autenticati possono inoltre gestire il proprio account e le configurazioni create, mentre le operazioni di amministrazione del catalogo sono protette tramite autorizzazione.

## Componenti principali

- **Laravel Sanctum** — autenticazione tramite token API
- **Eloquent ORM** — gestione di modelli, relazioni e tabelle pivot
- **Form Request** — validazione centralizzata delle richieste
- **Mail Verification** — verifica dell'indirizzo email
- **Password Reset** — recupero e reimpostazione della password
- **API Resource** — serializzazione delle risposte JSON

---

## 2. Struttura del database

Il sistema utilizza **PostgreSQL** come database relazionale e organizza i dati attorno al catalogo dei veicoli e alle configurazioni personalizzate degli utenti.

### Tabelle principali

| Tabella          | Descrizione                                                                  |
| ---------------- | ---------------------------------------------------------------------------- |
| `users`          | Utenti dell'applicazione con informazioni di autenticazione e verifica email |
| `brands`         | Marche automobilistiche                                                      |
| `vehicles`       | Veicoli disponibili nel catalogo                                             |
| `engines`        | Motorizzazioni disponibili                                                   |
| `setups`         | Allestimenti e configurazioni base                                           |
| `colors`         | Colori disponibili per i veicoli                                             |
| `optionals`      | Optional e accessori selezionabili                                           |
| `configurations` | Configurazioni salvate dagli utenti                                          |

### Tabelle di relazione

Il progetto utilizza diverse tabelle pivot per gestire le relazioni molti-a-molti tra:

- Veicoli e motorizzazioni
- Veicoli e allestimenti
- Veicoli e colori
- Allestimenti e optional
- Configurazioni e optional

Sono inoltre presenti regole di compatibilità tra optional per gestire dipendenze ed eventuali esclusioni tra accessori.

---

## 3. Struttura del progetto

```text
car-configurator-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controller API
│   │   ├── Middleware/       # Middleware applicativi
│   │   ├── Requests/         # Validazione delle richieste
│   │   └── Resources/        # Trasformazione delle risposte JSON
│   ├── Models/              # Modelli Eloquent e relazioni
│   ├── Traits/                # Utilities riutilizzabili
│   ├── Policies/            # Regole di autorizzazioni
│   ├── Servicies/           # Logica delle richieste
│   └── Providers/           # Service provider dell'applicazione
├── bootstrap/              # Bootstrap dell'applicazione
├── config/                 # Configurazioni di Laravel e servizi
├── database/
│   ├── factories/          # Factory per testing e seeding
│   ├── migrations/         # Schema del database
│   └── seeders/            # Dati iniziali dell'applicazione
├── routes/
│   ├── api.php             # Definizione delle rotte API
│   └── console.php
├── storage/                # File e risorse generate dall'applicazione
├── tests/
│   ├── Feature/            # Test di integrazione
│   └── Unit/               # Test unitari
└── public/
    └── index.php           # Entry point dell'applicazione
```

---

## Funzionalità principali

- Autenticazione e registrazione utenti
- Verifica email e recupero password
- Gestione del catalogo veicoli
- Gestione di motori, allestimenti, colori e optional
- Configurazione personalizzata dei veicoli
- Salvataggio delle configurazioni utente
- Relazioni e regole di compatibilità tra optional
- API REST JSON per integrazione con frontend Next.js
