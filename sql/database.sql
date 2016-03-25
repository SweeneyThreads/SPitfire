CREATE TABLE Requisiti (
	ID varchar(100) PRIMARY KEY,
	Nome varchar(150),
	Descrizione text,
	Fonte varchar(50),
	completato tinyint(1) DEFAULT 0,
	Note text
)engine=InnoDB;

CREATE TABLE derivazRequisiti (
	padre varchar(100),
	figlio varchar(100),
	PRIMARY KEY(padre,figlio),
	FOREIGN KEY padre REFERENCES Requisiti(ID),
	FOREIGN KEY figlio REFERENCES Requisiti(ID)
)engine=InnoDB;

CREATE TABLE terminiGlossario(
	nome varchar(100) PRIMARY KEY
);