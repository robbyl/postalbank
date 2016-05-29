<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for core_completion subsystem.
 *
 * @package     core_completion
 * @category    string
 * @copyright   &copy; 2008 The Open University
 * @author      Sam Marshall
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['achievinggrade'] = 'Achieving grade';
$string['achievedgrade'] = 'Achieved grade';
$string['activities'] = 'Activities';
$string['activityaggregation'] = 'Condition requires';
$string['activityaggregation_all'] = 'ALL selected activities to be completed';
$string['activityaggregation_any'] = 'ANY selected activities to be completed';
$string['activitiescompleted'] = 'Activity completion';
$string['activitiescompletednote'] = 'Note: Activity completion must be set for an activity to appear in the above list.';
$string['activitycompletion'] = 'Activity completion';
$string['aggregationmethod'] = 'Aggregation method';
$string['all'] = 'All';
$string['any'] = 'Any';
$string['approval'] = 'Approval';
$string['badautocompletion'] = 'When you select automatic completion, you must also enable at least one requirement (below).';
$string['complete'] = 'Complete';
$string['completed'] = 'Completed';
$string['completedunlocked'] = 'Completion options unlocked';
$string['completedunlockedtext'] = 'When you save changes, completion state for all students will be erased. If you change your mind about this, do not save the form.';
$string['completedwarning'] = 'Completion options locked';
$string['completedwarningtext'] = 'One or more students ({$a}) has already marked this activity as completed. Changing completion options will erase their completion state and may cause confusion. Thus the options have been locked and should not be unlocked unless absolutely necessary.';
$string['completeviarpl'] = 'Complete via rpl';
$string['completion'] = 'Completion tracking';
$string['completion-alt-auto-enabled'] = 'The system marks this item complete according to conditions: {$a}';
$string['completion-alt-auto-fail'] = 'Completed: {$a} (did not achieve pass grade)';
$string['completion-alt-auto-n'] = 'Not completed: {$a}';
$string['completion-alt-auto-pass'] = 'Completed: {$a} (achieved pass grade)';
$string['completion-alt-auto-y'] = 'Completed: {$a}';
$string['completion-alt-manual-enabled'] = 'Students can manually mark this item complete: {$a}';
$string['completion-alt-manual-n'] = 'Not completed: {$a}. Select to mark as complete.';
$string['completion-alt-manual-y'] = 'Completed: {$a}. Select to mark as not complete.';
$string['completion-fail'] = 'Completed (did not achieve pass grade)';
$string['completion-n'] = 'Not completed';
$string['completion-pass'] = 'Completed (achieved pass grade)';
$string['completion-title-manual-n'] = 'Mark as complete: {$a}';
$string['completion-title-manual-y'] = 'Mark as not complete: {$a}';
$string['completion-y'] = 'Completed';
$string['completion_automatic'] = 'Show activity as complete when conditions are met';
$string['completion_help'] = 'If enabled, activity completion is tracked, either manually or automatically, based on certain conditions. Multiple conditions may be set if desired. If so, the activity will only be considered complete when ALL conditions are met.

A tick next to the activity name on the course page indicates when the activity is complete.';
$string['completion_link'] = 'activity/completion';
$string['completion_manual'] = 'Students can manually mark the activity as completed';
$string['completion_none'] = 'Do not indicate activity completion';
$string['completionactivitydefault'] = 'Use activity default';
$string['completiondefault'] = 'Default completion tracking';
$string['completiondisabled'] = 'Disabled, not shown in activity settings';
$string['completionenabled'] = 'Enabled, control via completion and activity settings';
$string['completionexpected'] = 'Expect completed on';
$string['completionexpected_help'] = 'This setting specifies the date when the activity is expected to be completed. The date is not shown to students and is only displayed in the activity completion report.';
$string['completionicons'] = 'Completion tick boxes';
$string['completionicons_help'] = 'A tick next to an activity name may be used to indicate when the activity is complete.

If a box with a dotted border is shown, a tick will appear automatically when you have completed the activity according to conditions set by the teacher.

If a box with a solid border is shown, you can click it to tick the box when you think you have completed the activity. (Clicking it again removes the tick if you change your mind.) The tick is optional and is simply a way of tracking your progress through the course.';
$string['completionmenuitem'] = 'Completion';
$string['completionnotenabled'] = 'Completion is not enabled';
$string['completionnotenabledforcourse'] = 'Completion is not enabled for this course';
$string['completionnotenabledforsite'] = 'Completion is not enabled for this site';
$string['completionondate'] = 'Date';
$string['completionondatevalue'] = 'User must remain enrolled until';
$string['completionprogressonview'] = 'Mark as In Progress on first view';
$string['completionprogressonviewhelp'] = 'Mark course completion status as In Progress as soon as students view the course the first time (instead of when they meet the first criterion).';
$string['completionprogressonview_help'] = 'Mark course completion status as In Progress as soon as students view the course the first time (instead of when they meet the first criterion).';
$string['completionduration'] = 'Enrolment';
$string['completionsettingslocked'] = 'Completion settings locked';
$string['completionusegrade'] = 'Require grade';
$string['completionusegrade_desc'] = 'Student must receive a grade to complete this activity';
$string['completionusegrade_help'] = 'If enabled, the activity is considered complete when a student receives a grade. Pass and fail icons may be displayed if a pass grade for the activity has been set.';
$string['completionview'] = 'Require view';
$string['completionview_desc'] = 'Student must view this activity to complete it';
$string['configcompletiondefault'] = 'The default setting for completion tracking when creating new activities.';
$string['configenablecompletion'] = 'When enabled, this lets you turn on completion tracking (progress) features at course level.';
$string['configenablecourserpl'] = 'When enabled, a course can be marked as completed by assigning the user a Record of Prior Learning.';
$string['configenablemodulerpl'] = 'When enabled for a module, any Course Completion criteria for that module type can be marked as completed by assigning the user a Record of Prior Learning.';
$string['confirmselfcompletion'] = 'Confirm self completion';
$string['courseaggregation'] = 'Condition requires';
$string['courseaggregation_all'] = 'ALL selected courses to be completed';
$string['courseaggregation_any'] = 'ANY selected courses to be completed';
$string['coursealreadycompleted'] = 'You have already completed this course';
$string['coursecomplete'] = 'Course complete';
$string['coursecompleted'] = 'Course completed';
$string['coursecompletion'] = 'Course completion';
$string['coursecompletioncondition'] = 'Condition: {$a}';
$string['coursegrade'] = 'Course grade';
$string['coursesavailable'] = 'Courses available';
$string['coursesavailableexplaination'] = 'Note: Course completion conditions must be set for a course to appear in the above list.';
$string['courserpl'] = 'Course RPL';
$string['courserplorallcriteriagroups'] = 'RPL for course or <br />all critera groups';
$string['courserploranycriteriagroup'] = 'RPL for course or <br />any critera group';
$string['criteria'] = 'Criteria';
$string['criteriagroup'] = 'Criteria group';
$string['criteriarequiredall'] = 'All criteria below are required';
$string['criteriarequiredany'] = 'Any criteria below are required';
$string['csvdownload'] = 'Download in spreadsheet format (UTF-8 .csv)';
$string['datepassed'] = 'Date passed';
$string['days'] = 'Days';
$string['daysoftotal'] = '{$a->days} of {$a->total}';
$string['deletecompletiondata'] = 'Delete completion data';
$string['dependencies'] = 'Dependencies';
$string['dependenciescompleted'] = 'Completion of other courses';
$string['editcoursecompletionsettings'] = 'Edit course completion settings';
$string['enablecourserpl'] = 'Enable RPL for courses';
$string['enablemodulerpl'] = 'Enable RPL for modules';
$string['enablecompletion'] = 'Enable completion tracking';
$string['enablecompletion_help'] = 'If enabled, activity completion conditions may be set in the activity settings and/or course completion conditions may be set.';
$string['enrolmentduration'] = 'Enrolment duration';
$string['enrolmentdurationlength'] = 'User must remain enrolled for';
$string['err_noactivities'] = 'Completion information is not enabled for any activity, so none can be displayed. You can enable completion information by editing the settings for an activity.';
$string['err_nocourses'] = 'Course completion is not enabled for any other courses, so none can be displayed. You can enable course completion in the course settings.';
$string['err_nograde'] = 'A course pass grade has not been set for this course. To enable this criteria type you must create a pass grade for this course.';
$string['err_noroles'] = 'There are no roles with the capability moodle/course:markcomplete in this course.';
$string['err_nousers'] = 'There are no students on this course or group for whom completion information is displayed. (By default, completion information is displayed only for students, so if there are no students, you will see this error. Administrators can alter this option via the admin screens.)';
$string['err_settingslocked'] = 'One or more students have already completed a criterion so the settings have been locked. Unlocking the completion criteria settings will delete any existing user data and may cause confusion.';
$string['err_settingsunlockable'] = '<p>Modifying course completion criteria after some users have already completed the course is not recommended since it means different users will be marked as complete for different reasons.</p><p>At this point you can choose to delete all completion records for users who have already achieved this course. Their completion status will be recalculated using the new criteria next time the cron runs, so they may be marked as complete again.</p><p>Alternatively you can choose to keep all existing course completion records and accept that different users may have received their status for different accomplishments.</p>';
$string['err_system'] = 'An internal error occurred in the completion system. (System administrators can enable debugging information to see more detail.)';
$string['error:databaseupdatefailed'] = 'Database update failed';
$string['error:rplsaredisabled'] = 'Record of Prior Learning has been disabled by an Administrator';
$string['eventcoursecompleted'] = 'Course completed';
$string['eventcoursecompletionupdated'] = 'Course completion updated';
$string['eventcoursemodulecompletionupdated'] = 'Course module completion updated';
$string['excelcsvdownload'] = 'Download in Excel-compatible format (.csv)';
$string['fraction'] = 'Fraction';
$string['graderequired'] = 'Required course grade';
$string['gradexrequired'] = '{$a} required';
$string['inprogress'] = 'In progress';
$string['manualcompletionby'] = 'Manual completion by others';
$string['manualcompletionbynote'] = 'Note: The capability moodle/course:markcomplete must be allowed for a role to appear in the list.';
$string['manualselfcompletion'] = 'Manual self completion';
$string['manualselfcompletionnote'] = 'Note: The self completion block should be added to the course if manual self completion is enabled.';
$string['markcomplete'] = 'Mark complete';
$string['markedcompleteby'] = 'Marked complete by {$a}';
$string['markingyourselfcomplete'] = 'Marking yourself complete';
$string['moredetails'] = 'More details';
$string['nocriteriaset'] = 'No completion criteria set for this course';
$string['notcompleted'] = 'Not completed';
$string['notenroled'] = 'You are not enrolled in this course';
$string['nottracked'] = 'You are currently not being tracked by completion in this course';
$string['notyetstarted'] = 'Not yet started';
$string['overallaggregation'] = 'Completion requirements';
$string['overallaggregation_all'] = 'Course is complete when ALL conditions are met';
$string['overallaggregation_any'] = 'Course is complete when ANY of the conditions are met';
$string['pending'] = 'Pending';
$string['periodpostenrolment'] = 'Period post enrolment';
$string['progress'] = 'Student progress';
$string['progress-title'] = '{$a->user}, {$a->activity}: {$a->state} {$a->date}';
$string['progresstotal'] = 'Progress: {$a->complete} / {$a->total}';
$string['recognitionofpriorlearning'] = 'Recognition of prior learning';
$string['remainingenroledfortime'] = 'Remaining enrolled for a specified period of time';
$string['remainingenroleduntildate'] = 'Remaining enrolled until a specified date';
$string['reportpage'] = 'Showing users {$a->from} to {$a->to} of {$a->total}.';
$string['requiredcriteria'] = 'Required criteria';
$string['restoringcompletiondata'] = 'Writing completion data';
$string['roleaggregation'] = 'Condition requires';
$string['roleaggregation_all'] = 'ALL selected roles to mark when the condition is met';
$string['roleaggregation_any'] = 'ANY selected roles to mark when the condition is met';
$string['rpl'] = 'RPL';
$string['save'] = 'Save';
$string['saved'] = 'Saved';
$string['seedetails'] = 'See details';
$string['selectnone'] = 'Select none';
$string['self'] = 'Self';
$string['selfcompletion'] = 'Self completion';
$string['showinguser'] = 'Showing user';
$string['showrpl'] = 'Show RPL';
$string['showrpls'] = 'Show RPLs';
$string['unenrolingfromcourse'] = 'Unenrolling from course';
$string['unenrolment'] = 'Unenrolment';
$string['unit'] = 'Unit';
$string['unlockcompletion'] = 'Unlock completion options';
$string['unlockcompletiondelete'] = 'Unlock criteria and delete existing completion data';
$string['unlockcompletionwithoutdelete'] = 'Unlock criteria without deleting';
$string['usealternateselector'] = 'Use the alternate course selector';
$string['usernotenroled'] = 'User is not enrolled in this course';
$string['viewcoursereport'] = 'View course report';
$string['viewingactivity'] = 'Viewing the {$a}';
$string['viewedactivity'] = 'Viewed the {$a}';
$string['writingcompletiondata'] = 'Writing completion data';
$string['xdays'] = '{$a} days';
$string['yourprogress'] = 'Your progress';
$string['activitiescompleted_help']='These are activities that a learner is required to complete to complete this criteria. Activities are required to have "Activity completion" enabled in order to appear in this list.';
$string['activityaggregationmethod']='Aggregation method';
$string['activityaggregationmethod_help'] = 'An aggregation method of "All" means this criteria will be marked as complete when the learner has complete all the selected activities. If the aggregation method is set to "Any" only one of the selected activities will be required for the learner to complete the course.';
$string['activityrpl']='Activity RPL';
$string['afterspecifieddate']='After specified date';
$string['aggregationmethod']='Aggregation method';
$string['aggregationmethod_help'] = 'An aggregation method of "All" means the course will be marked as complete when the learner meets all the criteria set on this page. If the aggregation method is set to "Any" only one criteria type for the course will be required for the learner to complete the course.';
$string['aggregateall']='All';
$string['aggregateany']='Any';
$string['completiondependencies']='Completion dependencies';
$string['completiondependencies_help']='These are courses that a learner is required to complete before this course can be marked as complete';
$string['completionstartonenrol']='Completion tracking begins on enrolment';
$string['completionstartonenrolhelp']='Begin tracking a student\'s progress in course completion after course enrolment';
$string['completionstartonenrol_help']='Begin tracking a student\'s progress in course completion after course enrolment';
$string['courseaggregationmethod']='Aggregation method';
$string['courseaggregationmethod_help'] = 'An aggregation method of "All" means this criteria will be marked as complete when the learner has complete all the selected courses. If the aggregation method is set to "Any" only one of the selected courses will be required for the learner to complete the course.';
$string['coursegrade_help']='When enabled this criteria will be marked complete for a learner when they achieve the grade specified or higher';
$string['criteriagradenote'] = 'Please note that updating the required grade here will not update the current course pass grade.';
$string['date']='Date';
$string['date_help']='When enabled this criteria will be marked as complete for all users where the specified date is reached';
$string['enrolmentduration']='Days left';
$string['err_nocriteria']='No course completion criteria have been set for this course';
$string['err_noroles']='There are no roles with the capability \'moodle/course:markcomplete\' in this course. You can enable this criteria type by adding this capability to role(s).';
$string['datepassed']='Date passed';
$string['daysafterenrolment']='Days after enrolment';
$string['deletedcourse']='Deleted course';
$string['durationafterenrolment']='Duration after enrolment';
$string['durationafterenrolment_help']='When enabled this criteria will be marked as complete when the duration of a user\'s enrolment has reached the specified length.';
$string['manualcompletionby_help']='Enabling this criteria allows you to select a role (or multiple roles) to manually mark a learner as complete in a course';
$string['manualselfcompletion_help']='A learner can mark themselves complete in this course using the "Self completion" block';
$string['overallcriteriaaggregation']='Overall criteria type aggregation';
$string['overallcriteriaaggregation_help']='How the course completion system determines if a learner is complete';
$string['roleaggregationmethod']='Aggregation method';
$string['roleaggregationmethod_help'] = 'An aggregation method of "All" means this criteria will be marked as complete when the learner has been marked complete by all the selected roles. If the aggregation method is set to "Any" only one of the selected roles will be required to mark the learner complete for them to complete the course.';
$string['returntocourse']='Return to course';
$string['selectnone'] = 'Select none';

$string['archivecompletions'] = 'Completions archive';
$string['cannotarchivecompletions'] = 'No permission to archive this course completions';
$string['archivingcompletions'] = 'Archiving completions for course {$a}';
$string['archivedcompletions'] = 'Archived completions for course : {$a}';
$string['archivecompletionscheck'] = 'Are you sure you want to archive all completion records for users completed on this course?
    <br />
    This will store a limited historical record of the completions and then delete them completely.';
$string['archivecheck'] = 'Archive {$a} ?';
$string['nouserstoarchive'] = 'There are no users that have completed this course';
$string['archiveusersaffected'] = '{$a} users will be affected';
$string['usersarchived'] = '{$a} users completion records have been successfully archived';
