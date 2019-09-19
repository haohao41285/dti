<!-- REQUIRED JS SCRIPTS -->

{{ Html::script('js/manifest.js') }}
{{ Html::script('js/vendor.js') }}
{{ Html::script('js/app.js') }}

@include('layouts.message.message')

<script>
function perviewImage(){
$("input[type='file']").on("change", function(event){
    var preview = $(this).attr('previewImageId');
    if(preview){
      $("#"+preview+"").attr("src",URL.createObjectURL(event.target.files[0]));
    }
});
}
</script>
@stack('scripts')
