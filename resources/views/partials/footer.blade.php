{{-- resources/views/components/footer.blade.php --}}

<footer class="futuristic-footer">
    <div class="footer-background">
        <div class="particles"></div>
        <div class="glow"></div>
    </div>

    <div class="footer-container">
        <div class="footer-column">
            <h3 class="footer-title">À propos</h3>
            <ul class="footer-links">
                <li><a href="https://informatique-m2i.fr" class="footer-link">Notre entreprise</a></li>
                <li><a href="https://informatique-m2i.fr" class="footer-link">Notre équipe</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3 class="footer-title">Services</h3>
            <ul class="footer-links">
                <li><a href="#" class="footer-link">Infogérance</a></li>
                <li><a href="#" class="footer-link">Développement</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3 class="footer-title">Support</h3>
            <ul class="footer-links">
                @php
                $guideLinks = [
                'driver' => 'https://app-eu1.hubspot.com/guide-creator/g/VoaAqRlcgt',
                'leader' => 'https://app-eu1.hubspot.com/guide-creator/g/J9185nhnRF',
                'admin' => '#',
                'super-admin' => '#',
                ];

                $guideLink = '#'; // Lien par défaut

                if (auth()->check()) {
                // Attribuer le lien en fonction du rôle
                foreach ($guideLinks as $role => $link) {
                if (auth()->user()->hasRole($role)) {
                $guideLink = $link;
                break; // Arrêter après avoir trouvé le premier rôle correspondant
                }
                }
                }
                @endphp
                <li>
                    <a href="{{ $guideLink }}" target="_blank" class="footer-link support-link" target="_blank">
                        <span>Guide d'utilisation</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="mailto:lucas@informatique-m2i.fr" class="footer-link support-link" target="_blank">
                        <span>Contacter le support</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>

        <div class="footer-column">
            <h3 class="footer-title">Suivez-nous</h3>
            <div class="social-icons">
                <a href="https://informatique-m2i.fr" class="social-icon" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>

                </a>
                <a href="https://www.youtube.com/@m2i36" class="social-icon" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="256" height="180" viewBox="0 0 256 180" class="size-6">
                        <path fill="#f00" d="M250.346 28.075A32.18 32.18 0 0 0 227.69 5.418C207.824 0 127.87 0 127.87 0S47.912.164 28.046 5.582A32.18 32.18 0 0 0 5.39 28.24c-6.009 35.298-8.34 89.084.165 122.97a32.18 32.18 0 0 0 22.656 22.657c19.866 5.418 99.822 5.418 99.822 5.418s79.955 0 99.82-5.418a32.18 32.18 0 0 0 22.657-22.657c6.338-35.348 8.291-89.1-.164-123.134" />
                        <path fill="#fff" d="m102.421 128.06l66.328-38.418l-66.328-38.418z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="copyright flex justify-end w-full">
            <a href="https://informatique-m2i.fr" target="_blank" class="copyright-link">
                &copy; M2i Informatique
            </a>
        </div>
    </div>
</footer>

{{-- N'oubliez pas d'inclure Font Awesome dans votre layout principal pour les icônes --}}
{{-- Vous pouvez l'ajouter via CDN ou npm --}}

{{-- Styles pour le footer futuriste --}}
<style>
    .futuristic-footer {
        position: relative;
        background: linear-gradient(135deg, #14213d, #000);
        color: #f8f9fa;
        overflow: hidden;
        padding: 3rem 0 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Background animations */
    .footer-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 30px 30px;
        animation: particles-drift 60s linear infinite;
    }

    .glow {
        position: absolute;
        bottom: -50px;
        left: -10%;
        width: 120%;
        height: 100px;
        background: linear-gradient(to top, rgba(58, 134, 255, 0.2), transparent);
        filter: blur(20px);
        animation: glow-pulse 8s ease-in-out infinite;
    }

    /* Container and columns */
    .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 2;
    }

    .footer-column {
        margin-bottom: 1.5rem;
    }

    /* Titles */
    .footer-title {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
        position: relative;
        display: inline-block;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        height: 2px;
        width: 40px;
        background: #ff006e;
        transform-origin: left;
        animation: expand 3s ease-in-out infinite;
    }

    /* Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-link {
        color: #f8f9fa;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        padding-left: 0;
    }

    .footer-link:hover {
        color: #8338ec;
        padding-left: 10px;
    }

    .footer-link::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 1px;
        background: #8338ec;
        transition: all 0.3s ease;
        opacity: 0;
    }

    .footer-link:hover::before {
        width: 8px;
        left: 0;
        opacity: 1;
    }

    /* Style pour les liens de support */
    .support-link {
        display: flex;
        align-items: center;
    }

    .support-link svg {
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .support-link:hover svg {
        opacity: 1;
        transform: translateX(3px);
    }

    /* Style pour le copyright */
    .copyright-link {
        color: #f8f9fa;
        opacity: 0.8;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .copyright-link:hover {
        color: #3a86ff;
        opacity: 1;
    }

    /* Social Icons */
    .social-icons {
        display: flex;
        gap: 0.8rem;
        margin-bottom: 1.5rem;
    }

    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #f8f9fa;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .social-icon:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: all 0.5s ease;
    }

    .social-icon:hover:before {
        left: 100%;
    }

    .social-icon:hover {
        background: #3a86ff;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(58, 134, 255, 0.4);
    }

    /* Newsletter */
    .newsletter {
        margin-top: 1.5rem;
    }

    .newsletter h4 {
        font-size: 1rem;
        margin-bottom: 0.8rem;
    }

    .newsletter-input {
        display: flex;
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .newsletter-input input {
        flex: 1;
        padding: 0.8rem 1.2rem;
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: #f8f9fa;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.3s ease;
    }

    .newsletter-input input:focus {
        background: rgba(255, 255, 255, 0.1);
    }

    .newsletter-button {
        background: linear-gradient(135deg, #3a86ff, #8338ec);
        border: none;
        color: white;
        padding: 0 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .newsletter-button:hover {
        background: linear-gradient(135deg, #8338ec, #ff006e);
    }

    /* Footer Bottom */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 2rem;
        padding-top: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 2rem;
        padding-right: 2rem;
        position: relative;
        z-index: 2;
    }

    .copyright {
        font-size: 0.85rem;
        opacity: 0.7;
        margin-bottom: 1rem;
    }

    .footer-bottom-links {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .footer-bottom-links a {
        color: #f8f9fa;
        text-decoration: none;
        font-size: 0.85rem;
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #3a86ff;
        opacity: 1;
    }

    /* Animations */
    @keyframes expand {

        0%,
        100% {
            width: 40px;
        }

        50% {
            width: 100%;
        }
    }

    @keyframes particles-drift {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: 100px 100px;
        }
    }

    @keyframes glow-pulse {

        0%,
        100% {
            opacity: 0.5;
            transform: translateY(0);
        }

        50% {
            opacity: 0.8;
            transform: translateY(-20px);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }

        .footer-bottom-links {
            justify-content: center;
        }
    }
</style>

<script>
    // Animation supplémentaire pour les particules du footer
    document.addEventListener('DOMContentLoaded', function() {
        // Création des étoiles/particules animées
        const footer = document.querySelector('.futuristic-footer');
        const particlesContainer = document.querySelector('.particles');

        for (let i = 0; i < 50; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            star.style.left = `${Math.random() * 100}%`;
            star.style.top = `${Math.random() * 100}%`;
            star.style.animationDuration = `${3 + Math.random() * 7}s`;
            star.style.animationDelay = `${Math.random() * 5}s`;
            particlesContainer.appendChild(star);
        }

        // Effet parallaxe léger
        footer.addEventListener('mousemove', function(e) {
            const moveX = (e.clientX / window.innerWidth - 0.5) * 20;
            const moveY = (e.clientY / window.innerHeight - 0.5) * 20;

            particlesContainer.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });
</script>

<style>
    /* Styles supplémentaires pour les étoiles/particules */
    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: twinkle linear infinite;
    }

    @keyframes twinkle {

        0%,
        100% {
            opacity: 0;
            transform: scale(0.5);
        }

        50% {
            opacity: 1;
            transform: scale(1.5);
        }
    }
</style>