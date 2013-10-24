<h4><i class="icon-question-sign"></i> Help with roles</h4>
<dl>
    <dt>Super</dt>
    <dd>A super user has access to all operations. No access control is ever perfomed for a user with the super role.
    Commonly there is only ever 1 super user per system, although we may setup a super user role for support purposes.
     Not even a super user can create another super user account.</dd>
    <dt>Admin</dt>
    <dd>An admin user has access to all operations. However, access control is performed against the admin role
    and operations maybe limited in the future.</dd>
    <dt>Data Manager</dt>
    <dd>A data manager user has access to all operations apart from managing users and viewing the event log.</dd>
    <dt>Staff</dt>
    <dd>A staff user is the default role for an authenticated user. Users with the staff role can access reports but
    cannot access any of the setup operations.</dd>
    </dl>