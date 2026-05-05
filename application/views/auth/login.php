<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon-96x96.png'); ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/img/favicon.svg'); ?>" />
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.ico'); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>" />
    <link rel="manifest" href="<?= base_url('assets/img/site.webmanifest'); ?>" />

    <title>PT. Abadi Bersama Cerah</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        html, body{
            margin:0;
            padding:0;
            width:100%;
            min-height:100vh;
            min-height:100dvh;
            font-family: Inter, sans-serif;
        }

        body{
            background:
                linear-gradient(
                    135deg,
                    rgba(19,135,212,.92) 0%,
                    rgba(10,74,145,.90) 45%,
                    rgba(227,6,19,.82) 100%
                ),
                url('<?= base_url('assets/img/bg-login.jpg'); ?>') center/cover no-repeat;
        }

        .login-wrapper{
            width:100%;
            min-height:100vh;
            min-height:100dvh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
        }

        .login-container{
            width:100%;
            max-width:1400px;
            min-height:calc(100vh - 48px);
            min-height:calc(100dvh - 48px);
            display:flex;
            overflow:hidden;
            border-radius:28px;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.18);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            box-shadow:0 30px 80px rgba(0,0,0,.25);
        }

        .left-panel{
            flex:1;
            padding:clamp(30px,5vw,80px);
            display:flex;
            flex-direction:column;
            justify-content:center;
            position:relative;
            color:white;
        }

        .left-panel::before{
            content:'';
            position:absolute;
            width:380px;
            height:380px;
            background:rgba(255,255,255,.08);
            border-radius:50%;
            top:-120px;
            right:-100px;
            filter:blur(5px);
        }

        .badge-company{
            width:fit-content;
            padding:12px 22px;
            border-radius:999px;
            background:rgba(255,255,255,.15);
            border:1px solid rgba(255,255,255,.25);
            margin-bottom:28px;
            font-weight:600;
        }

        .left-panel h1{
            font-size:clamp(42px,5vw,72px);
            font-weight:800;
            line-height:1.05;
            margin-bottom:15px;
        }

        .left-panel p{
            max-width:540px;
            line-height:1.9;
            opacity:.95;
        }

        .right-panel{
            width:min(500px,100%);
            background:rgba(255,255,255,.96);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:clamp(25px,4vw,60px);
        }

        .login-box{
            width:100%;
            max-width:380px;
        }

        .logo{
            text-align:center;
            margin-bottom:18px;
        }

        .logo img{
            width:clamp(130px,18vw,190px);
            filter:drop-shadow(0 10px 20px rgba(0,0,0,.15));
        }

        .company-name{
            text-align:center;
            color:#64748B;
            margin-bottom:35px;
            font-size:14px;
            font-weight:500;
        }

        .form-label{
            font-weight:700;
            margin-bottom:10px;
            color:#0F172A;
        }

        .input-group-custom{
            position:relative;
            margin-bottom:22px;
        }

        .input-group-custom i{
            position:absolute;
            top:50%;
            left:18px;
            transform:translateY(-50%);
            color:#94A3B8;
            z-index:2;
        }

        .form-control{
            width:100%;
            height:56px;
            border-radius:15px;
            border:1px solid #E2E8F0;
            padding-left:50px;
            font-size:15px;
            transition:.3s;
            background:#fff;
        }

        .form-control:focus{
            border-color:#1387D4;
            box-shadow:0 0 0 4px rgba(19,135,212,.15);
        }

        .btn-login{
            width:100%;
            height:56px;
            border:none;
            border-radius:15px;
            font-weight:700;
            color:white;
            background:linear-gradient(
                135deg,
                #E30613,
                #C5000D
            );
            transition:.3s;
        }

        .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 16px 30px rgba(227,6,19,.30);
        }

        .error-box{
            background:#FFF1F2;
            border:1px solid #FECDD3;
            color:#BE123C;
            padding:14px 18px;
            border-radius:14px;
            margin-bottom:20px;
        }

        @media(max-width:768px){
            .login-wrapper{
                padding:0;
            }

            .login-container{
                border-radius:0;
                min-height:100vh;
                min-height:100dvh;
            }

            .left-panel{
                display:none;
            }

            .right-panel{
                width:100%;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-container">

        <div class="left-panel">
            <span class="badge-company">ABC System</span>
            <h1 style="color: #fff">Welcome..</h1>
            <!-- <p>
                Access your integrated corporate platform securely.
                Manage data, monitor operations, and streamline your workflow
                in one centralized system.
            </p> -->
        </div>

        <div class="right-panel">
            <div class="login-box">

                <div class="logo">
                    <img src="<?= base_url('assets/img/abc-trans.png'); ?>" alt="">
                </div>

                <div class="company-name">
                    PT. Abadi Bersama Cerah
                </div>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="error-box">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?= $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/process_login'); ?>" method="post">

                    <label class="form-label">Username</label>
                    <div class="input-group-custom">
                        <i class="fa-regular fa-user"></i>
                        <input type="text"
                               class="form-control"
                               name="username"
                               placeholder="Enter your username">
                    </div>

                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Enter your password">
                    </div>

                    <button type="submit" class="btn-login mt-2">
                        Sign In
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

<script src="<?= base_url('assets/libs/jquery/dist/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>

</body>
</html>