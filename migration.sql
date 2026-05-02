CREATE DATABASE IF NOT EXISTS Orizon;
USE Orizon;

-- Tabella Paesi
CREATE TABLE paesi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Tabella Viaggi
CREATE TABLE viaggi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posti_disponibili INT NOT NULL,
    creato_il TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella Pivot (Relazione molti-a-molti)
CREATE TABLE IF NOT EXISTS viaggi_paesi (
    viaggio_id INT,
    paese_id INT,
    FOREIGN KEY (viaggio_id) REFERENCES viaggi(id) ON DELETE CASCADE,
    FOREIGN KEY (paese_id) REFERENCES paesi(id) ON DELETE CASCADE,
    PRIMARY KEY (viaggio_id, paese_id)
);