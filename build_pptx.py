"""
Valenti Atelier - SEN 803 10-Minute PowerPoint Presentation Builder
Generates a genuine .pptx presentation file with luxury branding,
widescreen 16:9 layout, formatted content cards, and embedded presenter notes.
"""

import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE

def create_presentation():
    prs = Presentation()
    
    # Set slide dimensions to 16:9 widescreen (13.333 x 7.5 inches)
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    
    blank_layout = prs.slide_layouts[6] # completely blank layout

    # Brand Colors
    c_bg_dark = RGBColor(0x0B, 0x13, 0x20)       # Deep Midnight Navy
    c_card_bg = RGBColor(0x13, 0x22, 0x38)       # Elevated Navy Card
    c_gold = RGBColor(0xC5, 0xA8, 0x80)          # Atelier Luxury Gold
    c_gold_light = RGBColor(0xE5, 0xD4, 0xBE)    # Soft Gold
    c_white = RGBColor(0xFF, 0xFF, 0xFF)         # Bright White
    c_slate = RGBColor(0x94, 0xA3, 0xB8)         # Muted Slate
    c_green = RGBColor(0x10, 0xB9, 0x81)         # Success Green
    c_card_border = RGBColor(0x28, 0x3D, 0x5E)   # Card Border

    def add_background(slide):
        # Full slide dark background
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = c_bg_dark
        bg.line.fill.background() # no border
        return bg

    def add_header(slide, badge_text, slide_title, timer_text=None):
        # Badge
        tx_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.45), Inches(8), Inches(0.4))
        tf = tx_box.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = badge_text.upper()
        p.font.name = 'Arial'
        p.font.size = Pt(10)
        p.font.bold = True
        p.font.color.rgb = c_gold
        
        # Timer Pill
        if timer_text:
            timer_box = slide.shapes.add_textbox(Inches(10.5), Inches(0.4), Inches(2.0), Inches(0.35))
            ttf = timer_box.text_frame
            tp = ttf.paragraphs[0]
            tp.alignment = PP_ALIGN.RIGHT
            tp.text = f"⏱ {timer_text}"
            tp.font.name = 'Arial'
            tp.font.size = Pt(11)
            tp.font.bold = True
            tp.font.color.rgb = c_gold

        # Main Title
        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.8), Inches(11.7), Inches(0.8))
        tf_title = title_box.text_frame
        tf_title.word_wrap = True
        p_title = tf_title.paragraphs[0]
        p_title.text = slide_title
        p_title.font.name = 'Georgia'
        p_title.font.size = Pt(22)
        p_title.font.bold = True
        p_title.font.color.rgb = c_white

        # Gold decorative divider
        line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.65), Inches(11.733), Inches(0.02))
        line.fill.solid()
        line.fill.fore_color.rgb = c_card_border
        line.line.fill.background()

    def add_card(slide, left, top, width, height, bg_rgb=c_card_bg, border_rgb=c_card_border):
        card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_rgb
        card.line.color.rgb = border_rgb
        card.line.width = Pt(1)
        return card

    def add_notes(slide, text):
        notes_slide = slide.notes_slide
        tf = notes_slide.notes_text_frame
        tf.text = text

    # =========================================================================
    # SLIDE 1: TITLE SLIDE
    # =========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    add_background(s1)

    # Center card
    add_card(s1, Inches(1.5), Inches(1.0), Inches(10.333), Inches(5.5), bg_rgb=RGBColor(0x10, 0x1D, 0x30), border_rgb=c_gold)

    # Department & Course
    t_box = s1.shapes.add_textbox(Inches(1.8), Inches(1.3), Inches(9.7), Inches(0.5))
    tf = t_box.text_frame
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "POSTGRADUATE SOFTWARE TECHNOLOGY | SEN 803 COURSE PROJECT DEFENSE"
    p.font.name = 'Arial'
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = c_gold

    # Title
    t_main = s1.shapes.add_textbox(Inches(1.8), Inches(1.8), Inches(9.7), Inches(1.3))
    tf_main = t_main.text_frame
    p_main = tf_main.paragraphs[0]
    p_main.alignment = PP_ALIGN.CENTER
    p_main.text = "VALENTI ATELIER"
    p_main.font.name = 'Georgia'
    p_main.font.size = Pt(40)
    p_main.font.bold = True
    p_main.font.color.rgb = c_white

    # Subtitle
    p_sub = tf_main.add_paragraph()
    p_sub.alignment = PP_ALIGN.CENTER
    p_sub.text = "Leveraging Software Technology to Create Strategic Value in B2C Luxury Retailing"
    p_sub.font.name = 'Calibri'
    p_sub.font.size = Pt(16)
    p_sub.font.bold = True
    p_sub.font.color.rgb = c_gold_light

    # Info Grid inside Card
    meta_box = s1.shapes.add_textbox(Inches(2.0), Inches(3.4), Inches(9.3), Inches(2.6))
    tf_meta = meta_box.text_frame
    
    p1 = tf_meta.paragraphs[0]
    p1.alignment = PP_ALIGN.CENTER
    p1.text = "• Candidate: Sulaiman Adamu Ahmad   |   Academic Session: 2026 •"
    p1.font.name = 'Calibri'
    p1.font.size = Pt(13)
    p1.font.bold = True
    p1.font.color.rgb = c_white

    p2 = tf_meta.add_paragraph()
    p2.alignment = PP_ALIGN.CENTER
    p2.text = "• Prototype Status: Published & Live on Local Server (http://localhost:8000/) •"
    p2.font.name = 'Calibri'
    p2.font.size = Pt(12)
    p2.font.color.rgb = c_green

    p3 = tf_meta.add_paragraph()
    p3.alignment = PP_ALIGN.CENTER
    p3.text = "\nCourse Deliverables: Prototype Portal (60 Marks)  |  Business Plan (30 Marks)  |  Defense Walkthrough (10%)"
    p3.font.name = 'Calibri'
    p3.font.size = Pt(11)
    p3.font.color.rgb = c_slate

    add_notes(s1, 
        "Good morning, esteemed examiners. Today, I am proud to present Valenti Atelier—a bespoke direct-to-consumer "
        "luxury fashion house and digital retailing portal developed for SEN 803 Software Technology. In this 10-minute presentation, "
        "I will demonstrate how strategic software architecture eliminates intermediate retail markups, creates automated consumer value, "
        "and drives measurable business efficiency, followed by a live walkthrough of our published local portal on port 8000."
    )

    # =========================================================================
    # SLIDE 2: THE BUSINESS PROBLEM VS B2C SOLUTION
    # =========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    add_background(s2)
    add_header(s2, "Slide 2 • Market Opportunity", "The Problem: Traditional Wholesale vs. The B2C Solution", "0:35 – 1:15")

    # Card Left (The Problem)
    add_card(s2, Inches(0.8), Inches(1.9), Inches(5.7), Inches(4.8), border_rgb=RGBColor(0xE5, 0x3E, 0x3E))
    tx = s2.shapes.add_textbox(Inches(1.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf = tx.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "TRADITIONAL WHOLESALE MODEL (BROKEN)"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = RGBColor(0xFC, 0x81, 0x81)

    points_left = [
        "Multi-Tiered Intermediaries: Brands sell to agents, wholesalers, and department stores, each adding 100-200% margins.",
        "Bloated Consumer Pricing: A coat costing £120 to produce is marked up to £1,200 on department store shelves.",
        "Opaque Inventory & Stockouts: Zero live visibility across decentralized distributor networks.",
        "Disconnected Customer Relationship: Brands have no direct data or contact with end consumers."
    ]
    for pt in points_left:
        p_pt = tf.add_paragraph()
        p_pt.text = f"• {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(12.5)
        p_pt.font.color.rgb = c_slate
        p_pt.space_before = Pt(8)

    # Card Right (The Solution)
    add_card(s2, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8), border_rgb=c_gold)
    tx_r = s2.shapes.add_textbox(Inches(7.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf_r = tx_r.text_frame
    tf_r.word_wrap = True
    p_r = tf_r.paragraphs[0]
    p_r.text = "VALENTI ATELIER B2C PORTAL (THE SOLUTION)"
    p_r.font.name = 'Arial'
    p_r.font.size = Pt(12)
    p_r.font.bold = True
    p_r.font.color.rgb = c_gold

    points_right = [
        "Pure Direct-to-Consumer (B2C): Proprietary web portal connecting manufacturing directly to global patrons.",
        "Accessible Luxury Pricing: Identical Savile Row quality sold for £350–£500 while retaining 65–70% gross margins.",
        "Real-Time Stock Counters: Automated database triggers providing live scarcity indicators directly on product pages.",
        "Direct Client Relationship: Seamless order history, official invoice receipts, and interactive 4-stage dispatch tracking."
    ]
    for pt in points_right:
        p_pt = tf_r.add_paragraph()
        p_pt.text = f"✓ {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(12.5)
        p_pt.font.color.rgb = c_white
        p_pt.space_before = Pt(8)

    add_notes(s2, 
        "Historically, luxury fashion has been crippled by inefficient supply chains. A coat costing £120 to manufacture ends up priced at £1,200 "
        "in department stores due to distributor markups and boutique real estate overheads. Valenti Atelier dismantles this archaic structure. By deploying "
        "a proprietary B2C e-commerce portal, we offer identical Savile Row-grade pieces at £350 to £500, capturing a superior 65% gross margin while offering "
        "unrivaled consumer value."
    )

    # =========================================================================
    # SLIDE 3: SEN 803 TABLE 1 ALIGNMENT
    # =========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    add_background(s3)
    add_header(s3, "Slide 3 • Course Brief Alignment", "IT Value Creation Framework (Table 1: Online Shop)", "1:15 – 2:00")

    # 4 Pillar Cards
    pillars = [
        ("1. Digital Merchandising", "• High-resolution editorial photography\n• Multi-facet filtering (Category, Gender, Size, Price)\n• Dynamic stock scarcity counters\n• Verified fitting reviews"),
        ("2. Frictionless Transactions", "• Real-time shopping bag recalculation\n• Algorithmic voucher engine (SEN803 20% discount)\n• Dynamic tax & free shipping math\n• Simulated multi-rail payment channels"),
        ("3. Logistics Transparency", "• Instant printable official tax invoices\n• Interactive 4-stage dispatch tracking\n• Real-time courier status transitions\n• Eliminates post-purchase customer anxiety"),
        ("4. Operations Intelligence", "• Executive Gross Sales & Order KPI cards\n• Automated low-stock warning triggers (<5 units)\n• Complete garment CRUD inventory management\n• One-click order fulfillment controls")
    ]

    for i, (p_title, p_desc) in enumerate(pillars):
        col = i % 2
        row = i // 2
        l = Inches(0.8 + col * 6.0)
        t = Inches(1.9 + row * 2.5)
        add_card(s3, l, t, Inches(5.7), Inches(2.3))
        
        tx = s3.shapes.add_textbox(l + Inches(0.2), t + Inches(0.15), Inches(5.3), Inches(2.0))
        tf = tx.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = p_title
        p.font.name = 'Arial'
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = c_gold
        
        p_d = tf.add_paragraph()
        p_d.text = p_desc
        p_d.font.name = 'Calibri'
        p_d.font.size = Pt(11.5)
        p_d.font.color.rgb = c_slate
        p_d.space_before = Pt(4)

    add_notes(s3, 
        "In strict accordance with Table 1 of the SEN 803 brief—Online Shop Operations—our portal leverages IT across four critical pillars. "
        "First, digital merchandising with multi-faceted filtering and real-time stock counters. Second, an automated conversion engine featuring our custom voucher rules "
        "including our SEN803 course discount. Third, consumer trust through an interactive 4-stage logistics dispatch tracker. And fourth, an administrative back-office "
        "providing live KPI intelligence and inventory controls."
    )

    # =========================================================================
    # SLIDE 4: SYSTEM ARCHITECTURE (3-TIER PATTERN)
    # =========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    add_background(s4)
    add_header(s4, "Slide 4 • Software Engineering", "3-Tier Enterprise Architectural Pattern", "2:00 – 2:45")

    tiers = [
        ("TIER 1: PRESENTATION (CLIENT)", "Semantic HTML5 & Vanilla JavaScript\nCustom CSS3 Luxury Design System\nGlassmorphism & Responsive CSS Grid\nSub-200ms First Contentful Paint (No heavy node bloat)", c_gold),
        ("TIER 2: APPLICATION LOGIC (BACKEND)", "Native PHP 8.2 Runtime Engine\nSession Security & CSRF Token Handlers\nRole-Based Access Control (Admin vs Patron)\nAlgorithmic Cart & Voucher Pricing Engine", c_white),
        ("TIER 3: DATA PERSISTENCE (DATABASE)", "Dual-Driver PDO Relational Layer\nPrimary: MySQL / MariaDB (XAMPP Port 3306)\nFallback: Zero-Config Embedded SQLite\nAutomated Self-Migration & Data Seeding", c_green)
    ]

    for i, (t_title, t_body, t_color) in enumerate(tiers):
        l = Inches(0.8 + i * 4.0)
        add_card(s4, l, Inches(1.9), Inches(3.7), Inches(4.8), border_rgb=t_color)
        
        tx = s4.shapes.add_textbox(l + Inches(0.2), Inches(2.1), Inches(3.3), Inches(4.4))
        tf = tx.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = t_title
        p.font.name = 'Arial'
        p.font.size = Pt(11.5)
        p.font.bold = True
        p.font.color.rgb = t_color
        
        p_b = tf.add_paragraph()
        p_b.text = t_body
        p_b.font.name = 'Calibri'
        p_b.font.size = Pt(12)
        p_b.font.color.rgb = c_slate
        p_b.space_before = Pt(10)

    add_notes(s4, 
        "Our software architecture follows an industry-standard 3-tier design. At the front, we eliminated bloated client frameworks in favor of optimized "
        "semantic HTML5 and CSS3, achieving sub-200ms page load times. The backend is powered by native PHP 8.2 with rigorous session security. And for our database, "
        "we implemented a dual-driver PDO architecture that runs seamlessly on XAMPP MySQL, but also includes an automatic SQLite fallback with auto-seeding for flawless examiner portability."
    )

    # =========================================================================
    # SLIDE 5: DATA MODEL & SECURITY
    # =========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    add_background(s5)
    add_header(s5, "Slide 5 • Data & Security", "Normalized Relational Data Model & Cybersecurity", "2:45 – 3:30")

    # Left: Database Entities Card
    add_card(s5, Inches(0.8), Inches(1.9), Inches(5.7), Inches(4.8))
    tx_db = s5.shapes.add_textbox(Inches(1.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf_db = tx_db.text_frame
    tf_db.word_wrap = True
    p = tf_db.paragraphs[0]
    p.text = "7 NORMALIZED RELATIONAL TABLES (3NF)"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = c_gold

    schema_pts = [
        "users: Customer & admin profiles with BCrypt hashed passwords.",
        "categories: Slugs, lookbook banners, and department filters.",
        "products: Garment catalog, pricing, SKUs, and inventory stock counters.",
        "orders: Header table tracking status, totals, and shipping details.",
        "order_items: Normalized line items preserving historic purchase prices.",
        "coupons: Rules engine for percentage (SEN803) & fixed discounts.",
        "reviews: 5-star customer ratings and verified fitting feedback."
    ]
    for pt in schema_pts:
        p_pt = tf_db.add_paragraph()
        p_pt.text = f"• {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(11)
        p_pt.font.color.rgb = c_slate
        p_pt.space_before = Pt(4)

    # Right: Cybersecurity Controls Card
    add_card(s5, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    tx_sec = s5.shapes.add_textbox(Inches(7.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf_sec = tx_sec.text_frame
    tf_sec.word_wrap = True
    p_s = tf_sec.paragraphs[0]
    p_s.text = "CYBERSECURITY & DEFENSE MECHANISMS"
    p_s.font.name = 'Arial'
    p_s.font.size = Pt(12)
    p_s.font.bold = True
    p_s.font.color.rgb = c_green

    sec_pts = [
        "100% Prepared PDO Statements: Parameterized queries eliminating SQL Injection entirely.",
        "BCrypt Credential Hashing: PASSWORD_BCRYPT encryption preventing password reversal.",
        "Role-Based Access Control (RBAC): Strict requireAdmin() guard on all /admin/* endpoints.",
        "Cross-Site Request Forgery (CSRF): Cryptographic tokens required on state-modifying actions.",
        "XSS Mitigation: htmlspecialchars(..., ENT_QUOTES, 'UTF-8') on all browser outputs."
    ]
    for pt in sec_pts:
        p_pt = tf_sec.add_paragraph()
        p_pt.text = f"🔒 {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(11.5)
        p_pt.font.color.rgb = c_white
        p_pt.space_before = Pt(6)

    add_notes(s5, 
        "Security and data integrity were engineered into every layer. Our database is strictly normalized into seven relational tables. "
        "All database queries use prepared statements with parameterized inputs, providing complete immunity against SQL injection. User passwords "
        "are encrypted with BCrypt hashing, administrative endpoints are protected by Role-Based Access Controls, and state-modifying requests require "
        "cryptographically validated CSRF tokens."
    )

    # =========================================================================
    # SLIDE 6: TRANSITION TO LIVE DEMO
    # =========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    add_background(s6)
    add_header(s6, "Slide 6 • Live Demonstration Setup", "Transition to Live Prototype Demonstration", "3:30 – 4:30")

    # Center Big Card
    add_card(s6, Inches(1.2), Inches(1.9), Inches(10.9), Inches(4.8), border_rgb=c_green)
    tx = s6.shapes.add_textbox(Inches(1.5), Inches(2.1), Inches(10.3), Inches(4.4))
    tf = tx.text_frame
    tf.word_wrap = True

    p = tf.paragraphs[0]
    p.text = "🟢 LOCAL SERVER STATUS: PUBLISHED & ONLINE"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = c_green

    p2 = tf.add_paragraph()
    p2.text = "Server URL: http://localhost:8000/   |   Admin Console: http://localhost:8000/admin/index.php"
    p2.font.name = 'Calibri'
    p2.font.size = Pt(13)
    p2.font.bold = True
    p2.font.color.rgb = c_gold
    p2.space_before = Pt(6)

    demo_steps = [
        "1. Storefront Retailing: Interactive luxury catalog, faceted filtering, and live stock scarcity counters.",
        "2. Dynamic Cart & Voucher Engine: Input SEN803 -> Observe automatic 20% discount recalculation.",
        "3. Multi-Method Checkout: Simulated payment processing across Card, Mobile Money, and Bank Transfer.",
        "4. 4-Stage Logistics Tracker: Visual delivery pipeline (Order Placed -> Boxing -> Dispatched -> Delivered).",
        "5. Operations Back-Office: Executive KPI gross revenue dashboard, low-stock warnings, and inventory CRUD."
    ]
    for step in demo_steps:
        p_s = tf.add_paragraph()
        p_s.text = f"▶ {step}"
        p_s.font.name = 'Calibri'
        p_s.font.size = Pt(12)
        p_s.font.color.rgb = c_white
        p_s.space_before = Pt(8)

    add_notes(s6, 
        "At this juncture, as required by Section 4(c) of our project brief, I will now switch to our live published prototype running on our local server at "
        "localhost:8000 to demonstrate the end-to-end consumer shopping journey and back-office administrative workflows."
    )

    # =========================================================================
    # SLIDE 7: LIVE DEMO STEP-BY-STEP CHECKLIST
    # =========================================================================
    s7 = prs.slides.add_slide(blank_layout)
    add_background(s7)
    add_header(s7, "Slide 7 • Live Walkthrough Guide", "Live Prototype Demonstration: Step-by-Step Flow", "4:30 – 7:30")

    # Step Cards (3 left, 3 right)
    steps_left = [
        ("Step 1: Browse Storefront", "Open http://localhost:8000/ -> Highlight luxury editorial light theme, hero capsules, and categories."),
        ("Step 2: Faceted Filtering", "Navigate to /shop.php -> Filter by 'Outerwear' and price range -> Open 'The Savile Cashmere Overcoat'."),
        ("Step 3: Garment Detail & Stock Warning", "Highlight size pills, verified customer reviews, and live scarcity alert ('Only 4 units left!').")
    ]
    for i, (st, sd) in enumerate(steps_left):
        t = Inches(1.9 + i * 1.6)
        add_card(s7, Inches(0.8), t, Inches(5.7), Inches(1.4))
        tx = s7.shapes.add_textbox(Inches(1.0), t + Inches(0.1), Inches(5.3), Inches(1.2))
        tf = tx.text_frame
        p = tf.paragraphs[0]
        p.text = st
        p.font.name = 'Arial'
        p.font.size = Pt(12)
        p.font.bold = True
        p.font.color.rgb = c_gold
        p_d = tf.add_paragraph()
        p_d.text = sd
        p_d.font.name = 'Calibri'
        p_d.font.size = Pt(10.5)
        p_d.font.color.rgb = c_slate

    steps_right = [
        ("Step 4: Bag & Coupon Engine", "Add to bag -> Apply voucher 'SEN803' -> Watch subtotal immediately drop by 20% (£96.00 off)."),
        ("Step 5: Checkout & 4-Stage Tracking", "Proceed to checkout -> Select Card Simulation -> View official invoice and 4-stage tracking timeline."),
        ("Step 6: Admin Operations Console", "Open /admin/index.php (admin@valenti.com) -> Show live gross sales KPIs, low-stock alerts, and CRUD.")
    ]
    for i, (st, sd) in enumerate(steps_right):
        t = Inches(1.9 + i * 1.6)
        add_card(s7, Inches(6.8), t, Inches(5.7), Inches(1.4))
        tx = s7.shapes.add_textbox(Inches(7.0), t + Inches(0.1), Inches(5.3), Inches(1.2))
        tf = tx.text_frame
        p = tf.paragraphs[0]
        p.text = st
        p.font.name = 'Arial'
        p.font.size = Pt(12)
        p.font.bold = True
        p.font.color.rgb = c_green
        p_d = tf.add_paragraph()
        p_d.text = sd
        p_d.font.name = 'Calibri'
        p_d.font.size = Pt(10.5)
        p_d.font.color.rgb = c_white

    add_notes(s7, 
        "[DURING LIVE DEMO]: Here on our homepage, you observe the refined light editorial styling. Navigating to our Collection page, our faceted filter allows patrons "
        "to isolate garments by department, size, or price. Selecting the Savile Cashmere Overcoat, you see our dynamic stock counter showing scarcity. Adding this to our bag "
        "and proceeding to cart, I will now input our course promotional code: S-E-N-8-0-3. Instantly, our algorithmic pricing engine recalculates the subtotal, deducting 20% "
        "while maintaining full tax compliance. Completing checkout with simulated card payment brings us to our official invoice and our interactive 4-stage tracking timeline. "
        "Now, switching over to our Operations Console at /admin, executive managers immediately see updated Gross Sales KPIs, low-stock alerts, and full garment inventory management controls."
    )

    # =========================================================================
    # SLIDE 8: FINANCIAL VIABILITY & PROMOTIONAL ECONOMICS
    # =========================================================================
    s8 = prs.slides.add_slide(blank_layout)
    add_background(s8)
    add_header(s8, "Slide 8 • Financial Viability", "Financial Model, Pricing & Unit Economics", "7:30 – 8:15")

    # Card Left: Unit Economics
    add_card(s8, Inches(0.8), Inches(1.9), Inches(5.7), Inches(4.8))
    tx_l = s8.shapes.add_textbox(Inches(1.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf_l = tx_l.text_frame
    tf_l.word_wrap = True
    p = tf_l.paragraphs[0]
    p.text = "ACCESSIBLE LUXURY UNIT MARGINS"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = c_gold

    econ_pts = [
        "Savile Cashmere Overcoat: Cost £145 | Retail £480 | Margin: £335 (69.8%)",
        "Double-Breasted Blazer: Cost £85 | Retail £295 | Margin: £210 (71.2%)",
        "Mulberry Silk Evening Slip: Cost £70 | Retail £240 | Margin: £170 (70.8%)",
        "Heavyweight French Terry Hoodie: Cost £38 | Retail £125 | Margin: £87 (69.6%)",
        "Promotional Voucher Viability: With voucher SEN803 (20% off) applied on a £300 basket, the order produces £152 in gross profit (63.3% margin), keeping acquisition profitable on day one."
    ]
    for pt in econ_pts:
        p_pt = tf_l.add_paragraph()
        p_pt.text = f"• {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(11)
        p_pt.font.color.rgb = c_slate
        p_pt.space_before = Pt(6)

    # Card Right: 3-Year Projections
    add_card(s8, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    tx_r = s8.shapes.add_textbox(Inches(7.0), Inches(2.0), Inches(5.3), Inches(4.5))
    tf_r = tx_r.text_frame
    tf_r.word_wrap = True
    p_r = tf_r.paragraphs[0]
    p_r.text = "3-YEAR PRO FORMA FORECAST"
    p_r.font.name = 'Arial'
    p_r.font.size = Pt(12)
    p_r.font.bold = True
    p_r.font.color.rgb = c_green

    fin_pts = [
        "Year 1 (Launch): 3,200 Orders | £672,000 GMV | £456,960 Gross Profit (68%) | Net EBITDA: £216,960 (32.3%)",
        "Year 2 (Expansion): 8,500 Orders | £1,997,500 GMV | £1,378,275 Gross Profit | Net EBITDA: £794,275 (39.8%)",
        "Year 3 (Maturity): 19,200 Orders | £4,992,000 GMV | £3,494,400 Gross Profit | Net EBITDA: £2,183,400 (43.7%)",
        "Key Unit Metrics: Average Order Value (AOV) £210–£260; Customer Acquisition Cost (CAC) £45; Lifetime Value (LTV) £780."
    ]
    for pt in fin_pts:
        p_pt = tf_r.add_paragraph()
        p_pt.text = f"📊 {pt}"
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(11)
        p_pt.font.color.rgb = c_white
        p_pt.space_before = Pt(8)

    add_notes(s8, 
        "Turning to our financial model, our unit economics are exceptionally robust. Because we eliminate wholesale distribution intermediaries, our gross product margins "
        "remain above 68%. Furthermore, our promotional voucher architecture is economically sound: even with a 20% discount applied via voucher SEN803, our gross margin on an average "
        "basket remains over 63%, generating immediate cash flow while accelerating new customer acquisition."
    )

    # =========================================================================
    # SLIDE 9: RISK MITIGATION & CLOUD SCALABILITY
    # =========================================================================
    s9 = prs.slides.add_slide(blank_layout)
    add_background(s9)
    add_header(s9, "Slide 9 • Risk & Scalability", "Risk Assessment & Cloud Scalability Roadmap", "8:15 – 9:00")

    cards_s9 = [
        ("Flash Drop Traffic Spikes", "Containerized Docker microservices orchestrated across AWS ECS with automatic horizontal pod scaling during high-traffic capsule launches."),
        ("Global Content Latency", "Offloading high-resolution product photography and assets to Cloudflare Edge CDN with Brotli compression and image WebP caching."),
        ("High-Concurrency Cart Sessions", "In-memory Redis session caching layer providing sub-millisecond cart read/writes and coupon code validations."),
        ("Regulatory & Data Compliance", "Strict GDPR compliance with encrypted patron data storage, explicit cookie preferences, and PCI-DSS tokenized payment gateway integration.")
    ]
    for i, (title, desc) in enumerate(cards_s9):
        col = i % 2
        row = i // 2
        l = Inches(0.8 + col * 6.0)
        t = Inches(1.9 + row * 2.5)
        add_card(s9, l, t, Inches(5.7), Inches(2.3))
        
        tx = s9.shapes.add_textbox(l + Inches(0.2), t + Inches(0.15), Inches(5.3), Inches(2.0))
        tf = tx.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = f"🛡 {title}"
        p.font.name = 'Arial'
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = c_gold
        
        p_d = tf.add_paragraph()
        p_d.text = desc
        p_d.font.name = 'Calibri'
        p_d.font.size = Pt(11.5)
        p_d.font.color.rgb = c_slate
        p_d.space_before = Pt(6)

    add_notes(s9, 
        "To ensure long-term viability, we conducted a rigorous risk assessment. High-traffic flash sales and seasonal capsule drops are mitigated through a clear "
        "scalability roadmap: containerizing our application with Docker, offloading image assets to Cloudflare Edge CDN, and integrating Redis in-memory caching. "
        "Additionally, the platform adheres to GDPR data privacy regulations with secure session management and patron consent mechanisms."
    )

    # =========================================================================
    # SLIDE 10: CONCLUSION & DEFENSE SUMMARY
    # =========================================================================
    s10 = prs.slides.add_slide(blank_layout)
    add_background(s10)
    add_header(s10, "Slide 10 • Academic Evaluation", "Conclusion & Course Project Summary", "9:00 – 10:00")

    add_card(s10, Inches(1.2), Inches(1.9), Inches(10.9), Inches(4.8), border_rgb=c_gold)
    tx = s10.shapes.add_textbox(Inches(1.5), Inches(2.1), Inches(10.3), Inches(4.4))
    tf = tx.text_frame
    tf.word_wrap = True

    p = tf.paragraphs[0]
    p.text = "FULL COMPLIANCE WITH SEN 803 PROJECT REQUIREMENTS"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = c_gold

    eval_pts = [
        "✓ Prototype Business Portal (60 Marks): Fully developed, interactive B2C portal featuring dynamic catalog, automated voucher pricing, multi-rail checkout, 4-stage tracking, and admin back-office.",
        "✓ Business Plan Report (30 Marks): Comprehensive academic report in Word format (.docx) detailing strategic market positioning, IT value creation, and 3-year financial forecasts.",
        "✓ 10-Minute Presentation & Local Server Walkthrough (10%): Live demonstration on local Apache/PHP server on http://localhost:8000/ with dual-driver SQLite/MySQL zero-config portability.",
        "✓ Commercial Viability Proven: 65% gross margins, automated customer retention, and sound promotional economics."
    ]
    for pt in eval_pts:
        p_pt = tf.add_paragraph()
        p_pt.text = pt
        p_pt.font.name = 'Calibri'
        p_pt.font.size = Pt(12)
        p_pt.font.color.rgb = c_white
        p_pt.space_before = Pt(8)

    p_close = tf.add_paragraph()
    p_close.text = "\nThank you for your time and evaluation. I welcome your questions and examination discussion."
    p_close.font.name = 'Georgia'
    p_close.font.size = Pt(13)
    p_close.font.bold = True
    p_close.font.color.rgb = c_gold_light

    add_notes(s10, 
        "In conclusion, Valenti Atelier provides a complete, academically rigorous, and commercially viable demonstration of how software technology transforms B2C commerce. "
        "We have satisfied all requirements of the SEN 803 brief: delivering the business plan report, developing and publishing the local prototype portal, and illustrating its "
        "strategic value. Thank you very much for your time, and I am now ready for your questions and examination discussion."
    )

    out_path = os.path.join(os.path.dirname(__file__), "Valenti_Atelier_SEN803_Presentation.pptx")
    prs.save(out_path)
    print(f"PowerPoint Presentation successfully generated at: {out_path}")

if __name__ == '__main__':
    create_presentation()
