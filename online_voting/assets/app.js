/**
 * Online Voting System
 * Modern JavaScript Engine & Client Datastore
 */

const OVS = (() => {
  const KEYS = {
    USERS: 'ovs_users',
    VOTERS: 'ovs_voters',
    ELECTIONS: 'ovs_elections',
    CANDIDATES: 'ovs_candidates',
    PARTICIPATION: 'ovs_participation',
    VOTES: 'ovs_votes',
    SESSION: 'ovs_session',
    FLASH: 'ovs_flash'
  };

  // Seed default dataset matching original records
  function initStorage(force = false) {
    if (force || !localStorage.getItem(KEYS.USERS)) {
      const initialUsers = [
        { user_id: 1, name: 'Administrator', email: 'admin@vote.com', password: 'Admin@123', role: 'admin', created_at: '2026-10-06 05:00:00' },
        { user_id: 2, name: 'SHIVAM', email: 'utsavupadhyay813@gmail.com', password: 'Password@123', role: 'voter', created_at: '2026-10-06 06:00:00' },
        { user_id: 3, name: 'MOHIT UPADHYAY', email: 'mohitffyt6969@gmail.com', password: 'Password@123', role: 'voter', created_at: '2026-10-06 06:15:00' },
        { user_id: 4, name: 'YASH', email: 'yash@vote.com', password: 'Password@123', role: 'voter', created_at: '2026-10-06 06:30:00' }
      ];
      localStorage.setItem(KEYS.USERS, JSON.stringify(initialUsers));
    }

    if (force || !localStorage.getItem(KEYS.VOTERS)) {
      const initialVoters = [
        { voter_id: 1, user_id: 2, voter_number: 'VTR2600002', status: 'verified' },
        { voter_id: 2, user_id: 3, voter_number: 'VTR2600003', status: 'verified' },
        { voter_id: 3, user_id: 4, voter_number: 'VTR2600004', status: 'verified' }
      ];
      localStorage.setItem(KEYS.VOTERS, JSON.stringify(initialVoters));
    }

    if (force || !localStorage.getItem(KEYS.ELECTIONS)) {
      const initialElections = [
        {
          election_id: 1,
          title: 'KD COLLEGE YOUTH PRESIDENT ELECTION 2026',
          description: 'Official student body election for college youth presidency.',
          start_date: '2026-10-01 09:00:00',
          end_date: '2026-11-30 18:00:00',
          status: 'open',
          created_at: '2026-10-01 09:00:00'
        },
        {
          election_id: 2,
          title: 'SAKET COLLEGE YOUTH ELECTION',
          description: 'Inter-collegiate representative election.',
          start_date: '2025-10-08 15:28:00',
          end_date: '2026-10-08 15:40:00',
          status: 'closed',
          created_at: '2025-10-08 15:28:05'
        }
      ];
      localStorage.setItem(KEYS.ELECTIONS, JSON.stringify(initialElections));
    }

    if (force || !localStorage.getItem(KEYS.CANDIDATES)) {
      const initialCandidates = [
        {
          candidate_id: 1,
          election_id: 1,
          name: 'Aarav Sharma',
          party: 'Youth Progressive Alliance',
          symbol: '🦁',
          manifesto: 'Campus digitalization, transparent club funding, and sports development.'
        },
        {
          candidate_id: 2,
          election_id: 1,
          name: 'Rohan Patil',
          party: 'United Student Front',
          symbol: '⚡',
          manifesto: '24/7 library access, advanced technical research labs, and career mentorship.'
        },
        {
          candidate_id: 3,
          election_id: 1,
          name: 'Priya Deshmukh',
          party: 'Independent Voices',
          symbol: '🌟',
          manifesto: 'Student welfare, mental health awareness, and cultural activities.'
        },
        {
          candidate_id: 4,
          election_id: 2,
          name: 'Neha Gupta',
          party: 'Visionary League',
          symbol: '🦅',
          manifesto: 'Academic excellence and community service initiatives.'
        },
        {
          candidate_id: 5,
          election_id: 2,
          name: 'Aditya Verma',
          party: 'Action Party',
          symbol: '🗳️',
          manifesto: 'Student grievance redressal and campus upgrades.'
        }
      ];
      localStorage.setItem(KEYS.CANDIDATES, JSON.stringify(initialCandidates));
    }

    if (force || !localStorage.getItem(KEYS.PARTICIPATION)) {
      const initialParticipation = [
        { election_id: 2, voter_id: 1, voted_at: '2026-10-08 15:30:00' }
      ];
      localStorage.setItem(KEYS.PARTICIPATION, JSON.stringify(initialParticipation));
    }

    if (force || !localStorage.getItem(KEYS.VOTES)) {
      const initialVotes = [
        { vote_id: 1, election_id: 2, candidate_id: 4, cast_at: '2026-10-08 15:30:00' }
      ];
      localStorage.setItem(KEYS.VOTES, JSON.stringify(initialVotes));
    }
  }

  initStorage();

  // Low level getter & setter
  function get(key) {
    try {
      return JSON.parse(localStorage.getItem(key)) || [];
    } catch (e) {
      return [];
    }
  }

  function set(key, val) {
    localStorage.setItem(key, JSON.stringify(val));
  }

  // Utilities
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function getSession() {
    try {
      return JSON.parse(sessionStorage.getItem(KEYS.SESSION));
    } catch (e) {
      return null;
    }
  }

  function setSession(user) {
    sessionStorage.setItem(KEYS.SESSION, JSON.stringify(user));
  }

  function clearSession() {
    sessionStorage.removeItem(KEYS.SESSION);
  }

  function flash(message, type = 'success') {
    sessionStorage.setItem(KEYS.FLASH, JSON.stringify({ message, type }));
  }

  function getFlash() {
    const f = sessionStorage.getItem(KEYS.FLASH);
    if (!f) return null;
    sessionStorage.removeItem(KEYS.FLASH);
    try {
      return JSON.parse(f);
    } catch (e) {
      return null;
    }
  }

  function renderFlash(container) {
    const f = getFlash();
    if (!f) return;
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert a-${f.type}`;
    alertDiv.textContent = f.message;
    const parent = container || document.querySelector('main.container');
    if (parent) {
      parent.insertBefore(alertDiv, parent.firstChild);
    }
  }

  function requireRole(requiredRole, relativePathToRoot = '') {
    const s = getSession();
    if (!s || s.role !== requiredRole) {
      flash('Please log in with appropriate permissions to access this page.', 'error');
      window.location.href = relativePathToRoot + 'login.html';
      return false;
    }
    return true;
  }

  // Election status logic
  function electionState(el) {
    if (el.status === 'draft') return 'draft';
    if (el.status === 'closed') return 'closed';
    const now = Date.now();
    const start = new Date(el.start_date.replace(' ', 'T')).getTime();
    const end = new Date(el.end_date.replace(' ', 'T')).getTime();
    if (now < start) return 'scheduled';
    if (now > end) return 'ended';
    return 'live';
  }

  function badge(state) {
    return `<span class="badge b-${escapeHtml(state)}">${escapeHtml(state.toUpperCase())}</span>`;
  }

  function resultsVisible(el) {
    return ['ended', 'closed'].includes(electionState(el));
  }

  // Tally votes
  function tally(eid) {
    const candList = get(KEYS.CANDIDATES).filter(c => Number(c.election_id) === Number(eid));
    const votes = get(KEYS.VOTES).filter(v => Number(v.election_id) === Number(eid));
    const participation = get(KEYS.PARTICIPATION).filter(p => Number(p.election_id) === Number(eid));
    const verifiedVoters = get(KEYS.VOTERS).filter(v => v.status === 'verified');

    const candidates = candList.map(c => {
      const voteCount = votes.filter(v => Number(v.candidate_id) === Number(c.candidate_id)).length;
      return { ...c, votes: voteCount };
    }).sort((a, b) => b.votes - a.votes || a.name.localeCompare(b.name));

    const total = votes.length;
    const eligible = verifiedVoters.length;
    const votedCount = participation.length;
    const turnout = eligible > 0 ? Number(((votedCount * 100) / eligible).toFixed(1)) : 0;
    const maxVotes = candidates.length > 0 ? candidates[0].votes : 0;

    return { candidates, total, eligible, turnout, votedCount, maxVotes };
  }

  // Layout rendering (Top Persona Bar + Navbar + Footer + Flash)
  function renderLayout(options = {}) {
    const { title = 'Online Voting System', active = '', relativePath = '' } = options;
    document.title = title;

    const session = getSession();

    // 1. Top Quick Persona & Demo Bar (for easy testing workflow)
    let personaBar = document.querySelector('.persona-bar');
    if (!personaBar) {
      personaBar = document.createElement('div');
      personaBar.className = 'persona-bar noprint';
      document.body.insertBefore(personaBar, document.body.firstChild);
    }

    const currentRoleLabel = session 
      ? `Active: <b>${escapeHtml(session.name)}</b> (${session.role === 'admin' ? '🛡️ Admin' : '🗳️ Voter'})` 
      : 'Active: <i>Guest (Not Logged In)</i>';

    personaBar.innerHTML = `
      <div>${currentRoleLabel}</div>
      <div style="display:flex;gap:6px;align-items:center;">
        <span style="opacity:0.7">Quick Test:</span>
        <button type="button" class="persona-btn" id="ovs-quick-admin">👑 Admin</button>
        <button type="button" class="persona-btn" id="ovs-quick-voter">🗳️ Voter (Shivam)</button>
        ${session ? '<button type="button" class="persona-btn" id="ovs-quick-logout" style="background:#e03131">🚪 Logout</button>' : ''}
        <button type="button" class="persona-btn" id="ovs-reset-db" title="Restore default elections and demo voters">🔄 Reset Data</button>
      </div>
    `;

    document.getElementById('ovs-quick-admin').onclick = () => {
      login('admin@vote.com', 'Admin@123');
      flash('Switched to Admin role.');
      window.location.href = relativePath + 'admin/dashboard.html';
    };

    document.getElementById('ovs-quick-voter').onclick = () => {
      login('utsavupadhyay813@gmail.com', 'Password@123');
      flash('Switched to Voter role (Shivam).');
      window.location.href = relativePath + 'voter/dashboard.html';
    };

    if (session) {
      document.getElementById('ovs-quick-logout').onclick = () => {
        clearSession();
        flash('Logged out.');
        window.location.href = relativePath + 'login.html';
      };
    }

    document.getElementById('ovs-reset-db').onclick = () => {
      if (confirm('Reset database to clean default state?')) {
        initStorage(true);
        flash('Demo database reset to clean default state.');
        window.location.reload();
      }
    };

    // 2. Navigation Bar
    let nav = document.querySelector('nav.nav');
    if (!nav) {
      nav = document.createElement('nav');
      nav.className = 'nav';
      personaBar.after(nav);
    }

    let linksHtml = '';

    if (session && session.role === 'admin') {
      linksHtml = `
        <a href="${relativePath}admin/dashboard.html" class="${active === 'dashboard' ? 'active-link' : ''}">Dashboard</a>
        <a href="${relativePath}admin/elections.html" class="${active === 'elections' ? 'active-link' : ''}">Elections</a>
        <a href="${relativePath}admin/candidates.html" class="${active === 'candidates' ? 'active-link' : ''}">Candidates</a>
        <a href="${relativePath}admin/voters.html" class="${active === 'voters' ? 'active-link' : ''}">Voters</a>
        <a href="${relativePath}admin/results.html" class="${active === 'results' ? 'active-link' : ''}">Results</a>
        <span class="who">👑 ${escapeHtml(session.name)}</span>
        <a href="javascript:void(0)" id="ovs-logout-btn" class="btn sm bad" style="margin-left:6px;">Logout</a>
      `;
    } else if (session && session.role === 'voter') {
      linksHtml = `
        <a href="${relativePath}voter/dashboard.html" class="${active === 'dashboard' ? 'active-link' : ''}">My Elections</a>
        <span class="who">🗳️ ${escapeHtml(session.name)}</span>
        <a href="javascript:void(0)" id="ovs-logout-btn" class="btn sm bad" style="margin-left:6px;">Logout</a>
      `;
    } else {
      linksHtml = `
        <a href="${relativePath}index.html" class="${active === 'home' ? 'active-link' : ''}">Home</a>
        <a href="${relativePath}login.html" class="${active === 'login' ? 'active-link' : ''}">Login</a>
        <a href="${relativePath}register.html" class="${active === 'register' ? 'active-link' : ''}">Register</a>
      `;
    }

    nav.innerHTML = `
      <a class="brand" href="${relativePath}index.html">🗳️ Online Voting</a>
      <div class="links">${linksHtml}</div>
    `;

    const logoutBtn = document.getElementById('ovs-logout-btn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', () => {
        clearSession();
        flash('You have been logged out successfully.');
        window.location.href = relativePath + 'login.html';
      });
    }

    // 3. Footer
    let foot = document.querySelector('footer.foot');
    if (!foot) {
      foot = document.createElement('footer');
      foot.className = 'foot';
      foot.innerHTML = 'Online Voting System &middot; TYCS Mini Project';
      document.body.appendChild(foot);
    }

    // 4. Flash Messages
    renderFlash();
  }

  // Auth operations
  function login(email, password) {
    const users = get(KEYS.USERS);
    const normalizedEmail = (email || '').trim().toLowerCase();
    const user = users.find(u => u.email.toLowerCase() === normalizedEmail && u.password === password);
    if (!user) {
      return { success: false, message: 'Invalid email or password.' };
    }
    const sessionData = {
      uid: user.user_id,
      name: user.name,
      email: user.email,
      role: user.role
    };
    setSession(sessionData);
    return { success: true, user: sessionData };
  }

  function register(name, email, password) {
    const trimmedName = (name || '').trim();
    const normalizedEmail = (email || '').trim().toLowerCase();

    if (!trimmedName || !normalizedEmail.includes('@')) {
      return { success: false, message: 'Enter a valid name and email.' };
    }
    if (!password || password.length < 8) {
      return { success: false, message: 'Password must be at least 8 characters.' };
    }

    const users = get(KEYS.USERS);
    if (users.some(u => u.email.toLowerCase() === normalizedEmail)) {
      return { success: false, message: 'Email already registered.' };
    }

    const newUserId = users.length ? Math.max(...users.map(u => u.user_id)) + 1 : 1;
    const now = new Date().toISOString().replace('T', ' ').substring(0, 19);

    const newUser = {
      user_id: newUserId,
      name: trimmedName,
      email: normalizedEmail,
      password: password,
      role: 'voter',
      created_at: now
    };
    users.push(newUser);
    set(KEYS.USERS, users);

    // Register voter entry
    const voters = get(KEYS.VOTERS);
    const newVoterId = voters.length ? Math.max(...voters.map(v => v.voter_id)) + 1 : 1;
    const yearSuffix = new Date().getFullYear().toString().slice(-2);
    const voterNumber = 'VTR' + yearSuffix + String(newUserId).padStart(5, '0');

    voters.push({
      voter_id: newVoterId,
      user_id: newUserId,
      voter_number: voterNumber,
      status: 'pending'
    });
    set(KEYS.VOTERS, voters);

    return {
      success: true,
      voterNumber: voterNumber,
      message: `Registered! Your voter number is ${voterNumber}. Wait for admin verification, then log in.`
    };
  }

  function getVoterByUserId(userId) {
    const voters = get(KEYS.VOTERS);
    return voters.find(v => Number(v.user_id) === Number(userId)) || null;
  }

  return {
    KEYS,
    get,
    set,
    e: escapeHtml,
    getSession,
    setSession,
    clearSession,
    flash,
    getFlash,
    renderFlash,
    requireRole,
    electionState,
    badge,
    resultsVisible,
    tally,
    renderLayout,
    login,
    register,
    getVoterByUserId,
    initStorage
  };
})();
