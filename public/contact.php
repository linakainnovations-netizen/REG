<?php
$pageTitle = "Contact the Parish";
include_once 'includes/header.php';

$success = $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General enquiry');
    $message = trim($_POST['message'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone ?: null, $email, $subject, $message]);
            $success = "Thank you, $name! Your message was received — the parish office will respond soon.";
        } catch (PDOException $e) {
            $error = strpos($e->getMessage(), "doesn't exist") !== false
                ? "Message inbox not set up yet. Please run database_auth_contact_updates.sql, or call 0975 255 734."
                : "Could not send. Please call 0975 255 734.";
        }
    } else {
        $error = "Please fill your name, a valid email and your message.";
    }
}
?>
<section class="hero-premium" style="background: linear-gradient(rgba(15,23,42,0.78), rgba(15,23,42,0.78)), url('<?php echo BASE_URL; ?>assets/images/other/church.jpeg'); background-size: cover; background-position: center; color: white; padding: 5rem 0 4rem 0;">
    <div class="container text-center">
        <span style="display: inline-block; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); padding: 0.4rem 1.1rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 1.25rem;">Get in touch</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem;">Contact the Parish</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto; font-size: 1.1rem;">Sacrament enquiries, office visits, or anything else — we usually respond within one working day.</p>
        <div class="mt-4 flex" style="gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <a href="tel:0975255734" class="btn" style="background: white; color: #0f172a; font-weight: 700; padding: 0.7rem 1.4rem; border-radius: 2rem;"><i class="fas fa-phone mr-2"></i>0975 255 734</a>
            <a href="https://wa.me/260975255734" target="_blank" class="btn" style="background: #25D366; color: white; font-weight: 700; padding: 0.7rem 1.4rem; border-radius: 2rem;"><i class="fab fa-whatsapp mr-2"></i>WhatsApp Us</a>
        </div>
    </div>
</section>

<div class="container" style="margin-top: -2.5rem; position: relative; z-index: 5;">
    <div class="grid grid-cols-3">
        <div class="card" style="padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start; border: none; box-shadow: var(--shadow-md);">
            <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-map-marker-alt"></i></div>
            <div><h4 style="margin-bottom: 0.25rem;">Visit Us</h4><p class="text-muted" style="font-size: 0.9rem; margin: 0;">Chitukuko Road, Lusaka<br>Mon–Fri 08:00–16:00<br>Sat 08:00–12:00</p></div>
        </div>
        <div class="card" style="padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start; border: none; box-shadow: var(--shadow-md);">
            <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-phone"></i></div>
            <div><h4 style="margin-bottom: 0.25rem;">Call / WhatsApp</h4><p class="text-muted" style="font-size: 0.9rem; margin: 0;">0975 255 734<br>Office line &amp; WhatsApp<br>Mon–Sat</p></div>
        </div>
        <div class="card" style="padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start; border: none; box-shadow: var(--shadow-md);">
            <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #eff6ff; color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-church"></i></div>
            <div><h4 style="margin-bottom: 0.25rem;">Mass Times</h4><p class="text-muted" style="font-size: 0.9rem; margin: 0;">Sun 06:30 · 09:00 · 11:00<br>Sat 17:00 · Daily 06:00</p></div>
        </div>
    </div>
</div>

<div class="container mt-4 mb-4">
    <div class="grid" style="grid-template-columns: 1fr 1.1fr; gap: 2rem; align-items: stretch;">
        <div style="position: relative; border-radius: 1.25rem; overflow: hidden; box-shadow: var(--shadow-md); min-height: 100%;">
            <img src="<?php echo BASE_URL; ?>assets/images/other/home4.jpeg" alt="Parish sports and fellowship group" style="width: 100%; height: 100%; min-height: 420px; object-fit: cover; display: block;">
            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem 1.5rem 1.5rem 1.5rem; background: linear-gradient(transparent, rgba(15,23,42,0.9)); color: white;">
                <p style="margin: 0; font-weight: 700; font-size: 1.1rem;">One Faith, One Family</p>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; opacity: 0.85;">Fellowship beyond Sunday — sports, youth and lay groups.</p>
            </div>
        </div>
        <div class="card" style="padding: 2.25rem; border: none; box-shadow: var(--shadow-md); border-radius: 1.25rem;">
            <h3 style="font-size: 1.4rem; font-weight: 800;">Send a message</h3>
            <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1.5rem;">Fields marked * are required.</p>
            <?php if ($success): ?><div style="background: #d1fae5; color: #065f46; padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; align-items: flex-start;"><i class="fas fa-check-circle" style="margin-top: 0.15rem;"></i><span><?php echo htmlspecialchars($success); ?></span></div><?php endif; ?>
            <?php if ($error): ?><div style="background: #fee2e2; color: #991b1b; padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; align-items: flex-start;"><i class="fas fa-exclamation-circle" style="margin-top: 0.15rem;"></i><span><?php echo htmlspecialchars($error); ?></span></div><?php endif; ?>
            <form method="POST">
                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">FULL NAME *</label>
                        <input type="text" name="name" placeholder="e.g. Mary Zulu" required style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">PHONE</label>
                        <input type="text" name="phone" placeholder="0975..." style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem;">
                    </div>
                </div>
                <div class="mt-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">EMAIL *</label>
                    <input type="email" name="email" placeholder="you@example.com" required style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem;">
                </div>
                <div class="mt-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">SUBJECT</label>
                    <select name="subject" style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem; background: white;">
                        <option>General enquiry</option>
                        <option>Baptism</option>
                        <option>First Communion / Confirmation</option>
                        <option>Marriage</option>
                        <option>Funeral / Memorial</option>
                        <option>Joining a ministry</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">MESSAGE *</label>
                    <textarea name="message" rows="5" placeholder="How can we help?" required style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem; resize: vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-4" style="width: 100%; padding: 1rem; border-radius: 0.75rem; font-size: 1rem;"><i class="fas fa-paper-plane mr-2"></i>Send Message</button>
            </form>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>
<style>
@media (max-width: 768px) {
    .grid { grid-template-columns: 1fr !important; }
    section.hero-premium h1 { font-size: 2rem !important; }
}
input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
</style>
