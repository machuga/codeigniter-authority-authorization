<?php
/**
 * Authority
 *
 * Authority is an authorization library for CodeIgniter 2+ and PHPActiveRecord
 * This library is inspired by, and largely based off, Ryan Bates' CanCan gem
 * for Ruby on Rails.  It is not a 1:1 port, but the essentials are available.
 * Please check out his work at http://github.com/ryanb/cancan/
 *
 * @package     Authority
 * @version     0.0.3
 * @author      Matthew Machuga
 * @license     MIT License
 * @copyright   2011 Matthew Machuga
 * @link        http://github.com/machuga
 *
 **/

namespace Authority;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Ability {

    protected static $_rules = array();
    protected static $_action_aliases = array();

    public static function initialize($user)
    {
        static::action_alias('manage', array('create', 'read', 'update', 'delete'));
        static::action_alias('moderate', array('update', 'delete'));

        if ( ! $user || ! $user->role) return false;

        if ($user->role == 'admin')
        {
            static::allow('manage', 'all');
        }
    }

    public static function can($action, $resource, $resource_val = null)
    {
        if ( empty(static::$_rules)) {
            static::initialize(static::current_user());
        }

        // See if the action has been aliased to somethign else
        $true_action = static::determine_action($action);

        $matches = static::find_matches($true_action, $resource);

        if ($matches && ! empty($matches))
        {
            $results = array();
            $resource_value = ($resource_val) ?: $resource;

            foreach ($matches as $matched_rule)
            {
                $results[] = !($matched_rule->callback($resource_value) xor $matched_rule->allowed());
            }

            // Last rule overrides others
            return $results[count($results)-1];
        }
        else
        {
            return false;
        }
    }

    public static function cannot($action, $resource, $resource_val = null)
    {
        static::can($action, $resource, $resource_val);
    }

    public static function allow($action, $resource, \Closure $callback = null)
    {
        static::$_rules[] = new Rule(true, $action, $resource, $callback);
    }

    public static function deny($action, $resource, \Closure $callback = null)
    {
        static::$_rules[] = new Rule(false, $action, $resource, $callback);
    }

    public static function action_alias($action, Array $aliases)
    {
        static::$_action_aliases[$action] = $aliases;
    }

    public static function dealias($action)
    {
        return static::$_action_aliases[$action] ?: $action; 
    }

    protected static function determine_action($action)
    {
        $actions = array();
        if ( ! empty(static::$_action_aliases))
        {
            foreach (static::$_action_aliases as $aliased_action => $aliases)
            {
                if ( ! empty($aliases) && in_array($action, $aliases))
                {
                    $actions[] = $aliased_action;
                }
            }
        }

        if (empty($actions))
        {
            return $action;
        }
        else
        {
            $actions[] = $action;
            return $actions;
        }
    }

    protected static function find_matches($action, $resource)
    {
        $matches = array();
        if (!empty(static::$_rules))
        {
            foreach(static::$_rules as $rule)
            {
                if ($rule->relevant($action, $resource))
                {
                    $matches[] = $rule;
                }
            }
        }
        return $matches;
    }

    public function __construct()
    {
        static::initialize(static::current_user());
    }

    protected static function current_user()
    {
        $ci = get_instance();
        if (isset($ci->authentic)) {
            // using authentic library
            return $ci->authentic->current_user() ?: new \User;
        }
    }
}
