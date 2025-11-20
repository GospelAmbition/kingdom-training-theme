// Text prompts that will cycle
const prompts = [
    "Create a discipleship training video that sparks movements in unreached communities...",
    "Generate an engaging video teaching biblical principles for multiplying disciples...",
    "Produce a testimony video showing how media catalyzes church planting movements...",
    "Make an interactive video equipping believers to share the Gospel through digital tools...",
    "Create a training series on facilitating discovery Bible studies in oral cultures...",
    "Generate content showing how one faithful disciple can multiply into thousands..."
];

let currentPromptIndex = 0;
let currentText = '';
let currentCharIndex = 0;
let isTyping = false;
let currentPlatform = 'youtube'; // Track current platform

const textPromptElement = document.getElementById('text-prompt');
const particlesContainer = document.getElementById('particles-container');
const frameContainer = document.getElementById('frame-container');
const playerScreen = document.getElementById('player-screen');
const youtubeLayer = document.querySelector('.youtube-layer');

// Function to update platform
function updatePlatform() {
    const platforms = ['youtube', 'tiktok', 'facebook'];
    const currentIndex = platforms.indexOf(currentPlatform);
    const nextIndex = (currentIndex + 1) % platforms.length;
    currentPlatform = platforms[nextIndex];
    
    // Update the player appearance
    if (youtubeLayer) {
        youtubeLayer.setAttribute('data-platform', currentPlatform);
    }
}

// Function to fade and refresh YouTube screen
function fadeYoutubeScreen() {
    if (!youtubeLayer) return;
    
    // Remove any existing animation
    youtubeLayer.style.animation = 'none';
    
    // Force a reflow
    void youtubeLayer.offsetWidth;
    
    youtubeLayer.style.transition = 'opacity 0.8s ease-out';
    youtubeLayer.style.opacity = '0.2';
    
    setTimeout(() => {
        youtubeLayer.style.opacity = '1';
    }, 800);
}

// Function to spawn a burst of video frames
function spawnFrameBurst() {
    // Spawn 8-12 frames in quick succession
    const burstCount = 8 + Math.floor(Math.random() * 5);
    for (let i = 0; i < burstCount; i++) {
        setTimeout(() => {
            generateVideoFrame();
        }, i * 80); // Stagger the frame generation
    }
    
    // Spawn 25 particles with bias toward left side
    for (let i = 0; i < 25; i++) {
        setTimeout(() => {
            // Force left side for first 15 particles, then random
            const forceLeft = i < 15;
            spawnParticle(forceLeft);
        }, i * 40);
    }
}

// Typing animation
function typeText() {
    if (isTyping) return;
    isTyping = true;
    
    // Update platform when new prompt starts
    updatePlatform();
    
    // Fade YouTube screen when new prompt starts
    fadeYoutubeScreen();
    
    // Burst of video frames when new prompt starts (like initial animation)
    spawnFrameBurst();
    
    const prompt = prompts[currentPromptIndex];
    currentCharIndex = 0;
    currentText = '';
    
    const typingInterval = setInterval(() => {
        if (currentCharIndex < prompt.length) {
            currentText += prompt[currentCharIndex];
            textPromptElement.textContent = currentText;
            currentCharIndex++;
            
            // Spawn particles as we type
            if (currentCharIndex % 3 === 0) {
                spawnParticle();
            }
        } else {
            clearInterval(typingInterval);
            isTyping = false;
            
            // Wait before starting next prompt
            setTimeout(() => {
                currentPromptIndex = (currentPromptIndex + 1) % prompts.length;
                // Clear text with fade effect
                textPromptElement.style.opacity = '0';
                setTimeout(() => {
                    textPromptElement.textContent = '';
                    textPromptElement.style.opacity = '1';
                    typeText();
                }, 1000);
            }, 5000);
        }
    }, 50);
}

// Particle system
class Particle {
    constructor(type = 'data', forceLeft = false) {
        this.element = document.createElement('div');
        this.element.className = `particle ${type}`;
        
        // Start from text area
        const textRect = textPromptElement.getBoundingClientRect();
        this.x = textRect.left + Math.random() * textRect.width;
        this.y = textRect.top + Math.random() * textRect.height;
        
        // Target is video generation area or youtube player
        const targetY = type === 'create' 
            ? window.innerHeight * 0.15  // YouTube area
            : window.innerHeight * 0.5;  // Video generation area
        
        // 60% of particles go left, 40% go right (or force left if specified)
        if (forceLeft || Math.random() < 0.6) {
            // Target left side of screen
            this.targetX = window.innerWidth * 0.2 + (Math.random() - 0.5) * 200;
        } else {
            // Target right side of screen
            this.targetX = window.innerWidth * 0.75 + (Math.random() - 0.5) * 100;
        }
        this.targetY = targetY;
        
        this.duration = 2000 + Math.random() * 1000;
        this.startTime = performance.now();
        
        // Random size variation
        this.size = 3 + Math.random() * 4;
        this.element.style.width = `${this.size}px`;
        this.element.style.height = `${this.size}px`;
        
        this.opacity = 0;
        
        this.updatePosition();
        particlesContainer.appendChild(this.element);
    }
    
    updatePosition() {
        this.element.style.left = `${this.x}px`;
        this.element.style.top = `${this.y}px`;
        this.element.style.opacity = this.opacity;
    }
    
    update(currentTime) {
        const elapsed = currentTime - this.startTime;
        const progress = Math.min(elapsed / this.duration, 1);
        
        // Easing function for smooth movement
        const easeProgress = progress < 0.5 
            ? 2 * progress * progress 
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;
        
        // Update position with bezier-like curve
        const midX = (this.x + this.targetX) / 2 + (Math.random() - 0.5) * 100;
        this.x = this.x + (midX - this.x) * easeProgress * 0.5;
        this.y = this.y + (this.targetY - this.y) * easeProgress;
        
        // Fade in and out
        if (progress < 0.2) {
            this.opacity = progress / 0.2;
        } else if (progress > 0.8) {
            this.opacity = (1 - progress) / 0.2;
        } else {
            this.opacity = 0.8;
        }
        
        this.updatePosition();
        
        // Trigger frame generation when particles reach middle
        if (progress > 0.4 && progress < 0.5 && Math.random() > 0.95) {
            generateVideoFrame();
        }
        
        // Remove if done
        if (progress >= 1) {
            this.element.remove();
            return false;
        }
        
        return true;
    }
}

const particles = [];

function spawnParticle(forceLeft = false) {
    // Determine particle type based on progress
    const rand = Math.random();
    let type = 'data';
    if (rand > 0.7) type = 'process';
    if (rand > 0.9) type = 'create';
    
    const particle = new Particle(type, forceLeft);
    particles.push(particle);
}

// Video frame generation
let frameCount = 0;
const maxFrames = 6;

function generateVideoFrame() {
    if (frameCount >= maxFrames) {
        // Remove oldest frame
        const frames = frameContainer.querySelectorAll('.video-frame');
        if (frames.length > 0) {
            frames[0].remove();
        }
    }
    
    const frame = document.createElement('div');
    frame.className = 'video-frame';
    
    // Random position in a spiral pattern
    const angle = (frameCount * 137.5) * (Math.PI / 180); // Golden angle
    const radius = 120 + (frameCount * 15);
    const x = Math.cos(angle) * radius;
    const y = Math.sin(angle) * radius;
    
    frame.style.transform = `translate(-50%, -50%) translate(${x}px, ${y}px) scale(0)`;
    frame.style.opacity = '0';
    
    frameContainer.appendChild(frame);
    
    // Animate in (slowed down from 0.8s to 1.6s)
    setTimeout(() => {
        frame.style.transition = 'all 1.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
        frame.style.transform = `translate(-50%, -50%) translate(${x}px, ${y}px) scale(1)`;
        frame.style.opacity = '1';
    }, 50);
    
    // Animate to YouTube player after a delay (increased delay from 2000ms to 3500ms and transition from 1.5s to 2.5s)
    setTimeout(() => {
        const playerRect = playerScreen.getBoundingClientRect();
        const frameRect = frame.getBoundingClientRect();
        
        const targetX = playerRect.left + playerRect.width / 2 - frameRect.left - frameRect.width / 2;
        const targetY = playerRect.top + playerRect.height / 2 - frameRect.top - frameRect.height / 2;
        
        frame.style.transition = 'all 2.5s cubic-bezier(0.4, 0.0, 0.2, 1)';
        frame.style.transform = `translate(-50%, -50%) translate(${targetX}px, ${targetY}px) scale(2.5)`;
        frame.style.opacity = '0';
        
        setTimeout(() => {
            frame.remove();
        }, 2500);
    }, 3500);
    
    frameCount++;
}

// Animation loop
let lastTime = performance.now();

function animate(currentTime) {
    const deltaTime = currentTime - lastTime;
    lastTime = currentTime;
    
    // Update all particles
    for (let i = particles.length - 1; i >= 0; i--) {
        if (!particles[i].update(currentTime)) {
            particles.splice(i, 1);
        }
    }
    
    requestAnimationFrame(animate);
}

// Initialize
setTimeout(() => {
    typeText();
    animate(performance.now());
}, 500);

// After initial animation completes, remove the animation property so JS can control opacity
setTimeout(() => {
    if (youtubeLayer) {
        youtubeLayer.style.animation = 'none';
    }
}, 5000);

// Continuous particle spawning (increased frequency from 100ms to 80ms)
setInterval(() => {
    if (isTyping) {
        spawnParticle();
    }
}, 80);

// Generate frames periodically (slowed down from 800ms to 1200ms)
setInterval(() => {
    if (isTyping && Math.random() > 0.7) {
        generateVideoFrame();
    }
}, 1200);

// Handle window resize
function handleResize() {
    // Clear particles that are off-screen
    particles.forEach((particle, index) => {
        if (particle.x < -100 || particle.x > window.innerWidth + 100 ||
            particle.y < -100 || particle.y > window.innerHeight + 100) {
            particle.element.remove();
            particles.splice(index, 1);
        }
    });
}

window.addEventListener('resize', handleResize);

// Add thumbnail content to YouTube player dynamically
function updatePlayerThumbnail() {
    // Create animated thumbnail background
    const colors = ['#3b82f6', '#8b5cf6', '#06b6d4'];
    let colorIndex = 0;
    
    setInterval(() => {
        playerScreen.style.background = `
            linear-gradient(135deg, 
                ${colors[colorIndex % colors.length]}22 0%, 
                ${colors[(colorIndex + 1) % colors.length]}22 100%)
        `;
        colorIndex++;
    }, 3000);
}

updatePlayerThumbnail();

// Spawn initial burst of particles
setTimeout(() => {
    for (let i = 0; i < 20; i++) {
        setTimeout(() => spawnParticle(), i * 50);
    }
}, 1000);

