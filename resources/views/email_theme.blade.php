<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<style>
.deg-text{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-25%, -50%);
    width: 100%;

}
.deg-bg-text{
    position: absolute;
    bottom: -20px;
    left: -73px;
    z-index: 1;
}


.deg-bg-text-r{
    right: -50px !important;
    left: unset !important;
    bottom: -29px !important;
}


.deg-bg-text-review{
    bottom: -15px !important;
    left: -22px !important;
}


.deg-text-design{
    left: unset !important;
}


.deg-btn-link{
    background-color: #05A2DA;
    padding: 2px 20px;
    color: white;
    border-radius: 8px;
}
.deg-btn-link:hover{
    background-color: #0582c1 !important;
}
/* responsive */

@media only screen and (max-width: 767px) { 
    .pt-pb-0-mb{
        padding-top: 0rem!important;
        padding-bottom: 0rem!important;
    }
    .pl-pr-0-mb{
        padding-left: 0rem!important;
        padding-right: 0rem!important;
    }
    .p-15{
        padding: 15px !important;
    }
    .container{
        padding-top: 0rem!important;
        padding-bottom: 0rem!important;
    }
}


@media only screen and (max-width: 1023px) { 

    .deg-bg-text-r{
        right: -50px !important;
        left: unset !important;
        bottom: -29px !important;
    }
    .deg-img-design{
        overflow: hidden;
    }
    .deg-text{
        position: relative;
        top: unset;
        left: unset;
        transform: unset;
        width: 100%;
        padding: 15px;
    }
    .deg-bg-text{
        position: absolute;
        bottom: -37px;
        left: -73px;
        z-index: 1;
    }
}


@media only screen and (max-width: 575px) { 


}



/*// Small devices (landscape phones, 576px and up)*/
@media  only screen and (min-width: 568px) and (max-width:666px){ 
    

}


@media  only screen and (min-width: 667px) and (max-width:767px){ 
    
}




/*// Medium devices (tablets, 768px and up)*/
@media  only screen and (min-width: 768px) and (max-width:1023px) { 
   
}




/*// Large devices (desktops, 992px and up)*/
@media  only screen and (min-width: 992px) and (max-width:1199px) {
    
}





/*// Extra large devices (large desktops, 1200px and up)*/
@media  only screen and (min-width: 1200px) { 
/* Code here*/

}
</style>



<body>
    <div>
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
    </address>

<div class="container pt-5 pb-5" style="overflow: hidden;">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 pl-pr-0-mb" style="margin: 0 auto;">
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pl-pr-0-mb">
                <div class="deg-bg1">
                    <img src="'.public_path('/image/banner.png').'" class="w-100" alt="">
                </div>
            </div>
        </div>

        <!-- inailso -->
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="position: relative;z-index: 3;">
                <div class="deg-text deg-text-inailso">
                    <div class="deg-title-inail" style="position: relative;">
                        <div class="deg-web-inai pb-4" style="position: relative;z-index: 3;">
                            <h1>Website &<br>Inailso</h1>
                        </div> 
                        <div class="deg-bg-text w-100">
                            <img src="'.public_path('/image/1.png').'" class="w-100" alt="">
                        </div>
                    </div>
                    <div class="deg-text-inail">
                        Website đóng vai trò là gương mặt đại diện cho doanh nghiệp của bạn trên thế giới trực tuyến.App Inailso giúp bạn booking & quản lý dễ dàng qua Phone
                    </div>
                    <a href="https://dataeglobal.com"><div class="deg-btn-link btn mt-4" style="color: white !important;">Xem thêm</div></a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7" style="position: relative;z-index: 1;">
                <div class="deg-img deg-img-inailso">
                    <img src="'.public_path('/image/web.png').'" class="w-100" alt="">
                </div>
            </div>
        </div>
        <!-- end inailso -->


        <!-- design -->
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7" style="position: relative;z-index: 1;">
                <div class="deg-img deg-img-design">
                    <img src="'.public_path('/image/design.png').'" class="w-100" alt="" style="transform: scale(1.5);">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="position: relative;z-index: 3;">
                <div class="deg-text deg-text-design">
                    <div class="deg-title-design" style="position: relative;">
                        <div class="deg-web-design pb-4 text-right" style="position: relative;z-index: 3;">
                            <h1>Design</h1>
                        </div>
                        <div class="deg-bg-text deg-bg-text-r w-100">
                            <img src="'.public_path('/image/2.png').'" class="w-100" alt="">
                        </div>
                    </div>
                    <div class="deg-text-design text-right">
                        Xây dựng nhận diện thương hiệu giúp cho cơ sở kinh doanh của bạn trở nên nổi bật so với đối thủ cạnh tranh
                    </div>
                    <a href="https://dataeglobal.com"><div class="deg-btn-link btn mt-4" style="color: white !important;float: right;">Xem thêm</div></a>
                </div>
            </div>
            
        </div>
        <!-- end design -->


        <!-- review -->
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="position: relative;z-index: 3;">
                <div class="deg-text deg-text-review">
                    <div class="deg-title-review" style="position: relative;">
                        <div class="deg-web-review pb-4" style="position: relative;z-index: 3;">
                            <h1>Review<br>Google Map</h1>
                        </div>
                        <div class="deg-bg-text deg-bg-text-review w-100">
                            <img src="'.public_path('/image/3.png').'" class="w-100" alt="">
                        </div>
                    </div>
                    <div class="deg-text-review">
                        Tăng độ tin cậy cho thương hiệu của bạn. tăng tiếp xúc trực tuyến cũng như SEO local của bạn, tăng tỷ lệ click...
                    </div>
                    <a href="https://dataeglobal.com"><div class="deg-btn-link btn mt-4" style="color: white !important;">Xem thêm</div></a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7" style="position: relative;z-index: 1;">
                <div class="deg-img deg-img-review">
                    <img src="'.public_path('/image/review.png').'" class="w-100" alt="">
                </div>
            </div>
        </div>
        <!-- end review -->


        <!-- fanpage -->
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7" style="position: relative;z-index: 1;">
                <div class="deg-img deg-img-fanpage">
                    <img src="'.public_path('/image/fanpage.png').'" class="w-100" alt="" style="transform: scale(1);">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="position: relative;z-index: 3;">
                <div class="deg-text deg-text-design">
                    <div class="deg-title-fanpage" style="position: relative;">
                        <div class="deg-web-fanpage pb-4 text-right" style="position: relative;z-index: 3;">
                            <h1>Fanpage/google<br>Maintenance</h1>
                        </div>
                        <div class="deg-bg-text deg-bg-text-r w-100">
                            <img src="'.public_path('/image/4.png').'" class="w-100" alt="">
                        </div>
                    </div>
                    <div class="deg-text-fanpage text-right">
                        Duy trì tương tác với khách hàng cũ, xây dựng thương hiệu để dàng tiếp cận khách hàng mới. Tạo hình ảnh fanpage chuyên nghiệp, thân thiện với khách hàng.
                    </div>
                    <a href="https://dataeglobal.com"><div class="deg-btn-link btn mt-4" style="color: white !important;float: right;">Xem thêm</div></a>
                </div>
            </div>
        </div>
        <!-- end fanpage -->


        <!-- recomment -->
        <div class="row p-0 m-0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="position: relative;z-index: 3;">
                <div class="deg-text deg-text-review">
                    <div class="deg-title-review" style="position: relative;">
                        <div class="deg-web-review pb-4" style="position: relative;z-index: 3;">
                            <h1>Fanpage<br>Recommendation</h1>
                        </div>
                        <div class="deg-bg-text deg-bg-text-review w-100">
                            <img src="'.public_path('/image/5.png').'" class="w-100" alt="">
                        </div>
                    </div>
                    <div class="deg-text-review">
                        Tăng độ tin cậy cho thương hiệu của bạn. tăng tiếp xúc trực tuyến cũng như SEO local của bạn, tăng tỷ lệ click...
                    </div>
                    <a href="https://dataeglobal.com"><div class="deg-btn-link btn mt-4" style="color: white !important;">Xem thêm</div></a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7" style="position: relative;z-index: 1;">
                <div class="deg-img deg-img-review">
                    <img src="'.public_path('/image/google.png').'" class="w-100" alt="">
                </div>
            </div>
        </div>
        <!-- end recomment -->


        <!-- footer -->
        <div class="row p-0 m-0" style="background-color: #DCCCB2;">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center pt-2 pb-0">
                <p class="m-0">
                    <span style="font-size: 20px;">Data E Global</span><br>
                    <span style="font-weight: bold;">888-840-8070 | 714-707-2007</span><br>
                    <span style="font-size: 14px;">10161 Bolsa ave, Ste 207A, Westminster, CA 92683</span><br>
                <p>
                    

            </div>
        </div>
        <!-- end footer -->
    </div>
  </div>
</div>

</body>
</html>