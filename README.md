
Simple users management module
==============================

Module has registration users in backend,
registration on frontend with confirmation by email
and final activation by admin.

Initial you have to create at least one admin-user by migrations.

Module has very simple roles management in backend.
Initial exists two admin's roles: 'roleRoot' and 'roleAdmin' create by migrations.
In idea user with 'roleRoot' can anything (developer).
User with 'roleAdmin' is owner-supermoderator of all content without some developers rights.

Another roles must be create in modules where its need by migrations.
But role assignments you can do in this backend.
