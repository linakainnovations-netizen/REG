<?php
$pageTitle = "St. Charles Lwanga Regiment | Community Portal";
include_once 'includes/header.php';
$quickUpdates = [];
try {
    $quickUpdates = $pdo->query("SELECT message, created_at FROM live_updates WHERE status = 'published' AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY is_pinned DESC, created_at DESC LIMIT 2")->fetchAll();
} catch (Throwable $e) { $quickUpdates = []; }
?>

<!-- Hero Section with Premium Slider -->
<section class="hero-premium" style="height: 600px; position: relative; overflow: hidden; color: white;">
    <!-- Animated Slides -->
    <div class="hero-slider-wrapper">
        <div class="hero-slide" style="background-image: url('assets/images/other/homepage.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/other/home.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/other/home_9.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/other/home4.jpeg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/other/parish.jpg');"></div>
        <!-- Fixed overlay for text contrast -->
        <div style="position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)); z-index: 2;"></div>
    </div>

    <div class="container" style="position: relative; z-index: 10; height: 100%; display: flex; align-items: center;">
        <div class="grid" style="grid-template-columns: 1.2fr 0.8fr; gap: 4rem; align-items: center; width: 100%;">
            <div>
                <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2rem; display: inline-block;">
                    Official Parish Management System
                </span>
                <h1 style="font-size: 4rem; font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem;">One Faith, One People, One Portal.</h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 3rem; max-width: 600px;">Experience a more connected church community. Manage your groups, stay updated with news, and participate in Parish governance effortlessly.</p>
                
                <div class="flex" style="gap: 1rem;">
                    <a href="register" class="btn" style="background: white; color: var(--primary-color); padding: 1rem 2rem; border-radius: 0.5rem; font-size: 1.1rem; font-weight: 700;">Join the Portal</a>
                    <a href="ministries" class="btn" style="background: rgba(255,255,255,0.1); color: white; padding: 1rem 2rem; border-radius: 0.5rem; font-size: 1.1rem; font-weight: 700; border: 1px solid rgba(255,255,255,0.3);">Explore Ministries</a>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <div class="hero-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 2.5rem; border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.2); width: 100%; max-width: 400px;">
                    <h3 class="mb-4" style="color: white; font-weight: 700;">Quick Announcements</h3>
                    <?php if (!empty($quickUpdates)): foreach ($quickUpdates as $q): ?>
                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                        <p style="font-size: 0.875rem; opacity: 0.8;"><?php echo date('M j, H:i', strtotime($q['created_at'])); ?></p>
                        <p style="font-weight: 600;"><?php echo htmlspecialchars(mb_substr($q['message'], 0, 90)); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <a href="live-updates" style="color: white; font-weight: 700; font-size: 0.875rem;">View all live updates →</a>
                    <?php else: ?>
                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                        <p style="font-size: 0.875rem; opacity: 0.8;">Today</p>
                        <p style="font-weight: 600;">Liturgy Schedule Update</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1);">
                        <p style="font-size: 0.875rem; opacity: 0.8;">Tomorrow</p>
                        <p style="font-weight: 600;">Youth Choir Rehearsal</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Decorative Circle -->
    <div style="position: absolute; width: 600px; height: 600px; background: rgba(255,255,255,0.05); border-radius: 50%; top: -100px; right: -200px; z-index: 1;"></div>
</section>

<!-- Live / Giving / Bulletins quick banner -->
<section class="container mt-4">
    <div class="grid grid-cols-3" style="gap: 1.5rem;">
        <a href="media" class="card" style="padding: 1.5rem; background: #0f172a; color: white; text-decoration: none; border: none;">
            <span class="badge" style="background: #ef4444; color: white; padding: 0.3rem 0.8rem; font-size: 0.75rem;"><i class="fas fa-circle mr-2"></i>LIVE</span>
            <h3 class="mt-4" style="color: white;">Watch Mass Live</h3>
            <p style="opacity: 0.8; font-size: 0.9rem;">Facebook & YouTube streams, Mass times and replays.</p>
        </a>
        <a href="giving" class="card" style="padding: 1.5rem; background: #ecfdf5; text-decoration: none; border: 1px solid #a7f3d0;">
            <i class="fas fa-hand-holding-heart fa-2x" style="color: #059669;"></i>
            <h3 class="mt-4">Give via MoMo</h3>
            <p class="text-muted" style="font-size: 0.9rem;">MTN / Airtel to 0975255734, submit Txn ID.</p>
        </a>
        <a href="bulletins" class="card" style="padding: 1.5rem; background: #f5f3ff; text-decoration: none; border: 1px solid #ddd6fe;">
            <i class="fas fa-newspaper fa-2x" style="color: #7c3aed;"></i>
            <h3 class="mt-4">Sunday Bulletin</h3>
            <p class="text-muted" style="font-size: 0.9rem;">Download this week's readings & notices.</p>
        </a>
    </div>
</section>

<!-- Stats Section -->
<section style="background: white; padding: 3rem 0; margin-top: -3rem; position: relative; z-index: 10;">
    <div class="container">
        <div class="card" style="box-shadow: var(--shadow-lg); border-radius: 1rem; padding: 2rem; display: flex; justify-content: space-around; align-items: center; border: none; flex-wrap: wrap; gap: 2rem;">
            <div class="text-center">
                <h2 style="color: var(--primary-color); font-size: 2.5rem;">500+</h2>
                <p class="text-muted">Parish Members</p>
            </div>
            <div style="width: 1px; height: 50px; background: var(--border-color);" class="mobile-hide"></div>
            <div class="text-center">
                <h2 style="color: var(--secondary-color); font-size: 2.5rem;">12</h2>
                <p class="text-muted">Active Groups</p>
            </div>
            <div style="width: 1px; height: 50px; background: var(--border-color);" class="mobile-hide"></div>
            <div class="text-center">
                <h2 style="color: var(--accent-color); font-size: 2.5rem;">24/7</h2>
                <p class="text-muted">Portal Access</p>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Info Section -->
<section class="container mt-4 mb-4" style="padding: 4rem 0;">
    <div class="text-center mb-4">
        <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main);">Seamless Parish Governance</h2>
        <p class="text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">From Liturgy to Finance, our platform ensures every department within the Parish operates with transparency and efficiency.</p>
    </div>

    <div class="grid grid-cols-3 mt-4">
        <div class="card" style="border: none; background: #f1f5f9; padding: 2.5rem;">
            <i class="fas fa-bullhorn fa-2x mb-4" style="color: var(--primary-color);"></i>
            <h3 class="mb-2">Announcement System</h3>
            <p class="text-muted">Multi-layer approval workflow ensuring only verified information reaches you.</p>
        </div>
        <div class="card" style="border: none; background: #fffbeb; padding: 2.5rem;">
            <i class="fas fa-users fa-2x mb-4" style="color: var(--secondary-color);"></i>
            <h3 class="mb-2">Group Hub</h3>
            <p class="text-muted">Manage membership for Choirs, SCCs, and Lay Groups in one centralized place.</p>
        </div>
        <div class="card" style="border: none; background: #ecfdf5; padding: 2.5rem;">
            <i class="fas fa-tasks fa-2x mb-4" style="color: var(--accent-color);"></i>
            <h3 class="mb-2">Duty Scheduling</h3>
            <p class="text-muted">Automated schedules for liturgical tasks like reading, singing, and offertory.</p>
        </div>
    </div>
</section>

    <!-- Community Highlights -->
    <section class="mt-4 mb-4" style="background: #f8fafc; padding: 4rem 0; border-radius: 2rem;">
        <div class="container text-center">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 3rem;">Our Vibrant Community</h2>
            <div class="grid grid-cols-3">
                <div class="card" style="padding: 0; overflow: hidden; border: none; box-shadow: var(--shadow-md);">
                    <img src="assets/images/other/parish.jpg" style="width: 100%; height: 250px; object-fit: cover;">
                    <div class="p-4 text-left">
                        <h4 style="margin-bottom: 0.5rem; color: var(--primary-color);">Parish Facilities</h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Modern spaces dedicated to prayer, liturgical formation, and community gatherings.</p>
                    </div>
                </div>
                <div class="card" style="padding: 0; overflow: hidden; border: none; box-shadow: var(--shadow-md);">
                    <img src="assets/images/other/parish_council.jpg" style="width: 100%; height: 250px; object-fit: cover;">
                    <div class="p-4 text-left">
                        <h4 style="margin-bottom: 0.5rem; color: var(--secondary-color);">Lay Movements</h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Experience unity and fellowship within our various Lay Groups and SCC ministries.</p>
                    </div>
                </div>
                <div class="card" style="padding: 0; overflow: hidden; border: none; box-shadow: var(--shadow-md);">
                    <img src="assets/images/other/youths.jpg" style="width: 100%; height: 250px; object-fit: cover; object-position: top;">
                    <div class="p-4 text-left">
                        <h4 style="margin-bottom: 0.5rem; color: var(--accent-color);">Liturgy & Prayer</h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Dedication to spiritual growth through daily mass and weekly liturgical devotion.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Heritage: Patron & Martyrs -->
    <section class="container mt-4 mb-4" style="padding: 2rem 0 4rem 0;">
        <div class="card" style="padding: 0; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
            <div class="grid grid-cols-2" style="gap: 0; align-items: stretch;">
                <div>
                    <img src="assets\images\LwangaNcompanionMartyrs.jpg" alt="The 20 future martyrs at Bukumbi Mission, 1885" style="width: 100%; height: 100%; min-height: 320px; object-fit: cover;">
                </div>
                <div class="p-4" style="padding: 2.5rem; background: white;">
                    <span class="badge" style="background: #fef3c7; color: #92400e; padding: 0.3rem 0.8rem; font-size: 0.75rem; font-weight: 700;">OUR HERITAGE</span>
                    <h2 class="mt-4" style="font-size: 2rem; font-weight: 800; color: var(--text-main);">St. Charles Lwanga & the Martyrs</h2>
                    <p class="text-muted" style="line-height: 1.8; margin-top: 1rem;">This photograph was taken at Bukumbi Mission (Mwanza) in September 1885. The 20 future martyrs above had gone to welcome and congratulate their newly appointed Bishop to Uganda, Msgr. Leon Livinhac — among them St. Charles Lwanga, patron of our Regiment.</p>
                    <p class="text-muted" style="line-height: 1.8;">Our parish Regiment carries their witness forward. The full history of the church and the Regiment will be published here.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="container mt-4 mb-4" style="padding: 4rem 0;">
        <div class="text-center mb-4">
            <h2 style="font-size: 2rem; font-weight: 800;">Member Perspectives</h2>
        </div>
        <div class="grid grid-cols-2" style="gap: 3rem;">
            <div class="flex" style="gap: 1.5rem; align-items: center;">
                <div style="width: 80px; height: 80px; min-width: 80px; border-radius: 50%; border: 4px solid white; box-shadow: var(--shadow-md); background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800;">M</div>
                <div>
                    <p style="font-style: italic; color: var(--text-muted); line-height: 1.6;">"The portal has made it so easy to stay updated with our SCC sweeping roster. Transparency in our finance reports is what we truly needed!"</p>
                    <p class="mt-2" style="font-weight: 700; color: var(--primary-color);">Mary Zulu - SCC Chairperson</p>
                </div>
            </div>
            <div class="flex" style="gap: 1.5rem; align-items: center;">
                <div style="width: 80px; height: 80px; min-width: 80px; border-radius: 50%; border: 4px solid white; box-shadow: var(--shadow-md); background: var(--secondary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800;">J</div>
                <div>
                    <p style="font-style: italic; color: var(--text-muted); line-height: 1.6;">"I love being able to see when our Choir is next in the Singing Cycle. The automated notifications keep us connected wherever we are."</p>
                    <p class="mt-2" style="font-weight: 700; color: var(--primary-color);">Joseph Phiri - Choir Member</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map & Contact Section -->
    <section style="margin-top: 4rem; position: relative; height: 450px; border-radius: 2rem; overflow: hidden; box-shadow: var(--shadow-lg);">
        <img src="assets/images/other/footer.jpg" style="width: 100%; height: 100%; object-fit: cover; position: absolute; z-index: 1;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.4) 100%); z-index: 2;"></div>
        <div class="container" style="position: relative; z-index: 10; height: 100%; display: flex; align-items: center;">
            <div style="max-width: 500px; color: white;">
                <h2 style="font-size: 3rem; font-weight: 800; line-height: 1.1; margin-bottom: 2rem;">Always Here <br>for Our People.</h2>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <p class="flex" style="align-items: center; gap: 1rem;"><i class="fas fa-envelope text-primary"></i> office@stcharleslwangaregiment.org</p>
                    <p class="flex" style="align-items: center; gap: 1rem;"><i class="fas fa-phone text-primary"></i> +260 975 255 734</p>
                    <p class="flex" style="align-items: center; gap: 1rem;"><i class="fas fa-map-marker-alt text-primary"></i> Chitukuko Road, Lusaka, Zambia</p>
                </div>
                <a href="event_request" class="btn btn-primary mt-4" style="padding: 1rem 2.5rem; border-radius: 3rem;">Inquire About Sacraments</a>
            </div>
        </div>
    </section>
</div>

<?php include_once 'includes/footer.php'; ?>

<style>
@media (max-width: 768px) {
    .grid { grid-template-columns: 1fr !important; }
    .hero-premium h1 { font-size: 2.5rem !important; }
    .mobile-hide { display: none; }
}
</style>
