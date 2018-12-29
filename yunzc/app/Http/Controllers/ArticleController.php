<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use Carbon\Carbon;
use chuanglan\ChuanglanSmsApi;
use Illuminate\Http\Request;
use App\Article;
use Overtrue\Pinyin\Pinyin;

class ArticleController extends Controller
{
    public function index()
    {



        \DB::connection()->enableQueryLog();
        set_time_limit(600);
        $pinyin = new Pinyin();
        $areas = new Area();
        $areas->orderBy('id','desc')->chunk(100,function($datas) use ($areas,$pinyin){

            $rr = [];
            foreach ($datas as $v){
                $rr['letter'] = implode(' ',$pinyin->convert($v->area_name));

                $rr['id'] = $v->id;

                $areas->where('id',$rr['id'])->update($rr);
//                $queries = \DB::getQueryLog();


            }
        });



        $cities = new City();
       $areas->where('level',2)->orWhere(function($query){
          $query->where('level',1)->where('city_code','<>','[]');
       })->chunk(100,function($datas) use ($areas,$cities){

           $rrs = [];

           foreach ($datas as $data) {
               $rr['area_name'] = $data->area_name;
               $rr['level'] = $data->level;
               $rr['city_code'] = $data->city_code;
               $rr['area_code'] = $data->area_code;
               $rr['center'] = $data->center;
               $rr['parent_id'] = $data->parent_id;
               $rr['letter'] = $data->letter;
               $rr['initial'] = substr($data->letter,0,1);
               $rrs[] = $rr;
           }
           $cities->insert($rrs);


       });
        $queries = \DB::getQueryLog();

        dd($queries);

        echo 11111;



    /*  $set = new ChuanglanSmsApi();

      $rr = $set->sendSMS('15866609515','哈哈哈哈哈，你好啊');

      dd($rr);*/


    echo Carbon::now().'<hr>';
    echo Carbon::now()->addMinutes(10);





//        return Article::all();
    }

    public function show($id)
    {
        return Article::find($id);
    }

    public function store(Request $request)
    {
        return Article::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());

        return $article;
    }

    public function delete(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return 204;
    }
}