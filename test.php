<?php
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Studio | Création de sites web</title>
    <meta name="description" content="Site vitrine moderne pour une agence web créative.">
    <style>
        :root {
            --bg: #0b1020;
            --bg-soft: #111827;
            --panel: rgba(17, 24, 39, 0.8);
            --card: #f8fafc;
            --text: #e5eefb;
            --muted: #9fb0ca;
            --primary: #7c3aed;
            --primary-2: #22c55e;
            --accent: #38bdf8;
            --border: rgba(148, 163, 184, 0.2);
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.35);
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: radial-gradient(circle at top, #111827 0%, #0b1020 40%, #090d18 100%);
            color: var(--text);
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            background: rgba(11, 16, 32, 0.7);
            border-bottom: 1px solid var(--border);
        }

        .nav {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.04em;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            box-shadow: var(--shadow);
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .nav-cta,
        .button,
        .button-alt {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 999px;
            padding: 0.9rem 1.5rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .nav-cta,
        .button {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            box-shadow: 0 14px 30px rgba(124, 58, 237, 0.35);
        }

        .button-alt {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: var(--text);
        }

        .button:hover,
        .button-alt:hover,
        .nav-cta:hover {
            transform: translateY(-2px);
        }

        .hero {
            padding: 72px 0 48px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 40px;
        }

        .eyebrow {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: var(--accent);
            background: rgba(56, 189, 248, 0.08);
            border-radius: 999px;
            font-size: 0.8rem;
            letter-spacing: 0.04em;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2.6rem, 4vw, 5rem);
            line-height: 1.05;
            margin: 0 0 18px;
            letter-spacing: -0.04em;
        }

        .lead {
            font-size: 1.08rem;
            color: var(--muted);
            max-width: 620px;
            margin: 0 0 28px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
        }

        .mini-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            color: var(--muted);
        }

        .mini-stats strong {
            display: block;
            color: var(--text);
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .hero-visual {
            position: relative;
            min-height: 520px;
        }

        .dashboard {
            position: absolute;
            inset: 60px 0 0 30px;
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.96), rgba(15, 23, 42, 0.78));
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .dashboard-head {
            height: 54px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #fb7185;
        }

        .dot:nth-child(2) { background: #fbbf24; }
        .dot:nth-child(3) { background: #4ade80; }

        .dashboard-body {
            padding: 24px 20px 18px;
        }

        .chart {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            align-items: end;
            gap: 12px;
            height: 180px;
            padding: 20px 10px 8px;
        }

        .bar {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(180deg, var(--accent), var(--primary));
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .bar:nth-child(1) { height: 40%; }
        .bar:nth-child(2) { height: 60%; }
        .bar:nth-child(3) { height: 78%; }
        .bar:nth-child(4) { height: 52%; }
        .bar:nth-child(5) { height: 88%; }
        .bar:nth-child(6) { height: 74%; }
        .bar:nth-child(7) { height: 96%; }

        .panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 18px;
        }

        .metric,
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
        }

        .metric-label {
            display: block;
            color: var(--muted);
            font-size: 0.78rem;
            margin-bottom: 8px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.04em;
        }

        .floating-card {
            position: absolute;
            right: 0;
            bottom: 40px;
            width: 210px;
            padding: 18px 16px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .floating-card .badge {
            display: inline-block;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.12);
            color: #86efac;
            font-size: 0.7rem;
            margin-bottom: 8px;
        }

        .floating-card strong {
            font-size: 1.5rem;
            display: block;
            margin: 8px 0;
        }

        section {
            padding: 70px 0;
        }

        .section-heading {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 42px;
        }

        .section-heading h2 {
            font-size: clamp(2rem, 3vw, 3rem);
            margin: 0 0 14px;
            letter-spacing: -0.04em;
        }

        .section-heading p {
            margin: 0;
            color: var(--muted);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 24px;
            color: var(--text);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
        }

        .icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(56, 189, 248, 0.2));
            font-size: 1.5rem;
            margin-bottom: 18px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
        }

        .card p {
            margin: 0;
            color: var(--muted);
        }

        .about {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            align-items: center;
            gap: 40px;
        }

        .about-panel {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.12), rgba(56, 189, 248, 0.08));
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 28px;
        }

        .about-visual {
            position: relative;
            min-height: 440px;
        }

        .about-visual .stack {
            position: absolute;
            width: 82%;
            padding: 26px;
            border-radius: 24px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .about-visual .stack:nth-child(1) {
            top: 20px;
            left: 30px;
            transform: rotate(-5deg);
        }

        .about-visual .stack:nth-child(2) {
            right: 0;
            bottom: 0;
            transform: rotate(6deg);
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .tag {
            padding: 0.5rem 0.75rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.8rem;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .price-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 28px 22px;
            position: relative;
        }

        .price-card.featured {
            background: linear-gradient(180deg, rgba(124, 58, 237, 0.18), rgba(15, 23, 42, 0.76));
            border-color: rgba(124, 58, 237, 0.55);
            transform: translateY(-8px);
        }

        .popular {
            position: absolute;
            top: 18px;
            right: 18px;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            font-size: 0.7rem;
            border: 1px solid rgba(34, 197, 94, 0.35);
        }

        .price-card h3 {
            margin-top: 0;
            font-size: 1.4rem;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.05em;
            margin: 12px 0;
        }

        .price span {
            font-size: 1rem;
            color: var(--muted);
            font-weight: 400;
        }

        .price-card ul {
            list-style: none;
            padding: 0;
            margin: 20px 0 24px;
            color: var(--muted);
        }

        .price-card li {
            position: relative;
            padding-left: 26px;
            margin-bottom: 12px;
        }

        .price-card li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4ade80;
            font-weight: 700;
        }

        .cta-band {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.14), rgba(56, 189, 248, 0.14));
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 36px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .cta-band h3 {
            margin: 0 0 8px;
            font-size: clamp(1.8rem, 2.6vw, 2.6rem);
            letter-spacing: -0.04em;
        }

        .cta-band p {
            margin: 0;
            color: var(--muted);
        }

        footer {
            padding: 32px 0 54px;
            color: var(--muted);
            border-top: 1px solid var(--border);
            background: rgba(9, 13, 24, 0.7);
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        @media (max-width: 900px) {
            .hero-grid,
            .about,
            .features,
            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .nav {
                justify-content: space-between;
            }

            .hero-visual {
                min-height: 460px;
            }

            .cta-band {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 540px) {
            .hero {
                padding-top: 52px;
            }

            .hero-actions,
            .mini-stats {
                flex-direction: column;
                align-items: flex-start;
            }

            h1 {
                font-size: 2.5rem;
            }

            .dashboard {
                inset: 40px 0 0 0;
            }

            .floating-card {
                position: static;
                margin: 20px 0 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container nav">
            <a href="#home" class="brand" aria-label="Nova Studio">
                <span class="brand-mark">N</span>
                <span>Nova Studio</span>
            </a>

            <nav class="nav-links" aria-label="Navigation principale">
                <a href="#services">Services</a>
                <a href="#about">À propos</a>
                <a href="#pricing">Tarifs</a>
                <a href="#contact">Contact</a>
            </nav>

            <a href="#contact" class="nav-cta">Démarrer</a>
        </div>
    </header>

    <main id="home">
        <section class="hero">
            <div class="container hero-grid">
                <div>
                    <span class="eyebrow">Agence digitale</span>
                    <h1>Des sites web qui font vendre.</h1>
                    <p class="lead">
                        Nous concevons des expériences web premium, rapides et convertissantes pour
                        les marques qui veulent grandir sans friction.
                    </p>

                    <div class="hero-actions">
                        <a href="#pricing" class="button">Voir les offres</a>
                        <a href="#services" class="button-alt">Découvrir nos services</a>
                    </div>

                    <div class="mini-stats">
                        <div>
                            <strong>120+</strong>
                            projets livrés
                        </div>
                        <div>
                            <strong>4.9/5</strong>
                            satisfaction clients
                        </div>
                        <div>
                            <strong>48h</strong>
                            pour un devis
                        </div>
                    </div>
                </div>

                <div class="hero-visual" aria-label="Aperçu du tableau de bord de projet">
                    <div class="dashboard">
                        <div class="dashboard-head">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>
                        <div class="dashboard-body">
                            <div class="chart" aria-hidden="true">
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </div>

                            <div class="panel">
                                <div class="metric">
                                    <span class="metric-label">Conversion</span>
                                    <span class="metric-value">+38%</span>
                                </div>
                                <div class="metric">
                                    <span class="metric-label">Trafic</span>
                                    <span class="metric-value">+72%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card">
                        <span class="badge">ROI</span>
                        <div class="info">Performance mensuelle</div>
                        <strong>+245%</strong>
                        <div>Sur les 6 derniers mois</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Ce que nous faisons</span>
                    <h2>Une stratégie web complète pour chaque étape</h2>
                    <p>Du design à la performance, nous construisons des expériences qui inspirent confiance et convertissent.</p>
                </div>

                <div class="features">
                    <article class="card">
                        <div class="icon">✦</div>
                        <h3>Branding visuel</h3>
                        <p>Identité de marque cohérente, direction artistique et design premium adaptés à votre audience.</p>
                    </article>

                    <article class="card">
                        <div class="icon">⚡</div>
                        <h3>Sites ultra performants</h3>
                        <p>Vitesse d’exécution, bonnes pratiques SEO, expérience mobile fluide et navigation claire.</p>
                    </article>

                    <article class="card">
                        <div class="icon">📈</div>
                        <h3>Conversion & growth</h3>
                        <p>Stratégies de parcours client, appels à l’action efficaces et optimisation continue des résultats.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="about">
            <div class="container about">
                <div class="about-panel">
                    <span class="eyebrow">Notre approche</span>
                    <h2>On crée le site qui reflète votre ambition.</h2>
                    <p class="lead">
                        Nous associons stratégie business, design élégant et développement solide pour créer des interfaces qui attirent, rassurent et convertissent.
                    </p>

                    <div class="tag-list" aria-label="Compétences">
                        <span class="tag">UX/UI Design</span>
                        <span class="tag">SEO</span>
                        <span class="tag">E-commerce</span>
                        <span class="tag">Landing Pages</span>
                        <span class="tag">WordPress</span>
                        <span class="tag">Performance</span>
                    </div>
                </div>

                <div class="about-visual" aria-label="Exemples de projets">
                    <div class="stack">
                        <h3>Projet premium</h3>
                        <p>Refonte de la présence digitale d’une marque lifestyle.</p>
                        <ul>
                            <li>Design sur mesure</li>
                            <li>Optimisation des ventes</li>
                        </ul>
                    </div>
                    <div class="stack">
                        <h3>Résultats</h3>
                        <p>+91% d’augmentation de demandes qualifiées.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Tarifs</span>
                    <h2>Des formules pensées pour votre croissance</h2>
                    <p>Choisissez l’offre qui correspond à votre besoin, de la présence digitale à la croissance digitale.</p>
                </div>

                <div class="pricing-grid">
                    <article class="price-card">
                        <h3>Essentiel</h3>
                        <div class="price">390€ <span>/ site</span></div>
                        <ul>
                            <li>1 page d'accueil</li>
                            <li>Design responsive</li>
                            <li>Formulaire de contact</li>
                            <li>Optimisation basique SEO</li>
                        </ul>
                        <a href="#contact" class="button-alt">Choisir</a>
                    </article>

                    <article class="price-card featured">
                        <span class="popular">Populaire</span>
                        <h3>Professionnel</h3>
                        <div class="price">890€ <span>/ site</span></div>
                        <ul>
                            <li>5 sections sur mesure</li>
                            <li>Intégration CMS</li>
                            <li>Optimisation UX/UI</li>
                            <li>Support technique 1 mois</li>
                        </ul>
                        <a href="#contact" class="button">Choisir</a>
                    </article>

                    <article class="price-card">
                        <h3>Premium</h3>
                        <div class="price">1 690€ <span>/ site</span></div>
                        <ul>
                            <li>Projet sur mesure complet</li>
                            <li>Stratégie marketing incluse</li>
                            <li>Landing pages et funnels</li>
                            <li>Accompagnement avancé</li>
                        </ul>
                        <a href="#contact" class="button-alt">Choisir</a>
                    </article>
                </div>
            </div>
        </section>

        <section id="contact">
            <div class="container">
                <div class="cta-band">
                    <div>
                        <h3>Prêt à lancer votre nouveau site ?</h3>
                        <p>Parlons de votre projet et créons ensemble une présence digitale forte.</p>
                    </div>
                    <a href="mailto:hello@novastudio.example" class="button">hello@novastudio.example</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-row">
            <div>© <?= htmlspecialchars($currentYear, ENT_QUOTES, 'UTF-8'); ?> Nova Studio</div>
            <div>Design & développement sur mesure</div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach((link) => {
            link.addEventListener('click', (event) => {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    event.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
