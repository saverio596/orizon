# Orizon API - Gestione Viaggi

Benvenuti nel progetto **Orizon**, un sistema di API REST sviluppato in PHP per la gestione di viaggi e destinazioni internazionali. Il sistema permette di creare viaggi, associarli a più paesi e gestire le disponibilità dei posti.

## 🚀 Funzionalità
- **CRUD Paesi**: Creazione e visualizzazione delle destinazioni.
- **CRUD Viaggi**: Gestione completa dei viaggi (Crea, Leggi, Aggiorna, Elimina).
- **Filtri Avanzati**: Ricerca viaggi per numero di posti e per paese specifico.
- **Integrità del Database**: Utilizzo di transazioni SQL e vincoli Foreign Key (ON DELETE CASCADE).

## 🛠️ Requisiti Tecnici
- **Server Locale**: MAMP / XAMPP
- **Linguaggio**: PHP 8.3+
- **Database**: MySQL / MariaDB
- **Estensioni**: PDO per la connessione al database.

## 📂 Struttura del Database
Il progetto si basa su una relazione **Molti-a-Molti** tra Viaggi e Paesi.

### Tabelle:
1. **paesi**: Contiene l'elenco delle nazioni (`id`, `nome`).
2. **viaggi**: Contiene i dati generali del viaggio (`id`, `posti_disponibili`).
3. **viaggi_paesi**: Tabella ponte per collegare viaggi e paesi (`viaggio_id`, `paese_id`).

---

## 📡 Documentazione API

### 1. Paesi
- **Crea Paese**: `POST /api/paese/create.php`
- **Leggi Paesi**: `GET /api/paese/read.php`

### 2. Viaggi
| Metodo | Endpoint | Descrizione |
| :--- | :--- | :--- |
| **POST** | `/api/viaggio/create.php` | Crea un viaggio e associa i paesi. |
| **GET** | `/api/viaggio/read.php` | Legge i viaggi (supporta filtri `?posti=X` e `?paese_id=Y`). |
| **PUT** | `/api/viaggio/update.php` | Modifica posti e paesi di un viaggio esistente. |
| **DELETE** | `/api/viaggio/delete.php` | Elimina un viaggio e i suoi legami. |

### Esempio di Inserimento Viaggio (JSON):
```json
{
    "posti_disponibili": 25,
    "paesi_ids": [1, 2]
}