<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/15
 * Time: 14:55
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SearchBricks extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['users_id','specifications','category','style','brands_id','sketch','photo'];
    /**
     * 关联用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(Users::class);
    }
    public function getPhotoAttribute($image)
    {
        return json_decode($image, true);
    }
    public function getSpecificationsAttribute($specifications)
    {
        $list = Category::where('id','=',$specifications)->first();
        return $list['name'];
    }
    public function getCategoryAttribute($category)
    {
        $list = Category::where('id','=',$category)->first();
        return $list['name'];
    }
    public function getStyleAttribute($style)
    {
        $list = Category::where('id','=',$style)->first();
        return $list['name'];
    }
    public function getBrandsIdAttribute($brands_id)
    {
        $list = Brand::where('id','=',$brands_id)->first();
        return $list['name'];
    }

    /**
     * int(0:待审核，1：审核通过，2：未通过)
     */
    public function getExamineAttribute($examine)
    {
        switch ($examine){
            case 0:
                return'待审核';
                break;
            case 1:
                return '通过';
                break;
            case 2:
                return '未通过';
                break;
        }
    }

    /**
     *单条查询
     * @param $id
     * @return int
     */
    public function sel_search_bricks_info($id)
    {
        $res = \DB::table('search_bricks')
            ->select(['specifications','category','style','brands_id','sketch','photo','created_at'])
            ->where('deleted_at','=',NULL)
            ->where('examine','=','1')
            ->where('id','=',$id)
            ->first();
        if(isset($res)){
            $res->specifications = $this->getSpecificationsAttribute($res->specifications);
            $res->category = $this->getCategoryAttribute($res->category);
            $res->style = $this->getStyleAttribute($res->style);
            $res->brands_id = $this->getBrandsIdAttribute($res->brands_id);
            $res->photo = json_decode_photo($res->photo);
            return $res;
        }
        else{
            return $res;
        }
    }

    /**
     * 列表查询
     */
    public function sel_search_bricks_list($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        if(isset($data['f'])){
            $res = \DB::table('search_bricks')
                ->select(['id','specifications','category','style','brands_id','sketch','photo'])
                ->where('users_id','=',$data['users_id'])
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            $res = \DB::table('search_bricks')
                ->select(['id','specifications','category','style','brands_id','sketch','photo'])
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }

        if(isset($res[0])){
            foreach ($res as $k=>$v)
            {
                $res[$k]->specifications = $this->getSpecificationsAttribute($res[$k]->specifications);
                $res[$k]->category = $this->getCategoryAttribute($res[$k]->category);
                $res[$k]->style = $this->getStyleAttribute($res[$k]->style);
                $res[$k]->brands_id = $this->getBrandsIdAttribute($res[$k]->brands_id);
                $res[$k]->photo = json_decode_photo($res[$k]->photo);
            }
        }
        return $res;
    }

    public function sel_search_bricks_page($data)
    {
        if(isset($data['f'])){
            return \DB::table('search_bricks')
                ->where('users_id','=',$data['users_id'])
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->count();
        }else{
            return \DB::table('search_bricks')
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->count();
        }

    }

    public function del_my_data($id)
    {
        $date = date('Y-m-d H:i:s');
        return \DB::table('search_bricks')
            ->where(['id'=>$id])
            ->update(['deleted_at'=>$date,'updated_at'=>$date]);
    }

}