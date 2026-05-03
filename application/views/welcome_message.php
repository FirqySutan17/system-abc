<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PT Abadi Bersama Cerah</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{
    --abc-red:#E50414;
    --abc-blue:#0285D1;
    --abc-dark:#0f172a;
    --abc-gray:#64748b;
}

html,body{
    width:100%;
    height:100%;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    min-height:100vh;
    position:relative;
    overflow:hidden;
    background:linear-gradient(
        135deg,
        #ffffff 0%,
        #eef7ff 45%,
        #e0f2fe 100%
    );
}

/* background */
/* body::before{
    content:"";
    position:absolute;
    width:45vw;
    height:45vw;
    min-width:250px;
    min-height:250px;
    max-width:500px;
    max-height:500px;
    border-radius:50%;
    top:-15%;
    left:-10%;
    background:rgba(2,133,209,.08);
    filter:blur(60px);
}

body::after{
    content:"";
    position:absolute;
    width:35vw;
    height:35vw;
    min-width:220px;
    min-height:220px;
    max-width:420px;
    max-height:420px;
    border-radius:50%;
    right:-10%;
    bottom:-10%;
    background:rgba(229,4,20,.06);
    filter:blur(60px);
} */

/* sign in */
.signin-btn{
    position:absolute;
    top:24px;
    right:24px;
    z-index:100;
    text-decoration:none;
    color:#fff;
    padding:12px 24px;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    background:linear-gradient(
        135deg,
        var(--abc-blue),
        #0ea5e9
    );
    box-shadow:0 10px 30px rgba(2,133,209,.25);
    transition:.3s;
}

.signin-btn:hover{
    background:linear-gradient(
        135deg,
        var(--abc-blue),
        var(--abc-red)
    );
    transform:translateY(-2px);
}

/* center content */
.wrapper{
    position:absolute;
    inset:0;
    z-index:2;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    width:100%;
    padding:20px;
}

.logo{
    width:min(220px,45vw);
    height:auto;
    margin-bottom:24px;
}

h1{
    font-size:clamp(24px,4vw,40px);
    color:var(--abc-dark);
    margin-bottom:12px;
    font-weight:700;
}

p{
    font-size:clamp(14px,2vw,18px);
    color:var(--abc-gray);
}

.accent{
    width:90px;
    height:4px;
    border-radius:999px;
    margin-top:20px;
    background:linear-gradient(
        90deg,
        var(--abc-red),
        var(--abc-blue)
    );
}

/* mobile */
@media (max-width:768px){

    .signin-btn{
        top:18px;
        right:18px;
        padding:10px 18px;
        font-size:13px;
    }

    .logo{
        width:min(170px,42vw);
        margin-bottom:18px;
    }

    h1{
        font-size:28px;
    }

    p{
        font-size:15px;
    }

    .accent{
        width:70px;
        margin-top:16px;
    }
}

</style>
</head>
<body>

<a href="<?= base_url('login'); ?>" class="signin-btn">
    Sign In
</a>

<div class="wrapper">
    <img src="<?= base_url('assets/img/abc-trans.png'); ?>" class="logo">

    <h1>PT. Abadi Bersama Cerah</h1>
    <p>Website Dalam Tahap Pengembangan</p>

    <div class="accent"></div>
</div>

</body>
</html>