const card = (function(window, document) {
    'use strict';

    const card = {
        registerFilters: function() {
            // register events globally as not to loose listeners on update filter DOM.
            ['keyup', 'change'].forEach(evt => {
                document.addEventListener(evt, function(e) {
                    
                    const form = e.target.closest('form');
                    
                    if (form && form.hasAttribute('data-card-filter')) {
                        const queryString = new URLSearchParams(new FormData(form)).toString();
                        const uri = form.getAttribute('action')+'?'+queryString;
                        
                        if (e.type === 'keyup') {
                            setTimeout(() => {
                                card.updateFilter(uri, e);
                            }, 200);
                        } else {
                            card.updateFilter(uri, e);
                        }
                    }

                    return;
                });
            });
            
            document.addEventListener('click', function(e) {
                if (! e.target.hasAttribute('href') || ! e.target.closest('[data-card-filter]')) {
                    return;
                }
                e.preventDefault();
                card.updateFilter(e.target.getAttribute('href'), e);
            });
        },
        updateFilter: function(uri, event) {            
            fetch(uri, {
                method: 'GET',
            }).then(response => {
                return response.text();
            }).then(string => {
                const doc = (new DOMParser()).parseFromString(string, 'text/html');
                const cardFilterName = event.target.closest('[data-card-filter]').getAttribute('data-card-filter');

                let replaces = [
                    '[data-card-content="'+cardFilterName+'"]',
                ];
                
                replaces.forEach(selector => {
                    const newEl = doc.querySelector(selector);
                    const oldEl = document.querySelector(selector);
                    if (newEl && oldEl) {
                        oldEl.parentNode.replaceChild(newEl, oldEl);
                    }
                });
            });
        }
    };
    
    document.addEventListener('DOMContentLoaded', (e) => {
        card.registerFilters();
    });
    
    return card;
    
})(window, document);

export default card;