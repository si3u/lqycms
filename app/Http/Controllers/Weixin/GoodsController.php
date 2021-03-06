<?php
namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Weixin\CommonController;
use Illuminate\Http\Request;
use App\Common\ReturnCode;

class GoodsController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }
	
    //商品详情
    public function goodsDetail($id)
	{
        $postdata = array(
            'id'  => $id
		);
        if(isset($_SESSION['weixin_user_info'])){$postdata['user_id']=$_SESSION['weixin_user_info']['id'];}
        $url = env('APP_API_URL')."/goods_detail";
		$res = curl_request($url,$postdata,'GET');
        $data['post'] = $res['data'];
        if(!$data['post']){$this->error_jump(ReturnCode::NO_FOUND,route('weixin'),3);}
        
        //添加浏览记录
        if(isset($_SESSION['weixin_user_info']))
        {
            $postdata = array(
                'goods_id'  => $id,
                'access_token' => $_SESSION['weixin_user_info']['access_token']
            );
            $url = env('APP_API_URL')."/user_goods_history_add";
            curl_request($url,$postdata,'POST');
        }
        
		return view('weixin.goods.goodsDetail', $data);
	}
    
    //商品列表
    public function goodsList(Request $request)
	{
        if($request->input('typeid', '') != ''){$data['typeid'] = $request->input('typeid');}
        if($request->input('tuijian', '') != ''){$data['tuijian'] = $request->input('tuijian');}
        if($request->input('keyword', '') != ''){$data['keyword'] = $request->input('keyword');}
        if($request->input('status', '') != ''){$data['status'] = $request->input('status');}
        if($request->input('is_promote', '') != ''){$data['is_promote'] = $request->input('is_promote');}
        if($request->input('orderby', '') != ''){$data['orderby'] = $request->input('orderby');}
        if($request->input('max_price', '') != ''){$data['max_price'] = $request->input('max_price');}else{$data['max_price'] = 99999;}
        if($request->input('min_price', '') != ''){$data['min_price'] = $request->input('min_price');}else{$data['min_price'] = 0;}
        
        //商品列表
        $postdata = array(
            'limit'  => 10,
            'offset' => 0
		);
        $url = env('APP_API_URL')."/goods_list";
		$goods_list = curl_request($url,$postdata,'GET');
        $data['goods_list'] = $goods_list['data']['list'];
        
		return view('weixin.goods.goodsList', $data);
	}
}