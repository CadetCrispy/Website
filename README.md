Objective: Develop a high-performance, mobile-first, and secure website for Gro Bros, a company offering free and premium cannabis cultivation lessons. The site must be user-friendly, accessible, and scalable with a modular codebase. Track all changes with timestamps and rationales for traceability.

Instructions:

Use any IDE or development tools to implement the project.
Log every code change with a timestamp and rationale in comments (e.g., [2025-06-08 12:52 AM] Added CSRF token for security).
Create milestones (e.g., “User Authentication Complete”) to mark feature completion.
Follow coding standards below for naming, structure, and styling.
📦 Tech Stack
Backend: PHP 8.3+ (secure, modular logic)
Database: MySQL 8.0+ (users, lessons, forum, progress)
Frontend Styling: CSS (BEM, custom properties, mobile-first)
Frontend Interactivity: Vanilla JavaScript (ES6+, no frameworks)
Tools: Git (version control)
🎯 Core Features
Public Pages:
Home (index.php): Showcase featured lessons, value proposition, signup/login CTAs.
About (about.php): Company mission, team overview, contact info.
Contact (contact.php): Form (name, email, message) with CSRF protection and email validation.
User System:
Authentication (auth.php, login.php, signup.php, logout.php): Session-based login (PHP sessions, bcrypt passwords), roles (regular, premium, admin).
Profile (dashboard.php): Display username, join date, progress; allow password updates.
Lesson System:
Free Lessons (course.php): Accessible to all users, track progress (MySQL user_lessons table, progress bars in lesson-card.php).
Premium Lessons: Locked for non-premium users, include paywall stub (e.g., Stripe placeholder).
Progress Tracking: Store completion percentage, display visually.
Admin Dashboard (manage-lessons.php):
Lesson CRUD: Create, read, update, delete lessons with form validation.
Access Control: Restrict to admins via auth.php middleware.
Analytics: Display total users and lesson completions (table or chart).
File Upload System:
Formats: PDFs, MP4 videos, images in /uploads/lesson-assets.
Security: Validate file types, limit size (50MB), serve via PHP for access control.
Forum (/forum):
Threads (index.php, create-thread.php): Users create threads with titles and posts.
Posts & Comments (thread.php): Hierarchical (posts in threads, comments on posts).
Engagement: Upvote/downvote system (MySQL storage).
Profiles: Show user activity (threads, posts).
Moderation: Admin delete functionality, user “report” button.
Responsive Design:
Support mobile (320px+), tablet (768px+), desktop (1024px+).
Mobile-first with min-width media queries in styles.css.
Accessible navigation (hamburger menu on mobile, keyboard support).
Security:
Use PDO prepared statements for MySQL queries.
Add CSRF tokens to all POST forms.
Sanitize/validate inputs (e.g., filter_var($email, FILTER_VALIDATE_EMAIL)).
Enforce HTTPS in production.
Secure session handling (regenerate session IDs on login).
📁 File Structure
text

Collapse

Wrap

Copy
/grobros
├── /admin
│   └── manage-lessons.php          # Admin dashboard for lesson CRUD
├── /assets
│   ├── /css
│   │   └── styles.css             # Global BEM styles
│   ├── /js
│   │   └── main.js                # Vanilla JS interactivity
│   └── /images                    # Logos, icons
├── /forum
│   ├── index.php                  # Forum thread list
│   ├── thread.php                 # Single thread view
│   └── create-thread.php          # Thread creation form
├── /includes
│   ├── config.php                 # Database credentials
│   ├── db.php                     # PDO database connection
│   ├── auth.php                   # Authentication logic
│   ├── header.php                 # Common header
│   └── footer.php                 # Common footer
├── /templates
│   └── lesson-card.php            # Reusable lesson card
├── /uploads
│   └── /lesson-assets             # Secure lesson files
├── about.php                      # About page
├── contact.php                    # Contact form
├── index.php                      # Homepage
├── login.php                      # Login form
├── logout.php                     # Logout handler
├── signup.php                     # Registration form
├── course.php                     # Lesson view
├── dashboard.php                  # User dashboard
└── README.md                      # Documentation
🔄 Development Workflow
Setup:
Create the file structure manually or via scripts.
Initialize Git repository and commit initial structure.
Set up db.php with PDO and config.php with credentials.
Track Changes:
Add comments for every change:
php

Collapse

Wrap

Copy
// [2025-06-08 12:52 AM] Added CSRF token validation to contact form.
Commit changes with descriptive messages (e.g., “Implemented user login”).
Milestones:
Mark feature completion (e.g., “Authentication Complete”) in commit messages or documentation.
Test each milestone before proceeding.
Incremental Development:
Build public pages first, then authentication, lessons, forum, admin dashboard, and uploads.
Test responsiveness and security at each stage.
Testing:
Write PHPUnit tests for PHP logic (e.g., auth.php login).
Manually test UI components (e.g., progress bars) on mobile/desktop.
Verify accessibility (keyboard navigation, screen readers).
Performance:
Optimize MySQL queries (e.g., add indexes for lesson retrieval).
Minimize JavaScript DOM manipulations (e.g., debounce form submissions).
Compress images and stream videos efficiently.
📜 Coding Standards
Naming & Structure:

Use camelCase for JavaScript/PHP variables (e.g., userId, lessonTitle).
Use PascalCase for PHP classes (e.g., LessonManager, UserAuth).
Use snake_case for filenames/folders (e.g., manage_lessons.php, lesson_assets).
Keep code modular: logic in /includes, views in /templates, assets in /assets.
Code Quality:

Add PHPDoc comments for PHP functions:
php

Collapse

Wrap

Copy
/**
 * Validates user login credentials.
 * @param string $email User email.
 * @param string $password User password.
 * @return bool True if valid, false otherwise.
 */
Separate logic from views (e.g., lesson-card.php for UI, course.php for data).
Do not modify config.php or db.php except for credentials.
Write reusable functions in auth.php (e.g., restrictToAdmin()).
CSS Styling:

Use BEM naming (e.g., .lesson-card__title, .btn--primary).
Define color palette in :root:
css

Collapse

Wrap

Copy
:root {
  --primary: #2E7D32;
  --secondary: #4CAF50;
  --text: #333;
  --background: #F9FAFB;
}
Use rem units for spacing/font sizes (e.g., padding: 1rem).
Use CSS Grid/Flexbox; avoid floats.
No inline styles; use styles.css.
Add :focus and :hover states for accessibility (e.g., outline: 2px solid var(--primary)).
Mobile-first with min-width media queries:
css

Collapse

Wrap

Copy
.container {
  padding: 1rem;
}
@media (min-width: 768px) {
  .container {
    padding: 2rem;
  }
}
Define global buttons:
css

Collapse

Wrap

Copy
.btn { padding: 0.75rem 1.5rem; }
.btn--primary { background: var(--primary); color: white; }
.btn--danger { background: #EF4444; color: white; }
JavaScript:

Use ES6+ (e.g., const, let, arrow functions).
Avoid external libraries; use vanilla JavaScript.
Comment complex logic (e.g., AJAX in main.js).
Security:

Use PDO prepared statements for MySQL.
Add CSRF tokens to POST forms.
Validate/sanitize inputs (e.g., filter_var($email, FILTER_VALIDATE_EMAIL)).
Restrict uploads to PDFs/MP4s/images, 50MB max.
Actionable Steps
Setup:
Create file structure and initialize Git.
Configure db.php with PDO and config.php with credentials.
Write boilerplate for index.php, styles.css, main.js.
Build Incrementally:
Public Pages: Implement index.php, about.php, contact.php with responsive layouts and CSRF-protected form.
Authentication: Build auth.php, login.php, signup.php, logout.php with bcrypt and sessions.
Lessons: Develop course.php and lesson-card.php with progress tracking and MySQL.
Forum: Create /forum/index.php, thread.php, create-thread.php with thread/post hierarchy and upvotes.
Admin: Build manage-lessons.php with CRUD and admin-only access.
Uploads: Add secure file upload handling in manage-lessons.php.
Track & Test:
Log changes in comments: [2025-06-08 12:52 AM] Implemented feature X.
Commit with descriptive messages.
Write PHPUnit tests for PHP logic.
Test responsiveness and accessibility.
Optimize:
Optimize MySQL queries (e.g., add indexes).
Debounce JavaScript form submissions.
Compress images and stream videos.
Sample Development Tasks
Homepage: “Create index.php with a hero section, featured lessons, and signup CTA.”
Authentication: “Implement session-based login in auth.php and login.php with bcrypt.”
Forum: “Build /forum/index.php to list threads and thread.php for posts/comments.”
Styling: “Write styles.css with BEM classes and mobile-first layout for lesson cards.”
Security: “Add CSRF tokens to all forms in contact.php and /forum.”
Notes
Start Small: Build public pages first to establish the design system.
Security First: Implement CSRF and input validation early.
Review Code: Ensure correctness, security, and adherence to standards.
Backup: Commit to Git regularly to preserve history.