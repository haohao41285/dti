<?php
namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class ThemeMailPresenter extends Presenter{

	public function getThemeMail(){

	    $combo_service_html = "";
	    $image_check_1 = "";
	    $image_check_2 = "";

	    if($this->csb_card_number != ""){
	        $image_check_1 = "<img src='".public_path('images/green_mark.png')."'>";
	    }
        if($this->routing_number != ""){
            $image_check_2 = "<img src='".public_path('images/green_mark.png')."'>";
        }

	    foreach($this->combo_service_list as $combo_service){
	        $combo_service_html .= '
	         <tr>
                    <td style="border: 1px solid black;">'.$combo_service->cs_name.'</td>
                    <td style="text-align: right;border: 1px solid black;">1</td>
                    <td style="text-align: right;border: 1px solid black;">'.$combo_service->cs_price.'</td>
                    <td style="text-align: right;border: 1px solid black;">'.$combo_service->cs_price.'</td>
                </tr> 
	        ';
        }
		return "<div style='background: url(".public_path('images/logo_email_3.png').") 0 0 repeat;margin: 0px 0px 100px'>
                    <div style=\"border-bottom: 2px black solid\">
                        <span><img src='".public_path('images/logo274x29.png')."'></span>
                        <span style=\"float: right\"><b>INVOICE</b></span>
                    </div>
                    <div>
                        <span style=\"width: 50%;float:left\">
                            10161 Bolsa Ave, Ste 207A<br>
                            Westminster, CA 92683<br>
                            Email: info@dataeglobal.com<br>
                            Phone: (888) 840-8070
                        </span>
                        <span style=\"float: right\">
                            Invoice Number: #".$this->id."<br>
                            Order Date: ".format_date($this->created_at)."
                        </span>
                    </div>
                    <br>
                    <div style=\"margin-top: 100px\">
                        <table style=\"border-collapse: collapse;width: 100%;\">
                            <thead>
                            <tr>
                                <th style=\"text-align: center;border: 1px solid black;background-color: #b6b6b6;width: 50%\"><b>Bill to</b></th>
                                <th style=\"text-align: center;border: 1px solid black;background-color: #b6b6b6;width: 50%\"><b>Ship to</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style=\"border: 1px solid black;text-align: center\">
                                    ".$this->getPlace->place_name."<br>
                                    ".$this->getPlace->place_address."<br>
                                    <b>Attn:".$this->getCustomer->customer_firstname." ".$this->getCustomer->customer_lastname."</b>
                                </td>
                                <td style=\"border: 1px solid black;text-align: center\">
                                    ".$this->getPlace->place_name."<br>
                                    ".$this->getPlace->place_address."<br>
                                    <b>Attn:".$this->getCustomer->customer_firstname." ".$this->getCustomer->customer_lastname."</b>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style=\"margin-top: 30px\">
                        <table style=\"border-collapse: collapse;width: 100%\">
                            <thead>
                            <tr>
                                <th style=\"text-align: left;border: 1px solid black;background-color: #b6b6b6;width: 50%\"><b>Item Service</b></th>
                                <th style=\"text-align: center;border: 1px solid black;background-color: #b6b6b6;width: 10%\"><b>Quantity</b></th>
                                <th style=\"text-align: center;border: 1px solid black;background-color: #b6b6b6;width: 20%\"><b>Unit Price</b></th>
                                <th style=\"text-align: center;border: 1px solid black;background-color: #b6b6b6;width: 20%\"><b>Amount</b></th
                            </tr>
                            </thead>
                            <tbody>
                            ".$combo_service_html."
                            </tbody>
                        </table>
                    </div>
                    <div style=\"width: 50%;float: right;margin-top: 30px;\">
                        <div style=\"float: left\">
                            <span>Sub Total:</span><br>
                            <span>Discount:</span><br>
                            <span><b>Total Charge</b></span>
                        </div>
                        <div style=\"float: right;text-align: right\">
                            <span >$".$this->csb_amount."</span><br>
                            <span >$".$this->csb_amount_deal."</span><br>
                            <span ><b>$".$this->csb_charge."</b></span>
                        </div>
                    </div>
                    <div style =\"clear: both;\"></div>
                    <div style=\"margin-top: 30px;\">
                        <span style=\"width: 40%;float: left;\" >Payment made by:<b>".$this->getCreatedBy->user_firstname." ".$this->getCreatedBy->user_lastname."</b></span>
                        <span style=\"width: 30%;\" >".$image_check_1."Credit Cards End <b>".substr($this->csb_card_number,-5)."</b></span>
                        <span style=\"width: 30%;float: right\" >".$image_check_2."Check <b>#".$this->routing_number."</b></span>
                    </div>
                    <div style=\"text-align: center;margin-top: 50px\">
                        If you have any question about this invoice, please contact us shown above.
                        <h4><b>THANKS FOR YOUR BUSINESS</b></h4>
                    </div>
                </div>
                    ";
	}
    public function getThemeMail_2(){
        return '<div>
                    <p>Dear <i>'.$this->getCustomer->customer_firstname." ".$this->getCustomer->customer_lastname.'</i> !</p>
                    <p>
                    Cảm ơn bạn vì đã sử dụng Dịch vụ của Dataeglobal. Chúng tôi chắc chắn sẽ hoàn thành dịch vụ tốt nhât và gửi đến bạn sớm nhất có thể.<br>
                    Chúng tôi có gửi  INVOICE và THƯ XÁC NHẬN DỊCH VỤ được đính kèm bên dưới.</p>
                    <p>Trân trọng, </p>
                    
                </div>
                <div style="color:#1be131c4">
                -----------------------------------***------------------------------------
                </div>
                <address>
                    <p style="color: #1be131c4">Thuỳ Lâm</p>
                    DATA EGLOBAL _Together we growth<br>
                    Cell:  408 430 7739       Tollfree: 888 840 8070<br>
                    Add: 10161 Bolsa Ave., Ste 207A Westminster, CA 92683<br><br>
                    <a href="https://dataeglobal.com/" title="" style="text-decoration: none;margin-right: 20px"><img src="'.public_path('images/website.png').'" alt="">WEBSITE </a>
                    <a href="https://www.facebook.com/Posfornailsalon/" title="" style="text-decoration: none;margin-right: 20px"><img src="'.public_path('images/facebook.png').'" alt="">FANPAGE </a>
                    <a href="https://www.youtube.com/channel/UC71jjSWf6kSqwBqBKyoz31w" title="" style="text-decoration: none"><img src="'. public_path('images/youtube.png').'" alt="">YOUTUBE</a>
                </address>';
    }
}
?>
