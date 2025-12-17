(function () {
    "use strict";

    const __ = (window.wp && wp.i18n && wp.i18n.__) ? wp.i18n.__ : (t) => t;

    const slugify = (s) =>
        String(s || "")
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9_]+/g, "_")
            .replace(/^_+|_+$/g, "");

    const pascalCase = (s) =>
        slugify(s)
            .split("_")
            .filter(Boolean)
            .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
            .join("");

    function buildPhp(d) {
        const cls = d.className || `CFB_Field_${pascalCase(d.fieldName)}`;

        const supports = [];
        supports.push(`'escaping_html' => false`);
        supports.push(`'required'      => ${d.supportsRequired ? "true" : "false"}`);

        return `<?php
if ( ! class_exists( 'acf_field' ) ) {
	return;
}

class ${cls} extends acf_field {

	public function __construct() {
		$this->name     = '${d.fieldName}';
		$this->label    = __( '${d.fieldLabel}', '${d.textDomain}' );
		$this->category = '${d.category}';

		$this->defaults = array(
			'placeholder' => '',
		);

		$this->supports = array(
			${supports.join(",\n\t\t\t")}
		);

		$this->show_in_rest = ${d.showInRest ? "true" : "false"};

		parent::__construct();
	}

	public function render_field_settings( $field ) : void {

		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Placeholder', '${d.textDomain}' ),
				'name'         => 'placeholder',
				'type'         => 'text',
				'placeholder'  => __( 'Type something…', '${d.textDomain}' ),
			)
		);

	}

	public function render_field( $field ) : void {

		$placeholder = isset( $field['placeholder'] ) ? (string) $field['placeholder'] : '';

		printf(
			'<input type="text" class="%s" name="%s" value="%s" placeholder="%s" />',
			esc_attr( 'acf-${d.fieldName}-input' ),
			esc_attr( $field['name'] ),
			esc_attr( (string) $field['value'] ),
			esc_attr( $placeholder )
		);
	}

	public function input_admin_enqueue_scripts() : void {

		$version = defined( '${d.versionConst}' ) ? ${d.versionConst} : '1.0.0';

		${d.hasJs ? `wp_enqueue_script(
			'acf-${d.fieldName}',
			plugin_dir_url( __FILE__ ) . 'assets/js/acf-${d.fieldName}.js',
			array( 'acf-input' ),
			$version,
			true
		);` : ""}

		${d.hasCss ? `wp_enqueue_style(
			'acf-${d.fieldName}',
			plugin_dir_url( __FILE__ ) . 'assets/css/acf-${d.fieldName}.css',
			array(),
			$version
		);` : ""}

	}

}

// Register.
acf_register_field_type( '${cls}' );
`;
    }

    function buildJs(d) {
        return `(function($){
	"use strict";

	if ( typeof acf === "undefined" ) {
		return;
	}

	var Field = acf.Field.extend({
		type: "${d.fieldName}",

		events: {
			"input .acf-${d.fieldName}-input": "onChange"
		},

		onChange: function(e){
			// Example: you can validate or transform input here.
			// console.log("Value:", this.$(".acf-${d.fieldName}-input").val());
		}
	});

	acf.registerFieldType(Field);

})(jQuery);
`;
    }

    function buildCss(d) {
        return `.acf-${d.fieldName}-input{
	width: 100%;
	max-width: 100%;
}
`;
    }

    function buildBundle(d) {
        const parts = [];
        parts.push("/* ===============================");
        parts.push(" * PHP: Field Class");
        parts.push(" * =============================== */");
        parts.push(buildPhp(d));

        if (d.hasJs) {
            parts.push("");
            parts.push("/* ===============================");
            parts.push(" * JS: acf.Field.extend");
            parts.push(" * File: assets/js/acf-" + d.fieldName + ".js");
            parts.push(" * =============================== */");
            parts.push(buildJs(d));
        }

        if (d.hasCss) {
            parts.push("");
            parts.push("/* ===============================");
            parts.push(" * CSS: Field Styles");
            parts.push(" * File: assets/css/acf-" + d.fieldName + ".css");
            parts.push(" * =============================== */");
            parts.push(buildCss(d));
        }

        return parts.join("\n");
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
        const label = $(`#${uid}-label`);
        const category = $(`#${uid}-category`);
        const className = $(`#${uid}-class`);
        const textDomain = $(`#${uid}-textdomain`);
        const versionConst = $(`#${uid}-versionconst`);

        const hasJs = $(`#${uid}-hasjs`);
        const hasCss = $(`#${uid}-hascss`);
        const showInRest = $(`#${uid}-supportsrest`);
        const supportsRequired = $(`#${uid}-supportsrequired`);

        const textarea = $(`#${uid}-out`);
        const toast = root.querySelector("[data-toast]");

        let cm = null;

        if (window.wp && wp.codeEditor && textarea) {
            const settings =
                (window.ToolsForDevsAcfFieldGen && window.ToolsForDevsAcfFieldGen.codeEditor) ||
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

        root.addEventListener("click", async (e) => {
            const btn = e.target.closest('[data-action="generate"]');
            if (!btn) return;

            const fieldName = slugify(name.value);
            if (!fieldName) {
                alert(__('Please enter a valid Field Name.', 'tools-for-devs'));
                return;
            }

            const d = {
                fieldName,
                fieldLabel: clean(label.value) || pascalCase(fieldName),
                category: clean(category.value) || "basic",
                className: clean(className.value),
                textDomain: clean(textDomain.value) || "my-plugin",
                versionConst: clean(versionConst.value) || "MY_PLUGIN_VERSION",
                hasJs: !!hasJs.checked,
                hasCss: !!hasCss.checked,
                showInRest: !!showInRest.checked,
                supportsRequired: !!supportsRequired.checked,
            };

            const code = buildBundle(d);
            setOutput(code);
            await copyToClipboard(code, textarea, toast);
        });

        function clean(v) { return String(v || "").trim(); }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-acfgen]").forEach(initOne);
    });
})();
