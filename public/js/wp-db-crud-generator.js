(function () {
    "use strict";

    /**
     * Tools for Devs - DB CRUD Generator (front-end shortcode)
     * - Dynamic columns table (user-defined)
     * - Enforces a locked primary key `id` row (non-editable)
     * - Generates a single-file plugin output (copy/paste ready)
     * - Auto-copies to clipboard on Generate
     */

    function clean(v) {
        return String(v || "").trim();
    }

    function slug(v) {
        return clean(v)
            .toLowerCase()
            .replace(/[^a-z0-9_]+/g, "_")
            .replace(/^_+|_+$/g, "");
    }

    function pascal(v) {
        var p = slug(v).split("_").filter(Boolean);
        for (var i = 0; i < p.length; i++) {
            p[i] = p[i].charAt(0).toUpperCase() + p[i].slice(1);
        }
        return p.join("_");
    }

    function phpString(s) {
        return String(s || "").replace(/\\/g, "\\\\").replace(/'/g, "\\'");
    }

    // -----------------------------
    // Dynamic columns UI row
    // -----------------------------
    function colRow(data) {
        data = data || {};
        var locked = !!data.locked;

        var row = document.createElement("div");
        row.className = "dbcg-row" + (locked ? " dbcg-row--locked" : "");
        row.dataset.col = "1";
        if (locked) row.dataset.locked = "1";

        if (locked) {
            row.innerHTML =
                '<div><input type="text" data-k="name" value="id" readonly></div>' +
                '<div><input type="text" data-k="type" value="BIGINT(20) UNSIGNED" readonly></div>' +
                '<div><select data-k="null" disabled><option value="NO" selected>NO</option></select></div>' +
                '<div><input type="text" data-k="default" value="" readonly></div>' +
                '<div><input type="text" data-k="extra" value="AUTO_INCREMENT" readonly></div>' +
                '<div><span class="dbcg-lock" title="Primary key (fixed)" aria-label="Primary key (fixed)">🔒</span></div>';
            return row;
        }

        row.innerHTML =
            '<div><input type="text" data-k="name" placeholder="status"></div>' +
            '<div><input type="text" data-k="type" placeholder="VARCHAR(50)"></div>' +
            '<div><select data-k="null"><option value="NO">NO</option><option value="YES">YES</option></select></div>' +
            '<div><input type="text" data-k="default" placeholder="pending"></div>' +
            '<div><input type="text" data-k="extra" placeholder=""></div>' +
            '<div><button type="button" class="dbcg-mini-btn dbcg-mini-btn--danger" data-action="rm-col" aria-label="Remove column">×</button></div>';

        row.querySelector('[data-k="name"]').value = data.name || "";
        row.querySelector('[data-k="type"]').value = data.type || "";
        row.querySelector('[data-k="null"]').value = data["null"] || "NO";
        row.querySelector('[data-k="default"]').value =
            data["default"] === undefined ? "" : String(data["default"]);
        row.querySelector('[data-k="extra"]').value = data.extra || "";

        return row;
    }

    function readColumns(colsBox) {
        var rows = colsBox.querySelectorAll('[data-col="1"]');
        var out = [];

        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];

            // Ignore locked row from "user columns" (we enforce it separately)
            if (r.dataset.locked === "1") continue;

            out.push({
                name: clean(r.querySelector('[data-k="name"]').value),
                type: clean(r.querySelector('[data-k="type"]').value),
                "null": clean(r.querySelector('[data-k="null"]').value),
                "default": clean(r.querySelector('[data-k="default"]').value),
                extra: clean(r.querySelector('[data-k="extra"]').value),
            });
        }

        // Remove empty names and reserved "id"
        return out.filter(function (c) {
            var n = slug(c.name);
            return !!n && n !== "id";
        });
    }

    // -----------------------------
    // Build column SQL lines
    // -----------------------------
    function buildColumnSqlLines(d) {
        var lines = [];

        // Always enforce a valid primary key `id`
        lines.push("`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT");

        for (var j = 0; j < d.columns.length; j++) {
            var c = d.columns[j];
            var name = slug(c.name);

            if (!name || name === "id") continue;

            var type = clean(c.type) || "VARCHAR(255)";
            var nullable = clean(c["null"]) === "YES" ? "NULL" : "NOT NULL";
            var extra = clean(c.extra);

            var def = clean(c["default"]);
            var defSql = "";

            if (def) {
                if (def.toUpperCase() === "NULL") {
                    defSql = " DEFAULT NULL";
                } else if (
                    /^CURRENT_TIMESTAMP/i.test(def) ||
                    /^\d+(\.\d+)?$/.test(def)
                ) {
                    defSql = " DEFAULT " + def;
                } else {
                    defSql = " DEFAULT '" + def.replace(/'/g, "''") + "'";
                }
            }

            lines.push(
                ("`" +
                    name +
                    "` " +
                    type +
                    " " +
                    nullable +
                    defSql +
                    (extra ? " " + extra : "")).trim()
            );
        }

        if (d.timestamps) {
            lines.push("`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            lines.push("`updated_at` DATETIME NULL DEFAULT NULL");
        }

        return lines;
    }

    function buildAllowedColumns(d) {
        var names = ["id"]; // always

        for (var i = 0; i < d.columns.length; i++) {
            var n = slug(d.columns[i].name);
            if (!n || n === "id") continue;
            names.push(n);
        }

        if (d.timestamps) {
            names.push("created_at");
            names.push("updated_at");
        }

        // unique
        var map = {};
        var out = [];
        for (var j = 0; j < names.length; j++) {
            if (!map[names[j]]) {
                map[names[j]] = 1;
                out.push(names[j]);
            }
        }
        return out;
    }

    function buildExamplePayload(d) {
        var exampleCols = [];
        for (var i = 0; i < d.columns.length; i++) {
            var nn = slug(d.columns[i].name);
            if (nn && nn !== "id") exampleCols.push(nn);
            if (exampleCols.length >= 2) break;
        }

        // prefer common keys if present
        if (exampleCols.indexOf("title") === -1) {
            exampleCols.unshift("title");
        }
        exampleCols = exampleCols.filter(function (v, idx, arr) {
            return v && arr.indexOf(v) === idx;
        });

        var pairs = [];
        for (var k = 0; k < exampleCols.length; k++) {
            var key = exampleCols[k];
            if (key === "title") pairs.push("'title' => 'Example'");
            else if (key === "status") pairs.push("'status' => 'pending'");
            else pairs.push("'" + phpString(key) + "' => 'example'");
            if (pairs.length >= 2) break;
        }

        return "array( " + (pairs.length ? pairs.join(", ") : "'title' => 'Example'") + " )";
    }

    // -----------------------------
    // Build FULL single-file plugin output
    // -----------------------------
    function buildSingleFilePluginPhp(d) {
        var entityPascal = pascal(d.entity);
        var pluginName = "TFD DB CRUD - " + entityPascal;
        var pluginSlug = "tfd-dbcrud-" + slug(d.entity);

        var tableName = slug(d.table) || ("tfd_" + slug(d.entity) + "s");

        var colsLines = buildColumnSqlLines(d);
        var colsSql = colsLines
            .map(function (l) {
                return '\t\t"' + l.replace(/"/g, '\\"') + '"';
            })
            .join(",\n");

        var allowed = buildAllowedColumns(d);
        var allowedPhp = allowed
            .map(function (n) {
                return "'" + phpString(n) + "'";
            })
            .join(", ");

        var restBase = slug(d.entity);
        var restNamespace = clean(d.namespace) || "tfd-dbcrud/v1";
        var capability = clean(d.capability) || "manage_options";

        var enableRest = d.rest ? "true" : "false";
        var addTimestamps = d.timestamps ? "true" : "false";
        var useCharsetCollate = d.charset ? "true" : "false";

        var constPrefix = pluginSlug.toUpperCase().replace(/-/g, "_");
        var curlBase = "https://example.com/wp-json/" + restNamespace + "/" + restBase;

        var phpExampleData = buildExamplePayload(d);

        return (
            "<?php\n" +
            "/**\n" +
            " * Plugin Name: " + phpString(pluginName) + "\n" +
            " * Description: Generated plugin: custom table (dbDelta + upgrades), $wpdb repository CRUD, and optional REST CRUD endpoints.\n" +
            " * Version: " + phpString(d.version || "1.0.0") + "\n" +
            " * Author: Tools for Devs Generator\n" +
            " * Requires at least: 6.0\n" +
            " * Requires PHP: 7.4\n" +
            " */\n\n" +
            "if ( ! defined( 'ABSPATH' ) ) { exit; }\n\n" +

            "define( '" + constPrefix + "_VERSION', '" + phpString(d.version || "1.0.0") + "' );\n" +
            "define( '" + constPrefix + "_ENTITY', '" + restBase + "' );\n" +
            "define( '" + constPrefix + "_TABLE', '" + tableName + "' );\n" +
            "define( '" + constPrefix + "_OPTION_KEY', '" + phpString(d.optionKey || "tfd_dbcrud_version") + "' );\n" +
            "define( '" + constPrefix + "_REST_NAMESPACE', '" + phpString(restNamespace) + "' );\n" +
            "define( '" + constPrefix + "_CAPABILITY_WRITE', '" + phpString(capability) + "' );\n" +
            "define( '" + constPrefix + "_ENABLE_REST', " + enableRest + " );\n" +
            "define( '" + constPrefix + "_ADD_TIMESTAMPS', " + addTimestamps + " );\n" +
            "define( '" + constPrefix + "_USE_CHARSET_COLLATE', " + useCharsetCollate + " );\n\n" +

            "function " + constPrefix.toLowerCase() + "_columns_sql(): array {\n" +
            "\treturn array(\n" + colsSql + "\n\t);\n" +
            "}\n\n" +

            "final class " + constPrefix + "_Installer {\n" +
            "\tpublic static function table(): string {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\treturn $wpdb->prefix . " + constPrefix + "_TABLE;\n" +
            "\t}\n\n" +
            "\tpublic static function target_version(): string {\n" +
            "\t\treturn (string) " + constPrefix + "_VERSION;\n" +
            "\t}\n\n" +
            "\tpublic static function install(): void {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\trequire_once ABSPATH . 'wp-admin/includes/upgrade.php';\n" +
            "\t\t$table_name = self::table();\n" +
            "\t\t$charset_collate = '';\n" +
            "\t\tif ( " + constPrefix + "_USE_CHARSET_COLLATE ) {\n" +
            "\t\t\t$charset_collate = $wpdb->get_charset_collate();\n" +
            "\t\t}\n" +
            "\t\t$columns = " + constPrefix.toLowerCase() + "_columns_sql();\n\n" +
            "\t\t$sql = \"CREATE TABLE {$table_name} (\\n\\t\\t\" . implode( \",\\n\\t\\t\", $columns ) . \",\\n\\t\\tPRIMARY KEY (`id`)\\n) {$charset_collate};\";\n" +
            "\t\tdbDelta( $sql );\n" +
            "\t\tupdate_option( " + constPrefix + "_OPTION_KEY, self::target_version() );\n" +
            "\t}\n\n" +
            "\tpublic static function maybe_upgrade(): void {\n" +
            "\t\t$installed = (string) get_option( " + constPrefix + "_OPTION_KEY, '0' );\n" +
            "\t\t$target = self::target_version();\n" +
            "\t\tif ( version_compare( $installed, $target, '<' ) ) {\n" +
            "\t\t\tself::install();\n" +
            "\t\t}\n" +
            "\t}\n" +
            "}\n\n" +

            "final class " + constPrefix + "_Repository {\n" +
            "\tprivate static function table(): string {\n" +
            "\t\treturn " + constPrefix + "_Installer::table();\n" +
            "\t}\n\n" +
            "\tprivate static function allowed_columns(): array {\n" +
            "\t\treturn array( " + allowedPhp + " );\n" +
            "\t}\n\n" +
            "\tprivate static function filter_data( array $data ): array {\n" +
            "\t\t$allowed = array_flip( self::allowed_columns() );\n" +
            "\t\t$out = array();\n" +
            "\t\tforeach ( $data as $k => $v ) {\n" +
            "\t\t\t$key = sanitize_key( (string) $k );\n" +
            "\t\t\tif ( isset( $allowed[ $key ] ) && 'id' !== $key ) {\n" +
            "\t\t\t\t$out[ $key ] = $v;\n" +
            "\t\t\t}\n" +
            "\t\t}\n" +
            "\t\treturn $out;\n" +
            "\t}\n\n" +
            "\tpublic static function create( array $data ): int {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\t$table = self::table();\n" +
            "\t\t$insert = self::filter_data( $data );\n" +
            "\t\tif ( empty( $insert ) ) { return 0; }\n" +
            "\t\t$ok = $wpdb->insert( $table, $insert );\n" +
            "\t\treturn $ok ? (int) $wpdb->insert_id : 0;\n" +
            "\t}\n\n" +
            "\tpublic static function get( int $id ): ?array {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\t$table = self::table();\n" +
            "\t\t$row = $wpdb->get_row( $wpdb->prepare( \"SELECT * FROM {$table} WHERE id = %d LIMIT 1\", $id ), ARRAY_A );\n" +
            "\t\treturn $row ? (array) $row : null;\n" +
            "\t}\n\n" +
            "\tpublic static function list( array $args = array() ): array {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\t$table = self::table();\n" +
            "\t\t$per  = isset( $args['per'] ) ? max( 1, (int) $args['per'] ) : 20;\n" +
            "\t\t$page = isset( $args['page'] ) ? max( 1, (int) $args['page'] ) : 1;\n" +
            "\t\t$off  = ( $page - 1 ) * $per;\n" +
            "\t\t$sql  = \"SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d\";\n" +
            "\t\treturn (array) $wpdb->get_results( $wpdb->prepare( $sql, $per, $off ), ARRAY_A );\n" +
            "\t}\n\n" +
            "\tpublic static function update( int $id, array $data ): int {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\t$table = self::table();\n" +
            "\t\t$update = self::filter_data( $data );\n" +
            "\t\tif ( empty( $update ) ) { return 0; }\n" +
            "\t\tif ( " + constPrefix + "_ADD_TIMESTAMPS ) {\n" +
            "\t\t\t$update['updated_at'] = current_time( 'mysql' );\n" +
            "\t\t}\n" +
            "\t\treturn (int) $wpdb->update( $table, $update, array( 'id' => $id ) );\n" +
            "\t}\n\n" +
            "\tpublic static function delete( int $id ): int {\n" +
            "\t\tglobal $wpdb;\n" +
            "\t\t$table = self::table();\n" +
            "\t\treturn (int) $wpdb->delete( $table, array( 'id' => $id ) );\n" +
            "\t}\n" +
            "}\n\n" +

            // -----------------------------
            // REST API
            // -----------------------------
            "if ( " + constPrefix + "_ENABLE_REST ) {\n" +
            "\tadd_action( 'rest_api_init', function() {\n" +
            "\t\t$base = sanitize_key( " + constPrefix + "_ENTITY );\n" +
            "\t\t$ns   = " + constPrefix + "_REST_NAMESPACE;\n\n" +

            "\t\tregister_rest_route( $ns, '/' . $base, array(\n" +
            "\t\t\tarray(\n" +
            "\t\t\t\t'methods'  => WP_REST_Server::READABLE,\n" +
            "\t\t\t\t'callback' => function( WP_REST_Request $r ) {\n" +
            "\t\t\t\t\treturn rest_ensure_response( " + constPrefix + "_Repository::list( array(\n" +
            "\t\t\t\t\t\t'page' => (int) $r->get_param('page'),\n" +
            "\t\t\t\t\t\t'per'  => (int) $r->get_param('per'),\n" +
            "\t\t\t\t\t) ) );\n" +
            "\t\t\t\t},\n" +
            "\t\t\t\t'permission_callback' => '__return_true',\n" +
            "\t\t\t),\n" +
            "\t\t\tarray(\n" +
            "\t\t\t\t'methods'  => WP_REST_Server::CREATABLE,\n" +
            "\t\t\t\t'callback' => function( WP_REST_Request $r ) {\n" +
            "\t\t\t\t\tif ( ! current_user_can( " + constPrefix + "_CAPABILITY_WRITE ) ) {\n" +
            "\t\t\t\t\t\treturn new WP_Error( 'forbidden', 'Forbidden', array( 'status' => 403 ) );\n" +
            "\t\t\t\t\t}\n" +
            "\t\t\t\t\t$id = " + constPrefix + "_Repository::create( (array) $r->get_json_params() );\n" +
            "\t\t\t\t\treturn rest_ensure_response( array( 'id' => $id ) );\n" +
            "\t\t\t\t},\n" +
            "\t\t\t\t'permission_callback' => '__return_true',\n" +
            "\t\t\t),\n" +
            "\t\t) );\n\n" +

            "\t\tregister_rest_route( $ns, '/' . $base . '/(?P<id>\\d+)', array(\n" +
            "\t\t\tarray(\n" +
            "\t\t\t\t'methods'  => WP_REST_Server::READABLE,\n" +
            "\t\t\t\t'callback' => function( WP_REST_Request $r ) {\n" +
            "\t\t\t\t\t$row = " + constPrefix + "_Repository::get( (int) $r['id'] );\n" +
            "\t\t\t\t\treturn rest_ensure_response( $row ? $row : array() );\n" +
            "\t\t\t\t},\n" +
            "\t\t\t\t'permission_callback' => '__return_true',\n" +
            "\t\t\t),\n" +
            "\t\t\tarray(\n" +
            "\t\t\t\t'methods'  => WP_REST_Server::EDITABLE,\n" +
            "\t\t\t\t'callback' => function( WP_REST_Request $r ) {\n" +
            "\t\t\t\t\tif ( ! current_user_can( " + constPrefix + "_CAPABILITY_WRITE ) ) {\n" +
            "\t\t\t\t\t\treturn new WP_Error( 'forbidden', 'Forbidden', array( 'status' => 403 ) );\n" +
            "\t\t\t\t\t}\n" +
            "\t\t\t\t\t$updated = " + constPrefix + "_Repository::update( (int) $r['id'], (array) $r->get_json_params() );\n" +
            "\t\t\t\t\treturn rest_ensure_response( array( 'updated' => $updated ) );\n" +
            "\t\t\t\t},\n" +
            "\t\t\t\t'permission_callback' => '__return_true',\n" +
            "\t\t\t),\n" +
            "\t\t\tarray(\n" +
            "\t\t\t\t'methods'  => WP_REST_Server::DELETABLE,\n" +
            "\t\t\t\t'callback' => function( WP_REST_Request $r ) {\n" +
            "\t\t\t\t\tif ( ! current_user_can( " + constPrefix + "_CAPABILITY_WRITE ) ) {\n" +
            "\t\t\t\t\t\treturn new WP_Error( 'forbidden', 'Forbidden', array( 'status' => 403 ) );\n" +
            "\t\t\t\t\t}\n" +
            "\t\t\t\t\t$deleted = " + constPrefix + "_Repository::delete( (int) $r['id'] );\n" +
            "\t\t\t\t\treturn rest_ensure_response( array( 'deleted' => $deleted ) );\n" +
            "\t\t\t\t},\n" +
            "\t\t\t\t'permission_callback' => '__return_true',\n" +
            "\t\t\t),\n" +
            "\t\t) );\n" +

            "\t} );\n" +
            "}\n\n" +

            "register_activation_hook( __FILE__, array( '" + constPrefix + "_Installer', 'install' ) );\n" +
            "add_action( 'plugins_loaded', array( '" + constPrefix + "_Installer', 'maybe_upgrade' ) );\n\n" +

            "/**\n" +
            " * USAGE EXAMPLES\n" +
            " *\n" +
            " * PHP:\n" +
            " *   $id  = " + constPrefix + "_Repository::create( " + phpExampleData + " );\n" +
            " *   $row = " + constPrefix + "_Repository::get( $id );\n" +
            " *   $rows = " + constPrefix + "_Repository::list( array( 'page' => 1, 'per' => 20 ) );\n" +
            " *   " + constPrefix + "_Repository::update( $id, " + phpExampleData + " );\n" +
            " *   " + constPrefix + "_Repository::delete( $id );\n" +
            " *\n" +
            " * CURL (REST):\n" +
            " *   curl -X GET \"" + curlBase + "\"\n" +
            " *   curl -X POST \"" + curlBase + "\" -H \"Content-Type: application/json\" -d '{\"example\":\"value\"}'\n" +
            " *   curl -X GET \"" + curlBase + "/123\"\n" +
            " *   curl -X PUT \"" + curlBase + "/123\" -H \"Content-Type: application/json\" -d '{\"example\":\"value\"}'\n" +
            " *   curl -X DELETE \"" + curlBase + "/123\"\n" +
            " */\n"
        );
    }

    // -----------------------------
    // Clipboard helper
    // -----------------------------
    function copyToClipboard(text, textarea, toastEl) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard
                .writeText(text)
                .then(function () {
                    if (toastEl) {
                        toastEl.style.display = "inline";
                        setTimeout(function () {
                            toastEl.style.display = "none";
                        }, 2200);
                    }
                })
            ["catch"](function () {
                if (textarea) {
                    textarea.focus();
                    textarea.select();
                }
            });
        }

        try {
            if (textarea) {
                textarea.value = text;
                textarea.focus();
                textarea.select();
                document.execCommand("copy");
                if (toastEl) {
                    toastEl.style.display = "inline";
                    setTimeout(function () {
                        toastEl.style.display = "none";
                    }, 2200);
                }
            }
        } catch (e) { }
    }

    // -----------------------------
    // Init one shortcode instance
    // -----------------------------
    function initOne(root) {
        var uid = root.id;
        var $ = function (sel) {
            return root.querySelector(sel);
        };

        var entity = $("#" + uid + "-entity");
        var table = $("#" + uid + "-table");
        var ns = $("#" + uid + "-ns");
        var cap = $("#" + uid + "-cap");
        var opt = $("#" + uid + "-opt");
        var vconst = $("#" + uid + "-vconst");

        var timestamps = $("#" + uid + "-timestamps");
        var charset = $("#" + uid + "-charset");
        var repo = $("#" + uid + "-repo");
        var rest = $("#" + uid + "-rest");

        var colsBox = root.querySelector("[data-cols]");
        var textarea = $("#" + uid + "-out");
        var toast = root.querySelector("[data-toast]");

        // CodeMirror (WP Code Editor) - safe guard on front-end
        var cm = null;
        if (window.wp && window.wp.codeEditor && textarea) {
            try {
                var ed = window.wp.codeEditor.initialize(textarea, {
                    codemirror: {
                        mode: "text/x-php",
                        readOnly: true,
                        lineNumbers: true,
                        lineWrapping: true,
                    },
                });
                cm = ed && ed.codemirror ? ed.codemirror : null;
            } catch (e) {
                cm = null;
            }
        }

        function setOutput(code) {
            if (cm) {
                cm.setValue(code);
                cm.refresh();
            } else if (textarea) {
                textarea.value = code;
            }
        }

        // Ensure locked PK row exists and is non-editable.
        if (colsBox && !colsBox.querySelector('[data-locked="1"]')) {
            colsBox.appendChild(colRow({ locked: true }));
        }

        // Seed example rows if there are no editable rows yet.
        var editableRows = colsBox
            ? colsBox.querySelectorAll('[data-col="1"]:not([data-locked="1"])')
            : [];
        if (colsBox && editableRows.length === 0) {
            colsBox.appendChild(
                colRow({ name: "title", type: "VARCHAR(255)", "null": "NO" })
            );
            colsBox.appendChild(
                colRow({
                    name: "status",
                    type: "VARCHAR(50)",
                    "null": "NO",
                    "default": "pending",
                })
            );
        }

        root.addEventListener("click", function (e) {
            var addCol = e.target.closest('[data-action="add-col"]');
            if (addCol) {
                colsBox.appendChild(
                    colRow({ name: "", type: "VARCHAR(255)", "null": "NO" })
                );
                return;
            }

            var rmCol = e.target.closest('[data-action="rm-col"]');
            if (rmCol) {
                var r = rmCol.closest('[data-col="1"]');
                if (r && r.dataset.locked !== "1") r.remove();
                return;
            }

            var gen = e.target.closest('[data-action="generate"]');
            if (!gen) return;

            var d = {
                entity: clean(entity && entity.value) || "item",
                table:
                    slug(table && table.value) ||
                    ("tfd_" + slug(clean((entity && entity.value) || "item")) + "s"),
                namespace: clean(ns && ns.value) || "tfd-dbcrud/v1",
                capability: clean(cap && cap.value) || "manage_options",
                optionKey: clean(opt && opt.value) || "tfd_dbcrud_version",
                versionConst: clean(vconst && vconst.value) || "TFD_DBCRUD_VERSION",
                version: "1.0.0",
                timestamps: !!(timestamps && timestamps.checked),
                charset: !!(charset && charset.checked),
                repo: !!(repo && repo.checked),
                rest: !!(rest && rest.checked),
                columns: readColumns(colsBox),
            };

            // REST depends on repository (enforce)
            if (d.rest && !d.repo) {
                d.repo = true;
                if (repo) repo.checked = true;
            }

            var code = buildSingleFilePluginPhp(d);
            setOutput(code);
            copyToClipboard(code, textarea, toast);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        var nodes = document.querySelectorAll("[data-dbcg]");
        for (var i = 0; i < nodes.length; i++) {
            initOne(nodes[i]);
        }
    });
})();