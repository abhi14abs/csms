<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Grand Inauguration & Launch | Textile Committee CSMS</title>
    <link rel="shortcut icon" href="{{ asset('backAssets/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold-light: #ffe57f;
            --gold-main: #ffd700;
            --gold-dark: #b8860b;
            --gold-gradient: linear-gradient(135deg, #fff2a3 0%, #ffd700 40%, #cca01d 70%, #996e00 100%);
            --gold-glow: rgba(255, 215, 0, 0.45);
            --curtain-red-dark: #3a0007;
            --curtain-red-mid: #780016;
            --curtain-red-bright: #c1121f;
            --curtain-red-highlight: #e63946;
            --bg-stage: radial-gradient(circle at center, #1b2838 0%, #0d131a 60%, #05070a 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            background: #06090e;
            font-family: 'Outfit', sans-serif;
            color: #ffffff;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        /* STAGE CONTAINER (Scrollable on small viewports) */
        #stage-container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            background: var(--bg-stage);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 20px 0;
        }

        /* AMBIENT SPOTLIGHT EFFECT */
        .spotlight-left, .spotlight-right {
            position: fixed;
            top: -20%;
            width: 60vw;
            height: 140vh;
            pointer-events: none;
            opacity: 0.18;
            filter: blur(60px);
            z-index: 2;
            transition: opacity 1.5s ease;
        }
        .spotlight-left {
            left: -10%;
            background: radial-gradient(ellipse at center, rgba(255, 215, 0, 0.6) 0%, transparent 70%);
            transform: rotate(-25deg);
        }
        .spotlight-right {
            right: -10%;
            background: radial-gradient(ellipse at center, rgba(147, 197, 253, 0.5) 0%, transparent 70%);
            transform: rotate(25deg);
        }

        /* ------------------------------------------------------------- */
        /* REVEALED CONTENT (BEHIND CURTAIN) */
        /* ------------------------------------------------------------- */
        #revealed-content {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1120px;
            padding: 70px 20px 40px;
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            opacity: 0;
            transform: scale(0.94) translateY(20px);
            transition: all 1.4s cubic-bezier(0.16, 1, 0.3, 1) 0.4s;
            pointer-events: none;
        }

        #revealed-content.visible {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }

        /* INAUGURATION BADGE */
        .launch-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
            border: 1px solid rgba(255, 215, 0, 0.4);
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.2);
            padding: 8px 24px;
            border-radius: 999px;
            margin-bottom: 20px;
            animation: pulse-glow 3s infinite;
        }

        .launch-badge .badge-icon {
            font-size: 16px;
            color: var(--gold-main);
        }

        .launch-badge span {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--gold-light);
            text-transform: uppercase;
        }

        /* MAIN HERO CARD */
        .portal-glass-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 38px 44px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), inset 0 1px 1px rgba(255, 255, 255, 0.2);
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .portal-glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.8), transparent);
            animation: shimmer-line 4s infinite linear;
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 16px;
        }

        .brand-logo-wrap {
            position: relative;
            width: 80px;
            height: 80px;
            background: rgba(245, 242, 242, 0.96);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 215, 0, 0.4);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 15px var(--gold-glow);
            flex-shrink: 0;
        }

        .brand-logo-wrap img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .brand-text-block {
            text-align: left;
        }

        .brand-org-name {
            font-family: 'Cinzel', serif;
            font-size: 17px;
            letter-spacing: 3px;
            color: #ff3344;
            text-transform: uppercase;
            font-weight: 900;
        }

        .brand-system-title {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            background: linear-gradient(135deg, #ffffff 30%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .launch-tagline {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.82);
            max-width: 780px;
            margin: 12px auto 26px;
            line-height: 1.5;
            font-weight: 300;
        }

        /* HIGHLIGHT MODULE PILLARS */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 30px;
            text-align: left;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px;
            transition: all 0.3s ease;
            position: relative;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 215, 0, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            font-size: 22px;
            margin-bottom: 8px;
            display: inline-block;
        }

        .feature-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.4;
        }

        /* ACTION CTA BUTTONS */
        .cta-action-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-gold-launch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--gold-gradient);
            color: #0f172a;
            padding: 13px 30px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(255, 215, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-gold-launch:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 15px 35px rgba(255, 215, 0, 0.5);
            color: #000;
        }

        .btn-portal-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            padding: 13px 26px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-portal-secondary:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
            color: #fff;
        }

        .launch-meta-footer {
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 16px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.55);
        }

        .live-status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            margin-right: 6px;
            box-shadow: 0 0 10px #10b981;
            animation: pulse-green 2s infinite;
        }

        /* ------------------------------------------------------------- */
        /* CURTAIN THEATRE STAGE LAYER (FIXED OVERLAY) */
        /* ------------------------------------------------------------- */
        #curtain-stage {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            z-index: 50;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: auto;
            transition: opacity 0.8s ease 2.2s, visibility 0s linear 3s;
        }

        #curtain-stage.opened {
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
        }

        /* TOP VALANCE (PELMET DRAPERY) */
        .curtain-valance {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 90px;
            z-index: 60;
            background: 
                radial-gradient(ellipse at top, #9e0c1f 0%, #4a000b 100%),
                repeating-linear-gradient(90deg, 
                    rgba(0, 0, 0, 0.4) 0px, 
                    transparent 30px, 
                    rgba(255, 255, 255, 0.15) 60px, 
                    transparent 90px, 
                    rgba(0, 0, 0, 0.5) 120px
                );
            background-blend-mode: multiply;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.8), inset 0 -4px 10px rgba(0, 0, 0, 0.6);
            border-bottom: 6px solid #cfa228;
            transition: transform 2.2s cubic-bezier(0.77, 0, 0.175, 1);
            pointer-events: none;
        }

        .curtain-valance::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 100%;
            height: 15px;
            background: radial-gradient(circle at 10px 0, #ffd700 6px, transparent 7px) repeat-x;
            background-size: 18px 15px;
            filter: drop-shadow(0 3px 3px rgba(0, 0, 0, 0.6));
        }

        #curtain-stage.opened .curtain-valance {
            transform: translateY(-110%);
        }

        /* CURTAIN LEFT & RIGHT PANELS */
        .curtain-panel {
            position: fixed;
            top: 0;
            width: 50vw;
            height: 100vh;
            height: 100dvh;
            z-index: 55;
            transition: transform 2.5s cubic-bezier(0.75, 0.02, 0.18, 1), opacity 0.5s ease 2.2s;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.9);
            pointer-events: none;
        }

        .curtain-panel-left {
            left: 0;
            transform-origin: left top;
            background: 
                repeating-linear-gradient(90deg, 
                    #2e0006 0px, 
                    #6b0312 25px, 
                    #9b111e 50px, 
                    #e63946 75px, 
                    #9b111e 100px, 
                    #6b0312 125px, 
                    #2e0006 150px
                );
            box-shadow: 15px 0 35px rgba(0, 0, 0, 0.7);
        }

        .curtain-panel-right {
            right: 0;
            transform-origin: right top;
            background: 
                repeating-linear-gradient(90deg, 
                    #2e0006 0px, 
                    #6b0312 25px, 
                    #9b111e 50px, 
                    #e63946 75px, 
                    #9b111e 100px, 
                    #6b0312 125px, 
                    #2e0006 150px
                );
            box-shadow: -15px 0 35px rgba(0, 0, 0, 0.7);
        }

        /* TEXTURE & SPECULAR SHINE FOR REALISTIC VELVET */
        .curtain-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.4) 0%, transparent 20%, rgba(0, 0, 0, 0.3) 80%, rgba(0, 0, 0, 0.8) 100%);
            pointer-events: none;
        }

        /* VELVET PLEATS HIGHLIGHT SHINE */
        .curtain-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(90deg, 
                transparent 0px, 
                rgba(255, 255, 255, 0.08) 60px, 
                transparent 120px
            );
            pointer-events: none;
        }

        /* OPENING TRANSFORMS */
        #curtain-stage.opened .curtain-panel-left {
            transform: translateX(-105%) scaleX(0.08);
            opacity: 0.6;
        }

        #curtain-stage.opened .curtain-panel-right {
            transform: translateX(105%) scaleX(0.08);
            opacity: 0.6;
        }

        /* GOLDEN TIE-BACK ROPES */
        .curtain-rope {
            position: fixed;
            bottom: 25%;
            width: 38px;
            height: 100px;
            z-index: 58;
            transition: all 1.8s ease;
            pointer-events: none;
        }
        .curtain-rope-left {
            left: 15px;
            background: radial-gradient(circle, #ffd700 30%, #b8860b 90%);
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
        }
        .curtain-rope-right {
            right: 15px;
            background: radial-gradient(circle, #ffd700 30%, #b8860b 90%);
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
        }
        #curtain-stage.opened .curtain-rope {
            opacity: 0;
            transform: scale(0.5);
        }

        /* ------------------------------------------------------------- */
        /* PREMIUM CIRCULAR LAUNCH BUTTON (REPLACES BULKY TEXT CARD) */
        /* ------------------------------------------------------------- */
        #launch-pedestal {
            position: relative;
            z-index: 70;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            margin: auto;
            padding: 20px;
        }

        #curtain-stage.opened #launch-pedestal {
            opacity: 0;
            transform: scale(0.6);
            pointer-events: none;
        }

        /* CIRCULAR BUTTON INTERACTIVE CONTAINER */
        .circle-btn-container {
            position: relative;
            width: 320px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* ROTATING ORBITAL RINGS */
        .orbital-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Outer dashed golden/amber ring */
        .ring-outer-dashed {
            width: 310px;
            height: 310px;
            border: 2.5px dashed rgba(255, 175, 0, 0.7);
            animation: rotateClockwise 25s linear infinite;
            box-shadow: 0 0 20px rgba(255, 175, 0, 0.25);
        }

        /* Mid arc / radar accents */
        .ring-mid-arcs {
            width: 270px;
            height: 270px;
            border: 2px solid transparent;
            border-top: 2.5px solid #00f0ff;
            border-bottom: 2.5px solid #ffd700;
            animation: rotateCounterClockwise 15s linear infinite;
            filter: drop-shadow(0 0 10px rgba(0, 240, 255, 0.5));
        }

        /* Pulse glow aura */
        .ring-pulse-glow {
            width: 235px;
            height: 235px;
            border: 1.5px solid rgba(255, 215, 0, 0.35);
            animation: pulseRingAura 3s infinite ease-in-out;
        }

        /* Floating Tech Nodes */
        .orbital-node {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #ffb703;
            border-radius: 50%;
            box-shadow: 0 0 10px #ffb703;
            pointer-events: none;
        }
        .node-1 { top: 18px; left: 70px; }
        .node-2 { bottom: 30px; right: 50px; }
        .node-3 { top: 95px; right: 10px; width: 7px; height: 7px; background: #00f0ff; box-shadow: 0 0 10px #00f0ff; }
        .node-4 { bottom: 60px; left: 20px; }

        /* THE MAIN CIRCULAR SPHERE BUTTON */
        .circle-launch-btn {
            position: relative;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 4.5px solid #ff9e00;
            outline: none;
            cursor: pointer;
            background: radial-gradient(circle at 35% 30%, #3b82f6 0%, #1d4ed8 42%, #0a1435 90%);
            box-shadow: 
                0 0 45px rgba(255, 158, 0, 0.65),
                inset 0 0 30px rgba(0, 0, 0, 0.8),
                inset 0 2px 5px rgba(255, 255, 255, 0.6),
                0 20px 45px rgba(0, 0, 0, 0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        /* Glossy 3D Sphere Highlight */
        .btn-gloss-highlight {
            position: absolute;
            top: 6px;
            left: 20%;
            width: 60%;
            height: 38%;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.48) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        /* Inner content alignment */
        .btn-inner-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transform: translateY(2px);
        }

        /* Glowing Golden Power Icon */
        .power-icon-wrap {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .power-icon-svg {
            width: 50px;
            height: 50px;
            stroke: #ffd700;
            filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.9));
            transition: all 0.3s ease;
            animation: iconBreathe 2.5s infinite ease-in-out;
        }

        /* Text: LAUNCH CSMS */
        .circle-btn-text {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 2.5px;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.9), 0 0 14px rgba(255, 255, 255, 0.5);
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        /* HOVER & ACTIVE INTERACTIONS */
        .circle-btn-container:hover .circle-launch-btn {
            transform: scale(1.06);
            border-color: #ffd700;
            box-shadow: 
                0 0 60px rgba(255, 215, 0, 0.85),
                inset 0 0 35px rgba(0, 0, 0, 0.6),
                0 25px 50px rgba(0, 0, 0, 0.95);
        }

        .circle-btn-container:hover .power-icon-svg {
            transform: scale(1.1);
            stroke: #ffffff;
            filter: drop-shadow(0 0 16px rgba(255, 255, 255, 1));
        }

        .circle-btn-container:hover .ring-outer-dashed {
            animation-duration: 12s;
            border-color: #ffd700;
        }

        .circle-btn-container:hover .ring-mid-arcs {
            animation-duration: 8s;
        }

        .circle-btn-container:active .circle-launch-btn {
            transform: scale(0.96);
        }

        /* KEYFRAMES FOR ROTATING RINGS & GLOWS */
        @keyframes rotateClockwise {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes rotateCounterClockwise {
            from { transform: translate(-50%, -50%) rotate(360deg); }
            to { transform: translate(-50%, -50%) rotate(0deg); }
        }

        @keyframes pulseRingAura {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.4; }
            50% { transform: translate(-50%, -50%) scale(1.08); opacity: 0.8; box-shadow: 0 0 25px rgba(255, 215, 0, 0.4); }
        }

        @keyframes iconBreathe {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.8)); }
            50% { transform: scale(1.06); filter: drop-shadow(0 0 16px rgba(255, 215, 0, 1)); }
        }

        /* ------------------------------------------------------------- */
        /* PRESENTATION TOOLBAR (TOP RIGHT CONTROLS) */
        /* ------------------------------------------------------------- */
        .stage-toolbar {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 100;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            max-width: 80vw;
        }

        .tool-btn {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .tool-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 215, 0, 0.5);
            transform: translateY(-1px);
        }

        /* ------------------------------------------------------------- */
        /* CONFETTI CANVAS */
        /* ------------------------------------------------------------- */
        #confetti-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 80;
            pointer-events: none;
        }

        /* GENERAL KEYFRAME ANIMATIONS */
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); border-color: rgba(255, 215, 0, 0.3); }
            50% { box-shadow: 0 0 35px rgba(255, 215, 0, 0.45); border-color: rgba(255, 215, 0, 0.6); }
        }

        @keyframes pulse-green {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.2); }
        }

        @keyframes shimmer-line {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* ------------------------------------------------------------- */
        /* RESPONSIVE DESIGN FOR TABLETS & MOBILES */
        /* ------------------------------------------------------------- */
        @media (max-width: 992px) {
            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .brand-system-title {
                font-size: 24px;
            }
            .portal-glass-card {
                padding: 30px 24px;
            }
        }

        @media (max-width: 600px) {
            #stage-container {
                padding: 10px 0;
            }
            #revealed-content {
                padding: 55px 12px 30px;
            }
            .portal-glass-card {
                padding: 22px 14px;
                border-radius: 18px;
            }
            .launch-badge {
                padding: 6px 14px;
                margin-bottom: 12px;
            }
            .launch-badge span {
                font-size: 11px;
                letter-spacing: 1px;
            }
            .brand-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
                margin-bottom: 10px;
            }
            .brand-logo-wrap {
                width: 64px;
                height: 64px;
                margin: 0 auto;
                border-radius: 16px;
            }
            .brand-logo-wrap img {
                width: 52px;
                height: 52px;
            }
            .brand-text-block {
                text-align: center;
            }
            .brand-org-name {
                font-size: 14px;
                letter-spacing: 2px;
            }
            .brand-system-title {
                font-size: 19px;
                line-height: 1.25;
            }
            .launch-tagline {
                font-size: 13.5px;
                margin: 8px auto 16px;
                line-height: 1.45;
            }
            .feature-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-bottom: 18px;
            }
            .feature-card {
                padding: 12px 14px;
            }
            .feature-icon {
                font-size: 18px;
                margin-bottom: 4px;
            }
            .feature-title {
                font-size: 14px;
            }
            .feature-desc {
                font-size: 12px;
            }
            .cta-action-group {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .btn-gold-launch, .btn-portal-secondary {
                width: 100%;
                justify-content: center;
                padding: 13px 18px;
                font-size: 14px;
            }
            .launch-meta-footer {
                flex-direction: column;
                gap: 6px;
                text-align: center;
                font-size: 12px;
                padding-top: 14px;
            }

            /* Responsive Circular Button on Mobile */
            .circle-btn-container {
                width: 240px;
                height: 240px;
            }
            .ring-outer-dashed {
                width: 230px;
                height: 230px;
            }
            .ring-mid-arcs {
                width: 200px;
                height: 200px;
            }
            .ring-pulse-glow {
                width: 175px;
                height: 175px;
            }
            .circle-launch-btn {
                width: 160px;
                height: 160px;
                border-width: 3.5px;
            }
            .power-icon-wrap {
                width: 40px;
                height: 40px;
            }
            .power-icon-svg {
                width: 36px;
                height: 36px;
            }
            .circle-btn-text {
                font-size: 13px;
                letter-spacing: 1.5px;
            }

            .curtain-valance {
                height: 55px;
            }
            .curtain-valance::after {
                height: 10px;
                bottom: -10px;
                background-size: 14px 10px;
            }
            .curtain-rope {
                display: none;
            }

            /* Toolbar on Mobile */
            .stage-toolbar {
                top: 8px;
                right: 8px;
                gap: 5px;
            }
            .tool-btn {
                padding: 5px 8px;
                font-size: 11px;
                border-radius: 6px;
            }
        }
    </style>
</head>

<body>

    <div id="stage-container">
        <!-- Confetti Canvas -->
        <canvas id="confetti-canvas"></canvas>

        <!-- Ambient Spotlights -->
        <div class="spotlight-left"></div>
        <div class="spotlight-right"></div>

        <!-- STAGE CONTROLS (TOP RIGHT) -->
        <div class="stage-toolbar">
            <button class="tool-btn" id="btn-sound-toggle" onclick="toggleSound()">
                <span id="sound-icon">🔊</span> <span id="sound-label">Sound</span>
            </button>
            <button class="tool-btn" onclick="triggerExtraConfetti()">
                <span>🎊</span> <span>Confetti</span>
            </button>
            <button class="tool-btn" id="btn-replay" onclick="replayLaunch()" style="display:none;">
                <span>🔄</span> <span>Replay</span>
            </button>
            <button class="tool-btn" onclick="toggleFullScreen()">
                <span>⛶</span> <span>Full Screen</span>
            </button>
        </div>

        <!-- ------------------------------------------------------------- -->
        <!-- REVEALED INAUGURATED SYSTEM PORTAL (STAGE BACKDROP) -->
        <!-- ------------------------------------------------------------- -->
        <div id="revealed-content">
            <div class="launch-badge">
                <span class="badge-icon">✦</span>
                <span>Officially Inaugurated &amp; Live</span>
                <span class="badge-icon">✦</span>
            </div>

            <div class="portal-glass-card">
                <div class="brand-header">
                    <div class="brand-logo-wrap">
                        <img src="{{ asset('backAssets/images/logo.png') }}" alt="CSMS Logo">
                    </div>
                    <div class="brand-text-block">
                        <div class="brand-org-name">Textile Committee</div>
                        <h1 class="brand-system-title">Cooperative Society Management System</h1>
                    </div>
                </div>

                <p class="launch-tagline">
                    A modern, unified digital platform empowering cooperative members with precision share tracking, 
                    automated payroll batch deductions, robust loan underwriting, and complete financial audit transparency.
                </p>

                <!-- 4 CORE PILLARS MATRIX -->
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <div class="feature-title">Member Ledger &amp; Shares</div>
                        <div class="feature-desc">Real-time passbooks, automated monthly shares &amp; cumulative savings tracking.</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-title">Batch Deductions</div>
                        <div class="feature-desc">Automated payroll split, Excel reconciliation &amp; monthly ledger posting.</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📑</div>
                        <div class="feature-title">Loan &amp; Surety Engine</div>
                        <div class="feature-desc">Collateral validation, top-up refinancing, and surety liability safeguards.</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <div class="feature-title">Audit &amp; Governance</div>
                        <div class="feature-desc">Monthly transaction locking, historical imports, and annual audit integrity.</div>
                    </div>
                </div>

                <!-- CTA NAVIGATION BUTTONS -->
                <div class="cta-action-group">
                    @auth
                        @php $user = Illuminate\Support\Facades\Auth::user(); @endphp
                        @if ($user && $user->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn-gold-launch">
                                <span>👔 Enter Admin Dashboard</span> <span>➔</span>
                            </a>
                            <a href="{{ route('admin.monthly-due') }}" class="btn-portal-secondary">
                                <span>📊 Monthly Due Report</span>
                            </a>
                        @else
                            <a href="{{ route('member.dashboard') }}" class="btn-gold-launch">
                                <span>👤 Enter Member Dashboard</span> <span>➔</span>
                            </a>
                            <a href="{{ route('member.loan.apply') }}" class="btn-portal-secondary">
                                <span>📝 Apply for Loan</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-portal-secondary">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-gold-launch">
                            <span>🔐 Access CSMS Portal</span> <span>➔</span>
                        </a>
                        <a href="{{ url('/') }}" class="btn-portal-secondary">
                            <span>🏠 Society Home</span>
                        </a>
                    @endauth
                </div>

                <div class="launch-meta-footer">
                    <div>
                        <span class="live-status-dot"></span>
                        <strong>Status:</strong> System Operational &amp; Secured
                    </div>
                    <div>
                        <strong>Inauguration Date:</strong> <span id="live-launch-time"></span>
                    </div>
                    <div>
                        <strong>Version:</strong> 2.0 Enterprise
                    </div>
                </div>
            </div>
        </div>

        <!-- ------------------------------------------------------------- -->
        <!-- THEATRICAL VELVET CURTAINS & CIRCULAR LAUNCH BUTTON -->
        <!-- ------------------------------------------------------------- -->
        <div id="curtain-stage">
            <!-- Top Golden Valance / Pelmet -->
            <div class="curtain-valance"></div>

            <!-- Left and Right Velvet Curtains -->
            <div class="curtain-panel curtain-panel-left"></div>
            <div class="curtain-panel curtain-panel-right"></div>

            <!-- Golden Side Cords -->
            <div class="curtain-rope curtain-rope-left"></div>
            <div class="curtain-rope curtain-rope-right"></div>

            <!-- Center Animated Circular Launch Button -->
            <div id="launch-pedestal">
                <div class="circle-btn-container" onclick="performGrandLaunch()" role="button" tabindex="0" aria-label="Launch CSMS">
                    <!-- Rotating Orbital Tech Rings -->
                    <div class="orbital-ring ring-outer-dashed"></div>
                    <div class="orbital-ring ring-mid-arcs"></div>
                    <div class="orbital-ring ring-pulse-glow"></div>

                    <!-- Ambient Tech Nodes -->
                    <div class="orbital-node node-1"></div>
                    <div class="orbital-node node-2"></div>
                    <div class="orbital-node node-3"></div>
                    <div class="orbital-node node-4"></div>

                    <!-- Main Sphere Button -->
                    <button class="circle-launch-btn" id="btn-main-launch" type="button">
                        <div class="btn-gloss-highlight"></div>
                        <div class="btn-inner-content">
                            <!-- Golden Glowing Power Icon -->
                            <div class="power-icon-wrap">
                                <svg class="power-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                    <line x1="12" y1="2" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <span class="circle-btn-text">LAUNCH CSMS</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------- -->
    <!-- INTERACTIVE SCRIPT: FANFARE AUDIO, CURTAIN PHYSICS & CONFETTI -->
    <!-- ------------------------------------------------------------- -->
    <script>
        let soundEnabled = true;
        let audioCtx = null;

        // Set Live Launch Date
        document.addEventListener('DOMContentLoaded', () => {
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('live-launch-time').textContent = new Date().toLocaleDateString('en-US', dateOptions);
        });

        // Keydown support (Enter / Space) on circular button
        document.addEventListener('keydown', (e) => {
            if ((e.key === 'Enter' || e.key === ' ') && !document.getElementById('curtain-stage').classList.contains('opened')) {
                performGrandLaunch();
            }
        });

        // WEB AUDIO SYNTHESIZER: Ceremonial Brass Fanfare & Chimes
        function playCeremonialFanfare() {
            if (!soundEnabled) return;
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!audioCtx) {
                    audioCtx = new AudioContext();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }

                const now = audioCtx.currentTime;

                // Trumpet/Horn Chords Progression
                const fanfareNotes = [
                    { f: 261.63, start: 0.0, dur: 0.35, type: 'sawtooth', gain: 0.15 }, // C4
                    { f: 392.00, start: 0.0, dur: 0.35, type: 'sawtooth', gain: 0.15 }, // G4
                    { f: 523.25, start: 0.0, dur: 0.35, type: 'sawtooth', gain: 0.18 }, // C5

                    { f: 329.63, start: 0.4, dur: 0.35, type: 'sawtooth', gain: 0.15 }, // E4
                    { f: 392.00, start: 0.4, dur: 0.35, type: 'sawtooth', gain: 0.15 }, // G4
                    { f: 659.25, start: 0.4, dur: 0.35, type: 'sawtooth', gain: 0.18 }, // E5

                    { f: 392.00, start: 0.8, dur: 0.35, type: 'sawtooth', gain: 0.15 }, // G4
                    { f: 523.25, start: 0.8, dur: 0.35, type: 'sawtooth', gain: 0.18 }, // C5
                    { f: 783.99, start: 0.8, dur: 0.35, type: 'sawtooth', gain: 0.20 }, // G5

                    // Grand Triumphant Chord (C Major Grand Fortissimo)
                    { f: 261.63, start: 1.25, dur: 2.8, type: 'triangle', gain: 0.25 }, // C4
                    { f: 329.63, start: 1.25, dur: 2.8, type: 'sawtooth', gain: 0.18 }, // E4
                    { f: 392.00, start: 1.25, dur: 2.8, type: 'sawtooth', gain: 0.20 }, // G4
                    { f: 523.25, start: 1.25, dur: 2.8, type: 'sawtooth', gain: 0.25 }, // C5
                    { f: 1046.50, start: 1.25, dur: 2.8, type: 'sine', gain: 0.18 }     // C6
                ];

                fanfareNotes.forEach(note => {
                    const osc = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    osc.type = note.type;
                    osc.frequency.setValueAtTime(note.f, now + note.start);

                    gainNode.gain.setValueAtTime(0.001, now + note.start);
                    gainNode.gain.exponentialRampToValueAtTime(note.gain, now + note.start + 0.06);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, now + note.start + note.dur);

                    osc.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    osc.start(now + note.start);
                    osc.stop(now + note.start + note.dur + 0.1);
                });

                // Sparkly Chimes cascade
                const chimeFreqs = [1200, 1500, 1800, 2100, 2400, 2700, 3200];
                chimeFreqs.forEach((freq, idx) => {
                    const chOsc = audioCtx.createOscillator();
                    const chGain = audioCtx.createGain();
                    const chTime = now + 1.4 + (idx * 0.12);

                    chOsc.type = 'sine';
                    chOsc.frequency.setValueAtTime(freq, chTime);

                    chGain.gain.setValueAtTime(0.001, chTime);
                    chGain.gain.exponentialRampToValueAtTime(0.08, chTime + 0.02);
                    chGain.gain.exponentialRampToValueAtTime(0.0001, chTime + 0.8);

                    chOsc.connect(chGain);
                    chGain.connect(audioCtx.destination);

                    chOsc.start(chTime);
                    chOsc.stop(chTime + 0.9);
                });

            } catch (e) {
                console.warn('Audio Context init note:', e);
            }
        }

        // TOGGLE SOUND
        function toggleSound() {
            soundEnabled = !soundEnabled;
            document.getElementById('sound-icon').textContent = soundEnabled ? '🔊' : '🔇';
            document.getElementById('sound-label').textContent = soundEnabled ? 'Sound' : 'Muted';
        }

        // FULL SCREEN TOGGLE (FOR PROJECTOR)
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.warn(`Error enabling full-screen: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // GRAND LAUNCH TRIGGER
        function performGrandLaunch() {
            const curtainStage = document.getElementById('curtain-stage');
            const revealedContent = document.getElementById('revealed-content');
            const replayBtn = document.getElementById('btn-replay');

            // 1. Play celebratory fanfare
            playCeremonialFanfare();

            // 2. Open Curtains
            curtainStage.classList.add('opened');

            // 3. Reveal System Portal Content
            setTimeout(() => {
                revealedContent.classList.add('visible');
                replayBtn.style.display = 'inline-flex';
                // Ensure page scrolls to top of revealed content on mobile
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);

            // 4. Launch Massive Confetti Showers
            startConfettiCelebration();
        }

        // REPLAY LAUNCH (FOR PRESENTATION MULTIPLE TIMES)
        function replayLaunch() {
            const curtainStage = document.getElementById('curtain-stage');
            const revealedContent = document.getElementById('revealed-content');
            const replayBtn = document.getElementById('btn-replay');

            revealedContent.classList.remove('visible');
            curtainStage.classList.remove('opened');
            replayBtn.style.display = 'none';

            // Reset Confetti
            particles = [];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // EXTRA CONFETTI TRIGGER
        function triggerExtraConfetti() {
            const isMobile = window.innerWidth <= 600;
            const count1 = isMobile ? 80 : 150;
            const count2 = isMobile ? 40 : 80;
            createConfettiBurst(window.innerWidth / 2, window.innerHeight * 0.4, count1);
            createConfettiBurst(window.innerWidth * 0.25, window.innerHeight * 0.5, count2);
            createConfettiBurst(window.innerWidth * 0.75, window.innerHeight * 0.5, count2);
        }

        // -------------------------------------------------------------
        // HIGH PERFORMANCE CONFETTI ENGINE (HTML5 CANVAS)
        // -------------------------------------------------------------
        const canvas = document.getElementById('confetti-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let animationId = null;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        const CONFETTI_COLORS = [
            '#ffd700', '#ffb703', '#fb8500', '#e63946', '#2a9d8f', 
            '#4361ee', '#4cc9f0', '#7209b7', '#f72585', '#ffffff'
        ];

        class Particle {
            constructor(x, y, speedMult = 1) {
                this.x = x;
                this.y = y;
                this.size = Math.random() * 8 + 4;
                this.color = CONFETTI_COLORS[Math.floor(Math.random() * CONFETTI_COLORS.length)];
                
                const angle = Math.random() * Math.PI * 2;
                const speed = (Math.random() * 10 + 3) * speedMult;
                this.vx = Math.cos(angle) * speed;
                this.vy = Math.sin(angle) * speed - (Math.random() * 4 + 2);

                this.gravity = 0.22;
                this.drag = 0.96;
                this.rotation = Math.random() * 360;
                this.rotationSpeed = (Math.random() - 0.5) * 10;
                this.opacity = 1;
                this.decay = Math.random() * 0.006 + 0.004;
                this.shape = Math.random() > 0.3 ? 'rect' : 'circle';
            }

            update() {
                this.vx *= this.drag;
                this.vy *= this.drag;
                this.vy += this.gravity;

                this.x += this.vx;
                this.y += this.vy;

                this.rotation += this.rotationSpeed;
                this.opacity -= this.decay;
            }

            draw(ctx) {
                if (this.opacity <= 0) return;
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate((this.rotation * Math.PI) / 180);
                ctx.globalAlpha = Math.max(0, this.opacity);
                ctx.fillStyle = this.color;

                if (this.shape === 'rect') {
                    ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size * 0.6);
                } else {
                    ctx.beginPath();
                    ctx.arc(0, 0, this.size / 2, 0, Math.PI * 2);
                    ctx.fill();
                }

                ctx.restore();
            }
        }

        function createConfettiBurst(x, y, count = 80) {
            for (let i = 0; i < count; i++) {
                particles.push(new Particle(x, y));
            }
        }

        function startConfettiCelebration() {
            const isMobile = window.innerWidth <= 600;
            const mainCount = isMobile ? 90 : 160;
            const sideCount = isMobile ? 50 : 100;

            // Wave 1: Center blast
            createConfettiBurst(window.innerWidth / 2, window.innerHeight * 0.45, mainCount);

            // Wave 2: Left and right cannons
            setTimeout(() => {
                createConfettiBurst(window.innerWidth * 0.2, window.innerHeight * 0.4, sideCount);
                createConfettiBurst(window.innerWidth * 0.8, window.innerHeight * 0.4, sideCount);
            }, 350);

            // Wave 3: Top golden shower
            setTimeout(() => {
                const dropCount = isMobile ? 35 : 70;
                for (let i = 0; i < dropCount; i++) {
                    const p = new Particle(Math.random() * window.innerWidth, -10, 0.6);
                    p.vy = Math.random() * 3 + 2;
                    p.color = '#ffd700';
                    particles.push(p);
                }
            }, 800);

            if (!animationId) {
                animateConfetti();
            }
        }

        function animateConfetti() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i];
                p.update();
                p.draw(ctx);

                if (p.opacity <= 0 || p.y > canvas.height + 50) {
                    particles.splice(i, 1);
                }
            }

            if (particles.length > 0) {
                animationId = requestAnimationFrame(animateConfetti);
            } else {
                animationId = null;
            }
        }
    </script>
</body>

</html>
