@extends('layouts.app')

@section('title', 'System Project Documentation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 print:p-0" x-data="{
    projectTitle: 'Clara’s Beast: A Premium Food Ordering and Fast Delivery System',
    members: '{{ auth()->check() ? auth()->user()->name : 'Chynna Bagao & Development Team' }}',
    course: 'Systems Analysis and Design / Web Application Development (IT-301)',
    instructor: 'Prof. Michael Johnson',
    dateSubmitted: '{{ date('F d, Y') }}',
    showGuidelines: true,
    fontSize: 12,
    lineHeight: 1.5,
    customIntro: '',
    printDocument() {
        window.print();
    },
    copyToClipboard() {
        const text = document.getElementById('printable-paper-content').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Document content copied to clipboard successfully!');
        }).catch(err => {
            alert('Failed to copy text: ' + err);
        });
    }
}">

    <!-- Top Configuration Header & Academic Control Deck (Hidden in print) -->
    <div class="mb-8 p-6 bg-slate-900 text-white rounded-[2rem] shadow-xl border border-slate-800 space-y-6 print:hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div class="space-y-1">
                <span class="inline-flex items-center gap-1.5 bg-orange-600/10 border border-orange-500/20 px-3.5 py-1 rounded-full text-[10px] font-black uppercase text-orange-400 tracking-wider">
                    <i class="fas fa-file-invoice text-xs"></i> ACADEMIC DOCUMENTATION SYSTEM
                </span>
                <h1 class="text-2xl font-black tracking-tight text-white mt-1">Final System Project Paper Generator</h1>
                <p class="text-xs font-bold text-slate-400">Fill in your group and class details below to generate a formal, print-ready Times New Roman A4 manuscript.</p>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <button type="button" @click="copyToClipboard()" class="bg-slate-800 hover:bg-slate-700 text-white font-black px-5 py-3 rounded-xl text-xs uppercase tracking-widest transition-all cursor-pointer flex items-center gap-2">
                    <i class="fas fa-copy"></i> Copy Raw Text
                </button>
                <button type="button" @click="printDocument()" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-6 py-3 rounded-xl text-xs uppercase tracking-widest shadow-lg shadow-orange-950/50 transition-all cursor-pointer flex items-center gap-2">
                    <i class="fas fa-print"></i> Export to PDF / Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-bold text-slate-300">
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Project title name</label>
                <input type="text" x-model="projectTitle" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-orange-500 font-bold">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Members/Authors list</label>
                <input type="text" x-model="members" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-orange-500 font-bold">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Course / Class Subject</label>
                <input type="text" x-model="course" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-orange-500 font-bold">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Instructor's Name</label>
                <input type="text" x-model="instructor" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-orange-500 font-bold">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Date of Submission</label>
                <input type="text" x-model="dateSubmitted" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-orange-500 font-bold">
            </div>
            <!-- Formatting adjustments -->
            <div class="space-y-1.5">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-400">Styling options</label>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-2.5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Size: <span x-text="fontSize + 'pt'"></span></span>
                        <div class="flex gap-1.5">
                            <button type="button" @click="fontSize = Math.max(10, fontSize - 1)" class="px-1.5 py-0.5 bg-slate-700 hover:bg-slate-600 rounded text-[10px]">-</button>
                            <button type="button" @click="fontSize = Math.min(16, fontSize + 1)" class="px-1.5 py-0.5 bg-slate-700 hover:bg-slate-600 rounded text-[10px]">+</button>
                        </div>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-2.5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Spacing: <span x-text="lineHeight"></span></span>
                        <div class="flex gap-1.5">
                            <button type="button" @click="lineHeight = Math.max(1.0, lineHeight - 0.25)" class="px-1.5 py-0.5 bg-slate-700 hover:bg-slate-600 rounded text-[10px]">-</button>
                            <button type="button" @click="lineHeight = Math.min(2.5, lineHeight + 0.25)" class="px-1.5 py-0.5 bg-slate-700 hover:bg-slate-600 rounded text-[10px]">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Criteria Checklist overlay -->
        <div x-show="showGuidelines" class="p-4 bg-amber-500/10 border border-amber-500/20 text-xs font-bold rounded-2xl text-amber-100 flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-400 text-base mt-0.5 shrink-0"></i>
            <div class="space-y-1 flex-grow">
                <div class="flex justify-between items-center">
                    <span class="uppercase font-black text-[10px] tracking-wider text-amber-400">Syllabus Submission Checklist Conformity</span>
                    <button type="button" @click="showGuidelines = false" class="text-amber-400 hover:text-amber-200"><i class="fas fa-times"></i></button>
                </div>
                <p class="leading-relaxed">Generated output complies with guidelines: <span class="text-white">Times New Roman text</span>, customizable <span class="text-white">12pt base sizing</span>, double/1.5 space formats, <span class="text-white">1-inch standard margin alignments</span>, and encompasses the exact 11 requested criteria chapters (Title, Intro, Objectives, Scope, Overview, Tech Stack, System Design, DB, Testing, Conclusion, Recommendations).</p>
            </div>
        </div>
    </div>

    <!-- MAIN MANUSCRIPT SHEETS CANVAS -->
    <!-- Screen: centered with subtle drop shadow mimicking a printable A4 stack. Print: unstyled raw flowing content starting exactly with margins -->
    <div class="bg-slate-100 p-4 sm:p-8 rounded-[2rem] border border-slate-200 shadow-inner flex justify-center print:bg-transparent print:p-0 print:border-none print:shadow-none">

        <div id="printable-paper-content"
             style="font-family: 'Times New Roman', Times, serif;"
             :style="'font-size: ' + fontSize + 'pt; line-height: ' + lineHeight + ';'"
             class="w-full max-w-full md:w-[210mm] bg-white text-slate-900 border border-slate-200 p-6 sm:p-12 md:p-[1in] shadow-2xl space-y-12 text-justify select-text relative break-words print:shadow-none print:border-none print:p-0 print:text-black">

             <!-- Running header (Visible in print page top margin boundary via CSS style inside view) -->
             <div class="hidden print:flex justify-between items-center text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200 pb-1 mb-8">
                 <span>SYSTEM DESIGN PROJECT PAPER PROJECT: CLARA’S BEAST</span>
                 <span>SUBMITTED BY: CHYNNA BAGAO</span>
             </div>

             <!-- SECTION 1: TITLE PAGE -->
             <div class="min-h-[8.5in] flex flex-col justify-between items-center text-center py-20 bg-amber-50/5 print:bg-transparent page-break-after">
                 <div></div>

                 <!-- Project Title -->
                 <div class="space-y-8">
                     <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-orange-600 print:text-black leading-none mb-4">SYSTEM DESIGN & IMPLEMENTATION MONOGRAPH</p>
                     <h1 class="text-3xl font-bold leading-tight" x-text="projectTitle"></h1>
                     <div class="w-24 h-1 bg-orange-600 mx-auto print:hidden"></div>
                 </div>

                 <!-- Class details segment -->
                 <div class="space-y-10 my-16">
                     <div class="space-y-1">
                         <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">PREPARED BY MEMBERS OF THE PRIDE</p>
                         <p class="text-lg font-bold" x-text="members"></p>
                     </div>

                     <div class="space-y-1">
                         <p class="text-xs uppercase tracking-widest text-slate-400 font-bold font-serif">DEVELOPED UNDER COURSE DIRECTORY</p>
                         <p class="text-base font-bold italic" x-text="course"></p>
                     </div>
                 </div>

                 <!-- Instructor + submission details -->
                 <div class="space-y-1 pt-12">
                     <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">SUBMITTED FOR CRITICAL REVIEW TO</p>
                     <p class="text-base font-bold" x-text="'Instructor: ' + instructor"></p>
                     <p class="text-xs text-slate-500 mt-2 font-bold" x-text="'DATE OF SUBMISSION: ' + dateSubmitted"></p>
                 </div>

                 <div></div>
             </div>

             <!-- SECTION 2: INTRODUCTION -->
             <div class="space-y-4 pt-10">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">1. Introduction</h2>

                 <div class="space-y-4 text-indent-paragraph text-slate-800 print:text-black">
                     <p>
                         In the contemporary culinary and fast-food industry, the digital transition has evolved from a progressive auxiliary option into a cornerstone operational necessity. Traditional, manual floor service operations are inherently limited in tracking micro-kitchen order transformations, managing real-time inventory balances, and certifying external payment receipts without manual delay bottlenecks. Modern dining consumers demand hyper-speed feedback, dynamic menu interaction, and fluid visual notifications for their customized order lifecycles.
                     </p>
                     <p>
                         <strong>Clara’s Beast</strong> is designed to encapsulate a high-velocity, full-stack, responsive ordering and premium food fulfillment system. Operating as a hybrid microcommerce model, the platform bridges active customers, busy dynamic kitchen personnel, and high-tier inventory administrators through intuitive role-based permissions layouts. The system features a unified digital menu panel that classifies items based on categories, incorporates an instantaneous shopping cart, implements custom-structured pre-order dates for scheduled deliveries, and provides an end-to-end payment audit verification log to keep food service records clear of errors.
                     </p>
                     <p>
                         By resolving miscommunication errors in kitchen status updates and providing digital profiles with custom local file uploads, Clara’s Beast aims to empower both small-to-medium enterprise food hubs and premium culinary outlets to deliver with modern precision.
                     </p>
                 </div>
             </div>

             <!-- SECTION 3: OBJECTIVES OF THE SYSTEM -->
             <div class="space-y-4 pt-4 page-break-after">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">2. Objectives of the System</h2>

                 <div class="space-y-3 text-slate-800 print:text-black">
                     <p class="font-bold underline">General Objective:</p>
                     <p class="text-indent-paragraph">
                         To engineer and deploy a secure, web-enabled full-stack Food Ordering, Kitchen Pipeline Management, and automated GCash Transaction Verification system under the brand name "Clara’s Beast", ensuring responsive coordination among customers, kitchen preparation staff, and store administrators.
                     </p>

                     <p class="font-bold underline mt-4">Specific Objectives:</p>
                     <ul class="list-decimal pl-6 space-y-2">
                         <li><strong>Customer Profile Management:</strong> Build a registration module enabling customers to upload profile photographs or stream external web avatars, forming persistent user cards in the active database.</li>
                         <li><strong>Microcommerce Engine & Dynamic Shopping Cart:</strong> Implement a robust React-like client-side total recalculator that handles item portion modifications, capping quantity changes at 100 units to avoid database buffer overflows.</li>
                         <li><strong>Multi-state Kitchen Status System:</strong> Construct a database-backed state machine mapping order workflows from <em>Pending</em>, <em>Confirmed</em>, <em>Preparing</em>, <em>Ready</em>, <em>Completed</em>, and <em>Cancelled</em>, which is modifiable in real-time by kitchen crews.</li>
                         <li><strong>Financial Trail Security:</strong> Enable GCash QR payment transfers by implementing a receipt file validation checker that prevents files exceeding 5MB from uploading, while storing transaction references safely.</li>
                         <li><strong>Customer-Initiated Cancellation and Deletion:</strong> Authorize customers to cancel live orders before active kitchen preparation starts and permit complete removal of finalized order trails.</li>
                     </ul>
                 </div>
             </div>

             <!-- SECTION 4: SCOPE AND LIMITATIONS -->
             <div class="space-y-4 pt-4">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">3. Scope and Limitations</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p><strong>3.1 System Capabilities (Scope of the Project)</strong></p>
                     <p class="text-indent-paragraph">
                         The developed Clara's Beast system encompasses several key modules designed to handle all aspects of premium food commerce. These include: secure subscriber authorization built with CSRF-protected passwords, customizable user settings for updating name, email, credentials, profile portrait selection with image previews, dynamic frontend menu catalogs group-sorted by active categories, multi-item shopping carts, GCash receipt uploads with image size restrictions, custom checkout logic supporting immediate fulfillment versus pre-scheduled delivery date-times, active staff kitchen monitor grids, and granular settings controls to configureGCash details or general system open hours.
                     </p>

                     <p><strong>3.2 System Exclusions (Limitations of the System)</strong></p>
                     <p class="text-indent-paragraph">
                         The application operates with specific constraints contextually bound to local operations and compliance. It does not interface dynamically with live external banking APIs (e.g. Visa, Mastercard, or instant GCash e-wallet balance debits) such that payment clearance is managed through a secure, visual visual verify queue processed manually by Administrators using uploaded user receipt files. No automatic physical receipt printing hardware integration is pre-configured; instead, the system designs a visual invoice format optimized for desktop paper previews and standard PDF generation via native browser prints.
                     </p>
                 </div>
             </div>

             <!-- SECTION 5: SYSTEM OVERVIEW -->
             <div class="space-y-4 pt-4 page-break-after">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">4. System Overview</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p class="text-indent-paragraph">
                         Clara's Beast serves as a complete database-driven ordering hub with specific modules mapping directly to three principal system roles:
                     </p>

                     <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 print:bg-white print:border-none">
                         <p class="font-bold underline">System Modules Framework:</p>
                         <ul class="list-disc pl-6 space-y-1.5 mt-2">
                             <li><strong>Inventory Module:</strong> Handled by Admins to publish, view, update, deactivate, or delete active menu items, prices, and catalog categories.</li>
                             <li><strong>Order Dispatch module:</strong> Directs customer cart details into persistent order queues with specialized instruction logs.</li>
                             <li><strong>Kitchen Module:</strong> Multi-tiered kitchen pipeline enabling cooks to mark items as in prep, readied for courier pickup, or completed for customer consumption.</li>
                             <li><strong>Payment Audit Module:</strong> Centralizes GCash reference inputs and checkmark verifications.</li>
                         </ul>
                     </div>

                     <p class="font-bold mt-4">User Roles Matrix:</p>
                     <div class="overflow-x-auto">
                         <table class="w-full text-xs text-left border border-slate-300">
                             <thead>
                                 <tr class="bg-slate-100">
                                     <th class="p-2 border">Functional Feature</th>
                                     <th class="p-2 border">Customer Role</th>
                                     <th class="p-2 border">Kitchen Staff Role</th>
                                     <th class="p-2 border">System Admin Role</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                     <td class="p-2 border">Register & Profile Picture upload</td>
                                     <td class="p-2 border text-green-700">✓ Enabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                 </tr>
                                 <tr>
                                     <td class="p-2 border">Cart & Checkout Process</td>
                                     <td class="p-2 border text-green-700">✓ Enabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                 </tr>
                                 <tr>
                                     <td class="p-2 border">Set Pre-Order Time & GCash receipt</td>
                                     <td class="p-2 border text-green-700">✓ Enabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                 </tr>
                                 <tr>
                                     <td class="p-2 border">Kitchen Pipeline Processing</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                     <td class="p-2 border text-green-700">✓ Full Access</td>
                                     <td class="p-2 border text-green-700">✓ Dashboard View</td>
                                 </tr>
                                 <tr>
                                     <td class="p-2 border">Order Record Erasure</td>
                                     <td class="p-2 border text-green-700">✓ Self Cancel/Delete</td>
                                     <td class="p-2 border text-slate-450">&times; Disabled</td>
                                     <td class="p-2 border text-red-700">✓ Admin Void Delete</td>
                                 </tr>
                                 <tr>
                                     <td class="p-2 border">Menu & Category Management</td>
                                     <td class="p-2 border text-slate-450">&times; Read Only</td>
                                     <td class="p-2 border text-slate-450">&times; Read Only</td>
                                     <td class="p-2 border text-green-700">✓ Full Access</td>
                                 </tr>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>

             <!-- SECTION 6: TECHNOLOGIES USED -->
             <div class="space-y-4 pt-4">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">5. Technologies Used</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p class="text-indent-paragraph">
                         To ensure standard architecture, modern aesthetics, type-safety and swift deployment, a curated framework selection is utilized for both production builds and database migrations:
                     </p>

                     <div class="space-y-2">
                         <p><strong>5.1 Web Framework & Back-End</strong></p>
                         <p class="text-indent-paragraph pl-4">
                             The server is powered by the <strong>Laravel Framework</strong>, executing MVC pattern routing and state manipulation. Laravel provides secure session handlers, object-relation mappings (Eloquent ORM), CSRF form token headers, and file systems managers out-of-the-box.
                         </p>

                         <p><strong>5.2 Frontend Components & Styling</strong></p>
                         <p class="text-indent-paragraph pl-4">
                             The user interfaces and layout structures are rendered via the <strong>PHP Blade Templating Engine</strong>, utilizing master layout abstractions, modular components, and section inheritance decorators. The application layout is styled using <strong>Tailwind CSS v3+</strong>, incorporating responsive mobile prefixes, custom gradients for branding, layout transitions, and fluid grid parameters. <strong>AlpineJS v3</strong> is standardly added at the client view scope to govern reactive UI components like: client-side date selections, dynamic image source previews, file upload previews, and error notification dismissals.
                         </p>

                         <p><strong>5.3 Database Architecture</strong></p>
                         <p class="text-indent-paragraph pl-4">
                             <strong>MySQL</strong> serves as the core relational database storage engine utilizing Laravel migrations, table constraints, and foreign key rules, ensuring relational records stand secure against schema breakdowns.
                         </p>
                     </div>
                 </div>
             </div>

             <!-- SECTION 7: SYSTEM DESIGN -->
             <div class="space-y-4 pt-4 page-break-after">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">6. System Design</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p><strong>6.1 Use Case Diagram (System Interactions)</strong></p>
                     <p class="text-indent-paragraph mb-2">
                         The system models user interactions, permissions, and service scopes across Customer, Kitchen Staff, and Administrator actors through distinct use cases within the application boundary:
                     </p>

                     <!-- Technical ASCII Model representation --><!-- TEST -->
                     <div class="font-mono text-xs bg-slate-50 border border-slate-200 p-4 rounded-xl leading-relaxed whitespace-pre overflow-x-auto print:bg-white print:border-slate-300 animate-fade-in">
                                   CLARA'S BEAST SYSTEM USE CASES & BOUNDARIES
                     +-----------------------------------------------------------------+
                     | SYSTEM BOUNDARY                                                 |
                     |                                                                 |
                     |        (   Register / Manage Account & Profile   )              |
                     |                       ^                                         |
                     |                       |                                         |
                     |        (         Browse Menu & Categories        )              |
                     |                       ^                                         |
    +----------+     |                       |                                         |
    |          |-----+                       |                                         |
    | CUSTOMER |-----+-----------------------+                                         |
    |          |-----+---------------------------------------+                         |
    +----------+     |                                       |                         |
                     |        (    Customize Items & Place Order    )                  |
                     |                                       |                         |
                     |        (    Upload Payment Proof & Reference )                  |
                     |                       ^               |                         |
                     |                       |               |                         |
                     |        (      Track Order & Cancel Pending   )                  |
                     |                                                                 |
                     |                                                                 |
                     |        (     View Live Queue & Orders Tracker )                 |
    +----------+     |                       ^                                         |
    | KITCHEN  |-----+                       |                                         |
    |  STAFF   |-----+-----------------------+                                         |
    +----------+     |                                                                 |
                     |        (   Update Order Status Lifecycle     )                  |
                     |                                                                 |
                     |                                                                 |
                     |        (      Manage Menu Catalog & Stock    )                  |
                     |                       ^                                         |
    +----------+     |                       |                                         |
    |          |-----+                       |                                         |
    |  ADMIN   |-----+-----------------------+                                         |
    |          |-----+---------------------------------------+                         |
    +----------+     |                                       |                         |
                     |        (    Verify Payment & Receipt Proof   )                  |
                     |                                       |                         |
                     |        (   User Role System Administration   )                  |
                     |                                                                 |
                     +-----------------------------------------------------------------+
                     </div>
                     <div class="hidden">
+------------------+         +------------------+         +------------------+
|      users       |         |      orders      |         |     payments     |
+------------------+         +------------------+         +------------------+
| id [PK]          | 1     N | id [PK]          | 1     1 | id [PK]          |
| name             |---------| user_id [FK]     |---------| order_id [FK]    |
| email            |         | total_amount  -- |         | payment_method   |
| password         |         | order_type      | |         | amount           |
| role             |         | status          | |         | payment_status   |
| profile_image    |         | payment_method  | |         | reference_number |
+------------------+         +------------------+ |         | proof_image      |
                                                  |         +------------------+
                                                1 | N
                                         +------------------+
                                         |  order_details   |
                                         +------------------+
                                         | id [PK]          |
                                         | order_id [FK]    |
                                         | menu_item_id [FK]|
                                         | quantity         |
                                         | subtotal         |
                                         | instructions     |
                                         +------------------+
                     </div>

                     <p class="text-indent-paragraph mt-4">
                         The use case diagram outlines the behavioral dynamics and security boundaries of Clara's Beast Ordering System, delineating clear user execution pathways across three primary actors. The <strong>Customer</strong> acts as the primary external client, initiating interactions by self-registering and maintaining a secure profile. Customers navigate a categorized culinary catalogue, configure discrete order preferences—such as item quantities and custom culinary annotations or special instructions—and execute either immediate or scheduled checkout protocols, backed by cash or secure, file-validated online GCash ledger references. The <strong>Kitchen Staff</strong> represents the fulfillment terminal, consuming real-time order states to manage preparation pipelines; they interact exclusively with active culinary queues, upgrading order lifecycles from preparation states to active dispatch-ready and final fulfillment. Lastly, the <strong>Administrator</strong> oversees operational orchestration and financial integrity, auditing proof-of-payment image uploads against electronic banking reference logs, dynamically managing catalogue metadata (menu listings, pricing, and availability), and enforcing Role-Based Access Control (RBAC) schemas across both administrative and kitchen operators.
                     </p>

                     <p class="mt-4"><strong>6.2 Flowchart Diagram for Restaurant Ordering System</strong></p>
                     <p class="text-indent-paragraph">
                         This flowchart maps the operational lifecycle and data flow of Clara's Beast Ordering System, tracking how Role-Based Access Control (RBAC) securely splits the system into three distinct user pathways: Customer, Kitchen Staff, and Administrator. On the blue Customer pathway, users register, authenicate, browse menus by categories, customize cart items, and complete checkouts. They choose immediate or scheduled order types, and select Cash or GCash (the latter requiring GCash reference numbers and receipt proof uploads). This connects directly to the green Kitchen Staff workflow, where food prepares through statuses (Pending -> Confirmed -> Preparing -> Ready -> Completed). Concurrently, the orange Admin pathway oversees backend system integrity, verifying payments, managing culinary catalogue metadata, and managing user roles.
                     </p>
                     <div class="font-mono text-[10px] bg-slate-50 border border-slate-200 p-4 rounded-xl leading-relaxed whitespace-pre overflow-x-auto print:bg-white animate-fade-in">
           +-----------------------------------------------------------------------------------+
           |                     CLARA'S BEAST ORDERING LIFECYCLE FLOWCHART                     |
           +-----------------------------------------------------------------------------------+

              [ CUSTOMER PATHWAY (Blue) ]             [ ADMIN FLOW (Orange) ]                  [ KITCHEN WORKFLOW (Green) ]

                 +-----------------------+              +-----------------------+              +-----------------------+
                 | Register & Login      |              | Manage Food Menu      |              | Monitor Orders Queue  |
                 +-----------+-----------+              +-----------+-----------+              +-----------+-----------+
                             |                                      |                                      |
                             v                                      v                                      v
                 +-----------------------+                      /-------\                              +-----------------------+
                 | Add Items to Cart     |                      | Action|                              | Prepare Orders Queue  |
                 +-----------+-----------+                      \-------/                              +-----------+-----------+
                             |                                   /     \                                           |
                             v                            Reject/       \Approve                                   v
                 +-----------------------+                     /         \                             +-----------------------+
                 | Select Order Checkout |                    v           v                            | Update Order Status   |
                 +-----------+-----------+              +----------+ +----------+                      +-----------+-----------+
                             |                          | Cancel   | | Approve  |                                  |
                             v                          | Order    | | Payment  |                                  v
                 +-----------------------+              +----+-----+ +----+-----+                      +-----------------------+
                 | Place Pending Order   |                   |            |                            | Order Ready & Serve   |
                 +-----------+-----------+                   v            v                            +-----------------------+
                             |                     +-----------+ +----+-----+
                             +-------------------->| Queue     | | Cooking   |
                             (Notify Staff )       +-----------+ +----+-----+
                                                                          |
                                                                          v
                                                                     +----------+
                                                                     | Completed|
                                                                     +----------+
                     </div>
                     </div>
                 </div>
             </div>


             <!-- SECTION 6.3: SYSTEM ARCHITECTURE DIAGRAM (SEPARATE PAGE) -->
             <div class="space-y-4 pt-4 page-break-after">
                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p><strong>6.3 System Architecture Diagram</strong></p>
                      <p class="text-indent-paragraph">
                          The system architecture of Clara's Beast Ordering System follows a classic, highly secure three-tier design pattern optimized for modularity, database integrity, and transactional reliability. At the presentation layer, the application presents a responsive interface powered by tailwind styles and alpine/blade layouts to ensure customers, staff, and admins get live updates. The application layer runs on a robust Laravel 11 MVC engine equipped with security-hardened HTTP middleware layers to enforce strict Role-Based Access Control (RBAC) boundaries. This middleware intercepts requests to protect sensitive kitchen queue routes and administrator payment auditing routines. The persistence layer relies on a managed Aiven Cloud MySQL database, where connections are strictly encrypted via TLS/SSL Handshakes with cert verification (utilizing standard base CA certificates). Reference tracking for transactions integrates financial verifications for non-cash orders.
                      </p>
                      <div class="font-mono text-[10px] bg-slate-50 border border-slate-200 p-4 rounded-xl leading-relaxed whitespace-pre overflow-x-auto print:bg-white animate-fade-in mb-6">
           +-----------------------------------------------------------------------------------+
           |                     CLARA'S BEAST ORDERING SYSTEM ARCHITECTURE                    |
           +-----------------------------------------------------------------------------------+

             [ PRESENTATION LAYER (CLIENTS / BROWSER) ]
             +------------------------------+     +------------------------------+
             |       CUSTOMER CLIENT        |     |     STAFF / ADMIN CLIENT     |
             |  - Browse Classified Menus    |     |  - Track Fulfillments Queue  |
             |  - Special culinary order note|     |  - Manage Categories & DB    |
             |  - GCash Pay receipt upload  |     |  - Audit & Approve Payments  |
             +--------------+---------------+     +--------------+---------------+
                            |                                    |
                            | HTTP Request (Form Submit / Web)   |
                            v                                    v
             +-------------------------------------------------------------------+
             | nginx Web Proxy / Entry Route Gateway                             |
             +----------------------------------+--------------------------------+
                                                | Handled by artisan http-kernel
                                                v
             [ APPLICATION SERVER LAYER (LARAVEL 11 MVC ENGINE) ]
             +-------------------------------------------------------------------+
             |               Security & Middleware Enforcements Router           |
             |  - Auth Session Validator      - Guard Admin Panel (RBAC)         |
             |  - Guest/Customer Limiters     - Guard Kitchen Queue Controller   |
             +----------------------------------+--------------------------------+
                                                | Checked & Approved
                                                v
             +-------------------------------------------------------------------+
             |                      HTTP Controllers Layer                       |
             |  - AuthController: Sign in & register validation                  |
             |  - MenuController: Active lists, categories, & item details       |
             |  - CartController: Manage persistent cart session states          |
             |  - OrderController & AdminDashboardController                     |
             +----------------------------------+--------------------------------+
                             |                  |                  |
                    Query /  |                  | Active           | Validates
                    Persist  |                  | Categories       | Proof Images
                             v                  v                  v
             [ PERSISTENCE & RESOURCE SERVICES TIERS ]
             +-----------------+        +---------------+        +---------------+
             | Eloquent Models |        | File System   |        | GCash Ledger  |
             | User, MenuItem, |        | Storage       |        | (Reference    |
             | Order, Payment  |        | (Receipts &   |        | Verification  |
             | Category        |        | Food Images)  |        | & Audit)      |
             +--------+--------+        +---------------+        +---------------+
                      |
                      | TLS/SSL Encryption Handshake (verify cert verification)
                      +---------------[ mysql-your-service-name.aivencloud.com:25060 ]---------------+
                                                                                                     |
                                                                                                     v
                                                                                           +-------------------+
                                                                                           | MANAGED DATABASE  |
                                                                                           |  AIVEN CLOUD SQL  |
                                                                                           |  (MySQL 8 Engine) |
                                                                                           +-------------------+
                      </div>
                  </div>
              </div>

             <!-- SECTION 8: DATABASE STRUCTURE -->
             <div class="space-y-4 pt-4">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">7. Database Structure</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p class="text-indent-paragraph text-xs font-bold text-slate-500">
                         The following section documents the exact table schemas, data configurations, constraints, and relationships within Clara's Beast MySQL back-end migrations:
                     </p>

                     <!-- Table 1: Users -->
                     <div class="space-y-2">
                         <p class="font-bold text-sm">Table 1.1: <code>users</code></p>
                         <table class="w-full text-xs text-left border border-slate-300">
                             <thead class="bg-slate-100">
                                 <tr>
                                     <th class="p-1.5 border">Field</th>
                                     <th class="p-1.5 border">Type</th>
                                     <th class="p-1.5 border">Key</th>
                                     <th class="p-1.5 border">Nullability</th>
                                     <th class="p-1.5 border">Description</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                     <td class="p-1 border font-mono">id</td>
                                     <td class="p-1 border">BIGINT</td>
                                     <td class="p-1 border">PK</td>
                                     <td class="p-1 border text-red-600">NOT NULL</td>
                                     <td class="p-1 border">Auto-incrementing user index.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">name</td>
                                     <td class="p-1 border">VARCHAR(255)</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-red-600">NOT NULL</td>
                                     <td class="p-1 border">Member's complete alphabet identifier name.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">email</td>
                                     <td class="p-1 border">VARCHAR(255)</td>
                                     <td class="p-1 border">Unique</td>
                                     <td class="p-1 border text-red-600">NOT NULL</td>
                                     <td class="p-1 border">Security credential electronic mailbox identifier.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">email_verified_at</td>
                                     <td class="p-1 border">TIMESTAMP</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-emerald-600">NULLABLE</td>
                                     <td class="p-1 border">Timestamp when the email was verified.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">password</td>
                                     <td class="p-1 border">VARCHAR(255)</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-red-600">NOT NULL</td>
                                     <td class="p-1 border">Hashed password credential value using bcrypt.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">role</td>
                                     <td class="p-1 border">VARCHAR(50)</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-red-600">NOT NULL</td>
                                     <td class="p-1 border">Authorizations rank ('admin', 'staff', 'customer').</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">profile_image</td>
                                     <td class="p-1 border">VARCHAR(2048)</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-emerald-600">NULLABLE</td>
                                     <td class="p-1 border">Reference path to image upload or external URL.</td>
                                 </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">remember_token</td>
                                     <td class="p-1 border">VARCHAR(100)</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-emerald-600">NULLABLE</td>
                                     <td class="p-1 border">Token for securing Laravel's "remember me" session.</td>
                                  </tr>
                                 <tr>
                                     <td class="p-1 border font-mono">created_at / updated_at</td>
                                     <td class="p-1 border">TIMESTAMP</td>
                                     <td class="p-1 border">-</td>
                                     <td class="p-1 border text-emerald-600">NULLABLE</td>
                                     <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                 </tr>
                             </tbody>
                          </table>
                      </div>

                      <!-- Table 2: Categories -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.2: <code>categories</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Auto-incrementing category unique identifier.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">name</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">Unique</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Descriptive name of the menu category (e.g., Starters, Meals).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at / updated_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 3: Menu Items -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.3: <code>menu_items</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Auto-incrementing product unique identifier.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">name</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">The printed name of the culinary option.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">description</td>
                                      <td class="p-1 border">TEXT</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Detailed descriptions of portions, allergens, and ingredients.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">price</td>
                                      <td class="p-1 border">DECIMAL(10,2)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Uncapped monetary pricing for the item.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">category</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Category string group pointer.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">image</td>
                                      <td class="p-1 border">VARCHAR(2048)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Relative storage folder reference path for the option photo.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">availability_status</td>
                                      <td class="p-1 border">BOOLEAN</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Determines menu display filters (True = Available, False = Sold Out).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at / updated_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 4: Orders -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.4: <code>orders</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Auto-incrementing transaction index header number.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">user_id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">FK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Links user ID record in users table; updates cascade.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">total_amount</td>
                                      <td class="p-1 border">DECIMAL(10,2)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Billing statement final calculated payout cost.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">order_type</td>
                                      <td class="p-1 border">VARCHAR(50)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Fulfillment mode: 'Immediate' or 'Scheduled'.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">scheduled_datetime</td>
                                      <td class="p-1 border">DATETIME</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Fulfillment target timestamp for scheduled pre-orders.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">status</td>
                                      <td class="p-1 border">VARCHAR(50)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Fulfillment status workflow phase: (Pending, Confirmed, Preparing, Ready, Completed, Cancelled).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">payment_method</td>
                                      <td class="p-1 border">VARCHAR(50)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Fulfillment checkout option ('Cash', 'GCash').</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">payment_status</td>
                                      <td class="p-1 border">VARCHAR(50)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Clearance status of the order transaction ('Unpaid', 'Pending Verification', 'Paid', 'Failed').</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at / updated_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 5: Order Details -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.5: <code>order_details</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Auto-incrementing sub-line transaction item.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">order_id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">FK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Binds parent invoice record in orders table; cascading.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">menu_item_id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">FK</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Binds chosen menu option references; preserves as null if menu item is deleted from system roster.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">quantity</td>
                                      <td class="p-1 border">INTEGER</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Number of ordered portions (capped under system buffer limits).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">subtotal</td>
                                      <td class="p-1 border">DECIMAL(10,2)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Line subtotal cost before order-level modifiers.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">special_instructions</td>
                                      <td class="p-1 border">TEXT</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Custom notes for dietary requirements or preferences.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at / updated_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 6: Payments -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.6: <code>payments</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Auto-incrementing payments identifier.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">order_id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">FK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Relational parent linking to orders; cascaded.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">payment_method</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Method selected by user (Cash, GCash).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">amount</td>
                                      <td class="p-1 border">DECIMAL(10,2)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Amount transacted in local currency PHP.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">payment_status</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Status of this payment ledger entity.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">reference_number</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Unique 13-digit code representing electronic money receipt reference.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">proof_image</td>
                                      <td class="p-1 border">VARCHAR(2048)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">File path pointing to the receipt proof PNG or JPG upload (restricted to 5MB).</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">verified_by</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">FK</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">References user ID of the Admin/Staff member who validated the receipt.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at / updated_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Standard Laravel resource tracking columns.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 7: Password Reset Tokens -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.7: <code>password_reset_tokens</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">email</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">User mailbox identifying token request.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">token</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Security signature hash validation sequence.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">created_at</td>
                                      <td class="p-1 border">TIMESTAMP</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Timestamp when the reset token request was initialized.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                      <!-- Table 8: Sessions -->
                      <div class="space-y-2 mt-4">
                          <p class="font-bold text-sm">Table 1.8: <code>sessions</code></p>
                          <table class="w-full text-xs text-left border border-slate-300">
                              <thead class="bg-slate-100">
                                  <tr>
                                      <th class="p-1.5 border">Field</th>
                                      <th class="p-1.5 border">Type</th>
                                      <th class="p-1.5 border">Key</th>
                                      <th class="p-1.5 border">Nullability</th>
                                      <th class="p-1.5 border">Description</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td class="p-1 border font-mono">id</td>
                                      <td class="p-1 border">VARCHAR(255)</td>
                                      <td class="p-1 border">PK</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Dynamic string identifier matching secure cookie values.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">user_id</td>
                                      <td class="p-1 border">BIGINT</td>
                                      <td class="p-1 border">Index</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Points to signed-in user's PK; blank for unauthenticated guests.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">ip_address</td>
                                      <td class="p-1 border">VARCHAR(45)</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Network source protocol address of browser socket connection.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">user_agent</td>
                                      <td class="p-1 border">TEXT</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-emerald-600">NULLABLE</td>
                                      <td class="p-1 border">Browser user-agent representation header.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">payload</td>
                                      <td class="p-1 border">LONGTEXT</td>
                                      <td class="p-1 border">-</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Serialized session key-value storage buffer block.</td>
                                  </tr>
                                  <tr>
                                      <td class="p-1 border font-mono">last_activity</td>
                                      <td class="p-1 border">INTEGER</td>
                                      <td class="p-1 border">Index</td>
                                      <td class="p-1 border text-red-600">NOT NULL</td>
                                      <td class="p-1 border">Unix epoch timestamp representing user's last interactive ping.</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>

             <!-- SECTION 9: IMPLEMENTATION AND TESTING -->
             <div class="space-y-4 pt-4 page-break-after">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">8. Implementation and Testing</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p><strong>8.1 Agile System Development Process</strong></p>
                     <p class="text-indent-paragraph">
                         The development structure of Clara’s Beast followed custom agile sprints. Phase 1 comprised scaffolding the relational migrations and user authorization sessions. Phase 2 engineered the product catalog grouping views and the AlpineJS cart container. Phase 3 introduced high-utility profile settings, profile picture upload routines, and manual file movement limits. In Phase 4, the kitchen status controller state triggers and GCash receipt uploading screen was hardened against validation failures.
                     </p>

                     <p><strong>8.2 Testing Procedures and Validation Logs</strong></p>
                     <p class="text-indent-paragraph">
                         High-fidelity boundary condition testing was executed thoroughly:
                     </p>

                     <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 print:bg-white print:border-slate-300">
                         <ul class="list-disc pl-6 space-y-2 text-xs">
                             <li><strong>Test Code TC-01 (Avatar Upload Limit Check):</strong> Selecting a profile image of 18 Megabytes triggered Laravel validation errors as intended, restricting server storage strain and prompting with active rules.</li>
                             <li><strong>Test Code TC-02 (Date validation checks):</strong> Picking a retro date or the current timestamp for scheduled pre-order checkout triggers an instantaneous AlpineJS client warning banner.</li>
                             <li><strong>Test Code TC-03 (State limits cancel test):</strong> Customer tries to cancel order while kitchen status is in "Preparing" state. The server halts process, throws an HTTP 403 / warning redirect, and preserves store records.</li>
                             <li><strong>Test Code TC-04 (Cart portion index cap):</strong> Inputting a negative shopping cart quantity or exceeding 100 portion units correctly defaults and prompts users to keep units inside standard safe bounds.</li>
                         </ul>
                     </div>
                 </div>
             </div>

             <!-- SECTION 10: CONCLUSION -->
             <div class="space-y-4 pt-4">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">9. Conclusion</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p class="text-indent-paragraph">
                         In retrospect, the System design project developed across the semester for <strong>Clara’s Beast: A Premium Food Ordering System</strong> successfully achieved all primary and specific system objectives. By creating digital customer profiles with secure avatar uploading, construction of a responsive React-like instant cart, formulation of a dynamic five-tier kitchen status tracking queue, and visual transaction checking logs, the system eliminates administrative delivery delays.
                     </p>
                     <p class="text-indent-paragraph">
                         The benefits of this application rest in transforming raw operational miscommunication. Kitchen technicians obtain real-time order lists instantly, customers monitor active bookings directly, and store owners balance payment receipts visually via GCash audit dashboards. Development of this program built rich engineering lessons: integrating AlpineJS state parameters with backend database schemas, executing file system move rules securely, and wrapping components cleanly so user interfaces look modern and polished.
                     </p>
                 </div>
             </div>

             <!-- SECTION 11: RECOMMENDATIONS -->
             <div class="space-y-4 pt-4 border-t border-slate-100">
                 <h2 class="text-lg font-bold uppercase border-b border-black pb-1">10. Recommendations</h2>

                 <div class="space-y-4 text-slate-800 print:text-black">
                     <p class="text-indent-paragraph">
                         While the current monolithic release of Clara's Beast operates robustly, future improvements can expand transactional security and customer joy across subsequent semesters:
                     </p>

                     <ul class="list-decimal pl-6 space-y-2">
                         <li><strong>E-Wallet API integration:</strong> Establish live webhook callbacks matching GCash or PayMaya sandboxes for automated ledger updates without manual verification delays.</li>
                         <li><strong>Dynamic SMS Gateway alerts:</strong> Hook systems into SMS delivery triggers (e.g. Twilio or Infobip) to alert customers on instant ready-to-collect statuses.</li>
                         <li><strong>GPS rider tracking:</strong> Introduce Google Maps Platform geolocation SDK vectors to display driver locations in real-time.</li>
                         <li><strong>AI Analytics Core:</strong> Add model recommendation layers to suggest seasonal recipe bundles, optimizing store sales.</li>
                     </ul>
                 </div>

                 <!-- Signature block for formal presentation -->
                 <div class="pt-16 grid grid-cols-2 gap-8 text-center text-xs font-bold leading-none print:pt-24 print:block print:space-y-10">
                     <div class="space-y-2 inline-block w-[2.5in]">
                         <div class="border-b border-black pb-1 w-full font-serif font-black text-sm" x-text="members"></div>
                         <p class="text-[10px] text-slate-500 uppercase tracking-widest font-sans">Project Author Submitter</p>
                     </div>
                     <div class="space-y-2 inline-block w-[2.5in] float-right">
                         <div class="border-b border-black pb-1 w-full font-serif font-black text-sm" x-text="instructor"></div>
                         <p class="text-[10px] text-slate-500 uppercase tracking-widest font-sans">Course Supervisor Reviewer</p>
                     </div>
                 </div>

             </div>

        </div>

    </div>

</div>

<!-- Stylesheet insertion for pristine print pagination -->
<style>
    @media print {
        /* Set page margins exactly as requested to 1 inch */
        @page {
            size: A4;
            margin: 1in;
        }
        /* Root container overrides to span paper width */
        #printable-paper-content {
            width: 100% !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            color: black !important;
            background: white !important;
        }
        body {
            background-color: white !important;
            color: black !important;
        }
        main {
            padding: 0 !important;
        }
        nav, footer, .print\:hidden {
            display: none !important;
        }
        .page-break-after {
            page-break-after: always;
            break-after: page;
        }
    }
    .text-indent-paragraph {
        text-indent: 0.5in;
    }
</style>
@endsection
