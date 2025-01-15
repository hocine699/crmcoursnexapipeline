<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'storage_limit',
        'max_user',
        'max_account',
        'max_contact',
        'description',
        'status',
        'enable_chatgpt',
        'trial',
        'trial_days',
    ];

    public static $arrDuration = [
        'lifetime' => 'Lifetime',
        'month' => 'Per Month',
        'year' => 'Per Year',
    ];

    public function status()
    {
        return [
            __('Lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return Plan::count();
    }
    private static $most_purchese_plan = null;
    public static function most_purchese_plan()
        {
            if (self::$most_purchese_plan === null) {
                self::$most_purchese_plan = self::fetchmost_purchese_plan();
            }

            return self::$most_purchese_plan;
        }

    public static function fetchmost_purchese_plan()
    {
        $free_plan = Plan::where('price', '<=', 0)->first()->id;

        return User:: select('plans.name', 'plans.id', \DB::raw('count(*) as total'))->join('plans', 'plans.id', '=', 'users.plan')->where('type', '=', 'owner')->where('plan', '!=', $free_plan)->orderBy('total', 'Desc')->groupBy('plans.name', 'plans.id')->first();
    }

}
