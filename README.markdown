Authority Authorization for Codeigniter
=======================================

Authority is an authorization library inspired by, and largely based off, Ryan
Bates' CanCan gem for Ruby on Rails.  It is not a 1:1 port, but the essentials
are available. Please check out his work at http://github.com/ryanb/cancan/.

Usage
-----

Authority uses the `can()` function to check if the current user has permission
to access a certain portion of the site or application.  This function is an 
alias to the `Authority::can()` method, for convenience.  The `cannot()` 
function is simply a negation of `can()`.

### can() ###

`can()` is a function that accepts two parameters.  The first parameter should
be the action the current user is trying to perform, such as `read`, `create`,
`delete`.  The second parameter may be either an object or a class name for the
resource the current user wants to access, such as `Post`, or `Comment`.
Authority knows who the current user is internally upon instantiation of the 
library via default or custom configuration.

In CodeIgniter, `can()` is available in both Views and Controllers.  This means
you may restrict accessibility to entire controller actions or certain functionality on a
page (generally in conjunction with each other).

An example of this use in a PHP view:

```php
<p>Here is my awesome text blah blah blah</p>
<!-- $secret is of type Secret -->

<?php if (can('read', $secret)): ?>
    <p><?php echo $secret->message ?></p>
<?php endif ?>

<?php if (can('update', $secret)): ?>
    <p><?php echo anchor("secrets/edit/{$secret->id}", 'Click to edit an awesome secret') ?></p>
<?php endif ?>
```

### Authority::allow() ###

`Authority::allow()` is a method that accepts up to three  arguments.  It is used
to grant access to various elements of an application.  It's counterpart,
`Authority::deny()` accepts the same number of arguments, but will deny access
to various elements of the application.  The method signature is:

`allow($action, $resource, \Closure $callback = null)`

Like the `can()` helper, `$action` is the action the current user is trying to
perform, and `$resource` is the object or class name for the resource the
current user is trying to access.  An anonymous function may be passed in as
the third argument if finer grained control is desired (restrict editability
to a resource to only the owner).  `Authority::deny()` shares the same
signature.  Examples are below within the Configuration section.


Configuration
-------------

Inside `./application/sparks/authority/0.0.1/libraries/Authority.php`

If using Authentic authentication lib, `Authority::current_user()` method may
be left alone; otherwise, change it to fit your needs. 

Authority is fairly configuration agnostic (and at it's core it is framework
agnostic), so your setup may very.  All configuration should take place inside
of the the `Authority::initialize() method.  An example configuration:

```php
public static function initialize($user)
{
    // Set any aliased actions you may want to simplify your use
    // Here, 'manage' will cover basic crud
    Authority::action_alias('manage', array('create', 'read', 'update', 'delete'));

    // Next, we check if there is a user or not.  You may setup rules for a
    // guest user, or simply ignore them. Here, no permissions are given.
    if ( ! $user || ! $user->role) return false;

    // If the current user is an admin, setup permissions
    // Note: You may check on any attribute, class type, etc 
    if ($user->role == 'admin')
    {
        // Authority::allow() will grant permission for the current user to
        // perform that particular action.  The first parameter is the
        // action, the second parameter is the resource the user is trying
        // to access.  'all' is a default wildcard.
        Authority::allow('manage', 'all');

        // The last rule will always take precedence, so if we want our
        // admin to be able to manage users, but not delete them (unless
        // that user is him/herself, we will deny this ability.) As seen
        // below, a third closure parameter may be passed in to define
        // finer-grain permissions.
        Authority::deny('delete', 'User', function ($a_user) use ($user) {
            return $a_user->id !== $user->id;
        });
    }

    // We will give a typical member read-access to everything.
    if ($user->role == 'member')
    {
        Authority::allow('read', 'all');
    }

}
```

Caveats
-------

Currently, a resource must be a string or an object.  If a callback is to be
used on a resource, it must be an object.  The class type will be determined
when rule matching takes place.  Additionally, while not necessary, this 
plugin works well with PHPActiveRecord and likely most other ORMs.

Notes
-----

This library was created and is maintained by Matthew Machuga, is hosted on
GitHub, and is made possible by the GetSparks team.  Please support their
efforts!

If you have any questions, feel free to send me a message!

Twitter: @machuga
