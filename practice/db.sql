CREATE TABLE Sportsmans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL,
    age INTEGER NOT NULL,
    country TEXT NOT NULL
);

CREATE TABLE Sports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL
);

CREATE TABLE Stadiums (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL,
    city TEXT NOT NULL
);

CREATE TABLE Performances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    SportsmanID INTEGER NOT NULL,
    SportId INTEGER NOT NULL,
    StadiumID INTEGER NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (SportsmanID) REFERENCES Sportsmans (id),
    FOREIGN KEY (SportId) REFERENCES Sports (id),
    FOREIGN KEY (StadiumID) REFERENCES Stadiums (id)
);