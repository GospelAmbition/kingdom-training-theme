(function() {
    'use strict';
    
    const codeLines = [
        "// Initializing Kingdom Training Neural Network...",
        "import { Strategy, DiscipleMaking } from '@kingdom/core';",
        "import { Tools, Resources } from '@kingdom/inventory';",
        "",
        "/**",
        " * Configuration for the disciple making movement protocol.",
        " * Optimizes for reproducibility and spiritual depth.",
        " */",
        "class KingdomStrategy extends Strategy {",
        "    constructor() {",
        "        super({",
        "            objective: 'Great Commission',",
        "            methodology: 'Multiplication',",
        "            target: 'All Nations'",
        "        });",
        "    }",
        "",
        "    async deployTools() {",
        "        const toolset = await Tools.load(['DiscoveryBibleStudy', 'Zume', 'Training']);",
        "        ",
        "        console.log('Analyzing regional requirements...');",
        "        // Adapting strategy to local context",
        "        ",
        "        for (const tool of toolset) {",
        "             await this.implement(tool, {",
        "                 focus: 'Obedience',",
        "                 mode: 'Reproducible'",
        "             });",
        "        }",
        "        ",
        "        return true;",
        "    }",
        "}",
        "",
        "// Main Execution Context",
        "async function runSimulation() {",
        "    const movement = new DiscipleMaking();",
        "    const currentStrategy = new KingdomStrategy();",
        "    ",
        "    console.log('Starting generation...');",
        "    ",
        "    while (movement.isActive()) {",
        "        const metric = await movement.evaluate(currentStrategy);",
        "        ",
        "        if (metric.needsRefinement) {",
        "            // Adjusting approach based on feedback",
        "            currentStrategy.optimize();",
        "            await currentStrategy.deployTools();",
        "        }",
        "        ",
        "        // Generating equipping content",
        "        await new Promise(r => setTimeout(r, 100));",
        "    }",
        "}",
        "",
        "runSimulation();",
        "// Awaiting input streams...",
        "// Processing strategy vectors...",
        "// Optimizing tools for engagement...",
    ];

    function highlightText(text) {
        // Escape HTML
        let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        
        // Mask comments first
        const comments = [];
        html = html.replace(/(\/\/.*)/g, (match) => {
            comments.push(match);
            return `___COMMENT${comments.length - 1}___`;
        });

        // Mask strings
        const strings = [];
        html = html.replace(/('.*?'|".*?")/g, (match) => {
            strings.push(match);
            return `___STRING${strings.length - 1}___`;
        });

        // Highlight keywords
        html = html.replace(/\b(import|from|const|let|var|async|function|new|return|class|extends|constructor|super|while|if|await|for)\b/g, '<span class="keyword">$1</span>');
        
        // Highlight Classes
        html = html.replace(/\b(Strategy|DiscipleMaking|Tools|Resources|KingdomStrategy|Promise)\b/g, '<span class="class-name">$1</span>');

        // Highlight Functions
        html = html.replace(/\b(deployTools|load|log|implement|runSimulation|evaluate|optimize|isActive|setTimeout)\b/g, '<span class="function">$1</span>');

        // Restore Strings
        html = html.replace(/___STRING(\d+)___/g, (match, i) => `<span class="string">${strings[i]}</span>`);

        // Restore Comments
        html = html.replace(/___COMMENT(\d+)___/g, (match, i) => `<span class="comment">${comments[i]}</span>`);

        return html;
    }

    function initTypingAnimation(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            return;
        }

        let currentLine = 0;
        let currentChar = 0;

        function typeCode() {
            // Reset if we reached the end
            if (currentLine >= codeLines.length) {
                setTimeout(() => {
                    container.innerHTML = '';
                    currentLine = 0;
                    currentChar = 0;
                    typeCode();
                }, 2000);
                return;
            }

            const lineText = codeLines[currentLine];
            
            // Get or create line element
            let lineElement = container.children[currentLine];
            if (!lineElement) {
                lineElement = document.createElement('div');
                lineElement.className = 'code-line';
                container.appendChild(lineElement);
                
                // Auto scroll to bottom
                container.scrollTop = container.scrollHeight;
            }

            if (currentChar <= lineText.length) {
                const currentText = lineText.substring(0, currentChar);
                lineElement.textContent = currentText; // Type as plain text
                
                // Remove existing cursor
                const existingCursor = lineElement.querySelector('.cursor');
                if (existingCursor) {
                    existingCursor.remove();
                }
                
                // Add cursor
                const cursor = document.createElement('span');
                cursor.className = 'cursor';
                lineElement.appendChild(cursor);

                // Continuously scroll to bottom to keep latest line visible
                container.scrollTop = container.scrollHeight;

                currentChar++;
                
                // Typing speed: fast but variable
                const speed = Math.random() * 30 + 20;
                setTimeout(typeCode, speed);
            } else {
                // Line Finished
                // Replace content with highlighted HTML
                lineElement.innerHTML = highlightText(lineText);
                
                // Scroll to bottom after line is complete
                container.scrollTop = container.scrollHeight;
                
                currentLine++;
                currentChar = 0;
                
                // Pause between lines
                setTimeout(typeCode, Math.random() * 300 + 100);
            }
        }

        // Initialize with a slight delay to stagger the animations
        const delay = containerId === 'code-container-left' ? 0 : 
                      containerId === 'code-container-middle' ? 500 : 1000;
        setTimeout(typeCode, delay);
    }

    function initLLMBackground() {
        // Initialize all three columns
        initTypingAnimation('code-container-left');
        initTypingAnimation('code-container-middle');
        initTypingAnimation('code-container-right');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLLMBackground);
    } else {
        initLLMBackground();
    }
})();
