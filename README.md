# Online Voting System (TYCS Mini Project)

A secure, transparent, and responsive digital election management and balloting platform built with **HTML5, CSS3, and Modern JavaScript (ES6+)**.

---

## 🚀 Live Deployment Options

This project is 100% static, client-side, and ready for 1-click deployment on **Vercel**, **Render**, or **GitHub Pages**.

### Option A: Deploy on Vercel
1. Go to [vercel.com](https://vercel.com/) &rarr; click **"Add New Project"**.
2. Select your repository: **`utsavupadhyay813-crypto/Minii-project`**.
3. Keep default settings and click **"Deploy"**.

### Option B: Deploy on Render
1. Go to [render.com](https://render.com/) &rarr; click **"New +"** &rarr; select **"Static Site"**.
2. Connect your GitHub repository: **`utsavupadhyay813-crypto/Minii-project`**.
3. Configure:
   * **Name:** `online-voting` (or your choice)
   * **Branch:** `main`
   * **Build Command:** *(leave empty)*
   * **Publish Directory:** `online_voting`
4. Click **"Create Static Site"**. Your live `.onrender.com` link will be active in seconds!

---

## 🔑 Login Credentials

### 🛡️ Administrator Account
* **Email:** `admin@vote.com`
* **Password:** `Admin@123`
* **Access:** Full Admin Dashboard, Manage Elections, Nominate Candidates, Verify Voters, Real-time Turnout Analytics, Printable Audit Reports.

### 🗳️ Verified Voter Accounts
* **SHIVAM:** `utsavupadhyay813@gmail.com` / `Password@123` (Voter ID: `VTR2600002`)
* **MOHIT UPADHYAY:** `mohitffyt6969@gmail.com` / `Password@123` (Voter ID: `VTR2600003`)
* **YASH:** `yash@vote.com` / `Password@123` (Voter ID: `VTR2600004`)
* *(Or register any new voter directly via `register.html`)*

---

## 🛡️ Key Features & Security Design

1. **Strict Ballot Secrecy:**
   * Participation records (*who has voted*) are stored completely separately from ballots (*which candidate was chosen*).
   * Prevents anyone, including system administrators, from linking a voter to their cast ballot.

2. **One Person, One Vote:**
   * Programmatic constraints prevent duplicate submissions per voter in any given election.

3. **Voter Verification Gate:**
   * Newly registered voters must be verified by an administrator before they are permitted to vote.

4. **Candidate Lockdown:**
   * Candidate lists cannot be altered once an election leaves draft state, ensuring ballot integrity.

5. **Client-Side Datastore & Offline Support:**
   * Built using a modern ES6+ datastore engine (`localStorage`), enabling it to run entirely in modern web browsers without server configuration.

---

## 📁 Repository Structure

```
├── render.yaml           # Render deployment configuration
├── vercel.json           # Vercel routing configuration
├── index.html            # Automatic root entry router
├── README.md             # Project documentation
└── online_voting/
    ├── index.html        # Public homepage
    ├── login.html        # Unified login portal
    ├── register.html     # Voter registration
    ├── assets/
    │   ├── style.css     # Responsive CSS design
    │   └── app.js        # Core JavaScript engine & storage
    ├── admin/
    │   ├── dashboard.html# Admin metrics & analytics
    │   ├── elections.html# Election scheduling & state management
    │   ├── candidates.html# Candidate nomination management
    │   ├── voters.html   # Voter identity verification
    │   └── results.html  # Live visual tallies & PDF export
    └── voter/
        ├── dashboard.html# Voter elections portal
        ├── vote.html     # Digital voting booth with live candidate search
        └── results.html  # Official election outcomes
```
