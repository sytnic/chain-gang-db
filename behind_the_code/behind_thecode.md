## 05 Create DB

```sql

CREATE DATABASE chain_gang;

SHOW DATABASES;

GRANT ALL PRIVILEGES ON chain_gang.* TO 'webuser'@'localhost' IDENTIFIED BY 'secretpassword';

USE chain_gang;
```

## 06 Create Table

```sql

CREATE TABLE bicycles (
    id INT(11) AUTO_INCREMENT PRIMARY KEY, 
    brand VARCHAR(255) NOT NULL, 
    model VARCHAR(255) NOT NULL, 
    year INT(4) NOT NULL,
    category VARCHAR(255) NOT NULL, 
    gender VARCHAR(255) NOT NULL, 
    color VARCHAR(255) NOT NULL, 
    price DECIMAL(9,2) NOT NULL, 
    weight_kg DECIMAL(9,5) NOT NULL, 
    condition_id TINYINT(3) NOT NULL, 
    description TEXT NOT NULL
);

SHOW TABLES;

SHOW FIELDS FROM bicycles;

INSERT INTO bicycles (brand, model, year, category, gender, color, price, weight_kg, condition_id, description) VALUES ('Trek','Emonda','2017','Hybrid','Unisex','black','1495.00','1.5','5','');

INSERT INTO bicycles (brand, model, year, category, gender, color, price, weight_kg, condition_id, description) VALUES ('Cannondale','Synapse','2016','Road','Unisex','matte black','1999.00','1.0','5','');

SELECT * FROM bicycles;

exit

```

##
