SET NAMES utf8mb4;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS MATCH_STATS;
DROP TABLE IF EXISTS MATCHES;
DROP TABLE IF EXISTS CHARACTERS;
DROP TABLE IF EXISTS PLAYERS;

CREATE TABLE PLAYERS (
  player_id    INT          NOT NULL,
  username     VARCHAR(50)  NOT NULL,
  level        INT          NOT NULL DEFAULT 1,
  server       VARCHAR(20)  NOT NULL,
  creation_date DATETIME    NOT NULL,
  PRIMARY KEY (player_id),
  UNIQUE KEY uq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE CHARACTERS (
  character_id INT          NOT NULL,
  name         VARCHAR(50)  NOT NULL,
  role         VARCHAR(20)  NOT NULL,
  difficulty   INT          NOT NULL DEFAULT 5,
  PRIMARY KEY (character_id),
  UNIQUE KEY uq_name (name),
  CONSTRAINT chk_difficulty CHECK (difficulty BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE MATCHES (
  match_id  INT         NOT NULL,
  date      DATETIME    NOT NULL,
  duration  INT         NOT NULL,
  map       VARCHAR(50) NOT NULL,
  game_mode VARCHAR(30) NOT NULL,
  PRIMARY KEY (match_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE MATCH_STATS (
  match_id     INT                 NOT NULL,
  player_id    INT                 NOT NULL,
  character_id INT                 NOT NULL,
  win_loss     ENUM('Win','Loss')  NOT NULL,
  kills        INT                 NOT NULL DEFAULT 0,
  deaths       INT                 NOT NULL DEFAULT 0,
  assists      INT                 NOT NULL DEFAULT 0,
  PRIMARY KEY (match_id, player_id),
  KEY fk_char (character_id),
  CONSTRAINT fk_match     FOREIGN KEY (match_id)     REFERENCES MATCHES    (match_id)     ON DELETE CASCADE,
  CONSTRAINT fk_player    FOREIGN KEY (player_id)    REFERENCES PLAYERS    (player_id)    ON DELETE CASCADE,
  CONSTRAINT fk_character FOREIGN KEY (character_id) REFERENCES CHARACTERS (character_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET foreign_key_checks = 1;
