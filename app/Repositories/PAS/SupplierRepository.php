<?php

namespace App\Repositories\PAS;
use App\Models\PAS\Purchase\PaymentOrder;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\Supplier;
use App\Repositories\RpcRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Constant\ConstFile;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *供应商
 * @package namespace App\Repositories;
 */
class SupplierRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Supplier::class;
    }

    /*
     * 2019-05-08
     * 添加供应商
     */
    public function setAdd($user,$arr){
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(!empty($arr['type']) && isset($arr['type'])){
            $data['type']=intval($arr['type']);
        }
        if(empty($arr['code'])){
            return returnJson($message='编号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['code']=trim($arr['code']);

        if(empty($arr['title'])){
            return returnJson($message='供应商名称不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!preg_match('/^[\x7f-\xff]+$/', trim($arr['title']))){
            return returnJson($message='供应商名称只能是中文',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!empty($arr['mnemonic']) && isset($arr['mnemonic'])){
            $data['mnemonic']=trim($arr['mnemonic']);
        }
        if(!empty($arr['address']) && isset($arr['address'])){
            $data['address']=trim($arr['address']);
        }
        if(!empty($arr['tel']) && isset($arr['tel'])){
            $data['tel']=trim($arr['tel']);
        }
        if(!empty($arr['opening_bank']) && isset($arr['opening_bank'])){
            $data['opening_bank']=trim($arr['opening_bank']);
        }
        if(!empty($arr['number']) && isset($arr['number'])){
            $data['number']=trim($arr['number']);
        }
        if(!empty($arr['corporations']) && isset($arr['corporations'])){
            $data['corporations']=trim($arr['corporations']);
        }
        if(!empty($arr['contacts']) && isset($arr['contacts'])){
            $data['contacts']=trim($arr['contacts']);
        }
        if(!empty($arr['phone']) && isset($arr['phone'])){
            $data['phone']=trim($arr['phone']);
        }
        if(!empty($arr['zip_code']) && isset($arr['zip_code'])){
            $data['zip_code']=trim($arr['zip_code']);
        }
        if(!empty($arr['email']) && isset($arr['email'])){
            $data['email']=trim($arr['email']);
        }
        if(!empty($arr['remark']) && isset($arr['remark'])){
            $data['remark']=trim($arr['remark']);
        }
        if(!empty($arr['fax']) && isset($arr['fax'])){
            $data['fax']=trim($arr['fax']);
        }
        $where['code']=trim($arr['code']);
        $where['status']=1;

        $count = Supplier::where($where)->count();
        if($count){
            return returnJson($message='供应商编号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['title']=trim($arr['title']);
        $data['user_id']=$user_id;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        //dd($data);
        $n = Supplier::insert($data);
        if($n){
            return returnJson($message='供应商添加成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='供应商添加失败',$code=ConstFile::API_RESPONSE_FAIL);
    }


    /*
     * 2019-05-08
     * 添加供应商
     */
    public function setUpdate($user,$arr){
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(!empty($arr['type']) && isset($arr['type'])){
            $data['type']=intval($arr['type']);
        }
        if(empty($arr['id'])){
            return returnJson($message='供应商id不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!empty($arr['address']) && isset($arr['address'])){
            $data['address']=trim($arr['address']);
        }
        if(!empty($arr['tel']) && isset($arr['tel'])){
            $data['tel']=trim($arr['tel']);
        }
        if(!empty($arr['opening_bank']) && isset($arr['opening_bank'])){
            $data['opening_bank']=trim($arr['opening_bank']);
        }
        if(!empty($arr['number']) && isset($arr['number'])){
            $data['number']=trim($arr['number']);
        }
        if(!empty($arr['corporations']) && isset($arr['corporations'])){
            $data['corporations']=trim($arr['corporations']);
        }
        if(!empty($arr['contacts']) && isset($arr['contacts'])){
            $data['contacts']=trim($arr['contacts']);
        }
        if(!empty($arr['phone']) && isset($arr['phone'])){
            $data['phone']=trim($arr['phone']);
        }
        if(!empty($arr['zip_code']) && isset($arr['zip_code'])){
            $data['zip_code']=trim($arr['zip_code']);
        }
        if(!empty($arr['email']) && isset($arr['email'])){
            $data['email']=trim($arr['email']);
        }
        if(!empty($arr['remark']) && isset($arr['remark'])){
            $data['remark']=trim($arr['remark']);
        }
        if(!empty($arr['fax']) && isset($arr['fax'])){
            $data['fax']=trim($arr['fax']);
        }
        $where['id']=intval($arr['id']);
        $where['status']=1;

        $data['user_id']=$user_id;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        //dd($data);
        $n = Supplier::where($where)->update($data);
        if($n){
            return returnJson($message='供应商修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='供应商修改失败',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /*
     * 2019-05-08
     * 获取供应商编号
     */
    public function getCode()
    {
        $where[]=['code','like','SUP%'];
        $code = Supplier::where($where)->orderBy('id','desc')->value('code');
        //echo $code;die;
        if(!$code){
            $codes = 'SUP100001';
        }else{
            $code =substr($code,3,6);
            $codes = 'SUP'.(intval($code)+1);
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }
    /*2019-05-08
     * 中文字符转数组
     */
    public function chTowarr($arr)
    {
        $str=preg_replace("/\d|\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\||\s/","",$arr['title']);

        if(!preg_match('/^[\x7f-\xff]|[a-z]|[A-Z]+$/', $str)){
            return returnJson($message='供应商名称只能是中文',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $length = mb_strlen($str, 'utf-8');
        $codes='';
        for ($i=0; $i<$length; $i++){
            $strs = mb_substr($str, $i, 1, 'utf-8');
            $codes .=$this->getFirstCharter($strs);//获取中文字符的首字母
        }

        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }
    /*2019-05-08
     * 获取中文字符的首字母
     */
    function getFirstCharter($str){
        if(empty($str)){return '';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }
    /*2019-05-08
    * 获取供应商详情
    */
    public function getInfo($user,$arr)
    {
        $where['id']= $arr['id'];
        $where['status']= 1;

        $info = Supplier::where($where)->first();
        if($info){
            $info=$info->toArray();
        }else{
            $info=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$info);
    }

    /*2019-05-08
    * 获取供应商列表
    */
    public function getList($user)
    {
        $company_id=$user->company_id;
        $list = app()->make(RpcRepository::class)->getCustomerListByCompanyId($company_id,2);
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }


    /*2019-05-08
   * 获取供应商列表
   */
    public function getListOne($user,$arr)
    {
        $where['ctype']= 1;
        $where['status']= 1;
        $list = Supplier::where($where)->get(['id','code','title']);

        if($list){
            $list=$list->toArray();
        }else{
            $list=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }

    /**
     * 供应商对账统计
     */
    public function Statistical($user,$arr)
    {

        if(!empty($arr['title']) && isset($arr['title'])){
            $where[]=['a.supplier_name','like','%'.trim($arr['title']).'%'];
        }
        if(!empty($arr['start']) && isset($arr['start'])){
            $where[]=['a.updated_at','>=',trim($arr['start']).' 00:00:00'];
        }
        if(!empty($arr['end']) && isset($arr['end'])){
            $where[]=['a.updated_at','<=',trim($arr['end']).' 23:59:59'];
        }
        $where[]=['a.status','=',5];
        $list=DB::table('pas_purchase as a')
            ->leftJoin('pas_supplier as b','a.supplier_id','=','b.id')
            ->where($where)
            ->groupBy('b.id')
            ->select(['a.id as a_id','b.id','b.title','b.contacts','b.phone'])->paginate(10);

        if($list){
            $list=$list->toArray();
            //dd($list);
            $purchaseamount=DB::table('pas_purchase as a')->where($where)->sum('turnover_amount');//总金额
            $purchasearnest = DB::table('pas_purchase as a')->where($where)->sum('earnest_money');//总定金额
            $where[]=['a.status','=',4];
            $return_order = DB::table('pas_return_order as a')->where($where)->sum('money');//总退金额
            $payment_order = DB::table('pas_payment_order as a')->where($where)->sum('money');//总付款金额
            $list['to_sum']=$purchaseamount - $purchasearnest-$return_order - $payment_order;
            foreach($list['data'] as $key=>&$value){
                $wheres['supplier_id']=$value->id;
                if(!empty($arr['start']) && isset($arr['start'])){
                    $wheres[]=['updated_at','>=',trim($arr['start']).' 00:00:00'];
                }
                if(!empty($arr['end']) && isset($arr['end'])){
                    $wheres[]=['updated_at','<=',trim($arr['end']).' 23:59:59'];
                }
                $turnover_amount =  Purchase::where($wheres)->where('status','=', '5')->sum('turnover_amount');//总金额
                $earnest_money = Purchase::where($wheres)->where('status','=', '5')->sum('earnest_money');//总定金额

                $ReturnOrder =  ReturnOrder::where($wheres)->where('status','=', '4')->sum('money');//总退金额

                //付款金额
                $PaymentOrder = PaymentOrder::where($wheres)->where('status','=', '4')->sum('money');

                //累计采购金额
                $value->turnover_amount=$turnover_amount;

                //累计已付款金额
                $value->already_amount=$earnest_money+$PaymentOrder+$ReturnOrder;

                //累计未付款金额
                $value->nalready_amount=$turnover_amount - $value->already_amount;
            }

            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
        }else{
            $list=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }


    //采购统计
    Public function ProcurementStatistics($user,$arr){
        $type=0;
        if(!empty($arr['type']) && isset($arr['type'])){
            $type=intval($arr['type']);
        }
        $types=0;
        if(!empty($arr['types']) && isset($arr['types'])){
            $types=intval($arr['types']);
        }

        if($type==1){
            $startTime = date('Y-m-d').' 00:00:00';
            $endTime =date('Y-m-d').' 23:59:59';

            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }elseif ($type==2){//本周时间
            $startTime = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)).' 00:00:00';
            $endTime = date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)).' 23:59:59'; //同样使用w,以现在与周日相关天数算

            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }elseif ($type==3){//本月时间
            $startTime = date('Y-m-01', strtotime(date("Y-m-d"))).' 00:00:00';
            $endTime=date('Y-m-d', strtotime("$startTime +1 month -1 day")).' 23:59:59';

            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }elseif ($type==4){//本季度时间
            $season = ceil(date('m') /3); //获取月份的季度
            $startTime = date('Y-m-01',mktime(0,0,0,($season - 1) *3 +1,1,date('Y'))).' 00:00:00';
            //echo $BeginDate;
            $endTime=date('Y-m-t',mktime(0,0,0,$season * 3,1,date('Y'))).' 23:59:59';

            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }elseif ($type==5){//本年时间
            $smonth = 1;
            $emonth = 12;
            $startTime =date('Y').'-'.$smonth.'-1 00:00:00';
            $em = date('Y').'-'.$emonth.'-1 23:59:59';
            $endTime = date('Y-m-t H:i:s',strtotime($em));

            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }elseif($type==6){
            if(!empty($arr['start']) && isset($arr['start'])){
                $startTime=trim($arr['start']).' 00:00:00';
                $where[]=['updated_at','>=',trim($arr['start'])];
            }
            if(!empty($arr['end']) && isset($arr['end'])){
                $endTime =trim($arr['end']).' 23:59:59';
                $where[]=['updated_at','<=',trim($arr['end'])];
            }
        }else{
            $startTime = date('Y-m-d').' 00:00:00';
            $endTime =date('Y-m-d').' 23:59:59';
            $where[]=['updated_at','>=',$startTime];
            $where[]=['updated_at','<=',$endTime];
        }

        if($types==1){
            $list=   Purchase::where($where)->where('status',5)->groupBy('supplier_id')
                ->selectRaw('sum(number) as number,sum(total_sum) as total_sum,supplier_id,supplier_name')->get();


        }elseif ($types==2){
            $list=   Purchase::where($where)->where('status',5)->groupBy('days')
                ->selectRaw('sum(number) as number,sum(total_sum) as total_sum,date_format(updated_at,"%Y-%c-%d") as days')->get();
        }else{
            $wheres[]=['b.updated_at','>=',$startTime];
            $wheres[]=['b.updated_at','<=',$endTime];
            $list =  DB::table('pas_purchase_commodity_content as a')
                ->leftJoin('pas_purchase as b','a.p_id','=','b.id')
                ->where($wheres)
                ->where('b.status',5)
                ->groupBy('a.sku')
                ->selectRaw('a.goods_name,sum(a.number) as number,sku,sum(a.price) as price,sum(a.money) as money')
                ->get();
        }
        if($list){
            $datas= $list->toArray();
            $lists['sum']=Purchase::where($where)->where('status',5)
                ->selectRaw('sum(number) as number,sum(total_sum) as total_sum')->first();
            $lists['data']=$datas;
        }else{
            $lists=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$lists);
    }
}
