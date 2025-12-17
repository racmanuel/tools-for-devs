(function () {
    "use strict";

    const __ =
        window.wp && wp.i18n && wp.i18n.__ ? wp.i18n.__ : (t) => t;

    function normalizePrefix(p) {
        p = (p || "").trim();
        if (!p) return "";
        return p.endsWith("_") ? p : p + "_";
    }

    function parseSiteIds(input) {
        const raw = (input || "").trim();
        if (!raw) return [];
        return raw
            .split(",")
            .map((x) => parseInt(x.trim(), 10))
            .filter((n) => Number.isInteger(n) && n > 0);
    }

    function woocommerceSuffixes() {
        return [
            "wc_admin_notes",
            "wc_admin_note_actions",
            "wc_category_lookup",
            "wc_customer_lookup",
            "wc_download_log",
            "wc_orders",
            "wc_orders_meta",
            "wc_order_addresses",
            "wc_order_coupon_lookup",
            "wc_order_operational_data",
            "wc_order_product_lookup",
            "wc_order_stats",
            "wc_order_tax_lookup",
            "wc_product_attributes_lookup",
            "wc_product_download_directories",
            "wc_product_meta_lookup",
            "wc_rate_limits",
            "wc_reserved_stock",
            "wc_tax_rate_classes",
            "wc_webhooks",
            "woocommerce_api_keys",
            "woocommerce_attribute_taxonomies",
            "woocommerce_downloadable_product_permissions",
            "woocommerce_log",
            "woocommerce_order_itemmeta",
            "woocommerce_order_items",
            "woocommerce_payment_tokenmeta",
            "woocommerce_payment_tokens",
            "woocommerce_sessions",
            "woocommerce_shipping_zones",
            "woocommerce_shipping_zone_locations",
            "woocommerce_shipping_zone_methods",
            "woocommerce_tax_rates",
            "woocommerce_tax_rate_locations",
            "actionscheduler_actions",
            "actionscheduler_claims",
            "actionscheduler_groups",
            "actionscheduler_logs",
        ];
    }

    function renameLine(oldPrefix, newPrefix, tableName) {
        return `RENAME TABLE ${oldPrefix}${tableName} TO ${newPrefix}${tableName};`;
    }

    function updateOptionsLine(optionsTable, oldPrefix, newPrefix) {
        return `UPDATE ${optionsTable} SET option_name = REPLACE(option_name, '${oldPrefix}', '${newPrefix}') WHERE option_name LIKE '${oldPrefix}%';`;
    }

    function updateUsermetaLine(usermetaTable, oldPrefix, newPrefix) {
        return `UPDATE ${usermetaTable} SET meta_key = REPLACE(meta_key, '${oldPrefix}', '${newPrefix}') WHERE meta_key LIKE '${oldPrefix}%';`;
    }

    function buildSql(oldPrefix, newPrefix, isWoo, isMultisite, siteIds) {
        const lines = [];

        // Base site (matches your style/order)
        const ordered = [
            "comments",
            "commentmeta",
            "options",
            "postmeta",
            "posts",
            "terms",
            "termmeta",
            "term_relationships",
            "term_taxonomy",
        ];

        ordered.forEach((t) => lines.push(renameLine(oldPrefix, newPrefix, t)));

        lines.push(updateOptionsLine(`${newPrefix}options`, oldPrefix, newPrefix));

        lines.push(renameLine(oldPrefix, newPrefix, "usermeta"));
        lines.push(renameLine(oldPrefix, newPrefix, "users"));

        lines.push(updateUsermetaLine(`${newPrefix}usermeta`, oldPrefix, newPrefix));

        if (isWoo) {
            woocommerceSuffixes().forEach((t) =>
                lines.push(renameLine(oldPrefix, newPrefix, t))
            );
        }

        // Multisite subsites: wp_2_ -> wpime_2_
        if (isMultisite && siteIds.length) {
            siteIds.forEach((id) => {
                const oldSitePrefix = `${oldPrefix}${id}_`;
                const newSitePrefix = `${newPrefix}${id}_`;

                ordered.forEach((t) =>
                    lines.push(renameLine(oldSitePrefix, newSitePrefix, t))
                );

                lines.push(
                    updateOptionsLine(`${newSitePrefix}options`, oldSitePrefix, newSitePrefix)
                );

                // Update usermeta keys for that blog id (capabilities/roles)
                lines.push(
                    updateUsermetaLine(`${newPrefix}usermeta`, oldSitePrefix, newSitePrefix)
                );

                if (isWoo) {
                    woocommerceSuffixes().forEach((t) =>
                        lines.push(renameLine(oldSitePrefix, newSitePrefix, t))
                    );
                }
            });
        }

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
            // Fallback manual
            if (textarea) {
                textarea.focus();
                textarea.select();
            }
        }
    }

    function initOne(root) {
        const uid = root.id;

        const oldInput = root.querySelector(`#${uid}-old`);
        const newInput = root.querySelector(`#${uid}-new`);
        const sitesWrap = root.querySelector(".tfd-dbp-sites");
        const sitesInput = root.querySelector(`#${uid}-sites`);
        const wooInput = root.querySelector(`#${uid}-woo`);
        const msInput = root.querySelector(`#${uid}-ms`);
        const textarea = root.querySelector(`#${uid}-out`);
        const toast = root.querySelector("[data-toast]");

        let cm = null;

        // CodeMirror init (WP built-in)
        if (window.wp && wp.codeEditor && textarea) {
            const settings =
                (window.ToolsForDevsDbPrefixTool && window.ToolsForDevsDbPrefixTool.codeEditor) ||
                { codemirror: { mode: "text/x-sql", readOnly: true, lineNumbers: true, lineWrapping: true } };

            const ed = wp.codeEditor.initialize(textarea, settings);
            cm = ed && ed.codemirror ? ed.codemirror : null;
        }

        function setOutput(sql) {
            if (cm) {
                cm.setValue(sql);
                cm.refresh();
            } else {
                textarea.value = sql;
            }
        }

        function getOutput() {
            return cm ? cm.getValue() : textarea.value;
        }

        function toggleSitesField() {
            const show = !!msInput.checked;
            if (sitesWrap) {
                sitesWrap.style.display = show ? "" : "none";
            }
            if (!show && sitesInput) {
                sitesInput.value = "";
            }
        }

        // Initial state
        toggleSitesField();

        // Toggle on change
        msInput.addEventListener("change", toggleSitesField);

        root.addEventListener("click", async (e) => {
            const genBtn = e.target.closest('[data-action="generate"]');
            if (!genBtn) return;

            const oldPrefix = normalizePrefix(oldInput.value);
            const newPrefix = normalizePrefix(newInput.value);

            if (!oldPrefix) {
                alert(__('Please enter a valid old prefix.', 'tools-for-devs'));
                return;
            }
            if (!newPrefix) {
                alert(__('Please enter a valid new prefix.', 'tools-for-devs'));
                return;
            }

            const isWoo = !!wooInput.checked;      // ✅ not checked by default
            const isMultisite = !!msInput.checked;
            const siteIds = isMultisite ? parseSiteIds(sitesInput.value) : [];

            const sql = buildSql(oldPrefix, newPrefix, isWoo, isMultisite, siteIds);
            setOutput(sql);

            // ✅ Auto-copy on generate (like previous tool)
            await copyToClipboard(sql, textarea, toast);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-tfd-dbp]").forEach(initOne);
    });
})();
