# Orizon API - Gestione Viaggi

Benvenuti nel progetto **Orizon**, un sistema di API REST sviluppato in PHP per la gestione di viaggi e destinazioni internazionali. Il sistema permette di creare viaggi, associarli a più paesi e gestire le disponibilità dei posti.

## 🚀 Funzionalità
- **CRUD Paesi**: Creazione, visualizzazione, aggiornamento ed eliminazione delle destinazioni.
- **CRUD Viaggi**: Gestione completa dei viaggi (Crea, Leggi, Aggiorna, Elimina).
- **Filtri Avanzati**: Ricerca viaggi per numero di posti e per paese specifico.
- **Integrità del Database**: Utilizzo di transazioni SQL e vincoli Foreign Key (ON DELETE CASCADE).
- **Sicurezza**: Prepared statement nativi PDO, gestione CORS completa.

## 🛠️ Requisiti Tecnici
- **Server Locale**: MAMP (PHP 8.3+, MySQL)
- **Linguaggio**: PHP 8.3+
- **Database**: MySQL / MariaDB
- **Estensioni**: PDO, mod_rewrite Apache
- **Dipendenze**: Composer, vlucas/phpdotenv

## ⚙️ Installazione

### 1. Clona il progetto in MAMP
```bash
cd /Applications/MAMP/htdocs
git clone <repo-url> Orizon
```

### 2. Installa le dipendenze
```bash
cd Orizon
composer install
```

### 3. Configura le variabili d'ambiente
Crea un file `.env` nella root del progetto:
```env
DB_HOST=localhost
DB_NAME=Orizon
DB_USER=root
DB_PASS=root
DB_PORT=8889
```

### 4. Crea il database
Importa il file `migration.sql` da phpMyAdmin (`http://localhost:8888/phpMyAdmin`) oppure da terminale:
```bash
/Applications/MAMP/bin/mysql/bin/mysql -u root -p -P 8889 < migration.sql
```

### 5. Abilita mod_rewrite in MAMP
```bash
sed -i '' 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /Applications/MAMP/conf/apache/httpd.conf
```
Poi riavvia MAMP.

---

## 📂 Struttura del Progetto
```
Orizon/
├── App/
│   ├── controllers/
│   │   ├── PaeseController.php
│   │   └── ViaggioController.php
│   └── models/
│       ├── paese.php
│       └── viaggio.php
├── config/
│   └── database.php
├── core/
│   └── Router.php
├── vendor/
├── .env
├── .env.example
├── .gitignore
├── .htaccess
├── composer.json
├── index.php
└── migration.sql
```

## 📂 Struttura del Database
Il progetto si basa su una relazione **Molti-a-Molti** tra Viaggi e Paesi.

| Tabella | Campi | Descrizione |
| :--- | :--- | :--- |
| `paesi` | `id`, `nome` | Elenco delle nazioni |
| `viaggi` | `id`, `posti_disponibili`, `creato_il` | Dati generali del viaggio |
| `viaggi_paesi` | `viaggio_id`, `paese_id` | Tabella ponte molti-a-molti |

---

## 📡 Documentazione API

**Base URL**: `http://localhost:8888/Orizon`

### Paesi

| Metodo | Endpoint | Descrizione |
| :--- | :--- | :--- |
| `GET` | `/paese` | Restituisce tutti i paesi |
| `POST` | `/paese` | Crea un nuovo paese |
| `PUT` | `/paese/{id}` | Aggiorna un paese esistente |
| `DELETE` | `/paese/{id}` | Elimina un paese |

### Viaggi

| Metodo | Endpoint | Descrizione |
| :--- | :--- | :--- |
| `GET` | `/viaggio` | Restituisce tutti i viaggi (supporta filtri) |
| `GET` | `/viaggio/{id}` | Restituisce un viaggio specifico con i suoi paesi |
| `POST` | `/viaggio` | Crea un viaggio e associa i paesi |
| `PUT` | `/viaggio/{id}` | Modifica posti e paesi di un viaggio esistente |
| `DELETE` | `/viaggio/{id}` | Elimina un viaggio e i suoi legami |

### Filtri disponibili su `GET /viaggio`
| Parametro | Tipo | Descrizione |
| :--- | :--- | :--- |
| `paese_id` | integer | Filtra i viaggi che includono quel paese |
| `posti` | integer | Filtra i viaggi con almeno N posti disponibili |

Esempio: `GET /viaggio?paese_id=1&posti=5`

---

## 📋 Esempi di Richieste

### Crea un paese
```json
POST /paese
Content-Type: application/json

{ "nome": "Italia" }
```

### Crea un viaggio
```json
POST /viaggio
Content-Type: application/json

{
    "posti_disponibili": 25,
    "paesi_ids": [1, 2]
}
```

### Aggiorna un viaggio
```json
PUT /viaggio/1
Content-Type: application/json

{
    "posti_disponibili": 10,
    "paesi_ids": [1]
}
```
