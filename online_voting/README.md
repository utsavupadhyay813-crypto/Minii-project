# Online Voting System (TYCS Mini Project)

**Stack:** HTML5 | CSS3 | Modern JavaScript (ES6+ Engine & LocalStorage Datastore) | Apache / XAMPP

---

## 🌐 How to Run & Access

1. Ensure **Apache** is running in the XAMPP Control Panel.
2. Open your browser and navigate to:
   - **Home / Landing:** `http://localhost/online_voting/` (or `http://localhost/online_voting/index.html`)
   - **Login:** `http://localhost/online_voting/login.html`
   - **Register:** `http://localhost/online_voting/register.html`

### Default Login Accounts:
- **Admin:** `admin@vote.com` / `Admin@123`
- **Voter:** `utsavupadhyay813@gmail.com` / `Password@123` (or register a new voter)

---

## 🚀 Typical Demo Flow
1. **Admin:** Log in &rarr; Manage Elections &rarr; Add 2+ Candidates &rarr; Start (open) the election.
2. **Voter:** Register at `/register.html` &rarr; Admin verifies in `/admin/voters.html` &rarr; Voter logs in and votes once in `/voter/vote.html`.
3. **Results:** View live tally, percentage turnouts, and click **Print / Save as PDF** in `/admin/results.html`.

---

## 🛡️ Security & Integrity Features
- **Strict Ballot Secrecy:** Participation records (`who voted`) are stored completely separate from ballots (`what candidate was chosen`).
- **One Person, One Vote:** Programmatic constraint prevents multiple submissions per voter.
- **Voter Verification Gate:** Only verified voter accounts can access voting booths.
- **Candidate Lock:** Candidates cannot be modified once an election leaves draft state.
- **Complete Legacy PHP Backup:** Original PHP scripts are preserved in `php_legacy_backup/`.

---

## 📁 Folder Structure
```
online_voting/
├── index.html            # Main landing page
├── login.html            # Unified login portal (Admin & Voter)
├── register.html         # Voter registration portal
├── admin/
│   ├── dashboard.html    # Statistics & overview
│   ├── elections.html    # Manage & schedule elections
│   ├── candidates.html   # Manage candidate nominations
│   ├── voters.html       # Voter verification & management
│   └── results.html      # Live results & printable audit report
├── voter/
│   ├── dashboard.html    # Voter portal & active election list
│   ├── vote.html         # Secure voting booth
│   └── results.html      # Public outcome & winner display
├── assets/
│   ├── style.css         # UI stylesheet (identical theme & layout)
│   └── app.js            # Modern ES6+ business logic & storage engine
├── php_legacy_backup/    # Safe backup of previous PHP scripts
└── sql/
    └── database.sql      # Schema definitions
```
