## Site Map

Student Portal (Site Structure)
│                   <!-- navigation to about, faq, contact, login -->
├── index.html                      (Home Page - Public)<!--DONE-->
│
├── About Page                      (About Page - Public)<!--DONE-->
│       ├── Campus                  (Campus overview - everyone)<!--DONE-->
│       └── Academics               (Placement data, Offered Courses)<!--DONE-->
│
├── FAQ Page                        (FAQ Page - Public)<!--DONE-->
│
├── Contact Page                    (For Every User - Public)<!--DONE-->
│
│               <!-- Registration for new user -->
│
├── Register Page (if new user)     (Registration Choice - Public)<!--DONE-->
│   ├── Student Registration Page   (Student Registration - Public)<!--DONE-->
│   └── Admin Registration Page     (Admin Registration - Public)<!--DONE-->
│
│               <!-- Login Page of Student -->
│
├── Login Page                            (User Login - Public)<!--DONE-->
│   ├── Student Login Page                (Student Login - Student)
│   │   └── Student Dashboard Page        (Student Dashboard - Student)
│   │       ├── Profile Page              (Profile Management (Editable) - Student)
│   │       ├── Events Page               (Event Directory - Student)
│   │       ├── Calendar                  (For Students to schedule events)
│   │       ├── Attendance Tracker        (View attendance records - Student)
│   │       ├── Fee Information           (View fee status, dues, payment history - Student)
│   │       ├── Exam Result               (View exam results/marksheets - Student)
│   │       └── Logout
│   │
│   └── Admin Login Page                  (Admin Login - Admin)<!--DONE-->
│       └── Admin Dashboard Page          (Admin Dashboard - Admin)
│           ├── Manage Events             (Create, edit, delete, and track all events - Admin)
│           ├── Manage Students           (View, update, and manage student data - Admin)
│           ├── Attendance Overview       (Monitor student attendance - Admin)
│           └── Logout
│
├── Feedback Page                         (Feedback can be given by public - everyone)
│
└── 404 Page                              (Error - Public)