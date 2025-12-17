(function () {
    "use strict";

    const __ =
        window.wp && wp.i18n && wp.i18n.__ ? wp.i18n.__ : (t) => t;

    function normalizePrefix(p) {
        p = (p || "").trim();
        if (!p) return "";
        return p.endsWith("_") ? p : p + "_";
    }

    function sql(prefix, useTruncate) {
        const p = prefix;

        // Woo lookup tables (HPOS / analytics) that are safe to clear.
        const wcLookupTables = [
            "wc_product_meta_lookup",
            "wc_product_attributes_lookup",
            "wc_category_lookup",
            "wc_download_log",
            "wc_rate_limits",
            "wc_reserved_stock",
        ];

        const lines = [];

        lines.push("-- =========================================");
        lines.push("-- WooCommerce: Delete ALL products + variations");
        lines.push(`-- Prefix: ${p}`);
        lines.push("-- WARNING: destructive operation. Backup first.");
        lines.push("-- =========================================");
        lines.push("");
        lines.push("START TRANSACTION;");
        lines.push("");

        // 1) Delete relationships to product terms first.
        lines.push("-- Remove term relationships for products/variations");
        lines.push(
            `DELETE tr FROM ${p}term_relationships tr
INNER JOIN ${p}posts p2 ON p2.ID = tr.object_id
WHERE p2.post_type IN ('product','product_variation');`
        );
        lines.push("");

        // 2) Delete product meta.
        lines.push("-- Remove postmeta for products/variations");
        lines.push(
            `DELETE pm FROM ${p}postmeta pm
INNER JOIN ${p}posts p2 ON p2.ID = pm.post_id
WHERE p2.post_type IN ('product','product_variation');`
        );
        lines.push("");

        // 3) Delete orphaned attachments attached to products (optional-ish).
        // Keeping it conservative: only attachments with post_parent being a product/variation.
        lines.push("-- Remove attachments whose parent is a product/variation (optional)");
        lines.push(
            `DELETE a FROM ${p}posts a
INNER JOIN ${p}posts parent ON parent.ID = a.post_parent
WHERE a.post_type = 'attachment'
  AND parent.post_type IN ('product','product_variation');`
        );
        lines.push("");

        // 4) Delete the products themselves.
        lines.push("-- Delete products and variations");
        lines.push(
            `DELETE FROM ${p}posts
WHERE post_type IN ('product','product_variation');`
        );
        lines.push("");

        // 5) Clear Woo lookup tables (optional / faster).
        if (useTruncate) {
            lines.push("-- Clear WooCommerce lookup tables (TRUNCATE)");
            wcLookupTables.forEach((t) => lines.push(`TRUNCATE TABLE ${p}${t};`));
        } else {
            lines.push("-- Clear WooCommerce lookup tables (DELETE)");
            wcLookupTables.forEach((t) => lines.push(`DELETE FROM ${p}${t};`));
        }

        lines.push("");
        lines.push("COMMIT;");
        lines.push("");

        return lines.join("\n");
    }

    async function copyToClipboard(text, textarea, toastEl) {
        try {
            await navigator.clipboard.writeText(text);
            if (toastEl) {
                toastEl.style.display = "inline";
                setTimeout(() => (toastEl.style.display = "none"), 2200);
            }
        } catch (e) {
            // fallback: select
            if (textarea) {
                textarea.focus();
                textarea.select();
            }
        }
    }

    function initOne(root) {
        const uid = root.id;

        const prefixInput = root.querySelector(`#${uid}-prefix`);
        const truncateInput = root.querySelector(`#${uid}-truncate`);
        const textarea = root.querySelector(`#${uid}-out`);
        const toast = root.querySelector("[data-toast]");

        let cm = null;

        // CodeMirror init (WP built-in)
        if (window.wp && wp.codeEditor && textarea) {
            const settings =
                { codemirror: { mode: "text/x-sql", readOnly: true, lineNumbers: true, lineWrapping: true } };

            const ed = wp.codeEditor.initialize(textarea, settings);
            cm = ed && ed.codemirror ? ed.codemirror : null;
        }

        function setOutput(text) {
            if (cm) {
                cm.setValue(text);
                cm.refresh();
            } else {
                textarea.value = text;
            }
        }

        root.addEventListener("click", async (e) => {
            const btn = e.target.closest('[data-action="generate"]');
            if (!btn) return;

            const prefix = normalizePrefix(prefixInput.value);

            if (!prefix) {
                alert(__('Please enter a valid prefix.', 'tools-for-devs'));
                return;
            }

            const out = sql(prefix, !!truncateInput.checked);
            setOutput(out);

            // auto-copy
            await copyToClipboard(out, textarea, toast);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-wc-del]").forEach(initOne);
    });
})();
