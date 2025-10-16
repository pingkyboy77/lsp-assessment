<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'route_name',
        'slug',
        'icon',
        'description',
        'group',
        'sort_order',
        'is_active',
        'is_sidebar_menu',
        'allowed_roles',
        'parent_route'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sidebar_menu' => 'boolean',
        'allowed_roles' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Check if user can access a specific route
     */
    public static function canAccessRoute($routeName, $user)
    {
        // Super admin can access everything
        if ($user->hasRole('superadmin') || $user->hasRole('super admin')) {
            return true;
        }

        $page = self::where('route_name', $routeName)
            ->where('is_active', true)
            ->first();

        // If page doesn't exist in database, allow access (for system routes)
        if (!$page) {
            return true;
        }

        // Check if user has any of the allowed roles
        if (!$page->allowed_roles || empty($page->allowed_roles)) {
            return true; // No restrictions
        }

        $userRoles = $user->getRoleNames()->toArray();
        return !empty(array_intersect($userRoles, $page->allowed_roles));
    }

    /**
     * Get pages for a specific role
     */
    public static function getForRole($role)
    {
        // Untuk PostgreSQL, kita perlu menggunakan approach yang berbeda
        $pages = self::where('is_active', true)
            ->where('is_sidebar_menu', true)
            ->get()
            ->filter(function ($page) use ($role) {
                // Jika allowed_roles null atau kosong, allow semua
                if (!$page->allowed_roles || empty($page->allowed_roles)) {
                    return true;
                }

                // Check apakah role ada dalam allowed_roles
                return in_array($role, $page->allowed_roles);
            })
            ->sortBy('sort_order')
            ->groupBy(function ($page) {
                return $page->group ?: 'main';
            });

        return $pages;
    }

    /**
     * Check if current route matches this page
     */
    public function isCurrentRoute()
    {
        return request()->route() && request()->route()->getName() === $this->route_name;
    }

    /**
     * Get available groups
     */
    public static function getGroups()
    {
        return self::distinct('group')
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->pluck('group')
            ->sort();
    }

    /**
     * Scope for active pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sidebar menu pages
     */
    public function scopeSidebarMenu($query)
    {
        return $query->where('is_sidebar_menu', true);
    }
}
