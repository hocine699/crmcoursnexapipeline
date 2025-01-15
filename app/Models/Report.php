<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'entity_type',
        'group_by',
        'chart_type',
        'created_by',
    ];

    public static $entity_type = [
        ''=>'Select Entity Type',
        'users' => 'User',
        'accounts' => 'Account',
        'contacts' => 'Contact',
        'leads' => 'Lead',
        'opportunities' => 'Opportunities',
        'invoices' => 'Invoice',
        'cases' => 'Case',
        'quotes' => 'Quote',
        'products' => 'Product',
        'tasks' => 'Task',
        'calls' => 'Call',
        'campaigns' => 'Campaign',
        'sales_orders' => 'Sales Order',
    ];

    public static $chart_type = [
        'bar_vertical' => 'Bar vertical',
        'bar_horizontal' => 'Bar horizontal',
        'pie' => 'Pie',
        'line' => 'Line',
    ];
    public static $columns = [
        'count' => 'Count',
    ];
    public static $users = [
        'type' => 'Type',
        'user_roles' => 'User Role',
    ];
    public static $tasks = [
        'user_id' => 'User id',
        'status' => 'Status',
        'priority' => 'Priority',
        'Parent' => 'Parent',
    ];
    public static $sales_orders = [
        'user_id' => 'User id',
        'quote' => 'Quote',
        'opportunity' => 'Opportunity',
        'status' => 'Status',
    ];
    public static $quotes = [
        'status' => 'Status',
        'account' => 'Account',
        'opportunity' => 'Opportunity',
    ];
    public static $accounts = [
        'type' => 'Type',
        'industry' => 'Industry',
    ];
    public static $contacts = [
        'account' => 'Account',
    ];
    public static $leads = [
        'account' => 'Account',
        'status' => 'Status',
        'source' => 'Lead Source',
        'campaign' => 'Campaign',
    ];
    public static $opportunities = [
        'account' => 'Account',
        'contact' => 'Contact',
        'campaign' => 'Campaign',
        'stage' => 'Opportunity Stage',
    ];
    public static $invoices = [
        'salesorder' => 'Sales Order',
        'quote' => 'Quote',
        'opportunity' => 'Opportunity',
        'status' => 'Status',
        'account' => 'Account',
    ];
    public static $products = [
        'status' => 'Status',
        'category' => 'Category',
        'brand' => 'Brand',
    ];
    public static $cases = [
        'account' => 'Account',
        'status' => 'Status',
        'priority' => 'Priority',
        'contact' => 'Contact',
    ];
    public static $campaigns = [
        'status' => 'Status',
        'type' => 'Type',
    ];
    public static $calls = [
        'status' => 'Status',
        'direction' => 'Direction',
        'parent' => 'Parent',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
