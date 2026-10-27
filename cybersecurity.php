<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Cybersecurity case: SQL injection – Mark Nantiza</title>
<meta name="description" content="A basic case study of SQL injection: what it is, how it happens, the risks, and how developers prevent it.">
<meta name="theme-color" content="#f5f0e6">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=IBM+Plex+Mono:wght@400;500&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
<script>try{var t=localStorage.getItem("theme");if(t)document.documentElement.setAttribute("data-theme",t)}catch(e){}</script>
<link rel="stylesheet" href="site.css">
</head>
<body>
<canvas id="bg" aria-hidden="true"></canvas>
<header><div class="wrap bar">
<a class="logo" href="index.html">Mark John Ally Nantiza</a>
<nav id="nav" aria-label="Main">
<a href="index.html#about">About</a>
<a href="index.html#skills">Skills</a>
<a href="index.html#projects">Projects</a>
<a href="index.html#education">Education</a>
<a href="index.html#certificates">Certificates</a>
<a href="index.html#evidence" class="active">Evidence</a>
<a href="resume.html">Resume</a>
<a href="index.html#contact">Contact</a>
</nav>
<button class="icon-btn" id="theme" aria-label="Switch light or dark theme">◐</button>
<button class="icon-btn menu-btn" id="menu" aria-label="Open menu" aria-expanded="false">☰</button>
</div><div id="progress"></div></header>

<main id="top">
<div class="wrap phead">
<p class="eyebrow" data-r style="--i:0">Step 18 · Cybersecurity case</p>
<h1 data-r style="--i:1">SQL injection: <em>how it happens and how to prevent it</em></h1>
<p class="lead" data-r style="--i:2">A basic case study of one of the most common web vulnerabilities, written from a defensive point of view for developers who build PHP and database applications.</p>
<div class="tabs" data-r style="--i:3"><a class="chip" href="cybersecurity.php" aria-current="page">Cybersecurity</a><a class="chip" href="ai-literacy.php">AI literacy</a><a class="chip" href="technical-interview.php">Technical interview</a><a class="chip" href="mock-interview.php">Mock interview</a></div>
</div>

<section><div class="wrap">
<p class="eyebrow">01 · The case</p>
<h2>Five questions, answered</h2>
<div class="note"><b>Scope.</b> This case study is about understanding and defence. The demo below never connects to a database, and security testing should only be done on systems I own or have written permission to test.</div>

<article class="q"><span class="n">01</span><div>
<h3>What is the vulnerability?</h3>
<p>SQL injection (SQLi) happens when an application builds a database query by joining user input directly into the SQL text. The database cannot tell which part is the developer's code and which part is the user's data, so a crafted input can change what the query does.</p>
<p>It belongs to the Injection category of the OWASP Top 10, a widely used list of the most serious web application risks.</p>
</div></article>

<article class="q"><span class="n">02</span><div>
<h3>How can it happen?</h3>
<p>It usually starts with a login form, search box, or URL parameter whose value is placed straight into a query. This is the classic mistake in PHP:</p>
<pre class="bad"><code>// Vulnerable: input is joined into the SQL text
$sql = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "' AND password = '" . $_POST['password'] . "'";
$result = mysqli_query($conn, $sql);</code></pre>
<p>If the username contains a single quote, the quote closes the text early and the rest of the input is read as SQL. Try it below. Nothing here touches a database; it only shows how the query text changes.</p>
<div class="sim">
<label class="lbl" for="simIn" style="margin-top:0">Type a username</label>
<input id="simIn" type="text" value="mark" autocomplete="off" spellcheck="false">
<div class="sim-row">
<button class="chip" type="button" data-fill="mark">Normal name</button>
<button class="chip" type="button" data-fill="x' OR '1'='1">Text with a quote</button>
</div>
<div class="cmp">
<div><p class="lbl">Vulnerable query (string joined)</p><pre class="bad" id="simVuln" aria-live="polite"></pre></div>
<div><p class="lbl">Safe query (placeholders)</p><pre class="good" id="simSafe" aria-live="polite"></pre></div>
</div>
<p class="verdict" id="simMsg"></p>
</div>
<p>Attackers use this to skip a login check, read data from other tables, or change and delete records.</p>
</div></article>

<article class="q"><span class="n">03</span><div>
<h3>What are the risks?</h3>
<ul>
<li><b>Unauthorised access:</b> bypassing a login and reaching accounts or admin pages.</li>
<li><b>Data exposure:</b> customer details, order history, and password hashes can be read.</li>
<li><b>Data loss or tampering:</b> records can be changed or deleted, so the data can no longer be trusted.</li>
<li><b>Wider compromise:</b> stolen credentials and server details can be used to attack other systems.</li>
<li><b>Business and legal impact:</b> downtime, lost customer trust, and duties under the Data Privacy Act of 2012 (RA 10173) when personal data is exposed.</li>
</ul>
<p>For any online shop, including projects like Lunar Moon Jewelry, customer and order records are exactly what would be at risk.</p>
</div></article>

<article class="q"><span class="n">04</span><div>
<h3>How can developers prevent it?</h3>
<p>The main fix is to keep input as data by using prepared statements (parameterised queries):</p>
<pre class="good"><code>// Safer: placeholders keep input as data, not SQL
$stmt = $pdo-&gt;prepare("SELECT id, password_hash FROM users WHERE username = :u");
$stmt-&gt;execute([':u' =&gt; $_POST['username']]);
$user = $stmt-&gt;fetch();
if ($user &amp;&amp; password_verify($_POST['password'], $user['password_hash'])) {
    // log the user in
}</code></pre>
<ul>
<li>Use prepared statements with PDO or MySQLi for every query that includes input.</li>
<li>Validate input on the server: type, length, format, and allowed values.</li>
<li>Store passwords with <code>password_hash()</code> and check them with <code>password_verify()</code>.</li>
<li>Give the database account only the permissions the app needs.</li>
<li>Show generic error messages to users and keep detailed errors in server logs.</li>
</ul>
</div></article>

<article class="q"><span class="n">05</span><div>
<h3>What security practices should be implemented?</h3>
<ul>
<li><b>Defence in depth:</b> combine validation, prepared statements, least privilege, and monitoring so one failure is not enough.</li>
<li><b>Code review:</b> check every place user input reaches a query.</li>
<li><b>Keep software updated:</b> PHP, the database, frameworks, and libraries.</li>
<li><b>Protect traffic and sessions:</b> HTTPS and secure cookies (HttpOnly, Secure, SameSite).</li>
<li><b>Log and monitor:</b> watch for repeated failures and unusual requests.</li>
<li><b>Back up data</b> regularly and test the restore.</li>
<li><b>Test responsibly:</b> only on my own projects or with permission.</li>
</ul>
<p>Seminars such as <i>AI in Cybersecurity</i> (August 8, 2026) and <i>Digital Fortify</i> (November 24, 2023) helped me see why these habits matter.</p>
</div></article>
</div></section>

<section><div class="wrap">
<p class="eyebrow">02 · Takeaway</p>
<h2>Lesson learned</h2>
<p class="sub">Never trust input. Treat everything that comes from a user as data, and let the database driver handle it with placeholders.</p>
<p class="lbl">Review checklist for my own PHP projects</p>
<label class="chk"><input type="checkbox"><span>Every query that uses input is a prepared statement</span></label>
<label class="chk"><input type="checkbox"><span>Passwords are hashed with password_hash()</span></label>
<label class="chk"><input type="checkbox"><span>The database account has only the permissions it needs</span></label>
<label class="chk"><input type="checkbox"><span>Users see generic errors, and details go to the log</span></label>
<label class="chk"><input type="checkbox"><span>Input is validated on the server, not only in the browser</span></label>
<p class="chk-count" id="chkCount"></p>
</div></section>

<section><div class="wrap"><div class="pn">
<a class="btn" href="index.html#evidence">← Back to portfolio</a>
<a class="btn main" href="ai-literacy.php">Step 19 · AI literacy →</a>
</div></div></section>
</main>

<footer><div class="wrap">© <span id="yr">2026</span> Mark Nantiza. Built with HTML, CSS, JavaScript and PHP.</div></footer>
<button class="icon-btn top" id="top-btn" aria-label="Back to top">↑</button>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script src="site.js"></script>
<script>document.getElementById("yr").textContent=new Date().getFullYear();</script>
<script>
(function(){
  var $=function(id){return document.getElementById(id)};
  var inp=$("simIn"),vuln=$("simVuln"),safe=$("simSafe"),msg=$("simMsg");
  function run(){
    var v=inp.value,s=document.createElement("span");
    vuln.textContent="";
    vuln.append("SELECT * FROM users\nWHERE username = '");
    s.className="hl";s.textContent=v;vuln.append(s);
    vuln.append("' AND password = '••••••';");
    safe.textContent="SELECT * FROM users\nWHERE username = ? AND password = ?;\n\nbound as plain data: "+v;
    msg.textContent=v.indexOf("'")>-1
      ?"The quote closes the text early, so the rest of the input is read as SQL and the query's logic can change. With placeholders the same input stays plain data."
      :"Plain text stays inside the quotes, so both versions behave as intended. The danger starts with characters such as a single quote.";
  }
  inp.addEventListener("input",run);
  [].forEach.call(document.querySelectorAll("[data-fill]"),function(b){b.addEventListener("click",function(){inp.value=b.dataset.fill;run();inp.focus()})});
  run();
  var boxes=[].slice.call(document.querySelectorAll(".chk input")),cnt=$("chkCount");
  function upd(){cnt.textContent=boxes.filter(function(b){return b.checked}).length+" / "+boxes.length+" reviewed"}
  boxes.forEach(function(b){b.addEventListener("change",upd)});upd();
})();
</script>
</body>
</html>
