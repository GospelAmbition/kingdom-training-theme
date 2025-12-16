// Ideas Background Animation - Books & Sparks
// Only initialize if the container exists
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        const booksContainer = document.getElementById('books-container');
        const sparksContainer = document.getElementById('sparks-container');
        
        // Exit if containers don't exist
        if (!booksContainer || !sparksContainer) {
            return;
        }
        
        // Books configuration
        const bookConfigs = [
            { type: 'standard', color: 'color-1' },
            { type: 'tall', color: 'color-2' },
            { type: 'wide', color: 'color-3' },
            { type: 'standard', color: 'color-1' },
            { type: 'thin', color: 'color-2' },
            { type: 'tall', color: 'color-3' },
            { type: 'standard', color: 'color-1' },
            { type: 'wide', color: 'color-2' },
        ];
        
        // Create books
        bookConfigs.forEach((config, index) => {
            const book = document.createElement('div');
            book.className = `book ${config.type} ${config.color}`;
            booksContainer.appendChild(book);
        });
        
        // Spark particle system
        class Spark {
            constructor(containerWidth, containerHeight) {
                this.element = document.createElement('div');
                this.element.className = 'spark';

                // Random color
                const colors = ['color-1', 'color-2', 'color-3', 'color-4', 'color-5'];
                this.colorClass = colors[Math.floor(Math.random() * colors.length)];
                this.element.classList.add(this.colorClass);

                // Random starting position near books (bottom of container)
                const bookAreaStart = containerWidth * 0.2;
                const bookAreaEnd = containerWidth * 0.8;
                this.x = bookAreaStart + Math.random() * (bookAreaEnd - bookAreaStart);
                this.y = containerHeight * 0.7 + Math.random() * (containerHeight * 0.25);
                
                // Random velocity - rising upward with some horizontal drift
                this.vx = (Math.random() - 0.5) * 0.8; // Horizontal drift
                this.vy = -2 - Math.random() * 2; // Upward movement (negative Y)
                
                // Random size
                this.size = 2 + Math.random() * 3;
                this.element.style.width = `${this.size}px`;
                this.element.style.height = `${this.size}px`;
                
                // Random lifetime
                this.lifetime = 3000 + Math.random() * 4000; // 3-7 seconds
                this.age = 0;
                
                // Random delay before appearing
                this.delay = Math.random() * 200;
                this.delayed = false;
                
                // Fade and blur properties
                this.opacity = 0;
                this.blur = 0;
                
                this.updatePosition();
                sparksContainer.appendChild(this.element);
            }
            
            updatePosition() {
                this.element.style.left = `${this.x}px`;
                this.element.style.top = `${this.y}px`;
            }
            
            update(deltaTime, containerWidth, containerHeight) {
                // Handle initial delay
                if (!this.delayed) {
                    this.delay -= deltaTime;
                    if (this.delay > 0) {
                        this.element.style.opacity = '0';
                        return;
                    }
                    this.delayed = true;
                }

                this.age += deltaTime;

                // Update position
                this.x += this.vx;
                this.y += this.vy;

                // Add some turbulence/wind effect
                this.vx += (Math.random() - 0.5) * 0.1;
                this.vy += (Math.random() - 0.5) * 0.05;

                // Damping to prevent excessive speed
                this.vx *= 0.99;
                this.vy *= 0.995;

                // Calculate opacity based on age and position
                const progress = this.age / this.lifetime;
                const heightProgress = (containerHeight - this.y) / containerHeight;

                // Fade in at start, fade out at end
                if (progress < 0.1) {
                    this.opacity = progress / 0.1;
                } else if (progress > 0.8) {
                    this.opacity = (1 - progress) / 0.2;
                } else {
                    this.opacity = 0.6 + Math.sin(progress * Math.PI * 4) * 0.2; // Subtle pulsing
                }

                // Increase blur as it rises (fading out of focus)
                this.blur = heightProgress * 3;

                // Apply opacity and blur with breathing effect
                const breathe = 0.7 + Math.sin(this.age / 500) * 0.3;
                this.element.style.opacity = (this.opacity * breathe * 0.6).toString();
                this.element.style.filter = `blur(${this.blur}px)`;

                this.updatePosition();

                // Remove if expired or off screen
                if (this.age >= this.lifetime || this.y < -50 || this.x < -50 || this.x > containerWidth + 50) {
                    this.element.remove();
                    return false;
                }

                return true;
            }
        }
        
        // Spark management
        const sparks = [];
        const maxSparks = 500;
        let lastSpawnTime = 0;
        const spawnInterval = 20; // Spawn new spark every 20ms
        
        function spawnSpark(containerWidth, containerHeight) {
            if (sparks.length < maxSparks) {
                const spark = new Spark(containerWidth, containerHeight);
                sparks.push(spark);
            }
        }
        
        // Animation loop
        let lastTime = performance.now();
        const container = sparksContainer.parentElement;

        function animate(currentTime) {
            const deltaTime = currentTime - lastTime;
            lastTime = currentTime;

            // Read container dimensions once per frame (not per spark)
            const containerWidth = container.offsetWidth || window.innerWidth;
            const containerHeight = container.offsetHeight || window.innerHeight;

            // Spawn new sparks periodically
            if (currentTime - lastSpawnTime >= spawnInterval) {
                spawnSpark(containerWidth, containerHeight);
                lastSpawnTime = currentTime;
            }

            // Update all sparks
            for (let i = sparks.length - 1; i >= 0; i--) {
                const spark = sparks[i];
                if (!spark.update(deltaTime, containerWidth, containerHeight)) {
                    sparks.splice(i, 1);
                }
            }

            requestAnimationFrame(animate);
        }
        
        // Handle window resize
        function handleResize() {
            const container = sparksContainer.parentElement;
            const containerWidth = container.offsetWidth || window.innerWidth;
            // Remove sparks that are now off-screen
            sparks.forEach((spark, index) => {
                if (spark.x < -100 || spark.x > containerWidth + 100) {
                    spark.element.remove();
                    sparks.splice(index, 1);
                }
            });
        }
        
        window.addEventListener('resize', handleResize);
        
        // Start animation
        animate();
    }
})();
