<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Expenses;
use App\UserProfile;
use PdfReport;
use ExcelReport;
class ExpReportController extends Controller
{
public function expPaymentModeRecords(Request $request){
    $from_date=$this->convertDate($request->from_date);
    $to_date=$this->convertDate($request->to_date);
    $cardtype=$request->cardtype;
    $records=$this->cardRecords($from_date,$to_date,$cardtype);
    $voucherpath=$this->getVoucherPath($records);
      $isshow=true;
      $html = view('partials.Reports.dispCardWiseExpRecords',compact('records','from_date','to_date','cardtype','isshow','voucherpath'))->render();
    return response()->json(['html'=>$html]);
}

public function expDateRecords(Request $request){
    $from_date=$this->convertDate($request->input('from_date'));
    $to_date=$this->convertDate($request->input('to_date'));
    $records=null;
    $voucherpath=null;
    $model=new Expenses;
    $isshow=true;
    if(session('adminsection')){
    $records=$model->hydrate(DB::select("CALL getDetailedExpensesforAdmin('$from_date','$to_date')"));
    $count=count($records);
    $records1=[];
    for($i=0;$i <$count;$i++){
    $invarray=Expenses::whereBetween('payment_date', array($from_date, $to_date))->where('expense_name',$records[$i]->expense_name)->get();
    array_push($records1,json_decode($invarray));
    $voucherpath=$this->getVoucherPath($invarray);
  }
    }
    else{
      $userid=session('userid');
      $records=$model->hydrate(DB::select("CALL getDetailedExpensesForUser('$userid','$from_date','$to_date')"));
      $count=count($records);
      $records1=[];
      for($i=0;$i <$count;$i++){
      $invarray=Expenses::where('user_id',$userid)->whereBetween('payment_date', array($from_date, $to_date))->where('expense_name',$records[$i]->expense_name)->get();
      array_push($records1,json_decode($invarray));
      $voucherpath=$this->getVoucherPath($invarray);
    }

}
$html = view('partials.Reports.dispDetailedExpRecords',compact('records','records1','from_date','to_date','cardtype','isshow','voucherpath'))->render();
return response()->json(['html'=>$html]);
}

public function expUserRecords(Request $request){
    $from_date=$this->convertDate($request->input('from_date'));
    $to_date=$this->convertDate($request->input('to_date'));
    $userid=$request->userid;
    $records=null;
    $voucherpath=null;
    $isshow=true;
    $model=new Expenses;
    if(session('adminsection')){
      $records=$model->hydrate(DB::select("CALL getDetailedExpensesForUser('$userid','$from_date','$to_date')"));
      $voucherpath=$this->getVoucherPath($records);
     }
  else{
    $userid=session('userid');
    $records=$model->hydrate(DB::select("CALL getDetailedExpensesForUser('$userid','$from_date','$to_date')"));
    $voucherpath=$this->getVoucherPath($records);

    }
    $html = view('partials.Reports.dispUserWiseExpRecords',compact('records','from_date','to_date','userid','isshow','voucherpath'))->render();
    return response()->json(['html'=>$html]);
}

public function expPaymentModeReport($from_date1,$to_date1,$cardtype,$type){
  $from_date=$this->convertDate($from_date1);
  $to_date=$this->convertDate($to_date1);
  $reporttype=$type;
  $title = 'Expense Report - PaymentModeWise';
  $meta = [ // For displaying filters description on header
        'Date' => $from_date1 . ' To ' . $to_date1,
        'Mode of Payment'=>$cardtype
    ];
    if(session('adminsection')){
    $queryBuilder =Expenses::select(['user_id','expense_name','paid_towards','paid_amount','payment_date'])->where('payment_mode',$cardtype)->whereBetween('payment_date', array($from_date, $to_date));
    }else{
      $queryBuilder =Expenses::select(['user_id','expense_name','paid_towards','paid_amount','payment_date'])->where('payment_mode',$cardtype)->where('user_id',session('userid'))->whereBetween('payment_date', array($from_date, $to_date));
     }
     $columns = [ // Set Column to be displayed
             'User Id' => 'user_id',
             'Expense Name'=>'expense_name',
             'Paid Towards'=>'paid_towards',
             'Payment Date'=>'payment_date',
             'Amount Paid'=>'paid_amount'
         ];
         if($reporttype=="pdf"){
                    return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(cardwise)-'.date('d-m-Y'));
                  }
         else if($reporttype=="excel"){
                    return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(cardwise)-'.date('d-m-Y'));
                  }

}

public function expUserReport($from_date1,$to_date1,$userid,$type){
  $from_date=$this->convertDate($from_date1);
  $to_date=$this->convertDate($to_date1);
  $reporttype=$type;
  $title = 'Expense Report - UserWise';
  $meta = [ // For displaying filters description on header
        'Date' => $from_date1 . ' To ' . $to_date1,
        'User Id'=>$userid
    ];
    if(session('adminsection')){
    $queryBuilder =Expenses::select(['expense_name','paid_towards','paid_amount','payment_date','payment_mode'])->where('user_id',$userid)->whereBetween('payment_date', array($from_date, $to_date));
    }else{
      $queryBuilder =Expenses::select(['expense_name','paid_towards','paid_amount','payment_date','payment_mode'])->where('user_id',session('userid'))->whereBetween('payment_date', array($from_date, $to_date));
     }
     $columns = [ // Set Column to be displayed
             'Expense Name'=>'expense_name',
             'Paid Towards'=>'paid_towards',
             'Payment Date'=>'payment_date',
             'Payment Mode'=>'payment_mode',
             'Amount Paid'=>'paid_amount'
         ];
         if($reporttype=="pdf"){
                    return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(userwise)-'.date('d-m-Y'));
                  }
         else if($reporttype=="excel"){
                    return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(userwise)-'.date('d-m-Y'));
                  }
}

public function expDateReport($from_date1,$to_date1,$type){
  $from_date=$this->convertDate($from_date1);
  $to_date=$this->convertDate($to_date1);
  $reporttype=$type;
  $title = 'Expense Report - DateWise';
  $meta = [ // For displaying filters description on header
        'Date' => $from_date1 . ' To ' . $to_date1
    ];
    if(session('adminsection')){
    $queryBuilder =Expenses::select(['user_id','expense_name','paid_towards','paid_amount','payment_date','payment_mode'])->whereBetween('payment_date', array($from_date, $to_date));
    }else{
      $queryBuilder =Expenses::select(['user_id','expense_name','paid_towards','paid_amount','payment_date','payment_mode'])->where('user_id',session('userid'))->whereBetween('payment_date', array($from_date, $to_date));
     }
     $columns = [ // Set Column to be displayed
             'User Id'=>'user_id',
             'Expense Name'=>'expense_name',
             'Paid Towards'=>'paid_towards',
             'Payment Date'=>'payment_date',
             'Payment Mode'=>'payment_mode',
             'Amount Paid'=>'paid_amount'
         ];
         if($reporttype=="pdf"){
                    return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(datewise)-'.date('d-m-Y'));
                  }
         else if($reporttype=="excel"){
                    return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                    ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        'Amount Paid' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                    ])
                    ->limit(20)
                    ->download('ExpenseReport(datewise)-'.date('d-m-Y'));
                  }

}



public function cardRecords($from_date,$to_date,$cardtype){
  $records=null;
  $model=new Expenses;
  if(session('adminsection')){

  $records=$model->hydrate(DB::select("CALL getExpensesOnModeOfPayment('$cardtype','$from_date','$to_date')"));
  }else{
    $userid=session('userid');
    $records=$model->hydrate(DB::select("CALL getExpensesForUserOnModeOfPayment('$userid','$cardtype','$from_date','$to_date')"));
   }
   return $records;
}

public function getVoucherPath($records){
  $voucherpath=array();
  foreach($records as $data){
    $billpath=$data->vouchers;
    foreach($billpath as $path){
      array_push($voucherpath,$path->voucher_path);
    }
  }
  return $voucherpath;
}
}
