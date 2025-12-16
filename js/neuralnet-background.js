// Neural Network Background Animation
(function() {
    'use strict';
    
    // Find all neural network background containers
    const containers = document.querySelectorAll('.neuralnet-background');
    if (containers.length === 0) return;
    
    const animationInstances = [];
    
    // Initialize animation for each container
    containers.forEach((container) => {
        const canvas = container.querySelector('canvas');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        let width, height;
        let particles = [];
        const particleCount = 80;
        const connectionDistance = 150;
        const connectionDistanceSq = connectionDistance * connectionDistance;
        const rotationSpeed = 0.001; // Radians per frame
        let globalAngle = 0;
        let animationFrameId = null;
        let isVisible = true;
        
        // Resize handling
        function resize() {
            width = container.offsetWidth;
            height = container.offsetHeight;
            canvas.width = width;
            canvas.height = height;
        }
        
        // Particle Class
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 3 + 2;
                // Cyber-heart colors: pinks, purples, cyans
                const colors = ['#ff4081', '#00e5ff', '#e040fb'];
                this.color = colors[Math.floor(Math.random() * colors.length)];
            }
        
            update() {
                this.x += this.vx;
                this.y += this.vy;
        
                // Bounce off edges (conceptually, but we'll wrap for smoother flow)
                if (this.x < 0) this.x = width;
                if (this.x > width) this.x = 0;
                if (this.y < 0) this.y = height;
                if (this.y > height) this.y = 0;
            }
        
            draw(context) {
                context.save();
                context.translate(this.x, this.y);
                
                context.fillStyle = this.color;
                context.beginPath();
                
                // Heart shape logic
                // Scale down because the math produces a large heart
                const scale = this.size / 100; 
                context.scale(scale, scale);
                
                // Heart path
                // Top left curve
                context.moveTo(0, -30);
                context.bezierCurveTo(-30, -70, -80, -30, 0, 40);
                context.bezierCurveTo(80, -30, 30, -70, 0, -30);
                
                context.fill();
                context.restore();
            }
        }
        
        function init() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
        }
        
        function drawConnections() {
            // Batch lines by opacity level (5 buckets) to reduce draw calls
            const opacityBuckets = [[], [], [], [], []];

            for (let i = 0; i < particles.length; i++) {
                const p1 = particles[i];
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p1.x - p2.x;
                    const dy = p1.y - p2.y;
                    const distSq = dx * dx + dy * dy;

                    if (distSq < connectionDistanceSq) {
                        // Use squared distance ratio for opacity (avoids sqrt)
                        const ratio = distSq / connectionDistanceSq;
                        const bucketIndex = Math.min(4, (ratio * 5) | 0);
                        opacityBuckets[bucketIndex].push(p1.x, p1.y, p2.x, p2.y);
                    }
                }
            }

            // Draw each bucket with a single stroke call
            ctx.lineWidth = 1;
            for (let b = 0; b < 5; b++) {
                const bucket = opacityBuckets[b];
                if (bucket.length === 0) continue;

                const opacity = (1 - (b + 0.5) / 5) * 0.2;
                ctx.strokeStyle = `rgba(255, 255, 255, ${opacity})`;
                ctx.beginPath();
                for (let i = 0; i < bucket.length; i += 4) {
                    ctx.moveTo(bucket[i], bucket[i + 1]);
                    ctx.lineTo(bucket[i + 2], bucket[i + 3]);
                }
                ctx.stroke();
            }
        }
        
        function animate() {
            if (!isVisible) {
                animationFrameId = null;
                return;
            }

            // Clear canvas
            ctx.clearRect(0, 0, width, height);

            // Global Rotation logic
            ctx.save();
            ctx.translate(width / 2, height / 2);
            globalAngle += rotationSpeed;
            ctx.rotate(globalAngle);
            ctx.translate(-width / 2, -height / 2);

            // Draw connections first so they are behind nodes
            drawConnections();

            // Update and draw particles
            particles.forEach(p => {
                p.update();
                p.draw(ctx);
            });

            ctx.restore();

            animationFrameId = requestAnimationFrame(animate);
        }

        function startAnimation() {
            if (!animationFrameId && isVisible) {
                animate();
            }
        }
        
        // Initialize and start animation
        resize();
        window.addEventListener('resize', resize);
        init();

        // Pause animation when off-screen to save CPU
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isVisible = entry.isIntersecting;
                if (isVisible) {
                    startAnimation();
                }
            });
        }, { threshold: 0 });
        observer.observe(container);

        animate();

        // Store cleanup function
        animationInstances.push({
            cleanup: function() {
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                }
                observer.disconnect();
                window.removeEventListener('resize', resize);
            }
        });
    });
    
    // Cleanup function for when the page unloads
    window.addEventListener('beforeunload', function() {
        animationInstances.forEach(instance => instance.cleanup());
    });
})();
