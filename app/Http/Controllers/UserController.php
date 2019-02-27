<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expenses;
use App\ExpenseName;
use App\UserProfile;
use App\Ticket;
use App\ExpVoucher;
class UserController extends Controller
{
  public function getTransNums(Request $request){

     $expenses=Expenses::where('user_id',session('userid'))->get();
     return response()->json(['expenses'=>$expenses]);
  }

  public function saveExpForm(Request $request){
    $userid=session('userid');
    $expense=new Expenses;
    $imagedate=date("d-m-Y_H-i-s");
    $inputFileName = $request->file('file');

    $dir = 'assets/bills/';
    if($inputFileName!=null)
    {
      $path=$dir.$userid;
      if(!is_dir($path)){
         mkdir($path);
        }
  $extension = $request->file('file')->getClientOriginalExtension();
  $filename = $imagedate.'('.$request->expense_name.')'.'.'.$extension;
  $request->file('file')->move($path, $filename);
  $dir1 = 'assets/bills/'.$userid.'/';
  $pathfordb=$dir1.$filename;
    }

    $payment_date=date('Y-m-d',strtotime($request->payment_date));
    $cheq_date=date('Y-m-d',strtotime($request->cheq_date));
        $expense->user_id=$userid;
        $expense->fin_year=$request->fiscal_year;
        $expense->transaction_no=$request->trans_num;
        $expense->expense_name=$request->expense_name;
        $expense->paid_towards=$request->paid_towards;
        $expense->paid_amount=$request->paid_amt;
        $expense->payment_date=$payment_date;

        $expense->remarks=$request->remarks;
        $expense->payment_mode=$request->mode;
        $expense->bank_name=$request->bank_name;
        $expense->ifsc_code=$request->ifsc;
        $expense->acc_no=$request->account_no;
        $expense->credit_no=$request->credit_no;
        $expense->debit_no=$request->debit_no;
        $expense->cheque_no=$request->cheq_num;
        $expense->cheque_date=$cheq_date;
        //var_dump($expense);
       $expense->save();

       if($inputFileName!=null){
         $lastid=Expenses::where('user_id',$userid)->max('id');
         $voucher=new ExpVoucher;
         $voucher->expenses_id=$lastid;
         $voucher->voucher_path=$pathfordb;
         $voucher->save();
       }
       $message="Expense transaction saved successfully";
        //$data=$request->file('file');
        return response()->json(['msg'=>$message]);
  }
  public function getExpenseNames(Request $request){
    $expnames=ExpenseName::select('exp_name')->where('for_whom','general')->orwhere('for_whom',session('userid'))->get();
    return response()->json(['expnames'=>$expnames]);
  }


  public function editExpense($id){
    session(['id'=>$id]);
    $exp=null;
    $expenses=Expenses::where('id',$id)->get();
    foreach($expenses as $exp){
      $exp=$exp;
    }
    return response()->json(['exp'=>$exp]);
  }

  public function updateExpForm(Request $request){
    $payment_date=date('Y-m-d',strtotime($request->payment_date));
   $cheq_date=date('Y-m-d',strtotime($request->cheque_date));
   $data=$request->except('_token');
    $data['payment_date']=$payment_date;
    $data['cheque_date']=$cheq_date;
    $id=session('id');
    Expenses::where('user_id',session('userid'))->where('id',$id)->update($data);
    session()->forget('id');
    $message="Expense Updated successfully";
    return response()->json(['msg'=>$message]);
  }

  public function getUserExpenses(){
  
    $html = view('partials.dispUserExpenses',compact('userexpenses'))->render();
    return response()->json(['html'=>$html]);

  }

  public function deleteExpense($id){
    $datas=ExpVoucher::where('expenses_id',$id)->get();
    $userid=session('userid');
    foreach($datas as $data){
      $billpath=$data->voucher_path;
      if (file_exists($billpath))
      {
      unlink($billpath);
      }
    }
      Expenses::find($id)->delete();
      $html = view('partials.dispUserExpenses',compact('userexpenses'))->render();
      return response()->json(['html'=>$html]);
    }

public function showUserVoucher($expid){
  $datas=Expenses::where('id',$expid)->get();
  $exp_name=null;
  $voucherpath=array();
  $voucherid=array();
  foreach($datas as $data){

    $billpath=$data->vouchers;
    $exp_name=$data->expense_name;
    //echo $billpath;
    foreach($billpath as $path){
      array_push($voucherpath,$path->voucher_path);
      array_push($voucherid,$path->id);
    }
  }
    $html = view('partials.exp_vouchers',compact('voucherpath','expid','exp_name','voucherid'))->render();
    return response()->json(['html'=>$html]);
}

    public function saveTicket(Request $request){
       $ticket=new Ticket;
       $location=$request->input('location');
       $ticket->ticket_no=substr(strtoupper($location),0,2).date('Y');
       $ticket->user_id=session('userid');
       $ticket->expense_name=$request->input('expensenamelist');
       $ticket->description=$request->input('description');
       $ticket->location=$location;
       //$ticket->status=$request->input('status');
       $ticket->date=date('Y-m-d',strtotime($request->input('date')));
       $ticket->save();
       $msg="Ticket send successfully";
       return response()->json(['msg'=>$msg]);

    }

    public function dispTickets(){
      $html = view('partials.dispUserTickets',compact('tickets'))->render();
      return response()->json(['html'=>$html]);
    }

    public function updateTicketStatus(Request $request){
      $id=$request->id;
      $userid=$request->userid;
      $status=$request->status;
      $updatedBy=session('userid');
      Ticket::where('user_id',$userid)->where('id',$id)->update(['status'=>$status,'status_updated_by'=>$updatedBy]);
      $html = view('partials.dispUserTickets',compact('tickets'))->render();
      return response()->json(['html'=>$html]);
    }

    public function saveTicketRemark(Request $request){
      $id=$request->id;
      $remark=$request->remark;
      Ticket::where('id',$id)->update(['remark'=>$remark]);
      $remark=Ticket::where('id',$id)->value('remark');
      //$msg="Remark saved";
      return response()->json(['msg'=>$remark]);

    }

    public function uploadbill(Request $request){
      $imagedate=date("d-m-Y_H-i-s");
      $inputFileName = $request->file('file');
      $id=$request->id;
      $expname=$request->expname;
      $userid=session('userid');

      $dir = 'assets/bills/';
      if($inputFileName!=null)
      {
        $path=$dir.$userid;
        if(!is_dir($path)){
           mkdir($path);
          }
     $extension = $request->file('file')->getClientOriginalExtension();
    $filename = $imagedate.'('.$expname.')'.'.'.$extension;
    $request->file('file')->move($path, $filename);
    $dir1 = 'assets/bills/'.$userid.'/';
    $pathfordb=$dir1.$filename;
      }
      if($inputFileName!=null){
        $voucher=new ExpVoucher;
        $voucher->expenses_id=$id;
        $voucher->voucher_path=$pathfordb;
        $voucher->save();
      }
      $msg="Voucher uploaded successfully";
   return response()->json(['msg'=>$msg]);
    }

    public function deleteSelectedVoucher($imgid){
      $datas=ExpVoucher::find($imgid)->get();
      $userid=session('userid');
      foreach($datas as $data){
        $billpath=$data->voucher_path;
        //$date=$data->payment_date;
      }
      $filename = $billpath;
      $target_file=$filename;
      if (file_exists($target_file))
      {
      unlink($target_file);
      }

      ExpVoucher::find($imgid)->delete();
      return;
    }
}
