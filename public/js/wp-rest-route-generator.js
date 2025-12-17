(function () {
	"use strict";

	const __ = (window.wp && wp.i18n && wp.i18n.__) ? wp.i18n.__ : (t) => t;

	function clean(v) { return String(v || "").trim(); }

	function ensureLeadingSlash(route) {
		route = clean(route);
		if (!route) return "";
		return route.startsWith("/") ? route : "/" + route;
	}

	function phpString(s) {
		return String(s || "").replace(/\\/g, "\\\\").replace(/'/g, "\\'");
	}

	function methodToWpConst(method) {
		switch (method) {
			case "GET": return "WP_REST_Server::READABLE";
			case "POST": return "WP_REST_Server::CREATABLE";
			case "PUT":
			case "PATCH": return "WP_REST_Server::EDITABLE";
			case "DELETE": return "WP_REST_Server::DELETABLE";
			default: return "WP_REST_Server::READABLE";
		}
	}

	function buildPermissionCallback(mode, capability, indent) {
		const pad = indent || "\t\t\t\t\t";
		if (mode === "public") return `${pad}return true;`;
		if (mode === "logged_in") return `${pad}return is_user_logged_in();`;
		const cap = clean(capability) || "manage_options";
		return `${pad}return current_user_can( '${phpString(cap)}' );`;
	}

	// Detect route params: /items/(?P<id>\d+)/foo/(?P<slug>[-\w]+)
	function detectPathParams(route) {
		const found = [];
		const re = /\(\?P<([a-zA-Z0-9_]+)>([^)]+)\)/g;
		let m;
		while ((m = re.exec(route)) !== null) {
			const name = m[1];
			const pattern = m[2] || "";
			let type = "string";
			let sanitize = "sanitize_text_field";

			if (pattern.includes("\\d") || pattern.includes("[0-9]") || pattern === "\\d+") {
				type = "integer";
				sanitize = "absint";
			}

			found.push({
				name,
				in: "path",
				type,
				required: true,
				sanitize,
				__tfd_from_path: true,
			});
		}
		return found;
	}

	function argsToPhpArray(args, baseIndent) {
		const indent1 = baseIndent || "\t\t\t";
		const indent2 = indent1 + "\t";
		const indent3 = indent2 + "\t";

		const items = [];

		args.forEach((a) => {
			const name = clean(a.name);
			if (!name) return;

			const lines = [];
			lines.push(`'type'     => '${phpString(a.type || "string")}'`);
			lines.push(`'required' => ${a.required ? "true" : "false"}`);

			const san = clean(a.sanitize);
			if (san) {
				// function name only (simple MVP)
				lines.push(`'sanitize_callback' => '${phpString(san)}'`);
			}

			items.push(
				`${indent2}'${phpString(name)}' => array(\n` +
				`${indent3}${lines.join(`,\n${indent3}`)}\n` +
				`${indent2})`
			);
		});

		if (!items.length) return "array()";

		return `array(\n${items.join(",\n")}\n${indent1})`;
	}

	function buildCurlExample(d) {
		// Build a URL example: https://example.com/wp-json/ns/route
		// Replace path params with sample values.
		let route = d.route;
		const params = detectPathParams(route);
		params.forEach((p) => {
			// replace (?P<id>\d+) with 123, others with "example"
			const sample = p.type === "integer" ? "123" : "example";
			route = route.replace(new RegExp(`\\(\\?P<${p.name}>[^\\)]+\\)`), sample);
		});

		const url = `https://example.com/wp-json/${d.namespace}${route}`;

		const method = d.method.toUpperCase();
		if (method === "GET" || method === "DELETE") {
			return `curl -X ${method} "${url}"`;
		}

		// For POST/PUT/PATCH include JSON body from body args
		const bodyArgs = d.args.filter(a => (a.in || "query") === "body" && clean(a.name));
		const json = {};
		bodyArgs.forEach(a => {
			const t = a.type || "string";
			if (t === "integer") json[a.name] = 1;
			else if (t === "number") json[a.name] = 1.5;
			else if (t === "boolean") json[a.name] = true;
			else if (t === "array") json[a.name] = [];
			else if (t === "object") json[a.name] = {};
			else json[a.name] = "value";
		});

		return [
			`curl -X ${method} "${url}" \\`,
			`  -H "Content-Type: application/json" \\`,
			`  -d '${JSON.stringify(json, null, 2)}'`
		].join("\n");
	}

	function makeFunctionName(namespace, routePath, httpMethod) {
		return (
			"tfd_rest_" +
			(namespace + "_" + routePath + "_" + httpMethod)
				.replace(/[^a-zA-Z0-9_]+/g, "_")
				.replace(/^_+|_+$/g, "")
				.toLowerCase()
		);
	}

	function makeControllerClassName(namespace, routePath) {
		const base = (namespace + "_" + routePath)
			.replace(/[^a-zA-Z0-9_]+/g, "_")
			.replace(/^_+|_+$/g, "")
			.toLowerCase()
			.split("_")
			.filter(Boolean)
			.map(s => s.charAt(0).toUpperCase() + s.slice(1))
			.join("_");

		return `TFD_REST_${base}_Controller`;
	}

	function buildSimpleCode(d) {
		const fn = makeFunctionName(d.namespace, d.route, d.method);
		const wpMethod = methodToWpConst(d.method);

		const argsPhp = argsToPhpArray(d.args, "\t\t\t");

		const curl = buildCurlExample(d);

		return `<?php
/**
 * REST Route: ${d.namespace}${d.route}
 * Method: ${d.method}
 *
 * Example curl:
 * ${curl.split("\n").map(l => " * " + l).join("\n")}
 */

add_action( 'rest_api_init', function () {

	register_rest_route(
		'${phpString(d.namespace)}',
		'${phpString(d.route)}',
		array(
			'methods'             => ${wpMethod},
			'callback'            => '${phpString(fn)}',
			'permission_callback' => function( WP_REST_Request $request ) {
${buildPermissionCallback(d.permissionMode, d.capability, "\t\t\t\t\t")}
			},
			'args'                => ${argsPhp},
		)
	);

} );

/**
 * Endpoint callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function ${fn}( WP_REST_Request $request ) {

	// Examples:
	// $id = $request->get_param( 'id' );
	// $body = $request->get_json_params();

	$data = array(
		'ok' => true,
	);

	return rest_ensure_response( $data );
}
`;
	}

	function buildControllerCode(d) {
		const className = makeControllerClassName(d.namespace, d.route);
		const wpMethod = methodToWpConst(d.method);

		// In controller mode, we still keep "rest_base" nice if possible:
		// If route is "/items" -> rest_base = "items"
		// If route contains regex, we keep full route in register_routes.
		const restBaseGuess = clean(d.route)
			.replace(/^\//, "")
			.split("/")[0] || "items";

		const argsPhp = argsToPhpArray(d.args, "\t\t\t\t\t");

		const curl = buildCurlExample(d);

		return `<?php
/**
 * REST Route: ${d.namespace}${d.route}
 * Method: ${d.method}
 *
 * Example curl:
 * ${curl.split("\n").map(l => " * " + l).join("\n")}
 */

class ${className} extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = '${phpString(d.namespace)}';
		$this->rest_base = '${phpString(restBaseGuess)}';
	}

	/**
	 * Register routes.
	 */
	public function register_routes() : void {

		register_rest_route(
			$this->namespace,
			'${phpString(d.route)}',
			array(
				array(
					'methods'             => ${wpMethod},
					'callback'            => array( $this, 'handle_request' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => ${argsPhp},
				),
			)
		);
	}

	/**
	 * Permissions check.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function permissions_check( WP_REST_Request $request ) {
${buildPermissionCallback(d.permissionMode, d.capability, "\t\t")}
	}

	/**
	 * Main handler for this route.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_request( WP_REST_Request $request ) {

		// Examples:
		// $id = $request->get_param( 'id' );
		// $body = $request->get_json_params();

		$data = array(
			'ok' => true,
		);

		return rest_ensure_response( $data );
	}
}

add_action( 'rest_api_init', function () {
	$controller = new ${className}();
	$controller->register_routes();
} );
`;
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

	function createArgRow(root, data) {
		const row = document.createElement("div");
		row.className = "rrg-args-row";
		row.dataset.argRow = "1";

		row.innerHTML = `
			<div><input type="text" data-col="name" placeholder="id"></div>
			<div>
				<select data-col="in">
					<option value="path">path</option>
					<option value="query">query</option>
					<option value="body">body</option>
				</select>
			</div>
			<div>
				<select data-col="type">
					<option value="string">string</option>
					<option value="integer">integer</option>
					<option value="number">number</option>
					<option value="boolean">boolean</option>
					<option value="array">array</option>
					<option value="object">object</option>
				</select>
			</div>
			<div>
				<select data-col="required">
					<option value="no">No</option>
					<option value="yes">Yes</option>
				</select>
			</div>
			<div><input type="text" data-col="sanitize" placeholder="sanitize_text_field"></div>
			<div><button type="button" class="rrg-mini-btn rrg-mini-btn--danger" data-action="remove-arg">×</button></div>
		`;

		const set = (col, val) => {
			const el = row.querySelector(`[data-col="${col}"]`);
			if (!el) return;
			el.value = (val === undefined || val === null) ? "" : String(val);
		};

		set("name", data?.name || "");
		set("in", data?.in || "query");
		set("type", data?.type || "string");
		set("required", data?.required ? "yes" : "no");
		set("sanitize", data?.sanitize || "");

		// lock-ish if from path auto detect (still editable, but tag it)
		if (data && data.__tfd_from_path) row.dataset.fromPath = "1";

		return row;
	}

	function readArgsFromTable(tableEl) {
		const rows = Array.from(tableEl.querySelectorAll('[data-arg-row="1"]'));
		return rows.map(r => {
			const get = (col) => clean(r.querySelector(`[data-col="${col}"]`)?.value);
			return {
				name: get("name"),
				in: get("in") || "query",
				type: get("type") || "string",
				required: get("required") === "yes",
				sanitize: get("sanitize"),
				__tfd_from_path: r.dataset.fromPath === "1",
			};
		}).filter(a => !!clean(a.name));
	}

	function mergePathParamsIntoTable(tableEl, route) {
		const detected = detectPathParams(route);
		if (!detected.length) return;

		const existing = readArgsFromTable(tableEl);
		const existingNames = new Set(existing.map(a => a.name));

		detected.forEach(p => {
			if (existingNames.has(p.name)) return;
			tableEl.appendChild(createArgRow(tableEl, p));
		});
	}

	function initOne(root) {
		const uid = root.id;
		const $ = (sel) => root.querySelector(sel);

		const ns = $(`#${uid}-ns`);
		const route = $(`#${uid}-route`);
		const method = $(`#${uid}-method`);
		const perm = $(`#${uid}-perm`);
		const capWrap = root.querySelector(".rrg-cap");
		const cap = $(`#${uid}-cap`);
		const controller = $(`#${uid}-controller`);

		const argsTable = root.querySelector("[data-args-table]");
		const textarea = $(`#${uid}-out`);
		const toast = root.querySelector("[data-toast]");

		let cm = null;

		if (window.wp && wp.codeEditor && textarea) {
			const settings =
				(window.ToolsForDevsRestRouteGen && window.ToolsForDevsRestRouteGen.codeEditor) ||
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

		function toggleCap() {
			const show = (perm.value === "capability");
			if (capWrap) capWrap.style.display = show ? "" : "none";
		}
		toggleCap();
		perm.addEventListener("change", toggleCap);

		// Start with one empty row (query param).
		argsTable.appendChild(createArgRow(argsTable, { in: "query", type: "string", required: false, sanitize: "sanitize_text_field" }));

		// Auto-detect path params on load and on route change.
		function syncPathParams() {
			mergePathParamsIntoTable(argsTable, clean(route.value));
		}
		syncPathParams();
		route.addEventListener("input", syncPathParams);
		route.addEventListener("change", syncPathParams);

		root.addEventListener("click", async (e) => {
			const addBtn = e.target.closest('[data-action="add-arg"]');
			if (addBtn) {
				argsTable.appendChild(createArgRow(argsTable, { in: "query", type: "string", required: false, sanitize: "sanitize_text_field" }));
				return;
			}

			const removeBtn = e.target.closest('[data-action="remove-arg"]');
			if (removeBtn) {
				const row = removeBtn.closest('[data-arg-row="1"]');
				if (row) row.remove();
				return;
			}

			const genBtn = e.target.closest('[data-action="generate"]');
			if (!genBtn) return;

			const namespace = clean(ns.value);
			const routePath = ensureLeadingSlash(route.value);

			if (!namespace) {
				alert(__('Please enter a namespace (e.g. my-plugin/v1).', 'tools-for-devs'));
				return;
			}
			if (!routePath) {
				alert(__('Please enter a route (e.g. /items or /items/(?P<id>\\d+)).', 'tools-for-devs'));
				return;
			}

			// Ensure path params exist in table before generating.
			mergePathParamsIntoTable(argsTable, routePath);

			const httpMethod = clean(method.value) || "GET";
			const permissionMode = clean(perm.value) || "public";
			const capability = clean(cap.value);
			const args = readArgsFromTable(argsTable);

			const d = {
				namespace,
				route: routePath,
				method: httpMethod,
				permissionMode,
				capability,
				args,
			};

			const code = controller.checked ? buildControllerCode(d) : buildSimpleCode(d);

			setOutput(code);
			await copyToClipboard(code, textarea, toast);
		});
	}

	document.addEventListener("DOMContentLoaded", function () {
		document.querySelectorAll("[data-rrg]").forEach(initOne);
	});
})();
