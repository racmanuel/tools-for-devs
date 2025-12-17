(function () {
    "use strict";

    const __ =
        window.wp && wp.i18n && wp.i18n.__ ? wp.i18n.__ : (t) => t;

    function clean(v) {
        return String(v || "").trim();
    }

    function normalizeList(listStr) {
        return clean(listStr)
            .split(",")
            .map((s) => s.trim())
            .filter(Boolean);
    }

    function uniq(arr) {
        return Array.from(new Set(arr));
    }

    function buildHeader(d) {
        const lines = [];
        lines.push("<?php");
        lines.push("/**");
        lines.push(` * Plugin Name: ${d.plugin_name}`);

        if (d.plugin_uri) lines.push(` * Plugin URI: ${d.plugin_uri}`);
        if (d.description) lines.push(` * Description: ${d.description}`);
        if (d.version) lines.push(` * Version: ${d.version}`);
        if (d.author) lines.push(` * Author: ${d.author}`);
        if (d.author_uri) lines.push(` * Author URI: ${d.author_uri}`);
        if (d.license) lines.push(` * License: ${d.license}`);
        if (d.license_uri) lines.push(` * License URI: ${d.license_uri}`);

        let required = normalizeList(d.required_plugins);
        if (d.woocommerce) required.push("woocommerce");
        required = uniq(required);

        if (required.length) {
            lines.push(` * Requires Plugins: ${required.join(", ")}`);
        }

        if (d.text_domain) lines.push(` * Text Domain: ${d.text_domain}`);
        if (d.domain_path) lines.push(` * Domain Path: ${d.domain_path}`);
        if (d.network === "true") lines.push(" * Network: true");

        lines.push(" */");
        lines.push("");
        lines.push("// Prevent direct access to this file");
        lines.push("if ( ! defined( 'ABSPATH' ) ) {");
        lines.push("\texit;");
        lines.push("}");
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
            if (textarea) {
                textarea.focus();
                textarea.select();
            }
        }
    }

    function initOne(root) {
        const uid = root.id;

        const $ = (sel) => root.querySelector(sel);

        const name = $(`#${uid}-name`);
        const uri = $(`#${uid}-uri`);
        const version = $(`#${uid}-version`);
        const desc = $(`#${uid}-desc`);
        const author = $(`#${uid}-author`);
        const authorUri = $(`#${uid}-author-uri`);
        const license = $(`#${uid}-license`);
        const licenseUri = $(`#${uid}-license-uri`);
        const textDomain = $(`#${uid}-text-domain`);
        const domainPath = $(`#${uid}-domain-path`);
        const network = $(`#${uid}-network`);
        const required = $(`#${uid}-required`);
        const woo = $(`#${uid}-woo`);

        const textarea = $(`#${uid}-out`);
        const toast = root.querySelector("[data-toast]");

        let cm = null;

        if (window.wp && wp.codeEditor && textarea) {
            const settings =
                (window.ToolsForDevsPluginHeaderTool && window.ToolsForDevsPluginHeaderTool.codeEditor) ||
                { codemirror: { mode: "text/x-php", readOnly: true, lineNumbers: true, lineWrapping: true } };

            const ed = wp.codeEditor.initialize(textarea, settings);
            cm = ed && ed.codemirror ? ed.codemirror : null;
        }

        function setOutput(code) {
            if (cm) {
                cm.setValue(code);
                cm.refresh();
            } else {
                textarea.value = code;
            }
        }

        function getOutput() {
            return cm ? cm.getValue() : textarea.value;
        }

        root.addEventListener("click", async (e) => {
            const btn = e.target.closest('[data-action="generate"]');
            if (!btn) return;

            if (!clean(name.value)) {
                alert(__('Please enter a Plugin Name.', 'tools-for-devs'));
                return;
            }

            const code = buildHeader({
                plugin_name: clean(name.value),
                plugin_uri: clean(uri.value),
                description: clean(desc.value),
                version: clean(version.value),
                author: clean(author.value),
                author_uri: clean(authorUri.value),
                license: clean(license.value),
                license_uri: clean(licenseUri.value),
                text_domain: clean(textDomain.value),
                domain_path: clean(domainPath.value),
                network: clean(network.value),
                required_plugins: clean(required.value),
                woocommerce: !!woo.checked,
            });

            setOutput(code);

            // ✅ Same behavior as your other generators: auto-copy after generate.
            await copyToClipboard(code, textarea, toast);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-tfd-phg]").forEach(initOne);
    });
})();
