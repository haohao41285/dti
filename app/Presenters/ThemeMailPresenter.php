<?php
namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class ThemeMailPresenter extends Presenter{

	public function getThemeMail(){

	    $combo_service_html = "";
	    $image_check_1 = "";
	    $image_check_2 = "";

	    if($this->csb_card_number != ""){
	        $image_check_1 = "<img src='http://dti.datallys.com/images/green_mark.png'>";
	    }
        if($this->routing_number != ""){
            $image_check_2 = "<img src='http://dti.datallys.com/images/green_mark.png'>";
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
		return "<div style='background: url('http://dti.datallys.com/images/logo_email_3.png') 0 0 repeat;margin: 0px 0px 100px'>
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
        return '
        <!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
    

<body style="background-color: black">
<style>
        .content{display:none}
    </style>
    <div style="width:40%;margin: auto;z-index: 100">
        <img src="http://dti.datallys.com/images/logo dung luongj cao-79.png" style="width: 100%" alt="">
    </div>
    <div style="width:40%;margin: auto;background-color: white;padding: 20px;border-radius: 15px ">
        <div>
            <p>Dear <i>'.$this->getCustomer->customer_firstname." ".$this->getCustomer->customer_lastname.'</i> !</p>
            <p>
            Cảm ơn bạn vì đã sử dụng Dịch vụ của Dataeglobal. Chúng tôi chắc chắn sẽ hoàn thành dịch vụ tốt nhât và gửi đến bạn sớm nhất có thể.<br>
            Chúng tôi có gửi  INVOICE và THƯ XÁC NHẬN DỊCH VỤ được đính kèm bên dưới.</p>
            <p>Trân trọng, </p>
            
        </div>
        <div style="border-bottom: 1px gray dotted;margin: 20px">
            
        </div>
        <div>
            <img src="http://dti.datallys.com/images/banner.png" style="width: 100%" alt="">
        </div>
        <div  class="content">
            <div style="width: 50%;float: left">
                <div style="background: url(http://dti.datallys.com/images/1.png) no-repeat;background-size: 100% 120%;position: relative;text-align: left;padding-left: 10px;background-position: left;padding-left: 50px">
                    <h1>Website &<br>Inailso</h1>
                </div>
                <div style="padding-left: 50px;letter-spacing: 1px;font-size:14px">
                     Website đóng vai trò là gương mặt đại diện cho doanh nghiệp của bạn trên thế giới trực tuyến.App Inailso giúp bạn booking & quản lý dễ dàng qua Phone
                </div>
                <div style="margin-top: 10px;padding-left: 50px">
                    <a href="https://dataeglobal.com" style="height: auto;text-align: center;text-decoration: none;">
                        <div style="color: white !important;line-height: 30px;background-color: #00beff; background-image: linear-gradient(90deg, #0086e6,#a7ea2f);width: 100px;width: 100px;border-radius: 10px;">Xem thêm</div>
                    </a>
                </div>
            </div>
            <div style="width: 50%;float: right">
                <div>
                    <img src="http://dti.datallys.com/images/web.png"  style="transform: scale(1);width: 100%">
                </div>
            </div>
        </div>
        <div style="display: inline-block">
            <div style="width: 50%;float: left">
                <div>
                    <img src="http://dti.datallys.com/images/design.png" alt="" style="transform: scale(1.5);width: 100%">
                </div>
            </div>
            <div style="width: 50%;float: right">
                <div style="background: url(http://dti.datallys.com/images/2.png) no-repeat;background-size: 100% 120%;position: relative;text-align: left;padding-left: 10px;background-position: left;padding-left: 50px">
                    <h1>Design</h1>
                </div>
                <div style="padding-left: 50px;letter-spacing: 1px;">
                    Xây dựng nhận diện thương hiệu giúp cho cơ sở kinh doanh của bạn trở nên nổi bật so với đối thủ cạnh tranh
                </div>
                <div style="margin-top: 10px;padding-left: 50px">
                    <a href="https://dataeglobal.com" style="height: auto;text-align: center;text-decoration: none;">
                        <div style="color: white !important;line-height: 30px;background-color: #00beff; background-image: linear-gradient(90deg, #0086e6,#a7ea2f);width: 100px;width: 100px;border-radius: 10px;">Xem thêm</div>
                    </a>
                </div>
            </div>
        </div>
        <div style="display: inline-block">
            <div style="width: 50%;float: left">
                <div style="background: url(http://dti.datallys.com/images/3.png) no-repeat;background-size: 100% 120%;position: relative;text-align: left;padding-left: 10px;background-position: left;padding-left: 50px">
                    <h1>Review<br>Google Map</h1>
                </div>
                <div style="padding-left: 50px;letter-spacing: 1px;">
                    Tăng độ tin cậy cho thương hiệu của bạn. tăng tiếp xúc trực tuyến cũng như SEO local của bạn, tăng tỷ lệ click...
                </div>
                <div style="margin-top: 10px;padding-left: 50px">
                    <a href="https://dataeglobal.com" style="height: auto;text-align: center;text-decoration: none;">
                        <div style="color: white !important;line-height: 30px;background-color: #00beff; background-image: linear-gradient(90deg, #0086e6,#a7ea2f);width: 100px;width: 100px;border-radius: 10px;">Xem thêm</div>
                    </a>
                </div>
            </div>
            <div style="width: 50%;float: right">
                <div>
                    <img src="http://dti.datallys.com/images/review.png" style="width: 100%" alt="">
                </div>
            </div>
        </div>
        <div style="display: inline-block">
            <div style="width: 50%;float: left">
                <div>
                    <img src="http://dti.datallys.com/images/fanpage.png" alt="" style="transform: scale(1.5);width: 100%">
                </div>
            </div>
            <div style="width: 50%;float: right">
                <div style="background: url(http://dti.datallys.com/images/4.png) no-repeat;background-size: 100% 120%;position: relative;text-align: left;padding-left: 10px;background-position: left;padding-left: 50px">
                    <h1>Fanpage/google<br>Maintenance</h1>
                </div>
                <div style="padding-left: 50px;letter-spacing: 1px;">
                    Duy trì tương tác với khách hàng cũ, xây dựng thương hiệu để dàng tiếp cận khách hàng mới. Tạo hình ảnh fanpage chuyên nghiệp, thân thiện với khách hàng.
                </div>
                <div style="margin-top: 10px;padding-left: 50px">
                    <a href="https://dataeglobal.com" style="height: auto;text-align: center;text-decoration: none;">
                        <div style="color: white !important;line-height: 30px;background-color: #00beff; background-image: linear-gradient(90deg, #0086e6,#a7ea2f);width: 100px;width: 100px;border-radius: 10px;">Xem thêm</div>
                    </a>
                </div>
            </div>
        </div>
        <div style="display: inline-block">
            <div style="width: 50%;float: left">
                <div style="background: url(http://dti.datallys.com/images/5.png) no-repeat;background-size: 100% 120%;position: relative;text-align: left;padding-left: 10px;background-position: left;padding-left: 50px">
                    <h1>Fanpage<br>Recommendation</h1>
                </div>
                <div style="padding-left: 50px;letter-spacing: 1px;">
                    Tăng độ tin cậy cho thương hiệu của bạn. tăng tiếp xúc trực tuyến cũng như SEO local của bạn, tăng tỷ lệ click...
                </div>
                <div style="margin-top: 10px;padding-left: 50px">
                    <a href="https://dataeglobal.com" style="height: auto;text-align: center;text-decoration: none;">
                        <div style="color: white !important;line-height: 30px;background-color: #00beff; background-image: linear-gradient(90deg, #0086e6,#a7ea2f);width: 100px;width: 100px;border-radius: 10px;">Xem thêm</div>
                    </a>
                </div>
            </div>
            <div style="width: 50%;float: right">
                <div>
                    <img src="http://dti.datallys.com/images/google.png" style="transform: scale(1);width: 100%">
                </div>
            </div>
        </div>
        <div style="background-color: #DCCCB2;padding: 20px">
            <div>
                <address>
                    <span style="font-size: 20px;">Data E Global</span><br>
                    <span style="font-weight: bold;">888-840-8070 | 714-707-2007</span><br>
                    <span style="font-size: 14px;">10161 Bolsa ave, Ste 207A, Westminster, CA 92683</span><br>
                <address>
            </div>
            <div style="text-align:center">
                <a href="https://dataeglobal.com/" title="" style="text-decoration: none;margin-right: 20px"><img src="http://dti.datallys.com/images/website.png" alt="">WEBSITE </a>
                <a href="https://www.facebook.com/Posfornailsalon/" title="" style="text-decoration: none;margin-right: 20px"><img src="http://dti.datallys.com/images/facebook.png" alt="">FANPAGE </a>
                <a href="https://www.youtube.com/channel/UC71jjSWf6kSqwBqBKyoz31w" title="" style="text-decoration: none"><img src="http://dti.datallys.com/images/youtube.png" alt="">YOUTUBE</a>
            </div>
        </div>
    </div>
</body>
</html>
                ';
    }
}
?>
