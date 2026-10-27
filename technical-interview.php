<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Technical interview preparation – Mark Nantiza</title>
<meta name="description" content="A study guide covering programming, databases, web development, networking, cybersecurity, and engineering fundamentals, with practical problem-solving examples.">
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
<p class="eyebrow" data-r style="--i:0">Step 20 · Technical interview preparation</p>
<h1 data-r style="--i:1">Technical interview <em>study guide</em></h1>
<p class="lead" data-r style="--i:2">Short answers to the questions I expect across six areas: programming, database, web development, networking, cybersecurity, and engineering fundamentals, followed by practical problem-solving examples.</p>
<div class="tabs" data-r style="--i:3"><a class="chip" href="cybersecurity.php">Cybersecurity</a><a class="chip" href="ai-literacy.php">AI literacy</a><a class="chip" href="technical-interview.php" aria-current="page">Technical interview</a><a class="chip" href="mock-interview.php">Mock interview</a></div>
</div>

<section><div class="wrap">
<p class="eyebrow">01 · Study guide</p>
<h2>Questions by topic</h2>
<p class="sub">Filter by topic or search, then open a question to read my short answer.</p>
<div class="tools">
<div class="chips-row" role="group" aria-label="Filter by topic">
<button class="chip" type="button" data-t="all" aria-pressed="true">All</button>
<button class="chip" type="button" data-t="programming" aria-pressed="false">Programming</button>
<button class="chip" type="button" data-t="database" aria-pressed="false">Database</button>
<button class="chip" type="button" data-t="web" aria-pressed="false">Web</button>
<button class="chip" type="button" data-t="networking" aria-pressed="false">Networking</button>
<button class="chip" type="button" data-t="security" aria-pressed="false">Cybersecurity</button>
<button class="chip" type="button" data-t="engineering" aria-pressed="false">Engineering</button>
</div>
<label class="srch"><span class="sr">Search questions</span><input id="q" type="search" placeholder="Search questions" autocomplete="off"></label>
<button class="btn" type="button" id="toggleAll">Expand all</button>
</div>
<div id="list">
<details class="qa" data-t="programming"><summary><span class="tag">Programming</span><h3>What is the difference between a compiled and an interpreted language?</h3></summary><div class="qa-body"><p>A compiled language (such as C) is translated into machine code before it runs, which usually means faster execution and some errors caught early. An interpreted language (such as Python or JavaScript) is run line by line by an interpreter, which makes testing quick but can be slower. Many languages mix both ideas: Java compiles to bytecode, which the Java Virtual Machine then runs.</p></div></details>
<details class="qa" data-t="programming"><summary><span class="tag">Programming</span><h3>What are the four pillars of object-oriented programming?</h3></summary><div class="qa-body"><p><b>Encapsulation</b> keeps data and the methods that use it together and hides internal details. <b>Abstraction</b> exposes only what is needed. <b>Inheritance</b> lets a class reuse behaviour from a parent class. <b>Polymorphism</b> lets the same method name behave differently depending on the object.</p></div></details>
<details class="qa" data-t="programming"><summary><span class="tag">Programming</span><h3>What is the difference between an array and a linked list, and between a stack and a queue?</h3></summary><div class="qa-body"><p>An array stores items side by side, so access by index is fast but inserting in the middle is slow. A linked list stores nodes that point to the next one, so inserting is fast but lookup is slow. A stack is last in, first out (like undo history). A queue is first in, first out (like print jobs).</p></div></details>
<details class="qa" data-t="programming"><summary><span class="tag">Programming</span><h3>What is Big-O notation?</h3></summary><div class="qa-body"><p>It describes how the running time or memory of an algorithm grows as the input grows. O(1) is constant time, O(log n) is like binary search, O(n) is one pass through a list, and O(n²) is nested loops. I use it to compare solutions, not to measure exact time.</p></div></details>
<details class="qa" data-t="database"><summary><span class="tag">Database</span><h3>What is the difference between a primary key and a foreign key?</h3></summary><div class="qa-body"><p>A primary key uniquely identifies each row in a table, such as <code>product_id</code>. A foreign key is a column that points to a primary key in another table, such as <code>orders.customer_id</code> pointing to <code>customers.id</code>. It links the tables and keeps the relationship valid.</p></div></details>
<details class="qa" data-t="database"><summary><span class="tag">Database</span><h3>What is normalization?</h3></summary><div class="qa-body"><p>Normalization organises tables to reduce duplicate data and update problems. First normal form (1NF) means each column holds a single value. Second normal form (2NF) removes dependence on only part of a composite key. Third normal form (3NF) removes dependence between non-key columns. The result is smaller tables linked by keys.</p></div></details>
<details class="qa" data-t="database"><summary><span class="tag">Database</span><h3>What is the difference between INNER JOIN and LEFT JOIN?</h3></summary><div class="qa-body"><p>INNER JOIN returns only rows that have a match in both tables. LEFT JOIN returns every row from the left table and fills the right side with NULL when there is no match, for example customers who have not placed an order yet.</p></div></details>
<details class="qa" data-t="database"><summary><span class="tag">Database</span><h3>What does ACID mean?</h3></summary><div class="qa-body"><p><b>Atomicity:</b> a transaction happens fully or not at all. <b>Consistency:</b> rules stay valid. <b>Isolation:</b> transactions running together do not disturb each other. <b>Durability:</b> committed data survives a crash. In a shop, saving the order and reducing the stock should be one transaction.</p></div></details>
<details class="qa" data-t="web"><summary><span class="tag">Web development</span><h3>What happens when I type a URL and press Enter?</h3></summary><div class="qa-body"><p>The browser finds the server's IP address through DNS, opens a TCP connection (with TLS for HTTPS), and sends an HTTP request. The server replies with HTML, and the browser parses it, loads the CSS, JavaScript, and images it refers to, and renders the page.</p></div></details>
<details class="qa" data-t="web"><summary><span class="tag">Web development</span><h3>What is the difference between GET and POST?</h3></summary><div class="qa-body"><p>GET asks for data and puts parameters in the URL, so it can be bookmarked and cached and should not change anything on the server. POST sends data in the request body, which is used for forms and logins and for creating or changing data.</p></div></details>
<details class="qa" data-t="web"><summary><span class="tag">Web development</span><h3>What is the difference between client-side and server-side code?</h3></summary><div class="qa-body"><p>Client-side code (HTML, CSS, JavaScript) runs in the browser and handles display and interaction. Server-side code (PHP, Node.js) runs on the server and handles data, authentication, and business rules. I never rely on client-side validation alone, because users can bypass it.</p></div></details>
<details class="qa" data-t="web"><summary><span class="tag">Web development</span><h3>What is responsive design?</h3></summary><div class="qa-body"><p>A layout that adapts to the screen size using flexible units, CSS Grid or Flexbox, media queries, and the viewport meta tag. For example, this portfolio switches to a single column on phones.</p></div></details>
<details class="qa" data-t="networking"><summary><span class="tag">Networking</span><h3>What is the difference between the OSI model and TCP/IP?</h3></summary><div class="qa-body"><p>OSI has seven layers: physical, data link, network, transport, session, presentation, and application. TCP/IP simplifies this to four: link, internet, transport, and application. The layers help locate a fault, such as a cable (physical), an IP setting (network), or a web server (application).</p></div></details>
<details class="qa" data-t="networking"><summary><span class="tag">Networking</span><h3>What is the difference between an IP address and a MAC address?</h3></summary><div class="qa-body"><p>A MAC address is the hardware address of a network card and is used inside a local network. An IP address is a logical address given by the network and used to route traffic between networks. IPv4 uses 32 bits and IPv6 uses 128 bits.</p></div></details>
<details class="qa" data-t="networking"><summary><span class="tag">Networking</span><h3>What do DHCP and DNS do?</h3></summary><div class="qa-body"><p>DHCP automatically gives a device its IP address, subnet mask, default gateway, and DNS server. DNS translates names such as example.com into IP addresses so people do not need to remember numbers.</p></div></details>
<details class="qa" data-t="networking"><summary><span class="tag">Networking</span><h3>What does a /24 subnet mean, and how is a router different from a switch?</h3></summary><div class="qa-body"><p>/24 means the first 24 bits are the network part, so the mask is 255.255.255.0 with 256 addresses and 254 usable hosts (the network and broadcast addresses are reserved). A switch connects devices inside one network using MAC addresses. A router connects different networks using IP addresses.</p></div></details>
<details class="qa" data-t="security"><summary><span class="tag">Cybersecurity</span><h3>What is the CIA triad?</h3></summary><div class="qa-body"><p><b>Confidentiality:</b> only authorised people can see the data. <b>Integrity:</b> data stays accurate and is not changed without permission. <b>Availability:</b> systems and data can be used when needed. Encryption supports confidentiality, hashing and checks support integrity, and backups and redundancy support availability.</p></div></details>
<details class="qa" data-t="security"><summary><span class="tag">Cybersecurity</span><h3>What are some common web vulnerabilities?</h3></summary><div class="qa-body"><p><b>SQL injection:</b> input becomes part of the query. <b>Cross-site scripting (XSS):</b> injected script runs in other users' browsers, prevented by encoding output. <b>CSRF:</b> a logged-in user's browser is tricked into sending an unwanted request, prevented with tokens and SameSite cookies. <b>Broken authentication:</b> weak passwords or no multi-factor login.</p></div></details>
<details class="qa" data-t="security"><summary><span class="tag">Cybersecurity</span><h3>What is the difference between hashing and encryption?</h3></summary><div class="qa-body"><p>Hashing is one-way, so it is used to store passwords (with a slow algorithm such as bcrypt or Argon2 and a salt, for example <code>password_hash()</code> in PHP). Encryption is two-way with a key and protects data in transit or at rest, such as HTTPS.</p></div></details>
<details class="qa" data-t="security"><summary><span class="tag">Cybersecurity</span><h3>What are least privilege and multi-factor authentication?</h3></summary><div class="qa-body"><p>Least privilege gives users and programs only the access they need. Multi-factor authentication asks for a second proof, such as a code from an app, in addition to the password. Together they limit the damage when a password is stolen.</p></div></details>
<details class="qa" data-t="engineering"><summary><span class="tag">Engineering fundamentals</span><h3>What are the phases of the SDLC, and how do Agile and Waterfall differ?</h3></summary><div class="qa-body"><p>The phases are planning, requirements, design, development, testing, deployment, and maintenance. Waterfall completes each phase in order before the next one starts. Agile works in short iterations with frequent feedback, so requirements can change along the way.</p></div></details>
<details class="qa" data-t="engineering"><summary><span class="tag">Engineering fundamentals</span><h3>How does Git help in a project?</h3></summary><div class="qa-body"><p>Git tracks changes to files. A commit saves a snapshot, a branch lets me work on a feature separately, a merge combines the work, and a pull request asks for review before merging. GitHub hosts the repository, and this portfolio is published through GitHub Pages.</p></div></details>
<details class="qa" data-t="engineering"><summary><span class="tag">Engineering fundamentals</span><h3>What are the levels of software testing?</h3></summary><div class="qa-body"><p>Unit tests check small pieces such as a single function. Integration tests check that modules work together. System and acceptance tests check the whole application against the requirements. Testing early catches bugs while they are cheap to fix.</p></div></details>
<details class="qa" data-t="engineering"><summary><span class="tag">Engineering fundamentals</span><h3>How do I approach debugging?</h3></summary><div class="qa-body"><p>Reproduce the problem, read the error message, narrow down the cause by removing or logging parts, form a hypothesis, change one thing at a time, and test again. For example, if a PHP page shows nothing, the first place to look is the server error log.</p></div></details>
</div>
<p class="empty" id="none" hidden>No questions match your search.</p>
</div></section>

<section id="practice"><div class="wrap">
<p class="eyebrow">02 · Practice</p>
<h2>Practical problem-solving</h2>
<p class="sub">Interviewers often ask to see how I think, so these examples show the steps as well as the answers.</p>
<div class="cards">
<article class="card"><h3>Code: FizzBuzz</h3>
<p>Print 1 to 15, but write Fizz for multiples of 3, Buzz for multiples of 5, and FizzBuzz for both.</p>
<pre><code>for (let i = 1; i &lt;= 15; i++) {
  let out = "";
  if (i % 3 === 0) out += "Fizz";
  if (i % 5 === 0) out += "Buzz";
  console.log(out || i);
}</code></pre>
<p>The remainder operator checks divisibility. Building the string avoids a separate case for multiples of 15.</p>
<button class="btn" type="button" id="runFizz">Run it</button>
<div class="out" id="fizzOut" aria-live="polite">Output appears here.</div></article>

<article class="card"><h3>SQL: busiest categories</h3>
<p>Which product categories have the most items?</p>
<pre><code>SELECT category, COUNT(*) AS items
FROM products
GROUP BY category
ORDER BY items DESC;</code></pre>
<p>GROUP BY makes one row per category, COUNT(*) counts the rows in each group, and ORDER BY puts the largest first. To keep only large groups, add <code>HAVING COUNT(*) &gt; 5</code> after GROUP BY.</p></article>

<article class="card"><h3>Troubleshooting: no Wi-Fi connection</h3>
<p class="lbl">Problem</p>
<p>A computer cannot connect to Wi-Fi.</p>
<p class="lbl">Possible causes</p>
<p>Disabled adapter, driver problem, incorrect IP configuration, router problem, or DNS problem.</p>
<p class="lbl">Solution</p>
<ol class="steps">
<li>Check that airplane mode is off and the Wi-Fi adapter is enabled.</li>
<li>Restart the adapter, then update or reinstall the driver if it still fails.</li>
<li>Run <code>ipconfig /all</code>. An address starting with 169.254 means no address was received, so try <code>ipconfig /release</code> and <code>ipconfig /renew</code>.</li>
<li>Test another device and restart the router to see whether the router is the cause.</li>
<li>Run <code>ping 8.8.8.8</code>. If that works but <code>ping google.com</code> fails, the problem is DNS, so run <code>ipconfig /flushdns</code> or set another DNS server.</li>
</ol>
<p class="lbl">Lesson learned</p>
<p>Work from the bottom up and test one layer at a time: adapter, IP settings, router, then DNS. This finds the cause faster than changing many settings at once.</p></article>
</div>
</div></section>

<section><div class="wrap"><div class="pn">
<a class="btn" href="ai-literacy.php">← Step 19 · AI literacy</a>
<a class="btn main" href="mock-interview.php">Step 21 · Mock interview →</a>
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
  var items=[].slice.call(document.querySelectorAll(".qa")),chips=[].slice.call(document.querySelectorAll(".chips-row .chip")),
      q=$("q"),none=$("none"),tog=$("toggleAll"),topic="all";
  function vis(){return items.filter(function(d){return !d.hidden})}
  function sync(){var v=vis();tog.textContent=v.length&&v.every(function(d){return d.open})?"Collapse all":"Expand all"}
  function apply(){
    var t=q.value.trim().toLowerCase(),n=0;
    items.forEach(function(d){
      var ok=(topic==="all"||d.dataset.t===topic)&&(!t||d.textContent.toLowerCase().indexOf(t)>-1);
      d.hidden=!ok;if(ok)n++;
    });
    none.hidden=n>0;sync();
  }
  chips.forEach(function(c){c.addEventListener("click",function(){
    chips.forEach(function(x){x.setAttribute("aria-pressed",x===c)});topic=c.dataset.t;apply();
  })});
  q.addEventListener("input",apply);
  tog.addEventListener("click",function(){var v=vis(),all=v.every(function(d){return d.open});v.forEach(function(d){d.open=!all});sync()});
  items.forEach(function(d){d.addEventListener("toggle",sync)});
  $("runFizz").addEventListener("click",function(){
    var out=[],i,s;
    for(i=1;i<=15;i++){s="";if(i%3===0)s+="Fizz";if(i%5===0)s+="Buzz";out.push(s||i)}
    $("fizzOut").textContent=out.join("\n");
  });
})();
</script>
</body>
</html>
