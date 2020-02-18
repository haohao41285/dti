<!DOCTYPE html>
<html>
@include('layouts.partials.htmlhead')
<style type="text/css" media="screen">
	.content-confirm{
		background: url('{{ asset('images/tks.png') }}') no-repeat;
		background-position: center;
		background-size: contain;
	}
	.jumbotron {
	    padding: 1rem 2rem 4rem!important;
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
	  width: 150px;
	  height: 150px;
	  border-radius: 9999px;
	  /* background-color: HSL(45,100%,50%); */
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
	.sad{
		background-color: HSL(116.4, 49.4%, 33.3%);
	}
	.yay{
		background-color: HSL(50.8, 89.8%, 50%);
	}
	.haha{
		background-color: HSL(0, 100%, 56.5%);
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
	    transform: scale(3);
	    opacity: 0;
	  }
	}

</style>
	@if(isset($error))
	<body class="content content-confirm">
		<div class="col-md-6 offset-md-3 mt-5">
			<div class="text-center col-md-12">
				<a href="https://dataeglobal.com" title="">
					<img src="{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}" class="w-100" alt="">
				</a>
				<h4> Dịch vụ đã được đánh giá hoặc không tồn tại. Quý khách vui lòng kiểm tra lại! </h4>
				Quý vị có thể tìm hiểu các dịch vụ của chúng tôi tại <a href="https://dataeglobal.com/" >DataEglobal</a>

			</div>
		</div>
	</body>
	@else
	<body class="content">
	    <div class="col-md-6 offset-md-3">
			<div class="text-center col-md-12">
				<a href="https://dataeglobal.com" title="">
					<img src="{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}" class="w-100" alt="">
				</a>
			</div>
			<div class="col-md-12 text-center jumbotron">
				<h3 class="m-3">Đánh giá của quý vị sẽ giúp <a href="https://dataeglobal.com" style="text-decoration: none" title=""><b>DataEglobal</b></a> cải thiện dịch vụ tốt hơn</h3>
					<div class="row">
						<div class="col-md-4">
							<div class="sonar-wrapper">
								<div class="sonar-emitter" rating-level=3 style="background: url({{ asset('images/haha.png') }}) no-repeat center center;background-size: 100% 100%;">
							    <div class="sonar-wave haha"></div>
							    </div>
							    <b>Rất hài lòng</b>
							</div>
						</div>
						<div class="col-md-4">
							<div class="sonar-wrapper">
								<div class="sonar-emitter" rating-level=2 style="background: url({{ asset('images/yay.png') }}) no-repeat center center;background-size: 100% 100%;">
							    <div class="sonar-wave yay"></div>
							    </div>
							    <b>Hài lòng</b>
							</div>
						</div>
						<div class="col-md-4">
							<div class="sonar-wrapper">
								<div class="sonar-emitter" rating-level=1 style="background: url({{ asset('images/sad.png') }}) no-repeat center center;background-size: 100% 100%;">
							    <div class="sonar-wave sad"></div>
							    </div>
							    <b>Chưa hài lòng</b>
							</div>
						</div>
					</div>
				<label for="note" class="float-left mt-3"><b>Góp Ý</b></label>
				<textarea name="" id="note" class="form-control form-control-sm col-md-12" rows="5"></textarea>
				<input type="button" name="" value="Submit" class="btn btn-sm btn-primary float-right m-2 submit-rating">
			</div>
			<div class="col-md-12 mt-10 jumbotron">
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
		<div class="col-md-12 text-center" style="background-color: #323337 ">
	            <div>
	                <address>
	                    <span style="font-size: 20px;">Data E Global</span><br>
	                    <span style="font-weight: bold;">888-840-8070 | 714-707-2007</span><br>
	                    <span style="font-size: 14px;">10161 Bolsa ave, Ste 207A, Westminster, CA 92683</span><br>
	                <address>
	            </div>
	            <div style="text-align:center">
	                <a href="https://dataeglobal.com/" title="" style="text-decoration: none;margin-right: 20px"><img style="max-width: 40px" class="img-responsive"  src=" {{ asset('/images/website.png') }} " alt="">WEBSITE </a>
	                <a href="https://www.facebook.com/Posfornailsalon/" title="" style="text-decoration: none;margin-right: 20px"><img class="img-responsive" src="{{ asset('/images/facebook.png') }}" style="max-width: 40px" alt="">FANPAGE </a>
	                <a href="https://www.youtube.com/channel/UC71jjSWf6kSqwBqBKyoz31w" title="" style="text-decoration: none"><img class="img-responsive" src="{{ asset('/images/youtube.png') }}" style="max-width: 40px"  alt="">YOUTUBE</a>
	            </div>
			</div>
	</body>
	@endif
		

@include('layouts.partials.scripts')
@if(!isset($error))
<script>
	$(document).ready(function($) {

		var rating_level = 0;

		$(".sonar-emitter").click(function(){

			let rating = $(this).attr('rating-level');

			if(rating == 1 || rating == 2 || rating == 3){

				$(".sonar-emitter").removeClass('confirm');
				$(this).addClass('confirm');
				rating_level = rating;
			}
			else
				toastr.error('Vui lòng chọn đánh giá lại!');
		});
		//SEND CUSTOMER RATING
		$(".submit-rating").click(function(){

			if(rating_level == 0){
				toastr.error('Vui lòng chọn đánh giá.');
				return;

			}else{

				var note = $("#note").val();
				//SEND RATING
				$.ajax({
					url: '{{ route('customer_rating.post') }}',
					type: 'POST',
					dataType: 'html',
					data: {
						rating_level: rating_level,
						order_token: '{{ $token }}',
						_token: '{{ csrf_token() }}',
						note: note
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
						<div class="col-md-6 offset-md-3 mt-5">
							<div class="text-center col-md-12">
								<a href="https://dataeglobal.com" title="">
									<img src="`+'{{ asset('images/logo%20dung%20luongj%20cao-79.png') }}'+`" class="w-100" alt="">
								</a>
								<h4> Chúng tôi vô cùng trân trọng đánh giá của quý vị. </h4>
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