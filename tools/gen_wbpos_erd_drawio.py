#!/usr/bin/env python3
"""Generate tools/wbpos-erd.drawio with real relationship connectors + compact layout."""
from pathlib import Path
from xml.sax.saxutils import escape

# (id, title, fields, col, row)  — grid 6 columns
# fields: list of "name type" ; first with PK; FK marked in name
ENTITIES = [
    ("plans", "wbpos_plans", [
        "PK plan_id BIGINT", "plan_code VARCHAR", "plan_name VARCHAR",
        "price_monthly DECIMAL", "max_users INT", "max_locations INT",
        "max_items INT", "is_active TINYINT", "created_at TIMESTAMP",
    ], 0, 0),
    ("admins", "wbpos_platform_admins", [
        "PK admin_id INT", "username VARCHAR", "password_hash VARCHAR",
        "full_name VARCHAR", "email VARCHAR", "status VARCHAR", "created_at TIMESTAMP",
    ], 1, 0),
    ("tenants", "wbpos_tenants", [
        "PK tenant_id BIGINT", "tenant_code VARCHAR", "company_name VARCHAR",
        "status VARCHAR", "timezone VARCHAR", "currency_code CHAR",
        "db_hostname VARCHAR", "db_port INT", "db_name VARCHAR",
        "db_username VARCHAR", "db_password VARCHAR", "db_prefix VARCHAR",
        "template_version VARCHAR", "isolated_at DATETIME",
        "created_at TIMESTAMP", "updated_at TIMESTAMP",
    ], 2, 0),
    ("subs", "wbpos_subscriptions", [
        "PK subscription_id BIGINT", "FK tenant_id BIGINT", "FK plan_id BIGINT",
        "status VARCHAR", "trial_ends_at DATETIME", "period_start DATETIME",
        "period_end DATETIME", "cancel_at_period_end TINYINT",
        "created_at TIMESTAMP", "updated_at TIMESTAMP",
    ], 3, 0),
    ("reqs", "wbpos_subscription_requests", [
        "PK request_id BIGINT", "company_name VARCHAR", "tenant_code VARCHAR",
        "business_type VARCHAR", "address VARCHAR", "city VARCHAR", "country VARCHAR",
        "tax_id VARCHAR", "owner_first_name VARCHAR", "owner_last_name VARCHAR",
        "owner_email VARCHAR", "owner_phone VARCHAR", "owner_username VARCHAR",
        "owner_password_hash VARCHAR", "FK plan_id BIGINT", "payment_reference VARCHAR",
        "payment_token VARCHAR", "email_verify_token VARCHAR", "email_verified_at DATETIME",
        "status VARCHAR", "notes TEXT", "FK reviewed_by_admin_id INT",
        "created_at TIMESTAMP", "reviewed_at DATETIME",
    ], 4, 0),
    ("tcfg", "wbpos_tenant_config", [
        "PK FK tenant_id BIGINT", "PK config_key VARCHAR", "config_value TEXT",
    ], 5, 0),

    ("people", "wbpos_people", [
        "PK person_id INT", "first_name VARCHAR", "last_name VARCHAR", "gender INT",
        "phone_number VARCHAR", "email VARCHAR", "address_1 VARCHAR", "address_2 VARCHAR",
        "city VARCHAR", "state VARCHAR", "zip VARCHAR", "country VARCHAR",
        "comments TEXT", "FK tenant_id BIGINT",
    ], 0, 1),
    ("tusers", "wbpos_tenant_users", [
        "PK FK tenant_id BIGINT", "PK FK person_id INT",
        "tenant_role VARCHAR", "is_active TINYINT", "created_at TIMESTAMP",
    ], 1, 1),
    ("emp", "wbpos_employees", [
        "PK FK person_id INT", "username VARCHAR", "password VARCHAR",
        "deleted TINYINT", "hash_version TINYINT", "language VARCHAR",
        "language_code VARCHAR", "FK tenant_id BIGINT",
    ], 2, 1),
    ("cust", "wbpos_customers", [
        "PK FK person_id INT", "company_name VARCHAR", "account_number VARCHAR",
        "taxable TINYINT", "tax_id VARCHAR", "sales_tax_code_id INT", "package_id INT",
        "points INT", "deleted TINYINT", "discount DECIMAL", "discount_type TINYINT",
        "date TIMESTAMP", "FK employee_id INT", "consent TINYINT", "FK tenant_id BIGINT",
    ], 3, 1),
    ("supp", "wbpos_suppliers", [
        "PK FK person_id INT", "company_name VARCHAR", "agency_name VARCHAR",
        "account_number VARCHAR", "tax_id VARCHAR", "deleted TINYINT",
        "category TINYINT", "FK tenant_id BIGINT",
    ], 4, 1),
    ("gift", "wbpos_giftcards", [
        "PK giftcard_id INT", "record_time TIMESTAMP", "giftcard_number VARCHAR",
        "value DECIMAL", "deleted TINYINT", "FK person_id INT", "FK tenant_id BIGINT",
    ], 5, 1),

    ("sales", "wbpos_sales", [
        "PK sale_id INT", "sale_time TIMESTAMP", "FK customer_id INT", "FK employee_id INT",
        "comment TEXT", "invoice_number VARCHAR", "dinner_table_id INT", "quote_number VARCHAR",
        "sale_status TINYINT", "work_order_number VARCHAR", "sale_type TINYINT", "FK tenant_id BIGINT",
    ], 0, 2),
    ("si", "wbpos_sales_items", [
        "PK FK sale_id INT", "PK FK item_id INT", "PK line INT",
        "quantity_purchased DECIMAL", "item_cost_price DECIMAL", "item_unit_price DECIMAL",
        "discount DECIMAL", "discount_type TINYINT", "FK item_location INT",
        "print_option TINYINT", "FK tenant_id BIGINT",
    ], 1, 2),
    ("sp", "wbpos_sales_payments", [
        "PK payment_id INT", "FK sale_id INT", "payment_type VARCHAR",
        "payment_amount DECIMAL", "cash_refund DECIMAL", "cash_adjustment TINYINT",
        "FK employee_id INT", "payment_time TIMESTAMP", "reference_code VARCHAR", "FK tenant_id BIGINT",
    ], 2, 2),
    ("items", "wbpos_items", [
        "PK item_id INT", "name VARCHAR", "category VARCHAR", "FK supplier_id INT",
        "item_number VARCHAR", "description VARCHAR", "cost_price DECIMAL", "unit_price DECIMAL",
        "reorder_level DECIMAL", "receiving_quantity DECIMAL", "deleted TINYINT",
        "stock_type TINYINT", "item_type TINYINT", "tax_category_id INT", "FK tenant_id BIGINT",
    ], 3, 2),
    ("loc", "wbpos_stock_locations", [
        "PK location_id INT", "location_name VARCHAR", "deleted TINYINT", "FK tenant_id BIGINT",
    ], 4, 2),
    ("iq", "wbpos_item_quantities", [
        "PK FK item_id INT", "PK FK location_id INT", "quantity DECIMAL", "FK tenant_id BIGINT",
    ], 5, 2),

    ("inv", "wbpos_inventory", [
        "PK trans_id INT", "FK trans_items INT", "FK trans_user INT", "trans_date TIMESTAMP",
        "trans_comment TEXT", "FK trans_location INT", "trans_inventory DECIMAL", "FK tenant_id BIGINT",
    ], 0, 3),
    ("recv", "wbpos_receivings", [
        "PK receiving_id INT", "receiving_time TIMESTAMP", "FK supplier_id INT",
        "FK employee_id INT", "comment TEXT", "payment_type VARCHAR", "reference VARCHAR", "FK tenant_id BIGINT",
    ], 1, 3),
    ("ri", "wbpos_receivings_items", [
        "PK FK receiving_id INT", "PK FK item_id INT", "PK line INT",
        "quantity_purchased DECIMAL", "item_cost_price DECIMAL", "item_unit_price DECIMAL",
        "discount DECIMAL", "FK item_location INT", "receiving_quantity DECIMAL", "FK tenant_id BIGINT",
    ], 2, 3),
    ("ecat", "wbpos_expense_categories", [
        "PK expense_category_id INT", "category_name VARCHAR", "category_description VARCHAR",
        "deleted TINYINT", "FK tenant_id BIGINT",
    ], 3, 3),
    ("exp", "wbpos_expenses", [
        "PK expense_id INT", "date TIMESTAMP", "amount DECIMAL", "payment_type VARCHAR",
        "FK expense_category_id INT", "description VARCHAR", "FK employee_id INT",
        "deleted TINYINT", "tax_amount DECIMAL", "FK supplier_id INT", "FK tenant_id BIGINT",
    ], 4, 3),
    ("cash", "wbpos_cash_up", [
        "PK cashup_id INT", "open_date TIMESTAMP", "close_date TIMESTAMP",
        "open_amount_cash DECIMAL", "closed_amount_total DECIMAL", "closed_amount_due DECIMAL",
        "FK open_employee_id INT", "FK close_employee_id INT", "description VARCHAR",
        "deleted TINYINT", "FK tenant_id BIGINT",
    ], 5, 3),
]

# Real business FKs only (skip most tenant_id spaghetti so diagram is readable)
EDGES = [
    ("plans", "subs"), ("tenants", "subs"),
    ("plans", "reqs"), ("admins", "reqs"),
    ("tenants", "tcfg"), ("tenants", "tusers"), ("people", "tusers"), ("tenants", "people"),
    ("people", "emp"), ("people", "cust"), ("people", "supp"), ("emp", "cust"), ("people", "gift"),
    ("cust", "sales"), ("emp", "sales"), ("sales", "si"), ("sales", "sp"),
    ("items", "si"), ("loc", "si"), ("emp", "sp"),
    ("supp", "items"), ("items", "iq"), ("loc", "iq"),
    ("items", "inv"), ("emp", "inv"), ("loc", "inv"),
    ("supp", "recv"), ("emp", "recv"), ("recv", "ri"), ("items", "ri"), ("loc", "ri"),
    ("ecat", "exp"), ("emp", "exp"), ("supp", "exp"), ("emp", "cash"),
]

COL_W, GAP_X, GAP_Y = 210, 40, 40
ROW_BASE_Y = [40, 420, 820, 1180]


def field_fill(label: str) -> str:
    if label.startswith("PK"):
        return "#fff2cc"
    if "FK" in label.split()[0:2] or label.startswith("FK") or " FK " in f" {label}":
        return "#dae8fc"
    return "#ffffff"


def entity_xml(eid: str, title: str, fields: list[str], col: int, row: int) -> str:
    x = 40 + col * (COL_W + GAP_X)
    y = ROW_BASE_Y[row]
    h = 26 + 16 * len(fields)
    lines = [
        f'<mxCell id="{eid}" value="{escape(title)}" style="swimlane;fontStyle=1;align=center;verticalAlign=top;childLayout=stackLayout;horizontal=1;startSize=26;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;whiteSpace=wrap;html=1;fillColor=#6eb6e0;fontColor=#000000;strokeColor=#4a90b8;fontSize=12;" vertex="1" parent="1">',
        f'  <mxGeometry x="{x}" y="{y}" width="{COL_W}" height="{h}" as="geometry"/>',
        "</mxCell>",
    ]
    for i, field in enumerate(fields):
        fill = field_fill(field)
        lines.append(
            f'<mxCell id="{eid}_f{i}" value="{escape(field)}" style="text;strokeColor=none;fillColor={fill};align=left;verticalAlign=middle;spacingLeft=4;spacingRight=4;overflow=hidden;rotatable=0;whiteSpace=wrap;html=1;fontSize=10;" vertex="1" parent="{eid}">'
        )
        lines.append(f'  <mxGeometry y="{26 + i * 16}" width="{COL_W}" height="16" as="geometry"/>')
        lines.append("</mxCell>")
    return "\n".join(lines)


parts = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    '<mxfile host="app.diagrams.net" agent="WBPOS" version="24.0.0" type="device">',
    '  <diagram id="wbpos-erd" name="WBPOS ERD">',
    '    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1600" pageHeight="1600" math="0" shadow="0">',
    "      <root>",
    '        <mxCell id="0"/>',
    '        <mxCell id="1" parent="0"/>',
    '        <mxCell id="title" value="ERD — WBPOS" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;fontStyle=1;fontSize=20;" vertex="1" parent="1">',
    '          <mxGeometry x="600" y="5" width="280" height="30" as="geometry"/>',
    "        </mxCell>",
]

for eid, title, fields, col, row in ENTITIES:
    parts.append(entity_xml(eid, title, fields, col, row))

for i, (src, tgt) in enumerate(EDGES):
    parts.append(
        f'<mxCell id="e{i}" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;endArrow=ERmany;startArrow=ERone;endFill=0;startFill=0;strokeColor=#666666;" edge="1" parent="1" source="{src}" target="{tgt}">'
    )
    parts.append('  <mxGeometry relative="1" as="geometry"/>')
    parts.append("</mxCell>")

parts += [
    "      </root>",
    "    </mxGraphModel>",
    "  </diagram>",
    "</mxfile>",
    "",
]

out = Path(__file__).with_name("wbpos-erd.drawio")
out.write_text("\n".join(parts), encoding="utf-8")
print(f"Wrote {out}")
print(f"Tables: {len(ENTITIES)}  Relationships: {len(EDGES)}")
