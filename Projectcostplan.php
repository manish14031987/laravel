<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projectcostplan extends Model
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $table = 'project_cost_plan';

    protected $fillable = [
        'cost_plan_Id',
        'cost_plan_name',
        'cost_plan_type',
        'project_id',
        'start_date',
        'a_start_date',
        'l_start_date',
        'l_end_date',
        'e_start_date',
        'e_end_date',
        'a_end_date',
        'end_date',
        'duration',
        'persion_responsible',
        'phase_approval',
        'template',
        'reference_phase',
        'quality_approval',
        'created_by',
        'modified_by',
        'created_date',
        'modified_date',
        'status',
        'is_deleted'
    ];
    /*
     * public function customer()
     * {
     * return $this->hasOne('App\Customer', 'id', 'customer_id');
     * }
     *
     *
     * public function user()
     * {
     * return $this->hasOne('App\User', 'id', 'user_id');
     * }
     *
     *
     * public function status()
     * {
     * return $this->hasOne('App\Status', 'id', 'status_id');
     * }
     * public function plan()
     * {
     * return $this->hasOne('App\Plans', 'id', 'plan_id');
     * }
     */
}