<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/14
 * Time: 13:36
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClearingHouses extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    const EX_NO = 0;
    const EX_YES = 1;
    const UN_RMBM = 1;
    const UN_RMBK = 2;
    const NU = 0;
    const MO_Z = 1;
    const MO_K = 2;
    const IN_NO = 0;
    const IN_YES = 1;
    protected $fillable = ['users_id','univalent','unit','number','company','mode','invoice','room_city','address','remarks','contacts_name','contacts_tel','photo'];

    /**
     * 审核数据的处理
     * @param $examine
     * @return string
     */
    public function getExamineAttribute($examine)
    {
        if ($examine == self::EX_NO) {
            return '待审核';
        } elseif ($examine == self::EX_YES) {
            return '通过';
        } else {
            return '未通过';
        }
    }

    public function getPhotoAttribute($image)
    {
        return json_decode($image, true);
    }


    /**
     * 单位数据的处理
     * @param $unit
     * @return string
     */
    public function getUnitAttribute($unit)
    {
        if ($unit == self::UN_RMBM) {
           return '元/平方';
        } elseif ($unit == self::UN_RMBK) {
            return '元/片';
        }
    }

    /**
     * 出售数量数据的处理
     * @param $number
     * @return string
     */
    public function getNumberAttribute($number)
    {
        if ($number == self::NU) {
            return '数量不限';
        } else {
            return $number;
        }
    }

    /**
     * 交货方式数据的处理
     * @param $mode
     * @return string
     */
    public function getModeAttribute($mode)
    {
        if ($mode == self::MO_Z) {
            return '自提';
        } elseif($mode == self::MO_K) {
            return '快递';
        }
    }

    /**
     * 发票数据的处理
     * @param $invoice
     * @return string
     */
    public function getInvoiceAttribute($invoice)
    {
        if ($invoice == self::IN_NO) {
            return '否';
        } elseif($invoice == self::IN_YES) {
            return '是';
        }
    }

    /**
     * 关联用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(Users::class);
    }

    /**
     * 根据id查询数据
     */
    public function sel_clearing_houses_info($id)
    {
        $res = \DB::table('clearing_houses')
            ->join('cities','cities.area_code','=','clearing_houses.room_city')
            ->select(['clearing_houses.id','univalent','unit','number','company','mode','invoice','area_name','address','remarks','contacts_name','contacts_tel','photo','clearing_houses.created_at'])
            ->where('clearing_houses.id',$id)
            ->where('clearing_houses.deleted_at','=',NULL)
            ->where('examine','=','1')
            ->first();
        if(isset($res->photo)){
            $res->photo = json_decode_photo($res->photo);
        }
        return $res;
    }

    /**
     * 列表查询
     * 1时间先后2审核通过，3。城市范围4。数据条数
     */
    public function sel_clearing_houses_list($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        $where = array(
            'clearing_houses.deleted_at'=>NULL,
            'clearing_houses.examine'=>'1',
        );
        if(isset($data['f']))
        {
            $where['users_id'] = $data['users_id'];
            $res = \DB::table('clearing_houses')
                ->join('cities','cities.area_code','=','clearing_houses.room_city')
                ->select(['clearing_houses.id','univalent','unit','number','company','mode','invoice','area_name','address','remarks','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->orderBy('clearing_houses.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            if($data['room_city']!=0)
            {
                $where['clearing_houses.room_city']=$data['room_city'];
            }
            $res = \DB::table('clearing_houses')
                ->join('cities','cities.area_code','=','clearing_houses.room_city')
                ->select(['clearing_houses.id','univalent','unit','number','company','mode','invoice','area_name','address','remarks','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->orderBy('clearing_houses.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }
        foreach ($res as $k=>$v)
        {
            $res[$k]->photo = json_decode_photo($res[$k]->photo);
        }
        return $res;
    }

    public function sel_clearing_houses_page($data){
        $where = array(
            'clearing_houses.deleted_at'=>NULL,
            'clearing_houses.examine'=>'1',
        );
        if(isset($data['f'])) {
            $where['users_id'] =$data['users_id'];
            $res = \DB::table('clearing_houses')
                ->join('cities','cities.area_code','=','clearing_houses.room_city')
                ->select(['clearing_houses.id','univalent','unit','number','company','mode','invoice','area_name','address','remarks','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->count();
        }else{
            if($data['room_city']!=0)
            {
                $where['clearing_houses.room_city']=$data['room_city'];
            }
            $res = \DB::table('clearing_houses')
                ->join('cities','cities.area_code','=','clearing_houses.room_city')
                ->select(['clearing_houses.id','univalent','unit','number','company','mode','invoice','area_name','address','remarks','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->count();
        }
        return $res;

    }

    public function delMyData($id){
        $date = date('Y-m-d H:i:s');
        return \DB::table('clearing_houses')
            ->where(['id'=>$id])
            ->update(['deleted_at'=>$date,'updated_at'=>$date]);
    }


}