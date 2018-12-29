<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/14
 * Time: 16:43
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class JobRecruits extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['users_id','type','position','education','experience','experience_info','salary','station_time','sketch','contacts_name','contacts_tel','photo'];

    /**
     * 关联用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(Users::class);
    }

    public function getTypeAttribute($type)
    {
        if ($type == 1) {
            return '求职';
        } elseif ($type == 2) {
            return '招聘';
        }
    }

    /**
     * 0：其他，1：业务员，2：导购，
    3：经理，4：副总经理，5：总经理，6：财务，7：后勤
    8：行政，9：人事专员，职位
     * @param $position
     */
    public function getPositionAttribute($position)
    {
        switch ($position){
            case 0:
                return '其他';
                break;
            case 1:
                return '业务员';
                break;
            case  2:
                return '导购';
                break;
            case  3:
                return '经理';
                break;
            case  4:
                return '副总经理';
                break;
            case  5:
                return '总经理';
                break;
            case 6:
                return '财务';
                break;
            case  7:
                return '后勤';
                break;
            case  8:
                return '行政';
                break;
            case  9:
                return '人事专员';
                break;
            default:
                return $position;
                break;
        }

    }

    /**
     * 1：高中以下，2：高中，3：大专，
    4：本科，5：本科以上
     */
    public function getEducationAttribute($education)
    {
        switch ($education){
            case 1:
                return'高中以下';
                break;
            case 2:
                return '高中';
                break;
            case 3:
                return '大专';
                break;
            case 4:
                return '本科';
                break;
            case 5:
                return '本科以上';
                break;
        }


    }

    public function getExperienceAttribute($experience)
    {
        return $experience ? '有' : '否';
    }

    /**
     * 1:3000以下，2：3000~5000，3：5000~8000
    4：8000以上
     * @param $salary
     */
    public function getSalaryAttribute($salary)
    {
        switch ($salary){
            case 1:
                return'3000以下';
                break;
            case 2:
                return '3000~5000';
                break;
            case 3:
                return '5000~8000';
                break;
            case 4:
                return '8000以上';
                break;
        }
    }

    /**
     * 1：随时，2：1一周内
    3：一个月内
     */
    public function getStationTimeAttribute($station_time)
    {
        switch ($station_time){
            case 1:
                return'随时';
                break;
            case 2:
                return '一周内';
                break;
            case 3:
                return '一个月内';
                break;
        }
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

    public function getPhotoAttribute($photo)
    {
        return json_decode($photo, true);
    }

    /**
     * 根据id查询数据
     */
    public function sel_job_recruits_info($id)
    {
        $res = \DB::table('job_recruits')
            //->join('cities','cities.area_code','=','clearing_houses.room_city')
            ->select(['type','position','education','experience','experience_info','salary','station_time','sketch','contacts_name','contacts_tel','photo','created_at'])
            ->where('id',$id)
            ->where('deleted_at','=',NULL)
            ->where('examine','=','1')
            ->get();
        if(isset($res[0]))
        {
            $res[0]->photo = json_decode_photo($res[0]->photo);
            $res[0]->type = $this->getTypeAttribute($res[0]->type);
            $res[0]->position = $this->getPositionAttribute($res[0]->position);
            $res[0]->education = $this->getEducationAttribute($res[0]->education);
            $res[0]->experience = $this->getExperienceAttribute($res[0]->experience);
            $res[0]->salary = $this->getSalaryAttribute($res[0]->salary);
            $res[0]->station_time = $this->getStationTimeAttribute($res[0]->station_time);
        }
        $res = arr($res);
        return $res;
    }

    /**
     * 列表查询
     * 1时间先后2审核通过，3。城市范围4。数据条数
     */
    public function sel_job_recruits_list($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        if(isset($data['f'])){
            $res = \DB::table('job_recruits')
                ->select(['id','type','position','education','experience','experience_info','salary','station_time','sketch','contacts_name','contacts_tel','photo'])
                ->where('users_id','=',$data['users_id'])
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            $res = \DB::table('job_recruits')
                ->select(['id','type','position','education','experience','experience_info','salary','station_time','sketch','contacts_name','contacts_tel','photo'])
                ->where('type','=',$data['type'])
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
                $res[$k]->type = $this->getTypeAttribute($res[$k]->type);
                $res[$k]->position = $this->getPositionAttribute($res[$k]->position);
                $res[$k]->education = $this->getEducationAttribute($res[$k]->education);
                $res[$k]->experience = $this->getExperienceAttribute($res[$k]->experience);
                $res[$k]->salary = $this->getSalaryAttribute($res[$k]->salary);
                $res[$k]->station_time = $this->getStationTimeAttribute($res[$k]->station_time);
                $res[$k]->photo = json_decode_photo($res[$k]->photo);
            }
        }

        return $res;
    }

    /**
     * 总条数
     */
    public function sel_job_recruits_page($data)
    {
        if(isset($data['f'])){
            return \DB::table('job_recruits')
                ->where('users_id','=',$data['users_id'])
                ->where('deleted_at','=',NULL)
                ->where('examine','=','1')
                ->count();
        }
        return \DB::table('job_recruits')
            ->where('type','=',$data['type'])
            ->where('deleted_at','=',NULL)
            ->where('examine','=','1')
            ->count();
    }

    public function del_my_data($id)
    {
        $date = date('Y-m-d H:i:s');
        return \DB::table('job_recruits')
            ->where(['id'=>$id])
            ->update(['deleted_at'=>$date,'updated_at'=>$date]);
    }
}