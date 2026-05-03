const STORAGE_KEY = "yashi-associates-db-v1";
const THEME_KEY = "yashi-associates-theme";
const SESSION_KEY = "yashi-associates-session";

const seedData = {
    users: [
        { id: 1, name: "Aarav Mehta", email: "admin@yashi.local", password: "password123", role: "admin", title: "Managing Director" },
        { id: 2, name: "Priya Kapoor", email: "owner@yashi.local", password: "password123", role: "owner", title: "Asset Owner" },
        { id: 3, name: "Rohan Verma", email: "team@yashi.local", password: "password123", role: "team", title: "Project Lead" },
        { id: 4, name: "Sneha Batra", email: "sales@yashi.local", password: "password123", role: "team", title: "Sales Strategist" }
    ],
    properties: [
        {
            id: 101,
            title: "Skyline Crest Residences",
            address: "Sector 45, Gurgaon",
            price: 32000000,
            status: "Available",
            phase: "construction",
            ownerId: 2,
            teamIds: [3, 4],
            description: "Luxury high-rise residences with panoramic terraces and concierge-grade amenities.",
            salesProgress: 62,
            imageLabel: "Tower View"
        },
        {
            id: 102,
            title: "Verdant Courtyard Villas",
            address: "Noida Extension, Noida",
            price: 21500000,
            status: "Reserved",
            phase: "planning",
            ownerId: 2,
            teamIds: [3],
            description: "Private villa enclave built around landscaped courtyards and low-density living.",
            salesProgress: 34,
            imageLabel: "Courtyard"
        },
        {
            id: 103,
            title: "Harborline Commercial Hub",
            address: "MG Road, Indore",
            price: 48000000,
            status: "Sold",
            phase: "sold",
            ownerId: 2,
            teamIds: [4],
            description: "Premium mixed-use commercial asset with strong occupancy and corporate tenant potential.",
            salesProgress: 100,
            imageLabel: "Sky Lobby"
        }
    ],
    teams: [
        { id: 201, name: "Rohan Verma", role: "Project Lead", responsibility: "Construction oversight", email: "team@yashi.local", performance: 91 },
        { id: 202, name: "Sneha Batra", role: "Sales Strategist", responsibility: "Sales and owner reporting", email: "sales@yashi.local", performance: 88 }
    ],
    phases: [
        { id: 301, propertyId: 101, name: "Planning", status: "completed", progress: 100 },
        { id: 302, propertyId: 101, name: "Construction", status: "active", progress: 68 },
        { id: 303, propertyId: 102, name: "Planning", status: "active", progress: 42 },
        { id: 304, propertyId: 103, name: "Completion", status: "completed", progress: 100 }
    ],
    issues: [
        { id: 401, propertyId: 101, raisedBy: 2, assignedTo: 3, title: "Facade lighting delay", severity: "high", status: "in-progress", solution: "Vendor replacement approved and installation rescheduled.", createdAt: "2026-05-01" },
        { id: 402, propertyId: 102, raisedBy: 2, assignedTo: 3, title: "Parking layout revision", severity: "medium", status: "open", solution: "Awaiting revised consultant drawings.", createdAt: "2026-04-28" },
        { id: 403, propertyId: 103, raisedBy: 4, assignedTo: 4, title: "Final registry follow-up", severity: "low", status: "resolved", solution: "Documentation delivered to buyer and archived.", createdAt: "2026-04-21" }
    ]
};

const state = {
    db: loadDatabase(),
    session: loadSession(),
    theme: loadTheme(),
    activePanel: "overview"
};

const panelConfig = {
    admin: [
        { id: "overview", label: "Overview", subtitle: "Portfolio insights, analytics, and live operational health." },
        { id: "properties", label: "Properties", subtitle: "Add, edit, archive, and monitor every real estate asset." },
        { id: "users", label: "Users", subtitle: "Control access across admins, owners, and support users." },
        { id: "teams", label: "Teams", subtitle: "Assign responsibilities, track roles, and monitor team performance." },
        { id: "phases", label: "Phases", subtitle: "Track progress from planning through sold status." },
        { id: "issues", label: "Issues", subtitle: "Capture owner and team tickets, then drive them to resolution." }
    ],
    owner: [
        { id: "overview", label: "Overview", subtitle: "Your property snapshot, issue status, and milestones." },
        { id: "properties", label: "My Properties", subtitle: "View the real-time progress of your owned properties." },
        { id: "issues", label: "Issues", subtitle: "Report problems and track active resolutions." }
    ],
    team: [
        { id: "overview", label: "Overview", subtitle: "Assigned work, active phases, and open issues." },
        { id: "teams", label: "Team Board", subtitle: "Collaborate across responsibilities and performance goals." },
        { id: "phases", label: "Phases", subtitle: "Update delivery progress and execution milestones." },
        { id: "issues", label: "Issues", subtitle: "Respond to reported concerns and close work cleanly." }
    ]
};

document.addEventListener("DOMContentLoaded", () => {
    applyTheme(state.theme);
    bindGlobalEvents();
    renderMarketing();
    renderDashboard();
});

function loadDatabase() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (!stored) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(seedData));
        return structuredClone(seedData);
    }

    return JSON.parse(stored);
}

function saveDatabase() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state.db));
    renderMarketing();
    renderDashboard();
}

function loadSession() {
    const raw = localStorage.getItem(SESSION_KEY);
    return raw ? JSON.parse(raw) : null;
}

function saveSession(session) {
    state.session = session;
    if (session) {
        localStorage.setItem(SESSION_KEY, JSON.stringify(session));
    } else {
        localStorage.removeItem(SESSION_KEY);
    }
    renderDashboard();
}

function loadTheme() {
    return localStorage.getItem(THEME_KEY) || "dark";
}

function applyTheme(theme) {
    state.theme = theme;
    document.body.dataset.theme = theme;
    localStorage.setItem(THEME_KEY, theme);
    const label = document.getElementById("themeLabel");
    if (label) label.textContent = theme === "dark" ? "Dark" : "Light";
}

function bindGlobalEvents() {
    document.getElementById("themeToggle").addEventListener("click", () => {
        applyTheme(state.theme === "dark" ? "light" : "dark");
    });

    document.getElementById("loginTrigger").addEventListener("click", openAuthModal);
    document.getElementById("closeAuthModal").addEventListener("click", closeAuthModal);
    document.getElementById("closeRecordModal").addEventListener("click", closeRecordModal);
    document.getElementById("logoutButton").addEventListener("click", () => {
        saveSession(null);
        showToast("Logged out successfully.");
    });

    document.getElementById("loginForm").addEventListener("submit", handleLogin);
    document.getElementById("authModal").addEventListener("click", (event) => {
        if (event.target.id === "authModal") closeAuthModal();
    });
    document.getElementById("recordModal").addEventListener("click", (event) => {
        if (event.target.id === "recordModal") closeRecordModal();
    });

    document.querySelectorAll("[data-scroll]").forEach((button) => {
        button.addEventListener("click", () => {
            const selector = button.dataset.scroll;
            const target = document.querySelector(selector);
            if (target) target.scrollIntoView({ behavior: "smooth" });
        });
    });
}

function renderMarketing() {
    const metrics = getOverviewStats();
    const heroMetrics = document.getElementById("heroMetrics");
    const heroTemplate = document.getElementById("statCardTemplate");

    heroMetrics.innerHTML = "";
    [
        { label: "Properties", value: metrics.propertyCount, note: "Across active and sold inventory" },
        { label: "Open Issues", value: metrics.openIssues, note: "Tracked in the problem resolution flow" },
        { label: "Team Strength", value: metrics.teamCount, note: "Cross-functional delivery coverage" }
    ].forEach((item) => {
        const node = heroTemplate.content.firstElementChild.cloneNode(true);
        node.querySelector(".stat-label").textContent = item.label;
        node.querySelector(".stat-value").textContent = item.value;
        node.querySelector(".stat-note").textContent = item.note;
        heroMetrics.appendChild(node);
    });

    document.getElementById("stagePropertyCount").textContent = metrics.propertyCount;
    document.getElementById("phaseHighlights").innerHTML = summarizePhases()
        .map((phase) => `<li>${phase.label}: <strong>${phase.value}</strong></li>`)
        .join("");

    const publicPropertyGrid = document.getElementById("publicPropertyGrid");
    publicPropertyGrid.innerHTML = state.db.properties.map((property) => `
        <article class="property-card">
            <span class="property-badge">${property.imageLabel}</span>
            <h3>${property.title}</h3>
            <p>${property.address}</p>
            <div class="property-meta">
                <strong>${formatCurrency(property.price)}</strong>
                <span>${property.status}</span>
            </div>
        </article>
    `).join("");
}

function renderDashboard() {
    const guestDashboard = document.getElementById("guestDashboard");
    const dashboardShell = document.getElementById("dashboardShell");
    const currentUser = getCurrentUser();

    if (!currentUser) {
        guestDashboard.classList.remove("hidden");
        dashboardShell.classList.add("hidden");
        return;
    }

    guestDashboard.classList.add("hidden");
    dashboardShell.classList.remove("hidden");

    document.getElementById("currentUserName").textContent = currentUser.name;
    document.getElementById("currentUserMeta").textContent = `${capitalize(currentUser.role)} • ${currentUser.title}`;

    const config = panelConfig[currentUser.role] || panelConfig.admin;
    if (!config.find((panel) => panel.id === state.activePanel)) {
        state.activePanel = config[0].id;
    }

    renderNav(config);
    renderPanel(currentUser);
}

function renderNav(config) {
    const nav = document.getElementById("dashboardNav");
    nav.innerHTML = "";

    config.forEach((panel) => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = state.activePanel === panel.id ? "active" : "";
        button.textContent = panel.label;
        button.addEventListener("click", () => {
            state.activePanel = panel.id;
            renderDashboard();
        });
        nav.appendChild(button);
    });
}

function renderPanel(currentUser) {
    const panel = (panelConfig[currentUser.role] || panelConfig.admin).find((item) => item.id === state.activePanel);
    document.getElementById("panelTitle").textContent = panel.label;
    document.getElementById("panelSubtitle").textContent = panel.subtitle;

    const actions = document.getElementById("panelActions");
    const content = document.getElementById("panelContent");
    actions.innerHTML = "";
    content.innerHTML = "";

    switch (state.activePanel) {
        case "overview":
            renderOverview(content, currentUser);
            break;
        case "properties":
            renderProperties(content, actions, currentUser);
            break;
        case "users":
            renderUsers(content, actions);
            break;
        case "teams":
            renderTeams(content, actions, currentUser);
            break;
        case "phases":
            renderPhases(content, actions, currentUser);
            break;
        case "issues":
            renderIssues(content, actions, currentUser);
            break;
        default:
            content.innerHTML = `<div class="empty-state">This panel is not available yet.</div>`;
    }
}

function renderOverview(container, user) {
    const stats = getOverviewStats(user);
    const summary = document.createElement("div");
    summary.className = "summary-grid";
    summary.innerHTML = [
        summaryCard("Total Properties", stats.propertyCount, "Live portfolio count"),
        summaryCard("Open Issues", stats.openIssues, "Operational issues requiring attention"),
        summaryCard("Completion Rate", `${stats.completionRate}%`, "Properties in completion or sold"),
        summaryCard("Sales Progress", `${stats.avgSalesProgress}%`, "Average progress across inventory")
    ].join("");

    const analytics = document.createElement("div");
    analytics.className = "table-card";
    analytics.innerHTML = `
        <div class="table-toolbar">
            <strong>Operational Analytics</strong>
            <span class="muted">Offline sample dashboard</span>
        </div>
        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Insight</th>
                        <th>Value</th>
                        <th>Meaning</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>High Severity Issues</td><td>${stats.highSeverityIssues}</td><td>Critical blockers that need fast attention.</td></tr>
                    <tr><td>Top Team Performance</td><td>${stats.topPerformance}%</td><td>Best scored team contributor in the system.</td></tr>
                    <tr><td>Owner Properties</td><td>${stats.ownerProperties}</td><td>Properties tied to the logged-in owner.</td></tr>
                    <tr><td>Active Phases</td><td>${stats.activePhases}</td><td>Phases currently in motion across the portfolio.</td></tr>
                </tbody>
            </table>
        </div>
    `;

    container.append(summary, analytics);
}

function renderProperties(container, actions, user) {
    if (user.role !== "owner") {
        actions.appendChild(createActionButton("Add Property", () => openRecordModal("property")));
    }

    const properties = filterPropertiesForUser(user);
    container.appendChild(createTableCard({
        title: user.role === "owner" ? "Owned Properties" : "Property Management",
        subtitle: `${properties.length} properties available`,
        columns: ["Title", "Address", "Price", "Status", "Phase", "Sales", "Actions"],
        rows: properties.map((property) => [
            property.title,
            property.address,
            formatCurrency(property.price),
            badge(property.status),
            phasePill(property.phase),
            `${property.salesProgress}%`,
            actionButtons([
                { label: "View", action: `viewProperty(${property.id})` },
                ...(user.role === "owner" ? [] : [{ label: "Edit", action: `editRecord('property', ${property.id})` }]),
                ...(user.role === "admin" ? [{ label: "Delete", action: `deleteRecord('property', ${property.id})`, danger: true }] : [])
            ])
        ])
    }));
}

function renderUsers(container, actions) {
    actions.appendChild(createActionButton("Add User", () => openRecordModal("user")));
    container.appendChild(createTableCard({
        title: "User Access",
        subtitle: `${state.db.users.length} system users`,
        columns: ["Name", "Email", "Role", "Title", "Actions"],
        rows: state.db.users.map((user) => [
            user.name,
            user.email,
            rolePill(user.role),
            user.title,
            actionButtons([
                { label: "Edit", action: `editRecord('user', ${user.id})` },
                { label: "Delete", action: `deleteRecord('user', ${user.id})`, danger: true }
            ])
        ])
    }));
}

function renderTeams(container, actions, user) {
    if (user.role === "admin") {
        actions.appendChild(createActionButton("Add Team Member", () => openRecordModal("team")));
    }

    const teams = user.role === "team"
        ? state.db.teams.filter((member) => member.email === user.email)
        : state.db.teams;

    const cards = document.createElement("div");
    cards.className = "card-grid";
    cards.innerHTML = teams.map((member) => `
        <article class="list-card">
            <span class="role-pill">${capitalize(member.role)}</span>
            <h3>${member.name}</h3>
            <p>${member.responsibility}</p>
            <strong>${member.performance}% performance</strong>
            ${user.role === "admin" ? `<div class="actions-row"><button class="secondary-button" onclick="editRecord('team', ${member.id})">Edit</button><button class="danger-button" onclick="deleteRecord('team', ${member.id})">Delete</button></div>` : ""}
        </article>
    `).join("");

    container.appendChild(cards);
}

function renderPhases(container, actions, user) {
    if (user.role !== "owner") {
        actions.appendChild(createActionButton("Add Phase", () => openRecordModal("phase")));
    }

    const phases = state.db.phases.filter((phase) => {
        if (user.role === "owner") {
            const property = findById("properties", phase.propertyId);
            return property && property.ownerId === user.id;
        }
        if (user.role === "team") {
            const property = findById("properties", phase.propertyId);
            return property && property.teamIds.includes(getTeamMemberIdByEmail(user.email));
        }
        return true;
    });

    container.appendChild(createTableCard({
        title: "Development Phases",
        subtitle: `${phases.length} tracked milestones`,
        columns: ["Property", "Phase", "Status", "Progress", "Actions"],
        rows: phases.map((phase) => [
            findById("properties", phase.propertyId)?.title || "Unknown",
            phase.name,
            phasePill(phase.status),
            `${phase.progress}%`,
            actionButtons([
                ...(user.role === "owner" ? [{ label: "View", action: `void(0)` }] : [{ label: "Edit", action: `editRecord('phase', ${phase.id})` }]),
                ...(user.role === "owner" ? [] : [{ label: "Delete", action: `deleteRecord('phase', ${phase.id})`, danger: true }])
            ])
        ])
    }));
}

function renderIssues(container, actions, user) {
    actions.appendChild(createActionButton(user.role === "owner" ? "Report Issue" : "New Ticket", () => openRecordModal("issue")));

    const issues = state.db.issues.filter((issue) => {
        const property = findById("properties", issue.propertyId);
        if (!property) return false;
        if (user.role === "owner") return issue.raisedBy === user.id || property.ownerId === user.id;
        if (user.role === "team") return issue.assignedTo === getTeamMemberIdByEmail(user.email);
        return true;
    });

    container.appendChild(createTableCard({
        title: "Problem / Solution Tracking",
        subtitle: `${issues.length} tickets in the system`,
        columns: ["Issue", "Property", "Severity", "Status", "Solution", "Actions"],
        rows: issues.map((issue) => [
            issue.title,
            findById("properties", issue.propertyId)?.title || "Unknown",
            badge(issue.severity),
            ticketPill(issue.status),
            issue.solution,
            actionButtons([
                { label: "Edit", action: `editRecord('issue', ${issue.id})` },
                { label: "Delete", action: `deleteRecord('issue', ${issue.id})`, danger: true }
            ])
        ])
    }));
}

function createTableCard({ title, subtitle, columns, rows }) {
    const wrapper = document.createElement("section");
    wrapper.className = "table-card";
    wrapper.innerHTML = `
        <div class="table-toolbar">
            <div>
                <strong>${title}</strong>
                <div class="muted">${subtitle}</div>
            </div>
        </div>
        <div class="table-shell">
            <table>
                <thead>
                    <tr>${columns.map((column) => `<th>${column}</th>`).join("")}</tr>
                </thead>
                <tbody>
                    ${rows.length ? rows.map((row) => `<tr>${row.map((value) => `<td>${value}</td>`).join("")}</tr>`).join("") : `<tr><td colspan="${columns.length}"><div class="empty-state">No records available.</div></td></tr>`}
                </tbody>
            </table>
        </div>
    `;

    return wrapper;
}

function createActionButton(label, callback) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "primary-button";
    button.textContent = label;
    button.addEventListener("click", callback);
    return button;
}

function summaryCard(label, value, note) {
    return `
        <article class="overview-card">
            <span class="muted">${label}</span>
            <strong>${value}</strong>
            <small class="muted">${note}</small>
        </article>
    `;
}

function actionButtons(actions) {
    return `
        <div class="actions-cell">
            ${actions.map((item) => `<button class="${item.danger ? "danger-button" : "secondary-button"}" type="button" onclick="${item.action}">${item.label}</button>`).join("")}
        </div>
    `;
}

function badge(value) {
    return `<span class="table-badge">${value}</span>`;
}

function phasePill(value) {
    return `<span class="phase-pill phase-${normalizeToken(value)}">${capitalize(value)}</span>`;
}

function ticketPill(value) {
    return `<span class="ticket-pill ticket-${normalizeToken(value)}">${capitalize(value)}</span>`;
}

function rolePill(value) {
    return `<span class="role-pill">${capitalize(value)}</span>`;
}

function openAuthModal() {
    document.getElementById("authModal").classList.remove("hidden");
}

function closeAuthModal() {
    document.getElementById("authModal").classList.add("hidden");
}

function handleLogin(event) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const user = state.db.users.find((item) =>
        item.email.toLowerCase() === String(form.get("email")).toLowerCase() &&
        item.password === form.get("password")
    );

    if (!user) {
        showToast("Invalid login. Try one of the seeded sample accounts.");
        return;
    }

    saveSession({ userId: user.id });
    state.activePanel = "overview";
    closeAuthModal();
    showToast(`Welcome back, ${user.name}.`);
    document.getElementById("dashboard").scrollIntoView({ behavior: "smooth" });
}

function getCurrentUser() {
    if (!state.session) return null;
    return state.db.users.find((user) => user.id === state.session.userId) || null;
}

function openRecordModal(type, id = null) {
    const modal = document.getElementById("recordModal");
    const title = document.getElementById("recordModalTitle");
    const form = document.getElementById("recordForm");
    const record = id ? findById(`${type}s`, id) : null;

    modal.classList.remove("hidden");
    title.textContent = `${record ? "Edit" : "Add"} ${capitalize(type)}`;
    form.innerHTML = buildForm(type, record);
    form.onsubmit = (event) => submitRecord(event, type, id);
}

function closeRecordModal() {
    document.getElementById("recordModal").classList.add("hidden");
}

function editRecord(type, id) {
    openRecordModal(type, id);
}

function submitRecord(event, type, id) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const payload = Object.fromEntries(form.entries());
    const collectionKey = `${type}s`;
    const collection = state.db[collectionKey];

    const normalized = normalizePayload(type, payload, id);

    if (id) {
        const index = collection.findIndex((item) => item.id === id);
        collection[index] = { ...collection[index], ...normalized };
        showToast(`${capitalize(type)} updated.`);
    } else {
        normalized.id = generateId(collection);
        collection.push(normalized);
        showToast(`${capitalize(type)} created.`);
    }

    saveDatabase();
    closeRecordModal();
}

function deleteRecord(type, id) {
    if (!window.confirm("Delete this record?")) return;

    const collectionKey = `${type}s`;
    state.db[collectionKey] = state.db[collectionKey].filter((item) => item.id !== id);
    saveDatabase();
    showToast(`${capitalize(type)} deleted.`);
}

function viewProperty(id) {
    const property = findById("properties", id);
    if (!property) return;

    showToast(`${property.title} • ${property.address} • ${formatCurrency(property.price)}`);
}

function buildForm(type, record) {
    switch (type) {
        case "property":
            return `
                <div class="form-grid">
                    ${field("Title", "title", record?.title)}
                    ${field("Address", "address", record?.address)}
                    ${field("Price", "price", record?.price, "number")}
                    ${selectField("Status", "status", ["Available", "Reserved", "Sold"], record?.status)}
                    ${selectField("Phase", "phase", ["planning", "construction", "completion", "sold"], record?.phase)}
                    ${field("Sales Progress (%)", "salesProgress", record?.salesProgress ?? 0, "number")}
                    ${selectField("Owner", "ownerId", getUsersByRole("owner").map((user) => ({ label: user.name, value: user.id })), record?.ownerId)}
                    ${field("Image Label", "imageLabel", record?.imageLabel)}
                </div>
                ${textareaField("Description", "description", record?.description)}
                <button class="primary-button full-width" type="submit">${record ? "Save Property" : "Create Property"}</button>
            `;
        case "user":
            return `
                <div class="form-grid">
                    ${field("Name", "name", record?.name)}
                    ${field("Email", "email", record?.email, "email")}
                    ${field("Password", "password", record?.password || "password123")}
                    ${selectField("Role", "role", ["admin", "owner", "team"], record?.role)}
                    ${field("Title", "title", record?.title)}
                </div>
                <button class="primary-button full-width" type="submit">${record ? "Save User" : "Create User"}</button>
            `;
        case "team":
            return `
                <div class="form-grid">
                    ${field("Name", "name", record?.name)}
                    ${field("Role", "role", record?.role)}
                    ${field("Responsibility", "responsibility", record?.responsibility)}
                    ${field("Email", "email", record?.email, "email")}
                    ${field("Performance", "performance", record?.performance ?? 80, "number")}
                </div>
                <button class="primary-button full-width" type="submit">${record ? "Save Team Member" : "Create Team Member"}</button>
            `;
        case "phase":
            return `
                <div class="form-grid">
                    ${selectField("Property", "propertyId", state.db.properties.map((property) => ({ label: property.title, value: property.id })), record?.propertyId)}
                    ${field("Phase Name", "name", record?.name)}
                    ${selectField("Status", "status", ["planning", "construction", "completion", "sold", "active", "completed"], record?.status)}
                    ${field("Progress (%)", "progress", record?.progress ?? 0, "number")}
                </div>
                <button class="primary-button full-width" type="submit">${record ? "Save Phase" : "Create Phase"}</button>
            `;
        case "issue":
            return `
                <div class="form-grid">
                    ${selectField("Property", "propertyId", state.db.properties.map((property) => ({ label: property.title, value: property.id })), record?.propertyId)}
                    ${field("Issue Title", "title", record?.title)}
                    ${selectField("Severity", "severity", ["low", "medium", "high"], record?.severity)}
                    ${selectField("Status", "status", ["open", "in-progress", "resolved", "closed"], record?.status)}
                    ${selectField("Assigned To", "assignedTo", state.db.teams.map((member) => ({ label: member.name, value: member.id })), record?.assignedTo)}
                </div>
                ${textareaField("Solution / Notes", "solution", record?.solution)}
                <button class="primary-button full-width" type="submit">${record ? "Save Issue" : "Create Issue"}</button>
            `;
        default:
            return `<div class="empty-state">Unsupported form.</div>`;
    }
}

function normalizePayload(type, payload, id) {
    const currentUser = getCurrentUser();

    switch (type) {
        case "property":
            return {
                title: payload.title,
                address: payload.address,
                price: Number(payload.price || 0),
                status: payload.status,
                phase: payload.phase,
                ownerId: Number(payload.ownerId),
                teamIds: id ? (findById("properties", id)?.teamIds || []) : [],
                description: payload.description,
                salesProgress: Number(payload.salesProgress || 0),
                imageLabel: payload.imageLabel || "Property View"
            };
        case "user":
            return {
                name: payload.name,
                email: payload.email,
                password: payload.password,
                role: payload.role,
                title: payload.title
            };
        case "team":
            return {
                name: payload.name,
                role: payload.role,
                responsibility: payload.responsibility,
                email: payload.email,
                performance: Number(payload.performance || 0)
            };
        case "phase":
            return {
                propertyId: Number(payload.propertyId),
                name: payload.name,
                status: payload.status,
                progress: Number(payload.progress || 0)
            };
        case "issue":
            return {
                propertyId: Number(payload.propertyId),
                raisedBy: id ? findById("issues", id)?.raisedBy || currentUser.id : currentUser.id,
                assignedTo: Number(payload.assignedTo),
                title: payload.title,
                severity: payload.severity,
                status: payload.status,
                solution: payload.solution,
                createdAt: id ? findById("issues", id)?.createdAt || todayIso() : todayIso()
            };
        default:
            return payload;
    }
}

function field(label, name, value = "", type = "text") {
    return `
        <label>
            <span>${label}</span>
            <input type="${type}" name="${name}" value="${escapeAttr(value ?? "")}" required>
        </label>
    `;
}

function textareaField(label, name, value = "") {
    return `
        <label>
            <span>${label}</span>
            <textarea name="${name}" required>${escapeHtml(value ?? "")}</textarea>
        </label>
    `;
}

function selectField(label, name, options, selected) {
    const normalized = options.map((option) => typeof option === "string" ? { label: capitalize(option), value: option } : option);
    return `
        <label>
            <span>${label}</span>
            <select name="${name}" required>
                ${normalized.map((option) => `<option value="${option.value}" ${String(option.value) === String(selected) ? "selected" : ""}>${option.label}</option>`).join("")}
            </select>
        </label>
    `;
}

function getOverviewStats(user = null) {
    const properties = user ? filterPropertiesForUser(user) : state.db.properties;
    const issues = user
        ? state.db.issues.filter((issue) => properties.some((property) => property.id === issue.propertyId))
        : state.db.issues;

    const completionRate = properties.length
        ? Math.round((properties.filter((property) => ["completion", "sold"].includes(property.phase)).length / properties.length) * 100)
        : 0;

    return {
        propertyCount: properties.length,
        openIssues: issues.filter((issue) => ["open", "in-progress"].includes(issue.status)).length,
        highSeverityIssues: issues.filter((issue) => issue.severity === "high").length,
        teamCount: state.db.teams.length,
        completionRate,
        avgSalesProgress: properties.length ? Math.round(properties.reduce((sum, property) => sum + property.salesProgress, 0) / properties.length) : 0,
        topPerformance: Math.max(...state.db.teams.map((member) => member.performance), 0),
        ownerProperties: user?.role === "owner" ? properties.length : state.db.properties.filter((property) => property.ownerId === 2).length,
        activePhases: state.db.phases.filter((phase) => ["active", "construction", "planning"].includes(phase.status)).length
    };
}

function filterPropertiesForUser(user) {
    if (user.role === "owner") {
        return state.db.properties.filter((property) => property.ownerId === user.id);
    }

    if (user.role === "team") {
        const teamId = getTeamMemberIdByEmail(user.email);
        return state.db.properties.filter((property) => property.teamIds.includes(teamId));
    }

    return state.db.properties;
}

function summarizePhases() {
    return [
        { label: "Planning", value: state.db.properties.filter((property) => property.phase === "planning").length },
        { label: "Construction", value: state.db.properties.filter((property) => property.phase === "construction").length },
        { label: "Completion", value: state.db.properties.filter((property) => property.phase === "completion").length },
        { label: "Sold", value: state.db.properties.filter((property) => property.phase === "sold").length }
    ];
}

function findById(collectionKey, id) {
    return state.db[collectionKey].find((item) => item.id === id) || null;
}

function generateId(collection) {
    return Math.max(0, ...collection.map((item) => item.id)) + 1;
}

function getUsersByRole(role) {
    return state.db.users.filter((user) => user.role === role);
}

function getTeamMemberIdByEmail(email) {
    return state.db.teams.find((member) => member.email === email)?.id || 0;
}

function showToast(message) {
    let stack = document.querySelector(".toast-stack");
    if (!stack) {
        stack = document.createElement("div");
        stack.className = "toast-stack";
        document.body.appendChild(stack);
    }

    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;
    stack.appendChild(toast);
    setTimeout(() => toast.remove(), 3200);
}

function formatCurrency(value) {
    return new Intl.NumberFormat("en-IN", {
        style: "currency",
        currency: "INR",
        maximumFractionDigits: 0
    }).format(value);
}

function capitalize(value) {
    return String(value)
        .replace(/-/g, " ")
        .replace(/\b\w/g, (char) => char.toUpperCase());
}

function normalizeToken(value) {
    return String(value).toLowerCase().replace(/\s+/g, "-");
}

function todayIso() {
    return new Date().toISOString().slice(0, 10);
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;");
}

function escapeAttr(value) {
    return escapeHtml(value);
}

window.openRecordModal = openRecordModal;
window.editRecord = editRecord;
window.deleteRecord = deleteRecord;
window.viewProperty = viewProperty;
