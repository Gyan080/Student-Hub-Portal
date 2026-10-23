# Site Map

```text
Student Hub Portal
├── Public Pages
│   ├── index.html                         Home
│   ├── about.html                         About
│   ├── faqs.html                          FAQs
│   └── contact.html                       Contact
│
├── Authentication and Registration
│   ├── Login.html                         Login and registration choices
│   ├── Student/Stud_login.html            Student login
│   ├── Admin/Admin_login.html             Admin login
│   ├── Student/regis_stud.html            Student registration
│   └── Admin/regis_admin.html             Admin registration
│
├── Student Area
│   ├── Student/DashBoard/
│   │   ├── student_dashboard.php          Student dashboard (session-backed)
│   │   └── student_dashboard.html         Student dashboard (static)
│   ├── Student/Profile/profile.html       Student profile
│   ├── Student/Event/event.html           Campus events and registration
│   ├── Student/Exam_Result/exam_result.html Exam results
│   └── logout.php                         Logout
│
├── Admin Area
│   ├── Admin/DashBoard/
│   │   ├── admin_dashboard.php            Admin dashboard (session-backed)
│   │   ├── admin_dashboard.html           Admin dashboard (static)
│   │   ├── students_manage.php            Manage students
│   │   │   ├── student_add.php            Add a student
│   │   │   ├── student_edit.php           Edit a student
│   │   │   └── student_delete.php         Delete a student
│   │   ├── events_list.php                Manage events
│   │   │   ├── event_form.php             Add or edit an event
│   │   │   ├── event_delete.php           Delete an event
│   │   │   └── events_ajax.php            Event data route
│   │   ├── admins_manage.php              Manage administrators (super-admin)
│   │   └── audit_log.php                  Audit log (super-admin)
│
└── Server-side Routes and Helpers
    ├── Student/
    │   ├── Stud_login.php                 Student login handler
    │   ├── student_registration.php       Student registration handler
    │   └── login_error.php                Student login error
    ├── Admin/
    │   ├── Admin_login.php                Admin login handler
    │   └── save_admin.php                 Admin registration handler
    ├── Api/
    │   ├── events.php                     Events API
    │   ├── student_events.php             Student events and registration API
    │   └── db.php                         API database connection
    └── Admin/DashBoard/
        ├── auth_check.php                 Admin authentication helper
        └── audit_helper.php               Audit logging helper
```

Calendar, attendance, fee information, feedback, and a custom 404 page are not currently implemented.
