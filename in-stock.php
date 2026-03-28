<?php
require_once __DIR__ . '/admin/config.php';
$data  = loadUnits();
$units = $data['units'] ?? [];

function unitImage(array $u): string {
    if (!empty($u['image'])) return htmlspecialchars($u['image']);
    return htmlspecialchars(defaultImage($u['model']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>In Stock Units – Hotomobil USA</title>
  <meta name="description" content="Gladiator truck campers available now in the USA. Browse our in-stock units located in Orange County and Houston, Texas." />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    .stock-section { padding: 70px 0 90px; background: #f4f4f4; }
    .stock-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px; margin-top: 48px;
    }
    .stock-card {
      background: #fff; border-radius: 4px; overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
      display: flex; flex-direction: column;
      transition: transform .3s, box-shadow .3s;
    }
    .stock-card:hover { transform: translateY(-4px); box-shadow: 0 14px 40px rgba(0,0,0,.13); }
    .stock-card-img { position: relative; background: #1a1a1a; aspect-ratio: 16/11; overflow: hidden; }
    .stock-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .stock-card:hover .stock-card-img img { transform: scale(1.04); }
    .badge-sold { position: absolute; top: 12px; left: 12px; background: #e03030; color: #fff;
      font-family: 'Saira Condensed', sans-serif; font-size: .72rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 10px; border-radius: 2px; }
    .badge-demo { position: absolute; top: 12px; left: 12px; background: #e07800; color: #fff;
      font-family: 'Saira Condensed', sans-serif; font-size: .72rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 10px; border-radius: 2px; }
    .stock-card-body { padding: 20px 20px 0; flex: 1; display: flex; flex-direction: column; }
    .stock-card-body h3 { font-family: 'Saira Condensed', sans-serif; font-size: 1.35rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1a1a1a; margin-bottom: 6px; }
    .stock-card-subtitle { font-size: .82rem; color: #555; line-height: 1.55; margin-bottom: 4px; }
    .stock-card-vin { font-family: 'Saira Condensed', sans-serif; font-size: .78rem; font-weight: 600;
      letter-spacing: 1px; color: #999; margin-bottom: 14px; }
    .stock-card-footer { display: flex; align-items: center; justify-content: flex-end;
      padding: 12px 20px 14px; border-top: 1px solid #eee; margin-top: auto; }
    .btn-detail { display: inline-flex; align-items: center; gap: 6px; background: #29a8e0;
      color: #fff; font-family: 'Saira Condensed', sans-serif; font-size: .78rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; padding: 8px 18px; border-radius: 3px;
      transition: background .2s; }
    .btn-detail:hover { background: #1d8fc0; }
    .upgrades-wrap { border-top: 1px solid #eee; }
    .upgrades-toggle { width: 100%; display: flex; align-items: center; justify-content: space-between;
      padding: 12px 20px; background: none; border: none; cursor: pointer;
      font-family: 'Saira Condensed', sans-serif; font-size: .88rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; color: #333; transition: color .2s; }
    .upgrades-toggle:hover { color: #29a8e0; }
    .upgrades-toggle i { font-size: .75rem; transition: transform .25s; color: #999; }
    .upgrades-toggle.open i { transform: rotate(180deg); }
    .upgrades-body { display: none; padding: 4px 20px 14px; }
    .upgrades-body.open { display: block; }
    .upgrades-body ul { list-style: disc; padding-left: 18px; }
    .upgrades-body ul li { font-size: .82rem; color: #555; line-height: 1.8; list-style: disc; }
    .upgrades-body .no-upgrade { font-size: .82rem; color: #aaa; font-style: italic; }
    @media(max-width:900px){ .stock-grid{grid-template-columns:repeat(2,1fr)} }
    @media(max-width:560px){ .stock-grid{grid-template-columns:1fr} }
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
        <li class="nav-item"><a href="in-stock.php" class="nav-link nav-link-instock active">IN STOCK UNITS</a></li>
        <li class="nav-item"><a href="contact.html" class="nav-link">CONTACT</a></li>
      </ul>
    </div>
  </nav>

  <section class="page-hero">
    <div class="page-hero-bg">
      <img src="https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/gorsel_2025-11-09_162807841.png" alt="Gladiators In The USA" />
    </div>
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content container">
      <p class="breadcrumb"><a href="index.html">Home</a><span>/</span>In Stock Units</p>
      <h1>Gladiators In The USA</h1>
      <p>Browse our in-stock Gladiator truck campers currently located across the United States — ready for immediate delivery.</p>
    </div>
  </section>

  <section class="stock-section">
    <div class="container">
      <h2 class="section-title text-center">Available Units</h2>
      <p class="section-subtitle text-center">All units are located in the USA. Contact us for pricing, financing, and shipping.</p>

      <?php if ($units): ?>
      <div class="stock-grid">
        <?php foreach ($units as $u):
          $img = unitImage($u);
          $status = $u['status'] ?? 'available';
        ?>
        <div class="stock-card">
          <div class="stock-card-img">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($u['model']) ?>" loading="lazy" />
            <?php if ($status === 'sold'): ?>
              <span class="badge-sold">Sold</span>
            <?php elseif ($status === 'demo'): ?>
              <span class="badge-demo">Demo</span>
            <?php endif; ?>
          </div>
          <div class="stock-card-body">
            <h3><?= htmlspecialchars($u['model']) ?></h3>
            <p class="stock-card-subtitle">
              <?= htmlspecialchars($u['year']) ?> &bull;
              <?= htmlspecialchars($u['color']) ?> &bull;
              <?= htmlspecialchars($u['location']) ?>
            </p>
            <p class="stock-card-vin">Product / Vin #: <?= htmlspecialchars($u['vin']) ?></p>
          </div>
          <div class="stock-card-footer">
            <a href="unit-detail.php?id=<?= urlencode($u['id']) ?>" class="btn-detail">
              Detail <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
          <div class="upgrades-wrap">
            <button class="upgrades-toggle" aria-expanded="false">
              Upgrades <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="upgrades-body">
              <?php if (!empty($u['upgrades'])): ?>
                <ul>
                  <?php foreach ($u['upgrades'] as $upg): ?>
                    <li><?= htmlspecialchars($upg) ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="no-upgrade">Not Available</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="text-align:center;color:#888;margin-top:60px">No units currently listed. Check back soon.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="cta-banner">
    <div class="container">
      <h2>Interested in a Unit?</h2>
      <p>Get in touch for pricing, financing options, and shipping anywhere in the USA.</p>
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
          <li><a href="compatible-trucks.html">Compatible Trucks</a></li>
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
  <script>
    document.querySelectorAll('.upgrades-toggle').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var body = this.nextElementSibling;
        var isOpen = body.classList.contains('open');
        body.classList.toggle('open', !isOpen);
        this.classList.toggle('open', !isOpen);
        this.setAttribute('aria-expanded', String(!isOpen));
      });
    });
  </script>
</body>
</html>
