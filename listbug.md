# List of Bugs & Unfinished Features

## Bugs

- [x] Duration on Dashboard for Manager and Admin shows incorrect chart — check the datetime.
- [x] Task → monthly → status is not consistent; every month it resets to pending.
- [x] DailyReport verification by admin in index and show has no SweetAlert and returns redirect incorrectly.
- [x] DailyReport → create → status is not consistent and not shown yet.
- [x] Ticket → index for user does not show their own tickets.
- [x] Log Ticket escalated, take over, or cancel does not inform the description/text and is still named as “update”.
- [x] Filters in supports sort by date in oldest have a bugs.
- [x] Support Dashboard → ticket aktif didn't show the right value, and maybe we must fix it and need more eficient informations.
- [x] DailyReport → ticket dikerjakan → its not yet showing the same output like the others role supports, expecially for the "close tickets".
- [x] Admin escalated tickets also we need to fix it and create idea how to display it on the reports, remember the close tickets in here only for supports role and not yet implements as admin.
- [x] Admin and Manager Dashboard, in UI chart fix the width between Ticket Trends, and SLA Category.
- [x] Login Button loading bugs.
- [ ] Bugs in sidebar size, its diff when username has long text the other way around.
- [x] Durations tickets didn't setup yet.
- [x] DailyReports for support Unauthorized, and for the fields solve by in pdf, also in show or detail tickets, add the inprogress and open ticets, remove the showing of tickets or add it like modals things.
- [x] Search and Filter in tickets pages has a bug.
- [x] Daily Report Tickets, add note tickets when is aviable in every tickets. Expecially in pdf or in show data in system.
- [x] Fix bug logic in notes tickets.
- [ ] Total ticket in monthly report has a diff output.
- [x] Bugs in daily reports when the ticket open yesterday and solve by today, the previous reports is not recorded but updated along with the ticket handle and status.
- [x] Bugs in detail daily report -> tickets shows changes the data sources, as we do in pdf.
- [x] Add durations and time in WITA at dashboard manager
- [x] Confirm box in select priority by admin
- [x] Notes in daily report ticket snapsho modal, its not config first
- [x] Bugs in durations tickets calculations, the hours not calculate, its calcutate minuiete only.
- [ ] Datetime didn't setup into WITA using own helper yet (there's missmatchs between this).
- [x] User can set priority on tickets, before confrim by admin.
- [x] Bug in img solutions.
- [x] Add img in show detail daily reports.

## Unfinished Features

- [x] PDF structure.
- [x] Cron Job for Daily Report Telegram bot at 4 PM (not optimal yet).
- [x] Fix every table, add pagination, and add searching.
- [x] Make all create/edit/show actions a popup or alert so we can interact with them. Its called MODALS.
- [x] API for Monthly Report has not been created.
- [x] UI login.
- [x] Profile UI.
- [x] Logs for tickets.
- [x] Logs for task and reports, add like tickets feature, when necessary add the handbooks also.
- [x] Fix dashboard for User and Support roles.
- [x] Fix dashboard for Manager and Admin roles.
- [x] Fix logo.
- [x] Animate in logs detail/show.
- [x] In Tickets → Take over, handle-escalated not yet has sweet alert.
- [x] In Monthly Report → edit, create, destroy not yet has sweet alert.
- [ ] Make consistent language in alert dan other text.
- [x] Make responsive for any device.
- [x] Add footer and fix structure of the folder/file in source.
- [x] Create a button to close sidebar.
- [x] Add admin page that can create new category, locations and user.
- [ ] Add status and priority in admin settings. [OPSIONAL]. _need foreignkey and make new tables, its mean change a lot of things_
- [ ] Add Edit functions in daily report controller [OPSIONAL] _need foreignkey and make new tables, its mean change a lot of things_
- [x] Priority Tickets make it clear for admin to determine witch one the support gonna do first and so the admin can have access to determine the tickets priority.
- [x] Add more durations, tell apart between durations tickets open before handle by support and durations tickets solved by supports.
- [x] Add note when the tickets is didn't closed todays, from admin or IT support.
- [x] Paginate in monthly reports
- [x] Dashboard Total ticket, task, ect
- [x] Daily Report Show detail add the column in daily report ticket snapshot and refix the store func, ticketSnapshot, ect. "NEED MORE TESTING IN THE FUTURE"
- [x] Add edit and delete in daily report before verif by admin.
- [x] Add img solutions
- [x] Add priority input by users and validated it by admin.
- [ ] Add feature when user login they not set the locations again.
- [x] Add Filters in daily reports.
- [ ] Add Divisions.

## Other Notes

- Create API or other documentation.
- Create super admin that can perform CRUD.
- Check the whole feature in this project and make sure it's working correctly as expected on the pdf.
