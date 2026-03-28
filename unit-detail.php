<?php
require_once __DIR__ . '/admin/config.php';

$id   = trim($_GET['id'] ?? '');
$unit = $id ? unitById($id) : null;

if (!$unit) {
    header('Location: in-stock.php');
    exit;
}

$status      = $unit['status'] ?? 'available';
$img         = !empty($unit['image']) ? htmlspecialchars($unit['image']) : htmlspecialchars(defaultImage($unit['model']));
$statusLabel = ['available' => 'Available', 'sold' => 'Sold', 'demo' => 'Demo'];
$statusColor = ['available' => '#2ecc71',   'sold' => '#e74c3c', 'demo' => '#e07800'];
$sl          = $statusLabel[$status] ?? $status;
$sc          = $statusColor[$status] ?? '#888';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($unit['model']) ?> – <?= htmlspecialchars($unit['vin']) ?> – Hotomobil USA</title>
  <meta name="description" content="<?= htmlspecialchars($unit['model']) ?>, <?= htmlspecialchars($unit['year']) ?>, <?= htmlspecialchars($unit['color']) ?>, located in <?= htmlspecialchars($unit['location']) ?>." />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    .detail-section { padding: 70px 0 90px; background: #f4f4f4; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; }
    .detail-img-wrap { border-radius: 4px; overflow: hidden; background: #1a1a1a;
      aspect-ratio: 4/3; box-shadow: 0 8px 40px rgba(0,0,0,.15); }
    .detail-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .detail-info { display: flex; flex-direction: column; gap: 20px; }
    .detail-badge { display: inline-block; padding: 5px 14px; border-radius: 2px;
      font-family: 'Saira Condensed', sans-serif; font-size: .78rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; }
    .detail-model { font-family: 'Saira Condensed', sans-serif; font-size: clamp(2rem,5vw,3rem);
      font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #1a1a1a;
      line-height: 1; }
    .detail-meta { display: flex; flex-direction: column; gap: 8px; }
    .detail-meta-row { display: flex; align-items: center; gap: 10px; font-size: .9rem; color: #555; }
    .detail-meta-row i { color: #29a8e0; width: 18px; text-align: center; }
    .detail-vin { font-family: 'Saira Condensed', sans-serif; font-size: .85rem; font-weight: 600;
      letter-spacing: 1.5px; color: #999; padding: 10px 14px; background: #eee;
      border-radius: 3px; display: inline-block; }
    .detail-price-box { background: #1a1a1a; border-radius: 4px; padding: 22px 24px; }
    .detail-price-label { font-family: 'Saira Condensed', sans-serif; font-size: .72rem;
      font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #888; margin-bottom: 6px; }
    .detail-price { font-family: 'Saira Condensed', sans-serif; font-size: clamp(2rem,4vw,2.6rem);
      font-weight: 800; color: #29a8e0; letter-spacing: 1px; }
    .detail-price-note { font-size: .78rem; color: #666; margin-top: 6px; }
    .detail-upgrades { background: #fff; border-radius: 4px; padding: 22px 24px;
      border: 1px solid #e5e5e5; }
    .detail-upgrades h4 { font-family: 'Saira Condensed', sans-serif; font-size: .88rem;
      font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #1a1a1a;
      margin-bottom: 14px; }
    .detail-upgrades ul { list-style: none; padding: 0; }
    .detail-upgrades ul li { display: flex; align-items: flex-start; gap: 10px;
      font-size: .88rem; color: #444; padding: 7px 0; border-bottom: 1px solid #f0f0f0; }
    .detail-upgrades ul li:last-child { border-bottom: none; }
    .detail-upgrades ul li i { color: #29a8e0; margin-top: 2px; flex-shrink: 0; }
    .detail-cta { display: flex; gap: 14px; flex-wrap: wrap; }
    .btn-inquire { display: inline-flex; align-items: center; gap: 8px;
      background: #29a8e0; color: #fff; font-family: 'Saira Condensed', sans-serif;
      font-size: .88rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
      padding: 14px 28px; border-radius: 4px; transition: background .2s; }
    .btn-inquire:hover { background: #1d8fc0; }
    .btn-back { display: inline-flex; align-items: center; gap: 8px;
      border: 2px solid #ccc; color: #555; font-family: 'Saira Condensed', sans-serif;
      font-size: .88rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
      padding: 13px 24px; border-radius: 4px; transition: all .2s; }
    .btn-back:hover { border-color: #29a8e0; color: #29a8e0; }
    .sold-overlay { background: #fff0f0; border: 1px solid #e74c3c; border-radius: 4px;
      padding: 16px 20px; color: #c0392b; font-size: .88rem; font-weight: 500;
      display: flex; align-items: center; gap: 10px; }
    @media(max-width:860px){ .detail-grid{grid-template-columns:1fr} }
  </style>
</head>
<body>

  <nav class="navbar solid" id="navbar">
    <div class="nav-container">
      <a href="index.html" class="nav-logo">
        <img src="https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/logo-beyaz.png" alt="Hotomobil USA" />
      </a>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
      <ul class="nav-menu" id="navMenu">
        <li class="nav-item"><a href="index.html" class="nav-link">HOME</a></li>
        <li class="nav-item has-dropdown">
          <a href="truck-campers.html" class="nav-link">TRUCK CAMPERS <i class="fa-solid fa-chevron-down"></i></a>
          <ul class="dropdown">
            <li><a href="gladiator-xl.html">Gladiator XL</a></li>
            <li><a href="cyberglad-premium.html">Cyberglad Premium</a></li>
            <li><a href="gladiator-s-premium.html">Gladiator S Premium</a></li>
            <li><a href="gladiator-xle.html">Gladiator XLE</a></li>
            <li><a href="gladiator-l.html">Gladiator L</a></li>
          </ul>
        </li>
        <li class="nav-item"><a href="compatible-trucks.html" class="nav-link">COMPATIBLE TRUCKS</a></li>
        <li class="nav-item"><a href="about.html" class="nav-link">ABOUT HOTOMOBIL</a></li>
        <li class="nav-item"><a href="in-stock.php" class="nav-link nav-link-instock">IN STOCK UNITS</a></li>
        <li class="nav-item"><a href="contact.html" class="nav-link">CONTACT</a></li>
      </ul>
    </div>
  </nav>

  <section class="page-hero">
    <div class="page-hero-bg">
      <img src="https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/gorsel_2025-11-09_162807841.png" alt="" />
    </div>
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content container">
      <p class="breadcrumb">
        <a href="index.html">Home</a><span>/</span>
        <a href="in-stock.php">In Stock Units</a><span>/</span>
        <?= htmlspecialchars($unit['model']) ?>
      </p>
      <h1><?= htmlspecialchars($unit['model']) ?></h1>
      <p><?= htmlspecialchars($unit['year']) ?> &bull; <?= htmlspecialchars($unit['color']) ?> &bull; <?= htmlspecialchars($unit['location']) ?></p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <div class="detail-grid">

        <!-- Left: Image -->
        <div>
          <div class="detail-img-wrap">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($unit['model']) ?>" />
          </div>
        </div>

        <!-- Right: Info -->
        <div class="detail-info">

          <div>
            <span class="detail-badge"
              style="background:<?= $sc ?>22;color:<?= $sc ?>;border:1px solid <?= $sc ?>55">
              <?= htmlspecialchars($sl) ?>
            </span>
          </div>

          <h1 class="detail-model"><?= htmlspecialchars($unit['model']) ?></h1>

          <div class="detail-meta">
            <div class="detail-meta-row"><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($unit['year']) ?></div>
            <div class="detail-meta-row"><i class="fa-solid fa-palette"></i> <?= htmlspecialchars($unit['color']) ?></div>
            <div class="detail-meta-row"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($unit['location']) ?></div>
          </div>

          <div>
            <span class="detail-vin">Product / Vin #: <?= htmlspecialchars($unit['vin']) ?></span>
          </div>

          <?php if ($status === 'available' || $status === 'demo'): ?>
          <div class="detail-price-box">
            <p class="detail-price-label">Price</p>
            <p class="detail-price">
              <?= !empty($unit['price']) ? htmlspecialchars($unit['price']) : 'Contact for Price' ?>
            </p>
            <?php if (!empty($unit['price'])): ?>
            <p class="detail-price-note">Price may vary. Contact us for final quote and availability.</p>
            <?php endif; ?>
          </div>
          <?php elseif ($status === 'sold'): ?>
          <div class="sold-overlay">
            <i class="fa-solid fa-circle-xmark"></i>
            This unit has been sold. Contact us to inquire about similar available units.
          </div>
          <?php endif; ?>

          <?php if (!empty($unit['upgrades'])): ?>
          <div class="detail-upgrades">
            <h4>Upgrades Included</h4>
            <ul>
              <?php foreach ($unit['upgrades'] as $upg): ?>
                <li><i class="fa-solid fa-check"></i><?= htmlspecialchars($upg) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <div class="detail-cta">
            <?php if ($status !== 'sold'): ?>
            <a href="contact.html?unit=<?= urlencode($unit['vin']) ?>" class="btn-inquire">
              <i class="fa-solid fa-envelope"></i> Request a Quote
            </a>
            <?php endif; ?>
            <a href="in-stock.php" class="btn-back">
              <i class="fa-solid fa-arrow-left"></i> All Units
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>

  <section class="cta-banner">
    <div class="container">
      <h2>Have Questions?</h2>
      <p>Our team is ready to help with pricing, shipping, and financing options.</p>
      <div class="cta-buttons">
        <a href="contact.html" class="btn btn-cta btn-lg">Contact Us</a>
        <a href="truck-campers.html" class="btn btn-outline btn-lg">View All Models</a>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-instagram">
      <a href="https://instagram.com" target="_blank" rel="noopener">
        <i class="fa-brands fa-instagram"></i> Follow us on Instagram
      </a>
    </div>
    <div class="footer-main">
      <div class="footer-logo-col">
        <a href="index.html">
          <img src="https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/logo-beyaz.png" alt="Hotomobil USA" class="footer-logo" />
        </a>
        <p class="footer-tagline">Premium truck campers engineered for American pickup trucks.</p>
      </div>
      <div class="footer-col">
        <h4>Truck Campers</h4>
        <ul>
          <li><a href="gladiator-xl.html">Gladiator XL</a></li>
          <li><a href="cyberglad-premium.html">Cyberglad Premium</a></li>
          <li><a href="gladiator-s-premium.html">Gladiator S Premium</a></li>
          <li><a href="gladiator-xle.html">Gladiator XLE</a></li>
          <li><a href="gladiator-l.html">Gladiator L</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="truck-campers.html">All Models</a></li>
          <li><a href="in-stock.php">In Stock Units</a></li>
          <li><a href="about.html">About Hotomobil</a></li>
          <li><a href="contact.html">Contact Us</a></li>
        </ul>
      </div>
      <div class="footer-col footer-contact-col">
        <h4>Contact</h4>
        <p><i class="fa-solid fa-envelope"></i> <a href="mailto:info@gemoutdoor.com">info@gemoutdoor.com</a></p>
        <div class="footer-socials">
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 Hotomobil USA. All rights reserved.</p>
    </div>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>
