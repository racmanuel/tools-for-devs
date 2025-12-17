/**
 * WP Migration SQL Tool
 *
 * @since 1.0.0
 * @textdomain tools-for-devs
 */

(function () {
    "use strict";

    const hasCodeMirror = typeof window.wp !== "undefined" && wp.codeEditor;

    const { __ } = wp.i18n;

    function normalizeUrl(url) {
        return (url || "")
            .trim()
            .replace(/\s+/g, "")
            .replace(/\/+$/, "");
    }

    function escapeSqlString(str) {
        return String(str).replace(/'/g, "''");
    }

    function buildQueries(fromUrl, toUrl, prefix) {
        const f = escapeSqlString(fromUrl);
        const t = escapeSqlString(toUrl);
        const p = prefix;

        return `UPDATE ${p}options
SET option_value = REPLACE(option_value, '${f}', '${t}')
WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE ${p}posts
SET post_content = REPLACE(post_content, '${f}', '${t}');

UPDATE ${p}posts
SET post_excerpt = REPLACE(post_excerpt, '${f}', '${t}');

UPDATE ${p}postmeta
SET meta_value = REPLACE(meta_value, '${f}', '${t}');

UPDATE ${p}termmeta
SET meta_value = REPLACE(meta_value, '${f}', '${t}');

UPDATE ${p}comments
SET comment_content = REPLACE(comment_content, '${f}', '${t}');

UPDATE ${p}comments
SET comment_author_url = REPLACE(comment_author_url, '${f}', '${t}');

UPDATE ${p}posts
SET guid = REPLACE(guid, '${f}', '${t}')
WHERE post_type = 'attachment';
`;
    }

    async function copyToClipboard(text, textarea, toast) {
        try {
            await navigator.clipboard.writeText(text);
            if (toast) {
                toast.style.display = "block";
                setTimeout(() => (toast.style.display = "none"), 2200);
            }
        } catch (e) {
            if (textarea) {
                textarea.focus();
                textarea.select();
            }
        }
    }

    function initTool(root) {
        const uid = root.id;

        const fromInput = root.querySelector(`#${uid}-from`);
        const toInput = root.querySelector(`#${uid}-to`);
        const prefixInput = root.querySelector(`#${uid}-prefix`);
        const textarea = root.querySelector(`#${uid}-out`);
        const toast = root.querySelector("[data-toast]");

        let editor = null;

        // 🔹 Init CodeMirror if available
        if (hasCodeMirror && textarea) {
            editor = wp.codeEditor.initialize(textarea, {
                codemirror: {
                    mode: "text/x-sql",
                    readOnly: true,
                    lineNumbers: true,
                    lineWrapping: true,
                }
            });
        }

        root.addEventListener("click", async (event) => {
            const button = event.target.closest('[data-action="generate"]');
            if (!button) return;

            const fromUrl = normalizeUrl(fromInput.value);
            const toUrl = normalizeUrl(toInput.value);
            let prefix = (prefixInput.value || "wp_").trim();

            if (!prefix.endsWith("_")) {
                prefix += "_";
            }

            if (!fromUrl || fromUrl === "http://" || fromUrl === "https://") {
                alert(__('Please enter a valid "From" domain.', 'tools-for-devs'));
                return;
            }

            if (!toUrl || toUrl === "http://" || toUrl === "https://") {
                alert(__('Please enter a valid "To" domain.', 'tools-for-devs'));
                return;
            }

            const sql = buildQueries(fromUrl, toUrl, prefix);

            // 🔹 Update editor or textarea
            if (editor && editor.codemirror) {
                editor.codemirror.setValue(sql);
                editor.codemirror.refresh();
            } else {
                textarea.value = sql;
            }

            await copyToClipboard(sql, textarea, toast);
        });
    }


    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("[data-wp-mig]").forEach(initTool);
    });
})();
