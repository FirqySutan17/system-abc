<div class="container-fluid dashboard-modern" style="overflow-x: hidden">
    <style>
        .welcome-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;     /* horizontal center */
            justify-content: center; /* vertical center */
            text-align: center;
            width: 100%;
            padding: 40px 60px;
            background: rgba(255,255,255,0.6);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        /* TITLE */
        .welcome-title {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        /* ABC highlight */
        .welcome-title span {
            color: #e60012;
            font-weight: 700;
        }

        /* USER NAME */
        .welcome-user {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            letter-spacing: 1px;
        }

        .welcome-title span {
            background: linear-gradient(90deg, #e60012, #0072bc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-wrapper {
            animation: fadeInUp 0.6s ease;
        }

        .dashboard-modern {
            min-height: calc(100vh - 80px); /* kurangi tinggi header */
            display: flex;
            align-items: center;
            justify-content: center;
            
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 18px;
            }

            .welcome-user {
                font-size: 14px;
            }
        }
    </style>

    <!-- HEADER -->
    <div class="mb-4">
        <div class="welcome-wrapper">
            <h2 class="welcome-title">
                Welcome to <span>ABC System</span> 👋
            </h2>
            <p class="welcome-user">
                <?= strtoupper($this->session->userdata('name')); ?>
            </p>
        </div>
    </div>

</div>