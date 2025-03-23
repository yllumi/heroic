/** 
 * Alpine Debugger
 **/ 
if (document.body.classList.contains('env-development')) {

    document.addEventListener('alpine:init', () => {
    
        function syntaxHighlight(json) {
            if (typeof json != 'string') {
                json = JSON.stringify(json, null, 2);
            }
            json = json
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(?:\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                let cls = 'number';
                if (/^"/.test(match)) {
                    cls = /:$/.test(match) ? 'key' : 'string';
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return `<span style="color: ${getColor(cls)}">${match}</span>`;
            });
        }
        
        function getColor(cls) {
            switch (cls) {
                case 'key': return '#2ca94b';
                case 'string': return '#032f62';
                case 'number': return '#005cc5';
                case 'boolean': return '#e36209';
                case 'null': return '#6a737d';
                default: return '#000';
            }
        }  
    
        Alpine.directive('debug', (el) => {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.marginTop = '10px';
    
            const elId = el.id ? `#${el.id}` : `${el.tagName.toLowerCase()}[x-data]`;
            const accordionItem = document.createElement('div');
            accordionItem.style.marginBottom = '5px';
    
            const toggleBtn = document.createElement('button');
            toggleBtn.innerHTML = `Alpine.data Debug 🐞<br><code style="color:#81d9ff">${elId}</code>`;
            toggleBtn.style = `
                background: #333; color: white; font-size: 13px; padding: 5px 10px; border: none;
                border-radius: 4px; cursor: pointer; width: 100%; text-align: left; line-height: 14px;
            `;
    
            const panel = document.createElement('div');
            panel.style = `
                display: none; border: 1px solid #ccc; border-radius: 4px; padding: 10px;
                background: #f9f9f9; font-size: 16px; line-height: 18px; white-space: pre; overflow: auto; max-height: 400px;
                font-family: monospace;
            `;
    
            let interval = null;
    
            toggleBtn.addEventListener('click', () => {
                const isOpen = panel.style.display === 'block';
                const allPanels = document.querySelectorAll('.debug-panel');
                allPanels.forEach(p => p.style.display = 'none'); // Close all other panels
                if (isOpen) {
                    panel.style.display = 'none';
                    clearInterval(interval);
                } else {
                    panel.style.display = 'block';
                    updatePanel();
                    interval = setInterval(updatePanel, 1000);
                }
            });      
    
            function updatePanel() {
                try {
                    const data = Alpine.$data(el);
                    panel.innerHTML = `<pre style="margin: 0; font-family: monospace;">${syntaxHighlight(data)}</pre>`;
                } catch (e) {
                    panel.textContent = 'Error loading data.';
                }
            }
    
            accordionItem.appendChild(toggleBtn);
            accordionItem.appendChild(panel);
    
            // Append to a container for accordion behavior
            const accordionContainer = document.getElementById('alpine-debugger-accordion');
            if (accordionContainer) {
                accordionContainer.appendChild(accordionItem);
            } else {
                // Create the accordion container if it doesn't exist
                const newAccordionContainer = document.createElement('div');
                newAccordionContainer.id = 'alpine-debugger-accordion';
                newAccordionContainer.style.position = 'fixed';
                newAccordionContainer.style.bottom = '10px';
                newAccordionContainer.style.right = '10px';
                newAccordionContainer.style.zIndex = '100000';
                newAccordionContainer.style.maxHeight = '100vh';
                newAccordionContainer.style.overflowY = 'auto';
                document.body.appendChild(newAccordionContainer);
                newAccordionContainer.appendChild(accordionItem);
            }
        });
    
        // Simpan nama store secara manual atau via registerStore
        const storeNames = window.__ALPINE_STORE_NAMES_DEBUG__ || [];
        if (storeNames.length === 0) return;
    
        // 🔘 Tombol toggle di luar container
        const toggleBtn = document.createElement('button');
        toggleBtn.innerText = '🐞 Store Debugger';
        toggleBtn.style = `
            position: fixed;
            bottom: 10px;
            left: 10px;
            background: #333;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            z-index: 100001;
        `;
        document.body.appendChild(toggleBtn);
    
        // 📦 Kontainer debugger
        const container = document.createElement('div');
        container.style.position = 'fixed';
        container.style.bottom = '50px';
        container.style.left = '10px';
        container.style.zIndex = '100000';
        container.style.maxHeight = '90vh';
        container.style.overflowY = 'auto';
        container.style.padding = '10px';
        container.style.background = '#fff';
        container.style.border = '1px solid #ccc';
        container.style.borderRadius = '6px';
        container.style.fontSize = '13px';
        container.style.fontFamily = 'monospace';
        container.style.maxWidth = '350px';
        container.style.display = 'none';
    
        const title = document.createElement('div');
        title.textContent = '🐞 Alpine.store()';
        title.style.fontWeight = 'bold';
        title.style.marginBottom = '8px';
        container.appendChild(title);
    
        storeNames.forEach(name => {
            const toggle = document.createElement('button');
            toggle.innerText = `$store.${name}`;
            toggle.style = `
                display: block;
                background: #333;
                color: white;
                font-size: 13px;
                padding: 5px 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                margin-bottom: 4px;
                text-align: left;
                width: 100%;
            `;
    
            const panel = document.createElement('div');
            panel.style = `
                display: none;
                background: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 8px;
                margin-bottom: 10px;
                max-height: 400px;
                overflow: auto;
                font-size: 16px;
            `;
    
            toggle.addEventListener('click', () => {
                const isOpen = panel.style.display === 'block';
                panel.style.display = isOpen ? 'none' : 'block';
                if (!isOpen) {
                    const data = Alpine.store(name);
                    panel.innerHTML = `<pre style="margin: 0;">${syntaxHighlight(data)}</pre>`;
                }
            });
    
            container.appendChild(toggle);
            container.appendChild(panel);
        });
    
        document.body.appendChild(container);
    
        toggleBtn.addEventListener('click', () => {
            const isVisible = container.style.display === 'block';
            container.style.display = isVisible ? 'none' : 'block';
        });
    });
}