Authority Authorization for Codeigniter
=======================================

Documentation coming soon.

Authority is an authorization library inspired by, and largely based off, Ryan
Bates' CanCan gem for Ruby on Rails.  It is not a 1:1 port, but the essentials
are available. Please check out his work at http://github.com/ryanb/cancan/.

Usage
-----

Inside `./application/sparks/authority/0.0.1/libraries/Authority.php`

If using Authentic authentication lib, `Authority::current_user()` method may be left alone; otherwise, change it to fit your means. 

Authority is fairly configuration agnostic (and at it's core it is framework
agnostic), so your setup may very.  All configuration should take place inside
of the the `Authority::initialize() method.  An example configuration:

    public static function initialize($user)
    {
        // Set any aliased actions you may want to simplify your use
        // Here, 'manage' will cover basic crud
        Authority::action_alias('manage', array('create', 'read', 'update', 'delete'));

        // Next, we check if there is a user or not.  You may setup rules for a
        // guest user, or simply deny them.
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

More examples and clearer documentation will be provided soon
