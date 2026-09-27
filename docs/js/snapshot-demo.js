/* Static-preview helper injected into snapshotted PHP pages.
 * Frontend is 100% the real markup/CSS. Only backend calls are stubbed:
 *  - login form (password REGIMENT) -> dashboard.html
 *  - every other POST/form -> friendly "needs PHP backend" toast
 */
(function () {
  "use strict";
  function toast(msg) {
    var t = document.getElementById("demo-toast");
    if (!t) { alert(msg); return; }
    t.textContent = msg;
    t.style.display = "block";
    clearTimeout(t._h);
    t._h = setTimeout(function () { t.style.display = "none"; }, 3200);
  }

  // Demo accounts panel
  var link = document.getElementById("demo-accts");
  var panel = document.getElementById("demo-panel");
  if (link && panel) {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      if (panel.style.display === "block") { panel.style.display = "none"; return; }
      var users = (typeof DEMO_USERS !== "undefined") ? DEMO_USERS : [];
      panel.innerHTML = "<b>Demo accounts</b><br><small>Password for all: <b>REGIMENT</b></small><br><br>" +
        users.map(function (u) {
          return '<button data-u="' + u.username + '" style="display:block;width:100%;text-align:left;margin-bottom:.4rem;padding:.5rem .6rem;border:1px solid #cbd5e1;background:#f1f5f9;border-radius:.5rem;cursor:pointer;font-size:.8rem;"><b>' +
            u.username + '</b><br><small>' + u.role_name + '</small></button>';
        }).join("");
      panel.style.display = "block";
      panel.querySelectorAll("button").forEach(function (b) {
        b.addEventListener("click", function () {
          try { localStorage.setItem("demo_session", JSON.stringify({ username: b.getAttribute("data-u"), at: Date.now() })); } catch (e) {}
          location.href = "dashboard.html";
        });
      });
    });
  }

  // Login form -> dashboard (mirrors backend/auth_logic.php handleLogin, password REGIMENT)
  // Only forms that actually carry a login_id field count as login forms;
  // register / forgot-password forms fall through to the generic notice below.
  document.querySelectorAll("form").forEach(function (f) {
    if (!f.querySelector('[name="login_id"]')) return;
    f._demoIsLogin = true;
    f.addEventListener("submit", function (e) {
      e.preventDefault();
      var idEl = f.querySelector('[name="login_id"]');
      var pwEl = f.querySelector('[name="password"]');
      var id = (idEl && idEl.value || "").trim().toLowerCase();
      var pw = (pwEl && pwEl.value || "");
      var users = (typeof DEMO_USERS !== "undefined") ? DEMO_USERS : [];
      var u = users.find(function (x) { return x.username.toLowerCase() === id; });
      if (!u) { toast("Invalid username — click 'demo accounts' above and use one of those."); return; }
      if (pw !== "REGIMENT") { toast("Wrong password — demo password is REGIMENT for all users."); return; }
      try { localStorage.setItem("demo_session", JSON.stringify({ username: u.username, at: Date.now() })); } catch (e) {}
      location.href = "dashboard.html";
    });
  });

  // Every other backend form -> demo notice (frontend unchanged, backend stripped)
  document.querySelectorAll("form").forEach(function (f) {
    if (f._demoIsLogin) return;
    f.addEventListener("submit", function (e) {
      e.preventDefault();
      toast("Static preview: this action needs the PHP + MySQL backend. Login pages fully work with REGIMENT.");
    });
  });
})();
