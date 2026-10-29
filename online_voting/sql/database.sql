-- Online Voting System - database schema (MySQL / MariaDB)
CREATE DATABASE IF NOT EXISTS online_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_voting;

CREATE TABLE users (
  user_id    INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,              -- bcrypt hash via password_hash()
  role       ENUM('admin','voter') NOT NULL DEFAULT 'voter',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE voters (
  voter_id     INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL UNIQUE,
  voter_number VARCHAR(20) NOT NULL UNIQUE,
  status       ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE elections (
  election_id INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(150) NOT NULL,
  description TEXT,
  start_date  DATETIME NOT NULL,
  end_date    DATETIME NOT NULL,
  status      ENUM('draft','open','closed') NOT NULL DEFAULT 'draft',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE candidates (
  candidate_id INT AUTO_INCREMENT PRIMARY KEY,
  election_id  INT NOT NULL,
  name         VARCHAR(100) NOT NULL,
  party        VARCHAR(100) NOT NULL,
  symbol       VARCHAR(10)  DEFAULT '🗳️',
  manifesto    TEXT,
  FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Who has voted (one person = one vote). Contains NO candidate info.
CREATE TABLE voter_participation (
  election_id INT NOT NULL,
  voter_id    INT NOT NULL,
  voted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (election_id, voter_id),             -- DB-level duplicate-vote protection
  FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE,
  FOREIGN KEY (voter_id)    REFERENCES voters(voter_id)       ON DELETE CASCADE
) ENGINE=InnoDB;

-- Anonymous ballots. Contains NO voter info -> ballot secrecy.
CREATE TABLE votes (
  vote_id      INT AUTO_INCREMENT PRIMARY KEY,
  election_id  INT NOT NULL,
  candidate_id INT NOT NULL,
  cast_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (election_id)  REFERENCES elections(election_id)  ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE
) ENGINE=InnoDB;
