<!-- REQUIRED JS SCRIPTS -->

{{ Html::script('js/manifest.js') }}
{{ Html::script('js/vendor.js') }}
{{ Html::script('js/app.js') }}

@include('layouts.message.message')
@stack('scripts')
