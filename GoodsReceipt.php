<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model {

    protected $table = 'goods_receipt';
    public $timestamps = false;
    protected $fillable = [
        'document_date',
        'purchase_order_number',
        'posting_date',
        'created_by',
        'created_on',
        'changed_by',
        'changed_on',
        'company_id',
        'reversed'
    ];

    public static function getGoodsReceiptData($purchaseorders = NULL) {
        $query = self::query()
                ->select('goods_receipt.id', 'vendor.name', 'gritem.gl_account as gl_account_number', 'goods_receipt.purchase_order_number', 'pgrcost.dr_cr_indicator', 'project.project_Id', 'project.id as pid', 'goods_receipt.posting_date', 'gritem.quantity_received', 'gritem.purchase_order_item_no as item_no', 'gritem.item_cost', 'cost_centres.cost_centre')
                ->join('goods_receipt_item as gritem', 'gritem.goods_receipt_no', '=', 'goods_receipt.id')
                ->join('project_gr_cost as pgrcost', 'pgrcost.material_documber_number', '=', 'goods_receipt.id')
                ->leftJoin('cost_centres', 'cost_centres.cost_id', 'gritem.cost_center')
                ->leftJoin('vendor', 'vendor.id', '=', 'gritem.vendor_number')
                ->leftJoin('project', 'project.id', '=', 'gritem.project')
                ->orderBy('goods_receipt.id');

        if (isset($purchaseorders)) {
            if (is_array($purchaseorders)) {
                $query->whereIn('goods_receipt.purchase_order_number', $purchaseorders);
            } else {
                $query->where('goods_receipt.purchase_order_number', $purchaseorders);
            }
        }
        return $query->get();
    }

}
