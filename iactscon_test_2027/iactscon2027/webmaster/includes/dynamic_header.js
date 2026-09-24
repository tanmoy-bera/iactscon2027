/**
 * js/dynamic_header.js
 *
 * Add ONE <script src="js/dynamic_header.js"></script> tag to the file
 * that's already shared on every page (your sidebar/menu include).
 * That's the only change needed anywhere — no per-page edits, no DB changes.
 *
 * On load, this:
 *   1. Detects the current page's filename
 *   2. Fetches section/module/page names from includes/get_page_meta.php
 *   3. Replaces the <h2> inside <header> with the section name
 *   4. Rebuilds the breadcrumb: Home > Module (if any) > actual page name
 *
 *      The chain can end at 3 different levels depending on the CMS record:
 *        - real page under a module  -> Home / Module / Page (Page active)
 *        - module IS the page itself -> Home / Module (Module active)
 *        - section IS the page itself -> Home / Section (Section active)
 */
document.addEventListener("DOMContentLoaded", function () {

    var currentFile = window.location.pathname.split('/').pop() || 'index.php';
    console.log('[dynamic_header] currentFile:', currentFile); // TEMP DEBUG

    fetch('includes/get_page_meta.php?file=' + encodeURIComponent(currentFile))
        .then(function (res) {
            console.log('[dynamic_header] response status:', res.status); // TEMP DEBUG
            return res.json();
        })
        .then(function (data) {
            console.log('[dynamic_header] data received:', data); // TEMP DEBUG
            if (!data) return;

            // 1. Header section name
            var headerH2 = document.querySelector('header h2');
            if (headerH2 && data.sectionName) {
                headerH2.textContent = data.sectionName;
            }

            // 2. Breadcrumb rebuild
            var breadcrumb = document.querySelector('.breadcrumb');
            if (!breadcrumb) return;

            // Keep the first "Home" <li>, drop everything after it
            var items = breadcrumb.querySelectorAll('li');
            items.forEach(function (li, idx) {
                if (idx > 0) li.remove();
            });

            function appendItem(text, href, isActive) {
                var li = document.createElement('li');
                li.className = 'breadcrumb-item' + (isActive ? ' active' : '');

                if (isActive) {
                    li.setAttribute('aria-current', 'page');
                    li.textContent = text;
                } else {
                    var a = document.createElement('a');
                    a.href = href || 'javascript:void(0)';
                    a.textContent = text;
                    li.appendChild(a);
                }
                breadcrumb.appendChild(li);
            }

            if (data.pageName) {
                // Case 1: real page under a module - Module (link) / Page (active)
                if (data.moduleName) {
                    appendItem(data.moduleName, data.moduleLink, false);
                }
                appendItem(data.pageName, null, true);

            } else if (data.moduleName) {
                // Case 2: module IS the page - Module (active), no trailing item
                appendItem(data.moduleName, data.moduleLink, true);

            } else if (data.sectionName) {
                // Case 3: section IS the page - Section (active), no trailing item
                appendItem(data.sectionName, null, true);
            }
        })
        .catch(function (err) {
            console.error('dynamic_header: failed to load page meta', err);
        });
});