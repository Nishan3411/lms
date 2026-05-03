<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TeacherAssignmentController;
use App\Http\Controllers\Admin\CurriculumManagementController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\ResultReportController;
use App\Http\Controllers\Admin\TimetableController as AdminTimetableController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\Teacher\LearningMaterialController as TeacherLearningMaterialController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentWorkflowController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\TimetableController as TeacherTimetableController;
use App\Http\Controllers\Teacher\AnnouncementController as TeacherAnnouncementController;
use App\Http\Controllers\Teacher\LeaveRequestController as TeacherLeaveRequestController;
use App\Http\Controllers\Student\LearningMaterialController as StudentLearningMaterialController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\ResultController as StudentResultController;
use App\Http\Controllers\Student\TimetableController as StudentTimetableController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\LeaveRequestController as StudentLeaveRequestController;
use App\Http\Controllers\Student\FeeController as StudentFeeController;
use App\Http\Controllers\Student\ReportController as StudentReportController;
use App\Http\Controllers\Parent\FeeController as ParentFeeController;
use App\Http\Controllers\Parent\ResultController as ParentResultController;
use App\Http\Controllers\Parent\TimetableController as ParentTimetableController;
use App\Http\Controllers\Parent\AnnouncementController as ParentAnnouncementController;
use App\Http\Controllers\Parent\LeaveRequestController as ParentLeaveRequestController;
use App\Http\Controllers\Parent\StudentReportController as ParentStudentReportController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Parent\AttendanceController as ParentAttendanceController;
use App\Http\Controllers\Teacher\StudentReportController as TeacherStudentReportController;
use App\Http\Controllers\Payments\RazorpayPaymentController;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;

Route::get('/', function () {

    if (!Auth::check()) {
        return view('welcome');
    }

    return match (Auth::user()->role) {
        'admin'   => redirect('/admin/dashboard'),
        'teacher' => redirect('/teacher/dashboard'),
        'student' => redirect('/student/dashboard'),
        'parent'  => redirect('/parent/dashboard'),
        default   => view('welcome'),
    };
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/password', 'pages::settings.password')->name('user-password.edit');
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/two-factor', 'pages::settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

/*
|--------------------------------------------------------------------------
| Role Based Dashboards
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/curriculum', [CurriculumManagementController::class, 'index'])
            ->name('admin.curriculum');

        Route::get('/enrollment', [EnrollmentController::class, 'index'])
            ->name('admin.enrollment');

        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('admin.users');

        Route::get('/fees', [FeeController::class, 'index'])
            ->name('admin.fees.index');

        Route::get('/fees/export', [FeeController::class, 'export'])
            ->name('admin.fees.export');

        Route::get('/fees/invoices/{studentFee}', [FeeController::class, 'invoice'])
            ->name('admin.fees.invoices.show');

        Route::get('/fees/payments/{payment}/receipt', [FeeController::class, 'receipt'])
            ->name('admin.fees.payments.receipt');

        Route::get('/fees/students/{user}', [FeeController::class, 'showStudentFees'])
            ->name('admin.fees.students.show');

        Route::get('/attendance-report', [AttendanceReportController::class, 'index'])
            ->name('admin.attendance.index');

        Route::get('/attendance-report/export', [AttendanceReportController::class, 'export'])
            ->name('admin.attendance.export');

        Route::get('/results', [ResultReportController::class, 'index'])
            ->name('admin.results.index');

        Route::get('/timetable', [AdminTimetableController::class, 'index'])
            ->name('admin.timetable.index');

        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])
            ->name('admin.announcements.index');

        Route::get('/leave-requests', [AdminLeaveRequestController::class, 'index'])
            ->name('admin.leave-requests.index');

        Route::post('/curriculum/class-rooms', [CurriculumManagementController::class, 'storeClassRoom'])
            ->name('admin.class-rooms.store');

        Route::post('/users', [UserManagementController::class, 'store'])
            ->name('admin.users.store');

        Route::post('/fees', [FeeController::class, 'createFee'])
            ->name('admin.fees.store');

        Route::post('/fees/assign', [FeeController::class, 'assignFeeToClass'])
            ->name('admin.fees.assign');

        Route::post('/fees/payments', [FeeController::class, 'recordPayment'])
            ->name('admin.fees.payments.store');

        Route::post('/timetable', [AdminTimetableController::class, 'store'])
            ->name('admin.timetable.store');

        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])
            ->name('admin.announcements.store');

        Route::patch('/leave-requests/{leaveRequest}', [AdminLeaveRequestController::class, 'update'])
            ->name('admin.leave-requests.update');

        Route::patch('/curriculum/class-rooms/{classRoom}', [CurriculumManagementController::class, 'updateClassRoom'])
            ->name('admin.class-rooms.update');

        Route::delete('/curriculum/class-rooms/{classRoom}', [CurriculumManagementController::class, 'destroyClassRoom'])
            ->name('admin.class-rooms.destroy');

        Route::post('/curriculum/subjects', [CurriculumManagementController::class, 'storeSubject'])
            ->name('admin.subjects.store');

        Route::patch('/curriculum/subjects/{subject}', [CurriculumManagementController::class, 'updateSubject'])
            ->name('admin.subjects.update');

        Route::delete('/curriculum/subjects/{subject}', [CurriculumManagementController::class, 'destroySubject'])
            ->name('admin.subjects.destroy');

        Route::post('/curriculum/topics', [CurriculumManagementController::class, 'storeTopic'])
            ->name('admin.topics.store');

        Route::post('/enroll-student', [EnrollmentController::class, 'assignStudentToClass'])
            ->name('admin.enroll-student');

        Route::delete('/enroll-student', [EnrollmentController::class, 'removeStudentFromClass'])
            ->name('admin.enroll-student.destroy');

        Route::post('/link-parent', [EnrollmentController::class, 'assignParentToStudent'])
            ->name('admin.link-parent');

        Route::delete('/link-parent', [EnrollmentController::class, 'unlinkParentFromStudent'])
            ->name('admin.link-parent.destroy');

        Route::patch('/curriculum/topics/{topic}', [CurriculumManagementController::class, 'updateTopic'])
            ->name('admin.topics.update');

        Route::patch('/users/{user}', [UserManagementController::class, 'update'])
            ->name('admin.users.update');

        Route::delete('/curriculum/topics/{topic}', [CurriculumManagementController::class, 'destroyTopic'])
            ->name('admin.topics.destroy');

        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::delete('/timetable/{scheduleEntry}', [AdminTimetableController::class, 'destroy'])
            ->name('admin.timetable.destroy');

        Route::get('/assign-teacher', [TeacherAssignmentController::class, 'index'])
            ->name('admin.assign-teacher');

        Route::post('/assign-teacher', [TeacherAssignmentController::class, 'store'])
            ->name('admin.assign-teacher.store');

        Route::delete('/assign-teacher', [TeacherAssignmentController::class, 'destroy'])
            ->name('admin.assign-teacher.destroy');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])
        ->name('teacher.dashboard');

    Route::get('/teacher/materials', [TeacherLearningMaterialController::class, 'index'])
        ->name('teacher.materials');

    Route::get('/teacher/assignments', [TeacherAssignmentWorkflowController::class, 'index'])
        ->name('teacher.assignments.index');

    Route::get('/teacher/exams', [TeacherExamController::class, 'index'])
        ->name('teacher.exams.index');

    Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])
        ->name('teacher.timetable.index');

    Route::get('/teacher/announcements', [TeacherAnnouncementController::class, 'index'])
        ->name('teacher.announcements.index');

    Route::get('/teacher/leave-requests', [TeacherLeaveRequestController::class, 'index'])
        ->name('teacher.leave-requests.index');

    Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'index'])
        ->name('teacher.attendance.index');

    Route::get('/teacher/attendance/mark', [TeacherAttendanceController::class, 'markAttendance'])
        ->name('teacher.attendance.mark');

    Route::get('/teacher/student-reports', [TeacherStudentReportController::class, 'index'])
        ->name('teacher.student-reports.index');

    Route::post('/teacher/materials', [TeacherLearningMaterialController::class, 'store'])
        ->name('teacher.materials.store');

    Route::post('/teacher/assignments', [TeacherAssignmentWorkflowController::class, 'store'])
        ->name('teacher.assignments.store');

    Route::post('/teacher/exams', [TeacherExamController::class, 'store'])
        ->name('teacher.exams.store');

    Route::post('/teacher/announcements', [TeacherAnnouncementController::class, 'store'])
        ->name('teacher.announcements.store');

    Route::post('/teacher/attendance', [TeacherAttendanceController::class, 'storeAttendance'])
        ->name('teacher.attendance.store');

    Route::post('/teacher/exams/{exam}/results', [TeacherExamController::class, 'storeResults'])
        ->name('teacher.exams.results.store');

    Route::patch('/teacher/assignment-submissions/{assignmentSubmission}', [TeacherAssignmentWorkflowController::class, 'grade'])
        ->name('teacher.assignment-submissions.grade');

    Route::patch('/teacher/exams/{exam}/publish', [TeacherExamController::class, 'publish'])
        ->name('teacher.exams.publish');

    Route::patch('/teacher/leave-requests/{leaveRequest}', [TeacherLeaveRequestController::class, 'update'])
        ->name('teacher.leave-requests.update');

    Route::get('/teacher/assignments/{assignment}/download', [TeacherAssignmentWorkflowController::class, 'downloadAttachment'])
        ->name('teacher.assignments.download');

    Route::get('/teacher/assignment-submissions/{assignmentSubmission}/download', [TeacherAssignmentWorkflowController::class, 'downloadSubmission'])
        ->name('teacher.assignment-submissions.download');

    Route::delete('/teacher/materials/{learningMaterial}', [TeacherLearningMaterialController::class, 'destroy'])
        ->name('teacher.materials.destroy');

    Route::delete('/teacher/assignments/{assignment}', [TeacherAssignmentWorkflowController::class, 'destroy'])
        ->name('teacher.assignments.destroy');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    Route::get('/student/materials', [StudentLearningMaterialController::class, 'index'])
        ->name('student.materials');

    Route::get('/student/assignments', [StudentAssignmentController::class, 'index'])
        ->name('student.assignments.index');

    Route::get('/student/fees', [StudentFeeController::class, 'index'])
        ->name('student.fees.index');

    Route::get('/student/fees/invoices/{studentFee}', [StudentFeeController::class, 'invoice'])
        ->name('student.fees.invoices.show');

    Route::get('/student/fees/payments/{payment}/receipt', [StudentFeeController::class, 'receipt'])
        ->name('student.fees.payments.receipt');

    Route::post('/student/fees/{studentFee}/razorpay/order', [RazorpayPaymentController::class, 'createStudentOrder'])
        ->name('student.fees.razorpay.order');

    Route::post('/student/fees/razorpay/{paymentAttempt}/verify', [RazorpayPaymentController::class, 'verifyStudentOrder'])
        ->name('student.fees.razorpay.verify');

    Route::get('/student/results', [StudentResultController::class, 'index'])
        ->name('student.results.index');

    Route::get('/student/timetable', [StudentTimetableController::class, 'index'])
        ->name('student.timetable.index');

    Route::get('/student/announcements', [StudentAnnouncementController::class, 'index'])
        ->name('student.announcements.index');

    Route::get('/student/leave-requests', [StudentLeaveRequestController::class, 'index'])
        ->name('student.leave-requests.index');

    Route::get('/student/attendance', [StudentAttendanceController::class, 'index'])
        ->name('student.attendance.index');

    Route::get('/student/report', StudentReportController::class)
        ->name('student.report.show');

    Route::get('/student/materials/{learningMaterial}/download', [StudentLearningMaterialController::class, 'download'])
        ->name('student.materials.download');

    Route::post('/student/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])
        ->name('student.assignments.submit');

    Route::post('/student/leave-requests', [StudentLeaveRequestController::class, 'store'])
        ->name('student.leave-requests.store');

    Route::get('/student/assignments/{assignment}/download', [StudentAssignmentController::class, 'downloadAttachment'])
        ->name('student.assignments.download');

    Route::get('/student/assignment-submissions/{assignmentSubmission}/download', [StudentAssignmentController::class, 'downloadSubmission'])
        ->name('student.assignment-submissions.download');
});

Route::middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/parent/dashboard', [ParentDashboardController::class, 'index'])
        ->name('parent.dashboard');

    Route::get('/parent/fees', [ParentFeeController::class, 'index'])
        ->name('parent.fees.index');

    Route::get('/parent/fees/invoices/{studentFee}', [ParentFeeController::class, 'invoice'])
        ->name('parent.fees.invoices.show');

    Route::get('/parent/fees/payments/{payment}/receipt', [ParentFeeController::class, 'receipt'])
        ->name('parent.fees.payments.receipt');

    Route::post('/parent/fees/{studentFee}/razorpay/order', [RazorpayPaymentController::class, 'createParentOrder'])
        ->name('parent.fees.razorpay.order');

    Route::post('/parent/fees/razorpay/{paymentAttempt}/verify', [RazorpayPaymentController::class, 'verifyParentOrder'])
        ->name('parent.fees.razorpay.verify');

    Route::get('/parent/results', [ParentResultController::class, 'index'])
        ->name('parent.results.index');

    Route::get('/parent/timetable', [ParentTimetableController::class, 'index'])
        ->name('parent.timetable.index');

    Route::get('/parent/announcements', [ParentAnnouncementController::class, 'index'])
        ->name('parent.announcements.index');

    Route::get('/parent/leave-requests', [ParentLeaveRequestController::class, 'index'])
        ->name('parent.leave-requests.index');

    Route::get('/parent/attendance', [ParentAttendanceController::class, 'index'])
        ->name('parent.attendance.index');

    Route::get('/parent/student-reports', [ParentStudentReportController::class, 'index'])
        ->name('parent.student-reports.index');

    Route::post('/parent/leave-requests', [ParentLeaveRequestController::class, 'store'])
        ->name('parent.leave-requests.store');
});

require __DIR__.'/auth.php';
