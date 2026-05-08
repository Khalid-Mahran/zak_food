<?php
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (!password_verify($password, $user['password'])) {
            $error = 'Wrong password';
        } elseif ($user['role'] === 'vendor' && intval($user['is_approved']) !== 1) {
            $error = 'Your restaurant account is pending admin approval.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            redirectByRole($user['role']);
        }
    } else {
        $error = 'Email not found';
    }
}

include 'includes/header.php';
$burger_error = $login_error ?? '';
?>

<div class="auth-center-page">
    <div class="auth-center-box">
        <h2>Login</h2>
        <p class="small">Welcome back to ZAK Food.</p>

        <?php if ($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label>Email</label>
            <input type="email" name="email" autocomplete="off" required>

            <label>Password</label>
            <input type="password" name="password" autocomplete="new-password" required>

            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</div>


<script>
(function(){
var svg=document.getElementById('burgerSVG');
var bubble=document.getElementById('bubble');
var pwIn=document.getElementById('pw_login');
var emailIn=document.getElementById('email_login');
var pwToggle=document.getElementById('pwToggleBtn');
var toggleIcon=pwToggle?pwToggle.querySelector('span')||pwToggle:null;
var armL=document.getElementById('armL');
var armR=document.getElementById('armR');
var pL=document.getElementById('pupilL');
var pR=document.getElementById('pupilR');
var mouth=document.getElementById('mouth');
var browL=document.getElementById('browL');
var browR=document.getElementById('browR');
var blushL=document.getElementById('blushL');
var blushR=document.getElementById('blushR');
var lidL=document.getElementById('lidL');
var lidR=document.getElementById('lidR');
var state='normal';
var pwVisible=false;

function setNormal(){
  state='normal';
  if(svg){svg.classList.remove('covering','peeking');}
  if(pL){pL.setAttribute('cx','72');pL.setAttribute('cy','83');}
  if(pR){pR.setAttribute('cx','132');pR.setAttribute('cy','83');}
  if(mouth) mouth.setAttribute('d','M82,100 Q100,112 118,100');
  if(browL) browL.setAttribute('d','M55,66 Q68,57 82,62');
  if(browR) browR.setAttribute('d','M118,62 Q132,57 145,66');
  if(blushL) blushL.setAttribute('opacity','0');
  if(blushR) blushR.setAttribute('opacity','0');
  if(lidL){lidL.setAttribute('height','0');lidL.setAttribute('y','66');}
  if(lidR){lidR.setAttribute('height','0');lidR.setAttribute('y','66');}
  if(bubble) bubble.classList.remove('show');
}
function setCovering(){
  if(state==='covering') return;
  state='covering';
  if(svg){svg.classList.remove('peeking');svg.classList.add('covering');}
  if(blushL) blushL.setAttribute('opacity','0.7');
  if(blushR) blushR.setAttribute('opacity','0.7');
  if(mouth) mouth.setAttribute('d','M86,102 Q100,107 114,102');
}
function setPeeking(){
  state='peeking';
  if(svg){svg.classList.remove('covering');svg.classList.add('peeking');}
  if(pL){pL.setAttribute('cx','62');pL.setAttribute('cy','80');}
  if(pR){pR.setAttribute('cx','132');pR.setAttribute('cy','83');}
  if(blushL) blushL.setAttribute('opacity','0.8');
  if(blushR) blushR.setAttribute('opacity','0.3');
  if(mouth) mouth.setAttribute('d','M86,102 Q100,107 114,102');
  if(browL) browL.setAttribute('d','M55,60 Q68,51 82,58');
}

window.burgerError=function(msg){
  state='error';
  if(svg) svg.classList.remove('covering','peeking');
  if(pL){pL.setAttribute('cx','72');pL.setAttribute('cy','83');}
  if(pR){pR.setAttribute('cx','132');pR.setAttribute('cy','83');}
  if(mouth) mouth.setAttribute('d','M84,106 Q100,98 116,106');
  if(browL) browL.setAttribute('d','M55,66 Q68,72 82,68');
  if(browR) browR.setAttribute('d','M118,68 Q132,72 145,66');
  if(blushL) blushL.setAttribute('opacity','0');
  if(blushR) blushR.setAttribute('opacity','0');
  if(lidL){lidL.setAttribute('height','5');lidL.setAttribute('y','75');}
  if(lidR){lidR.setAttribute('height','5');lidR.setAttribute('y','75');}
  if(bubble){bubble.textContent=msg||'Wrong email or password!';bubble.classList.add('show');}
  if(svg){svg.classList.remove('shaking');void svg.offsetWidth;svg.classList.add('shaking');}
  setTimeout(function(){if(svg) svg.classList.remove('shaking');},700);
  setTimeout(function(){if(state==='error') setNormal();},3000);
};

if(pwIn){
  pwIn.addEventListener('focus',function(){if(state==='normal') setCovering();});
  pwIn.addEventListener('blur',function(){if(state==='covering'||state==='peeking') setNormal();});
}
if(emailIn){
  emailIn.addEventListener('focus',function(){if(state!=='normal') setNormal();});
}
if(pwToggle){
  pwToggle.addEventListener('mousedown',function(e){e.preventDefault();});
  pwToggle.addEventListener('click',function(){
    pwVisible=!pwVisible;
    if(pwIn) pwIn.type=pwVisible?'text':'password';
    if(pwToggle) pwToggle.innerHTML=pwVisible?'🙈':'👁️';
    if(pwVisible) setPeeking();
    else { if(pwIn&&document.activeElement===pwIn) setCovering(); else setNormal(); }
  });
}
})();
</script>

<?php include 'includes/footer.php'; ?>
