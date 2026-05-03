<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?= base_url('assets/img/site.webmanifest'); ?>">

    <title>PT. Artha Pratama Jaya Abadi</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        html,
        body{
            margin:0;
            padding:0;
            width:100%;
            height:100%;
            overflow:hidden;
            font-family: Inter, sans-serif;
        }

        body{
            background:
                linear-gradient(135deg, rgba(11,71,154,.95), rgba(30,120,255,.90)),
                url('<?= base_url('assets/img/bg-login.jpg'); ?>') center center/cover no-repeat;
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
            height:calc(100vh - 48px);
            height:calc(100dvh - 48px);
            max-width:1400px;
            border-radius:28px;
            overflow:hidden;
            display:flex;
            background:rgba(255,255,255,.08);
            backdrop-filter:blur(18px);
            -webkit-backdrop-filter:blur(18px);
            box-shadow:0 25px 80px rgba(0,0,0,.18);
        }

        .left-panel{
            flex:1;
            color:white;
            padding:clamp(30px,5vw,80px);
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .left-panel h1{
            font-size:clamp(34px,5vw,64px);
            font-weight:800;
            line-height:1.1;
            margin-bottom:20px;
        }

        .left-panel p{
            font-size:clamp(14px,1.2vw,18px);
            max-width:520px;
            line-height:1.8;
            opacity:.95;
        }

        .badge-company{
            display:inline-block;
            width:fit-content;
            padding:10px 18px;
            border-radius:999px;
            background:rgba(255,255,255,.15);
            margin-bottom:25px;
            font-size:14px;
            font-weight:600;
        }

        .right-panel{
            width:min(480px,100%);
            background:white;
            padding:clamp(24px,4vw,60px);
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .login-box{
            width:100%;
            max-width:380px;
        }

        .logo{
            text-align:center;
            margin-bottom:20px;
        }

        .logo img{
            width:clamp(120px,18vw,180px);
            height:auto;
        }

        .company-name{
            text-align:center;
            color:#6b7280;
            margin-bottom:32px;
            font-size:14px;
        }

        .form-label{
            font-weight:600;
            margin-bottom:10px;
            color:#111827;
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
            color:#9ca3af;
            z-index:2;
        }

        .form-control{
            height:56px;
            width:100%;
            border-radius:14px;
            border:1px solid #e5e7eb;
            padding-left:48px;
            font-size:15px;
            transition:.3s;
        }

        .form-control:focus{
            border-color:#0d6efd;
            box-shadow:0 0 0 4px rgba(13,110,253,.12);
        }

        .btn-login{
            width:100%;
            height:56px;
            border:none;
            border-radius:14px;
            background:linear-gradient(135deg,#0d6efd,#0056d6);
            color:white;
            font-weight:700;
            transition:.3s;
        }

        .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 30px rgba(13,110,253,.28);
        }

        .error-box{
            background:#fff1f2;
            border:1px solid #fecdd3;
            color:#be123c;
            padding:14px 18px;
            border-radius:14px;
            margin-bottom:20px;
            font-size:14px;
        }

        /* tablet */
        @media (max-width: 992px){
            .login-wrapper{
                padding:16px;
            }

            .login-container{
                height:calc(100vh - 32px);
                height:calc(100dvh - 32px);
            }

            .left-panel{
                flex:.8;
            }

            .right-panel{
                width:420px;
            }
        }

        /* mobile */
        @media (max-width: 768px){
            body{
                overflow:auto;
            }

            .login-wrapper{
                padding:0;
            }

            .login-container{
                height:100vh;
                height:100dvh;
                border-radius:0;
                display:block;
            }

            .left-panel{
                display:none;
            }

            .right-panel{
                width:100%;
                height:100%;
                padding:30px;
            }

            .login-box{
                max-width:100%;
            }
        }

        /* very small phone */
        @media (max-width: 420px){
            .right-panel{
                padding:22px;
            }

            .form-control,
            .btn-login{
                height:52px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-container">

        <div class="left-panel">
            <span class="badge-company">Corporate Information System</span>
            <h1>Welcome Back</h1>
            <p>
                Access your integrated corporate platform securely.
                Manage data, monitor operations, and streamline your workflow
                in one centralized system.
            </p>
        </div>

        <div class="right-panel">
            <div class="login-box">

                <div class="logo">
                    <img src="<?= base_url('assets/img/icon.png'); ?>" alt="">
                </div>

                <div class="company-name">
                    PT. Artha Pratama Jaya Abadi
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