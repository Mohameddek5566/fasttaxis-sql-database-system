


CREATE DATABASE FastTaxis_Assignment;
USE FastTaxis_Assignment;
CREATE TABLE Office (
  officeID INT PRIMARY KEY AUTO_INCREMENT,
  officeName VARCHAR(100) NOT NULL UNIQUE,
  location VARCHAR(100) NOT NULL,
  address VARCHAR(200) NOT NULL,
  phoneNumber VARCHAR(20) NOT NULL
);

CREATE TABLE Staff (
  staffID INT PRIMARY KEY AUTO_INCREMENT,
  officeID INT NOT NULL,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  DOB DATE NOT NULL,
  address VARCHAR(200) NOT NULL,
  gender VARCHAR(10) NOT NULL,
  phoneNumber VARCHAR(20) NOT NULL,
  icPassportNumber VARCHAR(30) NOT NULL UNIQUE,
  CONSTRAINT fk_staff_office
    FOREIGN KEY (officeID) REFERENCES Office(officeID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

CREATE TABLE Manager (
  staffID INT PRIMARY KEY,
  CONSTRAINT fk_manager_staff
    FOREIGN KEY (staffID) REFERENCES Staff(staffID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE AdministrativeStaff (
  staffID INT PRIMARY KEY,
  CONSTRAINT fk_admin_staff
    FOREIGN KEY (staffID) REFERENCES Staff(staffID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE Driver (
  staffID INT PRIMARY KEY,
  currentLocation VARCHAR(100),
  status VARCHAR(20) DEFAULT 'Available',
  licenseNumber VARCHAR(50) NOT NULL UNIQUE,
  CONSTRAINT fk_driver_staff
    FOREIGN KEY (staffID) REFERENCES Staff(staffID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE Owner (
  ownerID INT PRIMARY KEY AUTO_INCREMENT,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  phoneNumber VARCHAR(20) NOT NULL,
  address VARCHAR(200) NOT NULL,
  bankDetails VARCHAR(120) NOT NULL
);

CREATE TABLE Taxi (
  taxiID INT PRIMARY KEY AUTO_INCREMENT,
  ownerID INT NOT NULL,
  officeID INT NOT NULL,
  taxiModel VARCHAR(100) NOT NULL,
  licensePlate VARCHAR(20) NOT NULL UNIQUE,
  nextServiceDate DATE NOT NULL,
  CONSTRAINT fk_taxi_owner
    FOREIGN KEY (ownerID) REFERENCES Owner(ownerID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_taxi_office
    FOREIGN KEY (officeID) REFERENCES Office(officeID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

CREATE TABLE DriverTaxiAllocation (
  driverID INT NOT NULL,
  taxiID INT NOT NULL,
  allocatedFrom DATE NOT NULL,
  allocatedTo DATE NULL,
  PRIMARY KEY (driverID, taxiID, allocatedFrom),
  CONSTRAINT fk_alloc_driver
    FOREIGN KEY (driverID) REFERENCES Driver(staffID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_alloc_taxi
    FOREIGN KEY (taxiID) REFERENCES Taxi(taxiID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE Client (
  clientID INT PRIMARY KEY AUTO_INCREMENT,
  phoneNumber VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(100),
  address VARCHAR(200) NOT NULL
);

CREATE TABLE PrivateClient (
  clientID INT PRIMARY KEY,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  dateFirstBooking DATE NOT NULL,
  notes TEXT,
  CONSTRAINT fk_private_client
    FOREIGN KEY (clientID) REFERENCES Client(clientID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE BusinessClient (
  clientID INT PRIMARY KEY,
  companyName VARCHAR(120) NOT NULL,
  businessRegNumber VARCHAR(50) NOT NULL UNIQUE,
  contactPerson VARCHAR(120) NOT NULL,
  faxNumber VARCHAR(20),
  city VARCHAR(100) NOT NULL,
  CONSTRAINT fk_business_client
    FOREIGN KEY (clientID) REFERENCES Client(clientID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE Contract (
  contractNumber INT PRIMARY KEY AUTO_INCREMENT,
  clientID INT NOT NULL,
  fixedFee DECIMAL(10,2) NOT NULL,
  jobLimit INT NOT NULL,
  startDate DATE NOT NULL,
  endDate DATE NOT NULL,
  CONSTRAINT fk_contract_business
    FOREIGN KEY (clientID) REFERENCES BusinessClient(clientID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

CREATE TABLE Job (
  jobID INT PRIMARY KEY AUTO_INCREMENT,
  clientID INT NOT NULL,
  driverID INT NOT NULL,
  taxiID INT NOT NULL,
  contractNumber INT NULL,
  pickupAddress VARCHAR(200) NOT NULL,
  dropoffAddress VARCHAR(200) NOT NULL,
  pickupDateTime DATETIME NOT NULL,
  dropoffDateTime DATETIME NULL,
  mileageUsed DECIMAL(8,2) NULL,
  charge DECIMAL(10,2) NULL,
  status VARCHAR(20) NOT NULL,
  failureReason VARCHAR(200) NULL,
  CONSTRAINT fk_job_client
    FOREIGN KEY (clientID) REFERENCES Client(clientID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_job_driver
    FOREIGN KEY (driverID) REFERENCES Driver(staffID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_job_taxi
    FOREIGN KEY (taxiID) REFERENCES Taxi(taxiID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_job_contract
    FOREIGN KEY (contractNumber) REFERENCES Contract(contractNumber)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE Maintenance (
  maintenanceID INT PRIMARY KEY AUTO_INCREMENT,
  taxiID INT NOT NULL,
  serviceDate DATE NOT NULL,
  nextServiceDate DATE NOT NULL,
  description VARCHAR(200),
  CONSTRAINT fk_maint_taxi
    FOREIGN KEY (taxiID) REFERENCES Taxi(taxiID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
CREATE TABLE users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    role ENUM('DIRECTOR','STAFF','DRIVER','OWNER') NOT NULL,
    staffID INT NULL,
    ownerID INT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_users_staff
        FOREIGN KEY (staffID) REFERENCES Staff(staffID)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_users_owner
        FOREIGN KEY (ownerID) REFERENCES Owner(ownerID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO users (username, passwordHash, role, staffID, ownerID, isActive)
VALUES ('owner3', '$2y$10$lMgoWzxZQoKnWtGqUQCuC.BgReaRIwO6a9PS/IF/6BxgsPSP.XVBa', 'OWNER', NULL, 3, 1);

ALTER TABLE users
ADD COLUMN isActive TINYINT(1) NOT NULL DEFAULT 1;


INSERT INTO users (username, passwordHash, role, staffID, ownerID)
VALUES 
('director', '$2y$10$8J8Wv4JX6Nfdu3hWz4/m3ewovWKZHlgcEeDtzOcv8q55XLWzO7TVG', 'DIRECTOR', NULL, NULL);

SELECT userID, username, role, staffID FROM users WHERE username='staff1';

SELECT userID, username, role, passwordHash, isActive
FROM users
WHERE username = 'director';

INSERT INTO users (username, passwordHash, role, staffID, ownerID)
VALUES ('staff1', '$2y$10$flFL5cYChf8Kz1Wq4CkFAeRX7zoIK4t8c/DSDKlv72Xl5ZlTE526G', 'STAFF', 5, NULL);

UPDATE users
SET passwordHash = '$2y$10$qOislciYMdVXYVRvBnLbL.1AASQU7d91Faby4vnU6ecqGuy.yroR2'
WHERE username = 'director';

INSERT INTO users (username, passwordHash, role, staffID, ownerID, isActive)
VALUES ('driver7', '$2y$10$OJEC67F.xzJT2wgAEgnsPe/NNQW2mSsBpgZZRzq2KM8qIyufzPUM2', 'DRIVER', 7, NULL, 1);



INSERT INTO users (username, passwordHash, role, staffID, ownerID, isActive)
VALUES ('staff2', '$2y$10$Z1SPp7JSsboY9Qdd1m1rMugrcEfPuGUG5pCfy1iPTjbEMoiJlChUG', 'STAFF', 5, NULL, 1);

// inserrting  TEST data  
INSERT INTO Office (officeName, location, address, phoneNumber) VALUES
('Cyberjaya HQ','Cyberjaya','No 10, Jalan Cyber 8, Cyberjaya','03-8000 1000'),
('KL Sentral','Kuala Lumpur','Lot 2, KL Sentral, Kuala Lumpur','03-8000 2000'),
('KLCC','Kuala Lumpur','No 1, KLCC Road, Kuala Lumpur','03-8000 3000'),
('Shah Alam','Selangor','Section 7, Shah Alam, Selangor','03-8000 4000');

// Staff (10 rows)
INSERT INTO Staff (officeID, firstName, lastName, DOB, address, gender, phoneNumber, icPassportNumber) VALUES
(1,'Nani','Johar','1980-03-10','Cyberjaya, Selangor','Female','012-1111111','MY800310-10-1111'),
(2,'Farid','Rahman','1978-09-12','KL Sentral, Kuala Lumpur','Male','012-2222222','MY780912-10-2222'),
(3,'Aina','Salleh','1985-01-20','KLCC, Kuala Lumpur','Female','012-3333333','MY850120-10-3333'),
(4,'Haziq','Omar','1982-06-05','Shah Alam, Selangor','Male','012-4444444','MY820605-10-4444'),
(1,'Siti','Nora','1996-08-15','Cyberjaya, Selangor','Female','013-5555555','MY960815-10-5555'),
(1,'Irfan','Zaki','1994-11-02','Cyberjaya, Selangor','Male','013-6666666','MY941102-10-6666'),
(2,'Jane','Chen','1998-04-12','KL Sentral, Kuala Lumpur','Female','014-7777777','P1234567'),
(3,'Vijay','Shan','1997-07-18','KLCC, Kuala Lumpur','Male','014-8888888','P2345678'),
(4,'Karim','Ali','1993-02-23','Shah Alam, Selangor','Male','014-9999999','P3456789'),
(4,'Sabri','Sam','1999-12-01','Shah Alam, Selangor','Male','015-1010101','P4567890');

// Manager (4 rows = one per office)
INSERT INTO Manager (staffID) VALUES (1),(2),(3),(4);

//AdministrativeStaff (5 rows)
INSERT INTO AdministrativeStaff (staffID) VALUES (5),(6),(2),(3),(4);


//Driver (6 rows)
INSERT INTO Driver (staffID, currentLocation, status, licenseNumber) VALUES
(7,'KL Sentral','Available','D-KL-1001'),
(8,'KLCC','Available','D-KL-1002'),
(9,'Shah Alam','Busy','D-SLG-2001'),
(10,'Shah Alam','Available','D-SLG-2002'),
(5,'Cyberjaya','Available','D-CY-3001'),
(6,'Cyberjaya','Busy','D-CY-3002');

// Owner (10 rows)
INSERT INTO Owner (firstName, lastName, phoneNumber, address, bankDetails) VALUES
('Jane','Chen','017-1112222','Kuala Lumpur','Maybank 111-222-333'),
('Vijay','Shan','017-2223333','Kuala Lumpur','CIMB 222-333-444'),
('Karim','Ali','017-3334444','Selangor','RHB 333-444-555'),
('Sabri','Sam','017-4445555','Selangor','PublicBank 444-555-666'),
('Tan','Wei','017-5556666','Cyberjaya','HLB 555-666-777'),
('Nur','Huda','017-6667777','Cyberjaya','Maybank 888-111-222'),
('Ahmad','Faiz','017-7778888','Selangor','CIMB 999-222-333'),
('Lim','Jia','017-8889999','Kuala Lumpur','RHB 777-555-444'),
('Suresh','Kumar','017-9990000','Kuala Lumpur','PublicBank 333-222-111'),
('Aqilah','Yusof','017-0001112','Cyberjaya','HLB 123-456-789');

//taxi (10 rows) (officeID only 1-4)
INSERT INTO Taxi (ownerID, officeID, taxiModel, licensePlate, nextServiceDate) VALUES
(1,2,'Perodua Bezza','WXY 1234','2026-01-01'),
(2,3,'Toyota Vios','WXY 2345','2026-02-15'),
(3,4,'Honda City','BKP 3456','2026-01-01'),
(4,4,'Proton Saga','BKP 4567','2026-03-10'),
(5,1,'Perodua Axia','VCR 5678','2026-01-01'),
(6,1,'Toyota Yaris','VCR 6789','2026-02-01'),
(7,4,'Nissan Almera','BKP 7890','2026-04-05'),
(8,2,'Proton Persona','WXY 8901','2026-05-20'),
(9,3,'Honda Jazz','WXY 9012','2026-01-15'),
(10,1,'Perodua Myvi','VCR 0123','2026-01-01');

// DriverTaxiAllocation (10 rows)
INSERT INTO DriverTaxiAllocation (driverID, taxiID, allocatedFrom, allocatedTo) VALUES
(7,1,'2025-10-01',NULL),
(7,8,'2025-11-01',NULL),
(8,2,'2025-09-15',NULL),
(8,9,'2025-11-10',NULL),
(9,3,'2025-08-01',NULL),
(9,4,'2025-10-05',NULL),
(10,4,'2025-11-01',NULL),
(10,5,'2025-11-15',NULL),
(5,6,'2025-11-20',NULL),
(6,10,'2025-12-01',NULL);

// Client (10 rows)
INSERT INTO Client (phoneNumber, email, address) VALUES
('016-1000001','anne.liu@mail.com','111 Ria Road, Cyber 8, Cyberjaya'),
('016-1000002','mark.jacob@mail.com','C-31-12 M City, Ampang, Kuala Lumpur'),
('016-1000003','dan.brown@mail.com','13 KLCC Road, Kuala Lumpur'),
('016-1000004','kara.adam@mail.com','334 Jalan Kurnia, Selangor'),
('016-1000005','mira.han@mail.com','22 Persiaran Bestari, Cyberjaya'),
('03-60001001','ops@techhub.com','TechHub Tower, Cyberjaya'),
('03-60002002','admin@klmall.com','KL Mall HQ, Kuala Lumpur'),
('03-60003003','support@selangorlog.com','Selangor Logistics, Shah Alam'),
('03-60004004','hr@cityuni.com','City University, Kuala Lumpur'),
('03-60005005','finance@cyberbank.com','CyberBank, Cyberjaya');


// PrivateClient (5 rows)
INSERT INTO PrivateClient (clientID, firstName, lastName, dateFirstBooking, notes) VALUES
(1,'Anne','Liu','2025-07-25','First time booking'),
(2,'Mark','Jacob','2025-08-02','VIP customer'),
(3,'Dan','Brown','2025-08-02','Prefers morning rides'),
(4,'Kara','Adam','2025-08-25','Usually weekend'),
(5,'Mira','Han','2025-11-05','Frequent rides in Cyberjaya');

// BusinessClient (5 rows)
INSERT INTO BusinessClient (clientID, companyName, businessRegNumber, contactPerson, faxNumber, city) VALUES
(6,'TechHub Sdn Bhd','REG-TH-2020','Ali Manager','03-90001001','Cyberjaya'),
(7,'KLMall Berhad','REG-KLM-2018','Siti Admin','03-90002002','Kuala Lumpur'),
(8,'Selangor Logistics','REG-SL-2015','Hafiz Ops','03-90003003','Selangor'),
(9,'City University','REG-CU-2010','Nur HR','03-90004004','Kuala Lumpur'),
(10,'CyberBank','REG-CB-2012','Tan Finance','03-90005005','Cyberjaya');

// Contract (5 rows)
INSERT INTO Contract (clientID, fixedFee, jobLimit, startDate, endDate) VALUES
(6, 5000.00, 50, '2025-09-01', '2026-02-28'),
(7, 8000.00, 80, '2025-10-01', '2026-03-31'),
(8, 6500.00, 60, '2025-08-15', '2026-01-31'),
(9, 7000.00, 70, '2025-11-01', '2026-04-30'),
(10,9000.00, 90, '2025-07-01', '2026-06-30');


INSERT INTO Job
(clientID, driverID, taxiID, contractNumber, pickupAddress, dropoffAddress,
 pickupDateTime, dropoffDateTime, mileageUsed, charge, status, failureReason)
VALUES
(1,  7,  1,  NULL,'Cyber 8, Cyberjaya','DPulze Mall, Cyberjaya','2025-11-03 10:00:00','2025-11-03 10:25:00',12.50,28.00,'Completed',NULL),
(5, 10,  5,  NULL,'Cyberjaya','Putrajaya','2025-11-05 09:10:00','2025-11-05 09:40:00',18.20,35.00,'Completed',NULL),
(2,  8,  2,  NULL,'Ampang','KL Sentral','2025-11-10 14:00:00','2025-11-10 14:35:00',20.00,40.00,'Completed',NULL),
(3,  8,  9,  NULL,'KLCC','Bukit Bintang','2025-11-12 18:30:00','2025-11-12 18:50:00', 9.00,22.00,'Completed',NULL),
(4,  9,  3,  NULL,'Shah Alam','Klang','2025-11-20 08:00:00',NULL,NULL,NULL,'Failed','Client not available'),

(6,  7,  6,  1,'TechHub Tower','Cyberjaya Hospital','2025-11-06 11:00:00','2025-11-06 11:20:00',10.00,50.00,'Completed',NULL),
(7,  7,  1,  2,'KL Mall HQ','KL Sentral','2025-11-08 16:00:00','2025-11-08 16:30:00',15.50,80.00,'Completed',NULL),
(8,  9,  4,  3,'Selangor Logistics','Port Klang','2025-11-22 07:30:00','2025-11-22 08:20:00',33.00,60.00,'Completed',NULL),
(9,  8,  2,  4,'City University','KLCC','2025-11-15 12:15:00','2025-11-15 12:45:00',14.20,70.00,'Completed',NULL),
(10,10, 10,  5,'CyberBank','Putrajaya','2025-11-18 09:00:00','2025-11-18 09:40:00',22.00,90.00,'Completed',NULL);

UPDATE Job
SET charge = 50.00
WHERE status = 'Completed'
AND charge IS NULL;

UPDATE Job
SET
  status = 'Completed',
  dropoffDateTime = pickupDateTime + INTERVAL 35 MINUTE,
  mileageUsed = 18.50,
  charge = 45.00,
  failureReason = NULL
WHERE jobID = 35;

// Maintenance (10 rows)
INSERT INTO Maintenance (taxiID, serviceDate, nextServiceDate, description) VALUES
(1,'2025-07-01','2026-01-01','Scheduled service'),
(3,'2025-07-10','2026-01-01','Scheduled service'),
(5,'2025-07-20','2026-01-01','Scheduled service'),
(10,'2025-08-01','2026-01-01','Scheduled service'),
(2,'2025-08-01','2026-02-15','Oil change'),
(6,'2025-08-15','2026-02-01','Scheduled service'),
(4,'2025-09-01','2026-03-10','Brake check'),
(7,'2025-09-20','2026-04-05','Air filter replace'),
(8,'2025-10-05','2026-05-20','Tyre rotation'),
(9,'2025-10-15','2026-01-15','General inspection');


// queries a to t

// query A
SELECT o.officeName, s.firstName, s.lastName, s.phoneNumber
FROM Manager m
JOIN Staff s ON s.staffID = m.staffID
JOIN Office o ON o.officeID = s.officeID
ORDER BY o.officeName, s.lastName;


//query b
SELECT s.firstName, s.lastName
FROM Driver d
JOIN Staff s ON s.staffID = d.staffID
JOIN Office o ON o.officeID = s.officeID
WHERE s.gender = 'Female'
AND o.location = 'Cyberjaya';


//query c

SELECT o.officeName, COUNT(*) AS totalStaff
FROM Staff s
JOIN Office o ON o.officeID = s.officeID
GROUP BY o.officeID, o.officeName;


//  query d
SELECT t.taxiID, t.taxiModel, t.licensePlate, t.nextServiceDate
FROM Taxi t
JOIN Office o ON o.officeID = t.officeID
WHERE o.location = 'Cyberjaya';


// query e
SELECT COUNT(*) AS totalTaxis FROM Taxi;

//query f
SELECT t.taxiID, t.licensePlate, COUNT(DISTINCT a.driverID) AS totalDrivers
FROM Taxi t
LEFT JOIN DriverTaxiAllocation a ON a.taxiID = t.taxiID
GROUP BY t.taxiID, t.licensePlate;

// query g
SELECT o.ownerID, o.firstName, o.lastName, COUNT(t.taxiID) AS totalTaxis
FROM Owner o
JOIN Taxi t ON t.ownerID = o.ownerID
GROUP BY o.ownerID, o.firstName, o.lastName
HAVING COUNT(t.taxiID) > 1;


// query h
SELECT bc.companyName, c.address, bc.city
FROM BusinessClient bc
JOIN Client c ON c.clientID = bc.clientID
WHERE bc.city = 'Cyberjaya';


query i
SELECT ct.contractNumber, bc.companyName, ct.fixedFee, ct.jobLimit, ct.startDate, ct.endDate
FROM Contract ct
JOIN BusinessClient bc ON bc.clientID = ct.clientID
WHERE bc.city = 'Kuala Lumpur'
AND CURDATE() BETWEEN ct.startDate AND ct.endDate;

//query j
SELECT
  TRIM(SUBSTRING_INDEX(c.address, ',', -1)) AS city,
  COUNT(*) AS totalPrivateClients
FROM PrivateClient pc
JOIN Client c ON c.clientID = pc.clientID
GROUP BY TRIM(SUBSTRING_INDEX(c.address, ',', -1))
ORDER BY city;


// query k
SELECT j.jobID, j.pickupDateTime, j.dropoffDateTime, j.pickupAddress, j.dropoffAddress,
j.mileageUsed, j.charge, j.status
FROM Job j
WHERE j.driverID = 7
AND DATE(j.pickupDateTime) = '2025-11-03';


//query l
SELECT s.firstName, s.lastName,
TIMESTAMPDIFF(YEAR, s.DOB, CURDATE()) AS age
FROM Driver d
JOIN Staff s ON s.staffID = d.staffID
WHERE TIMESTAMPDIFF(YEAR, s.DOB, CURDATE()) > 25;


//query m
SELECT DISTINCT pc.firstName, pc.lastName, c.phoneNumber
FROM Job j
JOIN PrivateClient pc ON pc.clientID = j.clientID
JOIN Client c ON c.clientID = pc.clientID
WHERE j.pickupDateTime BETWEEN '2025-11-01' AND '2025-11-30';


//query n
SELECT pc.firstName, pc.lastName, c.address, COUNT(j.jobID) AS totalHires
FROM PrivateClient pc
JOIN Client c ON c.clientID = pc.clientID
JOIN Job j ON j.clientID = pc.clientID
GROUP BY pc.clientID, pc.firstName, pc.lastName, c.address
HAVING COUNT(j.jobID) > 3;

//query o
SELECT AVG(j.charge) AS averagePrivateCharge
FROM Job j
JOIN PrivateClient pc ON pc.clientID = j.clientID
WHERE j.charge IS NOT NULL
AND j.status = 'Completed';


//query p
SELECT t.taxiID, t.licensePlate, COUNT(j.jobID) AS totalJobs
FROM Taxi t
LEFT JOIN Job j ON j.taxiID = t.taxiID
GROUP BY t.taxiID, t.licensePlate;

//query q
SELECT d.staffID AS driverID, s.firstName, s.lastName, COUNT(j.jobID) AS totalJobs
FROM Driver d
JOIN Staff s ON s.staffID = d.staffID
LEFT JOIN Job j ON j.driverID = d.staffID
GROUP BY d.staffID, s.firstName, s.lastName;

//query r
SELECT t.taxiID, t.licensePlate, SUM(COALESCE(j.charge,0)) AS totalAmount
FROM Taxi t
JOIN Job j ON j.taxiID = t.taxiID
WHERE j.pickupDateTime BETWEEN '2025-11-01' AND '2025-11-30'
GROUP BY t.taxiID, t.licensePlate;


//query s
SELECT j.contractNumber, COUNT(j.jobID) AS totalJobs,
SUM(COALESCE(j.mileageUsed,0)) AS totalKM
FROM Job j
WHERE j.contractNumber = 11
GROUP BY j.contractNumber;

//query t
SELECT t.taxiID, t.licensePlate, t.taxiModel, t.nextServiceDate
FROM Taxi t
WHERE t.nextServiceDate = '2026-01-01';




//Nested Query 1
SELECT s.staffID AS driverID, s.firstName, s.lastName, COUNT(*) AS totalJobs
FROM Job j
JOIN Staff s ON s.staffID = j.driverID
GROUP BY s.staffID, s.firstName, s.lastName
HAVING COUNT(*) = (
  SELECT MAX(jobCount)
  FROM (
    SELECT COUNT(*) AS jobCount
    FROM Job
    GROUP BY driverID
  ) x
);


//Nested Query 2
SELECT pc.clientID, pc.firstName, pc.lastName, SUM(j.charge) AS totalSpent
FROM PrivateClient pc
JOIN Job j ON j.clientID = pc.clientID
WHERE j.charge IS NOT NULL AND j.status = 'Completed'
GROUP BY pc.clientID, pc.firstName, pc.lastName
HAVING SUM(j.charge) > (
  SELECT AVG(totalPerClient)
  FROM (
    SELECT SUM(j2.charge) AS totalPerClient
    FROM Job j2
    JOIN PrivateClient pc2 ON pc2.clientID = j2.clientID
    WHERE j2.charge IS NOT NULL AND j2.status = 'Completed'
    GROUP BY pc2.clientID
  ) t
);

//Nested Query 3
SELECT t.taxiID, t.licensePlate, t.taxiModel
FROM Taxi t
WHERE NOT EXISTS (
  SELECT 1
  FROM Maintenance m
  WHERE m.taxiID = t.taxiID
);

//Nested Query 4
SELECT d.staffID AS driverID, s.firstName, s.lastName
FROM Driver d
JOIN Staff s ON s.staffID = d.staffID
WHERE d.staffID IN (
  SELECT a.driverID
  FROM DriverTaxiAllocation a
  WHERE a.allocatedTo IS NULL
);


//Nested Query 5
SELECT bc.clientID, bc.companyName, ct.contractNumber
FROM BusinessClient bc
JOIN Contract ct ON ct.clientID = bc.clientID
WHERE NOT EXISTS (
  SELECT 1
  FROM Job j
  WHERE j.contractNumber = ct.contractNumber
);










