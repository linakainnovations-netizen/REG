/* St. Charles Lwanga Regiment Portal — STATIC DEMO DATA
 * Mirrors the real MySQL schema (users, roles, groups, announcements, finance...)
 * Password for ALL demo accounts: REGIMENT
 */
const DEMO_PASSWORD = "REGIMENT";

const DEMO_USERS = [
  { username: "super.admin",      full_name: "Super Administrator",   role_name: "Super Admin",                role_level: 1,  group: "General Parish",        phone: "0975000001", custom_id: "SCLR0001" },
  { username: "parish.priest",    full_name: "Fr. Emmanuel Mwanza",    role_name: "Parish Priest",              role_level: 2,  group: "General Parish",        phone: "0975000002", custom_id: "SCLR0002" },
  { username: "council.chair",    full_name: "Mary Banda",             role_name: "Parish Council Chairperson", role_level: 3,  group: "Parish Council",        phone: "0975000003", custom_id: "SCLR0003" },
  { username: "council.secretary",full_name: "Joseph Phiri",           role_name: "Parish Council Secretary",   role_level: 3,  group: "Parish Council",        phone: "0975000004", custom_id: "SCLR0004" },
  { username: "council.treasurer",full_name: "Grace Mutale",           role_name: "Parish Council Treasurer",   role_level: 3,  group: "Parish Council",        phone: "0975000005", custom_id: "SCLR0005" },
  { username: "youth.chair",      full_name: "Peter Simfukwe",         role_name: "Youth Office Chairperson",   role_level: 4,  group: "Youth Ministry",        phone: "0975000006", custom_id: "SCLR0006" },
  { username: "lay.leader",       full_name: "Agnes Mwila",            role_name: "Lay Group Leader",           role_level: 5,  group: "St. Anna Group",        phone: "0975000007", custom_id: "SCLR0007" },
  { username: "choir.leader",     full_name: "David Chanda",           role_name: "Choir Leader",               role_level: 5,  group: "St. Cecilia Choir",     phone: "0975000008", custom_id: "SCLR0008" },
  { username: "scc.leader",       full_name: "Rose Tembo",             role_name: "SCC Leader",                 role_level: 7,  group: "SCC St. Monica Zone",   phone: "0975000009", custom_id: "SCLR0009" },
  { username: "member.john",      full_name: "John Daka",              role_name: "Member",                     role_level: 10, group: "General Parish",        phone: "0975000010", custom_id: "SCLR0010" },
];

const DEMO_GROUPS = [
  { name: "St. Anna Group",        type: "lay_group", member_count: 45 },
  { name: "Catholic Women's League", type: "lay_group", member_count: 62 },
  { name: "St. Joseph Men's Group",  type: "lay_group", member_count: 38 },
  { name: "Youth Ministry (CYO)",  type: "youth",     member_count: 120 },
  { name: "Young Christian Workers", type: "youth",   member_count: 34 },
  { name: "Elders Council",        type: "elder",     member_count: 22 },
  { name: "SCC St. Monica Zone",   type: "scc",       member_count: 28 },
  { name: "SCC St. Francis Zone",  type: "scc",       member_count: 31 },
  { name: "SCC St. Theresa Zone",  type: "scc",       member_count: 25 },
  { name: "St. Cecilia Choir",     type: "choir",     member_count: 40 },
  { name: "Youth Choir",           type: "choir",     member_count: 35 },
  { name: "St. Michael Choir",     type: "choir",     member_count: 29 },
];

const DEMO_ANNOUNCEMENTS = [
  { title: "Sunday Mass Schedule Change", content: "Second Mass moves to 09:30 starting next Sunday to accommodate confirmation classes.", author: "Fr. Emmanuel Mwanza", status: "published", created_at: "Sep 25, 09:00" },
  { title: "Youth Choir Rehearsal", content: "All youth choir members to meet Saturday 14:00 in the parish hall. New members welcome.", author: "David Chanda", status: "published", created_at: "Sep 24, 16:20" },
  { title: "Parish Fundraising Dinner", content: "Tickets K150. Proceeds go to church roof repairs. See treasurer to purchase.", author: "Grace Mutale", status: "pending", created_at: "Sep 24, 10:05" },
  { title: "SCC Leaders Meeting", content: "All SCC leaders meet Friday 17:30 at the presbytery. Agenda: Lenten program.", author: "Rose Tembo", status: "pending", created_at: "Sep 23, 14:44" },
  { title: "Baptism Registration Open", content: "Parents to register infants at the parish office before month end with birth records.", author: "Joseph Phiri", status: "published", created_at: "Sep 22, 11:12" },
];

const DEMO_FINANCE = [
  { type: "offertory", contributor: "Sunday 1st Mass", amount: 4850.00, date: "2026-09-20", description: "Sunday offertory collection" },
  { type: "offertory", contributor: "Sunday 2nd Mass", amount: 3920.50, date: "2026-09-20", description: "Sunday offertory collection" },
  { type: "tithe", contributor: "Anonymous", amount: 1500.00, date: "2026-09-18", description: "Monthly tithe" },
  { type: "donation", contributor: "Mary Banda", amount: 2000.00, date: "2026-09-15", description: "Roof repair donation" },
  { type: "contribution", contributor: "Youth Ministry", amount: 850.00, date: "2026-09-12", description: "Fundraising contribution" },
  { type: "offertory", contributor: "Sunday 1st Mass", amount: 4610.00, date: "2026-09-13", description: "Sunday offertory collection" },
  { type: "donation", contributor: "Anonymous", amount: 500.00, date: "2026-09-10", description: "General donation" },
];

const DEMO_EVENTS = [
  { title: "Easter Fundraising Dinner", date: "2026-10-10 17:00", venue: "Parish Hall", status: "scheduled" },
  { title: "Confirmation Classes Begin", date: "2026-10-04 09:00", venue: "Catechism Block", status: "scheduled" },
  { title: "SCC Leaders Retreat", date: "2026-10-17 08:00", venue: "Presbytery", status: "scheduled" },
  { title: "Youth Sports Day", date: "2026-09-15 09:00", venue: "Parish Grounds", status: "completed" },
];

const DEMO_BULLETINS = [
  { title: "26th Sunday Ordinary Time", date: "Sep 27, 2026", pages: 4 },
  { title: "25th Sunday Ordinary Time", date: "Sep 20, 2026", pages: 4 },
  { title: "24th Sunday Ordinary Time", date: "Sep 13, 2026", pages: 6 },
];

const DEMO_ROQ = [
  { title: "Church Roof Sheets (120 pcs)", deadline: "2026-10-15", status: "open", offers: 4 },
  { title: "Plastic Chairs (200 pcs)", deadline: "2026-10-05", status: "open", offers: 6 },
  { title: "Sound System Repair", deadline: "2026-09-20", status: "awarded", offers: 3 },
];

const DEMO_GIVING = [
  { phone: "0977XXXX21", amount: 200.00, txn: "MTN99182736", status: "verified", date: "Sep 26" },
  { phone: "0975XXXX88", amount: 150.00, txn: "ATL55201983", status: "pending", date: "Sep 26" },
  { phone: "0966XXXX45", amount: 500.00, txn: "MTN99182011", status: "verified", date: "Sep 25" },
  { phone: "0971XXXX09", amount: 100.00, txn: "ATL55199872", status: "pending", date: "Sep 25" },
];

const DEMO_LIVE = [
  { message: "Sunday 09:30 Mass streaming live on Facebook. Join us!", created_at: "Today 08:55" },
  { message: "Parish Council meeting minutes published. See notice board.", created_at: "Yesterday 17:20" },
];

const DEMO_ROSTERS = [
  { week: "Oct 04 – Oct 10", sunday_mass: "St. Cecilia Choir", cleaning: "SCC St. Monica", readers: "Youth Ministry" },
  { week: "Oct 11 – Oct 17", sunday_mass: "Youth Choir", cleaning: "St. Anna Group", readers: "CWL" },
];

const DEMO_MINISTRIES = [
  { name: "Lectors & Readers", volunteers: 24, pending: 3 },
  { name: "Altar Servers", volunteers: 30, pending: 5 },
  { name: "Ushers & Welcomers", volunteers: 18, pending: 2 },
  { name: "Catechism Teachers", volunteers: 12, pending: 4 },
];

// Role -> dashboard folder + landing page (mirrors dashboard/index.php $roleFolders)
function demoRoleFor(username) {
  const u = DEMO_USERS.find(x => x.username === username);
  if (!u) return null;
  const lvl = u.role_level;
  let folder = "members";
  if (lvl === 1 || lvl === 2) folder = "admin_priest";
  else if (lvl === 3) folder = "parish_council";
  else if (lvl === 4) folder = "youth_council";
  else if (lvl === 5 && /choir/i.test(u.role_name)) folder = "choirs";
  else if (lvl === 5) folder = "lay_groups";
  else if (lvl === 7) folder = "SCC_zonez";
  return { ...u, folder };
}
