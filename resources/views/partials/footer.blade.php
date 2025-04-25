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
                    'driver' => 'pdf/DubocqPointage-Conducteurs.pdf',
                    'leader' => 'pdf/DubocqPointage-Dirigeants.pdf',
                    'admin' => 'pdf/DubocqPointage-Comptables.pdf',
                    'super-admin' => 'pdf/DubocqPointage-SuperAdmin.pdf',
                ];

                // Récupération des rôles de l'utilisateur pour déterminer s'il a plusieurs rôles
                $userRoles = [];
                $hasMultipleRoles = false;
                
                if (auth()->check()) {
                    foreach ($guideLinks as $role => $link) {
                        if (auth()->user()->hasRole($role)) {
                            $userRoles[$role] = $link;
                        }
                    }
                    
                    // Vérifier si l'utilisateur a plusieurs rôles
                    $hasMultipleRoles = count($userRoles) > 1;
                }
                @endphp
                
                <li>
                    @if($hasMultipleRoles)
                        <a href="#" class="footer-link support-link" id="open-guide-modal">
                            <span>Guide d'utilisation</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                        </a>
                    @elseif(count($userRoles) === 1)
                        @php
                            $guideLink = reset($userRoles);
                        @endphp
                        <a href="{{ asset($guideLink) }}" target="_blank" class="footer-link support-link">
                            <span>Guide d'utilisation</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                        </a>
                    @else
                        <a href="#" class="footer-link support-link">
                            <span>Guide d'utilisation</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                        </a>
                    @endif
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
        <div class="copyright flex justify-center max-w-7xl mx-auto border-t border-gray-700 py-2">
            <a href="https://informatique-m2i.fr" target="_blank" class="copyright-link pt-2">
                &copy; {{ date('Y') }} - M2i Informatique
            </a>
        </div>
    </div>
    
    <!-- Modal pour le choix du guide -->
    @if($hasMultipleRoles)
    <div id="guide-modal" class="guide-modal">
        <div class="guide-modal-content">
            <div class="guide-modal-header">
                <h3>Choisissez votre guide d'utilisation</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="guide-modal-body">
                <ul class="guide-list">
                    @foreach($userRoles as $role => $link)
                        <li>
                            <a href="{{ asset($link) }}" target="_blank" class="guide-option">
                                @if($role == 'driver')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="guide-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                    <span>Guide conducteur</span>
                                @elseif($role == 'leader')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="guide-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    <span>Guide dirigeant</span>
                                @elseif($role == 'admin')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="guide-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span>Guide comptable</span>
                                @elseif($role == 'super-admin')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="guide-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>Guide super admin</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
</footer>

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

    /* Modal styles */
    .guide-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
    }

    .guide-modal-content {
        position: relative;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        margin: 10% auto;
        padding: 0;
        width: 400px;
        max-width: 90%;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: modal-appear 0.4s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .guide-modal-header {
        padding: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .guide-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #f8f9fa;
        font-weight: 500;
    }

    .close-modal {
        color: #f8f9fa;
        font-size: 1.75rem;
        font-weight: 300;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .close-modal:hover {
        color: #3a86ff;
        transform: rotate(90deg);
    }

    .guide-modal-body {
        padding: 1.25rem;
    }

    .guide-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .guide-list li {
        margin-bottom: 0.75rem;
    }

    .guide-option {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        color: #f8f9fa;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .guide-option:hover {
        background: rgba(58, 134, 255, 0.2);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .guide-icon {
        width: 24px;
        height: 24px;
        margin-right: 0.75rem;
        color: #3a86ff;
    }

    @keyframes modal-appear {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animations */
    @keyframes expand {
        0%, 100% { width: 40px; }
        50% { width: 100%; }
    }

    @keyframes particles-drift {
        0% { background-position: 0 0; }
        100% { background-position: 100px 100px; }
    }

    @keyframes glow-pulse {
        0%, 100% {
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

        .guide-modal-content {
            margin: 20% auto;
            width: 320px;
        }
    }

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
        0%, 100% {
            opacity: 0;
            transform: scale(0.5);
        }
        50% {
            opacity: 1;
            transform: scale(1.5);
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
        
        // Modal functionality
        const modal = document.getElementById('guide-modal');
        const openModalBtn = document.getElementById('open-guide-modal');
        const closeModalBtn = document.querySelector('.close-modal');
        
        if (openModalBtn && modal) {
            // Open modal when clicking the button
            openModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
            });
            
            // Close modal when clicking the close button
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                    document.body.style.overflow = ''; // Re-enable scrolling
                });
            }
            
            // Close modal when clicking outside of it
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = ''; // Re-enable scrolling
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = ''; // Re-enable scrolling
                }
            });
        }
    });
</script>