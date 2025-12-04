import { useEffect, useRef } from 'react';
import { getThemeAssetUrl } from '@/lib/utils';
import '@/styles/genmap-background.css';

// Text prompts that will cycle
const prompts = [
    "Create a discipleship training video that sparks movements in unreached communities...",
    "Generate an engaging video teaching biblical principles for multiplying disciples...",
    "Produce a testimony video showing how media catalyzes church planting movements...",
    "Make an interactive video equipping believers to share the Gospel through digital tools...",
    "Create a training series on facilitating discovery Bible studies in oral cultures...",
    "Generate content showing how one faithful disciple can multiply into thousands..."
];

interface Particle {
    element: HTMLDivElement;
    x: number;
    y: number;
    targetX: number;
    targetY: number;
    duration: number;
    startTime: number;
    size: number;
    opacity: number;
}

type Platform = 'desktop' | 'tablet' | 'mobile';

// Performance: Check if animations should be disabled
function shouldDisableAnimations(): boolean {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export default function GenMapBackground() {
    const containerRef = useRef<HTMLDivElement>(null);
    const textPromptRef = useRef<HTMLDivElement>(null);
    const particlesContainerRef = useRef<HTMLDivElement>(null);
    const frameContainerRef = useRef<HTMLDivElement>(null);
    const playerScreenRef = useRef<HTMLDivElement>(null);
    const youtubeLayerRef = useRef<HTMLDivElement>(null);
    const platformRef = useRef<Platform>('desktop');
    const isVisibleRef = useRef(true);
    
    const currentPromptIndexRef = useRef(0);
    const currentCharIndexRef = useRef(0);
    const isTypingRef = useRef(false);
    const particlesRef = useRef<Particle[]>([]);
    const frameCountRef = useRef(0);
    const maxFrames = 6;
    const animationFrameRef = useRef<number>();

    useEffect(() => {
        // Performance: Skip all animations if reduced motion preferred
        if (shouldDisableAnimations()) return;
        // Particle class
        const spawnParticle = (type = 'data', forceLeft = false) => {
            if (!particlesContainerRef.current || !textPromptRef.current || !playerScreenRef.current || !containerRef.current) return;

            const element = document.createElement('div');
            element.className = `particle ${type}`;
            
            const textRect = textPromptRef.current.getBoundingClientRect();
            const containerRect = containerRef.current.getBoundingClientRect();
            
            const x = textRect.left - containerRect.left + Math.random() * textRect.width;
            const y = textRect.top - containerRect.top + Math.random() * textRect.height;
            
            const targetY = type === 'create' 
                ? containerRect.height * 0.15
                : containerRect.height * 0.5;
            
            // 60% of particles go left, 40% go right (or force left if specified)
            let targetX;
            if (forceLeft || Math.random() < 0.6) {
                // Target left side of screen
                targetX = containerRect.width * 0.2 + (Math.random() - 0.5) * 200;
            } else {
                // Target right side of screen
                targetX = containerRect.width * 0.75 + (Math.random() - 0.5) * 100;
            }
            
            const particle: Particle = {
                element,
                x,
                y,
                targetX,
                targetY,
                duration: 2000 + Math.random() * 1000,
                startTime: performance.now(),
                size: 3 + Math.random() * 4,
                opacity: 0
            };
            
            element.style.width = `${particle.size}px`;
            element.style.height = `${particle.size}px`;
            element.style.left = `${particle.x}px`;
            element.style.top = `${particle.y}px`;
            element.style.opacity = '0';
            element.style.transform = `translateZ(0)`; // GPU acceleration
            
            particlesContainerRef.current.appendChild(element);
            particlesRef.current.push(particle);
        };

        const updateParticle = (particle: Particle, currentTime: number): boolean => {
            const elapsed = currentTime - particle.startTime;
            const progress = Math.min(elapsed / particle.duration, 1);
            
            const easeProgress = progress < 0.5 
                ? 2 * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 2) / 2;
            
            const midX = (particle.x + particle.targetX) / 2 + (Math.random() - 0.5) * 100;
            particle.x = particle.x + (midX - particle.x) * easeProgress * 0.5;
            particle.y = particle.y + (particle.targetY - particle.y) * easeProgress;
            
            if (progress < 0.2) {
                particle.opacity = progress / 0.2;
            } else if (progress > 0.8) {
                particle.opacity = (1 - progress) / 0.2;
            } else {
                particle.opacity = 0.8;
            }
            
            particle.element.style.left = `${particle.x}px`;
            particle.element.style.top = `${particle.y}px`;
            particle.element.style.opacity = particle.opacity.toString();
            particle.element.style.transform = `translateZ(0)`; // GPU acceleration
            
            if (progress > 0.4 && progress < 0.5 && Math.random() > 0.95) {
                generateVideoFrame();
            }
            
            if (progress >= 1) {
                particle.element.remove();
                return false;
            }
            
            return true;
        };

        const generateVideoFrame = () => {
            if (!frameContainerRef.current || !playerScreenRef.current) return;
            
            if (frameCountRef.current >= maxFrames) {
                const frames = frameContainerRef.current.querySelectorAll('.video-frame');
                if (frames.length > 0) {
                    frames[0].remove();
                }
            }
            
            const frame = document.createElement('div');
            frame.className = 'video-frame';
            
            const angle = (frameCountRef.current * 137.5) * (Math.PI / 180);
            const radius = 120 + (frameCountRef.current * 15);
            const x = Math.cos(angle) * radius;
            const y = Math.sin(angle) * radius;
            
            frame.style.transform = `translate(-50%, -50%) translate(${x}px, ${y}px) scale(0)`;
            frame.style.opacity = '0';
            
            frameContainerRef.current.appendChild(frame);
            
            // Slower initial appearance - increased from 0.8s to 1.6s
            setTimeout(() => {
                if (!frame.parentElement) return;
                frame.style.transition = 'all 1.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                frame.style.transform = `translate(-50%, -50%) translate(${x}px, ${y}px) scale(1)`;
                frame.style.opacity = '1';
            }, 50);
            
            // Slower movement to YouTube player - increased delay from 2000ms to 3500ms and transition from 1.5s to 2.5s
            setTimeout(() => {
                if (!playerScreenRef.current || !containerRef.current || !frame.parentElement) return;
                
                const playerRect = playerScreenRef.current.getBoundingClientRect();
                const containerRect = containerRef.current.getBoundingClientRect();
                const frameRect = frame.getBoundingClientRect();
                
                const targetX = (playerRect.left - containerRect.left) + playerRect.width / 2 - (frameRect.left - containerRect.left) - frameRect.width / 2;
                const targetY = (playerRect.top - containerRect.top) + playerRect.height / 2 - (frameRect.top - containerRect.top) - frameRect.height / 2;
                
                frame.style.transition = 'all 2.5s cubic-bezier(0.4, 0.0, 0.2, 1)';
                frame.style.transform = `translate(-50%, -50%) translate(${targetX}px, ${targetY}px) scale(2.5)`;
                frame.style.opacity = '0';
                
                setTimeout(() => {
                    if (frame.parentElement) {
                        frame.remove();
                    }
                }, 2500);
            }, 3500);
            
            frameCountRef.current++;
        };

        const spawnFrameBurst = () => {
            const burstCount = 5 + Math.floor(Math.random() * 3); // Reduced from 8-13 to 5-8
            for (let i = 0; i < burstCount; i++) {
                setTimeout(() => {
                    generateVideoFrame();
                }, i * 100); // Increased delay from 80ms to 100ms
            }
            
            // Spawn 15 particles (reduced from 25) with bias toward left side
            for (let i = 0; i < 15; i++) {
                setTimeout(() => {
                    // Force left side for first 10 particles, then random
                    const forceLeft = i < 10;
                    spawnParticle('data', forceLeft);
                }, i * 60); // Increased delay from 40ms to 60ms
            }
        };

        const updatePlatform = () => {
            // Cycle through desktop, tablet, and mobile
            const platforms: Platform[] = ['desktop', 'tablet', 'mobile'];
            const currentIndex = platforms.indexOf(platformRef.current);
            const nextIndex = (currentIndex + 1) % platforms.length;
            platformRef.current = platforms[nextIndex];
            
            // Update the player appearance
            if (youtubeLayerRef.current) {
                youtubeLayerRef.current.setAttribute('data-platform', platformRef.current);
            }
        };

        const fadeYoutubeScreen = () => {
            if (!youtubeLayerRef.current) return;
            
            youtubeLayerRef.current.style.animation = 'none';
            void youtubeLayerRef.current.offsetWidth;
            
            youtubeLayerRef.current.style.transition = 'opacity 0.8s ease-out';
            youtubeLayerRef.current.style.opacity = '0.2';
            
            setTimeout(() => {
                if (youtubeLayerRef.current) {
                    youtubeLayerRef.current.style.opacity = '1';
                }
            }, 800);
        };

        const typeText = () => {
            if (isTypingRef.current || !textPromptRef.current) return;
            isTypingRef.current = true;
            
            updatePlatform();
            fadeYoutubeScreen();
            spawnFrameBurst();
            
            const prompt = prompts[currentPromptIndexRef.current];
            currentCharIndexRef.current = 0;
            
            const typingInterval = setInterval(() => {
                if (!textPromptRef.current) return;
                
                if (currentCharIndexRef.current < prompt.length) {
                    textPromptRef.current.textContent = prompt.substring(0, currentCharIndexRef.current + 1);
                    currentCharIndexRef.current++;
                    
                    // Reduced particle spawn rate during typing
                    if (currentCharIndexRef.current % 5 === 0) { // Changed from 3 to 5
                        spawnParticle();
                    }
                } else {
                    clearInterval(typingInterval);
                    isTypingRef.current = false;
                    
                    setTimeout(() => {
                        if (!textPromptRef.current) return;
                        currentPromptIndexRef.current = (currentPromptIndexRef.current + 1) % prompts.length;
                        textPromptRef.current.style.opacity = '0';
                        setTimeout(() => {
                            if (!textPromptRef.current) return;
                            textPromptRef.current.textContent = '';
                            textPromptRef.current.style.opacity = '1';
                            typeText();
                        }, 1000);
                    }, 5000);
                }
            }, 50);
        };

        const animate = (currentTime: number) => {
            // Performance: Skip animation when not visible
            if (!isVisibleRef.current) {
                animationFrameRef.current = requestAnimationFrame(animate);
                return;
            }
            
            for (let i = particlesRef.current.length - 1; i >= 0; i--) {
                if (!updateParticle(particlesRef.current[i], currentTime)) {
                    particlesRef.current.splice(i, 1);
                }
            }
            
            animationFrameRef.current = requestAnimationFrame(animate);
        };

        // Initialize
        setTimeout(() => {
            typeText();
            animationFrameRef.current = requestAnimationFrame(animate);
        }, 500);

        setTimeout(() => {
            if (youtubeLayerRef.current) {
                youtubeLayerRef.current.style.animation = 'none';
            }
        }, 5000);

        // Continuous particle spawning (reduced frequency for better performance)
        const particleInterval = setInterval(() => {
            if (isTypingRef.current) {
                spawnParticle();
            }
        }, 150); // Increased from 80ms to 150ms to reduce spawn rate

        // Generate frames periodically (slowed down from 800ms to 1200ms)
        const frameInterval = setInterval(() => {
            if (isTypingRef.current && Math.random() > 0.7) {
                generateVideoFrame();
            }
        }, 1200);

        // Spawn initial burst (reduced count for better performance)
        setTimeout(() => {
            for (let i = 0; i < 12; i++) { // Reduced from 20 to 12
                setTimeout(() => spawnParticle(), i * 80); // Increased delay from 50ms to 80ms
            }
        }, 1000);
        
        // Performance: Pause animation when container is not visible
        let intersectionObserver: IntersectionObserver | null = null;
        if (containerRef.current && typeof IntersectionObserver !== 'undefined') {
            intersectionObserver = new IntersectionObserver(
                ([entry]) => {
                    isVisibleRef.current = entry.isIntersecting;
                },
                { threshold: 0.1 }
            );
            intersectionObserver.observe(containerRef.current);
        }

        return () => {
            if (animationFrameRef.current) {
                cancelAnimationFrame(animationFrameRef.current);
            }
            if (intersectionObserver) {
                intersectionObserver.disconnect();
            }
            clearInterval(particleInterval);
            clearInterval(frameInterval);
            particlesRef.current.forEach(p => p.element.remove());
        };
    }, []);

    return (
        <div ref={containerRef} className="hidden md:block absolute inset-0 overflow-hidden pointer-events-none z-0" style={{ opacity: 0.5 }}>
            <div className="genmap-bg-container w-full h-full">
                {/* Text Input Layer */}
                <div className="absolute" style={{ bottom: 'calc(19% - 15px)', right: '5%', width: '80%', maxWidth: '700px', zIndex: 10 }}>
                    <div ref={textPromptRef} className="text-prompt"></div>
                </div>
                
                {/* Processing Particles */}
                <div ref={particlesContainerRef} className="absolute inset-0" style={{ zIndex: 5 }}></div>
                
                {/* Video Generation Layer */}
                <div className="absolute" style={{ top: '48%', right: '5%', transform: 'translateY(-50%)', width: '600px', height: '340px', zIndex: 7 }}>
                    <div ref={frameContainerRef} className="relative w-full h-full"></div>
                </div>
                
                {/* Video Player Output Layer */}
                <div ref={youtubeLayerRef} className="youtube-layer absolute" data-platform="desktop" style={{ top: '8%', right: '5%', width: '90%', maxWidth: '640px', zIndex: 10 }}>
                    <div className="youtube-player">
                        {/* Mobile Phone Frame - shown when platform is 'mobile' */}
                        <div className="mobile-phone-frame"></div>
                        <div ref={playerScreenRef} className="player-screen">
                            {/* Mobile Status Bar - shown when platform is 'mobile' */}
                            <div className="mobile-status-bar">
                                <span className="mobile-time">9:41</span>
                                <div className="mobile-status-icons">
                                    <span className="mobile-signal">📶</span>
                                    <span className="mobile-wifi">📶</span>
                                    <span className="mobile-battery">🔋</span>
                                </div>
                            </div>
                            {/* Spinning Gear - visible in both video and mobile modes */}
                            <div className="gear-image-container">
                                <img 
                                    src={getThemeAssetUrl('gear.png')} 
                                    alt="Spinning gear" 
                                    className="gear-image"
                                />
                            </div>
                        </div>
                        <div className="player-controls">
                            <div className="progress-bar">
                                <div className="progress-fill"></div>
                            </div>
                            <div className="control-buttons">
                                <div className="control-icon"></div>
                                <div className="time-display">0:00 / 2:45</div>
                                <div className="right-controls">
                                    <div className="control-icon"></div>
                                    <div className="control-icon"></div>
                                    <div className="control-icon"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

