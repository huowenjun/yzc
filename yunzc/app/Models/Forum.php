<?php
/**
 * 论坛
 */
namespace App\Models;

use App\Http\Controllers\Api\Forum\CommentController;
use App\Http\Controllers\Api\Forum\ForumController;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    //
    protected $fillable = ['title','content','parent_type','type','photo','room_city','users_id'];
    public function contentable(){
        return $this->morphTo();
    }


    /**
     * 列表数据 主分类1热2品3地4主
     */
    public function sel_list($data)
    {
        $db = new \stdClass();
        $data['page'] = ($data['now_page']-1)*10;
        if(!isset($data['parent_type'])||!isset($data['type'])){
            $where = [
                'deleted_at'=>Null,
                'examine'=>'1'
            ];
            $db->list = \DB::table('forums')
                ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                ->where($where)
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
            $count = \DB::table('forums')
                ->where($where)
                ->count();
            $db->last_page = ceil($count/10);
            $db->current_page = intval($data['now_page']);
        }else{
            $where = [
                'parent_type'=>$data['parent_type'],
                'type'=>$data['type'],
                'deleted_at'=>Null,
                'examine'=>'1'
            ];
            switch ($data['parent_type']){
                case 2:
                    $db->list = \DB::table('forums')
                        ->select(['forums.id','title','photo','browse_num','replies_num','parent_type','type'])
                        ->where($where)
                        ->orderBy('forums.updated_at','desc')
                        ->offset($data['page'])
                        ->limit(10)
                        ->get();
                    $count = \DB::table('forums')->where($where)->count();
                    $db->last_page = ceil($count/10);
                    $db->current_page = intval($data['now_page']);
                    break;
                case 3:
                    $db->list = \DB::table('forums')
                        ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                        ->where($where)
                        ->orderBy('updated_at','desc')
                        ->offset($data['page'])
                        ->limit(10)
                        ->get();
                    $count = \DB::table('forums')
                        ->where($where)
                        ->count();
                    $db->last_page = ceil($count/10);
                    $db->current_page = intval($data['now_page']);
                    break;
                case 4:
                    $db->list = \DB::table('forums')
                        ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                        ->where($where)
                        ->orderBy('forums.updated_at','desc')
                        ->offset($data['page'])
                        ->limit(10)
                        ->get();
                    $count = \DB::table('forums')
                        ->where($where)
                        ->count();
                    $db->last_page = ceil($count/10);
                    $db->current_page = intval($data['now_page']);
                    break;
            }
        }

        if(isset($db->list[0]))
        {
            foreach ($db->list as $key=>$value)
            {
                $db->list[$key]->photo = json_decode_photo($db->list[$key]->photo);
                $db->list[$key]->photo_status = count($db->list[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
                $db->list[$key]->browse_num = num2tring($db->list[$key]->browse_num);
                $db->list[$key]->replies_num = num2tring($db->list[$key]->replies_num);
                $db->list[$key]->collection_yes_or_no = 1;//没有收藏
            }
            if(isset($data['api_token'])&&!empty($data['api_token'])){
                $obj = new ForumController();
                $redis = GetAppToken($data['api_token']);
                if($redis['code'] == 200){
                    $data['users_id'] = $redis['u_id'];
                }else{
                    return msg_err($redis['code'],$redis['msg']);
                }
                foreach ($db->list as $key=>$value)
                {
                    $a = $obj->collectionYesOrNo([
                        'id'=>$db->list[$key]->id,
                        'parent_type'=>$db->list[$key]->parent_type,
                        'type'=>$db->list[$key]->type,
                        'api_token'=>$data['api_token'],
                    ])->original;
                    $db->list[$key]->collection_yes_or_no = $a['data']->f;
                }
            }
        }
        return $db;
    }

    /**
     * 随机列表
     */
    public function random_list($data)
    {
        $db = \DB::table('forums')
            ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
            ->whereIn('id',$data['id'])
            ->get();
        if(isset($db[0]))
        {
            foreach ($db as $key=>$value)
            {
                $db[$key]->photo = json_decode_photo($db[$key]->photo);
                $db[$key]->photo_status = count($db[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
                $db[$key]->browse_num = num2tring($db[$key]->browse_num);
                $db[$key]->replies_num = num2tring($db[$key]->replies_num);
                $db[$key]->collection_yes_or_no = 1;//没有收藏
            }
            if(isset($data['api_token'])&&!empty($data['api_token'])){
                $obj = new ForumController();
                $redis = GetAppToken($data['api_token']);
                if($redis['code'] == 200){
                    $data['users_id'] = $redis['u_id'];
                }else{
                    return msg_err($redis['code'],$redis['msg']);
                }
                foreach ($db as $key=>$value)
                {
                    $a = $obj->collectionYesOrNo([
                        'id'=>$db[$key]->id,
                        'parent_type'=>$db[$key]->parent_type,
                        'type'=>$db[$key]->type,
                        'api_token'=>$data['api_token'],
                    ])->original;
                    $db[$key]->collection_yes_or_no = $a['data']->f;
                }
            }
        }
        return $db;
    }
    /**
     * 点击列表后详情数据
     */
    public function sel_show($data)
    {
        $db = new \stdClass();
        $where = [
            'forums.id'=>$data['id'],
            'forums.examine'=>'1'
        ];
        switch ($data['parent_type']){
            case 2:
                $db = \DB::table('forums')
                    ->select(['forums.id','title','name','photo','browse_num','replies_num','fabulous_num','content','parent_type','type','forums.created_at','forums.users_id'])
                    ->join('brands','brands.id','=','forums.type')
                    ->where($where)
                    ->first();
                break;
            case 3:
                $db = \DB::table('forums')
                    ->select(['forums.id','title','area_name as name','photo','browse_num','replies_num','fabulous_num','content','parent_type','type','forums.created_at','forums.users_id'])
                    ->join('cities','cities.area_code','=','forums.type')
                    ->where($where)
                    ->first();
                break;
            case 4:
                $db = \DB::table('forums')
                    ->select(['forums.id','title','name','photo','browse_num','replies_num','fabulous_num','content','parent_type','type','forums.created_at','forums.users_id'])
                    ->join('themes','themes.id','=','forums.type')
                    ->where($where)
                    ->first();
                break;
        }
        $obj = new ForumController();
        $commentM = new CommentController();
        if(isset($db))
        {
            $db->photo = json_decode_photo($db->photo);
            $db->photo_status = count($db->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
            $db->browse_num = num2tring($db->browse_num);
            $db->replies_num = num2tring($db->replies_num);
            $db->fabulous_num = num2tring($db->fabulous_num);
            $db->collection_yes_or_no = 1;//没有收藏
            $db->fabulous_yes_or_no = 1;//没有点赞
            $db->created_at = explode(' ',$db->created_at)[0];
            $db->comment = $commentM->showComment(['forum_id'=>$db->id,'api_token'=>isset($data['api_token'])?$data['api_token']:''])->original['data'];
        }
        if(isset($data['api_token'])&&$data['api_token']!=false){
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            $a = $obj->collectionYesOrNo([
                'id'=>$db->id,
                'parent_type'=>$db->parent_type,
                'type'=>$db->type,
                'api_token'=>$data['api_token'],
            ])->original;//收藏
            $b = $obj->fabulousYesOrNo([
                'id'=>$db->id,
                'parent_type'=>$db->parent_type,
                'type'=>$db->type,
                'api_token'=>$data['api_token'],
            ])->original;//点赞;
            $db->fabulous_yes_or_no = $b['data']->f;
            $db->collection_yes_or_no = $a['data']->f;
        }

        return $db;
    }

    /**
     * 管理论坛列表
     */
    public function sel_manage($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        $db = \DB::table('forums')
                        ->select(['id','title','photo','browse_num','replies_num','parent_type','content','type'])
                        ->where('users_id','=',$data['users_id'])
                        ->where('examine','=','1')
                        ->where('deleted_at','=',Null)
                        ->orderBy('updated_at','desc')
                        ->offset($data['page'])
                        ->limit(10)
                        ->get();
        if(isset($db[0]))
        {
            foreach ($db as $key=>$value)
            {
                $db[$key]->photo = json_decode_photo($db[$key]->photo);
                $db[$key]->photo_status = count($db[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
                $db[$key]->browse_num = num2tring($db[$key]->browse_num);
                $db[$key]->replies_num = num2tring($db[$key]->replies_num);
            }
        }
        return $db;
    }
    /**
     * 查看详情
     */
    public function sel_foeum_info($id)
    {
        $db = \DB::table('forums')
            ->select(['id','title','photo','browse_num','replies_num','parent_type','type','content','users_id'])
            ->where('id','=',$id)
            ->where('deleted_at','=',Null)
            ->get();
        return  arr($db);
    }

    /**
     * 总页码
     * @param $data
     * @return int
     */
    public function sel_page($data)
    {
        return  $db = \DB::table('forums')
            ->where('users_id','=',$data['users_id'])
            ->where('deleted_at','=',Null)
            ->count();
    }

    /**
     * 软删除用户论坛数据
     */
    public function del_data($data)
    {
        return  $db = \DB::table('forums')
                        ->where('id','=',$data['id'])
                        ->where('users_id','=',$data['users_id'])
                        ->update(['deleted_at'=>date('Y-m-d H:i:s')]);
    }

    /**
     * 阅读量+1
     */
    public function inc($data)
    {
        return \DB::table('forums')
            ->where('id','=',$data['id'])
            ->increment('browse_num');
    }
    /**
     * 收藏量+1
     */
    public function collection_inc($data)
    {
        return \DB::table('forums')
            ->where('id','=',$data['id'])
            ->increment('collection_num');
    }

    /**
     * 收藏-1
     */
    public function collection_dinc($data)
    {
        return \DB::table('forums')
            ->where('id','=',$data['id'])
            ->decrement('collection_num');
    }

    /**
     * 点赞+1
     */
     public function fabulous_inc($data)
     {
         return \DB::table('forums')
             ->where('id','=',$data['id'])
             ->increment('fabulous_num');
     }

    /**
     * 点赞-1
     */
    public function fabulous_dinc($data)
    {
        return \DB::table('forums')
            ->where('id','=',$data['id'])
            ->decrement('fabulous_num');
    }

    /**
     * 常去论坛的数据处理
     * @param $data parent_type   type
     * 1热2品3地4主
     */
    public function sel_offten_go($data)
    {
        $db = new \stdClass();
        switch ($data[0]){
            case 1:
                break;
            case 2:
                $db = \DB::table('brands')
                    ->select(['id','name'])
                    ->where('id','=',$data[1])
                    ->where('status','=','1')
                    ->first();
                break;
            case 3:
                $db = \DB::table('cities')
                    ->select(['id','area_name as name'])
                    ->where('area_code','=',$data[1])
                    ->where('deleted_at','=',Null)
                    ->first();
                break;
            case 4:
                $db = \DB::table('themes')
                    ->select(['id','name'])
                    ->where('id','=',$data[1])
                    ->where('deleted_at','=',Null)
                    ->first();
                break;
        }
        return $db;
    }

    /**
     * 回帖+1
     */
    public function replies_inc($id)
    {
        return \DB::table('forums')
            ->where('id','=',$id)
            ->increment('replies_num');
    }

    /**
     * 单篇论坛回帖总数
     */
    public function replies_num($data){
        return \DB::table('forums')
            ->select(['replies_num'])
            ->where('id','=',$data['forum_id'])
            ->first();
    }

    /**
     * 热门论坛数据
     */
    public function hot_forum($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        $where = [
            'deleted_at'=>Null,
            'examine'=>'1',
            'status'=>1
        ];
        $db = \DB::table('forums')
            ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
            ->where($where)
            ->orderBy('updated_at','desc')
            ->offset($data['page'])
            ->limit(10)
            ->get();
        if(!isset($db[0])){
            return new \stdClass();
        }
        foreach ($db as $key=>$value)
        {
            $db[$key]->photo = json_decode_photo($db[$key]->photo);
            $db[$key]->photo_status = count($db[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
            $db[$key]->browse_num = num2tring($db[$key]->browse_num);
            $db[$key]->replies_num = num2tring($db[$key]->replies_num);
            $db[$key]->collection_yes_or_no = 1;//没有收藏
        }
        if(isset($data['api_token'])&&!empty($data['api_token'])){
            $obj = new ForumController();
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            foreach ($db as $key=>$value)
            {
                $a = $obj->collectionYesOrNo([
                    'id'=>$db[$key]->id,
                    'parent_type'=>$db[$key]->parent_type,
                    'type'=>$db[$key]->type,
                    'api_token'=>$data['api_token'],
                ])->original;
                $db[$key]->collection_yes_or_no = $a['data']->f;
            }
        }
        return $db;
    }

    public function hot_count()
    {
        $where = [
            'deleted_at'=>Null,
            'examine'=>'1',
            'status'=>1
        ];
        $db = \DB::table('forums')
            ->where($where)
            ->count();
        return $db;
    }

    public function f_count()
    {
        $where= [
            'deleted_at'=>Null,
            'examine'=>'1'
        ];
        $db = \DB::table('forums')
            ->select(['id','parent_type'])
            ->where($where)
            ->get();
        return $db;
    }

    /**
     * 论坛搜索
     * @param $data
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Collection
     */
    public function search_data($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        if(!isset($data['key']))
        {
            $db =  \DB::table('forums')
                ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                ->where('deleted_at','=',Null)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            $db = \DB::table('forums')
                ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                ->where('title','like','%'.$data['key'].'%')
                ->where('deleted_at','=',Null)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }
        foreach ($db as $key=>$value)
        {
            $db[$key]->photo = json_decode_photo($db[$key]->photo);
            $db[$key]->photo_status = count($db[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
            $db[$key]->browse_num = num2tring($db[$key]->browse_num);
            $db[$key]->replies_num = num2tring($db[$key]->replies_num);
            $db[$key]->collection_yes_or_no = 1;//没有收藏
        }
        if(isset($data['api_token'])&&!empty($data['api_token'])){
            $obj = new ForumController();
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            foreach ($db as $key=>$value)
            {
                $a = $obj->collectionYesOrNo([
                    'id'=>$db[$key]->id,
                    'parent_type'=>$db[$key]->parent_type,
                    'type'=>$db[$key]->type,
                    'api_token'=>$data['api_token'],
                ])->original;
                $db[$key]->collection_yes_or_no = $a['data']->f;
            }
        }
        return $db;
    }

    public function search_count($search_key)
    {
        if(!isset($search_key))
        {
            return \DB::table('forums')
                ->select(['id','parent_type'])
                ->where('title','like','%'.$search_key.'%')
                ->where('deleted_at','=',Null)
                ->where('examine','=','1')
                ->count();
        }
        return \DB::table('forums')
            ->select(['id','parent_type'])
            ->where('title','like','%'.$search_key.'%')
            ->where('deleted_at','=',Null)
            ->where('examine','=','1')
            ->count();
    }

    public function search_datas($data)
    {
        if(!isset($data['key']))
        {
            return new \stdClass();
        }else{
            $db = \DB::table('forums')
                ->select(['id','title','photo','browse_num','replies_num','parent_type','type'])
                ->where('title','like','%'.$data['key'].'%')
                ->where('deleted_at','=',Null)
                ->where('examine','=','1')
                ->orderBy('updated_at','desc')
                ->get();
        }
        foreach ($db as $key=>$value)
        {
            $db[$key]->photo = json_decode_photo($db[$key]->photo);
            $db[$key]->photo_status = count($db[$key]->photo)>=3?2:1;//统计图片数量，大于等于3，返回2，小于3返回1
            $db[$key]->browse_num = num2tring($db[$key]->browse_num);
            $db[$key]->replies_num = num2tring($db[$key]->replies_num);
            $db[$key]->collection_yes_or_no = 1;//没有收藏
        }
        if(isset($data['api_token'])&&!empty($data['api_token'])){
            $obj = new ForumController();
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            foreach ($db as $key=>$value)
            {
                $a = $obj->collectionYesOrNo([
                    'id'=>$db[$key]->id,
                    'parent_type'=>$db[$key]->parent_type,
                    'type'=>$db[$key]->type,
                    'api_token'=>$data['api_token'],
                ])->original;
                $db[$key]->collection_yes_or_no = $a['data']->f;
            }
        }
        return $db;
    }

}
