CREATE TABLE llxtm_signdevis_signature(
    rowid INT AUTO_INCREMENT PRIMARY KEY,
    fk_object INT NOT NULL,
    type_object VARCHAR(128) NOT NULL,
    sign_link TEXT NOT NULL,
    sign_status VARCHAR(50) DEFAULT 'pending',
    date_creation DATETIME NOT NULL,
    date_signature DATETIME,
    tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;