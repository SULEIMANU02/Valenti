"""
Valenti Atelier - SEN 803 10-Minute Presentation Guide & Slide Deck Builder
Generates a professional Word document (.docx) with slide designs, speaking scripts, and demo flows.
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

def create_presentation_doc():
    doc = docx.Document()
    
    for section in doc.sections:
        section.top_margin = Inches(0.9)
        section.bottom_margin = Inches(0.9)
        section.left_margin = Inches(0.9)
        section.right_margin = Inches(0.9)
        
        footer = section.footer
        f_p = footer.paragraphs[0]
        f_p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        f_run = f_p.add_run("SEN 803 Software Technology | 10-Minute Presentation & Portal Walkthrough Guide")
        f_run.font.name = 'Calibri'
        f_run.font.size = Pt(8.5)
        f_run.font.color.rgb = RGBColor(0x71, 0x80, 0x96)

    navy = RGBColor(0x0F, 0x29, 0x4A)
    gold = RGBColor(0x9A, 0x7B, 0x38)
    charcoal = RGBColor(0x2D, 0x37, 0x48)
    slate = RGBColor(0x4A, 0x55, 0x68)

    def set_cell_background(cell, fill_hex):
        tcPr = cell._tc.get_or_add_tcPr()
        shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
        tcPr.append(shd)

    def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
        tcPr = cell._tc.get_or_add_tcPr()
        tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
        tcPr.append(tcMar)

    # Document Header
    p_meta = doc.add_paragraph()
    p_meta.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_meta.paragraph_format.space_before = Pt(12)
    p_meta.paragraph_format.space_after = Pt(8)
    r = p_meta.add_run("SEN 803 SOFTWARE TECHNOLOGY — 10-MINUTE ORAL DEFENSE & LIVE DEMONSTRATION")
    r.font.name = 'Arial'
    r.font.size = Pt(10)
    r.font.bold = True
    r.font.color.rgb = slate

    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_after = Pt(4)
    r = p_title.add_run("VALENTI ATELIER PRESENTATION DECK")
    r.font.name = 'Georgia'
    r.font.size = Pt(22)
    r.font.bold = True
    r.font.color.rgb = navy

    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r = p_sub.add_run("Slide-by-Slide Content, Speaker Delivery Scripts, Time Breakdown & Live Portal Walkthrough")
    r.font.name = 'Calibri'
    r.font.size = Pt(12)
    r.font.italic = True
    r.font.color.rgb = gold

    # Timing Table
    p_t = doc.add_paragraph()
    p_t.paragraph_format.space_before = Pt(8)
    p_t.paragraph_format.space_after = Pt(4)
    r = p_t.add_run("Master 10-Minute Presentation Agenda & Time Allocation:")
    r.font.name = 'Arial'
    r.font.size = Pt(11)
    r.font.bold = True
    r.font.color.rgb = navy

    timing_data = [
        ["Phase 1: Concept & Market Problem", "Slides 1 – 3", "0:00 – 2:00 (2 mins)", "Hook examiner, outline Savile Row vs. B2C model, market gap."],
        ["Phase 2: IT Value Creation & Architecture", "Slides 4 – 6", "2:00 – 4:30 (2.5 mins)", "Detail Table 1 alignment, 3-tier tech stack, database schema, security."],
        ["Phase 3: Live Portal Demonstration", "Interactive Demo", "4:30 – 7:30 (3 mins)", "Live walk on http://localhost:8000/: storefront, SEN803 voucher, checkout, admin KPIs."],
        ["Phase 4: Financials, Scalability & Q&A", "Slides 7 – 10", "7:30 – 10:00 (2.5 mins)", "Unit economics, 3-year forecast, cloud roadmap, conclusion & defense."]
    ]
    tbl = doc.add_table(rows=1, cols=4)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    hdr_titles = ["Presentation Phase", "Slide Range", "Allocated Time", "Objective & Focus"]
    col_w = [1.8, 1.1, 1.4, 2.7]
    
    for i, h in enumerate(hdr_titles):
        c = tbl.cell(0, i)
        c.text = h
        set_cell_background(c, "0F294A")
        set_cell_margins(c, top=100, bottom=100, left=120, right=120)
        p = c.paragraphs[0]
        for r in p.runs:
            r.font.name = 'Arial'
            r.font.size = Pt(9)
            r.font.bold = True
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            
    for r_idx, row_vals in enumerate(timing_data):
        row = tbl.add_row()
        bg = "F7FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_vals):
            c = row.cells[c_idx]
            c.text = val
            set_cell_background(c, bg)
            set_cell_margins(c, top=80, bottom=80, left=120, right=120)
            p = c.paragraphs[0]
            for r in p.runs:
                r.font.name = 'Calibri'
                r.font.size = Pt(9)
                r.font.color.rgb = charcoal

    for row in tbl.rows:
        for i, w in enumerate(col_w):
            row.cells[i].width = Inches(w)

    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # Slides Detail List
    slides = [
        {
            "num": 1,
            "title": "Title Slide: Valenti Atelier — B2C Luxury Apparel Portal",
            "time": "0:00 – 0:35",
            "bullets": [
                "Course: SEN 803 Software Technology (Course Project)",
                "Project Component: Business Plan & Prototype Portal Defense",
                "Presenter: Sulaiman Adamu Ahmad",
                "Core Premise: Leveraging modern software engineering to create competitive advantage in direct-to-consumer luxury retailing."
            ],
            "script": (
                "\"Good morning, esteemed examiners and colleagues. Today, I am proud to present Valenti Atelier—a bespoke direct-to-consumer "
                "luxury fashion house and digital retailing portal developed for SEN 803 Software Technology. In this presentation, I will illustrate "
                "how strategic software architecture eliminates intermediary wholesale markups, creates frictionless consumer experiences, and drives measurable "
                "business value, followed by a live demonstration of our published prototype running locally on our server.\""
            )
        },
        {
            "num": 2,
            "title": "The Business Problem: Wholesale Inefficiencies vs. The B2C Solution",
            "time": "0:35 – 1:15",
            "bullets": [
                "The Traditional Retail Dilemma: Department stores impose 500–600% markups, opaque stock visibility, and disconnected customer relationships.",
                "The Value Gap: High consumer demand for premium artisanal fabrics (cashmere, silk, Japanese denim) without inflated luxury brand markups.",
                "Valenti Atelier's B2C Model: Direct-to-Consumer digital portal selling luxury garments directly from manufacturer to client at 65% gross margin."
            ],
            "script": (
                "\"Historically, luxury fashion has been crippled by inefficient supply chains. A coat costing £120 to manufacture ends up priced at £1,200 "
                "in department stores due to distributor markups and boutique real estate overheads. Valenti Atelier dismantles this archaic structure. By deploying "
                "a proprietary B2C e-commerce portal, we offer identical Savile Row-grade pieces at £350 to £500, capturing a superior 65% gross margin while offering "
                "unrivaled consumer value.\""
            )
        },
        {
            "num": 3,
            "title": "SEN 803 Core Alignment: IT-Driven Value Creation (Table 1)",
            "time": "1:15 – 2:00",
            "bullets": [
                "Table 1 Business Operation: Online Shop (Production or buying and selling products).",
                "Pillar 1 — Retailing & Merchandising: High-resolution catalog, multi-facet filtering (Category, Gender, Size, Price), live stock scarcity indicators.",
                "Pillar 2 — Frictionless Transactions: Real-time cart calculation, automated voucher engine (SEN803 20% discount), simulated multi-rail payment.",
                "Pillar 3 — Logistics Transparency: Automated official invoicing, 4-stage interactive fulfillment tracker (Placed → Boxed → Dispatched → Delivered).",
                "Pillar 4 — Executive Operations: Real-time back-office dashboard, KPI revenue metrics, automated low-stock warnings, and full product CRUD."
            ],
            "script": (
                "\"In strict accordance with Table 1 of the SEN 803 brief—Online Shop Operations—our portal leverages IT across four critical pillars. First, digital "
                "merchandising with multi-faceted filtering and real-time stock counters. Second, an automated conversion engine featuring our custom voucher rules "
                "including our SEN803 course discount. Third, consumer trust through an interactive 4-stage logistics dispatch tracker. And fourth, an administrative "
                "back-office providing live KPI intelligence and inventory controls.\""
            )
        },
        {
            "num": 4,
            "title": "System Architecture: 3-Tier Enterprise Pattern",
            "time": "2:00 – 2:45",
            "bullets": [
                "Presentation Tier: Semantic HTML5, CSS3 Custom Properties (variables), glassmorphism luxury theme, responsive Flexbox/Grid, vanilla JavaScript (no heavy node dependencies, sub-200ms render).",
                "Application Logic Tier: Modular PHP 8.2 backend, session security handlers, CSRF tokens, cart math calculations, and role-based access controllers.",
                "Data Persistence Tier: PHP Data Objects (PDO) with dual-driver support: MySQL/MariaDB for production server hosting, and zero-configuration SQLite for portable evaluation."
            ],
            "script": (
                "\"Our software architecture follows an industry-standard 3-tier design. At the front, we eliminated bloated client frameworks in favor of optimized "
                "semantic HTML5 and CSS3, achieving sub-200ms page load times. The backend is powered by native PHP 8.2 with rigorous session security. And for our database, "
                "we implemented a dual-driver PDO architecture that runs seamlessly on XAMPP MySQL, but also includes an automatic SQLite fallback with auto-seeding for flawless examiner portability.\""
            )
        },
        {
            "num": 5,
            "title": "Relational Data Model & Cybersecurity Engineering",
            "time": "2:45 – 3:30",
            "bullets": [
                "Normalized Relational Schema (3NF): 7 core entities (`users`, `categories`, `products`, `orders`, `order_items`, `coupons`, `reviews`).",
                "Prepared PDO Statements: 100% parameterization eliminating SQL Injection vulnerabilities.",
                "Cryptographic Password Hashing: BCrypt encryption (`PASSWORD_BCRYPT`) with automatic salt rotation.",
                "Role-Based Access Control (RBAC): Strict segregation between Customer (`customer`) and Administrator (`admin`) privileges.",
                "CSRF & XSS Protection: Cryptographic random tokens for mutations; `htmlspecialchars` sanitization across all view templates."
            ],
            "script": (
                "\"Security and data integrity were engineered into every layer. Our database is strictly normalized into seven relational tables. All database queries "
                "use prepared statements with parameterized inputs, providing complete immunity against SQL injection. User passwords are encrypted with BCrypt hashing, "
                "administrative endpoints are protected by Role-Based Access Controls, and state-modifying requests require cryptographically validated CSRF tokens.\""
            )
        },
        {
            "num": 6,
            "title": "Transition to Live Demonstration: Published Portal",
            "time": "3:30 – 4:30",
            "bullets": [
                "Local Server Deployment: Published locally via PHP built-in server on `http://localhost:8000/`.",
                "Zero-Friction Launcher: Automated Windows batch launcher (`run.bat` / `run_portal.bat`) handling port checks and database auto-seeding.",
                "Evaluation Access: Pre-seeded demo accounts for Administrator (`admin@valenti.com`) and Patron (`customer@valenti.com`).",
                "Live Demo Roadmap: (1) Browse & Filter -> (2) Add to Bag -> (3) Apply Coupon SEN803 -> (4) Simulated Checkout -> (5) Track Order -> (6) Admin Console."
            ],
            "script": (
                "\"At this juncture, as required by Section 4(c) of our project brief, I will now switch to our live published prototype running on our local server at "
                "localhost:8000 to demonstrate the end-to-end consumer shopping journey and back-office administrative workflows.\""
            )
        },
        {
            "num": 7,
            "title": "LIVE DEMO STEP-BY-STEP WALKTHROUGH SCRIPT",
            "time": "4:30 – 7:30 (CRITICAL 3-MINUTE DEMO)",
            "bullets": [
                "Step 1 (Storefront): Open http://localhost:8000/ -> Highlight luxury editorial aesthetics, category cards, and responsive navigation.",
                "Step 2 (Faceted Search): Click 'Outerwear' or navigate to /shop.php -> Show real-time filtering, price range slider, and sort dropdown.",
                "Step 3 (Product Page): Open 'The Savile Cashmere Overcoat' -> Highlight garment image, size selector pill, stock indicator ('Only 4 left!'), and customer reviews.",
                "Step 4 (Shopping Bag): Add to bag -> Open /cart.php -> Enter coupon 'SEN803' -> Watch subtotal instantly drop by 20% (£96.00 discount).",
                "Step 5 (Checkout): Proceed to /checkout.php -> Select 'Credit / Debit Card' simulation -> Click 'Place Order' -> Generate official invoice receipt.",
                "Step 6 (Order Tracking): Point out order number (e.g. #VAL-803-xxxx) and interactive 4-stage visual delivery tracker timeline.",
                "Step 7 (Admin Console): Navigate to /admin/index.php (login: admin@valenti.com / admin123) -> Showcase live Gross Sales KPIs, recent orders table, low-stock warnings, and product catalog CRUD."
            ],
            "script": (
                "\"[DURING LIVE DEMO]: Here on our homepage, you observe the refined light editorial styling. Navigating to our Collection page, our faceted filter allows patrons "
                "to isolate garments by department, size, or price. Selecting the Savile Cashmere Overcoat, you see our dynamic stock counter showing scarcity. Adding this to our bag "
                "and proceeding to cart, I will now input our course promotional code: S-E-N-8-0-3. Instantly, our algorithmic pricing engine recalculates the subtotal, deducting 20% "
                "while maintaining full tax compliance. Completing checkout with simulated card payment brings us to our official invoice and our interactive 4-stage tracking timeline. "
                "Now, switching over to our Operations Console at /admin, executive managers immediately see updated Gross Sales KPIs, low-stock alerts, and full garment inventory management controls.\""
            )
        },
        {
            "num": 8,
            "title": "Financial Viability, Pricing & Unit Economics",
            "time": "7:30 – 8:15",
            "bullets": [
                "Accessible Luxury Margin: Direct-to-Consumer pricing delivers 65%–72% gross margins across all garment categories.",
                "Promotional Voucher Viability: Minimum spend thresholds ensure vouchers like `SEN803` remain highly profitable (yielding 63.3% net margin even after 20% discount).",
                "Pro Forma Year 1 Projections: 3,200 orders, £672,000 GMV, £456,960 Gross Profit, and £216,960 Net EBITDA (32.3% profit margin).",
                "Year 3 Target: Scaled digital expansion reaching £4.99M GMV with £2.18M net operating profit."
            ],
            "script": (
                "\"Turning to our financial model, our unit economics are exceptionally robust. Because we eliminate wholesale distribution intermediaries, our gross product margins "
                "remain above 68%. Furthermore, our promotional voucher architecture is economically sound: even with a 20% discount applied via voucher SEN803, our gross margin on an average "
                "basket remains over 63%, generating immediate cash flow while accelerating new customer acquisition.\""
            )
        },
        {
            "num": 9,
            "title": "Risk Mitigation & Production Scalability Roadmap",
            "time": "8:15 – 9:00",
            "bullets": [
                "Proactive Risk Management: SQL injection defense via PDO; stockout prevention via automated low-stock warnings; fraud reduction via tokenized checkout.",
                "Cloud Scaling Phase 1: Containerization of PHP/MySQL runtime into Docker microservices.",
                "Cloud Scaling Phase 2: Edge asset caching and DDoS defense through Cloudflare CDN.",
                "Cloud Scaling Phase 3: Redis in-memory session caching for sub-millisecond cart retrieval during high-volume capsule drops.",
                "Compliance: GDPR patron data privacy, right-to-be-forgotten endpoints, and secure cookie architecture."
            ],
            "script": (
                "\"To ensure long-term viability, we conducted a rigorous risk assessment. High-traffic flash sales and seasonal capsule drops are mitigated through a clear "
                "scalability roadmap: containerizing our application with Docker, offloading image assets to Cloudflare Edge CDN, and integrating Redis in-memory caching. "
                "Additionally, the platform adheres to GDPR data privacy regulations with secure session management and patron consent mechanisms.\""
            )
        },
        {
            "num": 10,
            "title": "Conclusion & Examination Defense Summary",
            "time": "9:00 – 10:00",
            "bullets": [
                "Academic Deliverables Complete: Comprehensive 25-page Business Plan Report (30 Marks) & Deployed Prototype Portal (60 Marks).",
                "Tangible IT Value Demonstrated: Automated transactions, real-time inventory management, logistics transparency, and executive intelligence.",
                "Commercial Readiness: Enterprise-grade code quality, dual-driver database portability, and verified demo accounts.",
                "Thank you for your time and evaluation. I welcome your questions and examination feedback."
            ],
            "script": (
                "\"In conclusion, Valenti Atelier provides a complete, academically rigorous, and commercially viable demonstration of how software technology transforms B2C commerce. "
                "We have satisfied all requirements of the SEN 803 brief: delivering the business plan report, developing and publishing the local prototype portal, and illustrating its "
                "strategic value. Thank you very much for your time, and I am now ready for your questions and examination discussion.\""
            )
        }
    ]

    for slide in slides:
        p_s = doc.add_paragraph()
        p_s.paragraph_format.space_before = Pt(14)
        p_s.paragraph_format.space_after = Pt(2)
        p_s.paragraph_format.keep_with_next = True
        r = p_s.add_run(f"SLIDE {slide['num']}: {slide['title'].upper()}")
        r.font.name = 'Arial'
        r.font.size = Pt(12)
        r.font.bold = True
        r.font.color.rgb = navy

        p_time = doc.add_paragraph()
        p_time.paragraph_format.space_after = Pt(4)
        p_time.paragraph_format.keep_with_next = True
        r = p_time.add_run(f"⏱ Allocated Timestamp: {slide['time']}")
        r.font.name = 'Calibri'
        r.font.size = Pt(9.5)
        r.font.bold = True
        r.font.color.rgb = gold

        p_b_head = doc.add_paragraph()
        p_b_head.paragraph_format.space_after = Pt(2)
        p_b_head.paragraph_format.keep_with_next = True
        r = p_b_head.add_run("Slide Visual Content & Bullet Points:")
        r.font.name = 'Arial'
        r.font.size = Pt(10)
        r.font.bold = True
        r.font.color.rgb = slate

        for b in slide['bullets']:
            p_b = doc.add_paragraph(style='List Bullet')
            p_b.paragraph_format.space_after = Pt(2)
            p_b.paragraph_format.line_spacing = 1.15
            r = p_b.add_run(b)
            r.font.name = 'Calibri'
            r.font.size = Pt(9.5)
            r.font.color.rgb = charcoal

        # Spoken Script Box
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        c = tbl.cell(0, 0)
        set_cell_background(c, "F7FAFC")
        set_cell_margins(c, top=100, bottom=100, left=160, right=160)
        
        tcPr = c._tc.get_or_add_tcPr()
        tcBorders = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="none"/><w:left w:val="single" w:sz="30" w:space="0" w:color="9A7B38"/><w:bottom w:val="none"/><w:right w:val="none"/></w:tcBorders>')
        tcPr.append(tcBorders)
        
        p_box = c.paragraphs[0]
        p_box.paragraph_format.space_before = Pt(2)
        p_box.paragraph_format.space_after = Pt(2)
        r_box_title = p_box.add_run("🎙 WHAT TO SAY TO THE EXAMINER (VERBATIM SPEAKER SCRIPT):\n")
        r_box_title.font.name = 'Arial'
        r_box_title.font.size = Pt(9)
        r_box_title.font.bold = True
        r_box_title.font.color.rgb = gold
        
        r_box_body = p_box.add_run(slide['script'])
        r_box_body.font.name = 'Georgia'
        r_box_body.font.size = Pt(9.5)
        r_box_body.font.italic = True
        r_box_body.font.color.rgb = charcoal

        doc.add_paragraph().paragraph_format.space_after = Pt(6)

    # Q&A Defense Section
    doc.add_page_break()
    p_qa = doc.add_paragraph()
    p_qa.paragraph_format.space_before = Pt(14)
    p_qa.paragraph_format.space_after = Pt(6)
    r = p_qa.add_run("ANTICIPATED EXAMINER QUESTIONS & MODEL DEFENSE ANSWERS")
    r.font.name = 'Arial'
    r.font.size = Pt(14)
    r.font.bold = True
    r.font.color.rgb = navy

    qa_list = [
        ("Q1: Why did you choose native PHP instead of a framework like Laravel or Node/React?",
         "A1: Native PHP 8.2 provides maximum architectural transparency, zero external framework overhead, and lightning-fast cold execution speeds (<50ms). More importantly for an academic evaluation, it guarantees 100% portability on examiner systems without requiring complex Node/Composer dependency installations or build compilation steps."),
        ("Q2: How does your database ensure zero configuration on examiner machines?",
         "A2: Our `config/database.php` architecture employs a dual-driver pattern: it first attempts to connect to local MySQL on standard port 3306. If MySQL is offline, it automatically falls back to an embedded SQLite database (`database/valenti_atelier.db`). In both cases, auto-migration and seeding scripts automatically populate all tables and demo records if empty."),
        ("Q3: How does your voucher engine prevent abuse or negative margins?",
         "A3: Each voucher in our `coupons` database table is governed by constraints: discount type (percentage vs. fixed), minimum basket threshold (`min_spend`), and expiration date. For example, coupon `SEN803` strictly enforces a £50.00 minimum spend, ensuring every discounted order preserves at least a 63% gross profit margin."),
        ("Q4: What measures protect the portal against unauthorized administrative access?",
         "A4: We enforce Role-Based Access Control (RBAC) via the `requireAdmin()` helper on all `/admin/*` routes, checking the session's authenticated role flag. Passwords are encrypted with BCrypt hashing, and sensitive state changes are guarded by session-bound CSRF tokens.")
    ]

    for q, a in qa_list:
        p_q = doc.add_paragraph()
        p_q.paragraph_format.space_before = Pt(8)
        p_q.paragraph_format.space_after = Pt(2)
        r = p_q.add_run(q)
        r.font.name = 'Arial'
        r.font.size = Pt(10)
        r.font.bold = True
        r.font.color.rgb = navy

        p_a = doc.add_paragraph()
        p_a.paragraph_format.space_after = Pt(6)
        r = p_a.add_run(a)
        r.font.name = 'Calibri'
        r.font.size = Pt(9.5)
        r.font.color.rgb = charcoal

    out_path = os.path.join(os.path.dirname(__file__), "Valenti_Atelier_SEN803_Presentation_Guide.docx")
    doc.save(out_path)
    print(f"Presentation Guide successfully generated at: {out_path}")

if __name__ == '__main__':
    create_presentation_doc()
