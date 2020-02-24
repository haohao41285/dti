<!DOCTYPE html>
<html>
@include('layouts.partials.htmlhead')
<style type="text/css" media="screen">
	.col-md-6{
		padding:  0;
		border
	}
	h5{
		font-weight: 500;
	}
	.content-confirm{
		background: url('{{ asset('images/tks.png') }}') no-repeat;
		background-position: center;
		background-size: contain;
	}
	.jumbotron {
	    padding: .1rem!important;
	    margin: 0px;
	}
	.confirm{
		border:  3px dashed #307f2b;
	}
	.sonar-wrapper {
	  /* position: relative; */
	  /* z-index: 0; */
	  /* overflow: hidden; */
	  /* padding: 8rem 0; */
	}

	/* The circle */
	.sonar-emitter {
	  position: relative;
	  margin: 0 auto;
	  width: 80px;
	  height: 80px;
	  border-radius: 9999px;
	  background-size: contain;
	  /* background-color: HSL(45,100%,50%); */
	  box-shadow: 2px 10px 8px gray;
	}
	.submit-rating{
		box-shadow: 2px 10px 8px gray;
	}

	/* the 'wave', same shape and size as its parent */
	.sonar-wave {
	  position: absolute;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	  border-radius: 9999999px;
	  opacity: 0;
	  z-index: 2;
	  pointer-events: none;
	}
	.happy{
		background-color: HSL(0, 92.9%, 49.6%);
	}
	.yay{
		background-color: HSL(320.7, 89.8%, 50%);
	}
	.normal{
		background-color: HSL(59, 100%, 53.1%);
	}
	.satisfied{
		background-color: HSL(104.1, 100%, 56.5%);
	}
	.very_satisfied{
		background-color: HSL(0, 0%, 5.1%);
	}

	/*
	  Animate!
	  NOTE: add browser prefixes where needed.
	*/
	.sonar-emitter:hover .sonar-wave {
	  animation: sonarWave .5s linear infinite;
	  -webkit-animation: sonarWave .5s linear infinite;
	  -moz-animation: sonarWave .5s linear infinite;
	  -ms-animation: sonarWave .5s linear infinite;
	}

	@keyframes sonarWave {
	  from {
	    opacity: 0.4;
	  }
	  to {
	    transform: scale(5);
	    opacity: 0;
	  }
	}
	@media screen and (max-width: 800px) {
		.text-header{
			font-size: 18px;
			padding :  0;
		}
		h5{
			font-size: 18px;
		}
		.col-md-12,{
			margin: 0;
			padding : 2px;
		}
		.col-md-6{
			padding:  0px;
			margin: 0px;
		}
		.logo{
			width: 70%!important;
		}
		.sonar-wrapper{
			font-size: 13px;
		}
		.jumbotron{
			margin-bottom: 15px;
		}
		.sonar-emitter{
			height: 40px;
			width: 40px;
		}
		.social-btn{
			display: none;
		}
		.sonar-emitter {
			width: 35px;
			height: 35px;
		}
	}

</style>
	@if(isset($error))
	<body class="content content-confirm">
		<div class="col-md-6 offset-md-3 mt-5 p-3" style="background-color: white;border-radius: 10px">
			<div class="text-center col-md-12">
				<a href="https://dataeglobal.com" title="">
					<img src="{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}" class="w-100 logo" alt="">
				</a>
				<h4 class="text-header"> Dịch vụ đã được đánh giá hoặc không tồn tại. Quý khách vui lòng kiểm tra lại! </h4>
				Quý vị có thể tìm hiểu các dịch vụ của chúng tôi tại <a href="https://dataeglobal.com/" >DataEglobal</a>

			</div>
		</div>
	</body>
	@else
	<body class="content">
	    <div class="col-md-6 offset-md-3" style="background-color: white;padding-top: 1rem">
			<div class="text-center col-md-12">
				<a href="https://dataeglobal.com" title="">
					<img src="{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}" class="w-100 logo" alt="">
				</a>
			</div>
			<div class="col-md-12">
				<h3 class="m-3 text-center text-header">Đánh giá của quý vị sẽ giúp <a href="https://dataeglobal.com" style="text-decoration: none" title=""><b>DataEglobal</b></a> cải thiện dịch vụ tốt hơn</h3>
				<hr>
				<h5>1. Quý vị có hài lòng với dịch vụ của chúng tôi không?</h5>
					<div class="row text-center jumbotron">
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter rating_level" rating-level=1 style="background: url({{ asset('images/happy.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave happy"></div>
							    </div>
							    Rất hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter rating_level" rating-level=2 style="background: url({{ asset('images/yay.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave yay"></div>
							    </div>
							    Hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter rating_level" rating-level=3 style="background: url({{ asset('images/normal.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave normal"></div>
							    </div>
							    Bình thường
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter rating_level" rating-level=4 style="background: url({{ asset('images/satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave satisfied"></div>
							    </div>
							    Không hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter rating_level" rating-level=5 style="background: url({{ asset('images/very_satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave very_satisfied"></div>
							    </div>
							    Rất không hài lòng
							</div>
						</div>
					</div>
				<h5>2. Quý vị có hài lòng với Nhân viên tư vấn và Chăm sóc khách hàng của dataeglobal không?</h5>
					<div class="row text-center jumbotron">
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter service" rating-level=1 style="background: url({{ asset('images/happy.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave happy"></div>
							    </div>
							    Rất hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter service" rating-level=2 style="background: url({{ asset('images/yay.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave yay"></div>
							    </div>
							    Hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter service" rating-level=3 style="background: url({{ asset('images/normal.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave normal"></div>
							    </div>
							    Bình thường
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter service" rating-level=4 style="background: url({{ asset('images/satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave satisfied"></div>
							    </div>
							    Không hài lòng
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter service" rating-level=5 style="background: url({{ asset('images/very_satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave very_satisfied"></div>
							    </div>
							    Rất không hài lòng
							</div>
						</div>
					</div>
				<h5>3. Quý vị sẽ tiếp tục  mua dịch vụ của chúng tôi lần sau chứ?</h5>
					<div class="row text-center jumbotron">
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter continue_buy" rating-level=1 style="background: url({{ asset('images/yay.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave yay"></div>
							    </div>
							    Có
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter continue_buy" rating-level=2 style="background: url({{ asset('images/satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave satisfied"></div>
							    </div>
							    Không
							</div>
						</div>
					</div>
				<h5>4. Quý vị có sẵn sàng giới thiệu công ty của chúng tôi đến bạn bè?</h5>
					<div class="row text-center jumbotron">
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter introduce" rating-level=1 style="background: url({{ asset('images/yay.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave yay"></div>
							    </div>
							    Có
							</div>
						</div>
						<div class="col p-0">
							<div class="sonar-wrapper">
								<div class="sonar-emitter introduce" rating-level=2 style="background: url({{ asset('images/satisfied.png') }}) no-repeat center;background-size: 120% 120%;">
							    <div class="sonar-wave satisfied"></div>
							    </div>
							    Không
							</div>
						</div>
					</div>
				<h5>5. Điều gi khiến bạn chưa hài lòng về chúng tôi ??</h5>
				<div class="row text-center jumbotron">
					<textarea name="" id="note" class="form-control form-control-sm" rows="3"></textarea>
				</div>
				<h5>6.	Lý do bạn mua sản phẩm của chúng tôi.</h5>
				<div class="row text-center jumbotron">
					<textarea name="" id="reason" class="form-control form-control-sm" rows="3"></textarea>
				</div>
				<h5>7.	Sản phẩm/ dịch vụ mà bạn mong chúng tôi có trong lương lai?</h5>
				<div class="row text-center jumbotron">
					<textarea name="" id="product_feature" class="form-control form-control-sm" rows="3"></textarea>
				</div>
					
				<div class="col-md-12 float-right">
					<input type="button" name="" value="Submit" class="btn btn-sm btn-primary float-right m-2 submit-rating">
				</div>
			</div>
			<br><br>
			<div class="col-md-12 jumbotron">
				<address>
					<h5 class="text-center">ORDER INFORMATION</h5>
					Business: <b>{{ $place_info->place_name }}</b><br>
					Sub Total : <b>${{ $order_info->csb_amount }}</b><br>
					Discount : <b>${{ $order_info->csb_amount_deal }}</b><br>
					Total Charge : <b>${{ $order_info->csb_charge }}</b>
				</address>
					
				<table class="table table-bordered table-sm table-condensed table-hover" style="width: 100%">
					<thead>
						<tr class="thead-dark">
							<th>Item Service</th>
							<th class="text-right">Quantity</th>
							<th class="text-right">Unit Price</th>
							<th class="text-right">Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($combo_service_list as $service)
							<tr>
								<td>{{ $service->cs_name }}</td>
								<td class="text-right">1</td>
								<td class="text-right">{{ $service->cs_price }}</td>
								<td class="text-right">{{ $service->cs_price }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
		</div>
		<div class="col-md-6 offset-md-3 text-center" style="background-color: #323337 ">
	            <div>
	                <address>
	                    <span style="font-size: 20px;">Data E Global</span><br>
	                    <span style="font-weight: bold;">888-840-8070 | 714-707-2007</span><br>
	                    <span style="font-size: 14px;">10161 Bolsa ave, Ste 207A, Westminster, CA 92683</span><br>
	                <address>
	            </div>
	            <div style="text-align:center">
	                <a href="https://dataeglobal.com/"  title="" style="text-decoration: none;margin-right: 20px"><img style="max-width: 40px" class="img-responsive"  src=" {{ asset('/images/website.png') }} " title="Website" alt="Website"><span class="social-btn">WEBSITE</span> </a>
	                <a href="https://www.facebook.com/Posfornailsalon/" title="" style="text-decoration: none;margin-right: 20px"><img class="img-responsive" src="{{ asset('/images/facebook.png') }}" style="max-width: 40px" title="Facebook" alt="Facebook"><span class="social-btn">FANPAGE </span></a>
	                <a href="https://www.youtube.com/channel/UC71jjSWf6kSqwBqBKyoz31w" title="" style="text-decoration: none"><img class="img-responsive" src="{{ asset('/images/youtube.png') }}" style="max-width: 40px" title="Youtube"  alt="Youtube"><span class="social-btn">YOUTUBE</span></a>
	            </div>
			</div>
	</body>
	@endif
		

@include('layouts.partials.scripts')
@if(!isset($error))
<script>
	$(document).ready(function($) {

		var rating_level = 0;
		var service = 0;
		var continue_buy = 0;
		var introduce = 0;

		$(".rating_level").click(function(){

			let rating = $(this).attr('rating-level');

			if(rating == 1 || rating == 2 || rating == 3 || rating == 4 || rating == 5){

				$(".rating_level").removeClass('confirm');
				$(this).addClass('confirm');
				rating_level = rating;
			}
			else
				toastr.error('Vui lòng chọn đánh giá lại!');
		});
		$(".service").click(function(){

			let rating = $(this).attr('rating-level');

			if(rating == 1 || rating == 2 || rating == 3 || rating == 4 || rating == 5){

				$(".service").removeClass('confirm');
				$(this).addClass('confirm');
				service = rating;
			}
			else
				toastr.error('Vui lòng chọn đánh giá lại!');
		});
		$(".continue_buy").click(function(){

			let rating = $(this).attr('rating-level');

			if(rating == 1 || rating == 2){

				$(".continue_buy").removeClass('confirm');
				$(this).addClass('confirm');
				continue_buy = rating;
			}
			else
				toastr.error('Vui lòng chọn đánh giá lại!');
		});

		$(".introduce").click(function(){

			let rating = $(this).attr('rating-level');

			if(rating == 1 || rating == 2){

				$(".introduce").removeClass('confirm');
				$(this).addClass('confirm');
				introduce = rating;
			}
			else
				toastr.error('Vui lòng chọn đánh giá lại!');
		});
		//SEND CUSTOMER RATING
		$(".submit-rating").click(function(){

			if(rating_level == 0 || service == 0 || continue_buy == 0 || introduce ==0){
				toastr.error('Vui lòng chọn đánh giá.');
				return;

			}else{

				var note = $("#note").val();
				var product_feature = $("#product_feature").val();
				var reason = $("#reason").val();

				//SEND RATING
				$.ajax({
					url: '{{ route('customer_rating.post') }}',
					type: 'POST',
					dataType: 'html',
					data: {
						rating_level: rating_level,
						order_token: '{{ $token }}',
						_token: '{{ csrf_token() }}',
						note: note,
						service : service,
						continue_buy : continue_buy,
						introduce : introduce,
						product_feature : product_feature,
						reason : reason

					},
				})
				.done(function(data) {
					data = JSON.parse(data);
					if(data.status === 'error')
						toastr.error(data.message);
					else{
						toastr.success(data.message);
						var content_html = '';
						content_html = `
						<div class="col-md-6 offset-md-3 mt-5 p-3" style="background-color: white;border-radius:10px">
							<div class="text-center col-md-12">
								<a href="https://dataeglobal.com" title="">
									<img src="`+'{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}'+`" class="w-100 logo" alt="">
								</a>
								<h4 class="text-header"> Chúng tôi ghi nhận và trân trọng đánh giá của quý vị. </h4>
								Quý vị có thể tìm hiểu các dịch vụ của chúng tôi tại <a href="https://dataeglobal.com/" >DataEglobal</a>

							</div>
						</div>
						`;
						$('.content').addClass('content-confirm');
						$('.content').html(content_html);
					}
					console.log(data);
				})
				.fail(function() {
					console.log("error");
				});
			}
				
		})
	});
</script>
@endif
</html>