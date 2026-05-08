<?php
require_once 'includes/auth.php';

$login_error = '';
$reg_error   = '';
$reg_success = '';
$active_tab  = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $result   = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (!password_verify($password, $user['password'])) {
            $login_error = 'Wrong password!';
        } elseif ($user['role']==='vendor' && intval($user['is_approved'])!==1) {
            $login_error = 'Account pending admin approval.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            redirectByRole($user['role']);
        }
    } else {
        $login_error = 'Email not found!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $active_tab = 'register';
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if ($role === 'admin') {
        $reg_error = 'Admin registration not allowed.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $reg_error = 'Email already exists.';
        } else {
            $approved = $role==='vendor' ? 0 : 1;
            mysqli_query($conn, "INSERT INTO users (name,email,password,role,phone,is_approved) VALUES ('$name','$email','$password','$role','$phone',$approved)");
            $active_tab = 'login';
            $reg_success = $role==='vendor' ? 'Restaurant request sent! Pending admin approval.' : 'Account created! You can login now.';
        }
    }
}

include 'includes/header.php';
?>
<style>
.auth-page{min-height:calc(100vh - 64px);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 20px 48px;background:linear-gradient(135deg,#0f172a 0%,#1a2744 60%,#0f172a 100%);position:relative;overflow:hidden;}
.auth-page::before{content:'';position:absolute;top:-100px;right:-100px;width:400px;height:400px;background:radial-gradient(circle,rgba(249,115,22,0.18) 0%,transparent 70%);border-radius:50%;pointer-events:none;}
.auth-page::after{content:'';position:absolute;bottom:-100px;left:-60px;width:350px;height:350px;background:radial-gradient(circle,rgba(249,115,22,0.08) 0%,transparent 70%);border-radius:50%;pointer-events:none;}

.burger-stage{position:relative;width:200px;height:175px;display:flex;align-items:center;justify-content:center;margin-bottom:-12px;z-index:2;}
.bubble{position:absolute;top:-10px;left:50%;transform:translateX(-50%) scale(0);background:#fff0f0;border:2.5px solid #fca5a5;border-radius:18px;padding:10px 18px;font-size:13px;font-weight:700;color:#dc2626;white-space:nowrap;transform-origin:bottom center;transition:transform 0.35s cubic-bezier(.34,1.56,.64,1);pointer-events:none;z-index:10;box-shadow:0 4px 16px rgba(220,38,38,0.15);}
.bubble::after{content:'';position:absolute;bottom:-11px;left:50%;transform:translateX(-50%);border:10px solid transparent;border-top:10px solid #fca5a5;}
.bubble.show{transform:translateX(-50%) scale(1);}

@keyframes bshake{0%,100%{transform:translateX(0)}12%{transform:translateX(-11px)}25%{transform:translateX(11px)}37%{transform:translateX(-8px)}50%{transform:translateX(8px)}62%{transform:translateX(-5px)}75%{transform:translateX(5px)}87%{transform:translateX(-2px)}}
.shaking{animation:bshake 0.65s ease-in-out;}

.arm-l{transform-origin:68px 138px;transform:rotate(0deg);transition:transform 0.5s cubic-bezier(.34,1.56,.64,1);transform-box:fill-box;}
.arm-r{transform-origin:132px 138px;transform:rotate(0deg);transition:transform 0.5s cubic-bezier(.34,1.56,.64,1);transform-box:fill-box;}
.covering .arm-l{transform:rotate(-115deg);}
.covering .arm-r{transform:rotate(115deg);}
.peeking .arm-l{transform:rotate(-82deg);}
.peeking .arm-r{transform:rotate(92deg);}
.blush{transition:opacity 0.35s;}

.auth-card{background:white;border-radius:24px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.35);position:relative;z-index:1;}
.auth-header{background:linear-gradient(135deg,#f97316,#c2410c);padding:26px 40px 22px;text-align:center;}
.auth-logo{font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:white;letter-spacing:-0.03em;}
.auth-header p{color:rgba(255,255,255,0.75);font-size:13px;margin-top:4px;}
.auth-tabs{display:flex;border-bottom:1px solid #e2e8f0;}
.auth-tab{flex:1;padding:14px;text-align:center;font-weight:700;font-size:14px;color:#64748b;cursor:pointer;border:none;background:none;font-family:'DM Sans',sans-serif;transition:all 0.2s;border-bottom:3px solid transparent;margin-bottom:-1px;}
.auth-tab.active{color:#f97316;border-bottom-color:#f97316;background:#fff7ed;}
.auth-body{padding:28px 36px 32px;}
.auth-body form{background:none;border:none;box-shadow:none;padding:0;max-width:none;margin:0;}
.auth-body .btn{width:100%;padding:13px;font-size:15px;margin-top:4px;margin-right:0;justify-content:center;}
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:46px;margin-bottom:0;}
.pw-eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;cursor:pointer;font-size:20px;padding:4px;line-height:1;color:#64748b;border-radius:6px;transition:color 0.2s;}
.pw-eye:hover{color:#0f172a;}
</style>

<div class="auth-page">

  <!-- BURGER CHARACTER -->
  <div class="burger-stage">
    <div class="bubble" id="bub">Wrong email or password!</div>
    <svg id="bsvg" viewBox="0 0 200 200" width="175" height="175" style="overflow:visible">
      <!-- Left arm -->
      <g class="arm-l" id="armL">
        <rect x="2" y="129" width="68" height="19" rx="9" fill="#D4852A"/>
        <circle cx="12" cy="138" r="13" fill="#E8A855"/>
        <ellipse cx="4" cy="131" rx="4" ry="2.5" fill="#D4852A"/>
        <ellipse cx="4" cy="138" rx="4" ry="2.5" fill="#D4852A"/>
        <ellipse cx="4" cy="145" rx="4" ry="2.5" fill="#D4852A"/>
      </g>
      <!-- Right arm -->
      <g class="arm-r" id="armR">
        <rect x="130" y="129" width="68" height="19" rx="9" fill="#D4852A"/>
        <circle cx="188" cy="138" r="13" fill="#E8A855"/>
        <ellipse cx="196" cy="131" rx="4" ry="2.5" fill="#D4852A"/>
        <ellipse cx="196" cy="138" rx="4" ry="2.5" fill="#D4852A"/>
        <ellipse cx="196" cy="145" rx="4" ry="2.5" fill="#D4852A"/>
      </g>
      <!-- Bottom bun -->
      <ellipse cx="100" cy="172" rx="68" ry="21" fill="#E8A855"/>
      <ellipse cx="100" cy="168" rx="63" ry="9" fill="#D4852A" opacity="0.22"/>
      <!-- Patty -->
      <rect x="30" y="144" width="140" height="24" rx="8" fill="#6B3320"/>
      <rect x="30" y="144" width="140" height="7" rx="3" fill="#7D4535" opacity="0.35"/>
      <!-- Cheese -->
      <path d="M28,144 Q42,133 56,142 Q70,131 84,140 Q98,129 112,140 Q126,131 140,142 Q154,133 172,144 Z" fill="#FFC107"/>
      <!-- Lettuce -->
      <path d="M24,142 Q38,129 52,137 Q66,125 80,135 Q94,123 108,135 Q122,125 136,137 Q150,129 164,142 Q150,144 136,142 Q122,140 108,144 Q94,140 80,144 Q66,140 52,142 Q38,144 24,142Z" fill="#4CAF50" opacity="0.92"/>
      <!-- Top bun -->
      <ellipse cx="100" cy="88" rx="70" ry="44" fill="#D4852A"/>
      <ellipse cx="88" cy="70" rx="48" ry="23" fill="#E8A855" opacity="0.42"/>
      <!-- Sesame seeds -->
      <ellipse cx="80" cy="64" rx="7" ry="3.5" fill="#B8731A" transform="rotate(-18,80,64)"/>
      <ellipse cx="103" cy="57" rx="7" ry="3.5" fill="#B8731A"/>
      <ellipse cx="124" cy="65" rx="7" ry="3.5" fill="#B8731A" transform="rotate(18,124,65)"/>
      <!-- Eyebrows -->
      <path id="bwL" d="M55,66 Q68,57 82,62" stroke="#5D3A1A" stroke-width="3.5" fill="none" stroke-linecap="round"/>
      <path id="bwR" d="M118,62 Q132,57 145,66" stroke="#5D3A1A" stroke-width="3.5" fill="none" stroke-linecap="round"/>
      <!-- Left eye -->
      <circle cx="70" cy="80" r="14" fill="white"/>
      <circle id="pL" cx="72" cy="83" r="8" fill="#1a1a1a"/>
      <circle cx="76" cy="79" r="3" fill="white"/>
      <rect id="lidL" x="56" y="66" width="28" height="0" rx="4" fill="#D4852A"/>
      <!-- Right eye -->
      <circle cx="130" cy="80" r="14" fill="white"/>
      <circle id="pR" cx="132" cy="83" r="8" fill="#1a1a1a"/>
      <circle cx="136" cy="79" r="3" fill="white"/>
      <rect id="lidR" x="116" y="66" width="28" height="0" rx="4" fill="#D4852A"/>
      <!-- Mouth -->
      <path id="mth" d="M82,100 Q100,112 118,100" stroke="#5D3A1A" stroke-width="3" fill="none" stroke-linecap="round"/>
      <!-- Blush -->
      <ellipse class="blush" id="bshL" cx="54" cy="90" rx="10" ry="6" fill="#FF9999" opacity="0"/>
      <ellipse class="blush" id="bshR" cx="146" cy="90" rx="10" ry="6" fill="#FF9999" opacity="0"/>
    </svg>
  </div>

  <!-- AUTH CARD -->
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-logo">🍔 ZAK Food</div>
      <p>Order from the best restaurants near you</p>
    </div>

    <div class="auth-tabs">
      <button class="auth-tab <?php echo $active_tab==='login'?'active':''; ?>" id="tab-login" onclick="switchTab('login')">Login</button>
      <button class="auth-tab <?php echo $active_tab==='register'?'active':''; ?>" id="tab-reg" onclick="switchTab('register')">Register</button>
    </div>

    <!-- LOGIN -->
    <div class="auth-body" id="body-login" style="display:<?php echo $active_tab==='login'?'block':'none';?>">
      <?php if($login_error): ?>
        <div class="alert"><?php echo $login_error; ?></div>
      <?php endif; ?>
      <?php if($reg_success): ?>
        <div class="alert success"><?php echo $reg_success; ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="login">
        <label>Email Address</label>
        <input type="email" name="email" id="em" placeholder="you@example.com" required>
        <label>Password</label>
        <div class="pw-wrap">
          <input type="password" name="password" id="pw" placeholder="••••••••" required>
          <button class="pw-eye" id="eyeBtn" type="button">👁️</button>
        </div>
        <br>
        <button class="btn" type="submit">Login →</button>
      </form>
    </div>

    <!-- REGISTER -->
    <div class="auth-body" id="body-reg" style="display:<?php echo $active_tab==='register'?'block':'none';?>">
      <?php if($reg_error): ?>
        <div class="alert"><?php echo $reg_error; ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="register">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Your name" required>
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" required>
        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="01XXXXXXXXX">
        <label>Password</label>
        <div class="pw-wrap">
          <input type="password" name="password" placeholder="••••••••" required>
          <button class="pw-eye" type="button" onclick="var i=this.previousElementSibling;i.type=i.type==='password'?'text':'password';this.textContent=i.type==='password'?'👁️':'🙈';">👁️</button>
        </div>
        <br>
        <label>Account Type</label>
        <select name="role" required>
          <option value="customer">🛒 Customer — Order food</option>
          <option value="vendor">🏪 Restaurant / Vendor</option>
          <option value="delivery">🏍️ Delivery Worker</option>
        </select>
        <button class="btn" type="submit">Create Account →</button>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  var s=document.getElementById('bsvg');
  var bub=document.getElementById('bub');
  var pw=document.getElementById('pw');
  var em=document.getElementById('em');
  var eye=document.getElementById('eyeBtn');
  var pL=document.getElementById('pL');
  var pR=document.getElementById('pR');
  var mth=document.getElementById('mth');
  var bwL=document.getElementById('bwL');
  var bwR=document.getElementById('bwR');
  var bshL=document.getElementById('bshL');
  var bshR=document.getElementById('bshR');
  var lidL=document.getElementById('lidL');
  var lidR=document.getElementById('lidR');
  var state='normal';
  var pwVis=false;

  function normal(){
    state='normal';
    s.classList.remove('covering','peeking');
    pL.setAttribute('cx','72'); pL.setAttribute('cy','83');
    pR.setAttribute('cx','132'); pR.setAttribute('cy','83');
    mth.setAttribute('d','M82,100 Q100,112 118,100');
    bwL.setAttribute('d','M55,66 Q68,57 82,62');
    bwR.setAttribute('d','M118,62 Q132,57 145,66');
    bshL.setAttribute('opacity','0'); bshR.setAttribute('opacity','0');
    lidL.setAttribute('height','0'); lidL.setAttribute('y','66');
    lidR.setAttribute('height','0'); lidR.setAttribute('y','66');
    bub.classList.remove('show');
  }

  function cover(){
    if(state==='covering') return;
    state='covering';
    s.classList.remove('peeking'); s.classList.add('covering');
    bshL.setAttribute('opacity','0.7'); bshR.setAttribute('opacity','0.7');
    mth.setAttribute('d','M86,102 Q100,107 114,102');
  }

  function peek(){
    state='peeking';
    s.classList.remove('covering'); s.classList.add('peeking');
    pL.setAttribute('cx','61'); pL.setAttribute('cy','80');
    pR.setAttribute('cx','132'); pR.setAttribute('cy','83');
    bwL.setAttribute('d','M55,60 Q68,51 82,57');
    bshL.setAttribute('opacity','0.9'); bshR.setAttribute('opacity','0.3');
    mth.setAttribute('d','M86,102 Q100,107 114,102');
  }

  function error(msg){
    state='error';
    s.classList.remove('covering','peeking');
    pL.setAttribute('cx','72'); pL.setAttribute('cy','83');
    pR.setAttribute('cx','132'); pR.setAttribute('cy','83');
    mth.setAttribute('d','M84,106 Q100,98 116,106');
    bwL.setAttribute('d','M55,70 Q68,76 82,72');
    bwR.setAttribute('d','M118,72 Q132,76 145,70');
    bshL.setAttribute('opacity','0'); bshR.setAttribute('opacity','0');
    lidL.setAttribute('height','5'); lidL.setAttribute('y','75');
    lidR.setAttribute('height','5'); lidR.setAttribute('y','75');
    bub.textContent=msg||'Wrong email or password!';
    bub.classList.add('show');
    s.classList.remove('shaking'); void s.offsetWidth; s.classList.add('shaking');
    setTimeout(function(){ s.classList.remove('shaking'); },700);
    setTimeout(function(){ if(state==='error') normal(); },3500);
  }

  if(pw){
    pw.addEventListener('focus', function(){ if(state==='normal') cover(); });
    pw.addEventListener('blur',  function(){ if(state==='covering'||state==='peeking') normal(); });
  }
  if(em){
    em.addEventListener('focus', function(){ if(state!=='normal') normal(); });
  }
  if(eye){
    eye.addEventListener('mousedown', function(e){ e.preventDefault(); });
    eye.addEventListener('click', function(){
      pwVis=!pwVis;
      pw.type=pwVis?'text':'password';
      eye.textContent=pwVis?'🙈':'👁️';
      if(pwVis) peek();
      else { if(document.activeElement===pw) cover(); else normal(); }
    });
  }

  <?php if($login_error): ?>
  setTimeout(function(){ error(<?php echo json_encode($login_error); ?>); }, 150);
  <?php endif; ?>
})();

function switchTab(t){
  document.getElementById('body-login').style.display=t==='login'?'block':'none';
  document.getElementById('body-reg').style.display=t==='register'?'block':'none';
  document.getElementById('tab-login').classList.toggle('active',t==='login');
  document.getElementById('tab-reg').classList.toggle('active',t==='register');
}
</script>

<?php include 'includes/footer.php'; ?>
