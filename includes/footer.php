    </main>

    <footer style="background: linear-gradient(rgba(15, 23, 42, 0.92), rgba(15, 23, 42, 0.92)), url('<?php echo BASE_URL; ?>assets/images/other/footer.jpg'); background-size: cover; background-position: center; color: white; padding: 4rem 0 2rem 0; margin-top: 4rem; position: relative; overflow: hidden;">
        <div class="container">
            <div class="footer-layout-grid">
                <!-- About Column -->
                <div>
                    <h3 style="color: white; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo BASE_URL; ?>assets/images/other/main_logo.png" alt="St. Charles Lwanga Logo" style="height: 40px; width: auto; object-fit: contain;"> St. Charles Lwanga Regiment
                    </h3>
                    <img src="<?php echo BASE_URL; ?>assets/images/other/mass.jpeg" alt="Holy Mass at St. Charles Lwanga Regiment Parish" style="width: 100%; height: 140px; object-fit: cover; border-radius: 0.75rem; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15);">
                    <p style="color: #94a3b8; line-height: 1.6; font-size: 0.95rem;">
                        Empowering our Parish through technology, transparency, and communion. One faith, one portal. Serving Regiment Parish since 1940 — 80 Years celebrated 19th January 2020.
                    </p>
                    <p style="color: #e2e8f0; font-size: 0.85rem; margin-top: 1rem;">
                        <i class="fas fa-church mr-2" style="color: #f59e0b;"></i> Sun Masses 06:30, 09:00, 11:00 · Sat 17:00
                    </p>
                </div>

                <!-- Explore Hub -->
                <div>
                    <h4 style="color: white; margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Explore Ministries</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; padding: 0;">
                        <li><a href="<?php echo BASE_URL; ?>ministries" style="color: #94a3b8; text-decoration: none;">Ministries Hub</a></li>
                        <li><a href="<?php echo BASE_URL; ?>announcements" style="color: #94a3b8; text-decoration: none;">Announcements</a></li>
                        <li><a href="<?php echo BASE_URL; ?>rosters" style="color: #94a3b8; text-decoration: none;">Liturgy Rosters</a></li>
                        <li><a href="<?php echo BASE_URL; ?>offertory" style="color: #94a3b8; text-decoration: none;">Financial Transparency</a></li>
                    </ul>
                </div>

                <!-- Admins Portal -->
                <div>
                    <h4 style="color: white; margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Join the Portal</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; padding: 0;">
                        <li><a href="<?php echo BASE_URL; ?>login" style="color: #94a3b8; text-decoration: none;">Leader Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>join" style="color: #94a3b8; text-decoration: none;">Member Registration</a></li>
                        <li><a href="<?php echo BASE_URL; ?>roq" style="color: #94a3b8; text-decoration: none;">Procurement Hub (ROQ)</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact" style="color: #94a3b8; text-decoration: none;">Contact the Parish</a></li>
                    </ul>
                </div>

                <!-- Map Integration -->
                <div>
                    <h4 style="color: white; margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Find Us</h4>
                    <div id="leaflet-map" style="width: 100%; height: 180px; border-radius: 0.75rem; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); z-index: 1;">
                        <!-- Leaflet map will render here -->
                    </div>
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-top: 1rem;">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #ef4444;"></i> Chitukuko Road, Lusaka, Zambia
                    </p>
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">
                        <i class="fas fa-phone mr-2" style="color: #25D366;"></i> 0975 255 734
                    </p>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 4rem; padding-top: 2rem; display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 0.875rem; flex-wrap: wrap; gap: 1rem;">
                <p>&copy; <?php echo date('Y'); ?> St. Charles Lwanga Regiment Parish. All rights reserved. | Developed by <a href="https://denfas-simfukwe202.github.io/denfas_simfukwe/" target="_blank" style="text-decoration: none; font-weight: 800; letter-spacing: 0.5px;">
                    <span style="color: var(--primary-light);">DENFAS</span> <span style="color: var(--secondary-color);">SIMFUKWE</span>
                </a></p>
                <div class="flex" style="gap: 1.5rem; font-size: 1.25rem;">
                    <a href="https://web.facebook.com/groups/539879469476464/events" target="_blank" style="color: white;" title="Main Parish Group"><i class="fab fa-facebook-square"></i></a>
                    <a href="https://web.facebook.com/share/v/18VGFnWuAt/" target="_blank" style="color: white;" title="Choir Events"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@st.pauls.parish.c?_r=1&_t=ZS-95RRaLkfX8f" target="_blank" style="color: white;" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" target="_blank" style="color: white;" title="Parish WhatsApp Community"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Help Chatbot Widget -->
    <div id="help-chatbot" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <button id="chatbot-toggle" style="background: var(--primary-color); color: white; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 1.5rem; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: transform 0.3s; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-headset"></i>
        </button>
        <div id="chatbot-panel" style="display: none; position: absolute; bottom: 80px; right: 0; width: 300px; background: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden; font-family: sans-serif;">
            <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 1rem; text-align: center;">
                <h4 style="margin: 0; font-size: 1.1rem; color: white;">Need Help?</h4>
                <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">Contact Developer</p>
            </div>
            <div style="padding: 1rem;">
                <a href="https://wa.me/260975255734" target="_blank" style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; color: #334155; text-decoration: none; border-radius: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 1.5rem;"></i>
                    <span>WhatsApp <br><small>0975255734</small></span>
                </a>
                <a href="tel:0975255734" style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; color: #334155; text-decoration: none; border-radius: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                    <i class="fas fa-phone-alt" style="color: #3b82f6; font-size: 1.25rem; width: 1.5rem; text-align: center;"></i>
                    <span>Call or SMS <br><small>0975255734</small></span>
                </a>
                <a href="https://denfas-simfukwe202.github.io/denfas_simfukwe/" target="_blank" style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; color: #334155; text-decoration: none; border-radius: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                    <i class="fas fa-globe" style="color: #8b5cf6; font-size: 1.25rem; width: 1.5rem; text-align: center;"></i>
                    <span>View Portfolio <br><small>Denfas Simfukwe</small></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Leaflet Map Initialization
            var mapElement = document.getElementById('leaflet-map');
            if (mapElement) {
                // Coordinates for Chitukuko Road, Lusaka, Zambia approximately
                var map = L.map('leaflet-map').setView([-15.3875, 28.3228], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                L.marker([-15.3875, 28.3228]).addTo(map)
                    .bindPopup('<b>St. Charles Lwanga Regiment Parish</b><br>Chitukuko Road, Lusaka.').openPopup();
            }

            // Chatbot Toggle Logic
            var chatbotToggle = document.getElementById('chatbot-toggle');
            var chatbotPanel = document.getElementById('chatbot-panel');
            if (chatbotToggle && chatbotPanel) {
                chatbotToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (chatbotPanel.style.display === 'none' || chatbotPanel.style.display === '') {
                        chatbotPanel.style.display = 'block';
                    } else {
                        chatbotPanel.style.display = 'none';
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!chatbotPanel.contains(e.target) && e.target !== chatbotToggle) {
                        chatbotPanel.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <?php if (isset($extraJS)): ?>
        <script src="<?php echo BASE_URL; ?>assets/js/<?php echo $extraJS; ?>.js"></script>
    <?php endif; ?>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo BASE_URL; ?>sw.js')
                    .then(registration => {
                        console.log('PWA: ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(err => {
                        console.log('PWA: ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</body>
</html>
