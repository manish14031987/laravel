<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sales_order extends Model {

    protected $table = 'sales_order';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'salesorder_description',
        'inquiry',
        'quotation',
        'sales_order_type',
        'customer',
        'sales_region',
        'created_on',
        'created_by',
        'requested_by',
        'billing_type',
        'salesorder_number',
        'company_id',
        'changed_on',
        'changed_by',
        'customer_name',
        'sales_organization',
        'salesorder_gross_price',
        'salesorder_discount',
        'salesorder_discount_amt',
        'salesorder_discount_gross_price',
        'salesorder_sales_taxamt',
        'salesorder_net_price',
        'salesorder_freight_charges',
        'salesorder_total_price',
        'salesorder_profit_margin',
        'salesorder_profit_amt',
        'salesorder_profit_margin_grossprice',
        'approver_1',
        'approver_2',
        'approver_3',
        'approver_4',
        'approved_indicator',
        'approver_token'
    ];

    public static function getSalesOrderReport($sales_order_number_from = NULL , $sales_order_number_to = NULL)
    {
        $query = self::query();
        $query->select('sales_order.salesorder_number', 'sales_order.approved_indicator as status', 'sales_order.created_on',
                        'project.id as project_uid', 'project.project_Id', 'project.project_name',
                        'project.bucket_id','buckets.bucket_id as buck_id', 'project.cost_centre', 'project.project_desc',
                        'project.start_date as pr_start_date' , 'project.end_date as pr_end_date',
                        'project.a_start_date', 'project.a_end_date', 'project.f_start_date', 'project.f_end_date', 
                        'project.sch_date', 'project.p_end_date', 'project.created_by', 'project.p_start_date',
                        'buckets.name as bucket_name','createrole.role_name as createt_by_role_name', 'portfolio.name as portfolio_name', 
                        'portfolio.port_id as portfolio_id', 'portfolio_type.name as portfolio_type', 'portfolio.port_id as portfolio_id', 
                        'project_type.name as project_type_name', 'project_phase.phase_Id as project_phase_id',
                        'state_subrub.subrub as location','cost_centres.cost_centre as cost_centre_name','department_type.name as department_name','customer_master.contact_phone as customer_phone_no');
        $query->leftJoin('sales_item', 'sales_item.salesorder_number', '=', 'sales_order.salesorder_number');
        $query->leftJoin('customer_master', 'customer_master.id', '=', 'sales_order.customer');
        $query->leftJoin('project', 'project.project_Id', '=', 'sales_item.project_id');
        $query->leftJoin('project_type', 'project_type.id', '=', 'project.project_type');
        $query->leftJoin('portfolio', 'portfolio.id', '=', 'project.portfolio_id');
        $query->leftJoin('portfolio_type', 'portfolio_type.id', '=', 'portfolio.type');
        $query->leftJoin('department_type', 'department_type.id', '=', 'project.department');
        $query->leftJoin('buckets', 'buckets.id', '=', 'project.bucket_id');
        $query->leftJoin('project_phase', 'project_phase.project_id', '=', 'project.id');
        $query->leftJoin('createrole', 'createrole.id', '=', 'project.created_by');
        $query->leftJoin('state_subrub', 'state_subrub.id', '=', 'project.location_id');
        $query->leftJoin('cost_centres', 'cost_centres.cost_id', '=', 'project.cost_centre');
        $query->where('sales_order.company_id', '=', Auth::user()->company_id);
        $query->orderBy('sales_order.salesorder_number', 'desc');

        if (isset($sales_order_number_from) && isset($sales_order_number_to)) 
        $query->whereBetween('sales_order.salesorder_number', [$sales_order_number_from, $sales_order_number_to]);
        
        return $query->get();
    }
}
