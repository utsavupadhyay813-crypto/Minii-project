# Online Voting System (TYCS Mini Project)

**Stack:** HTML + CSS + JavaScript | PHP 8 (PDO) | MySQL/MariaDB | XAMPP

## Setup (XAMPP)
1. Copy the `online_voting` folder to `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin` -> Import -> choose `sql/database.sql`.
4. Open `http://localhost/online_voting/setup.php` once (creates the admin), then **delete `setup.php`**.
5. Open `http://localhost/online_voting/`.

Default admin: `admin@vote.com` / `Admin@123` (change it!)

## Typical demo flow
1. Admin: create election -> add 2+ candidates -> Start (open) the election.
2. Voter: register -> Admin verifies -> voter logs in and votes once.
3. Admin: Results page -> Print / Save as PDF.

## Security features
- `password_hash()` / `password_verify()` (bcrypt)
- PDO prepared statements (SQL-injection safe), output escaping with `htmlspecialchars` (XSS safe)
- CSRF tokens on every POST form, `session_regenerate_id()` on login, login attempt throttling
- **Ballot secrecy:** `voter_participation` (who voted) is separate from `votes` (what was chosen)
- **One person, one vote:** composite PRIMARY KEY (election_id, voter_id) + DB transaction
- Candidates locked once an election leaves draft; votes accepted only while open AND inside start/end time

## Folder structure
```
online_voting/
 index.php  login.php  register.php  logout.php  setup.php
 includes/  init.php header.php footer.php
 admin/     dashboard.php elections.php candidates.php voters.php results.php
 voter/     dashboard.php vote.php results.php
 assets/    style.css
 sql/       database.sql
```
