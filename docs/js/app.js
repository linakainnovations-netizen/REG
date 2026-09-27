/* St. Charles Lwanga Regiment — STATIC DEMO APP
 * Mirrors dashboard/index.php routing + each role's sidebar.php + main_pages/*.php
 * All passwords: REGIMENT. State persists in localStorage (per-browser demo).
 */
(function(){
"use strict";
const $ = s => document.querySelector(s);
const esc = s => String(s??"").replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const store = {
  get(k, fb){ try{ const v = localStorage.getItem("demo_"+k); return v?JSON.parse(v):fb; }catch(e){ return fb; } },
  set(k, v){ localStorage.setItem("demo_"+k, JSON.stringify(v)); }
};

// ---- session (mirrors SessionManager::requireLogin) ----
const sess = store.get("session", null);
if(!sess || !sess.username){ location.href = "login.html"; return; }
const me = demoRoleFor(sess.username);
if(!me){ localStorage.removeItem("demo_session"); location.href="login.html"; return; }

// ---- live demo state (seeded from demo-data.js, editable in-browser) ----
let announcements = store.get("announcements", DEMO_ANNOUNCEMENTS);
let financeTx = store.get("finance", DEMO_FINANCE);
let events = store.get("events", DEMO_EVENTS);
let live = store.get("live", DEMO_LIVE);
let giving = store.get("giving", DEMO_GIVING);
function persist(){ store.set("announcements",announcements); store.set("finance",financeTx); store.set("events",events); store.set("live",live); store.set("giving",giving); }

// ---- NAVS: mirror each sidebar.php ----
const NAVS = {
  admin_priest: [
    ["Management",[["overview","th-large","Overview"],["users","user-friends","User Directory"],["groups","hands-helping","Parish Groups"],["announcements","megaphone","Announcements"],["bulletins","newspaper","Bulletins"],["events","calendar-alt","Events"],["live_updates","bolt","Live Updates"],["ministries","hands-helping","Ministries"]]],
    ["Oversight (built pages)",[["finance","chart-pie","Collections"],["giving","hand-holding-heart","Giving Verify"],["roq","file-invoice-dollar","Procurement"]]],
    ["System",[["profile","user-circle","My Profile"],["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  parish_council: [
    ["Management",[["overview","th-large","Overview"],["announcements","megaphone","Announcements"],["live_updates","bolt","Live Updates"],["events","calendar-alt","Events"],["bulletins","newspaper","Bulletins"]]],
    ["Secretary",[["secretary_groups","users-cog","Manage Groups"],["secretary_rosters","calendar-alt","Plan Rosters"]]],
    ["Treasury",[["treasurer_finance","wallet","Financial Console"],["giving","hand-holding-heart","Giving Verify"]]],
    ["Governance",[["roq","file-invoice-dollar","Procurement"],["roq_submit","plus","New ROQ"],["roq_submissions","inbox","ROQ Offers"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  youth_council: [
    ["Management",[["overview","th-large","Overview"],["users","user-friends","User Directory"],["groups","hands-helping","Parish Groups"],["announcements","megaphone","Announcements"],["media","broadcast-tower","Media & Live"]]],
    ["Finance & ROQ",[["finance","chart-pie","Collections"],["roq","file-invoice-dollar","Procurement"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  lay_groups: [
    ["Management",[["overview","th-large","Overview"],["users","user-friends","User Directory"],["groups","hands-helping","Parish Groups"],["announcements","megaphone","Announcements"]]],
    ["Finance & ROQ",[["finance","chart-pie","Collections"],["roq","file-invoice-dollar","Procurement"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  choirs: [
    ["Management",[["overview","th-large","Overview"],["users","user-friends","User Directory"],["groups","hands-helping","Parish Groups"],["announcements","megaphone","Announcements"]]],
    ["Finance & ROQ",[["finance","chart-pie","Collections"],["roq","file-invoice-dollar","Procurement"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  SCC_zonez: [
    ["Management",[["overview","th-large","Overview"],["users","user-friends","User Directory"],["groups","hands-helping","Parish Groups"],["announcements","megaphone","Announcements"]]],
    ["Finance & ROQ",[["finance","chart-pie","Collections"],["roq","file-invoice-dollar","Procurement"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ],
  members: [
    ["My Parish",[["overview","th-large","Overview"],["announcements","megaphone","Announcements"],["events","calendar-alt","Events"],["bulletins","newspaper","Bulletins"]]],
    ["Participate",[["roq_submit","plus","Request Quotation"],["giving","hand-holding-heart","My Giving"]]],
    ["System",[["handover","handshake","Handover"],["settings","cog","Portal Settings"]]]
  ]
};
const TITLES = { admin_priest:"Priest Portal", parish_council:"Parish Council Portal", youth_council:"Youth Council Portal", lay_groups:"Lay Groups Portal", choirs:"Choirs Portal", SCC_zonez:"SCC Zone Portal", members:"Member Portal" };

// current page from hash (mirrors ?page= in dashboard/index.php)
function curPage(){ const h=(location.hash||"").replace("#/","").trim(); return h || "overview"; }

// ---- sidebar (mirrors siders_pages/sidebar.php) ----
function renderSidebar(){
  const nav = NAVS[me.folder] || NAVS.members;
  const page = curPage();
  let html = `<a href="#/overview" class="sidebar-brand"><div class="brand-icon"><img src="assets/logo.png" alt="logo" style="height:40px"></div><div class="brand-text"><span>St. Charles Lwanga</span><small>${esc(TITLES[me.folder]||"Portal")}</small></div></a><nav class="sidebar-nav">`;
  nav.forEach(([section, items])=>{
    // role-gate like the PHP strpos($_SESSION['role_name'],...) checks
    const filtered = items.filter(([slug])=>{
      if(me.folder!=="parish_council") return true;
      if(slug==="secretary_groups"||slug==="secretary_rosters") return /Secretary|Chairperson|Super Admin|Priest/i.test(me.role_name);
      if(slug==="treasurer_finance"||slug==="giving") return /Treasurer|Chairperson|Super Admin|Priest/i.test(me.role_name);
      return true;
    });
    if(!filtered.length) return;
    html += `<div class="nav-section">${esc(section)}</div>`;
    filtered.forEach(([slug,icon,label])=>{ html += `<a href="#/${slug}" class="nav-item ${page===slug?'active':''}"><i class="fas fa-${icon}"></i><span>${esc(label)}</span></a>`; });
  });
  html += `<a href="#" id="logoutLink" class="nav-item logout-item"><i class="fas fa-sign-out-alt"></i><span>Log Out</span></a></nav>`;
  html += `<div class="sidebar-switch">View as (demo)<select id="switchRole">${DEMO_USERS.map(u=>`<option value="${u.username}" ${u.username===me.username?'selected':''}>${esc(u.username)} — ${esc(u.role_name)}</option>`).join('')}</select></div><div class="sidebar-footer"><p>V 1.2.0 Stable · static demo</p></div>`;
  $("#sidebar").innerHTML = html;
  $("#logoutLink").onclick = e=>{e.preventDefault(); localStorage.removeItem("demo_session"); location.href="login.html";};
  $("#switchRole").onchange = e=>{ store.set("session",{username:e.target.value,at:Date.now()}); location.reload(); };
}

// ---- header (mirrors siders_pages/header.php) ----
function renderHeader(page){
  const title = page.replace(/_/g," ").replace(/\b\w/g,c=>c.toUpperCase());
  $("#dashHeader").innerHTML = `<div class="header-left"><button class="menu-toggle" id="menuBtn"><i class="fas fa-bars"></i></button>
  <div class="breadcrumb"><span class="text-muted">Dashboard</span><i class="fas fa-chevron-right mx-2" style="font-size:.7rem"></i><span class="breadcrumb-active">${esc(title)}</span></div></div>
  <div class="header-right"><div class="header-search"><i class="fas fa-search"></i><input id="globalSearch" placeholder="Search records... (filters tables)"></div>
  <a href="#/profile" style="text-decoration:none;color:inherit"><div class="user-profile-widget"><div class="user-info text-right"><p class="name">${esc(me.full_name)}</p><p class="role">${esc(me.role_name)}</p></div><div class="user-avatar-premium">${esc(me.full_name[0])}</div></div></a></div>`;
  $("#menuBtn").onclick=()=>{const sb=$("#sidebar"); sb.classList.toggle("open"); sb.classList.toggle("active");};
  const gs=$("#globalSearch"); if(gs) gs.oninput=e=>{ const q=e.target.value.toLowerCase(); document.querySelectorAll("#pageRoot table tbody tr").forEach(tr=>{ tr.style.display = tr.textContent.toLowerCase().includes(q)?"":"none"; }); };
}

// ================= PAGE RENDERERS (same containers/classes as main_pages/*.php) =================
const R = {};
R.overview = function(){
  const typeCounts = {}; DEMO_GROUPS.forEach(g=>typeCounts[g.type]=(typeCounts[g.type]||0)+1);
  const labels = Object.keys(typeCounts), counts = Object.values(typeCounts);
  const total = financeTx.reduce((s,t)=>s+Number(t.amount),0);
  const feed = [
    ...announcements.slice(0,4).map(a=>({type:"Announcement",description:a.title,created_at:a.created_at})),
    ...financeTx.slice(0,3).map(t=>({type:"Finance",description:t.type+": K"+t.amount,created_at:t.date})),
  ];
  return `<div class="overview-content">
  <div class="stats-grid mb-4">
    <div class="card overview-card" style="background:#fdf2f8"><i class="fas fa-user-shield fa-2x" style="color:#be185d"></i><h3>Management</h3><p>${DEMO_USERS.length} demo users · password REGIMENT.</p><a href="#/users" class="btn btn-outline" style="width:100%">Users</a></div>
    <div class="card overview-card" style="background:#ecfdf5"><i class="fas fa-check-double fa-2x" style="color:#059669"></i><h3>Approvals</h3><p>${announcements.filter(a=>a.status==="pending").length} pending publications.</p><a href="#/announcements" class="btn btn-outline" style="width:100%">Verify</a></div>
    <div class="card overview-card" style="background:#eff6ff"><i class="fas fa-hands-helping fa-2x" style="color:#2563eb"></i><h3>Ministries</h3><p>${DEMO_MINISTRIES.length} ministries · volunteers & join requests.</p><a href="#/ministries" class="btn btn-outline" style="width:100%">Ministries</a></div>
  </div>
  <div class="charts-container mb-4"><div class="card chart-card"><h3>Groups Distribution</h3><div class="chart-wrapper"><canvas id="groupsPieChart"></canvas></div></div>
  <div class="card chart-card"><h3>Revenue (2026) — K${total.toLocaleString()}</h3><div class="chart-wrapper"><canvas id="revenueBarChart"></canvas></div></div></div>
  <div class="card activity-card"><h3>Recent Activity Feed</h3><div class="table-responsive"><table class="activity-table"><thead><tr><th>Date</th><th>Type</th><th>Activity</th></tr></thead><tbody>
  ${feed.map(f=>`<tr><td>${esc(f.created_at)}</td><td><span class="badge badge-${esc(f.type.toLowerCase())}">${esc(f.type)}</span></td><td>${esc(f.description)}</td></tr>`).join('')}
  </tbody></table></div></div></div>
  <script>(function(){ if(!window.Chart) return;
    new Chart(document.getElementById('groupsPieChart'),{type:'doughnut',data:{labels:${JSON.stringify(labels)},datasets:[{data:${JSON.stringify(counts)},backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},cutout:'70%'}});
    new Chart(document.getElementById('revenueBarChart'),{type:'bar',data:{labels:['Offertory','Tithe','Donation','Contribution'],datasets:[{data:[${['offertory','tithe','donation','contribution'].map(t=>financeTx.filter(x=>x.type===t).reduce((s,x)=>s+ +x.amount,0)).join(',')}],backgroundColor:'rgba(59,130,246,.8)',borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true}},plugins:{legend:{display:false}}}});
  })();<\/script>`;
};

R.users = function(){
  const leaders = DEMO_USERS.filter(u=>u.role_level<10).length;
  return `<div class="user-directory-container"><div class="d-flex justify-content-between align-items-center mb-4"><div><h1>User Directory</h1><p class="text-muted">Manage all parish members, leaders, and administration staff.</p></div><div><a href="#/invite_leader" class="btn btn-primary"><i class="fas fa-user-plus mr-2"></i> Invite New Leader</a></div></div>
  <div class="grid grid-cols-4 mb-4" style="gap:1.5rem"><div class="card p-4" style="border-left:4px solid #3b82f6"><small class="text-muted text-uppercase font-weight-bold">Total Users</small><h2>${DEMO_USERS.length}</h2></div>
  <div class="card p-4" style="border-left:4px solid #10b981"><small class="text-muted text-uppercase font-weight-bold">Active Leaders</small><h2>${leaders}</h2></div>
  <div class="card p-4" style="border-left:4px solid #f59e0b"><small class="text-muted text-uppercase font-weight-bold">Parish Groups</small><h2>${DEMO_GROUPS.length}</h2></div>
  <div class="card p-4" style="border-left:4px solid #ef4444"><small class="text-muted text-uppercase font-weight-bold">Demo Password</small><h2 style="font-size:1.1rem">REGIMENT</h2></div></div>
  <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0" style="width:100%"><thead style="background:#f8fafc"><tr><th class="p-4 text-left">User</th><th class="p-4 text-left">Role</th><th class="p-4 text-left">Parish Group</th><th class="p-4 text-left">Phone</th><th class="p-4 text-right">Demo Login</th></tr></thead><tbody>
  ${DEMO_USERS.map(u=>{const ini=u.full_name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();return `<tr style="border-bottom:1px solid #f1f5f9"><td class="p-4"><div class="d-flex align-items-center"><div class="avatar-circle mr-3" style="background:${u.role_level<=2?'#1e3a8a':'#e2e8f0'};color:${u.role_level<=2?'white':'#64748b'}">${ini}</div><div><div class="font-weight-bold">${esc(u.full_name)}</div><small class="text-muted">${esc(u.custom_id)} · ${esc(u.username)}</small></div></div></td><td class="p-4"><span class="badge">${esc(u.role_name)}</span></td><td class="p-4 text-muted">${esc(u.group)}</td><td class="p-4 text-muted">${esc(u.phone)}</td><td class="p-4 text-right"><button class="btn btn-outline btn-sm" data-login="${esc(u.username)}">Login as</button></td></tr>`;}).join('')}
  </tbody></table></div></div></div></div>`;
};

R.groups = R.secretary_groups = function(){
  const labels = {lay_group:"Lay Groups",youth:"Youth Organizations",elder:"Elders/Senior Councils",scc:"Small Christian Communities (SCC)",choir:"Parish Choirs"};
  return `<div class="groups-container"><div class="d-flex justify-content-between align-items-center mb-4"><div><h1>Parish Groups</h1><p class="text-muted">High-level oversight of all organizations, choirs, and communities.</p></div><button class="btn btn-primary" id="newGroupBtn"><i class="fas fa-plus mr-2"></i> Register New Group</button></div>
  <div class="grid grid-cols-2" style="gap:2rem">${Object.entries(labels).map(([t,label])=>{const list=DEMO_GROUPS.filter(g=>g.type===t);return `<div class="card border-0 shadow-sm p-4 mb-4"><div class="d-flex justify-content-between align-items-center mb-3"><h3 class="group-section-title">${label}</h3><span class="badge badge-primary">${list.length} Total</span></div><div class="group-list">${list.length?list.map(g=>`<div class="group-item p-3 border-bottom d-flex align-items-center justify-content-between"><div class="d-flex align-items-center"><div class="group-logo-placeholder mr-3"><i class="fas fa-users"></i></div><div><div class="font-weight-bold">${esc(g.name)}</div><small class="text-muted">${g.member_count} Members</small></div></div></div>`).join(''):'<p class="text-muted">No groups registered in this category.</p>'}</div></div>`;}).join('')}</div></div>`;
};

R.announcements = function(){
  const pending = announcements.filter(a=>a.status==="pending").length;
  return `<div class="announcements-container"><div class="flex mb-4" style="justify-content:space-between;align-items:center"><div><h1>Parish Announcements</h1><p class="text-muted">Review, approve, publish. Published items show on the public Announcements page instantly.</p></div></div>
  <div class="card p-4 mb-4"><h3 class="mb-4">Create announcement</h3><form id="annForm" class="grid" style="grid-template-columns:2fr 1fr;gap:1rem"><input id="annTitle" placeholder="Title" required class="form-control"><select id="annCat" class="form-control"><option>general</option><option>youth</option><option>liturgy</option><option>finance</option></select><textarea id="annBody" rows="3" placeholder="Announcement body..." required class="form-control" style="grid-column:1/-1"></textarea><button class="btn btn-primary" style="grid-column:1/-1">Publish Now</button></form></div>
  <div class="grid mb-4" style="grid-template-columns:1fr 1fr;gap:1rem"><div class="card p-4"><h3>${announcements.length}</h3><small class="text-muted">Total Posted</small></div><div class="card p-4"><h3>${pending}</h3><small class="text-muted">Awaiting Approval</small></div></div>
  <div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="bg-light"><tr><th class="p-4">Announcement</th><th class="p-4">Author</th><th class="p-4">Status</th><th class="p-4">Date</th><th class="p-4 text-right">Actions</th></tr></thead><tbody>
  ${announcements.map((a,i)=>`<tr><td class="p-4"><div style="font-weight:700">${esc(a.title)}</div><small class="text-muted">${esc(a.content.slice(0,80))}...</small></td><td class="p-4">${esc(a.author)}</td><td class="p-4"><span class="badge">${esc(a.status)}</span></td><td class="p-4 text-muted">${esc(a.created_at)}</td><td class="p-4 text-right" style="white-space:nowrap">${a.status!=="published"?`<button class="btn btn-sm btn-success" data-pub="${i}"><i class="fas fa-check"></i></button>`:""}<button class="btn btn-sm btn-light text-danger" data-del="${i}"><i class="fas fa-trash-alt"></i></button></td></tr>`).join('')}
  </tbody></table></div></div></div>`;
};

R.finance = R.treasurer_finance = function(){
  const mOff = financeTx.filter(t=>t.type==="offertory").reduce((s,t)=>s+ +t.amount,0);
  return `<div class="finance-container"><div class="d-flex justify-content-between align-items-center mb-4"><div><h1>Collections & Finance</h1><p class="text-muted">Monitor parish revenue and contributions for the current period.</p></div><div class="btn-group"><button class="btn btn-outline mr-2" onclick="window.print()"><i class="fas fa-file-pdf mr-2"></i> Export Report</button><button class="btn btn-primary" id="recEntry"><i class="fas fa-plus mr-2"></i> Record Entry</button></div></div>
  <div class="grid grid-cols-4 mb-4" style="gap:1.5rem"><div class="card p-4 finance-summary-card"><div class="text-muted small font-weight-bold mb-2">MONTHLY OFFERTORY</div><h2 class="text-primary">K${mOff.toLocaleString(undefined,{minimumFractionDigits:2})}</h2></div><div class="card p-4 finance-summary-card"><div class="text-muted small font-weight-bold mb-2">TITHES (MTD)</div><h2 class="text-success">K4,850.00</h2></div><div class="card p-4 finance-summary-card"><div class="text-muted small font-weight-bold mb-2">ACTIVE PLEDGES</div><h2 class="text-warning">K12,400.00</h2></div><div class="card p-4 finance-summary-card"><div class="text-muted small font-weight-bold mb-2">REVENUE GOAL</div><div class="progress-container"><div class="progress-bar" style="width:65%"></div></div><small class="text-muted">65% of K20,000</small></div></div>
  <div class="card border-0 shadow-sm"><div class="card-header bg-white p-4"><h3 class="mb-0">Recent Transactions</h3></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="bg-light"><tr><th class="p-4">Type</th><th class="p-4">Contributor</th><th class="p-4 text-right">Amount</th><th class="p-4">Date</th><th class="p-4">Description</th></tr></thead><tbody>
  ${financeTx.map(t=>`<tr><td class="p-4"><span class="type-indicator type-${esc(t.type)}"></span>${esc(t.type)}</td><td class="p-4">${esc(t.contributor)}</td><td class="p-4 text-right">K${Number(t.amount).toLocaleString(undefined,{minimumFractionDigits:2})}</td><td class="p-4">${esc(t.date)}</td><td class="p-4 text-muted">${esc(t.description)}</td></tr>`).join('')}
  </tbody></table></div></div></div></div>`;
};

R.events = function(){
  return `<h1>Parish Events</h1><p class="text-muted">Publish Masses, meetings, fundraisers. Shows instantly on public Events page + homepage.</p>
  <div class="card p-4 mb-4"><form id="evForm" class="grid" style="grid-template-columns:2fr 1fr 1fr;gap:1rem"><input id="evTitle" placeholder="e.g. Easter Fundraising Dinner" required class="form-control"><input id="evDate" type="date" required class="form-control"><input id="evVenue" placeholder="Venue" class="form-control" value="Parish Church"><input id="evDesc" placeholder="Short description" class="form-control" style="grid-column:1/-1"><button class="btn btn-primary">Publish Event</button></form></div>
  ${events.map((r,i)=>`<div class="card p-4 mb-2 flex" style="justify-content:space-between;align-items:center"><span><strong>${esc(r.title)}</strong><br><small class="text-muted">${esc(r.date)} · ${esc(r.venue)} · ${esc(r.status)}</small></span><span><button class="btn btn-outline" data-evdel="${i}" style="padding:.4rem .8rem">Delete</button></span></div>`).join('')}`;
};

R.bulletins = function(){
  return `<h1>Sunday Bulletins</h1><p class="text-muted">Upload and share weekly bulletins (demo list mirrors bulletins table).</p>${DEMO_BULLETINS.map(b=>`<div class="card p-4 mb-2 flex" style="justify-content:space-between;align-items:center"><span><strong>${esc(b.title)}</strong><br><small class="text-muted">${esc(b.date)} · ${b.pages} pages</small></span><button class="btn btn-primary" onclick="alert('Demo: ${esc(b.title)} would download.')"><i class="fas fa-download"></i> Download</button></div>`).join('')}`;
};

R.live_updates = function(){
  return `<h1>Live Updates</h1><p class="text-muted">Pinned + published updates show on the homepage hero instantly.</p><div class="card p-4 mb-4"><form id="liveForm" class="grid" style="gap:1rem"><input id="liveMsg" class="form-control" placeholder="Type urgent update..." required><button class="btn btn-primary">Publish Update</button></form></div>${live.map((l,i)=>`<div class="card p-4 mb-2"><small class="text-muted">${esc(l.created_at)}</small><p style="font-weight:600">${esc(l.message)}</p><button class="btn btn-outline btn-sm" data-livedel="${i}">Remove</button></div>`).join('')}`;
};

R.ministries = function(){
  return `<h1>Ministries</h1><p class="text-muted">Volunteers & join requests.</p><div class="grid grid-cols-2" style="gap:1.5rem">${DEMO_MINISTRIES.map(m=>`<div class="card p-4"><h3>${esc(m.name)}</h3><p class="text-muted">${m.volunteers} volunteers · ${m.pending} pending requests</p><button class="btn btn-outline" onclick="alert('Demo: opening ${esc(m.name)} requests.')">Review Requests</button></div>`).join('')}</div>`;
};

R.giving = function(){
  return `<h1>Giving Verify</h1><p class="text-muted">MTN / Airtel MoMo to 0975255734 — verify Txn IDs below.</p><div class="card"><div class="table-responsive"><table class="table"><thead><tr><th class="p-4">Phone</th><th class="p-4 text-right">Amount</th><th class="p-4">Txn ID</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr></thead><tbody>${giving.map((g,i)=>`<tr><td class="p-4">${esc(g.phone)}</td><td class="p-4 text-right">K${g.amount}</td><td class="p-4">${esc(g.txn)}</td><td class="p-4"><span class="badge">${esc(g.status)}</span></td><td class="p-4 text-right">${g.status==="pending"?`<button class="btn btn-sm btn-success" data-verify="${i}">Verify</button>`:"✓"}</td></tr>`).join('')}</tbody></table></div></div>`;
};

R.roq = function(){
  return `<h1>Procurement (ROQ)</h1><p class="text-muted">Open quotations, deadlines and offers.</p>${DEMO_ROQ.map(q=>`<div class="card p-4 mb-2 flex" style="justify-content:space-between;align-items:center"><span><strong>${esc(q.title)}</strong><br><small class="text-muted">Deadline ${esc(q.deadline)} · ${q.offers} offers · ${esc(q.status)}</small></span><a href="#/roq_submissions" class="btn btn-outline">View Offers</a></div>`).join('')}<p style="margin-top:1rem"><a href="#/roq_submit" class="btn btn-primary">+ New ROQ</a></p>`;
};
R.roq_submit = function(){ return `<h1>New Request for Quotation</h1><p class="text-muted">Publish a procurement request (demo saves to this browser).</p><div class="card p-4"><form id="roqForm" class="grid" style="gap:1rem"><input id="roqTitle" class="form-control" placeholder="e.g. Plastic Chairs (200 pcs)" required><input id="roqDead" type="date" class="form-control" required><textarea id="roqDesc" class="form-control" rows="3" placeholder="Specifications..."></textarea><button class="btn btn-primary">Publish ROQ</button></form></div>`; };
R.roq_submissions = function(){ return `<h1>ROQ Offers</h1><p class="text-muted">Supplier bids awaiting award.</p><div class="card"><div class="table-responsive"><table class="table"><thead><tr><th class="p-4">ROQ</th><th class="p-4">Supplier</th><th class="p-4 text-right">Price</th><th class="p-4 text-right">Action</th></tr></thead><tbody><tr><td class="p-4">Church Roof Sheets</td><td class="p-4">Chilanga Hardware</td><td class="p-4 text-right">K18,500</td><td class="p-4 text-right"><button class="btn btn-sm btn-success" onclick="alert('Demo: offer awarded.')")">Award</button></td></tr><tr><td class="p-4">Plastic Chairs</td><td class="p-4">Kamwala Traders</td><td class="p-4 text-right">K9,800</td><td class="p-4 text-right"><button class="btn btn-sm btn-success" onclick="alert('Demo: offer awarded.')">Award</button></td></tr></tbody></table></div></div>`; };

R.secretary_rosters = function(){
  return `<h1>Plan Rosters</h1><p class="text-muted">Sunday Mass, cleaning and readers rota.</p><div class="card"><div class="table-responsive"><table class="table"><thead><tr><th class="p-4">Week</th><th class="p-4">Sunday Mass</th><th class="p-4">Cleaning</th><th class="p-4">Readers</th></tr></thead><tbody>${DEMO_ROSTERS.map(r=>`<tr><td class="p-4">${esc(r.week)}</td><td class="p-4">${esc(r.sunday_mass)}</td><td class="p-4">${esc(r.cleaning)}</td><td class="p-4">${esc(r.readers)}</td></tr>`).join('')}</tbody></table></div></div>`;
};

R.media = function(){
  return `<h1>Media & Live</h1><p class="text-muted">Facebook & YouTube streams, Mass times and replays.</p><div class="card p-4 mb-4" style="background:#0f172a;color:#fff"><span class="badge" style="background:#ef4444;color:#fff">LIVE (demo)</span><h3 style="color:#fff;margin-top:1rem">Sunday 09:30 Mass</h3><p style="opacity:.8">Stream placeholder — connect Facebook/YouTube embed in PHP version.</p><button class="btn" style="background:#fff" onclick="alert('Demo player')">▶ Watch Replay</button></div>`;
};

R.profile = function(){
  return `<h1>My Profile</h1><p class="text-muted">Account details for this demo login.</p><div class="card p-4"><p><b>${esc(me.full_name)}</b> (${esc(me.username)})</p><p class="text-muted">${esc(me.role_name)} · ${esc(me.group)} · ${esc(me.phone)} · ${esc(me.custom_id)}</p><p>Demo password: <b>REGIMENT</b></p><button class="btn btn-outline" onclick="alert('Demo: profile edit disabled in preview.')">Edit Profile</button></div>`;
};

R.handover = function(){
  return `<div class="handover-container"><div class="page-header mb-4"><h1>Leadership Handover</h1><p class="text-muted">Initiate the transition to your successor. Once confirmed, you remain admin for 30 days before automatic removal (PHP version).</p></div>
  <div class="grid grid-cols-2" style="gap:2rem"><div class="card p-4"><h3 class="mb-4">Invite Successor</h3><form id="handForm"><label>New Leader's Full Name</label><input class="form-control" id="handName" required style="width:100%;margin:.4rem 0 1rem"><label>Email Address</label><input type="email" class="form-control" id="handEmail" required style="width:100%;margin:.4rem 0 1rem"><label>Phone</label><input class="form-control" id="handPhone" style="width:100%;margin:.4rem 0 1rem"><button class="btn btn-primary" style="width:100%">Initiate Handover</button></form></div>
  <div class="card p-4"><h3>How it works</h3><ol style="color:#475569;line-height:2"><li>Your account becomes transitional (30 days).</li><li>Successor account is created with your role.</li><li>You guide them, then access expires automatically.</li></ol><p class="text-muted">Demo: shows success message only.</p></div></div></div>`;
};

R.settings = function(){ return `<h1>Portal Settings</h1><p class="text-muted">Church name, Mass times, MoMo number (demo read-only).</p><div class="card p-4"><p><b>Parish:</b> St. Charles Lwanga Regiment</p><p><b>MoMo:</b> 0975255734 (MTN/Airtel)</p><p><b>Sunday Masses:</b> 07:00 & 09:30</p><button class="btn btn-outline" onclick="alert('Demo: settings saved in PHP version.')">Save (demo)</button></div>`; };
R.invite_leader = function(){ return `<h1>Invite New Leader</h1><p class="text-muted">Activation-code flow from register page (PHP). Demo: copy a code.</p><div class="card p-4"><p>Demo activation code: <b>REG-2026-DEMO</b></p><button class="btn btn-primary" onclick="navigator.clipboard&&navigator.clipboard.writeText('REG-2026-DEMO');alert('Copied!')">Copy Code</button></div>`; };

// ---- router ----
function render(){
  const page = curPage();
  renderSidebar(); renderHeader(page);
  const fn = R[page] || R.overview;
  const root = $("#pageRoot");
  root.innerHTML = fn();
  // run inline <script> (charts)
  root.querySelectorAll("script").forEach(s=>{ const n=document.createElement("script"); n.textContent=s.textContent; document.body.appendChild(n); s.remove(); });
  bindActions();
  $("#logoutTop").onclick=e=>{e.preventDefault();localStorage.removeItem("demo_session");location.href="login.html";};
  window.scrollTo(0,0);
}

function bindActions(){
  document.querySelectorAll("[data-login]").forEach(b=>b.onclick=()=>{ store.set("session",{username:b.dataset.login,at:Date.now()}); location.reload(); });
  const af=$("#annForm"); if(af) af.onsubmit=e=>{e.preventDefault(); announcements.unshift({title:$("#annTitle").value,content:$("#annBody").value,author:me.full_name,status:"published",created_at:"Just now"}); persist(); render();};
  document.querySelectorAll("[data-pub]").forEach(b=>b.onclick=()=>{ announcements[+b.dataset.pub].status="published"; persist(); render(); });
  document.querySelectorAll("[data-del]").forEach(b=>b.onclick=()=>{ if(confirm("Delete?")){ announcements.splice(+b.dataset.del,1); persist(); render(); } });
  const rec=$("#recEntry"); if(rec) rec.onclick=()=>{ const a=prompt("Amount (K)?","500"); if(a){ financeTx.unshift({type:"offertory",contributor:me.full_name,amount:parseFloat(a)||0,date:"2026-09-27",description:"Manual entry (demo)"}); persist(); render(); } };
  const ef=$("#evForm"); if(ef) ef.onsubmit=e=>{e.preventDefault(); events.unshift({title:$("#evTitle").value,date:$("#evDate").value,venue:$("#evVenue").value||"Parish Church",status:"scheduled"}); persist(); render();};
  document.querySelectorAll("[data-evdel]").forEach(b=>b.onclick=()=>{ events.splice(+b.dataset.evdel,1); persist(); render(); });
  const lf=$("#liveForm"); if(lf) lf.onsubmit=e=>{e.preventDefault(); live.unshift({message:$("#liveMsg").value,created_at:"Just now"}); persist(); render();};
  document.querySelectorAll("[data-livedel]").forEach(b=>b.onclick=()=>{ live.splice(+b.dataset.livedel,1); persist(); render(); });
  document.querySelectorAll("[data-verify]").forEach(b=>b.onclick=()=>{ giving[+b.dataset.verify].status="verified"; persist(); render(); });
  const qf=$("#roqForm"); if(qf) qf.onsubmit=e=>{e.preventDefault(); alert("Demo: ROQ '"+$("#roqTitle").value+"' published (browser only)."); location.hash="#/roq";};
  const hf=$("#handForm"); if(hf) hf.onsubmit=e=>{e.preventDefault(); $("#pageRoot").innerHTML=`<div class="card" style="background:#ecfdf5;padding:2rem;text-align:center"><i class="fas fa-handshake fa-4x mb-4"></i><h2>Success!</h2><p>Handover initiated for ${esc($("#handName").value)} (demo — no email sent).</p><a href="#/overview" class="btn btn-primary">Back to Overview</a></div>`;};
  const ng=$("#newGroupBtn"); if(ng) ng.onclick=()=>alert("Demo: Group Registry opens in PHP version.");
}
window.addEventListener("hashchange", render);
render();
})();
