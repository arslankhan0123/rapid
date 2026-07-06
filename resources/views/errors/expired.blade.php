<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Subscription Expired</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b0f19;
            --accent-color: #f43f5e;
            --accent-glow: rgba(244, 63, 94, 0.4);
            --card-bg: rgba(17, 24, 39, 0.7);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --border-color: rgba(255, 255, 255, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at center, #1e1b4b 0%, var(--bg-color) 70%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        /* Ambient background glowing circles */
        .glow-circle {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            z-index: 1;
            filter: blur(80px);
            opacity: 0.5;
        }

        .glow-1 {
            top: -100px;
            left: -100px;
        }

        .glow-2 {
            bottom: -100px;
            right: -100px;
        }

        /* Glassmorphism Card Container */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 56px 40px;
            text-align: center;
            max-width: 550px;
            width: 100%;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10;
            animation: fadeInScale 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        /* Card top accent light bar */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        }

        /* Expired Icon & Glow */
        .icon-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 32px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--accent-color);
            opacity: 0.1;
            animation: pulse 2.5s infinite;
        }

        .icon-main {
            font-size: 48px;
            color: var(--accent-color);
            filter: drop-shadow(0 0 15px var(--accent-glow));
            z-index: 2;
            animation: float 4s ease-in-out infinite;
        }

        /* Typography */
        h1 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff 40%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.desc {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: 32px;
            font-weight: 300;
        }

        /* Highlight box for Date */
        .date-box {
            background: rgba(244, 63, 94, 0.05);
            border: 1px dashed rgba(244, 63, 94, 0.25);
            border-radius: 16px;
            padding: 18px 24px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .date-box i {
            color: var(--accent-color);
            font-size: 20px;
        }

        .date-text {
            font-size: 15px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .date-text span {
            color: var(--accent-color);
        }

        /* Action Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--accent-color);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 20px -10px var(--accent-color);
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -8px var(--accent-color);
            filter: brightness(1.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Footer Copyright/Info */
        .footer-info {
            margin-top: 32px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
            letter-spacing: 0.5px;
        }

        /* Animations */
        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.1;
            }
            50% {
                transform: scale(1.15);
                opacity: 0.25;
            }
            100% {
                transform: scale(1);
                opacity: 0.1;
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        @media (max-width: 640px) {
            .card {
                padding: 40px 24px;
            }
            h1 {
                font-size: 28px;
            }
            p.desc {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="glow-circle glow-1"></div>
    <div class="glow-circle glow-2"></div>

    <div class="card">
        <div class="icon-wrapper">
            <div class="icon-bg"></div>
            <i class="fa-solid fa-server icon-main"></i>
        </div>
        
        <h1>Server Expired</h1>
        <p class="desc">
            The subscription for this application has ended. Access to the portal and its features has been temporarily suspended.
        </p>

        <div class="date-box">
            <i class="fa-regular fa-calendar-xmark"></i>
            <div class="date-text">
                Expired Date: <span>19 June 2026</span>
            </div>
        </div>

        <!-- <a href="mailto:support@example.com?subject=Server%20Suspended%20-%20Rapidsma" class="btn">
            <i class="fa-solid fa-envelope-open-text"></i>
            Contact Administrator
        </a> -->

        <div class="footer-info">
            &copy; 2026 rapidsma. All rights reserved.
        </div>
    </div>

</body>
</html>
