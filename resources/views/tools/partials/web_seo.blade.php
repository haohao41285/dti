<form>
	<div class="mx-2">
		<div class="row">
			<label for="description"><b>Description</b></label>
			<textarea name="description" rows="3" class="form-control form-control-sm" id="description">{{ $webSeo->web_seo_descript??"" }}</textarea>
		</div>
		<div class="row">
			<label for="keywords"><b>Keywords</b></label>
			<input type="text" id="keywords" class="form-control form-control-sm" name="keywords" value="{{ $webSeo->web_seo_meta??'' }}" placeholder="">
		</div>
		<div class="row mt-1">
			<input type="button" class="btn btn-sm btn-primary webseo-submit" name="" value="SUBMIT">
		</div>
	</div>
</form>
	